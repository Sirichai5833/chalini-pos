<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCheckItem extends Model
{
  protected $fillable = [
    'stock_check_id',
    'product_id',
    'unit_id',
    'product_stock_id',
    'system_qty',
    'real_qty',
    'diff_qty',
];

public function product()
{
    return $this->belongsTo(Product::class);
}

public function unit()
{
    return $this->belongsTo(ProductUnit::class, 'unit_id');
}

}
