@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2>เพิ่มสินค้าใหม่พร้อมหน่วยนับ</h2>

    <form action="{{ route('product.product.storeWithUnit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- ฝั่งข้อมูลสินค้า -->
            <div class="col-md-6">
                <h4>ข้อมูลสินค้า</h4>
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">ประเภทสินค้า</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">-- เลือกประเภทสินค้า --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="barcode" class="form-label">บาร์โค้ดหลัก</label>
                    <input type="text" name="barcode" id="barcode" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" name="sku" id="sku" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">ของแถม</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">รูปภาพ</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>
            </div>

            <!-- ฝั่งข้อมูลหน่วยนับ -->
            <div class="col-md-6">
                <h4>ข้อมูลหน่วยนับ</h4>
                <div id="units-container">
                    <!-- Template หน่วยนับ -->
                    <div class="unit-group border rounded p-3 mb-3">
                        <div class="mb-3">
                            <label class="form-label">ชื่อหน่วยนับ</label>
                            <input type="text" name="units[0][unit_name]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">จำนวนหน่วย</label>
                            <input type="number" name="units[0][unit_quantity]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บาร์โค้ดหน่วยนับ</label>
                            <input type="text" name="units[0][unit_barcode]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาขายปีก</label>
                            <input type="number" name="units[0][price]" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาขายส่ง</label>
                            <input type="number" name="units[0][wholesale]" step="0.01" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ราคาทุน</label>
                            <input type="number" name="units[0][cost_price]" step="0.01" class="form-control">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-unit d-none">ลบชุดนี้</button>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-unit">+ เพิ่มหน่วยนับ</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
        <a href="{{ route('product.product.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>

<script>
   let unitIndex = 1;

document.getElementById('add-unit').addEventListener('click', function () {
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

// ปุ่มลบชุด
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-unit')) {
        e.target.closest('.unit-group').remove();
    }
});
</script>
@endsection
