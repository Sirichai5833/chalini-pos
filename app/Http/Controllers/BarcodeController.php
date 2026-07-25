<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Category;

class BarcodeController extends Controller
{
    public function index()
    {
        // ดึงสินค้าพร้อมหน่วยสินค้าและหมวดหมู่
        $products = Product::with([
            'productUnits',
            'category'
        ])
            ->where('is_active', true)
            ->get();

        // ดึงหมวดหมู่ทั้งหมด
        $categories = Category::orderBy('name')->get();

        return view('barcodes.index', compact(
            'products',
            'categories'
        ));
    }

    public function showByBarcode($barcode)
    {
        $unit = ProductUnit::where('barcode', $barcode)->first();

        if ($unit) {

            $product = $unit->product;

            return response()->json([
                'success' => true,

                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->unit_name,
                    'price' => $unit->price,
                    'wholesale' => $unit->wholesale,
                    'barcode' => $unit->barcode,
                    'quantity_per_unit' => $unit->unit_quantity,
                ]
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}