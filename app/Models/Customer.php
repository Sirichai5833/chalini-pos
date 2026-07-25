<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
   protected $fillable = [
    'name',
    'phone',
    'credit_limit',
    'current_debt',
    'is_active'
];
public function creditSales()
{
    return $this->hasMany(
        CreditSale::class
    );
}

public function paymentHistories()
{
    return $this->hasMany(CreditPaymentHistory::class);
}
}
