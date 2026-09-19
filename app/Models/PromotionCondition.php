<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionCondition extends Model
{
    protected $fillable = ['promotion_id', 'condition_type', 'operator', 'condition_value'];
    protected function casts(): array { return ['condition_value' => 'array']; }
    public function promotion(): BelongsTo { return $this->belongsTo(Promotion::class); }
}
