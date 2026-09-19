@extends('layouts.layout')

@section('content')
<style>
    [data-product-editor] {
        --pe-bg: #080808;
        --pe-panel: #151515;
        --pe-panel-soft: #1d1d1d;
        --pe-yellow: #ffc107;
        --pe-yellow-soft: #ffe27a;
        --pe-text: #f7f7f7;
        --pe-muted: #9d9d9d;
        --pe-border: rgba(255,255,255,.09);
        --pe-danger: #ff7180;
        --pe-success: #59d99d;
        color: var(--pe-text);
    }

    [data-product-editor] .editor-shell {
        max-width: 1480px;
        margin: 0 auto;
    }

    [data-product-editor] .editor-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.4rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(255,193,7,.32);
        border-radius: 22px;
        background:
            radial-gradient(circle at 90% 10%, rgba(255,193,7,.20), transparent 28%),
            linear-gradient(135deg, #222, #090909 72%);
        box-shadow: 0 20px 50px rgba(0,0,0,.32);
    }

    [data-product-editor] .hero-copy {
        display: flex;
        align-items: center;
        gap: .9rem;
    }

    [data-product-editor] .hero-icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--pe-yellow), var(--pe-yellow-soft));
        color: #111;
        font-size: 1.4rem;
        box-shadow: 0 10px 25px rgba(255,193,7,.22);
    }

    [data-product-editor] .hero-kicker {
        color: var(--pe-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    [data-product-editor] .hero-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.25rem, 2.4vw, 1.85rem);
        font-weight: 900;
    }

    [data-product-editor] .hero-subtitle {
        margin: .3rem 0 0;
        color: var(--pe-muted);
        font-size: .85rem;
    }

    [data-product-editor] .panel-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--pe-border);
        border-radius: 20px;
        background: linear-gradient(160deg, var(--pe-panel), #101010);
        box-shadow: 0 16px 40px rgba(0,0,0,.28);
    }

    [data-product-editor] .panel-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid rgba(255,193,7,.25);
        background: linear-gradient(135deg, #222, #111);
    }

    [data-product-editor] .panel-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: var(--pe-yellow);
        font-size: .95rem;
        font-weight: 900;
    }

    [data-product-editor] .panel-body {
        padding: 1.15rem;
    }

    [data-product-editor] .form-label {
        color: #e9e9e9;
        font-size: .79rem;
        font-weight: 800;
    }

    [data-product-editor] .form-control,
    [data-product-editor] .form-select,
    [data-product-editor] .input-group-text {
        min-height: 45px;
        border-color: #3f3f3f !important;
        background: #0c0c0c !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    [data-product-editor] .form-control:focus,
    [data-product-editor] .form-select:focus {
        border-color: var(--pe-yellow) !important;
        box-shadow: 0 0 0 .2rem rgba(255,193,7,.12) !important;
    }

    [data-product-editor] .form-control::placeholder {
        color: #666;
    }

    [data-product-editor] .form-control[readonly] {
        background: #202020 !important;
        color: #aaa !important;
    }

    [data-product-editor] .form-select option {
        background: #111;
        color: #fff;
    }

    [data-product-editor] .field-feedback {
        display: none;
        margin-top: .42rem;
        font-size: .74rem;
        font-weight: 750;
    }

    [data-product-editor] .field-feedback.show {
        display: block;
    }

    [data-product-editor] .field-feedback.error {
        color: var(--pe-danger);
    }

    [data-product-editor] .field-feedback.success {
        color: var(--pe-success);
    }

    [data-product-editor] .field-feedback.checking {
        color: var(--pe-muted);
    }

    [data-product-editor] .is-valid {
        border-color: rgba(89,217,157,.72) !important;
    }

    [data-product-editor] .is-invalid {
        border-color: rgba(255,113,128,.82) !important;
    }

    [data-product-editor] .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(92px, 1fr));
        gap: .7rem;
        margin-top: .75rem;
    }

    [data-product-editor] .image-item {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        border: 1px solid var(--pe-border);
        border-radius: 14px;
        background: #0b0b0b;
    }

    [data-product-editor] .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    [data-product-editor] .image-delete {
        position: absolute;
        top: .35rem;
        right: .35rem;
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border: 0;
        border-radius: 9px;
        background: rgba(190,35,50,.92);
        color: #fff;
    }

    [data-product-editor] .unit-card {
        overflow: hidden;
        margin-bottom: .85rem;
        border: 1px solid var(--pe-border);
        border-radius: 17px;
        background: var(--pe-panel-soft);
        transition: .2s;
    }

    [data-product-editor] .unit-card:hover {
        border-color: rgba(255,193,7,.42);
    }

    [data-product-editor] .unit-card.has-error {
        border-color: rgba(255,113,128,.75);
    }

    [data-product-editor] .unit-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .8rem 1rem;
        border-bottom: 1px solid var(--pe-border);
        background: #171717;
    }

    [data-product-editor] .unit-title {
        margin: 0;
        color: var(--pe-yellow);
        font-size: .87rem;
        font-weight: 900;
    }

    [data-product-editor] .unit-card-body {
        padding: 1rem;
    }

    [data-product-editor] .barcode-actions {
        display: flex;
        gap: .45rem;
    }

    [data-product-editor] .btn-barcode {
        min-height: 45px;
        border-color: rgba(255,193,7,.45);
        background: rgba(255,193,7,.08);
        color: var(--pe-yellow);
        font-weight: 850;
        white-space: nowrap;
    }

    [data-product-editor] .btn-barcode:hover {
        background: var(--pe-yellow);
        color: #111;
    }

    [data-product-editor] .btn-editor-primary,
    [data-product-editor] .btn-editor-outline {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .65rem 1rem;
        border-radius: 12px;
        font-weight: 850;
        text-decoration: none;
    }

    [data-product-editor] .btn-editor-primary {
        border: 1px solid var(--pe-yellow);
        background: linear-gradient(135deg, var(--pe-yellow), var(--pe-yellow-soft));
        color: #111;
    }

    [data-product-editor] .btn-editor-primary:hover {
        color: #000;
        transform: translateY(-1px);
    }

    [data-product-editor] .btn-editor-primary:disabled {
        border-color: #444;
        background: #333;
        color: #888;
        transform: none;
    }

    [data-product-editor] .btn-editor-outline {
        border: 1px solid #494949;
        background: rgba(255,255,255,.025);
        color: #ddd;
    }

    [data-product-editor] .btn-editor-outline:hover {
        border-color: var(--pe-yellow);
        color: var(--pe-yellow);
    }

    [data-product-editor] .validation-summary {
        display: none;
        margin-bottom: 1rem;
        padding: .85rem 1rem;
        border: 1px solid rgba(255,113,128,.35);
        border-radius: 13px;
        background: rgba(255,113,128,.08);
        color: #ff9ba5;
        font-size: .8rem;
    }

    [data-product-editor] .validation-summary.show {
        display: block;
    }

    [data-product-editor] .sticky-actions {
        position: sticky;
        bottom: 0;
        z-index: 20;
        display: flex;
        justify-content: flex-end;
        gap: .65rem;
        padding: .9rem 1rem;
        margin-top: 1rem;
        border: 1px solid var(--pe-border);
        border-radius: 16px;
        background: rgba(15,15,15,.94);
        backdrop-filter: blur(12px);
        box-shadow: 0 -10px 30px rgba(0,0,0,.26);
    }

    @media (max-width: 767.98px) {
        [data-product-editor] .editor-hero {
            align-items: stretch;
            flex-direction: column;
        }

        [data-product-editor] .hero-icon {
            width: 50px;
            height: 50px;
        }

        [data-product-editor] .sticky-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        [data-product-editor] .sticky-actions .btn,
        [data-product-editor] .sticky-actions a {
            width: 100%;
        }

        [data-product-editor] .barcode-actions {
            width: 100%;
        }

        [data-product-editor] .btn-barcode {
            flex: 1;
        }
    }
</style>

<div class="container-fluid py-4" data-product-editor>
    <div class="editor-shell">
        <div class="editor-hero">
            <div class="hero-copy">
                <div class="hero-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="hero-kicker">Product editor</div>
                    <h1 class="hero-title">แก้ไขสินค้าและหน่วยนับ</h1>
                    <p class="hero-subtitle">
                        แก้ไขรายละเอียด ราคา หน่วยนับ และบาร์โค้ด โดยระบบจะตรวจข้อมูลซ้ำก่อนบันทึก
                    </p>
                </div>
            </div>

            <a href="{{ route('product.product.index') }}" class="btn-editor-outline">
                <i class="bi bi-arrow-left"></i>
                กลับหน้ารายการ
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <strong>ไม่สามารถบันทึกข้อมูลได้</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="validationSummary" class="validation-summary">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            กรุณาแก้ไขข้อมูลที่ซ้ำหรือไม่ถูกต้องก่อนบันทึก
        </div>

        <form id="productEditForm"
              action="{{ route('product.updateWithUnit', $product->id) }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate>
            @method('PUT')
            @csrf

            <div class="row g-3">
                <div class="col-xl-5">
                    <section class="panel-card">
                        <div class="panel-heading">
                            <h2 class="panel-title">
                                <i class="bi bi-box-seam-fill"></i>
                                ข้อมูลสินค้า
                            </h2>
                        </div>

                        <div class="panel-body">
                            <div class="mb-3">
                                <label for="productName" class="form-label">
                                    ชื่อสินค้า <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="productName"
                                    value="{{ old('name', $product->name) }}"
                                    data-old-name="{{ $product->name }}"
                                    class="form-control"
                                    placeholder="กรอกชื่อสินค้า"
                                    maxlength="255"
                                    autocomplete="off"
                                    required
                                >
                                <div id="productNameFeedback" class="field-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">ประเภทสินค้า</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">-- เลือกประเภทสินค้า --</option>
                                    @foreach ($categories as $category)
                                        <option
                                            value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                                        >
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="min_stock" class="form-label">จำนวนแจ้งเตือนขั้นต่ำ</label>
                                <input
                                    type="number"
                                    name="min_stock"
                                    id="min_stock"
                                    class="form-control"
                                    value="{{ old('min_stock', $product->min_stock ?? 10) }}"
                                    min="0"
                                    step="1"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">ของแถม / รายละเอียดเพิ่มเติม</label>
                                <textarea
                                    name="description"
                                    id="description"
                                    class="form-control"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="กรอกรายละเอียดเพิ่มเติม"
                                >{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">เพิ่มรูปภาพสินค้า</label>
                                <input
                                    type="file"
                                    name="images[]"
                                    id="images"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp"
                                    multiple
                                >
                                <small class="text-secondary">รองรับ JPG, PNG และ WEBP</small>

                                @if ($product->images->count())
                                    <div class="image-grid">
                                        @foreach ($product->images as $image)
                                            @php
                                                $rawImagePath = trim((string) $image->image_path);

                                                if (\Illuminate\Support\Str::startsWith($rawImagePath, ['http://', 'https://'])) {
                                                    $imageUrl = $rawImagePath;
                                                } else {
                                                    $imagePath = ltrim($rawImagePath, '/');

                                                    foreach ([
                                                        'public/storage/',
                                                        'storage/app/public/',
                                                        'app/public/',
                                                        'public/',
                                                        'storage/',
                                                    ] as $prefix) {
                                                        if (\Illuminate\Support\Str::startsWith($imagePath, $prefix)) {
                                                            $imagePath = \Illuminate\Support\Str::after($imagePath, $prefix);
                                                            break;
                                                        }
                                                    }

                                                    $imageUrl = asset('storage/' . $imagePath);
                                                }
                                            @endphp
                                            <div class="image-item">
                                                <img
                                                    src="{{ $imageUrl }}"
                                                    alt="{{ $product->name }}"
                                                    loading="lazy"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';"
                                                >
                                                <button
                                                    type="button"
                                                    class="image-delete"
                                                    title="ลบรูปภาพ"
                                                    onclick="deleteImage({{ $image->id }}, this)"
                                                >
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-0">
                                <label for="is_active" class="form-label">สถานะการขาย</label>
                                <select name="is_active" id="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>
                                        เปิดใช้งาน
                                    </option>
                                    <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>
                                        ปิดใช้งาน
                                    </option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xl-7">
                    <section class="panel-card">
                        <div class="panel-heading">
                            <h2 class="panel-title">
                                <i class="bi bi-upc-scan"></i>
                                หน่วยนับและบาร์โค้ด
                            </h2>

                            <button type="button" class="btn btn-sm btn-outline-warning" id="addUnitBtn">
                                <i class="bi bi-plus-circle"></i>
                                เพิ่มหน่วยนับ
                            </button>
                        </div>

                        <div class="panel-body">
                            <div id="unitsContainer">
                                @forelse ($product->productUnits as $index => $unit)
                                    @php
                                        $hasSale = \App\Models\SaleItem::where('product_unit_id', $unit->id)->exists();
                                        $hasStock = \App\Models\ProductStocks::where('unit_id', $unit->id)
                                            ->where('store_stock', '>', 0)
                                            ->exists();
                                    @endphp

                                    <article class="unit-card unit-group" data-unit-index="{{ $index }}">
                                        <div class="unit-card-header">
                                            <h3 class="unit-title">
                                                <i class="bi bi-box2"></i>
                                                หน่วยนับ #<span class="unit-number">{{ $index + 1 }}</span>
                                            </h3>

                                            @if (!$hasSale && !$hasStock)
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-unit">
                                                    <i class="bi bi-trash3"></i>
                                                    ลบ
                                                </button>
                                            @else
                                                <span class="badge text-bg-secondary">
                                                    <i class="bi bi-lock-fill"></i>
                                                    มีการใช้งาน
                                                </span>
                                            @endif
                                        </div>

                                        <div class="unit-card-body">
                                            <input
                                                type="hidden"
                                                name="units[{{ $index }}][id]"
                                                value="{{ $unit->id }}"
                                            >

                                            <div class="row g-3">
                                                <div class="col-md-7">
                                                    <label class="form-label">
                                                        ชื่อหน่วยนับ <span class="text-danger">*</span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        name="units[{{ $index }}][unit_name]"
                                                        value="{{ old("units.$index.unit_name", $unit->unit_name) }}"
                                                        class="form-control unit-name-input @error("units.$index.unit_name") is-invalid @enderror"
                                                        data-unit-id="{{ $unit->id }}"
                                                        data-original-unit-name="{{ $unit->unit_name }}"
                                                        maxlength="100"
                                                        autocomplete="off"
                                                        required
                                                    >
                                                    <div class="field-feedback unit-name-feedback @error("units.$index.unit_name") show error @enderror">
                                                        @error("units.$index.unit_name")
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-5">
                                                    <label class="form-label">
                                                        จำนวนต่อหน่วย <span class="text-danger">*</span>
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="units[{{ $index }}][unit_quantity]"
                                                        value="{{ $index === 0 ? 1 : old("units.$index.unit_quantity", $unit->unit_quantity) }}"
                                                        class="form-control"
                                                        min="{{ $index === 0 ? 1 : 2 }}"
                                                        step="1"
                                                        {{ $index === 0 ? 'readonly' : '' }}
                                                        required
                                                    >
                                                    @if ($index === 0)
                                                        <small class="text-success">หน่วยฐานต้องมีค่าเท่ากับ 1</small>
                                                    @endif
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">
                                                        บาร์โค้ด <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input
                                                            type="text"
                                                            name="units[{{ $index }}][unit_barcode]"
                                                            value="{{ old("units.$index.unit_barcode", $unit->barcode) }}"
                                                            data-old-barcode="{{ $unit->barcode }}"
                                                            data-unit-id="{{ $unit->id }}"
                                                            class="form-control barcode-input"
                                                            maxlength="32"
                                                            autocomplete="off"
                                                            inputmode="numeric"
                                                            required
                                                        >
                                                        <button
                                                            type="button"
                                                            class="btn btn-barcode generate-barcode"
                                                            title="สร้างบาร์โค้ดใหม่"
                                                        >
                                                            <i class="bi bi-arrow-repeat"></i>
                                                            เปลี่ยนบาร์โค้ด
                                                        </button>
                                                    </div>
                                                    <div class="field-feedback barcode-feedback"></div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">ราคาขายปลีก</label>
                                                    <input
                                                        type="number"
                                                        name="units[{{ $index }}][price]"
                                                        value="{{ old("units.$index.price", $unit->price) }}"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">ราคาขายส่ง</label>
                                                    <input
                                                        type="number"
                                                        name="units[{{ $index }}][wholesale]"
                                                        value="{{ old("units.$index.wholesale", $unit->wholesale) }}"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">ราคาทุน</label>
                                                    <input
                                                        type="number"
                                                        name="units[{{ $index }}][cost_price]"
                                                        value="{{ old("units.$index.cost_price", $unit->cost_price) }}"
                                                        step="0.01"
                                                        min="0"
                                                        class="form-control"
                                                    >
                                                </div>
                                            </div>

                                            @if ($hasSale || $hasStock)
                                                <p class="mb-0 mt-3 text-secondary small">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    หน่วยนี้ลบไม่ได้ เพราะมีประวัติการขายหรือมีสต็อกอยู่
                                                </p>
                                            @endif
                                        </div>
                                    </article>
                                @empty
                                    <div id="emptyUnitsMessage" class="text-center text-secondary py-4">
                                        ยังไม่มีหน่วยนับ กรุณากด “เพิ่มหน่วยนับ”
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="sticky-actions">
                <a href="{{ route('product.product.index') }}" class="btn-editor-outline">
                    <i class="bi bi-x-circle"></i>
                    ยกเลิก
                </a>

                <button type="submit" class="btn-editor-primary" id="saveBtn">
                    <i class="bi bi-check-circle-fill"></i>
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('productEditForm');
    const productNameInput = document.getElementById('productName');
    const productNameFeedback = document.getElementById('productNameFeedback');
    const unitsContainer = document.getElementById('unitsContainer');
    const addUnitBtn = document.getElementById('addUnitBtn');
    const saveBtn = document.getElementById('saveBtn');
    const validationSummary = document.getElementById('validationSummary');

    let unitIndex = document.querySelectorAll('.unit-group').length;
    let nameTimer = null;
    const barcodeTimers = new WeakMap();
    let pendingRequests = 0;

    const validationState = {
        productName: true,
        unitNames: true,
        barcodes: true
    };

    function normalize(value) {
        return String(value ?? '').trim().replace(/\s+/g, ' ').toLocaleLowerCase('th-TH');
    }

    function setFeedback(element, message = '', type = '') {
        // บางหน่วยอาจไม่มี div feedback ทำให้ element เป็น null
        if (!element) {
            return;
        }

        element.textContent = message;
        element.className = 'field-feedback';

        if (message) {
            element.classList.add('show');

            if (type) {
                element.classList.add(type);
            }
        }
    }

    function updateSaveState() {
        const hasValidationError = Object.values(validationState)
            .some(value => value === false);

        /*
         * อย่าปิดปุ่มบันทึกจาก AJAX validation
         * เพราะหาก endpoint ตรวจสอบล้มเหลว ปุ่มจะถูกล็อกถาวร
         * ให้ Controller เป็นผู้ตรวจข้อมูลขั้นสุดท้าย
         */
        saveBtn.disabled = false;
        validationSummary.classList.toggle('show', hasValidationError);
    }

    function beginRequest() {
        pendingRequests++;
        updateSaveState();
    }

    function endRequest() {
        pendingRequests = Math.max(0, pendingRequests - 1);
        updateSaveState();
    }

    async function validateProductName() {
        const name = productNameInput.value.trim();
        const oldName = productNameInput.dataset.oldName?.trim() ?? '';

        productNameInput.classList.remove('is-valid', 'is-invalid');

        if (!name) {
            validationState.productName = false;
            productNameInput.classList.add('is-invalid');
            setFeedback(productNameFeedback, 'กรุณากรอกชื่อสินค้า', 'error');
            updateSaveState();
            return false;
        }

        if (normalize(name) === normalize(oldName)) {
            validationState.productName = true;
            productNameInput.classList.add('is-valid');
            setFeedback(productNameFeedback, 'ชื่อสินค้าเดิม สามารถใช้งานได้', 'success');
            updateSaveState();
            return true;
        }

        setFeedback(productNameFeedback, 'กำลังตรวจสอบชื่อสินค้า...', 'checking');
        beginRequest();

        try {
            const params = new URLSearchParams({
                name: name,
                product_id: '{{ $product->id }}'
            });

            const response = await fetch(`{{ route('product.name.check') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('ตรวจสอบชื่อสินค้าไม่สำเร็จ');
            }

            const data = await response.json();
            validationState.productName = !data.exists;

            productNameInput.classList.add(data.exists ? 'is-invalid' : 'is-valid');

            setFeedback(
                productNameFeedback,
                data.exists ? 'ชื่อสินค้านี้มีอยู่แล้วในระบบ' : 'ชื่อสินค้านี้สามารถใช้งานได้',
                data.exists ? 'error' : 'success'
            );

            return !data.exists;
        } catch (error) {
            console.error(error);
            validationState.productName = false;
            productNameInput.classList.add('is-invalid');
            setFeedback(productNameFeedback, 'ไม่สามารถตรวจสอบชื่อสินค้าได้ กรุณาลองใหม่', 'error');
            return false;
        } finally {
            endRequest();
        }
    }

    function getUnitNameInputs() {
        return [...unitsContainer.querySelectorAll('.unit-name-input')];
    }

    function getUnitNameFeedback(input) {
        return input
            .closest('.unit-card')
            ?.querySelector('.unit-name-feedback') ?? null;
    }

    function validateUnitNames() {
        const inputs = getUnitNameInputs();
        const groups = new Map();
        let valid = true;

        inputs.forEach(input => {
            const feedback = getUnitNameFeedback(input);
            const cleanedValue = String(input.value ?? '')
                .trim()
                .replace(/\s+/g, ' ');

            input.value = cleanedValue;
            input.classList.remove('is-valid', 'is-invalid');
            setFeedback(feedback);

            const key = normalize(cleanedValue);

            if (!key) {
                valid = false;
                input.classList.add('is-invalid');
                setFeedback(feedback, 'กรุณากรอกชื่อหน่วยนับ', 'error');
                return;
            }

            if (cleanedValue.length > 100) {
                valid = false;
                input.classList.add('is-invalid');
                setFeedback(
                    feedback,
                    'ชื่อหน่วยนับต้องไม่เกิน 100 ตัวอักษร',
                    'error'
                );
                return;
            }

            if (!groups.has(key)) {
                groups.set(key, []);
            }

            groups.get(key).push(input);
        });

        groups.forEach(groupInputs => {
            if (groupInputs.length > 1) {
                valid = false;

                groupInputs.forEach(input => {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    setFeedback(
                        getUnitNameFeedback(input),
                        'ชื่อหน่วยนับซ้ำภายในสินค้านี้',
                        'error'
                    );
                });

                return;
            }

            const input = groupInputs[0];

            if (!input.classList.contains('is-invalid')) {
                input.classList.add('is-valid');
                setFeedback(
                    getUnitNameFeedback(input),
                    'ชื่อหน่วยนับสามารถใช้งานได้',
                    'success'
                );
            }
        });

        validationState.unitNames = valid;
        updateUnitCardStates();
        updateSaveState();

        return valid;
    }

    async function validateBarcodes() {
        const inputs = [...document.querySelectorAll('.barcode-input')];
        const groups = new Map();
        const databaseChecks = [];
        let valid = true;

        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');

            const feedback = input.closest('.unit-card, .unit-group')?.querySelector('.barcode-feedback');
            setFeedback(feedback);

            const barcode = input.value.trim();

            if (!barcode) {
                valid = false;
                input.classList.add('is-invalid');
                setFeedback(feedback, 'กรุณากรอกบาร์โค้ด', 'error');
                return;
            }

            if (!/^[A-Za-z0-9\-_.]+$/.test(barcode)) {
                valid = false;
                input.classList.add('is-invalid');
                setFeedback(feedback, 'บาร์โค้ดใช้ได้เฉพาะตัวอักษร ตัวเลข - _ และ .', 'error');
                return;
            }

            const key = barcode.toLowerCase();
            if (!groups.has(key)) groups.set(key, []);
            groups.get(key).push(input);
        });

        groups.forEach(groupInputs => {
            if (groupInputs.length > 1) {
                valid = false;
                groupInputs.forEach(input => {
                    input.classList.add('is-invalid');
                    const feedback = input.closest('.unit-card, .unit-group')?.querySelector('.barcode-feedback');
                    setFeedback(feedback, 'บาร์โค้ดซ้ำภายในฟอร์ม', 'error');
                });
            }
        });

        inputs.forEach(input => {
            if (input.classList.contains('is-invalid')) return;

            const barcode = input.value.trim();
            const oldBarcode = input.dataset.oldBarcode?.trim() ?? '';

            if (barcode === oldBarcode) {
                input.classList.add('is-valid');
                const feedback = input.closest('.unit-card, .unit-group')?.querySelector('.barcode-feedback');
                setFeedback(feedback, 'บาร์โค้ดเดิม สามารถใช้งานได้', 'success');
                return;
            }

            databaseChecks.push(checkBarcodeInDatabase(input));
        });

        if (databaseChecks.length) {
            beginRequest();
            const results = await Promise.all(databaseChecks);
            endRequest();

            if (results.some(result => result === false)) {
                valid = false;
            }
        }

        validationState.barcodes = valid;
        updateUnitCardStates();
        updateSaveState();
        return valid;
    }

    async function checkBarcodeInDatabase(input) {
        const barcode = input.value.trim();
        const unitId = input.dataset.unitId ?? '';
        const feedback = input.closest('.unit-card, .unit-group')?.querySelector('.barcode-feedback');

        const requestId = String(Date.now()) + Math.random();
        input.dataset.barcodeRequestId = requestId;

        setFeedback(feedback, 'กำลังตรวจสอบบาร์โค้ด...', 'checking');

        try {
            const params = new URLSearchParams({
                barcode: barcode,

                // ส่งหลายชื่อไว้เพื่อรองรับ Controller เดิมและ Controller ที่แก้ใหม่
                unit_id: unitId,
                product_unit_id: unitId,
                exclude_unit_id: unitId,
            });

            const response = await fetch(
                `{{ route('barcode.check') }}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                }
            );

            if (!response.ok) {
                throw new Error('ตรวจสอบบาร์โค้ดไม่สำเร็จ');
            }

            const data = await response.json();

            // ไม่ใช้ผลลัพธ์เก่า หากผู้ใช้พิมพ์ค่าใหม่ระหว่างรอ Request
            if (input.dataset.barcodeRequestId !== requestId) {
                return true;
            }

            const exists = typeof data.exists === 'boolean'
                ? data.exists
                : (typeof data.available === 'boolean' ? !data.available : false);

            input.classList.remove('is-valid', 'is-invalid');
            input.classList.add(exists ? 'is-invalid' : 'is-valid');

            setFeedback(
                feedback,
                exists
                    ? 'บาร์โค้ดนี้ถูกใช้กับหน่วยสินค้าอื่นแล้ว'
                    : 'บาร์โค้ดนี้สามารถใช้งานได้',
                exists ? 'error' : 'success'
            );

            return !exists;
        } catch (error) {
            console.error(error);

            /*
             * ไม่ปิดปุ่มบันทึกเพราะปัญหาเครือข่าย
             * ให้ Laravel Controller ตรวจ unique ซ้ำอีกครั้งตอนบันทึก
             */
            input.classList.remove('is-invalid', 'is-valid');
            setFeedback(
                feedback,
                'ตรวจสอบบาร์โค้ดออนไลน์ไม่ได้ ระบบจะตรวจซ้ำตอนบันทึก',
                'checking'
            );

            return true;
        }
    }

    function updateUnitCardStates() {
        document.querySelectorAll('.unit-card').forEach(card => {
            card.classList.toggle('has-error', Boolean(card.querySelector('.is-invalid')));
        });
    }

    function generateEan13() {
        const first12 = Array.from({ length: 12 }, () => Math.floor(Math.random() * 10)).join('');
        let sum = 0;

        for (let index = 0; index < 12; index++) {
            const digit = Number(first12[index]);
            sum += index % 2 === 0 ? digit : digit * 3;
        }

        const checkDigit = (10 - (sum % 10)) % 10;
        return first12 + checkDigit;
    }

    function barcodeExistsInForm(candidate, currentInput) {
        return [...document.querySelectorAll('.barcode-input')]
            .some(input => input !== currentInput && input.value.trim() === candidate);
    }

    function assignNewBarcode(input) {
        let barcode;

        do {
            barcode = generateEan13();
        } while (barcodeExistsInForm(barcode, input));

        input.value = barcode;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
        input.select();
    }

    function createUnitTemplate(index) {
        return `
            <article class="unit-card unit-group" data-unit-index="${index}">
                <div class="unit-card-header">
                    <h3 class="unit-title">
                        <i class="bi bi-box2"></i>
                        หน่วยนับ #<span class="unit-number">${index + 1}</span>
                    </h3>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-unit">
                        <i class="bi bi-trash3"></i> ลบ
                    </button>
                </div>

                <div class="unit-card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="units[${index}][unit_name]"
                                   class="form-control unit-name-input"
                                   data-unit-id=""
                                   data-original-unit-name=""
                                   maxlength="100"
                                   autocomplete="off"
                                   required>
                            <div class="field-feedback unit-name-feedback"></div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">จำนวนต่อหน่วย <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="units[${index}][unit_quantity]"
                                   class="form-control"
                                   min="2"
                                   step="1"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">บาร์โค้ด <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text"
                                       name="units[${index}][unit_barcode]"
                                       class="form-control barcode-input"
                                       maxlength="32"
                                       autocomplete="off"
                                       inputmode="numeric"
                                       required>
                                <button type="button"
                                        class="btn btn-barcode generate-barcode"
                                        title="สร้างบาร์โค้ดใหม่">
                                    <i class="bi bi-arrow-repeat"></i>
                                    เปลี่ยนบาร์โค้ด
                                </button>
                            </div>
                            <div class="field-feedback barcode-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ราคาขายปลีก</label>
                            <input type="number"
                                   name="units[${index}][price]"
                                   class="form-control"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ราคาขายส่ง</label>
                            <input type="number"
                                   name="units[${index}][wholesale]"
                                   class="form-control"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ราคาทุน</label>
                            <input type="number"
                                   name="units[${index}][cost_price]"
                                   class="form-control"
                                   step="0.01"
                                   min="0">
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    function renumberUnits() {
        document.querySelectorAll('.unit-group').forEach((card, index) => {
            const number = card.querySelector('.unit-number');
            if (number) number.textContent = index + 1;
        });
    }

    productNameInput.addEventListener('input', () => {
        clearTimeout(nameTimer);
        validationState.productName = false;
        updateSaveState();

        nameTimer = setTimeout(validateProductName, 450);
    });

    function handleUnitFieldValidation(event) {
        const target = event.target;

        if (target.classList.contains('unit-name-input')) {
            validateUnitNames();
        }

        if (target.classList.contains('barcode-input')) {
            const input = target;
            const previousTimer = barcodeTimers.get(input);

            if (previousTimer) {
                clearTimeout(previousTimer);
            }

            validationState.barcodes = false;
            updateSaveState();

            const timer = setTimeout(() => {
                validateBarcodes();
                barcodeTimers.delete(input);
            }, 450);

            barcodeTimers.set(input, timer);
        }
    }

    unitsContainer.addEventListener('input', handleUnitFieldValidation);
    unitsContainer.addEventListener('change', handleUnitFieldValidation);

    unitsContainer.addEventListener(
        'blur',
        event => {
            if (event.target.classList.contains('unit-name-input')) {
                validateUnitNames();
            }
        },
        true
    );

    unitsContainer.addEventListener('click', event => {
        const generateButton = event.target.closest('.generate-barcode');
        if (generateButton) {
            const input = generateButton.closest('.input-group').querySelector('.barcode-input');
            assignNewBarcode(input);
            return;
        }

        const removeButton = event.target.closest('.remove-unit');
        if (removeButton) {
            removeButton.closest('.unit-group')?.remove();
            renumberUnits();
            validateUnitNames();
            validateBarcodes();
        }
    });

    addUnitBtn.addEventListener('click', () => {
        document.getElementById('emptyUnitsMessage')?.remove();
        unitsContainer.insertAdjacentHTML('beforeend', createUnitTemplate(unitIndex));

        const newCard = unitsContainer.lastElementChild;
        const barcodeInput = newCard.querySelector('.barcode-input');

        unitIndex++;
        assignNewBarcode(barcodeInput);
        newCard.querySelector('.unit-name-input')?.focus();

        validateUnitNames();
    });

    form.addEventListener('submit', event => {
        event.preventDefault();

        if (form.dataset.submitting === '1') {
            return;
        }

        const hasAtLeastOneUnit =
            document.querySelectorAll('.unit-group').length > 0;

        getUnitNameInputs().forEach(input => {
            input.value = String(input.value ?? '')
                .trim()
                .replace(/\s+/g, ' ');
        });

        const unitNamesValid = validateUnitNames();

        // ตรวจเฉพาะค่าว่าง รูปแบบ และค่าซ้ำภายในฟอร์ม
        // ไม่รอ AJAX เพื่อไม่ให้ปุ่มบันทึกค้าง
        const barcodeInputs = [
            ...document.querySelectorAll('.barcode-input')
        ];

        const barcodeValues = barcodeInputs.map(input =>
            input.value.trim().toLowerCase()
        );

        let barcodesValid = true;

        barcodeInputs.forEach(input => {
            const value = input.value.trim();
            const feedback = input
                .closest('.col-12')
                ?.querySelector('.barcode-feedback');

            input.classList.remove('is-invalid');

            if (!value) {
                barcodesValid = false;
                input.classList.add('is-invalid');
                setFeedback(feedback, 'กรุณากรอกบาร์โค้ด', 'error');
                return;
            }

            if (!/^[A-Za-z0-9\-_.]+$/.test(value)) {
                barcodesValid = false;
                input.classList.add('is-invalid');
                setFeedback(
                    feedback,
                    'รูปแบบบาร์โค้ดไม่ถูกต้อง',
                    'error'
                );
                return;
            }

            const duplicateCount = barcodeValues.filter(
                barcode => barcode === value.toLowerCase()
            ).length;

            if (duplicateCount > 1) {
                barcodesValid = false;
                input.classList.add('is-invalid');
                setFeedback(
                    feedback,
                    'บาร์โค้ดซ้ำภายในฟอร์ม',
                    'error'
                );
            }
        });

        if (!hasAtLeastOneUnit) {
            validationSummary.textContent =
                'กรุณาเพิ่มหน่วยนับอย่างน้อย 1 รายการ';
            validationSummary.classList.add('show');
        }

        const nativeValid = form.checkValidity();

        if (
            !nativeValid ||
            !hasAtLeastOneUnit ||
            !unitNamesValid ||
            !barcodesValid
        ) {
            form.classList.add('was-validated');
            validationSummary.classList.add('show');

            document
                .querySelector('.is-invalid, :invalid')
                ?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            return;
        }

        form.dataset.submitting = '1';
        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...';

        HTMLFormElement.prototype.submit.call(form);
    });

    validateProductName();
    validateUnitNames();
    validateBarcodes();
});

async function deleteImage(id, button) {
    if (!confirm('ยืนยันการลบรูปภาพนี้หรือไม่?')) return;

    button.disabled = true;

    try {
        const response = await fetch(`{{ url('products/product/image') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'ลบรูปภาพไม่สำเร็จ');
        }

        button.closest('.image-item')?.remove();
    } catch (error) {
        alert(error.message);
        button.disabled = false;
    }
}
</script>
@endsection