<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'barcode', 'sku', 'category_id', 'description', 'image', 'is_active', 'is_online'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function stock()
    {
        return $this->hasOne(ProductStocks::class); 
    }

    public function stockMovements()
    {
        return $this->hasMany(ProductStockMovementsTable::class);
    }



    // ฟังก์ชันดึงจำนวนสินค้าคงเหลือ
    public function getStockQuantityAttribute()
    {
        $stock = $this->stock; // ดึงข้อมูล ProductStock จากความสัมพันธ์
        if ($stock) {
            return $stock->warehouse_stock + $stock->store_stock; // คำนวณสินค้าคงเหลือ
        }
        return 0; // หากไม่มีข้อมูลสินค้าในสต็อก, คืนค่า 0
    }

    public function defaultUnit()
{
    return $this->hasOne(ProductUnit::class)->orderBy('id'); // ดึงหน่วยแรกเป็นหลัก
}

}
