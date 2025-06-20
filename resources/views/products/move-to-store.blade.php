@extends('layouts.layout')

@section('content')
<div class="container">
    <h4>ย้ายสินค้าไปหน้าร้าน</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('product.stock.to.frontstore') }}" method="POST">
        @csrf

        <div id="product-rows">
            <!-- จะเพิ่มรายการสินค้าแบบหลายบรรทัดตรงนี้ -->
        </div>

        <button type="button" id="add-row" class="btn btn-secondary mb-3">+ เพิ่มรายการสินค้า</button>
        <br>
        <button type="submit" class="btn btn-primary">ย้ายสินค้า</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productUnits = @json($products->pluck('productUnits', 'id'));
        const products = @json($products);

        const productRowsContainer = document.getElementById('product-rows');
        const addRowButton = document.getElementById('add-row');

        function createRow(index) {
            const row = document.createElement('div');
            row.className = 'row mb-3 product-row';
            row.innerHTML = `  
                <div class="col-md-4">
                    <label class="form-label">สินค้า</label>
                    <select name="product_id[]" class="form-select product-select" required>
                        <option value="">-- เลือกสินค้า --</option>
                        ${products.map(product => `<option value="${product.id}">${product.name}</option>`).join('')}
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">หน่วยสินค้า</label>
                    <select name="unit_id[]" class="form-select unit-select" required>
                        <option value="">-- เลือกหน่วยสินค้า --</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">จำนวน</label>
                    <input type="number" name="quantity[]" class="form-control quantity-input" min="1" required>
                    <small class="text-muted available-info"></small>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-row">ลบ</button>
                </div>
            `;
            productRowsContainer.appendChild(row);

            const productSelect = row.querySelector('.product-select');
            const unitSelect = row.querySelector('.unit-select');
            const quantityInput = row.querySelector('.quantity-input');
            const availableInfo = row.querySelector('.available-info');

            productSelect.addEventListener('change', function () {
                const units = productUnits[this.value] || [];
                unitSelect.innerHTML = '<option value="">-- เลือกหน่วยสินค้า --</option>';
                availableInfo.textContent = '';
                quantityInput.value = '';

                units.forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.text = `${unit.unit_name} (${unit.unit_quantity})`;
                    option.dataset.stock = unit.warehouse_stock || 0;
                    option.dataset.unitQuantity = unit.unit_quantity || 1;
                    unitSelect.appendChild(option);
                });
            });

            unitSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const stock = parseInt(selected?.dataset?.stock || 0);
                const unitQty = parseInt(selected?.dataset?.unitQuantity || 1);
                quantityInput.value = '';
                availableInfo.textContent = stock && unitQty
                    ? `${stock} หน่วยย่อย (สูงสุด ${Math.floor(stock / unitQty)} หน่วย ${selected.text.split(' ')[0]})`
                    : 'ข้อมูลไม่พร้อมใช้งาน';
            });

            quantityInput.addEventListener('input', function () {
                const selected = unitSelect.options[unitSelect.selectedIndex];
                const stock = parseInt(selected?.dataset?.stock || 0);
                const unitQty = parseInt(selected?.dataset?.unitQuantity || 1);
                const inputQty = parseInt(this.value || 0);

                if (inputQty * unitQty > stock) {
                    alert(`จำนวนเกินจากคงเหลือ`);
                    this.value = '';
                }
            });

            // ปุ่มลบแถว
            row.querySelector('.btn-remove-row').addEventListener('click', () => {
                row.remove();
            });
        }

        // สร้างแถวเริ่มต้น 1 แถว
        createRow(0);

        addRowButton.addEventListener('click', () => {
            createRow(document.querySelectorAll('.product-row').length);
        });
    });
</script>

@endsection
