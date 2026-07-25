<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// 🟢 นำเข้า Model Activity และ Product
use Spatie\Activitylog\Models\Activity; 
use App\Models\Product;

class ProductUnit extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'product_units';

    protected $fillable = [
        'product_id',
        'unit_name',
        'unit_quantity',
        'price',
        'wholesale',
        'cost_price',
        'store_stock',
        'warehouse_stock',
        'barcode',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->useLogName('product') // 🟢 ใช้ชื่อกลุ่มเดียวกันกับ Product หลัก
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // 🟢 ฟังก์ชันพิเศษ: ย้ายประวัติของหน่วยนี้ไปแสดงผลที่ตัวสินค้าหลัก (Product)
    public function tapActivity(Activity $activity, string $eventName)
    {
        // บังคับเปลี่ยน subject ให้เป็น Product ตัวแม่ เพื่อให้ประวัติไปรวมกันที่เดียว
        $activity->subject_type = Product::class;
        $activity->subject_id = $this->product_id;
        
        // แนบข้อมูลเพิ่มเติมเพื่อให้รู้ใน Log ว่าเป็นของหน่วยสินค้าชื่ออะไร (เช่น กล่อง, แพ็ค, ชิ้น)
        $properties = $activity->properties->toArray();
        $properties['unit_name'] = $this->unit_name;
        $activity->properties = $properties;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStocks::class, 'product_id');
    }

    public function stock()
    {
        return $this->hasOne(ProductStocks::class, 'unit_id', 'id');
    }
}