<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    // กำหนดค่าที่สามารถกรอกได้
    protected $fillable = [
        'product_id',
        'quantity',
        'expiry_date',
        'batch_code', // เพิ่ม batch_code
    ];

    // กำหนดการแปลงประเภทข้อมูล เช่น วันที่
    protected $casts = [
        'expiry_date' => 'date',
    ];

    // สร้างความสัมพันธ์กับ Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
