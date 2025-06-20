<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStockMovement extends Model
{
    protected $table = 'product_stock_movements';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'unit',
        'reason',
        'ref_code',
          'location', // ✅ เพิ่ม location
          'unit_quantity', // เพิ่ม unit_quantity
    ];

    // (ไม่จำเป็น แต่ถ้าอยากให้เรียก product ได้ง่าย)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
