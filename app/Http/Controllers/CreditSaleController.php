<?php

namespace App\Http\Controllers;

use App\Models\CreditSale;
use App\Models\CreditSaleItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductUnit;
use App\Models\ProductStocks;
use App\Models\ProductStockMovement;
use App\Models\CreditPaymentHistory;
use App\Models\Shift;

class CreditSaleController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['creditSales'])
            ->orderBy('name')
            ->get();

        return view(
            'credit_sales.index',
            compact('customers')
        );
    }

    public function creditSale(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            $customer = Customer::findOrFail($request->customer_id);

// เช็คว่าลูกค้าถูกระงับไหม
if ($customer->is_active == 0) {

    DB::rollBack();

    return response()->json([
        'success' => false,
        'message' => 'ลูกค้าถูกระงับ ไม่สามารถขายเงินเชื่อได้'
    ], 403);
}
$shift = Shift::where('user_id', auth()->id())
    ->where('status', 'open')
    ->first();

if (!$shift) {
    return response()->json([
        'success' => false,
        'message' => 'กรุณาเปิดกะก่อนขาย'
    ], 403);
}
            // =========================
            // สร้าง Sale หลัก
            // =========================
           $sale = Sale::create([
    'staff_id' => auth()->id(),
    'shift_id' => $shift->id,
    'sale_type' => 'credit',
    'payment_method' => 'credit',
    'sale_date' => now(),
    'total_price' => 0,
]);
            $total = 0;
            // =========================
            // credit sale
            // =========================
            $creditSale = CreditSale::create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
                'due_date' => $request->due_date,
                'status' => 'unpaid'
            ]);


            foreach ($request->products as $item) {

                $productId = $selectedUnit->product_id;

                $unitId = $item['product_unit_id']
                    ?? $item['unit_id'];

                $qty = $item['qty'];

                $price = $item['price'];

                $selectedUnit = ProductUnit::findOrFail($unitId);

                $unitQty = $selectedUnit->unit_quantity;

                $totalBaseQty = $qty * $unitQty;

                // =========================
                // หา unit เล็กสุด
                // =========================
                $baseUnit = ProductUnit::where('product_id', $productId)
                    ->orderBy('unit_quantity', 'asc')
                    ->first();

                if (!$baseUnit) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => "ไม่พบหน่วยเล็กสุดของสินค้า",
                    ], 400);
                }

                // =========================
                // ตัดจากหน่วยที่ขายก่อน
                // =========================
                $stock = ProductStocks::where('product_id', $productId)
                    ->where('unit_id', $unitId)
                    ->first();

                $availableQty = $stock?->store_stock ?? 0;

                $deductQty = min($qty, $availableQty);

                $remainingQty = $qty - $deductQty;

                // =========================
                // ถ้ามี stock หน่วยนั้น
                // =========================
                if ($deductQty > 0 && $stock) {

                    $stock->decrement('store_stock', $deductQty);

                    ProductStockMovement::create([
                        'product_id' => $productId,
                        'type' => 'out',
                        'quantity' => $deductQty,
                        'unit_quantity' => $unitQty,
                        'unit' => $selectedUnit->unit_name,
                        'location' => 'store',
                        'note' => 'ขายเงินเชื่อจากหน่วยที่เลือก',
                    ]);
                }


                // =========================
                // ถ้าหน่วยที่เลือกไม่พอ
                // =========================
                if ($remainingQty > 0) {

                    $remainingBaseQty = $remainingQty * $unitQty;

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

                        if ($basePerUnit == 0 || $available == 0) {
                            continue;
                        }

                        // จำนวนที่ต้องแตก
                        $neededUnit = ceil($remainingBaseQty / $basePerUnit);

                        $usableQty = min($available, $neededUnit);

                        if ($usableQty <= 0) {
                            continue;
                        }

                        $convertedBase = $usableQty * $basePerUnit;

                        // ตัด stock หน่วยใหญ่
                        $stockOther->decrement('store_stock', $usableQty);

                        // เพิ่ม stock หน่วยเล็ก
                        $stockBaseUnit = ProductStocks::firstOrCreate(
                            [
                                'product_id' => $productId,
                                'unit_id' => $baseUnit->id
                            ],
                            [
                                'store_stock' => 0,
                                'warehouse_stock' => 0
                            ]
                        );

                        $stockBaseUnit->increment(
                            'store_stock',
                            $convertedBase
                        );

                        ProductStockMovement::create([
                            'product_id' => $productId,
                            'type' => 'out',
                            'quantity' => $usableQty,
                            'unit_quantity' => $basePerUnit,
                            'unit' => $otherUnit->unit_name,
                            'location' => 'store',
                            'note' => "แตกหน่วยจาก {$otherUnit->unit_name}",
                        ]);

                        $remainingBaseQty = max(
                            0,
                            $remainingBaseQty - $convertedBase
                        );

                        if ($remainingBaseQty <= 0) {
                            break;
                        }
                    }

                    // =========================
                    // ถ้ายังไม่พอ
                    // =========================
                    if ($remainingBaseQty > 0) {

                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' =>
                            'สินค้า ' .
                                $selectedUnit->product->name .
                                ' สต็อกไม่พอ'
                        ], 400);
                    }

                    // =========================
                    // ตัดจากหน่วยเล็ก
                    // =========================
                    $deductFromBase =
                        $totalBaseQty - ($deductQty * $unitQty);

                    $finalBaseStock = ProductStocks::where(
                        'product_id',
                        $productId
                    )
                        ->where('unit_id', $baseUnit->id)
                        ->first();

                    if (
                        !$finalBaseStock ||
                        $finalBaseStock->store_stock < $deductFromBase
                    ) {

                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' =>
                            'สินค้า ' .
                                $selectedUnit->product->name .
                                ' สต็อกไม่พอ'
                        ], 400);
                    }

                    $finalBaseStock->decrement(
                        'store_stock',
                        $deductFromBase
                    );

                    ProductStockMovement::create([
                        'product_id' => $productId,
                        'type' => 'out',
                        'quantity' => $deductFromBase,
                        'unit_quantity' => $baseUnit->unit_quantity,
                        'unit' => $baseUnit->unit_name,
                        'location' => 'store',
                        'note' => 'ขายเงินเชื่อหลังแตกหน่วย',
                    ]);
                }

                // =========================
                // sale item
                // =========================
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_unit_id' => $unitId,
                    'quantity' => $qty,
                    'unit_quantity' => $unitQty,
                    'price' => $price,
                ]);

                CreditSaleItem::create([
                    'credit_sale_id' => $creditSale->id,
                    'product_id' => $productId,
                    'product_unit_id' => $unitId,
                    'qty' => $qty,
                    'price' => $price,
                ]);

                $total += ($qty * $price);
            }

            // =========================
            // update total
            // =========================
            $sale->update([
                'total_price' => $total
            ]);

            $creditSale->update([
                'total_amount' => $total,
                'remaining_amount' => $total,
            ]);

            // =========================
            // เพิ่มหนี้ลูกค้า
            // =========================
            $customer->increment(
                'current_debt',
                $total
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'บันทึกขายเงินเชื่อสำเร็จ'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $sale = CreditSale::with([
            'customer',
            'items.product',
            'items.unit'
        ])->findOrFail($id);

        return view(
            'credit_sales.show',
            compact('sale')
        );
    }

    public function paySelected(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $customer = Customer::findOrFail($id);

            $sales = CreditSale::whereIn(
                'id',
                $request->sales ?? []
            )->get();

            foreach ($sales as $sale) {
                $amount = $sale->remaining_amount;

                $sale->paid_amount += $amount;

                $sale->remaining_amount = 0;

                $sale->status = 'paid';

                $sale->save();

                CreditPaymentHistory::create([
                    'customer_id' => $customer->id,
                    'credit_sale_id' => $sale->id,
                    'amount' => $amount,
                    'user_id' => auth()->id(),
                    'note' => 'ชำระเต็มจำนวน'
                ]);

                $customer->current_debt -= $amount;
            }

            if ($customer->current_debt < 0) {
                $customer->current_debt = 0;
            }

            $customer->save();

            DB::commit();

            return back()->with(
                'success',
                'ชำระเงินสำเร็จ'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function customerBills($id)
    {
        $customer = Customer::with([
            'creditSales.items.product',
            'paymentHistories'
        ])->findOrFail($id);

        return view(
            'credit_sales.customer_bills',
            compact('customer')
        );
    }

    public function payAll(Request $request, $id)
    {
        $request->validate([
            'pay_amount' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();

        try {

            $customer = Customer::findOrFail($id);

            $money = $request->pay_amount;

            $sales = CreditSale::where(
                'customer_id',
                $customer->id
            )
                ->where('status', '!=', 'paid')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($sales as $sale) {

                if ($money <= 0) {
                    break;
                }

                $remaining = $sale->remaining_amount;

                // =========================
                // จ่ายเต็มบิล
                // =========================
                if ($money >= $remaining) {

                    $sale->paid_amount += $remaining;

                    $sale->remaining_amount = 0;

                    $sale->status = 'paid';

                    $sale->save();

                    // บันทึกประวัติการชำระ
                    CreditPaymentHistory::create([
                        'customer_id' => $customer->id,
                        'credit_sale_id' => $sale->id,
                        'amount' => $remaining,
                        'user_id' => auth()->id(),
                        'payment_method' => $request->payment_method,
                        'note' => 'ชำระบิลเต็มจำนวน'
                    ]);

                    $money -= $remaining;
                } else {

                    // =========================
                    // จ่ายบางส่วน
                    // =========================
                    $sale->paid_amount += $money;

                    $sale->remaining_amount -= $money;

                    $sale->save();

                    // บันทึกประวัติการชำระ
                    CreditPaymentHistory::create([
                        'customer_id' => $customer->id,
                        'credit_sale_id' => $sale->id,
                        'amount' => $money,
                        'payment_method' => $request->payment_method,
                        'user_id' => auth()->id(),
                        'note' => 'ชำระบางส่วน'
                    ]);

                    $money = 0;
                }
            }

            // =========================
            // ลดหนี้รวมลูกค้า
            // =========================
            $customer->current_debt -= $request->pay_amount;

            if ($customer->current_debt < 0) {
                $customer->current_debt = 0;
            }

            $customer->save();

            DB::commit();

            return back()->with(
                'success',
                'รับชำระรวมสำเร็จ'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function payBill(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $sale = CreditSale::findOrFail($id);

            $customer = Customer::findOrFail(
                $sale->customer_id
            );

            // =========================
            // เลือกประเภทการชำระ
            // =========================
            if ($request->pay_type == 'full') {

                $amount = $sale->remaining_amount;
            } else {

                $request->validate([
                    'amount' => 'required|numeric|min:1'
                ]);

                $amount = $request->amount;
            }

            // =========================
            // เช็คยอดเกิน
            // =========================
            if ($amount > $sale->remaining_amount) {

                return back()->with(
                    'error',
                    'จำนวนเงินเกินยอดค้าง'
                );
            }

            // =========================
            // อัปเดตบิล
            // =========================
            $sale->paid_amount += $amount;

            $sale->remaining_amount -= $amount;

            if ($sale->remaining_amount <= 0) {

                $sale->remaining_amount = 0;

                $sale->status = 'paid';
            }

            $sale->save();

            // =========================
            // ประวัติการชำระ
            // =========================
            CreditPaymentHistory::create([

    'customer_id' => $customer->id,

    'credit_sale_id' => $sale->id,

    'amount' => $amount,

    'payment_method' => $request->payment_method,

    'user_id' => auth()->id(),

    'note' => $request->pay_type == 'full'
        ? 'ชำระเต็มจำนวน'
        : 'ชำระบางส่วน'
]);

            // =========================
            // ลดหนี้รวมลูกค้า
            // =========================
            $customer->current_debt -= $amount;

            if ($customer->current_debt < 0) {

                $customer->current_debt = 0;
            }

            $customer->save();

            DB::commit();

            return back()->with(
                'success',
                'ชำระบิลสำเร็จ'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}
