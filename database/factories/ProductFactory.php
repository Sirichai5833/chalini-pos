<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductStock;
use App\Models\ProductBatch;
use App\Models\ProductStocks;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// ===== Product Factory =====
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(2, true)), // ใช้คำสุ่ม 2 คำมาเป็นชื่อสินค้า เช่น "Smart Watch"
            'barcode' => $this->faker->ean13(),
            'sku' => strtoupper(Str::random(10)),
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence(),
            'image' => null,
            'is_active' => true,
            'is_online' => true,
        ];
    }
}

// ===== ProductUnit Factory =====
class ProductUnitFactory extends Factory
{
    protected $model = ProductUnit::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'unit_name' => 'ชิ้น',
            'unit_quantity' => 1,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'cost_price' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}

// ===== ProductStock Factory =====
class ProductStockFactory extends Factory
{
    protected $model = ProductStocks::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_stock' => $this->faker->numberBetween(0, 500),
            'store_stock' => $this->faker->numberBetween(0, 100),
            'track_stock' => true,
        ];
    }
}

// ===== ProductBatch Factory =====

