<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStocks extends Model
{

   protected $fillable = ['product_id', 'unit_id', 'warehouse_stock', 'store_stock'];

   // ใน ProductStock.php
public function product()
{
    return $this->belongsTo(Product::class);
}

public function unit()
{
    return $this->belongsTo(ProductUnit::class, 'unit_id');
}



// ใน ProductBatch.php




    
}

