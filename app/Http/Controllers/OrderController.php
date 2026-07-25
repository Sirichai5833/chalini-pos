<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStocks;
use App\Models\Setting;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;


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
        $id
    ) {
        $actor = $request->user();

        abort_unless(
            $actor && in_array($actor->role, ['admin', 'manager', 'owner', 'staff'], true),
            403
        );

        $order =
            Order::with(
                'orderItems'
            )->findOrFail($id);

        if (
            $actor->role === 'staff' &&
            (int) $order->assigned_to !== (int) $actor->id
        ) {
            abort(403);
        }


        $oldStatus =
            $order->status;


        $request->validate([

            'status' =>
                'required|string',

            'proof_image' =>
                'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'cancel_reason' =>
                'nullable|string|max:500',

        ]);


        if (

            $request->status === 'เสร็จสิ้น' &&

            !$request->hasFile('proof_image') &&

            !$order->proof_image

        ) {

            return back()->with(
                'error',
                'กรุณาแนบรูปหลักฐาน!'
            );

        }


        if (

            $request->status === 'ยกเลิก' &&

            !$request->cancel_reason

        ) {

            return back()->with(
                'error',
                'กรุณากรอกหมายเหตุการยกเลิก!'
            );

        }


        try {

            DB::transaction(function () use (
                $order,
                $request,
                $oldStatus
            ) {


                /*
                |--------------------------------------------------------------------------
                | คืนสต็อกเมื่อยกเลิก
                |--------------------------------------------------------------------------
                */

                if (

                    $request->status === 'ยกเลิก' &&

                    $oldStatus !== 'ยกเลิก'

                ) {


                    $movements =
                        ProductStockMovement::where(
                            'order_id',
                            $order->id
                        )
                            ->where(
                                'type',
                                'out'
                            )
                            ->get();


                    foreach ($movements as $move) {


                        $stock =
                            ProductStocks::where(
                                'product_id',
                                $move->product_id
                            )
                                ->whereHas(
                                    'unit',
                                    fn ($q) =>
                                    $q->where(
                                        'unit_name',
                                        $move->unit
                                    )
                                )
                                ->first();


                        if ($stock) {

                            $stock->increment(
                                'store_stock',
                                $move->quantity
                            );

                        }


                        ProductStockMovement::create([

                            'order_id' =>
                                $order->id,

                            'product_id' =>
                                $move->product_id,

                            'type' =>
                                'in',

                            'quantity' =>
                                $move->quantity,

                            'unit_quantity' =>
                                $move->unit_quantity,

                            'unit' =>
                                $move->unit,

                            'location' =>
                                'store',

                            'note' =>
                                'คืนสต็อกจากการยกเลิกออเดอร์',

                        ]);

                    }


                    $order->cancel_reason =
                        $request->cancel_reason;

                }


                /*
                |--------------------------------------------------------------------------
                | แนบรูปเมื่อเสร็จสิ้น
                |--------------------------------------------------------------------------
                */

                if ($request->hasFile('proof_image')) {


                    if (

                        $order->proof_image &&

                        Storage::disk('public')
                            ->exists(
                                $order->proof_image
                            )

                    ) {

                        Storage::disk('public')
                            ->delete(
                                $order->proof_image
                            );

                    }


                    $path =
                        $request
                            ->file('proof_image')
                            ->store(
                                'proofs',
                                'public'
                            );


                    $order->proof_image =
                        $path;

                }


                $order->status =
                    $request->status;


                $order->save();

            });


        } catch (\Exception $e) {

            return back()->with(
                'error',
                'เกิดข้อผิดพลาดในการอัปเดตสถานะ'
            );

        }


        return back()->with(
            'success',
            'อัปเดตสถานะสำเร็จ!'
        );

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

        $order =
            Order::findOrFail($id);


        if (

            $order->assigned_to &&

            $order->assigned_to !== Auth::id()

        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'ออเดอร์นี้ถูกรับโดยพนักงานคนอื่นแล้ว'
                );

        }


        $order->assigned_to =
            Auth::id();

        $order->status =
            'กำลังจัดสินค้า';


        $order->save();


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
