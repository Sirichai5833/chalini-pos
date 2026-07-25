<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditSaleItem extends Model
{
    protected $fillable = [
        'credit_sale_id',
        'product_id',
        'product_name',
        'product_unit_id',
        'qty',
        'price',
        'total',
    ];

    // =========================
    // relation ไปบิลเงินเชื่อ
    // =========================
    public function creditSale()
    {
        return $this->belongsTo(
            CreditSale::class,
            'credit_sale_id'
        );
    }

    // =========================
    // relation ไปสินค้า
    // =========================
    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }

    // =========================
    // relation ไปหน่วยสินค้า
    // =========================
    public function unit()
    {
        return $this->belongsTo(
            ProductUnit::class,
            'product_unit_id'
        );
    }
}
