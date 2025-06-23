<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Order;

class OrdersList extends Component
{
    use WithPagination;

    // ลบ public $orders; ออก

    protected $listeners = ['orderUpdated' => '$refresh'];

    public function render()
    {
        $orders = Order::with(['orderItems', 'user'])
            ->whereNotIn('status', ['เสร็จสิ้น', 'ยกเลิก'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.orders-list', compact('orders'));
    }
}
