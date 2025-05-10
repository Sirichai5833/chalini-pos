<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class UnitController extends Controller
{
    public function index()
    {
        $units = ProductUnit::all();
        $products = Product::all(); // <-- You need to fetch all products
        return view('units.index', compact('units', 'products'));
    }

    public function create()
    {
        $products = Product::all();  // ดึงข้อมูลสินค้าทั้งหมด
        return view('units.create', compact('products'));
    }


public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required',
        'unit_name' => 'required',
        'unit_quantity' => 'required|integer',
        'price' => 'required|numeric',
    ]);

    $barcode = $request->barcode ?? 'BC' . now()->format('ymdHis') . rand(100, 999);

    ProductUnit::create([
        'product_id' => $request->product_id,
        'unit_name' => $request->unit_name,
        'unit_quantity' => $request->unit_quantity,
        'barcode' => $barcode,
        'price' => $request->price,
        'cost_price' => $request->cost_price,
    ]);

    return redirect()->route('units.index')->with('success', 'เพิ่มหน่วยนับสำเร็จ');
}



    public function edit(ProductUnit $productUnit)
    {
        $products = Product::all(); // ดึงข้อมูลสินค้าทั้งหมด
        return view('units.edit', compact('productUnit', 'products'));
    }

    public function update(Request $request, ProductUnit $productUnit)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit_name' => 'required|string|max:255',
            'conversion_rate' => 'required|integer',
            'price' => 'required|numeric',
            'is_wholesale' => 'required|boolean',
            'barcode' => 'required|string|max:255|unique:product_units,barcode,' . $productUnit->id, // เช็กว่าไม่ซ้ำ (ยกเว้นตัวเอง)
        ]);

        $productUnit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Unit updated successfully');
    }

    public function destroy(ProductUnit $productUnit)
    {
        $productUnit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully');
    }



public function byProduct(Request $request)
{
    $products = Product::all();
    $units = [];

    if ($request->has('product_id')) {
        $units = ProductUnit::where('product_id', $request->product_id)->get();
    }

    return view('units.index', compact('products', 'units'));
}

}
