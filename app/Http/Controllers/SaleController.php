<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; // ต้องเพิ่มบรรทัดนี้
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    // แสดงหน้าขาย (POS)
    public function index()
    {
        $products = Product::with('productUnits')->get();

        $productData = [];
        foreach ($products as $product) {
            foreach ($product->productUnits as $unit) {
                $productData[] = [
                    'id' => $product->id,
                    'barcode' => $unit->barcode,
                    'name' => $product->name,
                    'unit' => $unit->unit_name,
                    'product_unit_id' => $unit->id,
                    'unit_id' => $unit->id,
                    'retail_price' => $unit->price, // ใช้ price เดิมเป็นราคาปลีก
                    'wholesale_price' => $unit->wholesale, // หรือจะใช้ logic แยกราคาได้ตามจริง
                    'freebie' => $product->description,
                    'cost_price' => $unit->unit_quantity,
                    'is_active' => $product->is_active, // ✅ เพิ่มตรงนี้
                ];
            } 
        }

        return view('sale.sale', ['products' => $productData]);
    }

    // อัปเดตสต็อกหลังขาย
    public function updateStockAfterSale(Request $request)
{
     

    // ตรวจดูว่า payload ถูกมั้ย
    $request->validate([
        'products' => 'required|array',
        'products.*.product_unit_id' => 'required|exists:product_units,id', // ✅
        'products.*.id' => 'required|exists:products,id',
        'products.*.qty' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        Log::info('🔍 Request Payload:', $request->all());
        $total = 0;
        
     foreach ($request->products as $item) {
    $product = Product::find($item['id']);
    $priceType = $item['price_type'];

    if (!$product || !$product->is_active) {
        return response()->json(['success' => false, 'message' => 'สินค้าถูกปิดการขาย: ' . $item['id']], 403);
    }

    $stock = ProductStocks::where('product_id', $item['id'])->first();

    // หาขนาดของหน่วยสินค้านี้ เช่น 1 แพ็ค มีกี่ชิ้น
    $productUnit = ProductUnit::find($item['product_unit_id']);
    $unitQuantity = $productUnit ? $productUnit->unit_quantity : 1;

    // คำนวณจำนวนชิ้นที่ต้องหัก
    $qtyToReduce = $item['qty'] * $unitQuantity;

    if (!$stock || $stock->store_stock < $qtyToReduce) {
        return response()->json(['success' => false, 'message' => 'สต็อกไม่พอ: ' . $item['id']], 400);
    }

    $total += $product->price * $item['qty'];

    // ลดสต็อกตามจำนวนชิ้นจริง
    $stock->store_stock -= $qtyToReduce;
    $stock->save();
}


        // เพิ่มบันทึกการขาย
        $sale = Sale::create([
           'user_id' => Auth::id(),         // อันนี้คือผู้ใช้ระบบ (ถ้าใช้ Laravel Auth)
    'staff_id' => Auth::id(),    
     'sale_type' => $priceType ,    // ✅ เพิ่มตรงนี้ ถ้าใช้ Auth::id() เป็น staff
    'total' => $total,
     'total_price' => $request->total_price, // ✅ ใช้ค่าที่ส่งมา
    'payment_method' => $request->payment_method ?? 'cash',
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['id']);

            SaleItem::create([
                'sale_id' => $sale->id,
               'product_unit_id' => $item['product_unit_id'], // ✅ ถ้าตารางใช้ชื่อว่า product_unit_id
                'quantity' => $item['qty'],
                'unit_quantity' => $item['product_unit_id'],
                'price' => $item['price'],
            ]);
        }

        DB::commit();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Stock update failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function history(Request $request)
{
    $query = Sale::with(['items.product', 'items.unit', 'staff'])->orderBy('sale_date', 'desc');

    // 🔐 ถ้าไม่ใช่ admin ให้ดูเฉพาะของตัวเอง
    if (!Auth::user()->is_admin) {
        $query->where('staff_id', Auth::id());
    } else {
        // ✅ filter เฉพาะ admin
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
    }

    // ✅ Filter ช่วงวันที่
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('sale_date', [$request->from_date, $request->to_date]);
    }

    // ✅ Filter ประเภทการขาย
    if ($request->filled('sale_type')) {
        $query->where('sale_type', $request->sale_type);
    }

    $sales = $query->get();

    // ถ้า admin ให้ส่งรายชื่อ staff ทั้งหมด
    $staffs = Auth::user()->is_admin
        ? \App\Models\User::where('role', 'staff')->get()
        : collect(); // ส่งค่าว่างถ้าไม่ใช่ admin

    return view('sale.history', compact('sales', 'staffs'));
}

public function show($id)
{
    $sale = Sale::with(['items.product', 'items.unit', 'staff'])->findOrFail($id);

    // 🔒 ถ้าเป็น staff และไม่ใช่เจ้าของรายการ ห้ามดู
    if (!Auth::user()->is_admin && $sale->staff_id !== Auth::id()) {
        abort(403, 'คุณไม่มีสิทธิ์ดูรายการนี้');
    }

    return view('sale.show', compact('sale'));
}

public function cancel(Sale $sale)
{
    // ย้อน stock กลับ
    foreach ($sale->items as $item) {
        $product = $item->product;
        if ($product) {
            $product->stock += $item->quantity;
            $product->save();
        }
    }

    // อัปเดตสถานะการขาย (ถ้ามีคอลัมน์ เช่น 'status')
    $sale->status = 'cancelled'; // หรือลบรายการเลยก็ได้: $sale->delete();
    $sale->save();

    return redirect()->route('sales.history')->with('success', 'ยกเลิกการขายเรียบร้อยแล้ว');
}

public function edit($id)
{
    $sale = Sale::with('items')->findOrFail($id);
    $products = Product::all();
    $units = ProductUnit::all();


    return view('sale.edit', compact('sale', 'products', 'units'));
}


public function update(Request $request, $id)
{
    $sale = Sale::with('items')->findOrFail($id);

    DB::transaction(function () use ($request, $sale) {
        $staffId = $request->staff_id ?? auth::id();
        if (!$staffId) {
            throw new \Exception('ไม่พบข้อมูล staff_id');
        }

        // อัปเดตข้อมูลหลัก
        $sale->update([
            'sale_date' => $request->sale_date,
            'staff_id' => $staffId,
            'sale_type' => $request->sale_type,
        ]);

        // คืน stock และลบรายการเดิม
        foreach ($sale->items as $item) {
            // คืน stock ถ้ามี
            $productStock = ProductStocks::where('product_id', $item->product_unit_id)->first();
            if ($productStock) {
                $productStock->increment('store_stock', $item->quantity);
            }

            // ลบ item
            $item->delete();
        }

        // เพิ่มรายการใหม่
        foreach ($request->items as $itemData) {
            $sale->items()->create([
                'product_unit_id' => $itemData['product_unit_id'],
                'unit_quantity' => $itemData['quantity'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],

            ]);

            // ลด stock ถ้ามี
            // $productStock = ProductStocks::where('product_id', $itemData['product_id'])->first();
            // if ($productStock) {
            //     $productStock->decrement('store_stock', $itemData['quantity']);
            // }
        }

        // คำนวณยอดรวมใหม่จาก request
        $total = collect($request->items)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $sale->update(['total_price' => $total]);
    });

    return redirect()->route('staff.sales.show', $sale->id)->with('success', 'อัปเดตรายการขายแล้ว');
}



public function destroy($id)
{
    $sale = Sale::with('items')->findOrFail($id);

    // คืน stock ก่อนลบ
    foreach ($sale->items as $item) {
        $stock = ProductStocks::where('product_id', $item->product_unit_id)->first();
        if ($stock) {
            $stock->increment('store_stock', $item->quantity);
        }

        ProductStockMovement::create([
            'product_id' => $item->product_unit_id,
            'type' => 'in',
            'quantity' => $item->quantity,
            'unit_quantity' => $item->unit_quantity,
            'unit' => $item->unit->unit_name ?? '-',
            'location' => 'store',
            'note' => 'คืน stock จากการลบการขาย',
        ]);
    }

    // ลบรายการย่อยก่อน
    $sale->items()->delete();

    // ลบรายการขายหลัก
    $sale->delete();

    return redirect()->route('staff.sales.history')->with('success', 'ลบรายการขายสำเร็จแล้ว');
}





public function generateQRCode(Request $request)
{
    $amount = $request->query('amount', 0);
    $bankAccount = '0843860015'; // เลขบัญชี
    $qrData = "โอนเงินจำนวน {$amount} บาท เข้าบัญชี {$bankAccount}";

    // สร้าง QR code
    $qrCode = QrCode::format('svg')
                    ->size(300)
                    ->encoding('UTF-8')
                    ->generate($qrData);

    return response($qrCode)
        ->header('Content-Type', 'image/svg+xml');
}


   public function checkout(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'phone' => 'required|string',
        'payment_method' => 'required|string',
        'slip' => 'required|image|max:2048',
        'products' => 'required|array',
        'products.*.product_unit_id' => 'required|exists:product_units,id',
        'products.*.qty' => 'required|integer|min:1',
        'products.*.price' => 'required|numeric|min:0',
        
    ]);

    DB::beginTransaction();

    try {
        // อัปโหลดสลิป
        $slipPath = $request->file('slip')->store('slips', 'public');
        // สร้าง Sale record
        $sale = Sale::create([
            'staff_id' => Auth::id(),
            'sale_date' => now(),
            'sale_type' => $request->payment_method,
            'slip' => $slipPath,
             'total_price' => $request->total_price, // ✅ ใช้ค่าที่ส่งมา
        ]);

        foreach ($request->products as $item) {
    $unit = ProductUnit::findOrFail($item['product_unit_id']);
    $product = $unit->product;

    SaleItem::create([
        'sale_id' => $sale->id,
        'product_unit_id' => $unit->id,
        'quantity' => $item['qty'],
        'unit_quantity' => $unit->unit_quantity,
        'price' => $item['price']
    ]);

    // ตัดสต็อก
    if ($product->stock && $product->stock->track_stock) {
        $product->stock->decrement('store_stock', $item['qty'] * $unit->unit_quantity);

        ProductStockMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $item['qty'],
            'unit_quantity' => $unit->unit_quantity,
            'unit' => $unit->unit_name,
            'location' => 'store',
            'note' => 'ขายสินค้า',
        ]);
    }
}
        DB::commit();
        return redirect()->route('sale.history')->with('success', 'บันทึกการขายเรียบร้อย');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Checkout failed: ' . $e->getMessage());
        return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
 

}
