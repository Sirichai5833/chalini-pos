<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerOrders extends Component
{
    public $orders = [];
    public $latestStatuses = [];

    public function mount()
    {
        $this->fetchOrders();
    }

    public function fetchOrders()
    {
        $this->orders = Order::with('orderItems.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        foreach ($this->orders as $order) {
            $this->latestStatuses[$order->id] = $order->status;
        }
    }

    public function pollOrders()
    {
        $updatedOrders = Order::where('user_id', Auth::id())->get();

        foreach ($updatedOrders as $order) {
            if (isset($this->latestStatuses[$order->id]) && $order->status !== $this->latestStatuses[$order->id]) {
                $this->dispatch('order-status-changed', id: $order->id, status: $order->status);
            }

            $this->latestStatuses[$order->id] = $order->status;
        }

        $this->fetchOrders();
    }

    public function render()
    {
        return view('livewire.customer-orders');
    }
}
