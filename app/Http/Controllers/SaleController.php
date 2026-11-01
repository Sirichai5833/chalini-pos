<?php

namespace App\Http\Controllers;

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
use Illuminate\Validation\ValidationException;
use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\Shift;
use App\Models\CreditSaleItem;
use App\Models\User;
use App\Services\StockDeductionService;
use Illuminate\Support\Str;




class SaleController extends Controller
{
    // แสดงหน้าขาย (POS)
    public function index()
    {
        $products = Product::with('productUnits')->paginate(50);

        $productData = [];
        foreach ($products as $product) {
            foreach ($product->productUnits as $unit) {
                $productData[] = [
                    'id' => $product->id,
                    'barcode' => $unit->barcode,
                    'name' => $product->name,
                        // รูปภาพสินค้า
    'image' => $product->images->first()
    ? asset('storage/' . $product->images->first()->image_path)
    : null,
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

        $customers = Customer::paginate(20);

        return view(
            'sale.sale',
            [
                'products' => $productData,
                'customers' => $customers
            ]
        );
    }

    // อัปเดตสต็อกหลังขาย
    public function updateStockAfterSale(
        Request $request,
        StockDeductionService $stockDeduction
    ) {
        $validated = $request->validate([
            'request_token' => 'required|uuid',
            'products' => 'required|array|min:1',
            'products.*.product_unit_id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_units,id',
            ],
            'products.*.qty' => 'required|integer|min:1',
            'price_type' => 'required|in:retail,wholesale',
            'payment_method' => 'required|in:cash,qr,card',
        ], [
            'request_token.required' =>
                'ไม่พบรหัสคำขอ กรุณารีเฟรชหน้าแล้วลองใหม่',
            'request_token.uuid' =>
                'รหัสคำขอไม่ถูกต้อง กรุณารีเฟรชหน้าแล้วลองใหม่',
            'products.required' =>
                'กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ',
            'products.min' =>
                'กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ',
            'products.*.product_unit_id.required' =>
                'ไม่พบหน่วยสินค้า กรุณาเลือกสินค้าใหม่',
            'products.*.product_unit_id.exists' =>
                'หน่วยสินค้านี้ไม่มีอยู่แล้ว กรุณารีเฟรชหน้า',
            'products.*.qty.required' =>
                'กรุณาระบุจำนวนสินค้า',
            'products.*.qty.integer' =>
                'จำนวนสินค้าต้องเป็นจำนวนเต็ม',
            'products.*.qty.min' =>
                'จำนวนสินค้าต้องไม่น้อยกว่า 1',
            'price_type.required' =>
                'กรุณาเลือกประเภทราคา',
            'payment_method.required' =>
                'กรุณาเลือกวิธีชำระเงิน',
        ]);

        $result = DB::transaction(function () use (
            $request,
            $validated,
            $stockDeduction
        ) {
            $actor = User::query()
                ->lockForUpdate()
                ->findOrFail($request->user()->id);

            abort_unless(
                in_array(
                    $actor->role,
                    ['admin', 'manager', 'owner', 'staff'],
                    true
                ),
                403
            );

            $requestReference = 'pos-request:' .
                $validated['request_token'];
            $existingSales = Sale::query()
                ->where('staff_id', $actor->id)
                ->where('slip', $requestReference)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($existingSales->count() > 1) {
                throw ValidationException::withMessages([
                    'request_token' =>
                        'พบคำขอขายซ้ำ กรุณารีเฟรชหน้าแล้วลองใหม่',
                ]);
            }

            if ($existingSales->isNotEmpty()) {
                return [
                    'sale' => $existingSales->first(),
                    'duplicate' => true,
                ];
            }

            $openShifts = Shift::query()
                ->where('user_id', $actor->id)
                ->where('status', 'open')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            abort_unless(
                $openShifts->isNotEmpty(),
                403,
                'กรุณาเปิดกะก่อนทำรายการ'
            );

            if ($openShifts->count() > 1) {
                throw ValidationException::withMessages([
                    'shift' => 'พบกะที่เปิดอยู่มากกว่า 1 กะ กรุณาติดต่อผู้ดูแลระบบ',
                ]);
            }

            $shift = $openShifts->first();
            $quantities = collect($validated['products'])
                ->mapWithKeys(fn (array $item) => [
                    (int) $item['product_unit_id'] => (int) $item['qty'],
                ]);
            $units = ProductUnit::query()
                ->with('product')
                ->whereIn(
                    'id',
                    $quantities->keys()
                )
                ->orderBy('product_id')
                ->orderBy('unit_quantity')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($units->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'products' => 'ไม่พบหน่วยสินค้าบางรายการ กรุณารีเฟรชหน้าแล้วเลือกสินค้าใหม่',
                ]);
            }

            $priceField = $validated['price_type'] === 'wholesale'
                ? 'wholesale'
                : 'price';
            $items = $units->map(function (
                ProductUnit $unit
            ) use ($quantities, $priceField) {
                if (!$unit->product || !$unit->product->is_active) {
                    throw ValidationException::withMessages([
                        'products' => 'สินค้าบางรายการถูกปิดการขายหรือไม่พร้อมจำหน่าย',
                    ]);
                }

                $price = $unit->{$priceField};

                if ($price === null || (float) $price < 0) {
                    throw ValidationException::withMessages([
                        'price_type' =>
                            'ไม่พบราคาขายของสินค้าบางรายการ',
                    ]);
                }

                $quantity = $quantities->get($unit->id);

                return [
                    'unit' => $unit,
                    'quantity' => $quantity,
                    'price' => (float) $price,
                    'subtotal' => round((float) $price * $quantity, 2),
                ];
            });

            // การตรวจและแตกหน่วยสต็อกทำใน StockDeductionService ภายใน transaction
            $total = round($items->sum('subtotal'), 2);

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'products' => 'ยอดขายรวมต้องมากกว่า 0 บาท',
                ]);
            }

            $sale = Sale::create([
                'staff_id' => $actor->id,
                'shift_id' => $shift->id,
                'sale_type' => $validated['price_type'],
                'total_price' => $total,
                'payment_method' => $validated['payment_method'],
                'sale_date' => now(),
                'slip' => $requestReference,
            ]);

            foreach ($items as $item) {
                $selectedUnit = $item['unit'];

                $stockDeduction->deduct(
                    $selectedUnit,
                    $item['quantity'],
                    'ขายหน้าร้าน'
                );

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_unit_id' => $selectedUnit->id,
                    'quantity' => $item['quantity'],
                    'unit_quantity' => $selectedUnit->unit_quantity,
                    'price' => $item['price'],
                    'cost' => (float) ($selectedUnit->cost_price ?? 0),
                ]);
            }

            return [
                'sale' => $sale,
                'duplicate' => false,
            ];
        });

        $sale = $result['sale']->load('items');

        return response()->json([
            'success' => true,
            'message' => 'บันทึกการขายสำเร็จแล้ว',
            'sale_id' => $sale->id,
            'duplicate' => $result['duplicate'],
            'total' => (float) $sale->total_price,
            'items' => $sale->items->map(fn (SaleItem $item) => [
                'product_unit_id' => $item->product_unit_id,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'subtotal' => round(
                    (float) $item->price * $item->quantity,
                    2
                ),
            ])->values(),
            'next_request_token' => (string) Str::uuid(),
        ]);
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

        $sales = $query->paginate(20);

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
            $unitIds = $sale->items->pluck('product_unit_id');

$units = ProductUnit::whereIn('id', $unitIds)
    ->get()
    ->keyBy('id');
            $unit = $units[$item->product_unit_id];
            $productId = $unit->product_id ?? $item->product->id ?? null;

            if (!$productId) continue;

            $stock = ProductStocks::where('product_id', $productId)
                ->where('unit_id', $unit->id ?? $item->product_unit_id)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->increment('store_stock', (int) $item->quantity);
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
        $products = Product::select('id', 'name')->get();

$units = ProductUnit::select(
    'id',
    'product_id',
    'unit_name'
)->get();


        return view('sale.edit', compact('sale', 'products', 'units'));
    }


    public function update(
        Request $request,
        $id,
        StockDeductionService $stockDeduction
    ) {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'sale_type' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_unit_id' => 'required|integer|exists:product_units,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use (
            $request,
            $validated,
            $id,
            $stockDeduction
        ) {
            $sale = Sale::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($id);

            $staffId = $request->staff_id ?? Auth::id();

            if (!$staffId) {
                throw ValidationException::withMessages([
                    'staff_id' => 'ไม่พบข้อมูลพนักงาน',
                ]);
            }

            /*
             * คืนสต็อกของรายการเดิมก่อน
             * store_stock ของแต่ละแถวคือจำนวนของ unit_id นั้นโดยตรง
             * จึงคืนด้วย quantity เท่านั้น ไม่คูณ unit_quantity
             */
            foreach ($sale->items as $oldItem) {
                $unit = ProductUnit::query()
                    ->whereKey($oldItem->product_unit_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $stock = ProductStocks::query()
                    ->where('product_id', $unit->product_id)
                    ->where('unit_id', $unit->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = ProductStocks::create([
                        'product_id' => $unit->product_id,
                        'unit_id' => $unit->id,
                        'warehouse_stock' => 0,
                        'store_stock' => 0,
                        'track_stock' => 1,
                    ]);
                }

                $stock->increment('store_stock', (int) $oldItem->quantity);

                ProductStockMovement::create([
                    'product_id' => $unit->product_id,
                    'type' => 'in',
                    'quantity' => (int) $oldItem->quantity,
                    'unit_quantity' => (int) ($unit->unit_quantity ?? 1),
                    'unit' => $unit->unit_name ?? '-',
                    'location' => 'store',
                    'note' => 'คืนสต็อกจากการแก้ไขรายการขาย #' . $sale->id,
                ]);
            }

            $sale->items()->delete();

            $total = 0;

            foreach ($validated['items'] as $itemData) {
                $unit = ProductUnit::query()
                    ->with('product')
                    ->lockForUpdate()
                    ->findOrFail($itemData['product_unit_id']);

                $quantity = (int) $itemData['quantity'];
                $price = (float) $itemData['price'];

                $stockDeduction->deduct(
                    $unit,
                    $quantity,
                    'แก้ไขรายการขาย #' . $sale->id
                );

                $sale->items()->create([
                    'product_unit_id' => $unit->id,
                    'unit_quantity' => (int) ($unit->unit_quantity ?? 1),
                    'quantity' => $quantity,
                    'price' => $price,
                    'cost' => (float) ($unit->cost_price ?? 0),
                ]);

                $total += $price * $quantity;
            }

            $sale->update([
                'sale_date' => $validated['sale_date'],
                'staff_id' => $staffId,
                'sale_type' => $validated['sale_type'] ?? $sale->sale_type,
                'total_price' => round($total, 2),
            ]);

            return $sale;
        });

        return redirect()
            ->route('staff.sales.show', $sale->id)
            ->with('success', 'อัปเดตรายการขายแล้ว');
    }





    // 1. เพิ่ม Request $request เพื่อรับค่าจากฟอร์ม
    // ยกเลิกรายการขายและคืนสต็อก
    public function destroy(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'cancel_reason' => [
                    'required',
                    'string',
                    'min:3',
                    'max:500',
                ],
            ],
            [
                'cancel_reason.required' =>
                    'กรุณาระบุเหตุผลในการยกเลิกรายการขาย',
                'cancel_reason.min' =>
                    'เหตุผลต้องมีอย่างน้อย 3 ตัวอักษร',
            ]
        );

        $actor = $request->user();

        abort_unless(
            $actor &&
            in_array(
                $actor->role,
                ['admin', 'manager', 'owner'],
                true
            ),
            403,
            'คุณไม่มีสิทธิ์ยกเลิกรายการขาย'
        );

        DB::transaction(function () use (
            $id,
            $actor,
            $validated
        ) {
            $sale = Sale::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($id);

            if (($sale->status ?? null) === 'cancelled') {
                throw ValidationException::withMessages([
                    'cancel_reason' =>
                        'รายการขายนี้ถูกยกเลิกไปแล้ว',
                ]);
            }

            $items = SaleItem::query()
                ->where('sale_id', $sale->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cancel_reason' =>
                        'ไม่พบรายการสินค้าในบิลนี้',
                ]);
            }

            $units = ProductUnit::query()
                ->whereIn(
                    'id',
                    $items->pluck('product_unit_id')->filter()->unique()
                )
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $unit = $units->get($item->product_unit_id);

                if (!$unit) {
                    throw ValidationException::withMessages([
                        'cancel_reason' =>
                            "ไม่พบหน่วยสินค้าของรายการ #{$item->id}",
                    ]);
                }

                /*
                 * ระบบนี้เก็บสต็อกแยกตาม unit_id
                 * จึงคืนจำนวนตาม quantity ที่ขายของหน่วยนั้นโดยตรง
                 */
                $stock = ProductStocks::query()
                    ->where('product_id', $unit->product_id)
                    ->where('unit_id', $unit->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = ProductStocks::create([
                        'product_id' => $unit->product_id,
                        'unit_id' => $unit->id,
                        'warehouse_stock' => 0,
                        'store_stock' => 0,
                        'track_stock' => 1,
                    ]);
                }

                $stock->increment(
                    'store_stock',
                    (int) $item->quantity
                );

                ProductStockMovement::create([
                    'product_id' => $unit->product_id,
                    'type' => 'in',
                    'quantity' => (int) $item->quantity,
                    'unit_quantity' =>
                        (int) ($item->unit_quantity
                            ?? $unit->unit_quantity
                            ?? 1),
                    'unit' => $unit->unit_name ?? '-',
                    'location' => 'store',
                    'note' =>
                        '[ยกเลิกการขาย] บิล #' .
                        $sale->id .
                        ' เหตุผล: ' .
                        $validated['cancel_reason'],
                    'user_id' => Auth::id(),
                ]);
            }

            /*
             * ไม่ลบ Sale และ SaleItem เพื่อเก็บประวัติ
             * ตาราง sales ต้องมีคอลัมน์ status
             */
            $sale->status = 'cancelled';

            // บันทึกข้อมูลเสริมเฉพาะเมื่อคอลัมน์มีอยู่จริง
            if (\Illuminate\Support\Facades\Schema::hasColumn(
                'sales',
                'cancel_reason'
            )) {
                $sale->cancel_reason =
                    $validated['cancel_reason'];
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn(
                'sales',
                'cancelled_by'
            )) {
                $sale->cancelled_by = $actor->id;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn(
                'sales',
                'cancelled_at'
            )) {
                $sale->cancelled_at = now();
            }

            $sale->save();
        });

        return redirect()
            ->route('staff.sales.history')
            ->with(
                'success',
                'ยกเลิกรายการขายและคืนสต็อกเรียบร้อยแล้ว'
            );
    }


    public function generateQRCode(Request $request)
    {
        $amount = $request->query('amount', 0);
        $bankAccount = DB::table('settings')->where('key', 'promptpay')->value('value');

        // โค้ดสำรอง (Fallback): เผื่อวันดีคืนดีในเบสไม่มีค่านั้น จะได้ไม่พัง คืนค่าเดิมไปก่อน
        if (!$bankAccount) {
            $bankAccount = '0843860015'; 
        }
        $qrData = "โอนเงินจำนวน {$amount} บาท เข้าบัญชี {$bankAccount}";

        // สร้าง QR code
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->encoding('UTF-8')
            ->generate($qrData);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }


    public function checkout(
        Request $request,
        StockDeductionService $stockDeduction
    ) {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|in:cash,qr,card,credit',
            'slip' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'products' => 'required|array|min:1',
            'products.*.product_unit_id' => 'required|integer|exists:product_units,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.price' => 'nullable|numeric|min:0',
        ]);

        $slipPath = $request->file('slip')->store('slips', 'public');

        $sale = DB::transaction(function () use (
            $request,
            $validated,
            $slipPath,
            $stockDeduction
        ) {
            $actor = $request->user();

            abort_unless($actor, 403);

            $shift = Shift::query()
                ->where('user_id', $actor->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            abort_unless($shift, 403, 'กรุณาเปิดกะก่อนขาย');

            $sale = Sale::create([
                'staff_id' => $actor->id,
                'shift_id' => $shift->id,
                'sale_date' => now(),
                'sale_type' => 'retail',
                'payment_method' => $validated['payment_method'],
                'slip' => $slipPath,
                'total_price' => 0,
            ]);

            $total = 0;

            foreach ($validated['products'] as $item) {
                $unit = ProductUnit::query()
                    ->with('product')
                    ->lockForUpdate()
                    ->findOrFail($item['product_unit_id']);

                if (!$unit->product || !$unit->product->is_active) {
                    throw ValidationException::withMessages([
                        'products' => 'สินค้าบางรายการถูกปิดการขายหรือไม่พร้อมจำหน่าย',
                    ]);
                }

                // ใช้ราคาจากฐานข้อมูล ป้องกันการแก้ราคาใน request
                $realPrice = (float) $unit->price;
                $quantity = (int) $item['qty'];
                $total += $realPrice * $quantity;

                $stockDeduction->deduct(
                    $unit,
                    $quantity,
                    'ขายสินค้าออนไลน์/Checkout'
                );

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_unit_id' => $unit->id,
                    'quantity' => $quantity,
                    'unit_quantity' => (int) ($unit->unit_quantity ?? 1),
                    'price' => $realPrice,
                    'cost' => (float) ($unit->cost_price ?? 0),
                ]);
            }

            $sale->update([
                'total_price' => round($total, 2),
            ]);

            return $sale;
        });

        return response()->json([
            'success' => true,
            'message' => 'ชำระเงินสำเร็จ',
            'sale_id' => $sale->id,
        ]);
    }



    public function creditSale(
        Request $request,
        StockDeductionService $stockDeduction
    ) {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'due_date' => 'required|date|after_or_equal:today',
            'products' => 'required|array|min:1',
            'products.*.product_unit_id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_units,id',
            ],
            'products.*.qty' => 'required|integer|min:1',
        ]);

        $result = DB::transaction(function () use (
            $request,
            $validated,
            $stockDeduction
        ) {
            $actor = $request->user();

            abort_unless(
                $actor &&
                    in_array(
                        $actor->role,
                        ['admin', 'manager', 'owner', 'staff'],
                        true
                    ),
                403
            );

            $shift = Shift::query()
                ->where('user_id', $actor->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            abort_unless($shift, 403, 'กรุณาเปิดกะก่อนขายเงินเชื่อ');

            $customer = Customer::query()
                ->whereKey($validated['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $customer->is_active,
                403,
                'ลูกค้าถูกระงับ ไม่สามารถขายเงินเชื่อได้'
            );

            $units = ProductUnit::query()
                ->with('product')
                ->whereIn(
                    'id',
                    collect($validated['products'])->pluck('product_unit_id')
                )
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = collect($validated['products'])->map(function (array $item) use ($units) {
                $unit = $units->get($item['product_unit_id']);
                $price = $unit?->price;

                if ($price === null || (float) $price < 0) {
                    throw ValidationException::withMessages([
                        'products' => 'ไม่พบราคาขายของสินค้าบางรายการ',
                    ]);
                }

                $quantity = (int) $item['qty'];
                $subtotal = round((float) $price * $quantity, 2);

                return [
                    'unit' => $unit,
                    'quantity' => $quantity,
                    'price' => (float) $price,
                    'subtotal' => $subtotal,
                ];
            });

            // การตรวจและแตกหน่วยสต็อกทำใน StockDeductionService ภายใน transaction
            $total = round($items->sum('subtotal'), 2);

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'products' => 'ยอดขายเงินเชื่อต้องมากกว่า 0 บาท',
                ]);
            }

            $sale = Sale::create([
                'staff_id' => $actor->id,
                'shift_id' => $shift->id,
                'sale_type' => 'credit',
                'payment_method' => 'credit',
                'sale_date' => now(),
                'total_price' => $total,
            ]);

            $creditSale = CreditSale::create([
                'customer_id' => $customer->id,
                'total_amount' => $total,
                'paid_amount' => 0,
                'remaining_amount' => $total,
                'due_date' => $validated['due_date'],
                'status' => 'unpaid',
            ]);

            foreach ($items as $item) {
                $unit = $item['unit'];

                $stockDeduction->deduct(
                    $unit,
                    $item['quantity'],
                    'ขายเงินเชื่อ'
                );

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_unit_id' => $unit->id,
                    'quantity' => $item['quantity'],
                    'unit_quantity' => $unit->unit_quantity,
                    'price' => $item['price'],
                    'cost' => $unit->cost_price,
                ]);

                CreditSaleItem::create([
                    'credit_sale_id' => $creditSale->id,
                    'product_id' => $unit->product_id,
                    'product_name' => $unit->product?->name,
                    'product_unit_id' => $unit->id,
                    'qty' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['subtotal'],
                ]);
            }

            $customer->current_debt = round(
                (float) $customer->current_debt + $total,
                2
            );
            $customer->save();

            return [
                'sale_id' => $sale->id,
                'credit_sale_id' => $creditSale->id,
                'total' => $total,
            ];
        });

        return response()->json([
            'success' => true,
            ...$result,
        ]);
    }
}