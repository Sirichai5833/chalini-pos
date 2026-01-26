<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exports\StockCheckExport;
use Maatwebsite\Excel\Facades\Excel;

class StockCheck extends Model
{
    protected $fillable = [
        'check_date',
        'cycle',
        'checked_by',
        'remark',
    ];
 public function user()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    // (ถ้ามี)
    public function items()
    {
        return $this->hasMany(StockCheckItem::class);
    }

}