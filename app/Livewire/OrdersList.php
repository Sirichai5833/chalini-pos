<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


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
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก', 'กำลังจัดสินค้า', 'กำลังจัดส่ง'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('livewire.orders-list', compact('orders'));
    }

public function assignOrder($orderId)
{
    $actor = Auth::user();

    abort_unless(
        $actor &&
            in_array(
                $actor->role,
                ['admin', 'manager', 'owner', 'staff'],
                true
            ),
        403
    );

    DB::transaction(function () use ($actor, $orderId) {
        $order = Order::query()
            ->lockForUpdate()
            ->findOrFail($orderId);

        if (
            !in_array(
                $order->status,
                [
                    'pending',
                    'รอดำเนินการ',
                    'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => 'ออเดอร์ในสถานะนี้ไม่สามารถรับงานได้',
            ]);
        }

        if ($order->assigned_to) {
            throw ValidationException::withMessages([
                'status' => 'ออเดอร์นี้มีคนรับแล้ว',
            ]);
        }

        $order->assigned_to = $actor->id;
        $order->assigned_at = now();
        $order->status = 'รอเจ้าหน้าที่รับคำสั่งซื้อ';
        $order->save();
    });

    // ถ้าคุณใช้ count แสดงจำนวนออเดอร์ใหม่อยู่
    $this->count = Order::where('status', 'รอเจ้าหน้าที่รับคำสั่งซื้อ')
                        ->whereNull('assigned_to')
                        ->count();

    $this->dispatch('notify', type: 'success', message: 'รับออเดอร์เรียบร้อยแล้ว');
}



}
