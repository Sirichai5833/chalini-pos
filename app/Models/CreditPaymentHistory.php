<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditPaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'credit_sale_id',
        'payment_method',
        'amount',
        'user_id',
        'note'
    ];

    // =========================
    // relation ลูกค้า
    // =========================
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // =========================
    // relation บิลเครดิต
    // =========================
    public function creditSale()
    {
        return $this->belongsTo(CreditSale::class);
    }

    // =========================
    // relation ผู้รับชำระ
    // =========================
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}