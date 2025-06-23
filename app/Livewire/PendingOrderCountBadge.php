<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;

class PendingOrderCountBadge extends Component
{
    public $count = 0;
    private $previousCount = 0;
    private $notified = false;

    public function mount()
    {
        $this->previousCount = Order::where('status', 'pending')->count();
        $this->count = $this->previousCount;
    }

    public function pollCount()
    {
        $current = Order::where('status', 'pending')->count();

        if ($current > $this->previousCount && !$this->notified) {
            // ✅ ใช้ dispatch แบบใหม่ของ Livewire v3
            $this->dispatch('new-order');
            $this->notified = true;
        }

        if ($current <= $this->previousCount) {
            $this->notified = false;
        }

        $this->previousCount = $current;
        $this->count = $current;
    }

    public function render()
    {
        return view('livewire.pending-order-count-badge');
    }
}
