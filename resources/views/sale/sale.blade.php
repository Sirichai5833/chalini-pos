
@extends('layouts.layout')

@section('content')
@php
    $promptPay = \DB::table('settings')->where('key', 'promptpay')->value('value') ?? '0843860015';
    $wholesaleMinimum = (float) \App\Models\Setting::get('wholesale_minimum_amount', 500);
@endphp

<style>
:root {
    --pos-yellow:#ffd43b;
    --pos-black:#080808;
    --pos-card:#181818;
    --pos-border:#303030;
    --pos-text:#f7f7f7;
    --pos-muted:#aaa;
}
body {
    background:radial-gradient(circle at top right,rgba(255,212,59,.08),transparent 28%),#080808;
}
.pos-page {color:var(--pos-text)}
.pos-hero {
    padding:1.35rem 1.5rem;margin:0 1rem 1.25rem;
    border:1px solid rgba(255,212,59,.22);border-radius:22px;
    background:linear-gradient(135deg,#070707,#171717 58%,#2c2400);
    box-shadow:0 18px 42px rgba(0,0,0,.36)
}
.pos-title {color:var(--pos-yellow);font-weight:950;margin:0}
.pos-subtitle {color:#ccc;margin:.35rem 0 0;font-size:.9rem}
.pos-user-badge {background:rgba(255,212,59,.11);color:var(--pos-yellow);border:1px solid rgba(255,212,59,.22)}
.pos-main-card,.payment-card {
    border:1px solid #2d2d2d!important;border-radius:20px!important;
    background:linear-gradient(180deg,#191919,#111);color:#fff;
    box-shadow:0 18px 40px rgba(0,0,0,.3)
}

/* ปุ่มเปิด-ปิดรายการสินค้า */
.pos-picker-toggle {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: #181818;
    color: var(--pos-yellow);
    border: 1px solid #303030;
    border-radius: 12px;
    font-weight: 800;
    transition: .2s;
}

.pos-picker-toggle:hover {
    background: rgba(255,212,59,.08);
    border-color: var(--pos-yellow);
}

.pos-picker-toggle .toggle-icon {
    transition: transform .25s ease;
}

.pos-picker-toggle[aria-expanded="false"] .toggle-icon {
    transform: rotate(-90deg);
}

.pos-picker-collapse {
    margin-top: 10px;
}

.pos-picker-collapse .pos-picker {
    border-radius: 12px;
}

.pos-picker-collapse.collapsing {
    transition: height .25s ease;
}
.pos-main-card .card-body,.payment-card .card-body {background:transparent}
.section-label {color:var(--pos-yellow);font-weight:900}
.form-label {color:#ddd;font-weight:800}
.form-control,.form-select,.input-group-text {
    min-height:43px;border-color:#3a3a3a;background:#131313;color:#fff;box-shadow:none
}
.form-control:focus,.form-select:focus {
    border-color:var(--pos-yellow);background:#171717;color:#fff;
    box-shadow:0 0 0 .2rem rgba(255,212,59,.12)
}
.form-control::placeholder {color:#777}
.form-select option {background:#171717;color:#fff}
input[type=date]::-webkit-calendar-picker-indicator {filter:invert(1)}
.sale-type-box,.total-box,.auto-print-box {
    padding:12px;border:1px solid rgba(255,212,59,.22);
    border-radius:14px;background:rgba(255,212,59,.05)
}
.form-check-input {background-color:#111;border-color:#666}
.form-check-input:checked {background-color:var(--pos-yellow);border-color:var(--pos-yellow)}
.barcode-box {position:relative}
.barcode-box input {padding-left:42px}
.barcode-box:before {
    content:"\f6a5";position:absolute;left:14px;top:39px;
    font-family:"bootstrap-icons";color:var(--pos-yellow);z-index:2
}
.pos-picker {border:1px solid #303030;border-radius:15px;background:#101010;overflow:hidden}
.pos-picker-search {padding:11px;border-bottom:1px solid #303030}
.pos-picker-list {max-height:600px;overflow-y:auto}
.pos-picker-item {
    display:flex;align-items:center;gap:11px;width:100%;padding:11px;
    border:0;border-bottom:1px solid #292929;background:transparent;
    color:#fff;text-align:left;transition:.18s
}
.pos-picker-item:hover {background:rgba(255,212,59,.09)}
.pos-picker-item:disabled {opacity:.45;cursor:not-allowed}
.pos-picker-image,.pos-picker-placeholder {
    width:56px;height:56px;flex-shrink:0;border-radius:10px;
    object-fit:cover;background:#292929
}
.pos-picker-placeholder {
    display:flex;align-items:center;justify-content:center;
    color:var(--pos-yellow);font-size:23px
}
.pos-picker-name {font-size:.87rem;font-weight:800;color:#fff}
.pos-picker-detail {font-size:.74rem;color:#aaa;margin-top:4px}
.pos-picker-price {font-size:.9rem;font-weight:900;color:var(--pos-yellow)}
.pos-picker-empty {padding:25px 10px;text-align:center;color:#999}
#productTable {
    --bs-table-bg:transparent;--bs-table-color:#eee;
    --bs-table-border-color:#303030;--bs-table-hover-bg:rgba(255,212,59,.05);
    --bs-table-hover-color:#fff
}
#productTable thead th {
    background:#101010!important;color:var(--pos-yellow);
    font-size:.74rem;white-space:nowrap
}
#productTable td {color:#eee;vertical-align:middle}
#productTable .form-control {min-height:35px;max-width:75px;margin:auto}
.payment-card .card-header {
    border-bottom:1px solid rgba(255,212,59,.22)!important;
    background:linear-gradient(135deg,rgba(255,212,59,.12),rgba(255,212,59,.03)),#111!important;
    color:var(--pos-yellow)!important
}
#totalAmount,#qrTotalAmount {color:var(--pos-yellow)!important}
.total-box {background:rgba(255,212,59,.07)}
.quick-cash-grid .btn {
    min-width:55px;min-height:39px;border:1px solid #3b3b3b;
    border-radius:10px;background:#171717;color:#ddd;font-weight:800
}
.quick-cash-grid .btn:hover {background:var(--pos-yellow);color:#111}
.change-box {border:1px solid #303030;border-radius:15px;background:#121212;padding:13px}
#change {color:var(--pos-yellow)!important}
.payment-actions .btn,.secondary-actions .btn {border-radius:12px;font-weight:900}
.btn-pay-cash {background:#22c55e;color:#07130b;border:0}
.btn-pay-credit {background:rgba(255,212,59,.08);color:var(--pos-yellow);border:1px solid var(--pos-yellow)}
.btn-pay-qr {background:#38bdf8;color:#06141a;border:0}
#qrBox {border:1px solid rgba(255,212,59,.22);background:#101010;color:#fff;border-radius:18px}
.modal-content {background:#151515;color:#fff;border:1px solid rgba(255,212,59,.22)}
.modal-header,.modal-footer {background:#101010;border-color:#303030}
.modal-title {color:var(--pos-yellow)}
.modal-body {background:#151515}
.modal-body .card {background:#111}
@media(max-width:991.98px) {
    .pos-picker-list {max-height:300px}
    .payment-column {margin-top:1rem}
}
@media(max-width:575.98px) {
    .pos-hero {margin:0 .5rem 1rem;padding:1rem}
    .pos-hero-content {align-items:flex-start!important;flex-direction:column}
}
</style>

<section class="pos-hero">
    <div class="pos-hero-content d-flex align-items-center justify-content-between gap-3">
        <div>
            <h2 class="pos-title"><i class="bi bi-cart-check-fill me-2"></i>ระบบขายหน้าร้าน</h2>
            <p class="pos-subtitle">เลือกสินค้า สแกนบาร์โค้ด รับชำระเงิน และจัดการบิลในหน้าเดียว</p>
        </div>
        <span class="badge rounded-pill px-3 py-2 pos-user-badge">
            <i class="bi bi-person-badge-fill me-1"></i>{{ Auth::user()->name ?? 'ผู้ใช้งาน' }}
        </span>
    </div>
</section>

@if(session('success'))
    <x-sweet-alert icon="success" title="ดำเนินการสำเร็จ" text="{{ session('success') }}" confirm-button-text="ตกลง"/>
@endif
@if(session('error'))
    <x-sweet-alert icon="error" title="เกิดข้อผิดพลาด" text="{{ session('error') }}" confirm-button-text="ตกลง"/>
@endif

<div class="container-fluid py-3 px-2 pos-page">
    <div class="row g-3">

        {{-- สินค้าและตะกร้า --}}
        <div class="col-lg-8 col-12">
            <div class="card pos-main-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">พนักงาน: {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}</h5>
                            <small class="text-secondary">วันที่: {{ date('d/m/Y') }}</small>
                        </div>
                        <span class="badge pos-user-badge align-self-start">POS SYSTEM</span>
                    </div>
<div class="d-flex justify-content-start mb-3">
    <button type="button"
        class="pos-picker-toggle"
        style="width:auto; gap:15px;"
        data-bs-toggle="collapse"
        data-bs-target="#productPickerCollapse"
        aria-expanded="false"
        aria-controls="productPickerCollapse">

        <span>
            <i class="bi bi-box-seam-fill me-2"></i>เลือกสินค้า
        </span>

        <i class="bi bi-chevron-down toggle-icon"></i>
    </button>
</div>

<div class="collapse pos-picker-collapse" id="productPickerCollapse">
    <div class="pos-picker">
        <div class="pos-picker-search">
            <input type="text" id="productSearch" class="form-control"
                placeholder="ค้นหาชื่อสินค้า / บาร์โค้ด">
        </div>
        <div id="productPicker" class="pos-picker-list">
            <div class="pos-picker-empty">กำลังโหลดสินค้า...</div>
        </div>
    </div>
</div>
                    <div class="row g-3">
                        {{-- แถบเลือกสินค้า --}}
                       



                        {{-- ตะกร้าสินค้า --}}
                        <div class="col-lg-12">
                            <h5 class="section-label mb-3"><i class="bi bi-cart3 me-2"></i>รายการในตะกร้า</h5>

                            <div class="sale-type-box mb-3">
                                <label class="form-label mb-2">ประเภทการขาย</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="priceType"
                                               id="retail" value="retail" checked>
                                        <label class="form-check-label" for="retail">ปลีก</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="priceType"
                                               id="wholesale" value="wholesale">
                                        <label class="form-check-label" for="wholesale">ส่ง</label>
                                    </div>
                                </div>
                            </div>

                            <div class="barcode-box mb-3">
                                <label class="form-label">สแกน / พิมพ์บาร์โค้ด</label>
                                <input type="text" id="barcodeInput" class="form-control"
                                       placeholder="สแกนบาร์โค้ดแล้วกด Enter" autocomplete="off">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center align-middle" id="productTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>สินค้า</th>
                                            <th>หน่วย</th>
                                            <th>จำนวน</th>
                                            <th>ราคา</th>
                                            <th>รวม</th>
                                            <th>ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-list">
                                        <tr><td colspan="7" class="text-secondary py-4">ยังไม่มีสินค้าในตะกร้า</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-secondary">จำนวนรายการสินค้า</span>
                                <strong id="cartCount">0 รายการ</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ชำระเงิน --}}
        <div class="col-lg-4 col-12 payment-column">
            <div class="card payment-card h-100">
                <div class="card-header py-3">
                    <h4 class="mb-0"><i class="bi bi-wallet2 me-2"></i>ชำระเงิน</h4>
                </div>
                <div class="card-body p-3">

                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 mb-3 auto-print-box">
                        <label for="autoPrintSwitch" class="mb-0 fw-bold">🖨️ พิมพ์ใบเสร็จอัตโนมัติ</label>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="autoPrintSwitch"
                                   onchange="toggleAutoPrint(this.checked)">
                        </div>
                    </div>

                    <div class="total-box text-center mb-3">
                        <span class="small text-secondary fw-bold">ยอดรวมทั้งหมด</span>
                        <h1 id="totalAmount" class="my-2 fw-bolder">0.00 บาท</h1>
                    </div>

                    <div class="mb-3">
                        <label for="cash" class="form-label">เงินที่รับมา (เงินสด)</label>
                        <div class="input-group">
                            <span class="input-group-text">฿</span>
                            <input type="number" class="form-control text-end fw-bold" id="cash"
                                   placeholder="0.00" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">💵 ทางลัดเงินสด</label>
                        <div class="d-flex flex-wrap gap-2 quick-cash-grid">
                            <button type="button" class="btn flex-grow-1" onclick="addCash(1)">+1</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(2)">+2</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(5)">+5</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(10)">+10</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(20)">+20</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(50)">+50</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(100)">+100</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(500)">+500</button>
                            <button type="button" class="btn flex-grow-1" onclick="addCash(1000)">+1000</button>
                            <button type="button" class="btn btn-danger" onclick="clearCash()">ล้าง</button>
                        </div>
                    </div>

                    <div class="change-box mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label mb-2">เงินทอน</label>
                            <small class="text-secondary">THB</small>
                        </div>
                        <input type="text" class="form-control text-end fs-4 fw-bold border-0 bg-transparent"
                               id="change" readonly value="0.00 บาท">
                    </div>

                    <div class="d-grid gap-2 payment-actions">
                        <button type="button" id="payCashButton" class="btn btn-pay-cash btn-lg py-3"
                                onclick="payCash()">💵 ชำระด้วยเงินสด</button>

                        {{-- <button type="button" class="btn btn-pay-credit py-3"
                                onclick="openCreditModal()">💳 ขายเงินเชื่อ (ค้างชำระ)</button> --}}

                        <button type="button" id="payQRButton" class="btn btn-pay-qr btn-lg py-3"
                                onclick="payQRCode()">📱 ชำระด้วย QR Code</button>

                        <button type="button" id="confirmQRButton" class="btn btn-info text-white fw-bold py-3 d-none"
                                onclick="confirmPaymentByQR()">✅ ยืนยันรับเงินโอนแล้ว</button>
                    </div>

                    <div class="d-flex gap-2 mt-3 secondary-actions">
                        <button type="button" class="btn btn-light w-50 fw-bold" onclick="holdBill()">📥 พักบิล</button>
                        <button type="button" class="btn btn-dark w-50 fw-bold" onclick="showHeldBills()">
                            📋 บิลที่พัก <span class="badge bg-danger" id="heldCount" style="display:none">0</span>
                        </button>
                    </div>

                    <div id="qrBox" class="card p-3 mt-3 text-center" style="display:none">
                        <div class="mb-2">
                            <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center"
                                 style="width:55px;height:55px;font-size:24px">C</div>
                        </div>
                        <h5 class="fw-bold">Chalini Store</h5>
                        <div class="small text-secondary mb-2">PromptPay ID: {{ $promptPay }}</div>
                        <div class="bg-white rounded-3 p-2 d-inline-block mx-auto">
                            <img id="qrImage" src="" alt="PromptPay QR" style="width:200px;height:200px;object-fit:contain">
                        </div>
                        <h4 class="mt-3" id="qrTotalAmount">0.00 บาท</h4>
                        <small class="text-secondary">กรุณาตรวจสอบยอดเงินเข้าบัญชีก่อนยืนยัน</small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal ขายเงินเชื่อ --}}
<div class="modal fade" id="creditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">ขายเงินเชื่อ (บันทึกยอดหนี้)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card mb-4 border-danger">
                    <div class="card-body">
                        <div class="text-danger small fw-bold">ยอดเงินที่จะบันทึกเป็นหนี้</div>
                        <h2 class="text-danger fw-bold"><span id="creditTotal">0.00</span> บาท</h2>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="creditCustomer" class="form-label mb-0">เลือกลูกค้าค้างชำระ</label>
                        <button type="button" class="btn btn-sm btn-link" onclick="toggleAddCustomerForm()">+ เพิ่มลูกค้าใหม่</button>
                    </div>
                    <select id="creditCustomer" class="form-select">
                        <option value="">-- กรุณาเลือกลูกค้า --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-active="{{ $customer->is_active }}">
                                {{ $customer->name }} (ยอดค้างเดิม: {{ number_format($customer->current_debt,2) }} บ.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="addCustomerBox" class="card mb-3 d-none">
                    <div class="card-body">
                        <label class="form-label">ชื่อลูกค้า</label>
                        <input type="text" id="newCustomerName" class="form-control mb-2" placeholder="ชื่อ-นามสกุล">
                        <label class="form-label">เบอร์โทรศัพท์</label>
                        <input type="text" id="newCustomerPhone" class="form-control mb-2" placeholder="เบอร์โทรศัพท์">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAddCustomerForm(false)">ยกเลิก</button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addCustomer()">บันทึกลูกค้า</button>
                        </div>
                    </div>
                </div>

                <label for="creditDueDate" class="form-label">วันครบกำหนดชำระ</label>
                <input type="date" id="creditDueDate" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="confirmCreditSale()">ยืนยันบันทึกเงินเชื่อ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
'use strict';

const shopPromptPayID = @json($promptPay);
const sellerName = @json(Auth::user()->name ?? 'ผู้ใช้งาน');
const sellerId = @json(Auth::user()->id ?? '-');
const productCatalog = @json($products ?? []);
const wholesaleMinimumAmount = Number(@json($wholesaleMinimum));
const saleUrl = @json(route('update.stock'));
const csrfToken = @json(csrf_token());

let products = [];
let heldBills = [];
let totalAmount = 0;
let posSaleRequestToken = @json((string) \Illuminate\Support\Str::uuid());
let saleSubmissionInFlight = false;
let autoPrintEnabled = localStorage.getItem('autoPrintEnabled') === 'true';

/* ------------------------------
   เครื่องมือทั่วไป
------------------------------ */

function showAlert(icon, title, text = '') {
    return Swal.fire({
        icon, title, text,
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#198754',
        allowOutsideClick: false
    });
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;'
    })[char]);
}

function getUnitId(product) {
    return Number(product.product_unit_id || product.unit_id || 0);
}

function getPriceType() {
    return document.querySelector('input[name="priceType"]:checked')?.value || 'retail';
}

function getPrice(product, type = getPriceType()) {
    const retail = Number(product.retail_price ?? product.price ?? 0);
    const wholesale = Number(product.wholesale_price ?? product.wholesale ?? 0);
    return type === 'wholesale' && wholesale > 0 ? wholesale : retail;
}

function formatMoney(value) {
    return Number(value || 0).toLocaleString('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function getProductImage(product) {
    return product.image || product.image_url || '';
}

/* ------------------------------
   รายการเลือกสินค้า
------------------------------ */

function renderProductPicker(keyword = '') {
    const container = document.getElementById('productPicker');
    if (!container) return;

    const search = String(keyword).trim().toLowerCase();

    const filtered = productCatalog.filter(product => {
        const active = product.is_active === true ||
            product.is_active === 1 ||
            product.is_active === '1';

        if (!active) return false;

        return !search ||
            String(product.name || '').toLowerCase().includes(search) ||
            String(product.barcode || '').toLowerCase().includes(search) ||
            String(product.sku || '').toLowerCase().includes(search);
    });

    if (!filtered.length) {
        container.innerHTML = '<div class="pos-picker-empty">ไม่พบสินค้าที่ค้นหา</div>';
        return;
    }

    container.innerHTML = filtered.map(product => {
        const image = getProductImage(product);
        const price = getPrice(product);
        const unitId = getUnitId(product);

        return `
            <button type="button" class="pos-picker-item"
                ${unitId ? `onclick="addProductToCart(${unitId})"` : 'disabled'}
                title="คลิกเพื่อเพิ่มสินค้า">
                ${image
                    ? `<img class="pos-picker-image" src="${escapeHtml(image)}" alt=""
                         onerror="this.outerHTML='<div class=&quot;pos-picker-placeholder&quot;><i class=&quot;bi bi-box-seam&quot;></i></div>'">`
                    : `<div class="pos-picker-placeholder"><i class="bi bi-box-seam"></i></div>`
                }
                <div class="flex-grow-1 min-w-0">
                    <div class="pos-picker-name">${escapeHtml(product.name)}</div>
                    <div class="pos-picker-detail">${escapeHtml(product.unit || 'หน่วย')} · ${escapeHtml(product.barcode || 'ไม่มีบาร์โค้ด')}</div>
                    <div class="pos-picker-price">${formatMoney(price)} บาท</div>
                </div>
                <i class="bi bi-plus-circle-fill text-warning fs-5"></i>
            </button>
        `;
    }).join('');
}

function addProductToCart(unitId) {
    const product = productCatalog.find(item => getUnitId(item) === Number(unitId));

    if (!product) {
        showAlert('error', 'ไม่พบสินค้า', 'ไม่พบข้อมูลหน่วยสินค้าที่เลือก');
        return;
    }

    if (!(product.is_active === true || product.is_active === 1 || product.is_active === '1')) {
        showAlert('warning', 'สินค้าปิดการขาย', product.name);
        return;
    }

    if (!getUnitId(product)) {
        showAlert('error', 'ข้อมูลหน่วยสินค้าไม่ถูกต้อง', 'ไม่พบรหัสหน่วยสินค้า');
        return;
    }

    const existing = products.find(item => getUnitId(item) === Number(unitId));

    if (existing) {
        existing.qty += 1;
    } else {
        products.push({
            ...product,
            product_unit_id: Number(unitId),
            unit_id: Number(unitId),
            qty: 1,
            price: getPrice(product),
            price_type: getPriceType()
        });
    }

    renderProductList();
    updateTotalAmount();
    updateQRCode();
}

/* ------------------------------
   บาร์โค้ด
------------------------------ */

function addProductByBarcode(barcode) {
    const code = String(barcode).trim();

    const product = productCatalog.find(item =>
        String(item.barcode || '').trim() === code
    );

    if (!product) {
        showAlert('warning', 'ไม่พบสินค้าในระบบ', `บาร์โค้ด: ${code}`);
        document.getElementById('barcodeInput')?.focus();
        return;
    }

    addProductToCart(getUnitId(product));
    document.getElementById('barcodeInput')?.focus();
}

/* ------------------------------
   ตะกร้าสินค้า
------------------------------ */

function renderProductList() {
    const tbody = document.getElementById('product-list');
    if (!tbody) return;

    if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-secondary py-4">ยังไม่มีสินค้าในตะกร้า</td></tr>';
        return;
    }

    tbody.innerHTML = products.map((product, index) => `
        <tr>
            <td>${index + 1}</td>
            <td class="text-start">
                <div class="fw-bold">${escapeHtml(product.name)}</div>
                <small class="text-secondary">${escapeHtml(product.barcode || '')}</small>
                ${product.freebie ? `<div class="small text-warning">แถม: ${escapeHtml(product.freebie)}</div>` : ''}
            </td>
            <td>${escapeHtml(product.unit || '-')}</td>
            <td>
                <input type="number" class="form-control form-control-sm text-center"
                    min="1" step="1" value="${Number(product.qty)}"
                    onchange="updateQty(${index}, this.value)">
            </td>
            <td>${formatMoney(product.price)}</td>
            <td class="fw-bold">${formatMoney(product.qty * product.price)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm"
                    onclick="removeProduct(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function removeProduct(index) {
    products.splice(index, 1);
    renderProductList();
    updateTotalAmount();
    updateQRCode();
}

function updateQty(index, qty) {
    const quantity = Math.max(1, Math.floor(Number(qty) || 1));
    if (!products[index]) return;

    products[index].qty = quantity;
    renderProductList();
    updateTotalAmount();
    updateQRCode();
}

function updateTotalAmount() {
    totalAmount = products.reduce((sum, product) =>
        sum + Number(product.qty) * Number(product.price), 0
    );

    document.getElementById('totalAmount').innerText = formatMoney(totalAmount) + ' บาท';
    document.getElementById('cartCount').innerText = products.length + ' รายการ';

    const hasProducts = products.length > 0 && totalAmount > 0;
    document.getElementById('payCashButton').disabled = !hasProducts;
    document.getElementById('payQRButton').disabled = !hasProducts;

    updateChange();
}

function updatePriceTypeForAll(type) {
    if (type === 'wholesale') {
        if (!products.length) {
            document.getElementById('retail').checked = true;
            showAlert('warning', 'ยังไม่มีสินค้า', 'กรุณาเพิ่มสินค้าก่อนเลือกขายส่ง');
            renderProductPicker();
            return;
        }

        const retailTotal = products.reduce((sum, item) =>
            sum + Number(item.qty) * Number(item.retail_price ?? item.price ?? 0), 0
        );

        if (retailTotal < wholesaleMinimumAmount) {
            document.getElementById('retail').checked = true;

            showAlert(
                'warning',
                'ยอดซื้อไม่ถึงขั้นต่ำขายส่ง',
                `ยอดซื้อ ${formatMoney(retailTotal)} บาท ต้องซื้อเพิ่ม ${formatMoney(wholesaleMinimumAmount - retailTotal)} บาท`
            );

            renderProductPicker();
            return;
        }
    }

    products.forEach(product => {
        product.price_type = type;
        product.price = getPrice(product, type);
    });

    renderProductList();
    updateTotalAmount();
    updateQRCode();
    renderProductPicker(document.getElementById('productSearch').value);
}

/* ------------------------------
   เงินสดและเงินทอน
------------------------------ */

function addCash(amount) {
    const input = document.getElementById('cash');
    input.value = (Number(input.value) || 0) + Number(amount);
    updateChange();
}

function clearCash() {
    document.getElementById('cash').value = '';
    updateChange();
}

function updateChange() {
    const cash = Number(document.getElementById('cash').value) || 0;
    const change = cash - totalAmount;
    document.getElementById('change').value =
        formatMoney(change > 0 ? change : 0) + ' บาท';
}

/* ------------------------------
   QR PromptPay
------------------------------ */

function updateQRCode() {
    const image = document.getElementById('qrImage');
    if (!image) return;

    if (totalAmount <= 0) {
        image.src = '';
        return;
    }

    image.src = `https://promptpay.io/${encodeURIComponent(shopPromptPayID)}/${totalAmount.toFixed(2)}.png`;
    document.getElementById('qrTotalAmount').innerText = formatMoney(totalAmount) + ' บาท';
}

function payQRCode() {
    if (!products.length || totalAmount <= 0) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าก่อนสร้าง QR Code');
    }

    updateQRCode();
    document.getElementById('qrBox').style.display = 'block';
    document.getElementById('confirmQRButton').classList.remove('d-none');
}

function confirmPaymentByQR() {
    if (!products.length) return;

    submitSaleData('qr', data => {
        playSound();

        if (autoPrintEnabled) {
            printReceipt(0, 0, 'คิวอาร์โค้ด / พร้อมเพย์');
        }

        clearCart();
        showAlert('success', 'รับชำระเรียบร้อย', 'บันทึกการขายผ่าน QR Code สำเร็จ');
    });
}

/* ------------------------------
   บันทึกการขาย
------------------------------ */

async function submitSaleData(paymentMethod, callback) {
    if (saleSubmissionInFlight) return;

    if (!products.length) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าก่อนชำระเงิน');
    }

    if (products.some(item => !getUnitId(item))) {
        return showAlert('error', 'ข้อมูลสินค้าไม่ถูกต้อง', 'มีสินค้าที่ไม่มีรหัสหน่วยสินค้า');
    }

    saleSubmissionInFlight = true;

    const priceType = getPriceType();

    try {
        const response = await fetch(saleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                request_token: posSaleRequestToken,
                payment_method: paymentMethod,
                price_type: priceType,
                products: products.map(item => ({
                    product_unit_id: getUnitId(item),
                    qty: Number(item.qty)
                }))
            })
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (error) {
            console.error('ข้อมูลตอบกลับไม่ใช่ JSON:', text);
            throw new Error('เซิร์ฟเวอร์ส่งข้อมูลกลับมาไม่ถูกต้อง');
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'ไม่สามารถบันทึกการขายได้');
        }

        if (Array.isArray(data.items)) {
            const previousProducts = [...products];

            products = data.items.map(item => {
                const unitId = Number(item.product_unit_id);
                const current = previousProducts.find(product =>
                    getUnitId(product) === unitId
                );
                const catalog = productCatalog.find(product =>
                    getUnitId(product) === unitId
                );

                return {
                    ...catalog,
                    ...current,
                    product_unit_id: unitId,
                    unit_id: unitId,
                    qty: Number(item.quantity),
                    price: Number(item.price),
                    price_type: priceType
                };
            });
        }

        if (data.total !== undefined) {
            totalAmount = Number(data.total);
        }

        if (data.next_request_token) {
            posSaleRequestToken = data.next_request_token;
        }

        renderProductList();
        updateTotalAmount();
        updateQRCode();

        if (typeof callback === 'function') {
            callback(data);
        }

    } catch (error) {
        console.error('บันทึกการขายผิดพลาด:', error);
        showAlert('error', 'บันทึกการขายไม่สำเร็จ', error.message || 'กรุณาลองใหม่อีกครั้ง');
    } finally {
        saleSubmissionInFlight = false;
    }
}

async function payCash() {
    if (!products.length || totalAmount <= 0) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าก่อนชำระเงิน');
    }

    const cash = Number(document.getElementById('cash').value) || 0;
    const amountBeforeSale = totalAmount;

    if (cash < amountBeforeSale) {
        return showAlert('error', 'เงินสดไม่พอ', 'กรุณารับเงินให้ครบตามยอดรวม');
    }

    const result = await Swal.fire({
        title: 'ยืนยันการชำระเงิน?',
        html: `ยอดรวม <strong>${formatMoney(amountBeforeSale)} บาท</strong><br>รับเงิน ${formatMoney(cash)} บาท<br>เงินทอน ${formatMoney(cash - amountBeforeSale)} บาท`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    });

    if (!result.isConfirmed) return;

    submitSaleData('cash', data => {
        const serverTotal = Number(data.total ?? amountBeforeSale);
        const change = cash - serverTotal;

        playSound();

        if (autoPrintEnabled) {
            printReceipt(cash, change, 'เงินสด');
        }

        clearCart();
        showAlert('success', 'ชำระเงินสดสำเร็จ', `เงินทอน ${formatMoney(change)} บาท`);
    });
}

/* ------------------------------
   พิมพ์ใบเสร็จ
------------------------------ */

function printReceipt(cash, change, paymentMethod) {
    const receiptWindow = window.open('', '', 'width=400,height=600');

    if (!receiptWindow) {
        showAlert('warning', 'ไม่สามารถเปิดหน้าพิมพ์ได้', 'กรุณาอนุญาต Popup ในเบราว์เซอร์');
        return;
    }

    const receiptItems = products.map((product, index) => `
        <tr>
            <td>${index + 1}. ${escapeHtml(product.name)}<br>
                ${product.qty} ${escapeHtml(product.unit || '')} x ${formatMoney(product.price)}
            </td>
            <td style="text-align:right">${formatMoney(product.qty * product.price)}</td>
        </tr>
    `).join('');

    const content = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>ใบเสร็จ</title>
        <style>
            @page {size:58mm auto;margin:2mm}
            body {width:54mm;margin:0;font-family:Tahoma,sans-serif;font-size:11px;color:#000}
            h3 {text-align:center;font-size:16px;margin:5px 0}
            p {margin:4px 0}
            hr {border:0;border-top:1px dashed #000}
            table {width:100%;font-size:10px;border-collapse:collapse}
            td {padding:3px 0;vertical-align:top}
        </style>
    </head>
    <body>
        <h3>{{ $shopSettings->shop_name ?? 'Chalini Store' }}</h3>
        <p>วันที่: ${new Date().toLocaleString('th-TH')}</p>
        <p>พนักงาน: ${escapeHtml(sellerName)}</p>
        <p>ชำระโดย: ${escapeHtml(paymentMethod)}</p>
        <hr>
        <table>${receiptItems}</table>
        <hr>
        <p>ยอดรวม: ${formatMoney(totalAmount)} บาท</p>
        ${cash > 0 ? `<p>รับเงิน: ${formatMoney(cash)} บาท</p><p>เงินทอน: ${formatMoney(change)} บาท</p>` : ''}
        <hr>
        <p style="text-align:center">ขอบคุณที่ใช้บริการ</p>
    </body>
    </html>`;

    receiptWindow.document.open();
    receiptWindow.document.write(content);
    receiptWindow.document.close();
    receiptWindow.focus();

    setTimeout(() => {
        receiptWindow.print();
        receiptWindow.close();
    }, 300);
}

function playSound() {
    const audio = new Audio('/sounds/cash.mp3');
    audio.play().catch(error => console.warn('เล่นเสียงไม่ได้:', error));
}

function clearCart() {
    products = [];
    renderProductList();
    updateTotalAmount();
    updateQRCode();

    document.getElementById('cash').value = '';
    document.getElementById('qrBox').style.display = 'none';
    document.getElementById('confirmQRButton').classList.add('d-none');
    document.getElementById('barcodeInput').value = '';
    document.getElementById('barcodeInput').focus();
}

/* ------------------------------
   พักบิล / เรียกคืนบิล
------------------------------ */

function holdBill() {
    if (!products.length) {
        return showAlert('warning', 'ไม่มีสินค้า', 'ไม่สามารถพักบิลว่างได้');
    }

    heldBills.push({
        id: Date.now(),
        created_at: new Date().toLocaleTimeString('th-TH'),
        items: JSON.parse(JSON.stringify(products)),
        total: totalAmount,
        price_type: getPriceType()
    });

    updateHeldBadge();
    clearCart();
    showAlert('success', 'พักบิลสำเร็จ', 'สามารถเรียกคืนบิลได้จากปุ่มบิลที่พัก');
}

function updateHeldBadge() {
    const badge = document.getElementById('heldCount');
    badge.innerText = heldBills.length;
    badge.style.display = heldBills.length ? 'inline-block' : 'none';
}

function showHeldBills() {
    if (!heldBills.length) {
        return showAlert('info', 'ไม่มีบิลที่พักไว้');
    }

    const html = heldBills.map((bill, index) => `
        <div class="border rounded-3 p-3 mb-2 text-start">
            <div class="fw-bold text-primary">บิลที่ #${index + 1}</div>
            <div class="small text-secondary">${escapeHtml(bill.created_at)}</div>
            <div class="my-2">สินค้า ${bill.items.length} รายการ</div>
            <div class="fw-bold">ยอดรวม ${formatMoney(bill.total)} บาท</div>
            <div class="mt-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary"
                    onclick="restoreBill(${bill.id})">เรียกคืน</button>
                <button class="btn btn-sm btn-outline-danger"
                    onclick="deleteHeldBill(${bill.id})">ลบ</button>
            </div>
        </div>
    `).join('');

    Swal.fire({
        title: 'รายการบิลที่พักไว้',
        html,
        width: 520,
        showConfirmButton: false,
        showCloseButton: true
    });
}

function restoreBill(id) {
    const bill = heldBills.find(item => item.id === id);
    if (!bill) return;

    if (products.length) {
        const result = confirm('มีสินค้าในตะกร้าปัจจุบัน ต้องการแทนที่ด้วยบิลที่เรียกคืนหรือไม่?');
        if (!result) return;
    }

    products = JSON.parse(JSON.stringify(bill.items));
    document.getElementById(bill.price_type === 'wholesale' ? 'wholesale' : 'retail').checked = true;

    heldBills = heldBills.filter(item => item.id !== id);
    updateHeldBadge();

    renderProductList();
    updateTotalAmount();
    updateQRCode();
    renderProductPicker();

    Swal.close();
    showAlert('success', 'เรียกคืนบิลสำเร็จ');
}

function deleteHeldBill(id) {
    heldBills = heldBills.filter(item => item.id !== id);
    updateHeldBadge();
    showHeldBills();
}

/* ------------------------------
   ขายเงินเชื่อ
------------------------------ */

function openCreditModal() {
    if (!products.length || totalAmount <= 0) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าก่อนขายเงินเชื่อ');
    }

    document.getElementById('creditTotal').innerText = formatMoney(totalAmount);
    document.getElementById('creditCustomer').value = '';
    document.getElementById('creditDueDate').value = '';
    toggleAddCustomerForm(false);

    bootstrap.Modal.getOrCreateInstance(document.getElementById('creditModal')).show();
}

function toggleAddCustomerForm(show = null) {
    const box = document.getElementById('addCustomerBox');

    if (show === true || (show === null && box.classList.contains('d-none'))) {
        box.classList.remove('d-none');
        document.getElementById('newCustomerName').focus();
    } else {
        box.classList.add('d-none');
        document.getElementById('newCustomerName').value = '';
        document.getElementById('newCustomerPhone').value = '';
    }
}

async function addCustomer() {
    const name = document.getElementById('newCustomerName').value.trim();
    const phone = document.getElementById('newCustomerPhone').value.trim();

    if (!name) {
        return showAlert('warning', 'กรุณากรอกชื่อลูกค้า');
    }

    try {
        const response = await fetch('/members/customers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({name, phone})
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'ไม่สามารถเพิ่มลูกค้าได้');
        }

        const customer = data.customer;
        const select = document.getElementById('creditCustomer');
        const option = document.createElement('option');

        option.value = customer.id;
        option.textContent = `${customer.name} (ยอดค้างเดิม: 0.00 บ.)`;
        option.dataset.active = '1';
        option.selected = true;

        select.appendChild(option);
        toggleAddCustomerForm(false);

        showAlert('success', 'เพิ่มลูกค้าสำเร็จ', `เพิ่ม ${name} เรียบร้อยแล้ว`);

    } catch (error) {
        showAlert('error', 'เพิ่มลูกค้าไม่สำเร็จ', error.message);
    }
}

async function confirmCreditSale() {
    const customerSelect = document.getElementById('creditCustomer');
    const customerId = customerSelect.value;
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    const dueDate = document.getElementById('creditDueDate').value;

    if (!customerId) {
        return showAlert('warning', 'กรุณาเลือกลูกค้า');
    }

    if (selectedOption.dataset.active === '0') {
        return showAlert('error', 'ลูกค้าถูกระงับ', 'ไม่สามารถขายเงินเชื่อให้ลูกค้ารายนี้ได้');
    }

    if (!dueDate) {
        return showAlert('warning', 'กรุณาเลือกวันครบกำหนดชำระ');
    }

    if (!products.length) {
        return showAlert('warning', 'ไม่มีสินค้าในบิล');
    }

    const result = await Swal.fire({
        title: 'ยืนยันการขายเงินเชื่อ?',
        html: `ลูกค้า: <strong>${escapeHtml(selectedOption.textContent)}</strong><br>ยอดหนี้: <strong>${formatMoney(totalAmount)} บาท</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันบันทึกหนี้',
        cancelButtonText: 'ยกเลิก'
    });

    if (!result.isConfirmed) return;

    Swal.fire({
        title: 'กำลังบันทึกข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch('/staff/credit-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                customer_id: customerId,
                total_price: totalAmount,
                payment_method: 'credit',
                due_date: dueDate,
                products: products.map(product => ({
                    product_unit_id: getUnitId(product),
                    qty: Number(product.qty),
                    price: Number(product.price)
                }))
            })
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (error) {
            throw new Error('เซิร์ฟเวอร์ส่งข้อมูลกลับมาไม่ถูกต้อง');
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'ไม่สามารถบันทึกเงินเชื่อได้');
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('creditModal')).hide();

        if (autoPrintEnabled) {
            printReceipt(0, 0, 'เงินเชื่อ');
        }

        playSound();
        clearCart();
        showAlert('success', 'ขายเงินเชื่อสำเร็จ', 'บันทึกยอดหนี้เรียบร้อยแล้ว');

    } catch (error) {
        Swal.close();
        showAlert('error', 'บันทึกเงินเชื่อไม่สำเร็จ', error.message);
    }
}

/* ------------------------------
   ตั้งค่าพิมพ์อัตโนมัติ
------------------------------ */

function toggleAutoPrint(enabled) {
    autoPrintEnabled = enabled;
    localStorage.setItem('autoPrintEnabled', String(enabled));

    showAlert(
        enabled ? 'success' : 'info',
        enabled ? 'เปิดการพิมพ์ใบเสร็จอัตโนมัติแล้ว' : 'ปิดการพิมพ์ใบเสร็จอัตโนมัติแล้ว'
    );
}

/* ------------------------------
   เริ่มต้นระบบ
------------------------------ */

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('autoPrintSwitch').checked = autoPrintEnabled;

    document.getElementById('productSearch').addEventListener('input', event => {
        renderProductPicker(event.target.value);
    });

    document.getElementById('barcodeInput').addEventListener('keydown', event => {
        if (event.key !== 'Enter') return;

        event.preventDefault();
        event.stopPropagation();

        const barcode = event.target.value.trim();
        event.target.value = '';

        if (barcode) addProductByBarcode(barcode);
    });

    document.querySelectorAll('input[name="priceType"]').forEach(input => {
        input.addEventListener('change', () => updatePriceTypeForAll(input.value));
    });

    document.getElementById('cash').addEventListener('input', updateChange);

    renderProductPicker();
    renderProductList();
    updateTotalAmount();
    updateHeldBadge();
});
const pickerCollapse = document.getElementById('productPickerCollapse');
const pickerToggle = document.querySelector('.pos-picker-toggle');

pickerCollapse.addEventListener('show.bs.collapse', () => {
    pickerToggle.setAttribute('aria-expanded', 'true');
});

pickerCollapse.addEventListener('hide.bs.collapse', () => {
    pickerToggle.setAttribute('aria-expanded', 'false');
});
</script>
@endsection
