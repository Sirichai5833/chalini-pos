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
}