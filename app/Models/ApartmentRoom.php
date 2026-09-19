<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApartmentRoom extends Model
{
    protected $fillable = ['building', 'floor', 'room_number', 'delivery_zone', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function memberProfiles(): HasMany { return $this->hasMany(MemberProfile::class, 'room_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'room_id'); }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true); }
    public function getDisplayNameAttribute(): string { return "{$this->building}-{$this->room_number}"; }
}
