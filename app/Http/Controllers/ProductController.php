<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductStockMovement;
use App\Models\ProductStockMovementsTable;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // แสดงรายการสินค้า
    public function index(Request $request)
    {
        $categories = Category::all();
        $products = Product::query();

        // กรองตามประเภทถ้ามี
        if ($request->filled('category_id')) {
            $products->where('category_id', $request->category_id);
        }

        $products = $products->with(['category', 'stock', 'defaultUnit'])->latest()->get();

        return view('products.index', compact('products', 'categories'));
    }

    // แสดงฟอร์มเพิ่มสินค้า
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // บันทึกสินค้าใหม่พร้อมหน่วยนับ
    public function storeWithUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'units' => 'required|array|min:1',
            'units.*.unit_name' => 'required|string|max:255',
            'units.*.unit_quantity' => 'required|integer|min:1',
            'units.*.unit_barcode' => 'nullable|string|max:255',
            'units.*.price' => 'required|numeric',
            'units.*.wholesale' => 'required|numeric',
            'units.*.cost_price' => 'nullable|numeric',
        ]);

        $product = Product::create($request->only(['name', 'category_id', 'barcode', 'sku', 'description']));

        // จัดการรูปภาพ
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
            $product->save();
        }

        foreach ($request->units as $unit) {
            // แปลงชื่อ unit_barcode ให้กลายเป็น barcode ที่ model ใช้
            $unit['barcode'] = $unit['unit_barcode'];
            unset($unit['unit_barcode']);

            ProductUnit::create(array_merge($unit, ['product_id' => $product->id]));
        }


        return redirect()->route('product.product.index')->with('success', 'เพิ่มสินค้าและหน่วยนับสำเร็จ');
    }

    // แสดงฟอร์มแก้ไข
    public function edit($id)
    {
        $product = Product::with('productUnits')->findOrFail($id);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // อัปเดตข้อมูลสินค้าพร้อมหน่วยนับ
    public function updateWithUnit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'barcode' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'units' => 'required|array|min:1',
            'units.*.unit_name' => 'required|string|max:255',
            'units.*.unit_quantity' => 'required|integer|min:1',
            'units.*.unit_barcode' => 'required|string|max:255',
            'units.*.price' => 'required|numeric',
            'units.*.wholesale' => 'required|numeric',
            'units.*.cost_price' => 'nullable|numeric',
        ]);

        // อัปเดตข้อมูลสินค้า
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // อัปเดตหน่วยนับ
        foreach ($request->units as $unitData) {
            // แปลงชื่อ unit_barcode ให้กลายเป็น barcode ที่ model ใช้
            $unitData['barcode'] = $unitData['unit_barcode'];
            unset($unitData['unit_barcode']);

            if (isset($unitData['id'])) {
                // อัปเดตหน่วยที่มีอยู่
                $unit = ProductUnit::findOrFail($unitData['id']);
                $unit->update($unitData);
            } else {
                // เพิ่มหน่วยใหม่
                ProductUnit::create(array_merge($unitData, ['product_id' => $product->id]));
            }
        }


        return redirect()->route('product.product.index')->with('success', 'อัปเดตข้อมูลสินค้าสำเร็จ');
    }

    // ลบสินค้า
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->productUnits()->delete();
        $product->delete();

        return redirect()->route('product.product.index')->with('success', 'ลบสินค้าสำเร็จ');
    }

    public function showAddStockForm()
    {
        $products = Product::where('is_active', true)->get();
        return view('products.add_stock', compact('products'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'location' => 'required|in:warehouse,store',
            'unit_quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $stock = ProductStocks::firstOrCreate(
            ['product_id' => $request->product_id],
            ['warehouse_stock' => 0, 'store_stock' => 0]
        );

        if ($request->location === 'warehouse') {
            $stock->increment('warehouse_stock', $request->quantity);
        } else {
            $stock->increment('store_stock', $request->quantity);
        }

        ProductStockMovement::create([
            'product_id' => $request->product_id,
            'type' => 'in',
            'unit_quantity' => 1, // ✅ ต้องใส่
            'quantity' => $request->quantity,
            'unit' => null,
            'note' => $request->note ?? "เพิ่มสินค้าเข้าทางแบบฟอร์ม",
        ]);

        return redirect()->route('product.products.add-stock-form')->with('success', 'เพิ่มสินค้าเข้าสต็อกเรียบร้อยแล้ว');
    }


    public function addStockMulti(Request $request)
    {
        if (!$request->has('items') || !is_array($request->items)) {
            return redirect()->back()->with('error', 'กรุณาแสกนสินค้าอย่างน้อย 1 รายการก่อนบันทึก');
        }

        foreach ($request->items as $productItems) {
            foreach ($productItems as $unitItems) {
                $item = $unitItems;

                // ที่เหลือใช้เหมือนเดิม
                $productStock = ProductStocks::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    ['warehouse_stock' => 0, 'store_stock' => 0]
                );

                if ($item['location'] === 'warehouse') {
                    $productStock->warehouse_stock += $item['quantity'];
                } elseif ($item['location'] === 'store') {
                    $productStock->store_stock += $item['quantity'];
                }

                $productStock->save();

                ProductStockMovement::create([
                    'product_id' => $item['product_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                    'unit_quantity' => $item['unit_quantity'],
                    'note' => $item['note'] ?? '',
                    'location' => $item['location'],
                ]);
            }
        }


        return redirect()->back()->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }

    public function indexstock(Request $request)
    {
        $categories = Category::all();
        $products = Product::query();

        // กรองตามประเภทถ้ามี
        if ($request->filled('category_id')) {
            $products->where('category_id', $request->category_id);
        }

        // โหลดข้อมูลความสัมพันธ์ที่จำเป็น เช่น stock, category, defaultUnit
        $products = $products->with(['category', 'stock', 'defaultUnit'])->latest()->get();

        // ไปที่ view เหมือน index หรือแยก view ก็ได้
        return view('products.show-stock', compact('products', 'categories'));
    }
}
