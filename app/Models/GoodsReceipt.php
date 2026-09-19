<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'receipt_code', 'purchase_order_id', 'supplier_id', 'supplier_invoice_no',
        'received_by', 'received_at', 'status', 'subtotal', 'discount_total',
        'tax_amount', 'grand_total', 'note',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime', 'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2', 'tax_amount' => 'decimal:2', 'grand_total' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
    public function items(): HasMany { return $this->hasMany(GoodsReceiptItem::class); }
}
