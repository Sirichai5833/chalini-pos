<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $slipPath = $request->file('slip')->store('slips', 'public');
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

   public function ordersList()
{
    $orders = Order::with(['orderItems.product', 'user'])
        ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('sale.order', compact('orders'));
}


public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|string',
    ]);

    $order->update(['status' => $request->status]);

    return redirect()->back()->with('success', 'อัปเดตสถานะเรียบร้อยแล้ว');
}
public function orderHistory()
{
    $orders = Order::with(['orderItems.product', 'user'])
        ->whereIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('sale.order-history', compact('orders'));
}

}
