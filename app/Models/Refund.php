<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $table = 'payment_refunds';

    protected $fillable = [
        'refund_code', 'payment_id', 'amount', 'reason', 'status', 'refunded_by', 'refunded_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'refunded_at' => 'datetime'];
    }

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function refundedBy(): BelongsTo { return $this->belongsTo(User::class, 'refunded_by'); }
}
