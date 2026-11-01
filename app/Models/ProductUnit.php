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
        ->useLogName('product')
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
}

public function tapActivity(Activity $activity, string $eventName)
{
    $activity->subject_type = Product::class;
    $activity->subject_id = $this->product_id;

    $properties = $activity->properties->toArray();

    $properties['unit_name'] = $this->unit_name;
    $properties['unit_id'] = $this->id;

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