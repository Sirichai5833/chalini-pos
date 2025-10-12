@extends('layouts.layout')

@section('content')

<div class="container py-4">
    <h4 class="mb-4 text-primary fw-bold text-center">
        <i class="fas fa-warehouse me-2"></i> ย้ายสินค้าจากคลังไปหน้าร้าน
    </h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> มีข้อผิดพลาด!</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-lg border-0 mb-4">
        <div class="card-header bg-light border-bottom py-3">
            <h5 class="mb-0 text-dark"><i class="fas fa-boxes me-2"></i> รายการสินค้าที่ต้องการย้าย</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('product.stock.to.frontstore') }}" method="POST">
                @csrf

                <div id="product-rows">
                    {{-- แถวรายการสินค้าจะถูกเพิ่มด้วย JS --}}
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="button" id="add-row" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> เพิ่มรายการสินค้า
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-right-arrow-left me-2"></i> ย้ายสินค้า
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@php
    $unitBarcodeMap = collect();

    foreach ($products as $product) {
        foreach ($product->productUnits as $unit) {
            if ($unit->barcode) {
                $unitBarcodeMap->put($unit->barcode, [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->unit_name,
                    'unit_quantity' => $unit->unit_quantity,
                    'warehouse_stock' => $unit->stock->warehouse_stock ?? 0,
                ]);
            }
        }
    }
@endphp

<script>
    const unitBarcodeMap = @json($unitBarcodeMap);

    document.addEventListener('DOMContentLoaded', function () {
        const productRowsContainer = document.getElementById('product-rows');
        const addRowButton = document.getElementById('add-row');

        let rowCounter = 0;

        function createRow() {
            rowCounter++;
            const row = document.createElement('div');
            row.className = 'row mb-3 product-row align-items-end border-bottom pb-3';
            row.id = `product-row-${rowCounter}`;

            row.innerHTML = `
                <div class="col-md-4 mb-2 mb-md-0">
                    <label for="barcode-input-${rowCounter}" class="form-label fw-bold">สแกนบาร์โค้ดสินค้า:</label>
                    <input type="text" class="form-control barcode-input" id="barcode-input-${rowCounter}" placeholder="สแกนหรือกรอกรหัสบาร์โค้ด" required autocomplete="off">
                    <input type="hidden" name="product_id[]" class="product-id">
                    <div class="small text-primary mt-1 product-name fw-semibold"></div>
                    <div class="invalid-feedback barcode-feedback"></div>
                </div>

                <div class="col-md-3 mb-2 mb-md-0">
                    <label class="form-label fw-bold">หน่วยสินค้า:</label>
                    <input type="hidden" name="unit_id[]" class="unit-id">
                    <div class="small text-secondary mt-1 unit-name-display"></div>
                </div>

                <div class="col-md-3 mb-2 mb-md-0">
                    <label for="quantity-input-${rowCounter}" class="form-label fw-bold">จำนวน:</label>
                    <input type="number" name="quantity[]" class="form-control quantity-input" id="quantity-input-${rowCounter}" min="1" required>
                    <small class="text-info mt-1 available-info fw-semibold"></small>
                    <div class="invalid-feedback quantity-feedback"></div>
                </div>

                <div class="col-md-2 d-flex justify-content-center align-items-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-row">
                        <i class="fas fa-trash-alt"></i> ลบ
                    </button>
                </div>
            `;

            productRowsContainer.appendChild(row);

            const barcodeInput = row.querySelector('.barcode-input');
            const productIdInput = row.querySelector('.product-id');
            const productName = row.querySelector('.product-name');
            const unitIdInput = row.querySelector('.unit-id');
            const unitNameDisplay = row.querySelector('.unit-name-display');
            const quantityInput = row.querySelector('.quantity-input');
            const availableInfo = row.querySelector('.available-info');
            const barcodeFeedback = row.querySelector('.barcode-feedback');
            const quantityFeedback = row.querySelector('.quantity-feedback');

            barcodeInput.focus();

            barcodeInput.addEventListener('change', function () {
                const barcode = barcodeInput.value.trim();
                const unit = unitBarcodeMap[barcode];

                barcodeInput.classList.remove('is-invalid');
                barcodeFeedback.textContent = '';
                quantityInput.classList.remove('is-invalid');
                quantityFeedback.textContent = '';

                if (!unit) {
                    barcodeInput.classList.add('is-invalid');
                    barcodeFeedback.textContent = 'ไม่พบสินค้านี้ในระบบ';
                    productIdInput.value = '';
                    productName.textContent = '';
                    unitIdInput.value = '';
                    unitNameDisplay.textContent = '';
                    availableInfo.textContent = '';
                    quantityInput.value = '';
                    quantityInput.removeAttribute('max');
                    return;
                }

                productIdInput.value = unit.product_id;
                productName.textContent = `${unit.product_name}`;
                unitIdInput.value = unit.unit_id;
                unitNameDisplay.textContent = `${unit.unit_name}`;
                availableInfo.textContent = `คงเหลือในคลัง: ${unit.warehouse_stock} ${unit.unit_name}`;
                quantityInput.value = '';
                quantityInput.removeAttribute('max');
                quantityInput.focus();

                const allRows = document.querySelectorAll('.product-row');
                const lastRow = allRows[allRows.length - 1];
                if (row.id === lastRow.id) {
                    createRow();
                }
            });

           quantityInput.addEventListener('input', function () {
    const barcode = barcodeInput.value.trim();
    const inputQty = parseInt(this.value || 0);
    const unit = unitBarcodeMap[barcode];

    quantityInput.classList.remove('is-invalid');
    quantityFeedback.textContent = '';

    if (!unit) {
        quantityInput.classList.add('is-invalid');
        quantityFeedback.textContent = 'กรุณาสแกนบาร์โค้ดก่อนระบุจำนวน';
        this.value = '';
        return;
    }

    if (inputQty > unit.warehouse_stock) {
        const confirmBreak = confirm(
            `⚠️ ต้องการ ${inputQty} ${unit.unit_name} แต่คงเหลือในคลังเพียง ${unit.warehouse_stock} หน่วย\n` +
            `คุณต้องการ "แตกหน่วยใหญ่" เพื่อดำเนินการต่อหรือไม่?`
        );

        if (!confirmBreak) {
            this.value = '';
            return;
        }

       const maxConvertible = available * unit.unit_quantity;
        if (inputQty > maxConvertible) {
            quantityInput.classList.add('is-invalid');
            quantityFeedback.textContent = `แม้จะแตกหน่วยแล้วก็ยังไม่พอ (${maxConvertible} หน่วยสูงสุดที่เป็นไปได้)`;
            this.value = '';
            return;
        }
    }
});


            row.querySelector('.btn-remove-row').addEventListener('click', () => {
                row.remove();
            });
        }

        createRow();
        addRowButton.addEventListener('click', () => {
            createRow();
        });
    });
    
</script>

@endsection
