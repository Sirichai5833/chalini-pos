<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRule extends Model
{
    protected $fillable = [
        'name', 'spend_amount', 'earn_points', 'redeem_points', 'redeem_value',
        'minimum_redeem_points', 'maximum_redeem_percent', 'points_expire_days',
        'start_at', 'end_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'spend_amount' => 'decimal:2', 'earn_points' => 'integer', 'redeem_points' => 'integer',
            'redeem_value' => 'decimal:2', 'minimum_redeem_points' => 'integer',
            'maximum_redeem_percent' => 'decimal:2', 'points_expire_days' => 'integer',
            'start_at' => 'datetime', 'end_at' => 'datetime', 'is_active' => 'boolean',
        ];
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()));
    }
}
