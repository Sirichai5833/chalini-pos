<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OnlineController extends Controller
{
    // ฟังก์ชันแสดงหน้าของ POS
    public function index()
{
    $products = Product::paginate(12); // ✅ ใช้ paginate
    return view('online.pos', compact('products'));
}
}
