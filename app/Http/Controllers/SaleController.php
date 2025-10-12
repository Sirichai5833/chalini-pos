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
    $request->validate([
        'products' => 'required|array',
        'products.*.product_unit_id' => 'required|exists:product_units,id',
        'products.*.id' => 'required|exists:products,id',
        'products.*.qty' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $total = 0;

        $sale = Sale::create([
            'user_id' => Auth::id(),
            'staff_id' => Auth::id(),
            'sale_type' => $request->price_type ?? 'retail',
            'total_price' => 0,
            'payment_method' => $request->payment_method ?? 'cash',
            'sale_date' => now(),
        ]);

        foreach ($request->products as $item) {
            $productId = $item['id'];
            $unitId = $item['product_unit_id'];
            $qty = $item['qty'];
            $price = $item['price'];

            $selectedUnit = ProductUnit::findOrFail($unitId);
            $unitQty = $selectedUnit->unit_quantity;
            $totalBaseQty = $qty * $unitQty;

            $baseUnit = ProductUnit::where('product_id', $productId)
                ->orderBy('unit_quantity', 'asc')
                ->first();

            if (!$baseUnit) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "ไม่พบหน่วยเล็กสุดของสินค้ารหัส {$productId}",
                ], 400);
            }

            // หักจากหน่วยที่เลือกก่อน
            $stock = ProductStocks::where('product_id', $productId)
                ->where('unit_id', $unitId)
                ->first();

            $availableQty = $stock?->store_stock ?? 0;
            $deductQty = min($qty, $availableQty);
            $remainingQty = $qty - $deductQty;

            if ($deductQty > 0 && $stock) {
                $stock->decrement('store_stock', $deductQty);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'out',
                    'quantity' => $deductQty,
                    'unit_quantity' => $unitQty,
                    'unit' => $selectedUnit->unit_name,
                    'location' => 'store',
                    'note' => 'ขายจากหน่วยที่เลือก',
                ]);
            }

            // หากไม่พอ → แปลงหน่วย
            if ($remainingQty > 0) {
                $remainingBaseQty = $remainingQty * $unitQty;
                $convertedBase = 0;

                $otherUnits = ProductUnit::where('product_id', $productId)
                    ->where('id', '!=', $unitId)
                    ->orderBy('unit_quantity', 'asc')
                    ->get();

              foreach ($otherUnits as $otherUnit) {
    $stockOther = ProductStocks::where('product_id', $productId)
        ->where('unit_id', $otherUnit->id)
        ->first();

    $available = $stockOther?->store_stock ?? 0;
    $basePerUnit = $otherUnit->unit_quantity;

    if ($basePerUnit === 0 || $available === 0) continue;

    // ❌ เดิม (อาจไม่กล้าแตก)
    // $usableUnit = floor($remainingBaseQty / $basePerUnit);
    // $usableQty = min($usableUnit, $available);

    // ✅ แก้เป็น:
    $neededUnit = ceil($remainingBaseQty / $basePerUnit);
    $usableQty = min($available, $neededUnit);
    $convertedBase = $usableQty * $basePerUnit;
    $remainingBaseQty -= $convertedBase;

    if ($usableQty > 0) {
        $stockOther->decrement('store_stock', $usableQty);

        $stockBaseUnit = ProductStocks::firstOrCreate(
            ['product_id' => $productId, 'unit_id' => $baseUnit->id],
            ['store_stock' => 0, 'warehouse_stock' => 0]
        );

        $stockBaseUnit->increment('store_stock', $convertedBase);

        ProductStockMovement::create([
            'product_id' => $productId,
            'type' => 'out',
            'quantity' => $usableQty,
            'unit_quantity' => $basePerUnit,
            'unit' => $otherUnit->unit_name,
            'location' => 'store',
            'note' => "แตกหน่วยจาก {$otherUnit->unit_name} เป็น {$baseUnit->unit_name}",
        ]);
    }

    if ($remainingBaseQty <= 0) break;
}

                if ($convertedBase < $remainingBaseQty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "สินค้ารหัส {$productId} มีสต็อกไม่พอ ",
                    ], 400);
                }

                $deductFromBase = $totalBaseQty - ($deductQty * $unitQty);

                $finalBaseStock = ProductStocks::where('product_id', $productId)
                    ->where('unit_id', $baseUnit->id)
                    ->first();

                if (!$finalBaseStock || $finalBaseStock->store_stock < $deductFromBase) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "สต็อกไม่พอที่จะขาย ",
                    ], 400);
                }

                $finalBaseStock->decrement('store_stock', $deductFromBase);

                ProductStockMovement::create([
                    'product_id' => $productId,
                    'type' => 'out',
                    'quantity' => $deductFromBase,
                    'unit_quantity' => $baseUnit->unit_quantity,
                    'unit' => $baseUnit->unit_name,
                    'location' => 'store',
                    'note' => 'ขายสินค้าหลังแปลงหน่วยจากหน่วยอื่น',
                ]);
            }

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_unit_id' => $unitId,
                'quantity' => $qty,
                'unit_quantity' => $unitQty,
                'price' => $price,
            ]);

            $total += $price * $qty;
        }

        $sale->update(['total_price' => $total]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'บันทึกการขายสำเร็จแล้ว',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
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
    foreach ($sale->items as $item) {
        $unit = ProductUnit::find($item->product_unit_id);
        $productId = $unit->product_id ?? $item->product->id ?? null;

        if (!$productId) continue;

        $stock = ProductStocks::where('product_id', $productId)
                              ->where('unit_id', $unit->id ?? $item->product_unit_id)
                              ->first();

        if ($stock) {
            $stock->increment('store_stock', $item->quantity * ($unit->unit_quantity ?? 1));
        }

        ProductStockMovement::create([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $item->quantity,
            'unit_quantity' => $unit->unit_quantity ?? 1,
            'unit' => $unit->unit_name ?? '-',
            'location' => 'store',
            'note' => 'คืน stock จากการยกเลิกการขาย',
        ]);
    }

    $sale->status = 'cancelled';
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

    foreach ($sale->items as $item) {
       $unit = ProductUnit::find($item->product_unit_id);
$productStock = ProductStocks::where('product_id', $unit->product_id)
                             ->where('unit_id', $unit->id)
                             ->first();
if ($productStock) {
    $productStock->increment('store_stock', $item->quantity * $unit->unit_quantity);
}


        ProductStockMovement::create([
            'product_id' => $unit->product_id, // ✅ แก้ตรงนี้
            'type' => 'in',
            'quantity' => $item->quantity,
            'unit_quantity' => $unit->unit_quantity,
            'unit' => $unit->unit_name ?? '-',
            'location' => 'store',
            'note' => 'คืน stock จากการลบการขาย',
        ]);
    }

    $sale->items()->delete();
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

    // ลดสต็อกหน่วยย่อย
    $baseUnit = ProductUnit::where('product_id', $product->id)
        ->orderBy('unit_quantity', 'asc')
        ->first();

    if ($unit->unit_quantity > $baseUnit->unit_quantity) {
        $baseStock = ProductStocks::where('product_id', $product->id)
            ->where('unit_id', $baseUnit->id)
            ->first();

        if ($baseStock) {
            $baseStock->decrement('store_stock', $item['qty'] * $unit->unit_quantity);
            ProductStockMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $item['qty'] * $unit->unit_quantity,
                'unit_quantity' => $baseUnit->unit_quantity,
                'unit' => $baseUnit->unit_name,
                'location' => 'store',
                'note' => 'ตัดสต็อกหน่วยย่อยจากการขาย',
            ]);
        }
    }
}
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'ชำระเงินสำเร็จ',
            'sale_id' => $sale->id,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        ], 500);
    }
}
}
