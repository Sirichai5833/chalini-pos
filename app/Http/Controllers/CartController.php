<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * อ่านค่าตั้งค่าการจัดส่งจากตาราง settings
     */
    private function getShippingSettings(): array
    {
        $settings = [
            'shipping_fee' => 0,
            'shipping_free_enabled' => false,
            'shipping_free_minimum' => 300,
        ];

        try {
            $rows = DB::table('settings')
                ->whereIn('key', [
                    'shipping_fee',
                    'shipping_free_enabled',
                    'shipping_free_minimum',
                ])
                ->pluck('value', 'key');

            $settings['shipping_fee'] = max(
                0,
                (float) ($rows['shipping_fee'] ?? $settings['shipping_fee'])
            );

            $settings['shipping_free_enabled'] = in_array(
                strtolower((string) ($rows['shipping_free_enabled'] ?? '0')),
                ['1', 'true', 'yes', 'on'],
                true
            );

            $settings['shipping_free_minimum'] = max(
                0,
                (float) ($rows['shipping_free_minimum'] ?? $settings['shipping_free_minimum'])
            );
        } catch (\Throwable $e) {
            // หากอ่านค่าตั้งค่าไม่ได้ ให้ใช้ค่าเริ่มต้น
            report($e);
        }

        return $settings;
    }

    /**
     * คำนวณค่าจัดส่งและยอดสุทธิ
     */
    private function calculateShipping(float $subtotal): array
    {
        $settings = $this->getShippingSettings();

        $regularShippingFee = $settings['shipping_fee'];
        $freeShippingEnabled = $settings['shipping_free_enabled'];
        $freeShippingMinimum = $settings['shipping_free_minimum'];

        $isFreeShipping = $freeShippingEnabled
            && $subtotal >= $freeShippingMinimum;

        $shippingFee = $isFreeShipping ? 0 : $regularShippingFee;

        $remainingForFreeShipping = (
            $freeShippingEnabled && !$isFreeShipping
        )
            ? max(0, $freeShippingMinimum - $subtotal)
            : 0;

        return [
            'shippingFee' => $regularShippingFee,
            'actualShippingFee' => $shippingFee,
            'freeShippingEnabled' => $freeShippingEnabled,
            'freeShippingMinimum' => $freeShippingMinimum,
            'isFreeShipping' => $isFreeShipping,
            'remainingForFreeShipping' => $remainingForFreeShipping,
            'grandTotal' => round($subtotal + $shippingFee, 2),
        ];
    }

    /**
     * คำนวณยอดรวมสินค้าในตะกร้า
     */
    private function calculateCartTotal(array $cart): array
    {
        $total = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            $total += $price * $quantity;
            $totalItems += $quantity;
        }

        return [
            'total' => round($total, 2),
            'totalItems' => $totalItems,
        ];
    }

    /**
     * แสดงหน้าตะกร้าสินค้า
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $outOfStockItems = [];

        foreach ($cart as $id => &$item) {
            $product = Product::find($id);

            if ($product) {
                $item['stock'] = $product->stock;

                if ((int) $item['quantity'] > (int) $product->stock) {
                    $outOfStockItems[] = [
                        'name' => $item['name'] ?? $product->name,
                        'requested' => (int) $item['quantity'],
                        'available' => (int) $product->stock,
                    ];
                }
            } else {
                $outOfStockItems[] = [
                    'name' => $item['name'] ?? 'สินค้าไม่พบ',
                    'requested' => (int) ($item['quantity'] ?? 0),
                    'available' => 0,
                ];
            }
        }

        unset($item);

        session()->put('cart', $cart);

        $cartSummary = $this->calculateCartTotal($cart);
        $total = $cartSummary['total'];

        $shipping = $this->calculateShipping($total);

        return view('online.cart', array_merge(
            [
                'cart' => $cart,
                'total' => $total,
                'totalItems' => $cartSummary['totalItems'],
                'outOfStockItems' => $outOfStockItems,
            ],
            $shipping
        ));
    }

    /**
     * อัปเดตจำนวนสินค้า (AJAX PUT)
     */
    public function updateQuantity(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบสินค้านี้ในตะกร้า',
            ], 404);
        }

        $product = Product::find($id);
        $requestedQty = (int) $validated['quantity'];

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบสินค้าในระบบ',
            ], 404);
        }

        if ($requestedQty > (int) $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "สินค้าคงเหลือเพียง {$product->stock} ชิ้น ไม่เพียงพอ",
            ], 400);
        }

        $cart[$id]['quantity'] = $requestedQty;
        $cart[$id]['stock'] = $product->stock;

        session()->put('cart', $cart);

        $cartSummary = $this->calculateCartTotal($cart);
        $shipping = $this->calculateShipping($cartSummary['total']);

        $itemTotal = (float) $cart[$id]['price'] * $requestedQty;

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตจำนวนสินค้าเรียบร้อยแล้ว',
            'quantity' => $requestedQty,
            'item_total' => number_format($itemTotal, 2, '.', ''),
            'total' => number_format($cartSummary['total'], 2, '.', ''),
            'total_items' => $cartSummary['totalItems'],

            // ข้อมูลค่าจัดส่ง
            'shipping_fee' => number_format($shipping['actualShippingFee'], 2, '.', ''),
            'regular_shipping_fee' => number_format($shipping['shippingFee'], 2, '.', ''),
            'is_free_shipping' => $shipping['isFreeShipping'],
            'remaining_for_free_shipping' => number_format(
                $shipping['remainingForFreeShipping'],
                2,
                '.',
                ''
            ),
            'grand_total' => number_format($shipping['grandTotal'], 2, '.', ''),
        ]);
    }

    /**
     * ลบสินค้าออกจากตะกร้า (AJAX DELETE)
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบสินค้านี้ในตะกร้า',
            ], 404);
        }

        unset($cart[$id]);

        session()->put('cart', $cart);

        $cartSummary = $this->calculateCartTotal($cart);
        $shipping = $this->calculateShipping($cartSummary['total']);

        return response()->json([
            'success' => true,
            'message' => 'ลบสินค้าเรียบร้อยแล้ว',
            'total' => number_format($cartSummary['total'], 2, '.', ''),
            'total_items' => $cartSummary['totalItems'],

            // ข้อมูลค่าจัดส่ง
            'shipping_fee' => number_format($shipping['actualShippingFee'], 2, '.', ''),
            'regular_shipping_fee' => number_format($shipping['shippingFee'], 2, '.', ''),
            'is_free_shipping' => $shipping['isFreeShipping'],
            'remaining_for_free_shipping' => number_format(
                $shipping['remainingForFreeShipping'],
                2,
                '.',
                ''
            ),
            'grand_total' => number_format($shipping['grandTotal'], 2, '.', ''),
        ]);
    }
}
