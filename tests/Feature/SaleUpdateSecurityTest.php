<?php

namespace Tests\Feature;

use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SaleUpdateSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_call_sale_update_endpoint(): void
    {
        [$sale] = $this->saleFixture();
        $member = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($member)->putJson(
            "/staff/sales/{$sale->id}",
            $this->updatePayload()
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSaleUnchanged($sale);
    }

    public function test_staff_cannot_update_another_staffs_sale(): void
    {
        [$sale] = $this->saleFixture();
        $otherStaff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($otherStaff)->putJson(
            "/staff/sales/{$sale->id}",
            $this->updatePayload()
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSaleUnchanged($sale);
    }

    public function test_quantity_change_cannot_modify_stock_or_sale_items(): void
    {
        [$sale, $item, , $stock, $staff] = $this->saleFixture();
        $payload = $this->updatePayload();
        $payload['items'][0]['quantity'] = 999;

        $response = $this->actingAs($staff)->putJson(
            "/staff/sales/{$sale->id}",
            $payload
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSame(2, (int) $item->fresh()->quantity);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_product_unit_change_is_rejected_without_mutation(): void
    {
        [$sale, $item, $unit, , $staff] = $this->saleFixture();
        [$otherUnit] = $this->productUnit('Other Product', 'box', 12, 7);
        $payload = $this->updatePayload();
        $payload['items'][0]['product_unit_id'] = $otherUnit->id;

        $response = $this->actingAs($staff)->putJson(
            "/staff/sales/{$sale->id}",
            $payload
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSame($unit->id, (int) $item->fresh()->product_unit_id);
        $this->assertDatabaseCount('sale_items', 1);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_client_total_and_prices_cannot_change_recorded_total(): void
    {
        [$sale, $item, , , $staff] = $this->saleFixture();
        $payload = $this->updatePayload();
        $payload['total_price'] = 0.01;
        $payload['items'][0]['price'] = 0.01;

        $response = $this->actingAs($staff)->putJson(
            "/staff/sales/{$sale->id}",
            $payload
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertEquals(20, (float) $sale->fresh()->total_price);
        $this->assertEquals(10, (float) $item->fresh()->price);
    }

    public function test_legacy_update_cannot_bypass_safe_cancellation_policy(): void
    {
        [$sale, , , $stock, $staff] = $this->saleFixture();
        $payload = $this->updatePayload();
        $payload['items'] = [];
        $payload['sale_type'] = 'cancelled';

        $response = $this->actingAs($staff)->putJson(
            "/staff/sales/{$sale->id}",
            $payload
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSaleUnchanged($sale);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_invalid_request_changes_no_financial_or_stock_data(): void
    {
        [$sale, , , $stock, $staff] = $this->saleFixture();

        $response = $this->actingAs($staff)->patchJson(
            "/staff/sales/{$sale->id}",
            [
                'sale_date' => 'not-a-date',
                'items' => 'not-an-array',
                'staff_id' => User::factory()->create(['role' => 'admin'])->id,
            ]
        );

        $this->assertRouteUnavailable($response->status());
        $this->assertSaleUnchanged($sale);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_edit_and_update_resource_actions_are_not_registered(): void
    {
        [$sale, , , , $staff] = $this->saleFixture();

        $this->assertFalse(Route::has('staff.sales.edit'));
        $this->assertFalse(Route::has('staff.sales.update'));

        $this->actingAs($staff)
            ->getJson("/staff/sales/{$sale->id}/edit")
            ->assertNotFound();

        $this->assertRouteUnavailable(
            $this->actingAs($staff)
                ->putJson("/staff/sales/{$sale->id}", $this->updatePayload())
                ->status()
        );
    }

    private function saleFixture(): array
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit(
            'Original Product',
            'piece',
            1,
            5
        );
        $sale = Sale::create([
            'staff_id' => $staff->id,
            'sale_date' => '2026-01-01 10:00:00',
            'sale_type' => 'retail',
            'total_price' => 20,
            'payment_method' => 'cash',
        ]);
        $item = SaleItem::create([
            'sale_id' => $sale->id,
            'product_unit_id' => $unit->id,
            'quantity' => 2,
            'unit_quantity' => 1,
            'price' => 10,
            'cost' => 4,
        ]);

        return [$sale, $item, $unit, $stock, $staff];
    }

    private function updatePayload(): array
    {
        return [
            'sale_date' => '2026-01-02 10:00:00',
            'sale_type' => 'wholesale',
            'total_price' => 999999,
            'items' => [
                [
                    'product_unit_id' => 1,
                    'quantity' => 2,
                    'price' => 10,
                ],
            ],
        ];
    }

    private function assertSaleUnchanged(Sale $sale): void
    {
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'staff_id' => $sale->staff_id,
            'sale_type' => 'retail',
            'total_price' => 20,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'quantity' => 2,
            'price' => 10,
        ]);
        $this->assertDatabaseCount('sale_items', 1);
        $this->assertSame(0, ProductStockMovement::count());
    }

    private function assertRouteUnavailable(int $status): void
    {
        $this->assertContains($status, [404, 405]);
    }

    private function productUnit(
        string $productName,
        string $unitName,
        int $unitQuantity,
        int $storeStock
    ): array {
        $productId = DB::table('products')->insertGetId([
            'name' => $productName,
            'is_active' => true,
            'is_online' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $unitId = DB::table('product_units')->insertGetId([
            'product_id' => $productId,
            'unit_name' => $unitName,
            'unit_quantity' => $unitQuantity,
            'price' => 10,
            'cost_price' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $stockId = DB::table('product_stocks')->insertGetId([
            'product_id' => $productId,
            'unit_id' => $unitId,
            'warehouse_stock' => 0,
            'store_stock' => $storeStock,
            'track_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            ProductUnit::findOrFail($unitId),
            ProductStocks::findOrFail($stockId),
        ];
    }
}
