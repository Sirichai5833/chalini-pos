<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrdersList extends Component
{
    use WithPagination;

    // ลบ public $orders; ออก

    protected $listeners = ['orderUpdated' => '$refresh'];


    public $count = 0;

public function pollCount()
{
    $newCount = Order::where('status', 'รอดำเนินการ')
                    ->whereNull('assigned_to')
                    ->count();

    // แจ้งเตือนเฉพาะกรณีมีออเดอร์ใหม่เข้ามา และยังไม่มีใครรับ
    if ($newCount > $this->count) {
        $this->dispatchBrowserEvent('new-order');
    }

    $this->count = $newCount;
}

    public function render()
    {
        $orders = Order::with(['orderItems', 'user'])
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก', 'กำลังดำเนินการ', 'กำลังจัดส่ง'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.orders-list', compact('orders'));
    }

  public function assignOrder($orderId)
{
    $order = Order::find($orderId);

    if ($order->assigned_to) {
        $this->dispatch('notify', ['type' => 'error', 'message' => 'ออเดอร์นี้มีคนรับแล้ว']);
        return;
    }

    $order->assigned_to = Auth::id();
    $order->assigned_at = now();

    // ✅ เพิ่มบรรทัดนี้เพื่อเปลี่ยนสถานะเป็น "รอดำเนินการ"
    $order->status = 'รอดำเนินการ';

    $order->save();

    // ถ้าคุณใช้ count แสดงจำนวนออเดอร์ใหม่อยู่
    $this->count = Order::where('status', 'รอดำเนินการ')
                        ->whereNull('assigned_to')
                        ->count();

    $this->dispatch('notify', type: 'success', message: 'รับออเดอร์เรียบร้อยแล้ว');
}



}
