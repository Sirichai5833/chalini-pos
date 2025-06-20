<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_unit_id',
        'quantity',
        'price',
        'total',
    ];

    // ความสัมพันธ์กับ Order (หลาย OrderItem เป็นของ 1 Order)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ความสัมพันธ์กับ Product (OrderItem หนึ่งอ้างถึง 1 สินค้า)
    public function productUnit()
{
    return $this->belongsTo(ProductUnit::class);
}

public function product()
{
    return $this->hasOneThrough(
        Product::class,
        ProductUnit::class,
        'id',        // local key on product_units
        'id',        // local key on products
        'product_unit_id', // foreign key on order_items
        'product_id'       // foreign key on product_units
    );
}
}
