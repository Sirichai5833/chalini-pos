@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 stock-transfer-page">
    <section class="transfer-hero">
        <div class="transfer-hero-content">
            <div class="transfer-hero-icon">
                <i class="fas fa-warehouse"></i>
            </div>

            <div class="flex-grow-1">
                <div class="transfer-kicker">STOCK TRANSFER</div>
                <h2 class="transfer-title">ย้ายสินค้าจากคลังไปหน้าร้าน</h2>
                <p class="transfer-subtitle">
                    สแกนบาร์โค้ด ระบุจำนวน และตรวจสอบสต็อกก่อนย้ายสินค้า
                </p>
            </div>

            <div class="transfer-status">
    <i class="fas fa-arrow-right-arrow-left"></i>
    คลัง → หน้าร้าน
</div>

<div class="transfer-status">
    <i class="fas fa-user-circle"></i>
    ผู้ทำรายการ: {{ auth()->user()->name ?? 'ไม่พบข้อมูล' }}
</div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert transfer-alert transfer-alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-check me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert transfer-alert transfer-alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading mb-2">
                <i class="fas fa-triangle-exclamation me-2"></i>
                มีข้อผิดพลาด
            </h5>

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    <div class="transfer-card">
        <div class="transfer-card-header">
            <div>
                <div class="transfer-card-kicker">TRANSFER ITEMS</div>
                <h4 class="transfer-card-title">
                    <i class="fas fa-boxes-stacked me-2"></i>
                    รายการสินค้าที่ต้องการย้าย
                </h4>
            </div>

            <div class="transfer-card-badge">
                <i class="fas fa-barcode me-1"></i>
                รองรับการสแกนบาร์โค้ด
            </div>
        </div>

        <div class="transfer-card-body">
            <form
                id="stockTransferForm"
                action="{{ route('product.stock.to.frontstore') }}"
                method="POST"
                novalidate
            >
                @csrf

                <div id="product-rows"></div>

                <div class="transfer-actions">
                    <button
                        type="button"
                        id="add-row"
                        class="btn btn-add-row"
                    >
                        <i class="fas fa-circle-plus me-2"></i>
                        เพิ่มรายการสินค้า
                    </button>

                    <button
                        type="submit"
                        id="submitTransferBtn"
                        class="btn btn-transfer-submit"
                    >
                        <i class="fas fa-arrow-right-arrow-left me-2"></i>
                        ย้ายสินค้า
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
>

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

<style>
    :root {
        --transfer-yellow: #ffd43b;
        --transfer-yellow-soft: #ffe978;
        --transfer-black: #090909;
        --transfer-panel: #151515;
        --transfer-panel-soft: #1d1d1d;
        --transfer-border: rgba(255, 212, 59, .22);
        --transfer-text: #f7f7f7;
        --transfer-muted: #a8a8a8;
        --transfer-danger: #ef4444;
        --transfer-success: #22c55e;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--transfer-black);
    }

    .stock-transfer-page {
        max-width: 1450px;
        color: var(--transfer-text);
    }

    .transfer-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid var(--transfer-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 25%),
            linear-gradient(135deg, #0a0a0a 0%, #181818 68%, #2b2300 100%);
        box-shadow: 0 20px 48px rgba(0,0,0,.38);
    }

    .transfer-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .transfer-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .transfer-hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--transfer-yellow), var(--transfer-yellow-soft));
        color: #111;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
    }

    .transfer-kicker,
    .transfer-card-kicker {
        color: var(--transfer-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .16em;
    }

    .transfer-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 2rem);
        font-weight: 950;
    }

    .transfer-subtitle {
        margin: .35rem 0 0;
        color: #c7c7c7;
        font-size: .9rem;
    }

    .transfer-status {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: none;
        padding: .65rem .9rem;
        border: 1px solid rgba(255, 212, 59, .32);
        border-radius: 999px;
        background: rgba(255, 212, 59, .09);
        color: var(--transfer-yellow);
        font-size: .78rem;
        font-weight: 900;
    }

    .transfer-alert {
        border-radius: 16px;
        color: #fff;
    }

    .transfer-alert-success {
        border: 1px solid rgba(34, 197, 94, .35);
        background: rgba(34, 197, 94, .12);
    }

    .transfer-alert-danger {
        border: 1px solid rgba(239, 68, 68, .35);
        background: rgba(239, 68, 68, .12);
    }

    .transfer-card {
        overflow: hidden;
        border: 1px solid #2e2e2e;
        border-radius: 22px;
        background: linear-gradient(180deg, #181818, #101010);
        box-shadow: 0 18px 44px rgba(0,0,0,.32);
    }

    .transfer-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--transfer-border);
        background: linear-gradient(135deg, #202020, #111);
    }

    .transfer-card-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .transfer-card-badge {
        padding: .5rem .75rem;
        border: 1px solid rgba(255, 212, 59, .25);
        border-radius: 999px;
        background: rgba(255, 212, 59, .07);
        color: var(--transfer-yellow);
        font-size: .74rem;
        font-weight: 850;
    }

    .transfer-card-body {
        padding: 1.25rem;
    }

    .product-row {
        position: relative;
        margin-bottom: 1rem !important;
        padding: 1rem;
        border: 1px solid #303030;
        border-radius: 18px;
        background: linear-gradient(180deg, #171717, #101010);
        transition: .2s ease;
    }

    .product-row:hover {
        border-color: rgba(255, 212, 59, .38);
        transform: translateY(-1px);
    }

    .product-row::before {
        content: attr(data-row-number);
        position: absolute;
        top: -10px;
        left: 16px;
        display: grid;
        place-items: center;
        min-width: 28px;
        height: 28px;
        padding: 0 .4rem;
        border-radius: 999px;
        background: var(--transfer-yellow);
        color: #111;
        font-size: .72rem;
        font-weight: 950;
    }

    .stock-transfer-page .form-label {
        color: #ddd;
        font-size: .78rem;
        font-weight: 850 !important;
    }

    .stock-transfer-page .form-control {
        min-height: 46px;
        border: 1px solid #3b3b3b;
        border-radius: 12px;
        background: #0d0d0d;
        color: #fff;
        box-shadow: none;
    }

    .stock-transfer-page .form-control:focus {
        border-color: var(--transfer-yellow);
        background: #0b0b0b;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .stock-transfer-page .form-control::placeholder {
        color: #6f6f6f;
    }

    .product-name {
        color: var(--transfer-yellow) !important;
        min-height: 20px;
    }

    .unit-name-display {
        color: #ddd !important;
        min-height: 20px;
    }

    .available-info {
        display: block;
        color: #86efac !important;
        min-height: 20px;
    }

    .barcode-input.is-invalid,
    .quantity-input.is-invalid {
        border-color: var(--transfer-danger) !important;
        box-shadow: 0 0 0 .2rem rgba(239, 68, 68, .1);
    }

    .invalid-feedback {
        font-size: .76rem;
        font-weight: 700;
    }

    .btn-remove-row {
        min-width: 86px;
        min-height: 42px;
        border: 1px solid rgba(239, 68, 68, .4);
        border-radius: 11px;
        background: rgba(239, 68, 68, .1);
        color: #fca5a5;
        font-weight: 850;
    }

    .btn-remove-row:hover {
        background: var(--transfer-danger);
        color: #fff;
    }

    .transfer-actions {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        margin-top: 1.25rem;
        padding-top: 1.1rem;
        border-top: 1px solid #303030;
    }

    .btn-add-row,
    .btn-transfer-submit {
        min-height: 46px;
        border-radius: 12px;
        font-weight: 900;
    }

    .btn-add-row {
        border: 1px solid #474747;
        background: #191919;
        color: #ddd;
    }

    .btn-add-row:hover {
        border-color: var(--transfer-yellow);
        color: var(--transfer-yellow);
    }

    .btn-transfer-submit {
        min-width: 170px;
        border: 1px solid var(--transfer-yellow);
        background: linear-gradient(135deg, var(--transfer-yellow), var(--transfer-yellow-soft));
        color: #111;
        box-shadow: 0 10px 24px rgba(255, 212, 59, .15);
    }

    .btn-transfer-submit:hover {
        color: #000;
        transform: translateY(-1px);
    }

    @media (max-width: 767.98px) {
        .stock-transfer-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .transfer-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .transfer-hero-content {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .transfer-hero-icon {
            width: 52px;
            height: 52px;
        }

        .transfer-status {
            width: 100%;
            justify-content: center;
        }

        .transfer-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .transfer-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .transfer-actions .btn {
            width: 100%;
        }
    }
</style>

<script>
    const unitBarcodeMap = @json($unitBarcodeMap);

    document.addEventListener('DOMContentLoaded', function () {
        const productRowsContainer = document.getElementById('product-rows');
        const addRowButton = document.getElementById('add-row');
        const transferForm = document.getElementById('stockTransferForm');
        const submitButton = document.getElementById('submitTransferBtn');

        let rowCounter = 0;

        function createRow() {
            rowCounter++;

            const row = document.createElement('div');
            row.className = 'row g-3 product-row align-items-end';
            row.id = `product-row-${rowCounter}`;
            row.dataset.rowNumber = rowCounter;

            row.innerHTML = `
                <div class="col-xl-4 col-md-6">
                    <label for="barcode-input-${rowCounter}" class="form-label">
                        <i class="fas fa-barcode me-1"></i>
                        สแกนบาร์โค้ดสินค้า
                    </label>

                    <input
                        type="text"
                        class="form-control barcode-input"
                        id="barcode-input-${rowCounter}"
                        placeholder="สแกนหรือกรอกรหัสบาร์โค้ด"
                        autocomplete="off"
                    >

                    <input
                        type="hidden"
                        name="product_id[]"
                        class="product-id"
                    >

                    <div class="small mt-2 product-name fw-semibold"></div>
                    <div class="invalid-feedback barcode-feedback"></div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label class="form-label">
                        <i class="fas fa-box-open me-1"></i>
                        หน่วยสินค้า
                    </label>

                    <input
                        type="hidden"
                        name="unit_id[]"
                        class="unit-id"
                    >

                    <div class="unit-name-display mt-2">ยังไม่ได้เลือกสินค้า</div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label for="quantity-input-${rowCounter}" class="form-label">
                        <i class="fas fa-arrow-up-1-9 me-1"></i>
                        จำนวนที่ต้องการย้าย
                    </label>

                    <input
                        type="number"
                        name="quantity[]"
                        class="form-control quantity-input"
                        id="quantity-input-${rowCounter}"
                        min="1"
                        step="1"
                    >

                    <small class="available-info mt-2"></small>
                    <div class="invalid-feedback quantity-feedback"></div>
                </div>

                <div class="col-xl-2 col-md-6 d-flex justify-content-xl-center">
                    <button
                        type="button"
                        class="btn btn-remove-row"
                    >
                        <i class="fas fa-trash-alt me-1"></i>
                        ลบ
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

            function resetRowData() {
                productIdInput.value = '';
                productName.textContent = '';
                unitIdInput.value = '';
                unitNameDisplay.textContent = 'ยังไม่ได้เลือกสินค้า';
                availableInfo.textContent = '';
                quantityInput.value = '';
                quantityInput.removeAttribute('max');
            }

            function resolveBarcode() {
                const barcode = barcodeInput.value.trim();
                const unit = unitBarcodeMap[barcode];

                barcodeInput.classList.remove('is-invalid');
                barcodeFeedback.textContent = '';
                quantityInput.classList.remove('is-invalid');
                quantityFeedback.textContent = '';

                if (!barcode) {
                    resetRowData();
                    return;
                }

                if (!unit) {
                    barcodeInput.classList.add('is-invalid');
                    barcodeFeedback.textContent = 'ไม่พบสินค้านี้ในระบบ';
                    resetRowData();
                    return;
                }

                productIdInput.value = unit.product_id;
                productName.textContent = unit.product_name;
                unitIdInput.value = unit.unit_id;
                unitNameDisplay.textContent = unit.unit_name;
                availableInfo.textContent =
                    `คงเหลือในคลัง: ${unit.warehouse_stock} ${unit.unit_name}`;

                quantityInput.value = '';
                quantityInput.max = Math.max(0, Number(unit.warehouse_stock));
                quantityInput.focus();

                const allRows = document.querySelectorAll('.product-row');
                const lastRow = allRows[allRows.length - 1];

                if (row.id === lastRow.id) {
                    createRow();
                }
            }

            barcodeInput.addEventListener('change', resolveBarcode);

            barcodeInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    resolveBarcode();
                }
            });

            quantityInput.addEventListener('input', function () {
                const barcode = barcodeInput.value.trim();
                const inputQty = Number.parseInt(this.value || '0', 10);
                const unit = unitBarcodeMap[barcode];

                quantityInput.classList.remove('is-invalid');
                quantityFeedback.textContent = '';

                if (!unit) {
                    quantityInput.classList.add('is-invalid');
                    quantityFeedback.textContent =
                        'กรุณาสแกนบาร์โค้ดก่อนระบุจำนวน';
                    return;
                }

                if (inputQty < 1) {
                    quantityInput.classList.add('is-invalid');
                    quantityFeedback.textContent =
                        'จำนวนต้องไม่น้อยกว่า 1';
                    return;
                }

                if (inputQty > Number(unit.warehouse_stock)) {
                    quantityInput.classList.add('is-invalid');
                    quantityFeedback.textContent =
                        `สต็อกในคลังไม่เพียงพอ คงเหลือ ${unit.warehouse_stock} ${unit.unit_name}`;
                }
            });

            row.querySelector('.btn-remove-row').addEventListener('click', function () {
                const rows = document.querySelectorAll('.product-row');

                if (rows.length === 1) {
                    barcodeInput.value = '';
                    resetRowData();
                    return;
                }

                row.remove();
            });
        }

        transferForm.addEventListener('submit', function (event) {
            const rows = [...document.querySelectorAll('.product-row')];
            const validRows = rows.filter(row => {
                return row.querySelector('.product-id').value &&
                    row.querySelector('.unit-id').value &&
                    Number(row.querySelector('.quantity-input').value) > 0;
            });

            let hasError = false;

            rows.forEach(row => {
                const productId = row.querySelector('.product-id').value;
                const unitId = row.querySelector('.unit-id').value;
                const quantityInput = row.querySelector('.quantity-input');
                const quantityFeedback = row.querySelector('.quantity-feedback');

                if (!productId && !unitId && !quantityInput.value) {
                    return;
                }

                if (!productId || !unitId) {
                    row.querySelector('.barcode-input').classList.add('is-invalid');
                    hasError = true;
                }

                if (Number(quantityInput.value) < 1) {
                    quantityInput.classList.add('is-invalid');
                    quantityFeedback.textContent = 'กรุณาระบุจำนวนที่ต้องการย้าย';
                    hasError = true;
                }
            });

            if (validRows.length === 0 || hasError) {
                event.preventDefault();

                const firstInvalid = transferForm.querySelector('.is-invalid');

                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalid.focus();
                }

                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML =
                '<i class="fas fa-spinner fa-spin me-2"></i> กำลังย้ายสินค้า...';
        });

        createRow();

        addRowButton.addEventListener('click', function () {
            createRow();
        });
    });
</script>
@endsection