<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements AuditableContract // 👈 เพิ่ม implements
{
    use HasFactory, Notifiable, Auditable; // 👈 เพิ่ม Auditable

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'image',
        'room_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

   public function getIsAdminAttribute()
{
    return $this->role === 'admin';
}

    public function sales() {
    return $this->hasMany(Sale::class, 'staff_id');  // หรือชื่อฟิลด์ที่คุณใช้
}
}
