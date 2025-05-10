<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    // กำหนดชื่อของตาราง
    protected $table = 'product_units';

    // กำหนด field ที่สามารถ fill ได้
    protected $fillable = [
        'product_id',
        'unit_name',
        'unit_quantity', // ✅ ต้องมีตัวนี้
        'price',
        'wholesale', // <<< อันนี้ต้องมี!
        'cost_price',
        'barcode',
    ];
    

    // สร้างความสัมพันธ์กับ Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
