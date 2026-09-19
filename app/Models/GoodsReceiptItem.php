<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends Model
{
    protected $fillable = [
        'goods_receipt_id', 'purchase_order_item_id', 'product_id', 'product_unit_id',
        'purchased_quantity', 'bonus_quantity', 'unit_quantity', 'received_quantity_base',
        'unit_cost', 'line_cost', 'batch_code', 'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'purchased_quantity' => 'integer', 'bonus_quantity' => 'integer',
            'total_received_quantity' => 'integer', 'unit_quantity' => 'integer',
            'received_quantity_base' => 'integer', 'unit_cost' => 'decimal:4',
            'line_cost' => 'decimal:2', 'expiry_date' => 'date',
        ];
    }

    public function goodsReceipt(): BelongsTo { return $this->belongsTo(GoodsReceipt::class); }
    public function purchaseOrderItem(): BelongsTo { return $this->belongsTo(PurchaseOrderItem::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function productUnit(): BelongsTo { return $this->belongsTo(ProductUnit::class); }
}
