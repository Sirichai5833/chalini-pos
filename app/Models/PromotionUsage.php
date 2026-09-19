<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsage extends Model
{
    protected $fillable = [
        'promotion_id', 'customer_id', 'sale_id', 'order_id', 'discount_amount', 'used_at',
    ];

    protected function casts(): array
    {
        return ['discount_amount' => 'decimal:2', 'used_at' => 'datetime'];
    }

    public function promotion(): BelongsTo { return $this->belongsTo(Promotion::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
