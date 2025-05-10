<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;
class Sale extends Model
{
      

    protected $fillable = [
         'sale_date' => 'datetime',
         'staff_id',
        'sale_type',
        'total_price',
        // เพิ่มฟิลด์อื่นๆ ที่ต้องการทำ mass assignment
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

public function staff()
{
    return $this->belongsTo(User::class, 'staff_id'); // หากมี field staff_id
}

}
