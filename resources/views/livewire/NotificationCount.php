<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ProductStocks;
use Illuminate\Support\Carbon;

class NotificationCount extends Component
{
    public $lowStockCount = 0;
    public $expireCount = 0;

    protected $listeners = ['refreshNotification' => '$refresh'];

    public function mount()
    {
        $this->fetchCounts();
    }

    public function fetchCounts()
    {
        $this->lowStockCount = ProductStocks::where('store_stock', '<', 10)->count();

        // สมมติ expire_date เก็บที่ product_stocks.expire_date (ถ้าเก็บใน products เปลี่ยนโมเดล)
        $this->expireCount = ProductStocks::whereDate('expire_date', '<=', Carbon::now()->addDays(7))->count();
    }

    public function render()
    {
        return view('livewire.notification-count');
    }
}
