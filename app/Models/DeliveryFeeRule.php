<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeliveryFeeRule extends Model
{
    protected $fillable = [
        'name', 'fulfillment_type', 'delivery_zone', 'fee', 'minimum_order',
        'free_delivery_threshold', 'start_at', 'end_at', 'priority', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2', 'minimum_order' => 'decimal:2',
            'free_delivery_threshold' => 'decimal:2', 'start_at' => 'datetime',
            'end_at' => 'datetime', 'priority' => 'integer', 'is_active' => 'boolean',
        ];
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()))
            ->orderByDesc('priority');
    }

    public function calculate(float $subtotal): float
    {
        if ($subtotal < (float) $this->minimum_order) return 0.0;
        if ($this->free_delivery_threshold !== null && $subtotal >= (float) $this->free_delivery_threshold) return 0.0;
        return (float) $this->fee;
    }
}
