<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerOrders extends Component
{
    use WithPagination;

    public $latestStatuses = [];

    // ✅ เพิ่มตัวนี้กลับมา
    public $statusFilter = 'all';

    private function ordersQuery()
    {
        $query = Order::with('orderItems.product')
            ->where('user_id', Auth::id())
            ->latest();

        // ✅ ใช้ filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query;
    }

    public function updatedStatusFilter()
    {
        // 🔥 เวลาเลือก dropdown → โหลดใหม่ทันที
        $this->resetPage();
        $this->latestStatuses = [];
    }

    public function pollOrders()
    {
        $updatedOrders = Order::query()
            ->where('user_id', Auth::id())
            ->whereIn('id', array_keys($this->latestStatuses))
            ->pluck('status', 'id');

        foreach ($updatedOrders as $id => $status) {
            if (
                isset($this->latestStatuses[$id]) &&
                $status !== $this->latestStatuses[$id]
            ) {
                $this->dispatch('order-status-changed', id: $id, status: $status);
            }

            $this->latestStatuses[$id] = $status;
        }
    }

    public function render()
    {
        $orders = $this->ordersQuery()->paginate(15);

        foreach ($orders as $order) {
            $this->latestStatuses[$order->id] = $order->status;
        }

        return view('livewire.customer-orders', [
            'orders' => $orders,
        ]);
    }

}
