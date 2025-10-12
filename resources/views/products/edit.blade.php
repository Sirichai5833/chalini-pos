@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">แก้ไขข้อมูลสินค้าและหน่วยนับ</h2>
        <hr>

        <form action="{{ route('product.updateWithUnit', $product->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <h4 class="mb-3 text-primary">ข้อมูลสินค้า</h4>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่อสินค้า <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $product->name }}"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">ประเภทสินค้า</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">-- เลือกประเภทสินค้า --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด / ของแถม</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">รูปภาพสินค้า</label>
                                <input type="file" name="images[]" id="images" class="form-control" multiple>
                                @if ($product->images->count())
                                    <small class="form-text text-muted">รูปภาพปัจจุบัน:</small>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach ($product->images as $image)
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                    class="img-thumbnail" width="100">
                                                <a href="{{ route('product.image.delete', $image->id) }}"
                                                    class="position-absolute top-0 end-0 btn btn-sm btn-danger"
                                                    onclick="return confirm('ลบรูปนี้?')">×</a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>


                            <div class="mb-3">
                                <label for="is_active" class="form-label">สถานะการขาย</label>
                                <select name="is_active" id="is_active" class="form-select">
                                    <option value="1" {{ $product->is_active == 1 ? 'selected' : '' }}>เปิดใช้งาน
                                    </option>
                                    <option value="0" {{ $product->is_active == 0 ? 'selected' : '' }}>ปิดใช้งาน
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4 class="mb-3 text-primary">ข้อมูลหน่วยนับ</h4>
                    <div id="units-container">
                        @forelse ($product->productUnits as $index => $unit)
                            <div class="unit-group card mb-3 shadow-sm border-info">
                                <div class="card-body">
                                    <h5 class="card-title text-info">หน่วยนับ #{{ $index + 1 }}</h5>
                                    <input type="hidden" name="units[{{ $index }}][id]"
                                        value="{{ $unit->id }}">

                                    <div class="mb-3">
                                        <label class="form-label">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                                        <input type="text" name="units[{{ $index }}][unit_name]"
                                            value="{{ $unit->unit_name }}" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">จำนวนหน่วย <span class="text-danger">*</span></label>
                                        <input type="number" name="units[{{ $index }}][unit_quantity]"
                                            value="{{ $unit->unit_quantity }}" class="form-control" required
                                            min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">บาร์โค้ดหน่วยนับ <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="units[{{ $index }}][unit_barcode]"
                                            value="{{ $unit->barcode }}" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ราคาขายปลีก <span class="text-danger">*</span></label>
                                        <input type="number" name="units[{{ $index }}][price]"
                                            value="{{ $unit->price }}" step="0.01" class="form-control" required
                                            min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ราคาขายส่ง <span class="text-danger">*</span></label>
                                        <input type="number" name="units[{{ $index }}][wholesale]"
                                            value="{{ $unit->wholesale }}" step="0.01" class="form-control" required
                                            min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ราคาทุน</label>
                                        <input type="number" name="units[{{ $index }}][cost_price]"
                                            value="{{ $unit->cost_price }}" step="0.01" class="form-control"
                                            min="0">
                                    </div>

                                    @php
                                        $hasSale = \App\Models\SaleItem::where('product_unit_id', $unit->id)->exists();
                                        $hasStock = \App\Models\ProductStocks::where('unit_id', $unit->id)
                                            ->where('store_stock', '>', 0)
                                            ->exists();
                                    @endphp

                                    @if (!$hasSale && !$hasStock)
                                        <button type="button" class="btn btn-danger btn-sm remove-unit">
                                            <i class="fas fa-trash-alt me-1"></i> ลบหน่วยนับนี้
                                        </button>
                                    @else
                                        <p class="text-muted small mt-2">
                                            <i class="fas fa-info-circle me-1"></i> ไม่สามารถลบหน่วยนับนี้ได้
                                            เนื่องจากมีการใช้งานในการขายหรือมีสต็อกสินค้าอยู่
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">ยังไม่มีหน่วยนับสำหรับสินค้านี้ กรุณาเพิ่มหน่วยนับอย่างน้อยหนึ่งรายการ
                            </p>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-outline-success mb-4" id="add-unit">
                        <i class="fas fa-plus-circle me-1"></i> เพิ่มหน่วยนับ
                    </button>
                </div>
            </div>

            <hr class="mt-4 mb-4">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
                </button>
                <a href="{{ route('product.product.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times-circle me-1"></i> ยกเลิก
                </a>
            </div>
        </form>
    </div>

    <script>
        let unitIndex = {{ $product->productUnits->count() }}; // Initialize with the count of existing units

        document.getElementById('add-unit').addEventListener('click', function() {
            const container = document.getElementById('units-container');

            const html = `
                <div class="unit-group card mb-3 shadow-sm border-success">
                    <div class="card-body">
                        <h5 class="card-title text-success">หน่วยนับใหม่</h5>
                        <div class="mb-3">
                            <label class="form-label">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                            <input type="text" name="units[${unitIndex}][unit_name]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">จำนวนหน่วย <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][unit_quantity]" class="form-control" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บาร์โค้ดหน่วยนับ <span class="text-danger">*</span></label>
                            <input type="text" name="units[${unitIndex}][unit_barcode]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาขายปลีก <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][price]" step="0.01" class="form-control" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาขายส่ง <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][wholesale]" step="0.01" class="form-control" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาทุน</label>
                            <input type="number" name="units[${unitIndex}][cost_price]" step="0.01" class="form-control" min="0">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-unit">
                            <i class="fas fa-trash-alt me-1"></i> ลบหน่วยนับนี้
                        </button>
                    </div>
                </div>`;

            container.insertAdjacentHTML('beforeend', html);
            unitIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-unit')) {
                e.target.closest('.unit-group').remove();
            }
        });
    </script>
@endsection
