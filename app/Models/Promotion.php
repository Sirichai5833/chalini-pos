<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $fillable = [
        'promotion_code', 'name', 'description', 'promotion_type', 'channel', 'member_only',
        'is_stackable', 'usage_limit', 'usage_limit_per_member', 'start_at', 'end_at',
        'priority', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'member_only' => 'boolean', 'is_stackable' => 'boolean', 'is_active' => 'boolean',
            'usage_limit' => 'integer', 'usage_limit_per_member' => 'integer',
            'priority' => 'integer', 'start_at' => 'datetime', 'end_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function conditions(): HasMany { return $this->hasMany(PromotionCondition::class); }
    public function rewards(): HasMany { return $this->hasMany(PromotionReward::class); }
    public function usages(): HasMany { return $this->hasMany(PromotionUsage::class); }
    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()));
    }

    public function scopeForChannel(Builder $query, string $channel): Builder
    {
        return $query->whereIn('channel', ['all', $channel]);
    }
}
