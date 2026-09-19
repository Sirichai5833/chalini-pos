<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'customer_id', 'transaction_type', 'points', 'balance_after', 'reference_type',
        'reference_id', 'description', 'expires_at', 'created_by',
    ];

    protected function casts(): array
    {
        return ['points' => 'integer', 'balance_after' => 'integer', 'expires_at' => 'datetime'];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
