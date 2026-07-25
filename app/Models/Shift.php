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
    'opened_at',
    'closed_at',
    'status',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class);
    }
}
