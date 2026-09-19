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
    int $id,
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
    $completedStatuses = ['เสร็จสิ้น', 'completed'];

    $validated = $request->validate([
        'status' => [
            'required',
            'string',
            Rule::in($this->orderStatuses()),
        ],

        'proof_image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png',
            'max:2048',
        ],

        'cancel_reason' => [
            Rule::requiredIf(
                in_array(
                    $request->input('status'),
                    $cancelStatuses,
                    true
                )
            ),
            'nullable',
            'string',
            'max:500',
        ],
    ], [
        'status.required' => 'กรุณาเลือกสถานะคำสั่งซื้อ',
        'status.in' => 'สถานะคำสั่งซื้อไม่ถูกต้อง',

        'proof_image.image' => 'ไฟล์หลักฐานต้องเป็นรูปภาพ',
        'proof_image.mimes' => 'รองรับเฉพาะไฟล์ JPG, JPEG และ PNG',
        'proof_image.max' => 'รูปหลักฐานต้องมีขนาดไม่เกิน 2 MB',

        'cancel_reason.required' => 'กรุณาระบุเหตุผลในการยกเลิก',
        'cancel_reason.max' => 'เหตุผลในการยกเลิกต้องไม่เกิน 500 ตัวอักษร',
    ]);

    DB::transaction(function () use (
        $id,
        $request,
        $validated,
        $actor,
        $cancelStatuses,
        $completedStatuses,
        $stockReversal
    ) {
        $order = Order::query()
            ->lockForUpdate()
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | พนักงานอัปเดตได้เฉพาะออเดอร์ที่ตัวเองรับไว้
        |--------------------------------------------------------------------------
        */
        if (
            $actor->role === 'staff' &&
            (int) $order->assigned_to !== (int) $actor->id
        ) {
            abort(
                403,
                'คุณไม่มีสิทธิ์อัปเดตคำสั่งซื้อนี้'
            );
        }

        $currentStatus = (string) $order->status;
        $newStatus = (string) $validated['status'];

        /*
        |--------------------------------------------------------------------------
        | ตรวจสอบการเปลี่ยนสถานะทีละขั้น
        |--------------------------------------------------------------------------
        */
        $this->ensureValidOrderTransition(
            $currentStatus,
            $newStatus
        );

        /*
        |--------------------------------------------------------------------------
        | สถานะเสร็จสิ้นต้องมีรูปหลักฐาน
        |--------------------------------------------------------------------------
        */
        if (
            in_array($newStatus, $completedStatuses, true) &&
            !$request->hasFile('proof_image') &&
            empty($order->proof_image)
        ) {
            throw ValidationException::withMessages([
                'proof_image' => 'กรุณาแนบรูปหลักฐานก่อนเปลี่ยนเป็นเสร็จสิ้น',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ยกเลิกคำสั่งซื้อและคืนสต็อก
        |--------------------------------------------------------------------------
        */
        if (in_array($newStatus, $cancelStatuses, true)) {
            $stockReversal->reverseOrder(
                $order,
                $validated['cancel_reason']
            );

            $order->cancel_reason =
                $validated['cancel_reason'];
        } else {
            $order->cancel_reason = null;
        }

        /*
        |--------------------------------------------------------------------------
        | บันทึกรูปหลักฐาน
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('proof_image')) {
            $oldProofImage = $order->proof_image;

            $newProofImage = $request
                ->file('proof_image')
                ->store('proofs', 'public');

            $order->proof_image = $newProofImage;

            /*
             * ลบรูปเดิมหลังจากเก็บรูปใหม่สำเร็จแล้ว
             */
            if (
                $oldProofImage &&
                Storage::disk('public')->exists($oldProofImage)
            ) {
                Storage::disk('public')->delete($oldProofImage);
            }
        }

        $order->status = $newStatus;
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
    string $newStatus
): void {
    /*
    |--------------------------------------------------------------------------
    | ลำดับสถานะที่อนุญาต
    |--------------------------------------------------------------------------
    */
    $statusFlow = [
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

        /*
         * รองรับชื่อภาษาอังกฤษ หากมีข้อมูลเดิมในฐานข้อมูล
         */
        'pending' => [
            'preparing',
            'cancelled',
        ],

        'preparing' => [
            'shipping',
            'cancelled',
        ],

        'shipping' => [
            'completed',
            'cancelled',
        ],

        /*
         * สถานะสุดท้าย ไม่สามารถเปลี่ยนต่อได้
         */
        'เสร็จสิ้น' => [],
        'ยกเลิก' => [],
        'completed' => [],
        'cancelled' => [],
    ];

    if ($currentStatus === $newStatus) {
        throw ValidationException::withMessages([
            'status' => 'สถานะใหม่ต้องไม่ซ้ำกับสถานะปัจจุบัน',
        ]);
    }

    if (!array_key_exists($currentStatus, $statusFlow)) {
        throw ValidationException::withMessages([
            'status' =>
                "ไม่พบลำดับของสถานะปัจจุบัน: {$currentStatus}",
        ]);
    }

    $allowedStatuses = $statusFlow[$currentStatus];

    if (!in_array($newStatus, $allowedStatuses, true)) {
        $nextStatusText = empty($allowedStatuses)
            ? 'ไม่มีสถานะถัดไป'
            : implode(' หรือ ', $allowedStatuses);

        throw ValidationException::withMessages([
            'status' =>
                "ไม่สามารถเปลี่ยนจาก {$currentStatus} เป็น {$newStatus} ได้ "
                . "สถานะที่อนุญาตคือ {$nextStatusText}",
        ]);
    }
}
    public function orderHistory(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $paymentMethod = trim((string) $request->input('payment_method', ''));

        // รองรับทั้งชื่อ field ใหม่ใน Blade และชื่อเดิมที่เคยใช้ใน Controller
        $dateFrom = $request->input('date_from')
            ?: $request->input('start_date');

        $dateTo = $request->input('date_to')
            ?: $request->input('end_date');

        /*
        |--------------------------------------------------------------------------
        | ประวัติคำสั่งซื้อ
        |--------------------------------------------------------------------------
        | แสดงเฉพาะคำสั่งซื้อที่จบกระบวนการแล้ว
        | ระบบปัจจุบันใช้ "เสร็จสิ้น" และ "ยกเลิก"
        | พร้อมรองรับข้อมูลเก่าภาษาอังกฤษในฐานข้อมูล
        */
        $historyStatuses = [
            'เสร็จสิ้น',
            'ยกเลิก',
            'completed',
            'delivered',
            'cancelled',
        ];

        $query = Order::query()
            ->with([
                'user',
                'orderItems.product',
                'orderItems.productUnit',
            ])
            ->whereIn('status', $historyStatuses);

        /*
        |--------------------------------------------------------------------------
        | ค้นหา
        |--------------------------------------------------------------------------
        | ID / order_code / tracking_number / ชื่อ / อีเมล / เบอร์โทร
        */
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('order_code', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | กรองสถานะ
        |--------------------------------------------------------------------------
        */
        if ($status !== '') {
            $statusMap = [
                'สำเร็จ' => ['เสร็จสิ้น', 'completed', 'delivered'],
                'เสร็จสิ้น' => ['เสร็จสิ้น', 'completed', 'delivered'],
                'completed' => ['เสร็จสิ้น', 'completed', 'delivered'],

                'ยกเลิก' => ['ยกเลิก', 'cancelled'],
                'cancelled' => ['ยกเลิก', 'cancelled'],
            ];

            $query->whereIn(
                'status',
                $statusMap[$status] ?? [$status]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | กรองวิธีชำระเงิน
        |--------------------------------------------------------------------------
        */
        if ($paymentMethod !== '') {
            $paymentMap = [
                'qr' => [
                    'โอนผ่านบัญชีธนาคาร',
                    'qr',
                    'qrcode',
                    'promptpay',
                    'พร้อมเพย์',
                    'โอนเงิน',
                ],
                'โอนผ่านบัญชีธนาคาร' => [
                    'โอนผ่านบัญชีธนาคาร',
                    'qr',
                    'qrcode',
                    'promptpay',
                    'พร้อมเพย์',
                    'โอนเงิน',
                ],
                'cod' => [
                    'เก็บเงินปลายทาง',
                    'cod',
                    'cash_on_delivery',
                ],
                'เก็บเงินปลายทาง' => [
                    'เก็บเงินปลายทาง',
                    'cod',
                    'cash_on_delivery',
                ],
            ];

            $query->whereIn(
                'payment_method',
                $paymentMap[$paymentMethod] ?? [$paymentMethod]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | กรองวันที่
        |--------------------------------------------------------------------------
        */
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | ใช้ Blade หน้าที่ผู้ใช้กำลังแก้จริง
        |--------------------------------------------------------------------------
        */
        return view('online.orders.history', compact('orders'));
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