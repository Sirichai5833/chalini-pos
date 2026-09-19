<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = ['product_id', 'quantity_on_hand', 'reserved_quantity', 'track_stock'];

    protected function casts(): array
    {
        return [
            'quantity_on_hand' => 'integer', 'reserved_quantity' => 'integer',
            'available_quantity' => 'integer', 'track_stock' => 'boolean',
        ];
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    public function hasAvailable(int $quantity): bool
    {
        return !$this->track_stock || $this->available_quantity >= $quantity;
    }
}
