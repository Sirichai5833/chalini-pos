<?php
namespace App\Http\Livewire;

use Livewire\Component;

class NotificationCount extends Component
{
    public $lowStockCount;
    public $expireCount;

    protected $listeners = ['refreshNotification' => '$refresh'];

    public function render()
    {
        // นี่เป็นตัวอย่าง ควรดึงข้อมูลจาก DB จริง
        $this->lowStockCount = \App\Models\Product::where('stock', '<', 10)->count();
        $this->expireCount = \App\Models\Product::whereDate('expire_date', '<=', now()->addDays(7))->count();

        return view('livewire.notification-count');
    }
}
