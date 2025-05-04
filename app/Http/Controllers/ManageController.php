<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category; // ← เพิ่มบรรทัดนี้ถ้าคุณใช้ category
use Illuminate\Support\Facades\Log;

class ManageController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all(); // ดึงข้อมูลหมวดหมู่ทั้งหมดจากฐานข้อมูล
        
        // ถ้ามีการเลือกประเภทสินค้าให้กรองข้อมูล
        if ($request->has('category_id') && $request->category_id != "") {
            $products = Product::where('category_id', $request->category_id)->get();
        } else {
            // ถ้าไม่ได้เลือก category_id หรือเลือก "ทั้งหมด" ให้แสดงสินค้าทั้งหมด
            $products = Product::all();
        }
        return view('products.Allproducts', compact('products', 'categories')); // ส่งทั้ง $products และ $categories ไปยัง view
    }

    public function create()
{
    $categories = Category::all(); // ดึงหมวดหมู่ทั้งหมดจากฐานข้อมูล
    return view('products.create', compact('categories')); // ส่งตัวแปร categories ไปยัง view

    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'selling_price' => 'required|numeric|min:0',
        'track_stock' => 'nullable|boolean',
    ]);

    // จัดการอัปโหลดรูปภาพ
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
    } else {
        $imagePath = null;
    }

    // สร้างสินค้า
    $product = Product::create([
        
        'name' => $request->name,
        'barcode' => $request->barcode,
        'sku' => $request->sku,
        'unit' => $request->unit,
        'cost_price' => floatval($request->input('cost_price', 0)),
        'selling_price' => floatval($request->input('selling_price', 0)),
        'promotion_price' => floatval($request->input('promotion_price', 0)),
        'has_gift' => $request->has('has_gift'),
        'gift_name' => $request->gift_name,
        'stock' => $request->input('stock', 0),
        'track_stock' => $request->has('track_stock'),
        'is_online' => $request->has('is_online'),
        'is_active' => $request->has('is_active'),
        'image' => $imagePath,
        'description' => $request->description,
        'qr_code' => $request->qr_code,
        'category_id' => $request->category_id,
    ]);

    return redirect()->route('products.index')->with('success', 'Product created successfully!');
}

public function edit($id)
{
    $product = Product::findOrFail($id);  // ดึงข้อมูลสินค้าตาม id
    $categories = Category::all();  // ดึงข้อมูลหมวดหมู่ทั้งหมด

    return view('products.edit', compact('product', 'categories'));
}



public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'selling_price' => 'required|numeric|min:0',
        'track_stock' => 'nullable|boolean',
    ]);

    $product = Product::findOrFail($id);

    // อัปเดตรูปภาพหากมี
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $product->image = $imagePath;
    }

    $product->update([
        'name' => $request->name,
        'barcode' => $request->barcode,
        'sku' => $request->sku,
        'unit' => $request->unit,
        'cost_price' => floatval($request->input('cost_price', 0)),
        'selling_price' => floatval($request->input('selling_price', 0)),
        'promotion_price' => floatval($request->input('promotion_price', 0)),
        'has_gift' => $request->has('has_gift'),
        'gift_name' => $request->gift_name,
        'stock' => $request->input('stock', 0),
        'track_stock' => $request->has('track_stock'),
        'is_online' => $request->has('is_online'),
        'is_active' => $request->has('is_active'),
        'description' => $request->description,
        'qr_code' => $request->qr_code,
        'category_id' => $request->category_id,
    ]);

    return redirect()->route('products.index')->with('success', 'อัปเดตสินค้าสำเร็จ');
}

public function destroy($id)
{
    $product = Product::findOrFail($id);  // ค้นหาสินค้าตาม ID

    // ลบสินค้าจากฐานข้อมูล
    $product->delete();

    // ส่งกลับไปยังหน้ารายการสินค้าและแสดงข้อความสำเร็จ
    return redirect()->route('products.index')->with('success', 'สินค้าถูกลบเรียบร้อยแล้ว');
}



    

    
}
