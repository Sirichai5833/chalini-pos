@extends('layouts.layout')

@section('content')

    @if (session('success'))
        <x-sweet-alert icon="success" title="Oh Yeah!" text="{{ session('success') }}" confirm-button-text="Ok" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="Oops..." text="{{ session('error') }}" confirm-button-text="Ok" />
    @endif

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Products</h2>
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                + เพิ่มประเภทสินค้า
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                + เพิ่มสินค้าใหม่
            </button>
        </div>
    </div>


    <!-- ฟอร์มเลือกประเภทสินค้า -->
    <form method="GET" action="#" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="category" class="form-label">เลือกประเภทสินค้า</label>
                <select name="category_id" id="category" class="form-select">
                    <option value="">เลือกทั้งหมด</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">กรอง</button>
            </div>
        </div>
    </form>

    @if ($products->count())
        <div class="row">
            @foreach ($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card shadow-sm border-0 h-100 rounded-4">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top rounded-top-4"
                                alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex justify-content-center align-items-center rounded-top-4"
                                style="height: 150px;">
                                <small class="text-muted">ไม่มีรูปภาพ</small>
                            </div>
                        @endif
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-1">{{ Str::limit($product->name, 20) }}</h6>
                            <div class="mb-2">
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                </span>
                            </div>
                            <p class="mb-1 text-muted"><strong>รหัส:</strong> {{ $product->id }}</p>
                            {{-- <p class="mb-1 text-muted">
                                    <strong>ราคาขายปลีก:</strong> {{ number_format($product->defaultUnit->price ?? 0, 2) }}
                                    บาท
                                </p>
                                <p class="mb-1 text-muted">
                                    <strong>ราคาขายส่ง:</strong>
                                    {{ number_format($product->defaultUnit->wholesale ?? 0, 2) }} บาท
                                </p> --}}
                            <p class="mb-1 text-muted">
                                <strong>จำนวนขายหน้าร้าน:</strong> {{ $product->stock->store_stock ?? 0 }}
                            </p>
                            <p class="mb-1 text-muted">
                                <strong>จำนวนในสต็อก:</strong> {{ $product->stock->warehouse_stock ?? 0 }}
                            </p>
                            <p class="mb-1 text-muted">
                                <strong>ของแถม:</strong> {{ $product->description ?? 0 }}
                            </p>

                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <a href="{{ route('product.product.edit', $product->id) }}"
                                class="btn btn-outline-warning btn-sm">แก้ไข</a>
                            <form action="{{ route('product.product.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าจะลบ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">ลบ</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">ไม่มีข้อมูลสินค้า</div>
    @endif
    </div>
    <!-- Modal สำหรับเพิ่มสินค้า -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-4">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มสินค้าใหม่พร้อมหน่วยนับ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('product.product.storeWithUnit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- ฝั่งข้อมูลสินค้า -->
                            <div class="col-md-6">
                                <h6>ข้อมูลสินค้า</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label">ชื่อสินค้า</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">ประเภทสินค้า</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">-- เลือกประเภทสินค้า --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="barcode" class="form-label">บาร์โค้ดหลัก</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" name="sku" id="sku" class="form-control">
                                </div> --}}

                                <div class="mb-3">
                                    <label for="description" class="form-label">ของแถม</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">รูปภาพ</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label">สถานะการขาย</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" selected>เปิด</option>
                                        <option value="0">ปิด</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ฝั่งข้อมูลหน่วยนับ -->
                            <div class="col-md-6">
                                <h6>ข้อมูลหน่วยนับ</h6>
                                <div id="units-container">
                                    <!-- Template หน่วยนับแรก -->
                                    <div class="unit-group border rounded p-3 mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">ชื่อหน่วยนับ</label>
                                            <input type="text" name="units[0][unit_name]" class="form-control"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">จำนวนหน่วย</label>
                                            <input type="number" name="units[0][unit_quantity]" class="form-control"
                                                required>
                                        </div>
                                        <!-- เพิ่ม canvas ลงไปต่อจาก input -->
                                        <div class="mb-3">
                                            <label for="barcode2" class="form-label">รหัสบาร์โค้ด</label>
                                            <div class="input-group">
                                                <input type="text" name="units[0][unit_barcode]" id="barcode2"
                                                    class="form-control" placeholder="ระบุหรือกดสร้าง" required>
                                                <button type="button"
                                                    class="btn btn-outline-success generate-barcode-btn">
                                                    🔄 สร้าง
                                                </button>
                                            </div>
                                            <div class="barcode-preview mt-2 text-center" style="display:none;">
                                                <canvas class="barcode-canvas"></canvas>
                                                <p class="barcode-value mt-1"></p>
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <label class="form-label">ราคาขายปลีก</label>
                                            <input type="number" name="units[0][price]" step="0.01"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">ราคาขายส่ง</label>
                                            <input type="number" name="units[0][wholesale]" step="0.01"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">ราคาทุน</label>
                                            <input type="number" name="units[0][cost_price]" step="0.01"
                                                class="form-control">
                                        </div>
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-unit d-none">ลบชุดนี้</button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary mb-3" id="add-unit">+
                                    เพิ่มหน่วยนับ</button>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal: เพิ่มประเภทสินค้า -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-gradient bg-primary text-white rounded-top-4 px-4 py-3">
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
                            <label for="name" class="form-label fw-semibold">ชื่อประเภทสินค้า <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required placeholder="เช่น ขนม, เครื่องดื่ม">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">รายละเอียด</label>
                            <textarea name="description" id="description" class="form-control rounded-3" rows="3"
                                placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
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
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            addCategoryModal.show();
        });
    </script>
@endif



    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <!-- JavaScript -->
    @push('scripts')
        <script>
            let unitIndex = 1;

            document.getElementById('add-unit').addEventListener('click', function() {
                const container = document.getElementById('units-container');
                const html = `
        <div class="unit-group border rounded p-3 mb-3">
            <div class="mb-3">
                <label class="form-label">ชื่อหน่วยนับ</label>
                <input type="text" name="units[${unitIndex}][unit_name]" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">จำนวนหน่วย</label>
                <input type="number" name="units[${unitIndex}][unit_quantity]" class="form-control" required>
            </div>
            <div class="mb-3">
    <label class="form-label">บาร์โค้ดหน่วยนับ</label>
    <div class="input-group">
        <input type="text" name="units[${unitIndex}][unit_barcode]" class="form-control" placeholder="ระบุหรือกดสร้าง" required>
        <button type="button" class="btn btn-outline-success generate-barcode-btn">
            🔄 สร้าง
        </button>
    </div>
    <div class="barcode-preview mt-2 text-center" style="display:none;">
        <canvas class="barcode-canvas"></canvas>
        <p class="barcode-value mt-1"></p>
    </div>
</div>

            <div class="mb-3">
                <label class="form-label">ราคาขายปลีก</label>
                <input type="number" name="units[${unitIndex}][price]" step="0.01" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ราคาขายส่ง</label>
                <input type="number" name="units[${unitIndex}][wholesale]" step="0.01" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ราคาทุน</label>
                <input type="number" name="units[${unitIndex}][cost_price]" step="0.01" class="form-control">
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-unit">ลบชุดนี้</button>
        </div>`;
                container.insertAdjacentHTML('beforeend', html);
                unitIndex++;
            });

            document.addEventListener("click", function(e) {
                if (e.target.classList.contains('generate-barcode-btn')) {
                    const container = e.target.closest('.input-group').parentElement;
                    const input = container.querySelector('input');
                    const previewDiv = container.querySelector('.barcode-preview');
                    const canvas = container.querySelector('.barcode-canvas');
                    const valueText = container.querySelector('.barcode-value');

                    const barcodeValue = Math.random().toString().slice(2, 14); // ตัวอย่างการสุ่มบาร์โค้ด
                    input.value = barcodeValue;

                    JsBarcode(canvas, barcodeValue, {
                        format: "CODE128"
                    });
                    valueText.textContent = barcodeValue;
                    previewDiv.style.display = "block";
                }
            });
        </script>
    @endpush
    </div>
    </div>


@endsection
