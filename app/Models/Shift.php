<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
   protected $fillable = [
    'user_id',
    'shift_type_id',
    'opening_cash',
    'expected_cash',
    'closing_cash',
    'cash_difference',
    'expected_qr',
    'closing_qr',
    'qr_difference',
    'opened_at',
    'closed_at',
    'status',
    'note',
    'closed_by',
];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',

        'opening_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',

        // QR Payment
        'expected_qr' => 'decimal:2',
        'closing_qr' => 'decimal:2',
        'qr_difference' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
