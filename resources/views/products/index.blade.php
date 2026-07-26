@extends('layouts.layout')

@push('styles')
<style>
    .btn-edit-custom {
        background: linear-gradient(135deg, #ffb74d, #ff9800);
        border: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);
    }

    .btn-edit-custom:hover {
        background: linear-gradient(135deg, #ffa726, #fb8c00);
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(255, 152, 0, 0.4);
        color: white;
    }

    .btn-edit-custom i {
        transition: transform 0.3s ease;
    }

    .btn-edit-custom:hover i {
        transform: rotate(-10deg) scale(1.1);
    }
</style>
@endpush

@section('flash-messages')
    @include('layouts.partials.flash-messages', [
        'successTitle' => 'Success!',
        'errorTitle' => 'Oops...',
        'confirmButtonText' => 'Ok',
    ])
@endsection

@section('content')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-primary">
                จัดการสินค้า
            </h2>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">
                    <i class="bi bi-tags me-1"></i> เพิ่มประเภทสินค้า
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มสินค้าใหม่
                </button>
            </div>
        </div>

        {{-- Product Control Panel --}}
        <div class="card shadow-sm mb-4 border-0 rounded-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ url()->current() }}" id="filterForm">
                    <div class="row g-3 align-items-center">

                        {{-- Category Filter --}}
                        <div class="col-lg-3 col-md-4">
                            <label for="category" class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-tag-fill me-1"></i> ประเภทสินค้า
                            </label>
                            <select name="category_id" id="category" class="form-select border-2 rounded-3 shadow-none"
                                onchange="this.form.submit()">
                                <option value="">-- แสดงทั้งหมด --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Bar --}}
                        <div class="col-lg-9 col-md-6">
                            <label for="searchProduct" class="form-label small fw-bold text-muted mb-2">
                                <i class="bi bi-search me-1"></i> ค้นหาสินค้า
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2 border-end-0 rounded-start-3">
                                    <i class="bi bi-search text-primary"></i>
                                </span>
                                <input type="text" id="searchProduct" name="search" value="{{ request('search') }}"
                                    class="form-control border-2 border-start-0 bg-light shadow-none rounded-end-3"
                                    placeholder="พิมพ์ชื่อสินค้า...">
                            </div>
                        </div>



                    </div>
                </form>
            </div>
        </div>
        {{-- Product List Display --}}
        @if ($products->count())
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach ($products as $product)
                    <div class="col product-item" data-name="{{ strtolower($product->name) }}"
                        data-id="{{ strtolower($product->id) }}">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            @if ($product->images->count())
                                <div id="carouselProduct{{ $product->id }}" class="carousel slide mb-3"
                                    data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach ($product->images as $key => $image)
                                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                    class="d-block w-100" style="height:200px; object-fit:cover;">

                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($product->images->count() > 1)
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselProduct{{ $product->id }}" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselProduct{{ $product->id }}" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    @endif
                                </div>
                            @endif


                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title fw-bold text-truncate mb-1" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h5>
                                <div class="mb-2">
                                    <span
                                        class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }} rounded-pill">
                                        {{ $product->is_active ? 'ใช้งานอยู่' : 'ไม่ใช้งาน' }}
                                    </span>
                                </div>
                                <p class="card-text mb-1 text-muted small"><strong>รหัสสินค้า:</strong>
                                    {{ $product->id }}</p>
                                    <p class="card-text mb-2 text-muted small">
                                        <strong>ประเภท:</strong> {{ $product->category->name ?? 'ไม่มี' }}
                                    </p>
                                    <p class="card-text mb-2 text-muted small">
                                        <strong>ของแถม:</strong> {{ $product->description ?? 'ไม่มี' }}
                                    </p>
                                @php
                                    $totalWarehouse = 0;
                                    $totalStore = 0;

                                    foreach ($product->productUnits as $unit) {
                                        $stock = $unit->stock;
                                        $qtyPerUnit = $unit->unit_quantity;

                                        if ($stock) {
                                            $totalWarehouse += $stock->warehouse_stock ?? 0;
                                            $totalStore += $stock->store_stock ?? 0;
                                        }
                                    }
                                @endphp

                                <p class="card-text mb-0 text-muted small">
    <strong>จำนวนทั้งหมด:</strong>
</p>


<ul class="list-unstyled ps-3 mb-2 small text-muted">


@php
    $totalWarehousePieces = 0;
    $totalStorePieces = 0;
@endphp


<h6 class="fw-bold text-primary mt-2">
    📦 คลังสินค้า
</h6>

<ul class="list-unstyled ps-3 mb-2 small text-muted">

@foreach ($product->productUnits as $unit)

    @php
        $stock = $unit->stock;

        $warehouseQty = $stock 
            ? ($stock->warehouse_stock ?? 0)
            : 0;

        $unitQty = $unit->unit_quantity > 0 
            ? $unit->unit_quantity 
            : 1;

        $totalWarehousePieces += $warehouseQty * $unitQty;
    @endphp


    <li>
        {{ $unit->unit_name }} :
        <strong>{{ $warehouseQty }}</strong>
        {{ $unit->unit_name }}
    </li>

@endforeach


<li class="mt-2">
    รวมคลัง :
    <strong>{{ $totalWarehousePieces }}</strong>
    ชิ้น
</li>

</ul>



<h6 class="fw-bold text-success mt-3">
    🏪 หน้าร้าน
</h6>


<ul class="list-unstyled ps-3 mb-2 small text-muted">

@foreach ($product->productUnits as $unit)

    @php

        $stock = $unit->stock;

        $storeQty = $stock
            ? ($stock->store_stock ?? 0)
            : 0;


        $unitQty = $unit->unit_quantity > 0 
            ? $unit->unit_quantity 
            : 1;


        $totalStorePieces += $storeQty * $unitQty;

    @endphp


    <li>
        {{ $unit->unit_name }} :
        <strong>{{ $storeQty }}</strong>
        {{ $unit->unit_name }}
    </li>


@endforeach


<li class="mt-2">
    รวมหน้าร้าน :
    <strong>{{ $totalStorePieces }}</strong>
    ชิ้น
</li>

</ul>
</ul>

                            </div>
                            <div
                                class="card-footer bg-white border-0 pt-0 d-flex justify-content-between align-items-center">
                                <a href="{{ route('product.product.edit', $product->id) }}"
                                    class="btn btn-edit-custom btn-sm rounded-pill flex-grow-1 me-2 text-center">
                                    แก้ไขสินค้า
                                </a>

                                {{-- <form action="{{ route('product.product.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้า {{ $product->name }}? การดำเนินการนี้ไม่สามารถย้อนกลับได้');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill flex-grow-1">
                                        <i class="bi bi-trash me-1"></i> ลบ
                                    </button>
                                </form> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info text-center py-4 rounded-3 shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> ไม่พบข้อมูลสินค้าในหมวดหมู่นี้
            </div>
        @endif
    </div>

    {{-- Modal for Adding New Product --}}
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header bg-success text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addProductModalLabel">
                        <i class="bi bi-box-seam-fill"></i> เพิ่มสินค้าใหม่พร้อมหน่วยนับ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('product.product.storeWithUnit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            {{-- Product Information Section --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="mb-3 text-success"><i class="bi bi-info-circle me-2"></i>ข้อมูลสินค้าหลัก</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">ชื่อสินค้า <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="เช่น โค้ก 2 ลิตร" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold">ประเภทสินค้า <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id"
                                        class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- เลือกประเภทสินค้า --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>จำนวนแจ้งเตือนขั้นต่ำ</label>
                                    <input type="number" name="min_stock" class="form-control"
                                        value="{{ old('min_stock', $product->min_stock ?? 10) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="description"
                                        class="form-label fw-semibold">ของแถม/รายละเอียดเพิ่มเติม</label>
                                    <textarea name="description" id="description" class="form-control rounded-3" rows="3"
                                        placeholder="เช่น แถมแก้วน้ำ, รายละเอียดเฉพาะของสินค้า">{{ old('description') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">เลือกรูปภาพสินค้า (อัปโหลดได้หลายรูป)</label>
                                    <input type="file" class="form-control" name="images[]" id="images" multiple>
                                    <small class="text-muted">สามารถเลือกได้หลายไฟล์พร้อมกัน (กด Ctrl หรือ Shift)</small>

                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label fw-semibold">สถานะการขาย</label>
                                    <select name="is_active" id="is_active" class="form-select rounded-3">
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>เปิด
                                            (พร้อมขาย)</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>ปิด
                                            (ไม่พร้อมขาย)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Unit Information Section --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="mb-3 text-primary"><i class="bi bi-rulers me-2"></i>ข้อมูลหน่วยนับสินค้า</h6>
                                <div id="units-container">
                                    {{-- Initial Unit Input (Dynamically added/cloned) --}}
                                    <div class="unit-group border rounded-3 p-3 mb-3 bg-light">
                                        <h6 class="text-secondary small mb-3">หน่วยนับหลัก <span class="fw-normal">(เช่น
                                                ชิ้น, ขวด)</span></h6>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">ชื่อหน่วยนับ <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="units[0][unit_name]"
                                                class="form-control rounded-3" placeholder="เช่น ชิ้น, ขวด, แพ็ค"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">
                                                จำนวนหน่วย
                                            </label>

                                            <input type="number" name="units[0][unit_quantity]"
                                                class="form-control rounded-3" value="1" readonly>

                                            <small class="text-success">
                                                หน่วยแรก
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">รหัสบาร์โค้ด</label>
                                            <div class="input-group">
                                                <input type="text" name="units[0][unit_barcode]"
                                                    class="form-control rounded-start-3" placeholder="ระบุเองหรือกดสร้าง" required>
                                                <button type="button"
                                                    class="btn btn-outline-secondary generate-barcode-btn rounded-end-3"
                                                    title="สร้างบาร์โค้ดสุ่ม">
                                                    <i class="bi bi-arrow-clockwise me-1"></i> สร้าง
                                                </button>
                                            </div>
                                            <div class="barcode-preview mt-2 text-center" style="display:none;">
                                                <canvas class="barcode-canvas"></canvas>
                                                <p class="barcode-value mt-1 fw-bold text-dark"></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาขายปลีก <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][price]" step="0.01"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาขายส่ง <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][wholesale]" step="0.01"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาทุน <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][cost_price]" step="0.01"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-unit d-none rounded-pill">
                                            <i class="bi bi-trash me-1"></i> ลบหน่วยนับนี้
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-3"
                                    id="add-unit">
                                    <i class="bi bi-plus me-1"></i> เพิ่มหน่วยนับอื่น
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3 d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success rounded-pill px-5">
                                <i class="bi bi-save2-fill me-2"></i> บันทึกสินค้า
                            </button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i> ยกเลิก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for Adding Category --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addCategoryModalLabel">
                        <i class="bi bi-tags-fill"></i> เพิ่มประเภทสินค้า
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label for="category_name" class="form-label fw-semibold">ชื่อประเภทสินค้า <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="category_name"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required placeholder="เช่น ขนม, เครื่องดื่ม">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-save2-fill me-1"></i> บันทึกประเภทสินค้า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script to show addCategoryModal if there are validation errors --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
                addCategoryModal.show();
            });
        </script>
    @endif

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        let barcodeTimeout;
        let unitIndex = document.querySelectorAll('.unit-group').length;

        // Re-index unit fields
        function reIndexUnits() {
            document.querySelectorAll('#units-container .unit-group').forEach((unitGroup, index) => {
                unitGroup.querySelectorAll('[name^="units["]').forEach(input => {
                    const name = input.getAttribute('name');
                    input.setAttribute('name', name.replace(/units\[\d+\]/, `units[${index}]`));
                });
                const removeButton = unitGroup.querySelector('.remove-unit');
                if (removeButton) {
                    if (index === 0) removeButton.classList.add('d-none');
                    else removeButton.classList.remove('d-none');
                }
            });
            unitIndex = document.querySelectorAll('.unit-group').length;
        }

        // Add new unit group
        document.getElementById('add-unit').addEventListener('click', function() {
            const container = document.getElementById('units-container');
            const templateHtml = `
            <div class="unit-group border rounded-3 p-3 mb-3 bg-light">
                <h6 class="text-secondary small mb-3">หน่วยนับรอง</h6>
                <div class="mb-3">
                    <label class="form-label small text-muted">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                    <input type="text" name="units[${unitIndex}][unit_name]" class="form-control rounded-3"
                        placeholder="เช่น แพ็ค, กล่อง" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">จำนวน (เทียบกับหน่วยเล็กสุด) <span class="text-danger">*</span></label>
                    <input type="number" name="units[${unitIndex}][unit_quantity]" class="form-control rounded-3"
                         min="2" required>
                    <small class="text-muted">
กรอกจำนวนของหน่วยฐาน เช่น
แพ็ค = 6
ลัง = 24
</small>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">รหัสบาร์โค้ด</label>
                    <div class="input-group">
                        <input type="text" name="units[${unitIndex}][unit_barcode]" class="form-control rounded-start-3"
                            placeholder="ระบุเองหรือกดสร้าง" required>
                        <button type="button" class="btn btn-outline-secondary generate-barcode-btn rounded-end-3" title="สร้างบาร์โค้ดสุ่ม">
                            <i class="bi bi-arrow-clockwise me-1"></i> สร้าง
                        </button>
                    </div>
                    <div class="barcode-preview mt-2 text-center" style="display:none;">
                        <canvas class="barcode-canvas"></canvas>
                        <p class="barcode-value mt-1 fw-bold text-dark"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small text-muted">ราคาขายปลีก <span class="text-danger">*</span></label>
                        <input type="number" name="units[${unitIndex}][price]" step="0.01" class="form-control rounded-3" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small text-muted">ราคาขายส่ง <span class="text-danger">*</span></label>
                        <input type="number" name="units[${unitIndex}][wholesale]" step="0.01" class="form-control rounded-3" placeholder="0.00" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small text-muted">ราคาทุน <span class="text-danger">*</span></label>
                        <input type="number" name="units[${unitIndex}][cost_price]" step="0.01" class="form-control rounded-3" placeholder="0.00" required>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-unit rounded-pill">
                    <i class="bi bi-trash me-1"></i> ลบหน่วยนับนี้
                </button>
            </div>`;
            container.insertAdjacentHTML('beforeend', templateHtml);
            reIndexUnits();
        });

        // Generate barcode button
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains('generate-barcode-btn')) {
                const container = e.target.closest('.input-group').parentElement;
                const input = container.querySelector('input');
                const previewDiv = container.querySelector('.barcode-preview');
                const canvas = container.querySelector('.barcode-canvas');
                const valueText = container.querySelector('.barcode-value');

                const barcodeValue = Math.random().toString().slice(2, 14);
                input.value = barcodeValue;

                JsBarcode(canvas, barcodeValue, {
                    format: "CODE128",
                    height: 40,
                    displayValue: false
                });
                valueText.textContent = barcodeValue;
                previewDiv.style.display = "block";

                // trigger input event to check duplicate
                input.dispatchEvent(new Event('input'));
            }
        });

        // Remove unit group
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-unit')) {
                const unitGroup = e.target.closest('.unit-group');
                if (document.querySelectorAll('.unit-group').length > 1) {
                    unitGroup.remove();
                    reIndexUnits();
                } else {
                    alert("ต้องมีหน่วยนับอย่างน้อยหนึ่งหน่วย");
                }
            }
        });

        // 🔍 ตรวจสอบบาร์โค้ดซ้ำ (แบบเรียลไทม์)
        document.addEventListener("input", async function(e) {
            if (e.target.name && e.target.name.includes("unit_barcode")) {
                const input = e.target;
                const barcode = input.value.trim();
                if (barcode.length < 1) return;

                try {
                    const response = await fetch(
                        `{{ route('barcode.check') }}?barcode=${barcode}`);
                    const data = await response.json();

                    const parent = input.closest('.mb-3');
                    let warning = parent.querySelector('.barcode-warning');
                    if (!warning) {
                        warning = document.createElement('small');
                        warning.classList.add('barcode-warning', 'text-danger', 'd-block', 'mt-1');
                        parent.appendChild(warning);
                    }

                    if (data.exists) {
                        warning.textContent = "⚠️ บาร์โค้ดนี้มีอยู่แล้วในระบบ กรุณาใช้บาร์โค้ดอื่น";
                        input.classList.add('is-invalid');
                    } else {
                        let hasError = false;
                    }
                } catch (error) {
                    console.error("Barcode check error:", error);
                }
            }
        });

        // 🚫 ป้องกันการบันทึกถ้าพบช่องบาร์โค้ดที่ซ้ำ
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.querySelector(
                'form[action="{{ route('product.product.storeWithUnit') }}"]'
            );

            const submitBtn = form.querySelector('button[type="submit"]');

            // =========================
            // เปิด/ปิดปุ่มบันทึก
            // =========================

            function toggleSubmitButton() {

                const invalidInputs = document.querySelectorAll('.is-invalid');

                submitBtn.disabled = invalidInputs.length > 0;
            }

            // =========================
            // เช็คชื่อสินค้าซ้ำ
            // =========================

            const productNameInput = document.querySelector('input[name="name"]');

            productNameInput.addEventListener('input', async function() {

                const name = this.value.trim();

                this.classList.remove('is-invalid');

                let warning = this.parentElement.querySelector('.name-warning');

                if (!warning) {

                    warning = document.createElement('small');

                    warning.classList.add(
                        'name-warning',
                        'text-danger',
                        'd-block',
                        'mt-1'
                    );

                    this.parentElement.appendChild(warning);
                }

                warning.textContent = '';

                if (name.length < 1) {

                    toggleSubmitButton();
                    return;
                }

                try {

                    const response = await fetch(
                        `{{ route('product.name.check') }}?name=${encodeURIComponent(name)}`
                    );

                    const data = await response.json();

                    if (data.exists) {

                        this.classList.add('is-invalid');

                        warning.textContent =
                            '⚠️ ชื่อสินค้านี้มีอยู่แล้วในระบบ';
                    }

                } catch (error) {

                    console.error(error);
                }

                toggleSubmitButton();
            });

            // =========================
            // เช็คบาร์โค้ดซ้ำ
            // =========================

            document.addEventListener('input', function(e) {

                if (
                    e.target.name &&
                    e.target.name.includes('unit_barcode')
                ) {

                    clearTimeout(barcodeTimeout);

                    const input = e.target;

                    barcodeTimeout = setTimeout(async () => {

                        const barcode = input.value.trim();

                        const parent = input.closest('.mb-3');

                        let warning = parent.querySelector('.barcode-warning');

                        if (!warning) {

                            warning = document.createElement('small');

                            warning.classList.add(
                                'barcode-warning',
                                'text-danger',
                                'd-block',
                                'mt-1'
                            );

                            parent.appendChild(warning);
                        }

                        let hasError = false;

                        // reset
                        input.classList.remove('is-invalid');

                        warning.textContent = '';

                        // =====================
                        // เช็คซ้ำในฟอร์ม
                        // =====================

                        const allInputs = document.querySelectorAll(
                            'input[name*="[unit_barcode]"]'
                        );

                        allInputs.forEach(item => {

                            if (
                                item !== input &&
                                item.value.trim() === barcode &&
                                barcode !== ''
                            ) {

                                hasError = true;
                            }
                        });

                        if (hasError) {

                            input.classList.add('is-invalid');

                            warning.textContent =
                                '⚠️ พบบาร์โค้ดซ้ำภายในฟอร์ม';

                            toggleSubmitButton();

                            return;
                        }

                        // =====================
                        // เช็ค DB
                        // =====================

                        if (barcode.length > 0) {

                            try {

                                const response = await fetch(
                                    `{{ route('barcode.check') }}?barcode=${barcode}`
                                );

                                const data = await response.json();

                                if (data.exists) {

                                    hasError = true;

                                    input.classList.add('is-invalid');

                                    warning.textContent =
                                        '⚠️ บาร์โค้ดนี้มีอยู่แล้วในระบบ';
                                }

                            } catch (error) {

                                console.error(error);
                            }
                        }

                        toggleSubmitButton();

                    }, 400); // รอหยุดพิมพ์ 400ms
                }
            });

            // =========================
            // กัน submit
            // =========================

            form.addEventListener('submit', function(e) {

                const invalidInputs = document.querySelectorAll('.is-invalid');

                if (invalidInputs.length > 0) {

                    e.preventDefault();

                    alert('กรุณาแก้ไขข้อมูลที่ซ้ำก่อนบันทึก');

                    return false;
                }
            });

        });
        // =========================
        // ค้นหาสินค้าแบบ realtime
        // =========================

        document.getElementById('searchProduct').addEventListener('input', function() {

            const keyword = this.value.toLowerCase().trim();

            const products = document.querySelectorAll('.product-item');

            products.forEach(product => {

                const name = product.dataset.name || '';
                const id = product.dataset.id || '';

                if (
                    name.includes(keyword) ||
                    id.includes(keyword)
                ) {
                    product.style.display = '';
                } else {
                    product.style.display = 'none';
                }

            });

        });
    </script>
@endpush
