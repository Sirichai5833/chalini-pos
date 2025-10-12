<?php

namespace App\Http\Controllers;
use App\Models\Product;

class BarcodeController extends Controller
{
    public function index()
    {
        // ดึง products พร้อมหน่วยสินค้า (barcodes)
        $products = Product::with('productUnits')->get();
        return view('barcodes.index', compact('products'));
    }

    public function showByBarcode($barcode)
{
    $unit = \App\Models\ProductUnit::where('barcode', $barcode)->first();

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

    return response()->json(['success' => false]);
}

}