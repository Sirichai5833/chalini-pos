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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
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

        $products = $products
            ->with(['category', 'images', 'productUnits.stock', 'defaultUnit'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products', 'name'),
                ],
                'category_id' => ['nullable', 'exists:categories,id'],
                'barcode' => ['nullable', 'string', 'max:255'],
                'sku' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'images' => ['nullable', 'array'],
                'images.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
                'units' => ['required', 'array', 'min:1'],
                'is_active' => ['required', 'boolean'],
                'min_stock' => ['required', 'integer', 'min:0'],
                'units.*.unit_name' => [
                    'required',
                    'string',
                    'max:100',
                ],
                'units.*.unit_quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],
                'units.*.unit_barcode' => [
                    'required',
                    'string',
                    'max:32',
                    'regex:/^[A-Za-z0-9\-_.]+$/',
                    'distinct',
                    Rule::unique('product_units', 'barcode'),
                ],
                'units.*.price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
                'units.*.wholesale' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
                'units.*.cost_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],
            ],
            [
                'name.unique' =>
                    'ชื่อสินค้านี้มีอยู่ในระบบแล้ว',
                'units.required' =>
                    'กรุณาเพิ่มหน่วยนับอย่างน้อย 1 รายการ',
                'units.min' =>
                    'กรุณาเพิ่มหน่วยนับอย่างน้อย 1 รายการ',
                'units.*.unit_name.required' =>
                    'กรุณากรอกชื่อหน่วยนับ',
                'units.*.unit_name.max' =>
                    'ชื่อหน่วยนับต้องไม่เกิน 100 ตัวอักษร',
                'units.*.unit_barcode.distinct' =>
                    'มีบาร์โค้ดซ้ำกันภายในฟอร์ม',
                'units.*.unit_barcode.unique' =>
                    'บาร์โค้ดนี้ถูกใช้กับสินค้าอื่นแล้ว',
                'units.*.unit_barcode.regex' =>
                    'บาร์โค้ดใช้ได้เฉพาะตัวอักษร ตัวเลข และเครื่องหมาย - _ .',
            ]
        );

        $validated['units'] = $this->normalizeUnits(
            $validated['units']
        );

        $this->validateDuplicateUnitNames(
            $validated['units']
        );

        $this->validateDuplicateBarcodes(
            $validated['units']
        );

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create([
                'name' => trim($validated['name']),
                'category_id' =>
                    $validated['category_id'] ?? null,
                'barcode' =>
                    isset($validated['barcode'])
                        ? trim($validated['barcode'])
                        : null,
                'sku' =>
                    isset($validated['sku'])
                        ? trim($validated['sku'])
                        : null,
                'description' =>
                    $validated['description'] ?? null,
                'is_active' =>
                    (bool) $validated['is_active'],
                'min_stock' =>
                    (int) $validated['min_stock'],
            ]);

            foreach ($request->file('images', []) as $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'image_path' => $path,
                ]);
            }

            foreach ($validated['units'] as $unitData) {
                $createdUnit = $product->productUnits()->create([
                    'unit_name' =>
                        $unitData['unit_name'],
                    'unit_quantity' =>
                        (int) $unitData['unit_quantity'],
                    'barcode' =>
                        $unitData['unit_barcode'],
                    'price' =>
                        (float) $unitData['price'],
                    'wholesale' =>
                        (float) $unitData['wholesale'],
                    'cost_price' =>
                        isset($unitData['cost_price'])
                        && $unitData['cost_price'] !== ''
                            ? (float) $unitData['cost_price']
                            : null,
                ]);

                ProductStocks::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'unit_id' => $createdUnit->id,
                    ],
                    [
                        'warehouse_stock' => 0,
                        'store_stock' => 0,
                        'track_stock' => 1,
                    ]
                );
            }
        });

        return redirect()
            ->route('product.product.index')
            ->with(
                'success',
                'เพิ่มสินค้าและหน่วยนับสำเร็จ'
            );
    }

    // แสดงฟอร์มแก้ไข
    // อัปเดตเมธอด edit ใน ProductController.php
public function edit($id)
{
    $product = Product::with('productUnits')->findOrFail($id);
    $categories = Category::all();

    // 1. ดึงประวัติการรับเข้า-ออกของสต็อกเฉพาะสินค้าชิ้นนี้
    $stockMovements = ProductStockMovement::where('product_id', $id)
        ->latest()
        ->take(10) // ดึง 10 รายการล่าสุด
        ->get();

    // 2. ดึงประวัติการทำรายการของระบบ (Spatie Activity Log)
    $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', Product::class)
        ->where('subject_id', $id)
        ->with('causer')
        ->latest()
        ->take(10)
        ->get();

    return view('products.edit', compact('product', 'categories', 'stockMovements', 'activities'));
}

    // อัปเดตข้อมูลสินค้าพร้อมหน่วยนับ
  public function updateWithUnit(Request $request, $id)
{
    $product = Product::with(['images', 'productUnits'])
        ->findOrFail($id);

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('products', 'name')
                ->ignore($product->id),
        ],

        'category_id' => [
            'nullable',
            'exists:categories,id',
        ],

        'barcode' => [
            'nullable',
            'string',
            'max:255',
        ],

        'sku' => [
            'nullable',
            'string',
            'max:255',
        ],

        'description' => [
            'nullable',
            'string',
        ],

        'images' => [
            'nullable',
            'array',
        ],

        'images.*' => [
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],

        'units' => [
            'required',
            'array',
            'min:1',
        ],

        'units.*.id' => [
            'nullable',
            'integer',
        ],

        'units.*.unit_name' => [
            'required',
            'string',
            'max:255',
        ],

        'units.*.unit_quantity' => [
            'required',
            'integer',
            'min:1',
        ],

        'units.*.unit_barcode' => [
            'required',
            'string',
            'max:32',
            'regex:/^[A-Za-z0-9\-_.]+$/',
        ],

        'units.*.price' => [
            'required',
            'numeric',
            'min:0',
        ],

        'units.*.wholesale' => [
            'required',
            'numeric',
            'min:0',
        ],

        'units.*.cost_price' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'is_active' => [
            'required',
            'boolean',
        ],

        'min_stock' => [
            'required',
            'integer',
            'min:0',
        ],
    ], [
        'name.unique' =>
            'ชื่อสินค้านี้มีอยู่ในระบบแล้ว',

        'units.*.unit_barcode.regex' =>
            'บาร์โค้ดใช้ได้เฉพาะตัวอักษร ตัวเลข และเครื่องหมาย - _ .',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Normalize และตรวจข้อมูลซ้ำภายในฟอร์ม
    |--------------------------------------------------------------------------
    */
    $validated['units'] = $this->normalizeUnits(
        $validated['units']
    );

    $this->validateDuplicateUnitNames(
        $validated['units']
    );

    $this->validateDuplicateBarcodes(
        $validated['units']
    );

    /*
    |--------------------------------------------------------------------------
    | ตรวจ unique บาร์โค้ด โดยยกเว้นหน่วยเดิม
    |--------------------------------------------------------------------------
    */
    foreach ($validated['units'] as $index => $unitData) {
        $unitId = $unitData['id'] ?? null;

        if ($unitId) {
            $belongsToProduct = $product->productUnits()
                ->whereKey($unitId)
                ->exists();

            if (!$belongsToProduct) {
                throw ValidationException::withMessages([
                    "units.$index.id" =>
                        'ไม่พบหน่วยสินค้านี้ หรือหน่วยสินค้าไม่ได้อยู่ในสินค้าที่กำลังแก้ไข',
                ]);
            }
        }

        validator(
            $unitData,
            [
                'unit_barcode' => [
                    'required',
                    'string',
                    'max:32',
                    'regex:/^[A-Za-z0-9\-_.]+$/',

                    Rule::unique(
                        'product_units',
                        'barcode'
                    )->ignore($unitId),
                ],
            ],
            [
                'unit_barcode.unique' =>
                    'บาร์โค้ด :input ถูกใช้กับหน่วยสินค้าอื่นแล้ว',

                'unit_barcode.regex' =>
                    'บาร์โค้ดใช้ได้เฉพาะตัวอักษร ตัวเลข และเครื่องหมาย - _ .',
            ]
        )->validate();
    }

    DB::transaction(function () use (
        $request,
        $validated,
        $product
    ) {
        /*
        |--------------------------------------------------------------------------
        | อัปเดตข้อมูลสินค้าหลัก
        |--------------------------------------------------------------------------
        */
        $product->update([
            'name' => trim($validated['name']),

            'category_id' =>
                $validated['category_id'] ?? null,

            'barcode' =>
                isset($validated['barcode'])
                    ? trim($validated['barcode'])
                    : null,

            'sku' =>
                isset($validated['sku'])
                    ? trim($validated['sku'])
                    : null,

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                (bool) $validated['is_active'],

            'min_stock' =>
                (int) $validated['min_stock'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | อัปโหลดรูปใหม่
        |--------------------------------------------------------------------------
        | จะลบรูปเดิมเฉพาะเมื่อมีการอัปโหลดรูปใหม่เข้ามา
        */
        if ($request->hasFile('images')) {
            $oldImages = $product->images()->get();
            $oldImagesCount = $oldImages->count();

            foreach ($oldImages as $oldImage) {
                $storedPath = $this->normalizePublicImagePath(
                    $oldImage->image_path
                );

                if (
                    $storedPath &&
                    Storage::disk('public')->exists($storedPath)
                ) {
                    Storage::disk('public')->delete($storedPath);
                }

                $oldImage->delete();
            }

            $newImagesCount = 0;

            foreach ($request->file('images', []) as $image) {
                $path = $image->store(
                    'products',
                    'public'
                );

                $product->images()->create([
                    'image_path' => $path,
                ]);

                $newImagesCount++;
            }

            activity('product')
                ->performedOn($product)
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'attributes' => [
                        'image' =>
                            "อัปโหลดรูปภาพใหม่ {$newImagesCount} รูป",
                    ],

                    'old' => [
                        'image' =>
                            "รูปภาพเดิม {$oldImagesCount} รูป",
                    ],
                ])
                ->log('updated');
        }

        /*
        |--------------------------------------------------------------------------
        | ลบหน่วยที่ถูกนำออกจากฟอร์ม
        |--------------------------------------------------------------------------
        */
        $unitIdsInForm = collect($validated['units'])
            ->pluck('id')
            ->filter()
            ->map(fn ($unitId) => (int) $unitId)
            ->values()
            ->all();

        $unitsToDelete = $product->productUnits()
            ->when(
                count($unitIdsInForm) > 0,
                fn ($query) =>
                    $query->whereNotIn('id', $unitIdsInForm)
            )
            ->when(
                count($unitIdsInForm) === 0,
                fn ($query) => $query
            )
            ->get();

        foreach ($unitsToDelete as $unitToDelete) {
            $hasSale = DB::table('sale_items')
                ->where(
                    'product_unit_id',
                    $unitToDelete->id
                )
                ->exists();

            $hasStock = DB::table('product_stocks')
                ->where(
                    'unit_id',
                    $unitToDelete->id
                )
                ->where(function ($query) {
                    $query
                        ->where('store_stock', '>', 0)
                        ->orWhere('warehouse_stock', '>', 0);
                })
                ->exists();

            if ($hasSale || $hasStock) {
                throw ValidationException::withMessages([
                    'units' =>
                        "ไม่สามารถลบหน่วย {$unitToDelete->unit_name} ได้ เพราะมีประวัติการขายหรือมีสต็อกอยู่",
                ]);
            }

            $unitToDelete->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | อัปเดตหรือสร้างหน่วยสินค้า
        |--------------------------------------------------------------------------
        */
        foreach ($validated['units'] as $index => $unitData) {
            $unitId = $unitData['id'] ?? null;

            $attributes = [
                'unit_name' =>
                    trim($unitData['unit_name']),

                'unit_quantity' =>
                    (int) $unitData['unit_quantity'],

                'barcode' =>
                    trim($unitData['unit_barcode']),

                'price' =>
                    (float) $unitData['price'],

                'wholesale' =>
                    (float) $unitData['wholesale'],

                'cost_price' =>
                    isset($unitData['cost_price'])
                        && $unitData['cost_price'] !== ''
                            ? (float) $unitData['cost_price']
                            : null,
            ];

            if ($unitId) {
                $unit = $product->productUnits()
                    ->whereKey($unitId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $unit->update($attributes);
            } else {
                $product->productUnits()->create(
                    $attributes
                );
            }
        }
    });

    return redirect()
        ->route('product.product.index')
        ->with(
            'success',
            'อัปเดตข้อมูลสินค้าสำเร็จ'
        );
}

private function normalizeUnits(array $units): array
{
    return collect($units)
        ->map(function (array $unit): array {
            $unit['unit_name'] = preg_replace(
                '/\s+/u',
                ' ',
                trim((string) ($unit['unit_name'] ?? ''))
            );

            $unit['unit_barcode'] = trim(
                (string) ($unit['unit_barcode'] ?? '')
            );

            return $unit;
        })
        ->values()
        ->all();
}

private function validateDuplicateUnitNames(array $units): void
{
    $normalizedNames = collect($units)
        ->mapWithKeys(function (array $unit, int $index): array {
            return [
                $index => mb_strtolower(
                    (string) ($unit['unit_name'] ?? ''),
                    'UTF-8'
                ),
            ];
        });

    $duplicateNames = $normalizedNames
        ->filter(fn ($name) => $name !== '')
        ->duplicates()
        ->unique()
        ->values();

    if ($duplicateNames->isEmpty()) {
        return;
    }

    $errors = [];

    foreach ($normalizedNames as $index => $name) {
        if ($duplicateNames->contains($name)) {
            $errors["units.$index.unit_name"] =
                'ชื่อหน่วยนับซ้ำภายในสินค้านี้';
        }
    }

    throw ValidationException::withMessages($errors);
}

private function validateDuplicateBarcodes(array $units): void
{
    $normalizedBarcodes = collect($units)
        ->mapWithKeys(function (array $unit, int $index): array {
            return [
                $index => strtolower(
                    (string) ($unit['unit_barcode'] ?? '')
                ),
            ];
        });

    $duplicateBarcodes = $normalizedBarcodes
        ->filter(fn ($barcode) => $barcode !== '')
        ->duplicates()
        ->unique()
        ->values();

    if ($duplicateBarcodes->isEmpty()) {
        return;
    }

    $errors = [];

    foreach ($normalizedBarcodes as $index => $barcode) {
        if ($duplicateBarcodes->contains($barcode)) {
            $errors["units.$index.unit_barcode"] =
                'บาร์โค้ดซ้ำภายในฟอร์ม';
        }
    }

    throw ValidationException::withMessages($errors);
}

private function normalizePublicImagePath(
    ?string $imagePath
): ?string {
    if (!$imagePath) {
        return null;
    }

    $imagePath = ltrim(
        trim($imagePath),
        '/'
    );

    $prefixes = [
        'public/storage/',
        'storage/app/public/',
        'app/public/',
        'public/',
        'storage/',
    ];

    foreach ($prefixes as $prefix) {
        if (str_starts_with($imagePath, $prefix)) {
            $imagePath = substr(
                $imagePath,
                strlen($prefix)
            );

            break;
        }
    }

    return $imagePath;
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
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $suppliers = DB::table('suppliers')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'products.add_stock',
            compact('products', 'suppliers')
        );
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
        $validated = $request->validate(
            [
                'supplier_id' => [
                    'required',
                    'integer',
                    Rule::exists('suppliers', 'id')
                        ->where(fn ($query) =>
                            $query->where('is_active', true)
                        ),
                ],
                'reference_number' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
                'received_date' => [
                    'required',
                    'date',
                ],
                'note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'items' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'items.*.*.product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                ],
                'items.*.*.unit_id' => [
                    'required',
                    'integer',
                    'exists:product_units,id',
                ],
                'items.*.*.quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],
                'items.*.*.unit_quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],
                'items.*.*.location' => [
                    'required',
                    Rule::in(['warehouse', 'store']),
                ],
                'items.*.*.is_free' => [
                    'nullable',
                    'boolean',
                ],
                'items.*.*.lot_number' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
                'items.*.*.no_expiry' => [
                    'nullable',
                    'boolean',
                ],
                'items.*.*.expiry_date' => [
                    'nullable',
                    'date',
                ],
                'items.*.*.unit_cost' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],
            ],
            [
                'supplier_id.required' =>
                    'กรุณาเลือกผู้จำหน่าย',
                'items.required' =>
                    'กรุณาสแกนสินค้าอย่างน้อย 1 รายการ',
                'items.min' =>
                    'กรุณาสแกนสินค้าอย่างน้อย 1 รายการ',
            ]
        );

        $flatItems = collect($validated['items'])
            ->flatMap(fn ($productItems) =>
                collect($productItems)->values()
            )
            ->values();

        if ($flatItems->isEmpty()) {
            throw ValidationException::withMessages([
                'items' =>
                    'กรุณาสแกนสินค้าอย่างน้อย 1 รายการ',
            ]);
        }

        DB::transaction(function () use (
            $validated,
            $flatItems
        ) {
            $receiptNumber =
                'REC-' .
                now()->format('Ymd-His') .
                '-' .
                strtoupper(substr(uniqid(), -5));

            $stockReceiptId = DB::table('stock_receipts')
                ->insertGetId([
                    'receipt_number' => $receiptNumber,
                    'supplier_id' => $validated['supplier_id'],
                    'reference_number' =>
                        $validated['reference_number'] ?? null,
                    'received_date' => $validated['received_date'],
                    'received_by' => Auth::id(),
                    'note' => $validated['note'] ?? null,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            foreach ($flatItems as $index => $item) {
                $productId = (int) $item['product_id'];
                $unitId = (int) $item['unit_id'];
                $quantity = (int) $item['quantity'];
                $unitQuantity = (int) $item['unit_quantity'];
                $location = $item['location'];
                $isFree = (bool) ($item['is_free'] ?? false);
                $noExpiry =
                    (bool) ($item['no_expiry'] ?? false);

                $lotNumber = trim(
                    (string) ($item['lot_number'] ?? '')
                );

                $expiryDate = $noExpiry
                    ? null
                    : ($item['expiry_date'] ?? null);

                $unit = ProductUnit::query()
                    ->whereKey($unitId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$unit) {
                    throw ValidationException::withMessages([
                        "items.$index.unit_id" =>
                            'หน่วยสินค้าไม่ตรงกับสินค้าที่เลือก',
                    ]);
                }

                $product = Product::query()
                    ->whereKey($productId)
                    ->where('is_active', true)
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        "items.$index.product_id" =>
                            'สินค้านี้ถูกปิดใช้งานหรือไม่มีอยู่แล้ว',
                    ]);
                }

                $stock = ProductStocks::query()
                    ->where('product_id', $productId)
                    ->where('unit_id', $unitId)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = ProductStocks::create([
                        'product_id' => $productId,
                        'unit_id' => $unitId,
                        'warehouse_stock' => 0,
                        'store_stock' => 0,
                        'track_stock' => 1,
                    ]);
                }

                if ($location === 'store') {
                    $stock->increment(
                        'store_stock',
                        $quantity
                    );
                } else {
                    $stock->increment(
                        'warehouse_stock',
                        $quantity
                    );
                }

                $batchCode = $lotNumber !== ''
                    ? $lotNumber
                    : sprintf(
                        'BATCH-%s-%s-%s',
                        $productId,
                        $unitId,
                        now()->format('YmdHis')
                    );

                $batchQuantity =
                    $quantity * $unitQuantity;

                $batch = ProductBatch::create([
                    'product_id' => $productId,
                    'product_unit_id' => $unitId,
                    'batch_code' => $batchCode,
                    'quantity' => $batchQuantity,
                    'expiry_date' => $expiryDate,
                    'is_acknowledged' => 0,
                ]);

                $unitCost = isset($item['unit_cost'])
                    && $item['unit_cost'] !== ''
                        ? (float) $item['unit_cost']
                        : (
                            $unit->cost_price !== null
                                ? (float) $unit->cost_price
                                : null
                        );

                $receiptItemData = [
                    'stock_receipt_id' => $stockReceiptId,
                    'product_id' => $productId,
                    'product_unit_id' => $unitId,
                    'product_batch_id' => $batch->id,
                    'quantity' => $quantity,
                    'unit_quantity' => $unitQuantity,
                    'location' => $location,
                    'is_free' => $isFree,
                    'lot_number' =>
                        $lotNumber !== ''
                            ? $lotNumber
                            : null,
                    'expiry_date' => $expiryDate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn(
                    'stock_receipt_items',
                    'unit_cost'
                )) {
                    $receiptItemData['unit_cost'] = $unitCost;
                }

                DB::table('stock_receipt_items')
                    ->insert($receiptItemData);

                $movementData = [
                    'product_id' => $productId,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'unit_quantity' => $unitQuantity,
                    'unit' => $unit->unit_name,
                    'location' => $location,
                    'note' =>
                        $validated['note']
                        ?? "รับสินค้าเลขที่ {$receiptNumber}",
                    'is_free' => $isFree,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn(
                    'product_stock_movements',
                    'stock_receipt_id'
                )) {
                    $movementData['stock_receipt_id'] =
                        $stockReceiptId;
                }

                DB::table('product_stock_movements')
                    ->insert($movementData);

                activity('product')
                    ->causedBy(Auth::user())
                    ->performedOn($product)
                    ->withProperties([
                        'stock_receipt_id' => $stockReceiptId,
                        'receipt_number' => $receiptNumber,
                        'supplier_id' =>
                            $validated['supplier_id'],
                        'unit_id' => $unitId,
                        'quantity' => $quantity,
                        'unit_quantity' => $unitQuantity,
                        'location' => $location,
                        'batch_code' => $batchCode,
                        'expiry_date' => $expiryDate,
                        'is_free' => $isFree,
                    ])
                    ->event('stock_received')
                    ->log('รับสินค้าเข้าสต็อก');
            }
        });

        return redirect()
            ->route('product.products.add-stock-form')
            ->with(
                'success',
                'บันทึกการรับสินค้าและเพิ่มสต็อกเรียบร้อยแล้ว'
            );
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
        $products = $products
            ->with(['category', 'stock', 'defaultUnit'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
    $query = Activity::where('log_name', 'product')
        ->where('subject_type', Product::class)
        ->with(['subject', 'causer']);

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    if ($request->filled('event')) {
        $query->where('event', $request->event);
    } else {
        $query->whereNotIn('event', [
            'stock_added',
        ]);
    }

    $activities = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view(
        'products.show-histroy',
        compact('activities')
    );
}

public function searchStockInHistory(Request $request)
{
    $validated = $request->validate([
        'received_by' => ['nullable', 'integer', 'exists:users,id'],
        'search' => ['nullable', 'string', 'max:255'],
        'receipt_number' => ['nullable', 'string', 'max:50'],
        'reference_number' => ['nullable', 'string', 'max:100'],
        'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
        'location' => ['nullable', Rule::in(['warehouse', 'store'])],
        'is_free' => ['nullable', Rule::in(['0', '1'])],
        'from' => ['nullable', 'date'],
        'to' => ['nullable', 'date', 'after_or_equal:from'],
        'print' => ['nullable', 'boolean'],
    ]);

    $search = trim((string) ($validated['search'] ?? ''));
    $receiptNumber = trim((string) ($validated['receipt_number'] ?? ''));
    $referenceNumber = trim((string) ($validated['reference_number'] ?? ''));

    $receivedBy = $validated['received_by'] ?? null;
    $supplierId = $validated['supplier_id'] ?? null;
    $location = $validated['location'] ?? null;
    $isFree = $validated['is_free'] ?? null;
    $from = $validated['from'] ?? null;
    $to = $validated['to'] ?? null;

    $isPrint = $request->boolean('print');

    /*
    |--------------------------------------------------------------------------
    | Query ใบรับสินค้า: 1 แถวต่อ 1 ใบรับ
    |--------------------------------------------------------------------------
    */
    $query = DB::table('stock_receipts as sr')
        ->leftJoin('suppliers as s', 's.id', '=', 'sr.supplier_id')
        ->leftJoin('users as u', 'u.id', '=', 'sr.received_by')
        ->where('sr.status', 'completed')
        ->select([
            'sr.id',
            'sr.receipt_number',
            'sr.supplier_id',
            'sr.reference_number',
            'sr.received_date',
            'sr.received_by',
            'sr.note',
            'sr.status',
            'sr.created_at',
            's.name as supplier_name',
            'u.name as received_by_name',
        ])
        ->selectSub(
            DB::table('stock_receipt_items as count_items')
                ->selectRaw('COUNT(*)')
                ->whereColumn('count_items.stock_receipt_id', 'sr.id'),
            'item_count'
        )
        ->selectSub(
            DB::table('stock_receipt_items as sum_items')
                ->selectRaw('COALESCE(SUM(sum_items.quantity), 0)')
                ->whereColumn('sum_items.stock_receipt_id', 'sr.id'),
            'total_quantity'
        );

    // ค้นหาชื่อสินค้า บาร์โค้ด หรือหน่วยสินค้าในใบรับ
    if ($search !== '') {
        $query->whereExists(function ($subQuery) use ($search) {
            $subQuery->selectRaw('1')
                ->from('stock_receipt_items as ri')
                ->join('products as p', 'p.id', '=', 'ri.product_id')
                ->leftJoin(
                    'product_units as pu',
                    'pu.id',
                    '=',
                    'ri.product_unit_id'
                )
                ->whereColumn('ri.stock_receipt_id', 'sr.id')
                ->where(function ($filter) use ($search) {
                    $filter->where('p.name', 'like', "%{$search}%")
                        ->orWhere('p.barcode', 'like', "%{$search}%")
                        ->orWhere('pu.barcode', 'like', "%{$search}%")
                        ->orWhere('pu.unit_name', 'like', "%{$search}%");
                });
        });
    }

    if ($receiptNumber !== '') {
        $query->where(
            'sr.receipt_number',
            'like',
            "%{$receiptNumber}%"
        );
    }

    if ($referenceNumber !== '') {
        $query->where(
            'sr.reference_number',
            'like',
            "%{$referenceNumber}%"
        );
    }

    if ($receivedBy !== null && $receivedBy !== '') {
        $query->where('sr.received_by', $receivedBy);
    }

    if ($supplierId !== null && $supplierId !== '') {
        $query->where('sr.supplier_id', $supplierId);
    }

    // กรองคลังสินค้า / หน้าร้าน
    if ($location !== null && $location !== '') {
        $query->whereExists(function ($subQuery) use ($location) {
            $subQuery->selectRaw('1')
                ->from('stock_receipt_items as ri')
                ->whereColumn('ri.stock_receipt_id', 'sr.id')
                ->where('ri.location', $location);
        });
    }

    // กรองสินค้าแถม / ไม่ใช่สินค้าแถม
    if ($isFree !== null && $isFree !== '') {
        $query->whereExists(function ($subQuery) use ($isFree) {
            $subQuery->selectRaw('1')
                ->from('stock_receipt_items as ri')
                ->whereColumn('ri.stock_receipt_id', 'sr.id')
                ->where('ri.is_free', (int) $isFree);
        });
    }

    if ($from) {
        $query->whereDate('sr.received_date', '>=', $from);
    }

    if ($to) {
        $query->whereDate('sr.received_date', '<=', $to);
    }

    $query->orderByDesc('sr.received_date')
        ->orderByDesc('sr.id');

    /*
    |--------------------------------------------------------------------------
    | แบ่งหน้า หรือแสดงทั้งหมดสำหรับพิมพ์
    |--------------------------------------------------------------------------
    */
    $receipts = $isPrint
        ? $query->get()
        : $query->paginate(20)->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | ดึงรายละเอียดสินค้าของใบรับที่แสดงอยู่
    |--------------------------------------------------------------------------
    */
    $receiptIds = $isPrint
        ? $receipts->pluck('id')
        : $receipts->getCollection()->pluck('id');

    $itemsByReceipt = collect();

    if ($receiptIds->isNotEmpty()) {
        $itemsByReceipt = DB::table('stock_receipt_items as ri')
            ->join('products as p', 'p.id', '=', 'ri.product_id')
            ->leftJoin(
                'product_units as pu',
                'pu.id',
                '=',
                'ri.product_unit_id'
            )
            ->whereIn('ri.stock_receipt_id', $receiptIds)
            ->select([
                'ri.id',
                'ri.stock_receipt_id',
                'ri.product_id',
                'ri.product_unit_id',
                'ri.product_batch_id',
                'ri.quantity',
                'ri.unit_quantity',
                'ri.location',
                'ri.is_free',
                'ri.lot_number',
                'ri.expiry_date',
                'p.name as product_name',
                'p.barcode as product_barcode',
                'pu.unit_name',
                'pu.barcode as unit_barcode',
            ])
            ->orderBy('ri.id')
            ->get()
            ->groupBy('stock_receipt_id');
    }

    // แนบรายการสินค้าเข้าไปในแต่ละใบรับ
    if ($isPrint) {
        $receipts->each(function ($receipt) use ($itemsByReceipt) {
            $receipt->items = $itemsByReceipt->get($receipt->id, collect());
        });
    } else {
        $receipts->getCollection()->transform(
            function ($receipt) use ($itemsByReceipt) {
                $receipt->items = $itemsByReceipt->get(
                    $receipt->id,
                    collect()
                );

                return $receipt;
            }
        );
    }

    $suppliers = DB::table('suppliers')
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name']);

    $receivers = DB::table('users')
        ->orderBy('name')
        ->get(['id', 'name']);

    return view('products.stock-in-history', compact(
        'receipts',
        'suppliers',
        'receivers',
        'search',
        'receiptNumber',
        'referenceNumber',
        'supplierId',
        'location',
        'isFree',
        'from',
        'to',
        'isPrint',
        'receivedBy'
    ));
}
public function movementHistory(Request $request)
{
    $search = $request->input('search');
    $from = $request->input('from');
    $to = $request->input('to');

    $query = ProductStockMovement::with(['product'])
        // ✅ กรองเอาเฉพาะรายการที่เป็นการ "ย้ายคลัง" หรือ "ยกเลิกการขาย"
        ->where(function ($q) {
            $q->where('note', 'like', '[ยกเลิกการขาย]%')
              ->orWhere('note', 'like', '[ย้ายคลัง]%');
        })
        ->when($search, function ($query, $search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('barcode', 'like', "%$search%");
            });
        })
        ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
        ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
        ->orderBy('created_at', 'desc');

    $movements = $query->paginate(30);

    return view('products.movement-history', compact('movements', 'search', 'from', 'to'));
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
    $validated = $request->validate([
        'barcode' => [
            'required',
            'string',
            'max:32',
        ],

        'unit_id' => [
            'nullable',
            'integer',
        ],

        'product_unit_id' => [
            'nullable',
            'integer',
        ],

        'exclude_unit_id' => [
            'nullable',
            'integer',
        ],
    ]);

    $barcode = trim($validated['barcode']);

    $unitId =
        $validated['exclude_unit_id']
        ?? $validated['product_unit_id']
        ?? $validated['unit_id']
        ?? null;

    $exists = ProductUnit::query()
        ->where('barcode', $barcode)
        ->when(
            $unitId,
            function ($query) use ($unitId) {
                $query->where('id', '!=', $unitId);
            }
        )
        ->exists();

    return response()->json([
        'exists' => $exists,
        'available' => !$exists,
    ]);
}

public function checkProductName(Request $request)
{
    $name = $request->name;
    $productId = $request->product_id;

    $query = Product::where('name', $name);

    // ยกเว้นสินค้าตัวเองตอน edit
    if ($productId) {

        $query->where('id', '!=', $productId);
    }

    return response()->json([
        'exists' => $query->exists()
    ]);
}
}