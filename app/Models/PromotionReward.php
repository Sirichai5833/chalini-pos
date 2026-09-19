<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionReward extends Model
{
    protected $fillable = ['promotion_id', 'reward_type', 'reward_value', 'maximum_discount'];
    protected function casts(): array { return ['reward_value' => 'array', 'maximum_discount' => 'decimal:2']; }
    public function promotion(): BelongsTo { return $this->belongsTo(Promotion::class); }
}
