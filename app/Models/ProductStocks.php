<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStocks extends Model
{

   protected $fillable = ['product_id', 'unit_id', 'warehouse_stock', 'store_stock'];


    
}

