<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_code', 'supplier_id', 'ordered_by', 'order_date', 'expected_date', 'status',
        'subtotal', 'discount_total', 'tax_amount', 'grand_total', 'note',
        'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date', 'expected_date' => 'date', 'approved_at' => 'datetime',
            'subtotal' => 'decimal:2', 'discount_total' => 'decimal:2',
            'tax_amount' => 'decimal:2', 'grand_total' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function orderedBy(): BelongsTo { return $this->belongsTo(User::class, 'ordered_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts(): HasMany { return $this->hasMany(GoodsReceipt::class); }
}
