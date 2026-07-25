<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory(1000)->create()->each(function ($product) {

            // หน่วยชิ้น
            $piece = ProductUnit::factory()->create([
                'product_id' => $product->id,
                'unit_name' => 'ชิ้น',
                'unit_quantity' => 1,
            ]);

            // หน่วยแพ็ค
            $pack = ProductUnit::factory()->pack()->create([
                'product_id' => $product->id,
            ]);

            // หน่วยลัง
            $box = ProductUnit::factory()->box()->create([
                'product_id' => $product->id,
            ]);

            // stock ชิ้น
            ProductStocks::factory()->create([
                'product_id' => $product->id,
                'unit_id' => $piece->id,
            ]);

            // stock แพ็ค
            ProductStocks::factory()->create([
                'product_id' => $product->id,
                'unit_id' => $pack->id,
            ]);

            // stock ลัง
            ProductStocks::factory()->create([
                'product_id' => $product->id,
                'unit_id' => $box->id,
            ]);
        });
    }
}

