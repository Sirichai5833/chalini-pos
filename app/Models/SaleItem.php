<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'staff_id',
        'product_id',
        'product_unit_id',
        'quantity',
        'price',
        'unit_quantity',  // เพิ่ม unit_quantity ที่นี่
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

   public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }
}
