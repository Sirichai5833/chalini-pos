<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStocks;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;





class OrderController extends Controller
{
    public function checkout()
    {
        $member = Auth::user();
        return view('online.checkout', compact('member'));
    }

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
                $slipPath = null;
                if ($request->hasFile('slip')) {
                    $slipPath = Cloudinary::upload(
                        $request->file('slip')->getRealPath(),
                        [
                            'folder' => 'slips',
                        ]
                    )->getSecurePath();
                }
            }

            $order = new Order();
            $order->order_code = 'ORD' . strtoupper(uniqid());
            $order->user_id = Auth::id();
            $order->payment_method = $request->payment_method;
            $order->total_amount = $request->total_amount;
            $order->status = 'รอดำเนินการ';
            $order->slip_path = $slipPath;
            $order->save();

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_unit_id' => $item['product_unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();
            // \Livewire\Livewire::emit('orderUpdated'); // ✅ ให้ Livewire รีโหลด
            return redirect()->route('online.track')->with('success', 'ทำรายการสำเร็จ! คำสั่งซื้อ #' . $order->order_code);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function track()
    {
        // ดึงคำสั่งซื้อทั้งหมดของผู้ใช้ที่ล็อกอิน
        $orders = Order::with(['orderItems.product', 'user'])
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('online.track', compact('orders'));
    }






public function updateStatus(Request $request, $id)
{
    $order = Order::find($id);

    if (!$order) {
        return back()->with('error', 'ไม่พบออเดอร์');
    }

    $status = $request->input('status');

    if (!$status) {
        return back()->with('error', 'สถานะไม่ถูกส่งมา');
    }

    $order->status = $status;

    if ($status === 'เสร็จสิ้น' && $request->hasFile('proof_image')) {

        $upload = Cloudinary::upload(
            $request->file('proof_image')->getRealPath()
        );

        if (!$upload) {
            return back()->with('error', 'อัปโหลดรูปไม่สำเร็จ');
        }

        $order->proof_image = $upload->getSecurePath();
    }

    if ($status === 'ยกเลิก') {
        $order->cancel_reason = $request->input('cancel_reason');
    }

    $order->save();

    return back()->with('success', 'อัปเดตสถานะสำเร็จ');
}

    public function orderHistory()
    {
        $orders = Order::with(['orderItems.product', 'user'])
            ->whereIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sale.order-history', compact('orders'));
    }

    public function acceptOrder($id)
    {
        $order = Order::findOrFail($id);

        if ($order->assigned_to && $order->assigned_to !== Auth::id()) {
            return redirect()->back()->with('error', 'ออเดอร์นี้ถูกรับโดยพนักงานคนอื่นแล้ว');
        }

        $order->assigned_to = Auth::id();
        $order->status = 'กำลังดำเนินการ';
        $order->save();

        return redirect()->route('orders.my')->with('success', 'รับออเดอร์เรียบร้อยแล้ว');
    }


    public function myOrders()
    {
        $orders = Order::with(['user', 'orderItems.product', 'orderItems.productUnit'])
            ->where('assigned_to', Auth::id())
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
            ->orderBy('created_at', 'asc') // เรียงจากเก่ามากไปใหม่
            ->latest()
            ->get();

        return view('sale.show-order', compact('orders'));
    }



    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.product', 'orderItems.productUnit'])->findOrFail($id);

        // ตรวจสอบว่า user คนนี้เป็นคนรับออเดอร์หรือไม่
        if ($order->assigned_to !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ดูคำสั่งซื้อนี้');
        }

        return view('sale.show-order', compact('order'));
    }

    public function ordersList()
    {
        $orders = Order::with(['user', 'orderItems.product', 'orderItems.productUnit'])
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sale.order', compact('orders'));
    }
}
