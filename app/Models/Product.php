<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // กำหนดฟิลด์ที่สามารถกรอกได้ (Mass Assignment)
    protected $fillable = [
        'name', 'selling_price', 'barcode', 'sku', 'unit', 'cost_price',
        'promotion_price', 'has_gift', 'gift_name', 'stock',
        'track_stock', 'is_online', 'is_active', 'image',
        'description', 'qr_code', 'category_id',
    ];
    
    
}
