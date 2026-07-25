<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class PendingOrderCountBadge extends Component
{
    public $count = 0;

    private $previousCount = 0;
    private $notified = false;


    public function mount()
    {
        $this->previousCount = $this->getPendingCount();
        $this->count = $this->previousCount;
    }


    public function pollCount()
    {
        $current = $this->getPendingCount();


        // มีออเดอร์ใหม่
        if ($current > $this->previousCount && !$this->notified) {

            // Livewire v3
            $this->dispatch('new-order');

            $this->notified = true;
        }


        // ถ้าจำนวนลดลง ให้เปิดการแจ้งเตือนใหม่
        if ($current <= $this->previousCount) {
            $this->notified = false;
        }


        $this->previousCount = $current;
        $this->count = $current;
    }


    /**
     * ดึงจำนวน order ที่รอ
     * cache 10 วินาที ลด query ซ้ำ
     */
    private function getPendingCount()
    {
        return Cache::remember(
            'pending_order_count',
            10,
            function () {

                return Order::where(
                    'status',
                    'รอเจ้าหน้าที่รับคำสั่งซื้อ'
                )->count();

            }
        );
    }


    public function render()
    {
        return view('livewire.pending-order-count-badge');
    }
}