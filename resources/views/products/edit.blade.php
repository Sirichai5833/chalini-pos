@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h2>แก้ไขสินค้าและหน่วยนับ</h2>

        <form action="{{ route('product.updateWithUnit', $product->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <!-- ฝั่งข้อมูลสินค้า -->
                <div class="col-md-6">
                    <h4>ข้อมูลสินค้า</h4>
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อสินค้า</label>
                        <input type="text" name="name" id="name" value="{{ $product->name }}" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">ประเภทสินค้า</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">-- เลือกประเภทสินค้า --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="mb-3">
                        <label for="barcode" class="form-label">บาร์โค้ดหลัก</label>
                        <input type="text" name="barcode" id="barcode" value="{{ $product->barcode }}"
                            class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" name="sku" id="sku" value="{{ $product->sku }}"
                            class="form-control">
                    </div> --}}

                    <div class="mb-3">
                        <label for="description" class="form-label">ของแถม</label>
                        <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">รูปภาพ (อัพโหลดใหม่เพื่อเปลี่ยน)</label>
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
                    <h4>ข้อมูลหน่วยนับ</h4>
                    <div id="units-container">
                        @foreach ($product->productUnits as $index => $unit)
                            <div class="unit-group border rounded p-3 mb-3">
                                <input type="hidden" name="units[{{ $index }}][id]" value="{{ $unit->id }}">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อหน่วยนับ</label>
                                    <input type="text" name="units[{{ $index }}][unit_name]"
                                        value="{{ $unit->unit_name }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">จำนวนหน่วย</label>
                                    <input type="number" name="units[{{ $index }}][unit_quantity]"
                                        value="{{ $unit->unit_quantity }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">บาร์โค้ดหน่วยนับ</label>
                                    <input type="text" name="units[{{ $index }}][unit_barcode]"
                                        value="{{ $unit->barcode }}" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ราคาขายปลีก</label>
                                    <input type="number" name="units[{{ $index }}][price]"
                                        value="{{ $unit->price }}" step="0.01" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ราคาขายส่ง</label>
                                    <input type="number" name="units[{{ $index }}][wholesale]"
                                        value="{{ $unit->wholesale }}" step="0.01" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ราคาทุน</label>
                                    <input type="number" name="units[{{ $index }}][cost_price]"
                                        value="{{ $unit->cost_price }}" step="0.01" class="form-control">
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-unit">ลบชุดนี้</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" id="add-unit">+ เพิ่มหน่วยนับ</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            <a href="{{ route('product.product.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </form>
    </div>

    <script>
        let unitIndex = {{ $product->productUnits->count() }};

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
            <input type="text" name="units[${unitIndex}][unit_barcode]" class="form-control" required>
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

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-unit')) {
                e.target.closest('.unit-group').remove();
            }
        });
    </script>
@endsection
