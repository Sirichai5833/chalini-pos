<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
class Product extends Model
{
     use HasFactory, LogsActivity;
  

    protected $fillable = [
        'name', 'barcode', 'sku', 'category_id', 'description', 'image', 'is_active', 'is_online'
    ];
    
    protected static $logName = 'product';
    protected static $logAttributes = ['name', 'quantity'];
    protected static $logOnlyDirty = true;

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
        $stock = $this->stock->first();// ดึงข้อมูล ProductStock จากความสัมพันธ์
        if ($stock) {
            return $stock->warehouse_stock + $stock->store_stock; // คำนวณสินค้าคงเหลือ
        }
        return 0; // หากไม่มีข้อมูลสินค้าในสต็อก, คืนค่า 0
    }

    public function defaultUnit()
{
    return $this->hasOne(ProductUnit::class)->orderBy('id'); // ดึงหน่วยแรกเป็นหลัก
}

public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // ให้ log ทุกฟิลด์ที่อยู่ใน $fillable
            ->useLogName('product') // ตั้งชื่อ log (เช่น 'product')
            ->logOnlyDirty(); // log เฉพาะค่าที่มีการเปลี่ยนแปลง
    }

public function activities()
{
    return $this->morphMany(Activity::class, 'subject')->latest();
}

public function orderItems()
{
    return $this->hasMany(OrderItem::class, 'product_id', 'id');
}


// สมมติ orderItems เชื่อมกับ product_unit_id ใน OrderItem
public function orderItemsThroughUnits()
{
    return $this->hasManyThrough(
        OrderItem::class,
        ProductUnit::class,
        'product_id',        // Foreign key on ProductUnit table...
        'product_unit_id',   // Foreign key on OrderItem table...
        'id',                // Local key on Product table...
        'id'                 // Local key on ProductUnit table...
    );
}

public function productStocks()
{
    return $this->hasMany(ProductStocks::class);
}

}
