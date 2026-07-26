<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ProductListAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_role_can_render_product_index(): void
    {
        $this->actingAs($this->user('staff'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->assertSee('data-product-list', false);
    }

    public function test_member_is_still_forbidden_from_product_index(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('product.product.index'))
            ->assertForbidden();
    }

    public function test_product_form_and_route_contracts_are_unchanged(): void
    {
        $product = Product::create(['name' => 'Contract Product']);

        $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->assertSee('action="' . route('product.product.storeWithUnit') . '"', false)
            ->assertSee('action="' . route('categories.store') . '"', false)
            ->assertSee('href="' . route('product.product.edit', $product) . '"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="units[0][unit_name]"', false)
            ->assertSee('name="units[0][unit_barcode]"', false);
    }

    public function test_required_modal_and_control_ids_are_preserved(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->assertSee('id="filterForm"', false)
            ->assertSee('id="searchProduct"', false)
            ->assertSee('id="addProductModal"', false)
            ->assertSee('id="addProductModalLabel"', false)
            ->assertSee('id="addCategoryModal"', false)
            ->assertSee('id="addCategoryModalLabel"', false)
            ->assertSee('id="units-container"', false)
            ->assertSee('id="add-unit"', false);
    }

    public function test_barcode_and_search_data_attributes_are_preserved(): void
    {
        $product = Product::create(['name' => 'Searchable Product']);

        $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->assertSee('data-name="searchable product"', false)
            ->assertSee('data-id="' . $product->id . '"', false)
            ->assertSee('data-barcode-check-url="' . route('barcode.check') . '"', false)
            ->assertSee('data-product-name-check-url="' . route('product.name.check') . '"', false)
            ->assertSee('data-product-create-form', false)
            ->assertSee('class="barcode-preview', false)
            ->assertSee('class="barcode-canvas"', false);
    }

    public function test_jsbarcode_dependency_is_loaded_once_per_page(): void
    {
        $productResponse = $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk();

        $this->assertSame(
            1,
            substr_count($productResponse->getContent(), 'JsBarcode.all.min.js')
        );

        $barcodeResponse = $this->get(route('product.barcodes.index'))
            ->assertOk();

        $this->assertSame(
            1,
            substr_count($barcodeResponse->getContent(), 'JsBarcode.all.min.js')
        );
    }

    public function test_shared_flash_and_existing_validation_rendering_still_work(): void
    {
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag([
            'name' => ['Product name is required'],
        ]));

        $response = $this->actingAs($this->user('admin'))
            ->withSession([
                'success' => 'Product list flash message',
                'errors' => $errors,
            ])
            ->get(route('product.product.index'));

        $response->assertOk()
            ->assertSee('Product list flash message')
            ->assertSee('Product name is required')
            ->assertSee('data-show-category-modal="true"', false)
            ->assertSee('is-invalid', false);
    }

    public function test_page_assets_are_rendered_through_shared_stacks(): void
    {
        $content = $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->getContent();

        $stylePosition = strpos($content, '.btn-edit-custom');
        $headEndPosition = strpos($content, '</head>');
        $scriptPosition = strpos($content, 'initializeProductList');
        $bodyEndPosition = strpos($content, '</body>');

        $this->assertNotFalse($stylePosition);
        $this->assertNotFalse($headEndPosition);
        $this->assertNotFalse($scriptPosition);
        $this->assertNotFalse($bodyEndPosition);
        $this->assertLessThan($headEndPosition, $stylePosition);
        $this->assertLessThan($bodyEndPosition, $scriptPosition);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }
}
