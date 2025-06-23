<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStockMovementsTable extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reason',
        'ref_code',
        'unit',
        'is_free',
        'note',
        'unit_quantity', // เพิ่ม unit_quantity
          'location', // ✅ เพิ่ม location
    ];
}
