<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
          'order_code',
        'user_id',
        'payment_method',
        'status',
         'slip_path', 
        'tracking_number',
        'order_date',
        'total_amount',
    ];
     public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    // ความสัมพันธ์กับ OrderItem (1 คำสั่งซื้อ มีหลาย OrderItem)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ความสัมพันธ์กับ User (สมาชิกที่สั่งซื้อ)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
