<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProfile extends Model
{
    protected $fillable = [
        'customer_id', 'room_id', 'member_code', 'status', 'verified_at', 'verified_by',
        'joined_at', 'moved_out_at', 'online_order_allowed',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime', 'joined_at' => 'date', 'moved_out_at' => 'date',
            'online_order_allowed' => 'boolean',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function room(): BelongsTo { return $this->belongsTo(ApartmentRoom::class, 'room_id'); }
    public function verifier(): BelongsTo { return $this->belongsTo(User::class, 'verified_by'); }

    public function scopeOnlineAllowed(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('online_order_allowed', true)
            ->whereNotNull('verified_at')->whereHas('room', fn (Builder $q) => $q->where('is_active', true));
    }

    public function canOrderOnline(): bool
    {
        return $this->status === 'active' && $this->online_order_allowed
            && $this->verified_at !== null && $this->room?->is_active === true;
    }
}
