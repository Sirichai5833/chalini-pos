@extends('layouts.layout')

@section('content')

<style>
    :root {
        --ui-black: #111111;
        --ui-yellow: #facc15;
        --ui-yellow-dark: #eab308;
        --ui-bg: #f7f7f5;
        --ui-card: #ffffff;
        --ui-border: #e5e5e5;
        --ui-text: #1f1f1f;
        --ui-muted: #737373;
    }

    body {
    background: #f7f7f5 !important;
    color: var(--ui-text);
}

[data-bs-theme="dark"] body,
body[data-bs-theme="dark"] {
    background: #181818 !important;
}

    .receive-page {
        max-width: 1680px;
        margin: 0 auto;
        padding: 1rem;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        margin-bottom: .9rem;
        background: var(--ui-black);
        border-radius: 14px;
        color: #fff;
        border-left: 6px solid var(--ui-yellow);
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .page-title-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        background: var(--ui-yellow);
        color: #111;
        flex: 0 0 40px;
    }

    .page-title h3 {
        margin: 0;
        font-weight: 900;
        font-size: 1.2rem;
    }

    .page-title small {
        color: #cfcfcf;
        font-size: .82rem;
    }

    .user-info {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .info-badge {
        padding: .45rem .7rem;
        border-radius: 999px;
        background: #222;
        color: #e5e5e5;
        font-size: .78rem;
        border: 1px solid #333;
    }

    .info-badge.highlight {
        background: var(--ui-yellow);
        color: #111;
        border-color: var(--ui-yellow);
        font-weight: 800;
    }

    .layout-grid {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: .9rem;
        align-items: start;
    }

    .card-clean {
        background: var(--ui-card);
        border: 1px solid var(--ui-border);
        border-radius: 14px;
        overflow: hidden;
    }

    .card-title {
        padding: .8rem .95rem;
        border-bottom: 1px solid var(--ui-border);
        display: flex;
        align-items: center;
        gap: .55rem;
        font-weight: 900;
        background: #fff;
    }

    .card-title i {
        color: #111;
        background: var(--ui-yellow);
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: grid;
        place-items: center;
    }

    .card-body-clean {
        padding: .95rem;
    }

    .form-label {
        font-size: .8rem;
        font-weight: 800 !important;
        color: #333;
        margin-bottom: .35rem;
    }

    .form-control,
    .form-select {
        min-height: 41px;
        border-radius: 9px;
        border: 1px solid #d8d8d8;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--ui-yellow-dark);
        box-shadow: 0 0 0 .15rem rgba(250,204,21,.16) !important;
    }

    textarea.form-control {
        min-height: 78px;
    }

    .scanner-box {
        padding: .8rem;
        margin-bottom: .8rem;
        border: 1px solid #e2e2e2;
        border-radius: 12px;
        background: #fafafa;
    }

    .scanner-row {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .scanner-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        background: var(--ui-yellow);
        color: #111;
        flex: 0 0 38px;
    }

    #barcodeInput {
        min-height: 44px;
        font-weight: 800;
        border: 1.5px solid #222;
    }

    .helper-text {
        margin-top: .4rem;
        font-size: .76rem;
        color: var(--ui-muted);
    }

    .quick-tools {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: .6rem;
        margin-bottom: .8rem;
    }

    .tool-card {
        border: 1px solid var(--ui-border);
        border-radius: 10px;
        padding: .7rem;
        background: #fff;
    }

    .tool-card label {
        display: block;
        margin-bottom: .4rem;
        font-size: .76rem;
        color: #555;
        font-weight: 800;
    }

    .btn-black,
    .btn-yellow,
    .btn-outline-black {
        min-height: 39px;
        border-radius: 8px;
        font-weight: 800;
        font-size: .82rem;
    }

    .btn-black {
        background: #111;
        color: var(--ui-yellow);
        border: 1px solid #111;
    }

    .btn-black:hover {
        background: #000;
        color: #fde047;
    }

    .btn-yellow {
        background: var(--ui-yellow);
        color: #111;
        border: 1px solid var(--ui-yellow);
    }

    .btn-yellow:hover {
        background: var(--ui-yellow-dark);
        color: #111;
    }

    .btn-outline-black {
        background: #fff;
        color: #111;
        border: 1px solid #111;
    }

    .btn-outline-black:hover {
        background: #111;
        color: var(--ui-yellow);
    }

    .switch-line {
        min-height: 39px;
        display: flex;
        align-items: center;
        padding: .55rem .7rem;
        border: 1px solid var(--ui-border);
        border-radius: 8px;
        background: #fafafa;
    }

    .form-check-input:checked {
        background-color: #111;
        border-color: #111;
    }

    .table-wrap {
        border: 1px solid var(--ui-border);
        border-radius: 10px;
        overflow: hidden;
    }

    #productTable {
        margin: 0;
    }

    #productTable thead th {
        background: #111 !important;
        color: var(--ui-yellow);
        border-color: #222;
        font-size: .74rem;
        font-weight: 900;
        padding: .65rem .5rem;
        white-space: nowrap;
    }

    #productTable tbody td {
        padding: .62rem .5rem;
        font-size: .84rem;
        vertical-align: middle;
        border-color: #eeeeee;
    }

    #productTable tbody tr:hover {
        background: #fffdf0;
    }

    #productTable .form-control,
    #productTable .form-select {
        min-height: 36px;
        font-size: .82rem;
        border-radius: 7px;
    }

    .empty-products {
        padding: 2.4rem 1rem !important;
        text-align: center;
        color: #8a8a8a !important;
        background: #fafafa;
    }

    .empty-products i {
        display: block;
        margin-bottom: .45rem;
        color: #c9a500;
        font-size: 1.7rem;
    }

    .save-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .8rem;
        margin-top: .8rem;
        padding-top: .8rem;
        border-top: 1px solid var(--ui-border);
    }

    .save-note {
        margin-right: auto;
        color: var(--ui-muted);
        font-size: .78rem;
    }

    .btn-save {
        min-height: 42px;
        padding: .55rem 1rem;
        border: 0;
        border-radius: 9px;
        background: var(--ui-yellow);
        color: #111;
        font-weight: 900;
    }

    .btn-save:hover {
        background: var(--ui-yellow-dark);
        color: #111;
    }

    @media (max-width: 1100px) {
        .layout-grid {
            grid-template-columns: 1fr;
        }

        .quick-tools {
            grid-template-columns: 1fr 1fr;
        }

        .quick-tools .tool-card:last-child {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 700px) {
        .receive-page {
            padding: .6rem;
        }

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .user-info {
            justify-content: flex-start;
        }

        .quick-tools {
            grid-template-columns: 1fr;
        }

        .quick-tools .tool-card:last-child {
            grid-column: auto;
        }

        .save-row {
            align-items: stretch;
            flex-direction: column;
        }

        .save-note {
            margin-right: 0;
        }

        .btn-save {
            width: 100%;
        }
    }
</style>

<div class="receive-page">
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'รับสินค้าเข้าสำเร็จ!',
                text: @json(session('success')),
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#eab308',
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
@endif
    <div class="page-header">
        <div class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <div>
                <h3>รับสินค้าเข้าสต็อก</h3>
                <small>สแกนสินค้าและบันทึกรายการรับเข้า</small>
            </div>
        </div>

        <div class="user-info">
            <span class="info-badge">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y') }}
            </span>
            <span class="info-badge highlight">
                <i class="bi bi-person me-1"></i>{{ Auth::user()->name }}
            </span>
        </div>
    </div>

    <form id="stockReceiveForm"
          action="{{ route('product.products.add-stock-multi') }}"
          method="POST">
        @csrf

        <div class="layout-grid">

            <aside class="card-clean">
                <div class="card-title">
                    <i class="bi bi-file-earmark-text"></i>
                    ข้อมูลการรับสินค้า
                </div>

                <div class="card-body-clean">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">
                            ผู้จำหน่าย <span class="text-danger">*</span>
                        </label>
                        <select name="supplier_id"
                                id="supplier_id"
                                class="form-select @error('supplier_id') is-invalid @enderror"
                                required>
                            <option value="">-- เลือกผู้จำหน่าย --</option>
                            @foreach ($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}"
                                    @selected(old('supplier_id') == $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label">เอกสารอ้างอิง</label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               class="form-control @error('reference_number') is-invalid @enderror"
                               value="{{ old('reference_number') }}"
                               placeholder="เช่น PO-001">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="received_date" class="form-label">
                            วันที่รับสินค้า <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="received_date"
                               id="received_date"
                               class="form-control @error('received_date') is-invalid @enderror"
                               value="{{ old('received_date', now()->format('Y-m-d')) }}"
                               required>
                        @error('received_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="note" class="form-label">หมายเหตุ</label>
                        <textarea name="note"
                                  id="note"
                                  class="form-control"
                                  placeholder="รายละเอียดเพิ่มเติม">{{ old('note') }}</textarea>
                    </div>
                </div>
            </aside>

            <section class="card-clean">
                <div class="card-title">
                    <i class="bi bi-box-seam"></i>
                    รายการสินค้า
                </div>

                <div class="card-body-clean">

                    <div class="scanner-box">
                        <div class="scanner-row">
                            <div class="scanner-icon">
                                <i class="bi bi-upc-scan"></i>
                            </div>

                            <div class="flex-grow-1">
                                <input type="text"
                                       id="barcodeInput"
                                       class="form-control"
                                       placeholder="ยิงบาร์โค้ด หรือพิมพ์แล้วกด Enter"
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="helper-text">
                            สแกนสินค้าซ้ำ ระบบจะเพิ่มจำนวนให้อัตโนมัติ
                        </div>
                    </div>

                    <div class="quick-tools">
                        <div class="tool-card">
                            <label>เพิ่มเข้าที่</label>
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-black flex-fill"
                                        onclick="setAllLocations('warehouse')">
                                    คลัง
                                </button>
                                <button type="button"
                                        class="btn btn-yellow flex-fill"
                                        onclick="setAllLocations('store')">
                                    หน้าร้าน
                                </button>
                            </div>
                        </div>

                        <div class="tool-card">
                            <label>วันหมดอายุทั้งหมด</label>
                            <div class="input-group">
                                <input type="date"
                                       id="bulkExpiryDate"
                                       class="form-control">
                                <button type="button"
                                        class="btn btn-outline-black"
                                        onclick="applyExpiryDateToAll()">
                                    ใช้
                                </button>
                            </div>
                        </div>

                        <div class="tool-card">
                            <label>ตัวเลือก</label>
                            <div class="form-check form-switch switch-line ps-5">
                                <input class="form-check-input"
                                       type="checkbox"
                                       role="switch"
                                       id="allNoExpirySwitch"
                                       onchange="setNoExpiryForAll(this.checked)">
                                <label class="form-check-label fw-bold ms-1"
                                       for="allNoExpirySwitch">
                                    ไม่มีวันหมดอายุ
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-wrap">
                        <table class="table table-bordered table-hover text-center align-middle"
                               id="productTable">
                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th>หน่วย</th>
                                    <th>จำนวน</th>
                                    <th>เพิ่มเข้าที่</th>
                                    <th>ของแถม</th>
                                    <th>รหัสล็อต</th>
                                    <th>วันหมดอายุ</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>

                            <tbody id="product-body">
                                <tr id="emptyProductRow">
                                    <td colspan="8" class="empty-products">
                                        <i class="bi bi-upc-scan"></i>
                                        ยังไม่มีสินค้า กรุณาสแกนบาร์โค้ด
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="save-row">
                        <div class="save-note">
                            ตรวจสอบจำนวน ปลายทาง ล็อต และวันหมดอายุก่อนบันทึก
                        </div>

                        <button type="submit" class="btn btn-save">
                            <i class="bi bi-check-circle me-1"></i>
                            บันทึกการรับสินค้า
                        </button>
                    </div>
                </div>
            </section>

        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let barcode = '';
let lastTime = Date.now();

const barcodeInput = document.getElementById('barcodeInput');
const stockReceiveForm = document.getElementById('stockReceiveForm');

document.addEventListener('keydown', function (event) {
    const tag = event.target.tagName.toLowerCase();

    if (['input', 'textarea', 'select'].includes(tag)) {
        return;
    }

    const currentTime = Date.now();

    if (currentTime - lastTime > 100) {
        barcode = '';
    }

    if (event.key === 'Enter') {
        event.preventDefault();

        if (barcode.trim() !== '') {
            fetchProduct(barcode.trim());
            barcode = '';
        }
    } else if (event.key.length === 1) {
        barcode += event.key;
    }

    lastTime = currentTime;
});

barcodeInput.addEventListener('keydown', function (event) {
    if (event.key !== 'Enter') {
        return;
    }

    event.preventDefault();

    const code = this.value.trim();

    if (code !== '') {
        fetchProduct(code);
        this.value = '';
    }
});

async function fetchProduct(barcodeValue) {
    try {
        const response = await fetch(
            `/products/barcode/${encodeURIComponent(barcodeValue)}`,
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'ไม่พบสินค้านี้');
        }

        addProductRow(data.product);
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบสินค้า',
            text: error.message
        });

        barcodeInput.focus();
    }
}

function addProductRow(product) {
    const tbody = document.getElementById('product-body');
    const emptyRow = document.getElementById('emptyProductRow');
    const rowKey = `${product.id}_${product.unit_id}`;

    emptyRow?.remove();

    const existingRow = Array.from(tbody.rows)
        .find(row => row.dataset.rowKey === rowKey);

    if (existingRow) {
        const quantityInput = existingRow.querySelector(
            `input[name="items[${product.id}][${product.unit_id}][quantity]"]`
        );

        quantityInput.value =
            (parseInt(quantityInput.value, 10) || 0) + 1;

        return;
    }

    const row = document.createElement('tr');
    row.dataset.rowKey = rowKey;

    row.innerHTML = `
        <td class="text-start">
            <strong>${escapeHtml(product.name)}</strong>

            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][product_id]"
                value="${product.id}">

            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][unit_id]"
                value="${product.unit_id}">

            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][unit_name]"
                value="${escapeHtml(product.unit_name || '')}">

            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][unit_quantity]"
                value="${product.unit_quantity || 1}">
        </td>

        <td>${escapeHtml(product.unit_name || '-')}</td>

        <td>
            <input type="number"
                name="items[${product.id}][${product.unit_id}][quantity]"
                value="1"
                min="1"
                class="form-control text-center"
                required>
        </td>

        <td>
            <select name="items[${product.id}][${product.unit_id}][location]"
                    class="form-select location-select"
                    required>
                <option value="warehouse">คลัง</option>
                <option value="store">หน้าร้าน</option>
            </select>
        </td>

        <td>
            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][is_free]"
                value="0"
                class="is-free-value">

            <input class="form-check-input"
                   type="checkbox"
                   onchange="toggleFree(this)">
        </td>

        <td>
            <input type="text"
                name="items[${product.id}][${product.unit_id}][lot_number]"
                class="form-control"
                placeholder="ไม่มีก็เว้นว่าง">
        </td>

        <td>
            <input type="hidden"
                name="items[${product.id}][${product.unit_id}][no_expiry]"
                value="0"
                class="no-expiry-value">

            <input type="date"
                name="items[${product.id}][${product.unit_id}][expiry_date]"
                class="form-control expiry-input">

            <div class="form-check mt-2 text-start">
                <input class="form-check-input no-expiry-checkbox"
                       type="checkbox"
                       onchange="toggleNoExpiry(this)">

                <label class="form-check-label small">
                    ไม่มีวันหมดอายุ
                </label>
            </div>
        </td>

        <td>
            <button type="button"
                    class="btn btn-outline-danger btn-sm"
                    onclick="removeProductRow(this)">
                ลบ
            </button>
        </td>
    `;

    tbody.appendChild(row);

    // ใช้ค่าตั้งแบบกลุ่มกับสินค้าที่เพิ่งเพิ่มเข้ามาด้วย
    const allNoExpiry = document.getElementById('allNoExpirySwitch')?.checked ?? false;
    const bulkExpiryDate = document.getElementById('bulkExpiryDate')?.value ?? '';

    if (allNoExpiry) {
        const noExpiryCheckbox = row.querySelector('.no-expiry-checkbox');
        if (noExpiryCheckbox) {
            noExpiryCheckbox.checked = true;
            toggleNoExpiry(noExpiryCheckbox);
        }
    } else if (bulkExpiryDate) {
        const dateInput = row.querySelector('.expiry-input');
        if (dateInput) {
            dateInput.value = bulkExpiryDate;
        }
    }
}

function setAllLocations(location) {
    document.querySelectorAll('#product-body .location-select')
        .forEach(select => {
            select.value = location;
        });
}

function applyExpiryDateToAll() {
    const dateValue = document.getElementById('bulkExpiryDate').value;

    if (!dateValue) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาเลือกวันหมดอายุ',
            text: 'เลือกวันที่ก่อนนำไปใช้กับสินค้าทั้งหมด'
        });
        return;
    }

    document.getElementById('allNoExpirySwitch').checked = false;

    document.querySelectorAll('#product-body tr[data-row-key]')
        .forEach(row => {
            const dateInput = row.querySelector('.expiry-input');
            const hiddenInput = row.querySelector('.no-expiry-value');
            const checkbox = row.querySelector('.no-expiry-checkbox');

            if (dateInput) {
                dateInput.removeAttribute('readonly');
                dateInput.value = dateValue;
            }

            if (hiddenInput) {
                hiddenInput.value = '0';
            }

            if (checkbox) {
                checkbox.checked = false;
            }
        });
}

function setNoExpiryForAll(checked) {
    document.querySelectorAll('#product-body tr[data-row-key]')
        .forEach(row => {
            const dateInput = row.querySelector('.expiry-input');
            const hiddenInput = row.querySelector('.no-expiry-value');
            const checkbox = row.querySelector('.no-expiry-checkbox');

            if (hiddenInput) {
                hiddenInput.value = checked ? '1' : '0';
            }

            if (checkbox) {
                checkbox.checked = checked;
            }

            if (dateInput) {
                if (checked) {
                    dateInput.value = '';
                    dateInput.setAttribute('readonly', 'readonly');
                } else {
                    dateInput.removeAttribute('readonly');
                }
            }
        });
}

function toggleFree(checkbox) {
    const hiddenInput = checkbox
        .closest('td')
        .querySelector('.is-free-value');

    hiddenInput.value = checkbox.checked ? '1' : '0';
}

function toggleNoExpiry(checkbox) {
    const cell = checkbox.closest('td');
    const dateInput = cell.querySelector('.expiry-input');
    const hiddenInput = cell.querySelector('.no-expiry-value');

    hiddenInput.value = checkbox.checked ? '1' : '0';
    dateInput.value = '';

    if (checkbox.checked) {
        dateInput.setAttribute('readonly', 'readonly');
    } else {
        dateInput.removeAttribute('readonly');
    }
}

function removeProductRow(button) {
    button.closest('tr').remove();

    const tbody = document.getElementById('product-body');

    if (tbody.querySelectorAll('tr[data-row-key]').length === 0) {
        tbody.innerHTML = `
            <tr id="emptyProductRow">
                <td colspan="8" class="empty-products">
                    <i class="bi bi-upc-scan"></i>
                    ยังไม่มีสินค้า กรุณาสแกนบาร์โค้ด
                </td>
            </tr>
        `;
    }
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

stockReceiveForm.addEventListener('submit', function (event) {
    const productRows = document.querySelectorAll(
        '#product-body tr[data-row-key]'
    );

    if (productRows.length === 0) {
        event.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'ยังไม่มีสินค้า',
            text: 'กรุณาสแกนสินค้าอย่างน้อย 1 รายการ'
        });

        return;
    }

    if (this.dataset.submitting === '1') {
        event.preventDefault();
        return;
    }

    this.dataset.submitting = '1';

    const button = this.querySelector('button[type="submit"]');

    button.disabled = true;
    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        กำลังบันทึก...
    `;
});

document.addEventListener('DOMContentLoaded', function () {
    barcodeInput.focus();
});
</script>
@endsection