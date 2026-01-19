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
    $order = Order::with('orderItems')->findOrFail($id);
    $oldStatus = $order->status;

    $request->validate([
        'status' => 'required|string',
        'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'cancel_reason' => 'nullable|string|max:500',
    ]);

    // ❌ กันเสร็จสิ้นโดยไม่แนบรูป
    if ($request->status === 'เสร็จสิ้น' && !$request->hasFile('proof_image') && !$order->proof_image) {
        return back()->with('error', 'กรุณาแนบรูปหลักฐาน!');
    }

    // ❌ กันยกเลิกโดยไม่ใส่เหตุผล
    if ($request->status === 'ยกเลิก' && !$request->cancel_reason) {
        return back()->with('error', 'กรุณากรอกหมายเหตุการยกเลิก!');
    }

    try {
        DB::transaction(function () use ($order, $request, $oldStatus) {

            /* =======================
               คืนสต็อกเมื่อยกเลิก
            ======================= */
            if ($request->status === 'ยกเลิก' && $oldStatus !== 'ยกเลิก') {

                $movements = ProductStockMovement::where('order_id', $order->id)
                    ->where('type', 'out')
                    ->get();

                foreach ($movements as $move) {

                    $stock = ProductStocks::where('product_id', $move->product_id)
                        ->whereHas('unit', fn ($q) =>
                            $q->where('unit_name', $move->unit)
                        )
                        ->first();

                    if ($stock) {
                        $stock->increment('store_stock', $move->quantity);
                    }

                    ProductStockMovement::create([
                        'order_id' => $order->id,
                        'product_id' => $move->product_id,
                        'type' => 'in',
                        'quantity' => $move->quantity,
                        'unit_quantity' => $move->unit_quantity,
                        'unit' => $move->unit,
                        'location' => 'store',
                        'note' => 'คืนสต็อกจากการยกเลิกออเดอร์',
                    ]);
                }

                $order->cancel_reason = $request->cancel_reason;
            }

            /* =======================
               แนบรูปเมื่อเสร็จสิ้น
            ======================= */
            if ($request->hasFile('proof_image')) {

                // ลบรูปเก่า (ถ้ามี)
                if ($order->proof_image && Storage::disk('public')->exists($order->proof_image)) {
                    Storage::disk('public')->delete($order->proof_image);
                }

                $path = $request->file('proof_image')
                    ->store('proofs', 'public');

                $order->proof_image = $path;
            }

            $order->status = $request->status;
            $order->save();
        });

   } catch (\Exception $e) {
    dd($e->getMessage(), $e->getTraceAsString());
}


    return back()->with('success', 'อัปเดตสถานะสำเร็จ!');
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
