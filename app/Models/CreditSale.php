<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditSale extends Model
{
   protected $fillable = [
    'customer_id',
    'total_amount',
    'paid_amount',
    'remaining_amount',
    'due_date',
    'status'
];
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

public function items()
{
    return $this->hasMany(CreditSaleItem::class);
}
}
