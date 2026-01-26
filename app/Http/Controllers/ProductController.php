<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use App\Models\ProductStockMovement;
use App\Models\ProductStockMovementsTable;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use App\Helpers\ImageUploader;

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
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'units' => 'required|array|min:1',
            'is_active' => 'required|boolean',
            'units.*.unit_name' => 'required|string|max:255',
            'units.*.unit_quantity' => 'required|integer|min:1',
            'units.*.unit_barcode' => 'nullable|string|max:255',
            'units.*.price' => 'required|numeric',
            'units.*.wholesale' => 'required|numeric',
            'units.*.cost_price' => 'nullable|numeric',
        ]);

        $product = Product::create($request->only(['name', 'category_id', 'barcode', 'sku', 'description', 'is_active']));

        // จัดการรูปภาพ
        // ✅ บันทึกรูปภาพหลายรูป
       if ($request->hasFile('images')) {
    foreach ($request->file('images') as $image) {

        // ✅ กัน null
        if (!$image) {
            continue;
        }

        $url = ImageUploader::upload($image, 'products');

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $url,
        ]);
    }
}




        foreach ($request->units as $unit) {
            // แปลงชื่อ unit_barcode ให้กลายเป็น barcode ที่ model ใช้
            $unit['barcode'] = $unit['unit_barcode'];
            unset($unit['unit_barcode']);

            // สร้าง ProductUnit
            $createdUnit = ProductUnit::create(array_merge($unit, ['product_id' => $product->id]));

            // ✅ สร้าง ProductStock สำหรับหน่วยนี้
            ProductStocks::firstOrCreate([
                'product_id' => $product->id,
                'unit_id' => $createdUnit->id,
            ], [
                'warehouse_stock' => 0,
                'store_stock' => 0,
                'track_stock' => 1, // ถ้ามีระบบติดตาม stock
            ]);
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
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'units' => 'required|array|min:1',
            'is_active' => 'required|boolean',
            'units.*.unit_name' => 'required|string|max:255',
            'units.*.unit_quantity' => 'required|integer|min:1',
            'units.*.unit_barcode' => 'required|string|max:255',
            'units.*.price' => 'required|numeric',
            'units.*.wholesale' => 'required|numeric',
            'units.*.cost_price' => 'nullable|numeric',
        ]);

        // จัดการรูปภาพ
        // จัดการรูปภาพหลายรูป
        // จัดการรูปภาพหลายรูปแบบอัตโนมัติ
        if ($request->hasFile('images')) {
            // ลบรูปเดิมทั้งหมด
            foreach ($product->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }

            // อัปโหลดรูปใหม่
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                ]);
            }
        }


        // อัปเดตข้อมูลสินค้า
        $product->update($validated);

        // ✅ ลบหน่วยนับที่ถูกลบจากฟอร์ม
        $unitIdsInForm = collect($request->units)->pluck('id')->filter()->all();
        $product->productUnits()->whereNotIn('id', $unitIdsInForm)->delete();

        // อัปเดตหรือเพิ่มหน่วยนับ
        foreach ($request->units as $unitData) {
            $unitData['barcode'] = $unitData['unit_barcode'];
            unset($unitData['unit_barcode']);

            if (isset($unitData['id'])) {
                $unit = ProductUnit::findOrFail($unitData['id']);
                $unit->update($unitData);
            } else {
                ProductUnit::create(array_merge($unitData, ['product_id' => $product->id]));
            }
        }

        return redirect()->route('product.product.index')->with('success', 'อัปเดตข้อมูลสินค้าสำเร็จ');
    }


    // ลบสินค้า
    public function destroy($id)
    {
        $product = Product::with('productUnits.stock')->findOrFail($id);

        // เช็กว่ายังมี stock ค้างอยู่ไหม
        foreach ($product->productUnits as $unit) {
            $stock = $unit->stock;
            if (($stock->warehouse_stock ?? 0) > 0 || ($stock->store_stock ?? 0) > 0) {
                return redirect()->back()->with('error', 'ไม่สามารถลบสินค้าได้ เพราะยังมีสินค้าคงเหลือในสต็อก');
            }
        }

        // ถ้ามีรูป ลบออกจาก storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // ลบหน่วย และตัวสินค้า
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
            'unit_id' => 'required|exists:units,id', // ✅ ต้อง validate ด้วย
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
        foreach ($productItems as $item) {

            $productId  = $item['product_id'];
            $unitId     = $item['unit_id'];
            $quantity   = (int) $item['quantity'];        // จำนวนที่รับ (เช่น 1 แพ็ค)
            $unitQty    = (int) $item['unit_quantity'];   // 1 แพ็ค = กี่ชิ้น
            $location   = $item['location'];
            $note       = $item['note'] ?? '';
            $isFree     = isset($item['is_free']) && $item['is_free'] == 1;
            $expiryDate = $item['expiry_date'] ?? null;

            // ✅ แปลงเป็น "ชิ้นจริง"
            $addQty = $quantity * $unitQty;

            // ✅ stock ต้องมีแถวเดียวต่อสินค้า
            $productStock = ProductStocks::firstOrCreate(
                ['product_id' => $productId],
                ['warehouse_stock' => 0, 'store_stock' => 0]
            );

            if ($location === 'store') {
                $productStock->store_stock += $addQty;
            } else {
                $productStock->warehouse_stock += $addQty;
            }

            $productStock->save();

            // ✅ บันทึก batch (เป็นชิ้น)
            ProductBatch::create([
                'product_id'       => $productId,
                'quantity'         => $addQty,
                'product_unit_id'  => $unitId,
                'batch_code'       => $productId . '-' . now()->format('YmdHis'),
                'expiry_date'      => $expiryDate,
            ]);

            // unit ใช้แค่ log
            $unit = ProductUnit::find($unitId);

            ProductStockMovement::create([
                'product_id'    => $productId,
                'type'          => 'in',
                'quantity'      => $quantity,
                'unit_quantity' => $unitQty,
                'unit'          => $unit?->unit_name ?? '-',
                'location'      => $location,
                'note'          => $note,
                'is_free'       => $isFree ? 1 : 0,
            ]);

            activity('product')
                ->causedBy(Auth::user())
                ->performedOn(Product::find($productId))
                ->withProperties([
                    'unit_id'        => $unitId,
                    'quantity'       => $quantity,
                    'unit_quantity'  => $unitQty,
                    'location'       => $location,
                ])
                ->event('stock_added')
                ->log('เพิ่มสต็อกสินค้า (คำนวณเป็นชิ้น)');
        }
    }

    return redirect()
        ->route('product.products.add-stock-form')
        ->with('success', 'เพิ่มสต็อกสินค้าเรียบร้อยแล้ว');
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

    public function show($id)
    {
        $product = Product::with('activities.causer')->findOrFail($id);
        return view('products.show-histroy', compact('product'));
    }
    public function allHistory(Request $request)
    {
        $query = Activity::where('log_name', 'product')->with(['subject', 'causer']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('event')) {
            // ถ้ามีการเลือกประเภท event
            $query->where('event', $request->event);
        } else {
            // ถ้าไม่เลือกประเภท → ตัด stock_added ออก
            $query->where('event', '!=', 'stock_added');
        }

        $activities = $query->latest()->paginate(20);

        return view('products.show-histroy', compact('activities'));
    }


    public function searchStockInHistory(Request $request)
    {
        $search = $request->input('search');
        $from = $request->input('from');
        $to = $request->input('to');
        $isPrint = $request->has('print');

        $query = ProductStockMovement::with('product')
            ->where('type', 'in')
            ->when($search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('barcode', 'like', "%$search%");
                });
            })
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('created_at', 'desc');

        $movements = $isPrint ? $query->get() : $query->paginate(20);

        return view('products.stock-in-history', compact('movements', 'search', 'from', 'to', 'isPrint'));
    }

    public function deleteImage($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'ไม่พบรูปภาพ'], 404);
        }

        // ลบไฟล์
        Storage::disk('public')->delete($image->image_path);

        // ลบ record
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function checkBarcode(Request $request)
    {
        $barcode = $request->get('barcode');

        $exists = \App\Models\ProductUnit::where('barcode', $barcode)->exists();

        return response()->json(['exists' => $exists]);
    }
}
