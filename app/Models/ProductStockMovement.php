<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStockMovement extends Model
{
    protected $table = 'product_stock_movements';

    protected $fillable = [
        'product_id',
            'order_id', // ✅ เพิ่ม order_id
             'stock_receipt_id', // เพิ่มบรรทัดนี้
        'type',
        'quantity',
        'unit',
        'reason',
         'is_free', // ✅ เพิ่มตรงนี้
        'ref_code',
            'note',
          'location', // ✅ เพิ่ม location
          'unit_quantity', // เพิ่ม unit_quantity
           'user_id',
    ];
public function user()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
    // (ไม่จำเป็น แต่ถ้าอยากให้เรียก product ได้ง่าย)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
