<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_code', 'payable_type', 'payable_id', 'customer_id', 'payment_method',
        'amount', 'status', 'transaction_reference', 'proof_path', 'received_by',
        'paid_at', 'note',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    }

    public function payable(): MorphTo { return $this->morphTo(); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
    public function refunds(): HasMany { return $this->hasMany(Refund::class); }
}
