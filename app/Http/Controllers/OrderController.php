<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Services\SafeStockReversalService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    */

    public function checkout(Request $request)
{
    // 1. ดึงข้อมูลสินค้าจาก Session ตะกร้า
    $cart = session()->get('cart', []);

    // ถ้าไม่มีสินค้าในตะกร้า ให้ดีดกลับไปหน้าตะกร้า
    if (empty($cart)) {
        return redirect()->route('online.cart')->with('error', 'ไม่มีสินค้าในตะกร้า');
    }

    // 2. คำนวณยอดรวมสุทธิจาก Session ใน Server เสมอ (ไม่รับค่า total จาก URL)
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $member = auth()->user(); // หรือ Model สมาชิกของคุณ
    $shopSettings = Setting::first(); // หรือข้อมูลตั้งค่าร้านค้า

    // 3. ส่งข้อมูลไปยัง View โดยไม่ต้องรับ product_unit_id เพิ่มเติม
    return view('online.checkout', compact('cart', 'total', 'member', 'shopSettings'));
}


    /*
    |--------------------------------------------------------------------------
    | Place Order
    |--------------------------------------------------------------------------
    */

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'slip' => 'required_if:payment_method,โอนผ่านบัญชีธนาคาร|image|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $slipPath = null;

            if ($request->hasFile('slip')) {

                $slipPath = Cloudinary::upload(
                    $request->file('slip')->getRealPath(),
                    [
                        'folder' => 'slips',
                    ]
                )->getSecurePath();

            }


            $order = new Order();

            $order->order_code =
                'ORD' . strtoupper(uniqid());

            $order->user_id =
                Auth::id();

            $order->payment_method =
                $request->payment_method;

            $order->total_amount =
                $request->total_amount;

            $order->status =
                'รอเจ้าหน้าที่รับคำสั่งซื้อ';

            $order->slip_path =
                $slipPath;

            $order->save();


            foreach ($request->items as $item) {
    OrderItem::create([
        'order_id' => $order->id,
        'product_unit_id' => $item['product_unit_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'total' => $item['price'] * $item['quantity'],
    ]);
}


            DB::commit();
            // ⚡️ เพิ่มบรรทัดนี้: ลบข้อมูลสินค้าในตะกร้าออกจาก Session หลังสั่งซื้อสำเร็จ
            session()->forget('cart');


            return redirect()
                ->route('online.track')
                ->with(
                    'success',
                    'ทำรายการสำเร็จ! คำสั่งซื้อ #' .
                    $order->order_code
                );


        } catch (\Exception $e) {

            DB::rollback();


            return back()
                ->withErrors([
                    'error' =>
                        'เกิดข้อผิดพลาด: ' .
                        $e->getMessage()
                ]);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Track Orders
    |--------------------------------------------------------------------------
    */

    public function track()
    {
        $orders = Order::with([
            'orderItems.product',
            'user'
        ])
            ->where('user_id', Auth::id())
            ->whereNotIn(
                'status',
                [
                    'เสร็จสิ้น',
                    'ยกเลิก'
                ]
            )
            ->orderBy(
                'created_at',
                'desc'
            )
            ->paginate(10);


        return view(
            'online.track',
            compact('orders')
        );
    }


    public function myAcceptedOrders(Request $request)
    {
        $query =
            Order::where(
                'assigned_to',
                Auth::id()
            );


        if ($request->status) {

            $query->where(
                'status',
                $request->status
            );

        } else {

            $query->whereIn(
                'status',
                [
                    'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                    'กำลังจัดส่ง'
                ]
            );

        }


        $orders =
            $query->latest()->get();


        $pendingCount =
            Order::where(
                'assigned_to',
                Auth::id()
            )
                ->where(
                    'status',
                    'รอเจ้าหน้าที่รับคำสั่งซื้อ'
                )
                ->count();


        $shippingCount =
            Order::where(
                'assigned_to',
                Auth::id()
            )
                ->where(
                    'status',
                    'กำลังจัดส่ง'
                )
                ->count();


        return view(
            'sale.show-order',
            compact(
                'orders',
                'pendingCount',
                'shippingCount'
            )
        );

    }


    public function updateStatus(
        Request $request,
        $id,
        SafeStockReversalService $stockReversal
    ) {
        $actor = $request->user();

        abort_unless(
            $actor &&
                in_array(
                    $actor->role,
                    ['admin', 'manager', 'owner', 'staff'],
                    true
                ),
            403
        );

        $cancelStatuses = ['ยกเลิก', 'cancelled'];
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in($this->orderStatuses()),
            ],
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cancel_reason' => [
                Rule::requiredIf(
                    in_array($request->input('status'), $cancelStatuses, true)
                ),
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        DB::transaction(function () use (
            $id,
            $request,
            $validated,
            $cancelStatuses,
            $stockReversal
        ) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($id);
            $actor = $request->user();

            abort_unless(
                $actor &&
                    in_array(
                        $actor->role,
                        ['admin', 'manager', 'owner', 'staff'],
                        true
                    ),
                403
            );

            if (
                $actor->role === 'staff' &&
                (int) $order->assigned_to !== (int) $actor->id
            ) {
                abort(403);
            }

            $this->ensureValidOrderTransition(
                (string) $order->status,
                $validated['status']
            );

            if (
                in_array($validated['status'], ['เสร็จสิ้น', 'completed'], true) &&
                !$request->hasFile('proof_image') &&
                !$order->proof_image
            ) {
                throw ValidationException::withMessages([
                    'proof_image' => 'กรุณาแนบรูปหลักฐาน!',
                ]);
            }

            if (in_array($validated['status'], $cancelStatuses, true)) {
                $stockReversal->reverseOrder(
                    $order,
                    $validated['cancel_reason']
                );
                $order->cancel_reason = $validated['cancel_reason'];
            }

            if ($request->hasFile('proof_image')) {
                if (
                    $order->proof_image &&
                    Storage::disk('public')->exists($order->proof_image)
                ) {
                    Storage::disk('public')->delete($order->proof_image);
                }

                $order->proof_image = $request
                    ->file('proof_image')
                    ->store('proofs', 'public');
            }

            $order->status = $validated['status'];
            $order->save();
        });


        return back()->with(
            'success',
            'อัปเดตสถานะสำเร็จ!'
        );

    }

    private function orderStatuses(): array
    {
        return array_values(array_unique(array_merge(
            array_keys($this->orderTransitions()),
            ...array_values($this->orderTransitions())
        )));
    }

    private function orderTransitions(): array
    {
        return [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
            'รอเจ้าหน้าที่รับคำสั่งซื้อ' => [
                'กำลังจัดสินค้า',
                'ยกเลิก',
            ],
            'กำลังจัดสินค้า' => [
                'กำลังจัดส่ง',
                'ยกเลิก',
            ],
            'กำลังจัดส่ง' => [
                'เสร็จสิ้น',
                'ยกเลิก',
            ],
            'เสร็จสิ้น' => [],
            'ยกเลิก' => [],
        ];
    }

    private function ensureValidOrderTransition(
        string $currentStatus,
        string $nextStatus
    ): void {
        $allowed = $this->orderTransitions()[$currentStatus] ?? [];

        if (!in_array($nextStatus, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' =>
                    "Invalid order status transition: {$currentStatus} -> {$nextStatus}.",
            ]);
        }
    }


    public function orderHistory(Request $request)
    {
        $startDate =
            $request->start_date;

        $endDate =
            $request->end_date;


        $query =
            Order::with([
                'orderItems.product',
                'user'
            ])
                ->whereIn(
                    'status',
                    [
                        'เสร็จสิ้น',
                        'ยกเลิก'
                    ]
                );


        if ($startDate && $endDate) {

            $query->whereBetween(
                'created_at',
                [
                    \Carbon\Carbon::parse(
                        $startDate
                    )->startOfDay(),

                    \Carbon\Carbon::parse(
                        $endDate
                    )->endOfDay()
                ]
            );

        }


        $orders =
            $query
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->paginate(10)
                ->withQueryString();


        return view(
            'sale.order-history',
            compact('orders')
        );

    }


    public function acceptOrder($id)
    {
        $actor = Auth::user();

        abort_unless(
            $actor && in_array($actor->role, ['admin', 'manager', 'owner', 'staff'], true),
            403
        );

        DB::transaction(function () use ($actor, $id) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if (
                $order->assigned_to &&
                (int) $order->assigned_to !== (int) $actor->id
            ) {
                throw ValidationException::withMessages([
                    'status' => 'ออเดอร์นี้ถูกรับโดยพนักงานคนอื่นแล้ว',
                ]);
            }

            $nextStatus = match ($order->status) {
                'pending' => 'processing',
                'รอเจ้าหน้าที่รับคำสั่งซื้อ' => 'กำลังจัดสินค้า',
                default => null,
            };

            if (!$nextStatus) {
                throw ValidationException::withMessages([
                    'status' =>
                        "Order in status {$order->status} cannot be accepted.",
                ]);
            }

            $order->assigned_to = $actor->id;
            $order->assigned_at = now();
            $order->status = $nextStatus;
            $order->save();
        });


        return redirect()
            ->route('orders.my')
            ->with(
                'success',
                'รับออเดอร์เรียบร้อยแล้ว'
            );

    }


    public function myOrders()
    {
        $orders =
            Order::with([
                'user',
                'orderItems.product',
                'orderItems.productUnit'
            ])
                ->where(
                    'assigned_to',
                    Auth::id()
                )
                ->whereNotIn(
                    'status',
                    [
                        'เสร็จสิ้น',
                        'ยกเลิก'
                    ]
                )
                ->orderBy(
                    'created_at',
                    'asc'
                )
                ->latest()
                ->get();


        return view(
            'sale.show-order',
            compact('orders')
        );

    }


    public function show($id)
    {
        $actor = Auth::user();

        abort_unless(
            $actor && in_array($actor->role, ['admin', 'manager', 'owner', 'staff'], true),
            403
        );

        $order =
            Order::with([
                'user',
                'orderItems.product',
                'orderItems.productUnit'
            ])
                ->findOrFail($id);


        if (
            $actor->role === 'staff' &&
            (int) $order->assigned_to !== (int) $actor->id
        ) {

            abort(
                403,
                'คุณไม่มีสิทธิ์ดูคำสั่งซื้อนี้'
            );

        }


        return view(
            'sale.show-order',
            compact('order')
        );

    }
public function generateQRCode(Request $request)
{
    $amount = $request->query('amount');

    if (!is_numeric($amount) || $amount <= 0) {
        abort(400, 'Invalid amount');
    }

    $amount = number_format($amount, 2, '.', '');

    $promptPayID = Setting::get(
        'promptpay',
        env('PROMPTPAY_ID')
    );

    return redirect(
        "https://promptpay.io/{$promptPayID}/{$amount}"
    );
}

    public function ordersList()
    {
        $orders =
            Order::with([
                'user',
                'orderItems.product',
                'orderItems.productUnit'
            ])
                ->whereNotIn(
                    'status',
                    [
                        'เสร็จสิ้น',
                        'ยกเลิก'
                    ]
                )
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->paginate(10);


        return view(
            'sale.order',
            compact('orders')
        );

    }

}
