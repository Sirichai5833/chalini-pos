@extends('layouts.layout')

@section('content')
    <style>
        :root {
            --pos-yellow: #ffd43b;
            --pos-yellow-dark: #caa400;
            --pos-black: #080808;
            --pos-surface: #111111;
            --pos-card: #181818;
            --pos-soft: #222222;
            --pos-border: rgba(255, 212, 59, .22);
            --pos-text: #f7f7f7;
            --pos-muted: #aaa;
            --pos-success: #22c55e;
            --pos-danger: #ef4444;
            --pos-info: #38bdf8;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--pos-black);
            color: var(--pos-text);
        }

        .pos-page {
            color: var(--pos-text);
        }

        .pos-hero {
            position: relative;
            overflow: hidden;
            padding: 1.35rem 1.5rem;
            margin: 0 1rem 1.25rem;
            border: 1px solid var(--pos-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #070707 0%, #171717 58%, #2c2400 100%);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .36);
        }

        .pos-hero::after {
            content: "";
            position: absolute;
            right: -75px;
            bottom: -120px;
            width: 265px;
            height: 265px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .10);
            pointer-events: none;
        }

        .pos-hero-content {
            position: relative;
            z-index: 1;
        }

        .pos-title {
            margin: 0;
            color: var(--pos-yellow);
            font-weight: 950;
        }

        .pos-subtitle {
            margin: .35rem 0 0;
            color: #d2d2d2;
            font-size: .9rem;
        }

        .pos-user-badge {
            border: 1px solid var(--pos-border);
            background: rgba(255, 212, 59, .11);
            color: var(--pos-yellow);
            font-weight: 850;
        }

        .pos-main-card,
        .payment-card {
            overflow: hidden;
            border: 1px solid #2d2d2d !important;
            border-radius: 22px !important;
            background: linear-gradient(180deg, #191919, #111);
            color: var(--pos-text);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .34) !important;
        }

        .pos-main-card .card-body,
        .payment-card .card-body {
            background: transparent !important;
        }

        .section-label {
            color: var(--pos-yellow);
            font-weight: 900;
        }

        .form-label {
            color: #ddd;
            font-weight: 800;
        }

        .form-control,
        .form-select,
        .input-group-text {
            min-height: 46px;
            border-color: #3a3a3a;
            background: #131313;
            color: #fff;
            box-shadow: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pos-yellow);
            background: #171717;
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
        }

        .form-control::placeholder {
            color: #777;
        }

        .form-select option {
            background: #171717;
            color: #fff;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .sale-type-box {
            padding: .8rem 1rem;
            border: 1px solid var(--pos-border);
            border-radius: 16px;
            background: rgba(255, 212, 59, .05);
        }

        .form-check-input {
            border-color: #666;
            background-color: #111;
        }

        .form-check-input:checked {
            border-color: var(--pos-yellow);
            background-color: var(--pos-yellow);
        }

        .barcode-box {
            position: relative;
        }

        .barcode-box::before {
            content: "\f6a5";
            position: absolute;
            top: 50%;
            left: 14px;
            z-index: 2;
            color: var(--pos-yellow);
            font-family: "bootstrap-icons";
            transform: translateY(-50%);
        }

        .barcode-box input {
            padding-left: 2.6rem;
        }

        #productTable {
            --bs-table-bg: transparent;
            --bs-table-color: var(--pos-text);
            --bs-table-border-color: #303030;
            --bs-table-hover-bg: rgba(255, 212, 59, .05);
            --bs-table-hover-color: #fff;
            margin-bottom: 0;
        }

        #productTable thead th {
            border-color: #303030;
            background: #101010 !important;
            color: var(--pos-yellow);
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        #productTable td {
            border-color: #303030;
            color: #eee;
            vertical-align: middle;
        }

        #productTable .form-control {
            min-height: 38px;
            max-width: 82px;
            margin: auto;
        }

        .payment-card .card-header {
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--pos-border) !important;
            background:
                linear-gradient(135deg, rgba(255, 212, 59, .12), rgba(255, 212, 59, .03)),
                #111 !important;
            color: var(--pos-yellow) !important;
        }

        .auto-print-box {
            border: 1px solid var(--pos-border) !important;
            background: #121212 !important;
            color: #ddd;
        }

        .auto-print-box label {
            color: #ddd !important;
        }

        .form-switch .form-check-input {
            width: 2.6rem;
            height: 1.35rem;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--pos-yellow);
            border-color: var(--pos-yellow);
        }

        .total-box {
            padding: 1rem;
            border: 1px solid var(--pos-border) !important;
            border-radius: 18px !important;
            background: rgba(255, 212, 59, .07) !important;
            color: var(--pos-text);
        }

        #totalAmount {
            color: var(--pos-yellow) !important;
            text-shadow: 0 5px 18px rgba(255, 212, 59, .12);
        }

        .quick-cash-grid .btn {
            min-width: 62px;
            min-height: 40px;
            border: 1px solid #3b3b3b !important;
            border-radius: 12px;
            background: #171717 !important;
            color: #ddd !important;
            font-weight: 850;
        }

        .quick-cash-grid .btn:hover {
            border-color: var(--pos-yellow) !important;
            background: var(--pos-yellow) !important;
            color: #111 !important;
            transform: translateY(-1px);
        }

        .change-box {
            border: 1px solid #2f2f2f !important;
            border-radius: 18px !important;
            background: #121212 !important;
        }

        #change {
            color: var(--pos-yellow) !important;
        }

        .payment-actions .btn,
        .secondary-actions .btn {
            border-radius: 14px !important;
            font-weight: 900;
            transition: .2s ease;
        }

        .btn-pay-cash {
            border: 1px solid var(--pos-success);
            background: linear-gradient(135deg, #4ade80, var(--pos-success));
            color: #07130b;
        }

        .btn-pay-credit {
            border: 1px solid var(--pos-yellow);
            background: rgba(255, 212, 59, .08);
            color: var(--pos-yellow);
        }

        .btn-pay-qr {
            border: 1px solid var(--pos-info);
            background: linear-gradient(135deg, #7dd3fc, var(--pos-info));
            color: #06141a;
        }

        .btn-pay-cash:hover,
        .btn-pay-credit:hover,
        .btn-pay-qr:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        #qrBox {
            border: 1px solid var(--pos-border) !important;
            background: #101010 !important;
            color: #fff;
        }

        #qrBox .bg-white,
        #qrBox .bg-light {
            background: #fff !important;
            color: #111 !important;
        }

        #qrTotalAmount {
            color: var(--pos-yellow) !important;
        }

        .modal-content {
            border: 1px solid var(--pos-border) !important;
            background: #151515;
            color: var(--pos-text);
        }

        .modal-header,
        .modal-footer {
            border-color: #303030 !important;
            background: #101010 !important;
        }

        .modal-title {
            color: var(--pos-yellow);
        }

        .modal-body {
            background: #151515 !important;
        }

        .modal-body .card {
            border: 1px solid #303030 !important;
            background: #111 !important;
        }

        .modal-body .text-secondary,
        .modal-body .text-muted {
            color: var(--pos-muted) !important;
        }

        @media (max-width: 991.98px) {
            .payment-column {
                margin-top: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .pos-hero {
                margin-inline: .75rem;
                padding: 1.15rem;
                border-radius: 19px;
            }

            .pos-hero .d-flex {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .pos-main-card,
            .payment-card {
                border-radius: 18px !important;
            }

            .quick-cash-grid .btn {
                flex: 1 0 calc(25% - .5rem);
            }
        }
    </style>

    <section class="pos-hero">
        <div class="pos-hero-content d-flex align-items-center justify-content-between gap-3">
            <div>
                <h2 class="pos-title">
                    <i class="bi bi-cart-check-fill me-2"></i>
                    ระบบขายหน้าร้าน
                </h2>
                <p class="pos-subtitle">
                    สแกนสินค้า รับชำระเงิน และจัดการบิลจากหน้าจอเดียว
                </p>
            </div>

            <span class="badge rounded-pill px-3 py-2 pos-user-badge">
                <i class="bi bi-person-badge-fill me-1"></i>
                {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}
            </span>
        </div>
    </section>

    @if (session('success'))
        <x-sweet-alert icon="success" title="ดำเนินการสำเร็จ" text="{{ session('success') }}" confirm-button-text="ตกลง" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="เกิดข้อผิดพลาด" text="{{ session('error') }}" confirm-button-text="ตกลง" />
    @endif

    <div class="container-fluid py-4 px-0 pos-page">
        <div class="row g-0 m-0">
            <!-- Product Table -->
            <div class="col-lg-8 col-12 px-3">
                <div class="card pos-main-card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">ชื่อพนักงาน: {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}</h5>
                        <h6 class="mb-4">วันที่: {{ date('d/m/Y') }}</h6>

                        <h4 class="mb-3 section-label"><i class="bi bi-box-seam-fill me-2"></i>รายการสินค้า</h4>
                        <div class="mb-3 sale-type-box">
                            <label class="form-label">ประเภทการขาย</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="priceType" id="retail"
                                        value="retail" checked onchange="updatePriceTypeForAll(this.value)">
                                    <label class="form-check-label" for="retail">ปลีก</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="priceType" id="wholesale"
                                        value="wholesale" onchange="updatePriceTypeForAll(this.value)">
                                    <label class="form-check-label" for="wholesale">ส่ง</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 barcode-box">
                            <label class="form-label">สแกน / พิมพ์บาร์โค้ด</label>
                            <input type="text" id="barcodeInput" class="form-control"
                                placeholder="พิมพ์บาร์โค้ดแล้วกด Enter">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center align-middle" id="productTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>ของแถม</th>
                                        <th>หน่วย</th>
                                        <th>จำนวน</th>
                                        <th>ราคาสินค้า</th>
                                        <th>ราคารวม</th>
                                        <th>ลบ</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list">
                                    <!-- Products will be populated via JS -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12 px-3 payment-column">
    <div class="card payment-card h-100">
        <div class="card-header bg-dark text-white py-3 border-0">
            <h4 class="card-title mb-0 d-flex align-items-center justify-content-between" style="font-weight: 600;">
                <span><i class="bi bi-wallet2 me-2"></i> ชำระเงิน</span>
                <span class="badge bg-secondary fs-6 fw-normal">POS System</span>
            </h4>
        </div>

        <!-- 🖨️ สวิตช์เปิด-ปิด พิมพ์ใบเสร็จ -->
<div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mb-3 auto-print-box">
    <label for="autoPrintSwitch" class="fw-bold text-secondary mb-0" style="cursor: pointer;">
        🖨️ พิมพ์ใบเสร็จอัตโนมัติ
    </label>
    <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" id="autoPrintSwitch" role="switch" style="cursor: pointer; transform: scale(1.2);" onchange="toggleAutoPrint(this.checked)">
    </div>
</div>

        <div class="card-body p-4 bg-light/30">
            <div class="p-3 mb-4 text-center total-box">
                <span class="text-muted small text-uppercase fw-bold tracking-wider">ยอดรวมทั้งหมดที่ต้องชำระ</span>
                <h1 class="text-success my-2 fw-bolder" id="totalAmount" style="font-size: 2.5rem;">0 บาท</h1>
            </div>

            <div class="mb-3">
                <label for="cash" class="form-label fw-bold text-secondary">เงินที่รับมา (เงินสด)</label>
                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                    <span class="input-group-text bg-white border-end-0 text-muted">฿</span>
                    <input type="number" class="form-control border-start-0 fw-bold text-end" id="cash"
                        placeholder="0.00" oninput="updateChange()" style="font-size: 1.5rem;">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted mb-2">💵 ทางลัดเงินสด (บาท)</label>
                <div class="d-flex flex-wrap gap-2 quick-cash-grid">
                    <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addCash(1)">+1</button>
                    <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addCash(2)">+2</button>
                    <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addCash(5)">+5</button>
                    <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="addCash(10)">+10</button>
                    <button type="button" class="btn btn-sm text-success border-success bg-success bg-opacity-10 flex-grow-1 fw-bold" onclick="addCash(20)">+20</button>
                    <button type="button" class="btn btn-sm text-info border-info bg-info bg-opacity-10 flex-grow-1 fw-bold" onclick="addCash(50)">+50</button>
                    <button type="button" class="btn btn-sm text-danger border-danger bg-danger bg-opacity-10 flex-grow-1 fw-bold" onclick="addCash(100)">+100</button>
                    <button type="button" class="btn btn-sm text-warning border-warning bg-warning bg-opacity-10 flex-grow-1 fw-bold" onclick="addCash(500)" style="color: #6f42c1 !important; border-color: #6f42c1 !important; background-color: rgba(111, 66, 193, 0.1) !important;">+500</button>
                    <button type="button" class="btn btn-sm text-secondary border-secondary bg-secondary bg-opacity-10 flex-grow-1 fw-bold" onclick="addCash(1000)">+1000</button>
                    <button type="button" class="btn btn-sm btn-danger fw-bold px-3" onclick="clearCash()">ล้าง</button>
                </div>
            </div>

            <div class="mb-4 p-3 change-box">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="change" class="form-label mb-0 fw-bold text-secondary">เงินทอน</label>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">THB</span>
                </div>
                <input type="text" class="form-control form-control-lg text-end fw-bolder text-success border-0 bg-transparent p-0 fs-3"
                    id="change" readonly value="0.00 บาท">
            </div>

            <hr class="my-4 opacity-10">

            <div class="d-grid gap-2 payment-actions">
                <button class="btn btn-pay-cash btn-lg py-3 d-flex align-items-center justify-content-center gap-2" onclick="payCash()">
                    <span>💵 ชำระด้วยเงินสด</span>
                </button>
                {{-- <button class="btn btn-pay-credit btn-lg py-2 d-flex align-items-center justify-content-center gap-2" onclick="openCreditModal()">
                    <span>💳 ขายเงินเชื่อ (ค้างชำระ)</span>
                </button> --}}
                <button class="btn btn-pay-qr btn-lg py-3 d-flex align-items-center justify-content-center gap-2" onclick="payQRCode()">
                    <span>📱 ชำระด้วย QR Code</span>
                </button>
                
                <button id="confirmQRButton" class="btn btn-info btn-lg py-3 text-white fw-bold d-none mt-2 shadow-sm rounded-3 animated pulse infinite"
                    onclick="confirmPaymentByQR()">
                    ✅ ยืนยันรับเงินโอนแล้ว
                </button>
            </div>

            <div class="d-flex mt-3 gap-2 secondary-actions">
                <button class="btn btn-light border text-secondary w-50 py-2 rounded-3 fw-bold" onclick="holdBill()">
                    📥 พักบิลนี้
                </button>
                <button class="btn btn-dark w-50 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" onclick="showHeldBills()">
                    📋 บิลที่พักไว้ 
                    <span class="badge bg-danger rounded-circle" id="heldCount" style="display:none;">0</span>
                </button>
            </div>

            <div class="text-center mt-2">
                <div id="qrBox" class="card shadow border-0 text-center p-4 mt-3"
                    style="display:none; border-radius:24px; background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);">

                    <div class="mb-3 position-relative d-inline-block mx-auto">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow"
                            style="width:64px; height:64px; font-size:26px; font-weight:800; border: 4px solid #fff;">
                            C
                        </div>
                    </div>

                   <h5 class="fw-bold mb-0 text-white">Store</h5>

<div class="text-white small mb-3">
    <i class="bi bi-person-circle"></i>
    prompt pay ID:
    {{ \DB::table('settings')->where('key', 'promptpay')->value('value') ?? '0843860015' }}
</div>

                    <div class="bg-white rounded-4 p-3 d-inline-block mx-auto shadow-sm border mb-3">
                        <img id="qrImage" src="" alt="QR Code" class="img-fluid" style="max-width:200px; min-height:200px;">
                    </div>

                    <div class="bg-light p-3 rounded-3 border border-dashed mb-2">
                        <div class="text-muted small mb-1">ยอดรวมที่ต้องสแกนจ่าย</div>
                        <h3 class="fw-bolder text-primary mb-0" id="qrTotalAmount">0.00 บาท</h3>
                    </div>

                    <div class="small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> กรุณาสแกน QR เพื่อโอนเงินเข้าบัญชีร้าน
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const shopPromptPayID = @json(\DB::table('settings')->where('key', 'promptpay')->value('value') ?? '0843860015');
    const sellerName = @json(Auth::user()->name ?? 'ผู้ใช้งาน');
    const sellerId = @json(Auth::user()->id ?? '-');
    let products = [];
    let heldBills = [];
    let totalAmount = 0;
    let posSaleRequestToken =
        @json((string) \Illuminate\Support\Str::uuid());
    let saleSubmissionInFlight = false;
    const productCatalog = @json($products ?? []);
    let isAlertOpen = false;

    // สถานะเปิด/ปิดการพิมพ์ใบเสร็จ
let autoPrintEnabled = localStorage.getItem('autoPrintEnabled') === 'true';

// โหลดสถานะปุ่มเมื่อเปิดหน้า
document.addEventListener('DOMContentLoaded', () => {
    const printSwitch = document.getElementById('autoPrintSwitch');

    if (printSwitch) {
        printSwitch.checked = autoPrintEnabled;
    }
});

// ฟังก์ชันเปิด/ปิดการพิมพ์
function toggleAutoPrint(enabled) {
    autoPrintEnabled = enabled;

    // จำค่าการตั้งค่าไว้ แม้รีเฟรชหน้า
    localStorage.setItem('autoPrintEnabled', enabled);

    showAlert(
        enabled ? 'success' : 'info',
        enabled ? 'เปิดการพิมพ์ใบเสร็จแล้ว' : 'ปิดการพิมพ์ใบเสร็จแล้ว'
    );
}

    // ระบบปุ่มลัดจัดการเงินสด
    function addCash(amount) {
        const cashInput = document.getElementById('cash');
        let currentCash = parseFloat(cashInput.value) || 0;
        cashInput.value = currentCash + amount;
        updateChange();
    }

    function clearCash() {
        document.getElementById('cash').value = '';
        updateChange();
    }

    function clearCart() {
        products = [];
        updateTotalAmount();
        clearCash();
    }

    // ฟังก์ชันคำนวณเงินทอน
    function updateChange() {
        const cashInput = document.getElementById('cash');
        const changeInput = document.getElementById('change');
        let cash = parseFloat(cashInput.value) || 0;
        
        let change = cash - totalAmount;
        if (change < 0 || cash === 0) {
            changeInput.value = "0.00 บาท";
            changeInput.classList.remove('text-success');
            changeInput.classList.add('text-danger');
        } else {
            changeInput.value = change.toFixed(2) + " บาท";
            changeInput.classList.remove('text-danger');
            changeInput.classList.add('text-success');
        }
    }

    // ฟังก์ชันอัปเดตยอดรวม
    function updateTotalAmount() {
        document.getElementById('totalAmount').innerText = totalAmount.toFixed(2) + " บาท";
        updateChange();
    }

    // 📱 ฟังก์ชันกดจ่ายด้วย QR Code ให้แสดงเป็น Popup (SweetAlert2)
    function payQRCode() {
        if (totalAmount <= 0) {
            showAlert('warning', 'ไม่สามารถชำระเงินได้', 'ยอดรวมต้องมากกว่า 0 บาท');
            return;
        }

        // สร้าง URL ของ PromptPay QR Code ล่าสุดตามยอดเงินจริง
        const qrUrl = `https://promptpay.io/${shopPromptPayID}/${totalAmount.toFixed(2)}.png`;

        // ดีไซน์หน้าต่างสลิปแอปธนาคารบน Popup
        const qrHtml = `
            <div class="text-center p-2" style="font-family: 'Kanit', sans-serif;">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow-sm"
                        style="width:60px; height:60px; font-size:24px; font-weight:bold; border: 3px solid #fff;">
                        C
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">Store</h4>
                <div class="text-muted small mb-3">
                    <i class="bi bi-person-circle"></i> เจ้าของร้าน: สิริชัย
                </div>

                <div class="bg-white rounded-4 p-3 d-inline-block shadow-sm border mb-3">
                    <img src="${qrUrl}" alt="PromptPay QR Code" class="img-fluid" style="max-width:220px; min-height:220px;">
                </div>

                <div class="bg-light p-3 rounded-3 border border-dashed mb-3">
                    <div class="text-muted small mb-1">ยอดเงินที่ต้องสแกนจ่าย</div>
                    <h2 class="fw-bolder text-success mb-0">${totalAmount.toFixed(2)} บาท</h2>
                </div>

                <div class="small text-secondary mb-1">
                    <i class="bi bi-info-circle-fill text-primary"></i> ลูกค้าสแกนเสร็จแล้ว รบกวนตรวจสอบยอดเงินในแอปธนาคารของร้านก่อนกดยืนยัน
                </div>
            </div>
        `;

        // แสดงหน้าต่าง Popup
        Swal.fire({
            html: qrHtml,
            width: 450,
            showCancelButton: true,
            confirmButtonText: '✅ ยืนยันรับเงินโอนแล้ว',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545',
            focusConfirm: true,
            allowOutsideClick: false // ป้องกันการกดปิดพร่ำเพรื่อเพื่อความปลอดภัย
        }).then((result) => {
            if (result.isConfirmed) {
                confirmPaymentByQR();
            }
        });
    }

    function confirmPaymentByQR() {
        showAlert('success', 'สำเร็จ!', 'ระบบบันทึกการชำระเงินผ่าน QR Code เรียบร้อยแล้ว');
        clearCart();
    }

    function payCash() {
        const cash = parseFloat(document.getElementById('cash').value) || 0;
        if (cash < totalAmount) {
            showAlert('error', 'เงินสดไม่พอ', 'กรุณารับเงินจากลูกค้าให้ครบตามยอดรวม');
            return;
        }
        showAlert('success', 'ชำระเงินสำเร็จ', 'ทำรายการสำเร็จและเปิดลิ้นชักเก็บเงินแล้ว');
        clearCart();
    }

    function openCreditModal() {
        showAlert('info', 'ระบบขายเงินเชื่อ', 'เปิดโมดอลเลือกลูกค้าค้างชำระ (เพิ่มฟังก์ชันการทำงานตรงนี้)');
    }

    // แจ้งเตือนพื้นฐาน
    function showAlert(icon, title, text = '') {
        return Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#198754',
            allowOutsideClick: false,
            allowEscapeKey: true,
            allowEnterKey: false,
            showConfirmButton: true,
            timer: undefined,
            timerProgressBar: false
        });
    }

    // --- ระบบพักบิลและเรียกคืน ---
    function holdBill() {
        if (products.length === 0) {
            showAlert('warning', 'ไม่มีสินค้า', 'ไม่สามารถพักบิลว่างได้');
            return;
        }

        const bill = {
            id: Date.now(),
            created_at: new Date().toLocaleTimeString(),
            items: [...products],
            total: totalAmount
        };

        heldBills.push(bill);
        updateHeldBadge();
        clearCart();

        showAlert('success', 'พักบิลสำเร็จ', `พักบิลไว้แล้ว (${bill.items.length} รายการ)`);
    }

    function updateHeldBadge() {
        const badge = document.getElementById('heldCount');
        if (badge) {
            if (heldBills.length > 0) {
                badge.innerText = heldBills.length;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    function restoreBill(id) {
        const index = heldBills.findIndex(b => b.id === id);
        if (index !== -1) {
            products = [...heldBills[index].items];
            totalAmount = heldBills[index].total;
            heldBills.splice(index, 1);
            updateHeldBadge();
            
            if (typeof renderProductList === "function") renderProductList();
            
            updateTotalAmount();
            Swal.close();
            showAlert('success', 'เรียกคืนบิลสำเร็จ');
        }
    }

    function deleteHeldBill(id) {
        heldBills = heldBills.filter(b => b.id !== id);
        updateHeldBadge();
        showHeldBills();
    }

    function showHeldBills() {
        if (heldBills.length === 0) {
            showAlert('info', 'ไม่มีบิลที่พักไว้');
            return;
        }

        let html = '<div class="list-group">';
        heldBills.forEach((bill, index) => {
            html += `
                <div class="list-group-item list-group-item-action text-start p-3 mb-2 border rounded shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-primary">📋 บิลคิวที่ #${index + 1}</strong>
                        <span class="text-muted small"><i class="bi bi-clock"></i> ${bill.created_at}</span>
                    </div>
                    <div class="small text-secondary mb-2">จำนวนสินค้า: ${bill.items.length} รายการ</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">ยอดรวม: ${bill.total.toFixed(2)} บาท</span>
                        <div>
                            <button class="btn btn-sm btn-primary me-1 px-3 rounded-pill" onclick="restoreBill(${bill.id})">เรียกคืน</button>
                            <button class="btn btn-sm btn-outline-danger px-2 rounded-pill" onclick="deleteHeldBill(${bill.id})">ลบ</button>
                        </div>
                    </div>
                </div>`;
        });
        html += '</div>';

        Swal.fire({
            title: '📜 รายการบิลที่พักไว้',
            html: html,
            width: 500,
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    // ตัวจับสัญญาณ Barcode Reader
    document.getElementById('barcodeInput')?.addEventListener(
        'keydown',
        function (event) {
            if (event.key !== 'Enter') {
                return;
            }

            /*
             * หยุด Enter ของเครื่องสแกนไม่ให้ไหลไปกดปุ่ม "ตกลง"
             * ของ SweetAlert ที่เพิ่งเปิดขึ้นมาใน event เดียวกัน
             */
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            const barcode = this.value.trim();

            if (barcode === '') {
                return;
            }

            this.value = '';

            if (typeof addProductByBarcode === 'function') {
                addProductByBarcode(barcode);
            }
        }
    );

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof renderProductList === "function") renderProductList();
        updateTotalAmount();
        setupBarcodeScanner();
    });

    function setupBarcodeScanner() {
        let barcode = '';
        let lastTime = Date.now();

        document.addEventListener('keydown', (e) => {
            const tag = e.target.tagName.toLowerCase();
            if (tag === 'input' || tag === 'textarea') return;

            const currentTime = Date.now();
            const timeDiff = currentTime - lastTime;

            if (timeDiff > 100) {
                barcode = '';
            }

            if (e.key === 'Enter') {
                if (barcode.trim()) {
                    if (typeof addProductByBarcode === "function") addProductByBarcode(barcode.trim());
                    barcode = '';
                }
            } else {
                barcode += e.key;
            }
            lastTime = currentTime;
        });
    }
    
        function restoreBill(id) {

            const bill = heldBills.find(b => b.id === id);

            if (!bill) return;

            products = [...bill.items];

            heldBills = heldBills.filter(b => b.id !== id);

            renderProductList();

            updateTotalAmount();

            updateQRCode();

            Swal.close();

            showAlert(
                'success',
                'เรียกคืนบิลสำเร็จ'
            );
        }

        function deleteHeldBill(id) {

            heldBills = heldBills.filter(b => b.id !== id);

            Swal.close();

            showHeldBills();
        }

        function addProductByBarcode(barcode) {
            const product = productCatalog.find(p => p.barcode === barcode);

            if (!product) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่พบสินค้าในระบบ',
                    html: `
                        <div class="text-center">
                            <div class="mb-2">
                                ไม่พบสินค้าที่ใช้บาร์โค้ด
                            </div>
                            <div class="fw-bold font-monospace">
                                ${barcode}
                            </div>
                            <div class="small text-muted mt-2">
                                กรุณาตรวจสอบบาร์โค้ด หรือลงทะเบียนสินค้าก่อนขาย
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#ffc107',
                    allowOutsideClick: false,
                    allowEscapeKey: true,
                    allowEnterKey: false,
                    showConfirmButton: true,
                    timer: undefined,
                    timerProgressBar: false
                }).then(() => {
                    const input =
                        document.getElementById('barcodeInput');

                    if (input) {
                        input.value = '';
                        input.focus();
                    }
                });

                return;
            }

            if (!product.is_active || product.is_active == 0) {
                showAlert(
                    'warning',
                    'สินค้าถูกปิดการขาย',
                    `สินค้า "${product.name}" ยังไม่เปิดใช้งาน`
                ).then(() => {
                    document.getElementById('barcodeInput')?.focus();
                });

                return;
            }

            const existing = products.find(p => p.id === product.id && p.unit === product.unit);

            if (existing) {
                existing.qty += 1;
            } else {
                products.push({
                    ...product,
                    qty: 1,
                    price: parseFloat(product.retail_price),
                    price_type: 'retail',
                });
            }

            renderProductList();
            updateTotalAmount();
            updateQRCode();

            document.getElementById('barcodeInput').focus();
        }


        function renderProductList() {
            const tbody = document.getElementById('product-list');
            tbody.innerHTML = '';

            products.forEach((p, i) => {
                const row = `
            <tr>
                <td>${i + 1}</td>
                <td>${p.id}</td>
                <td>${p.name}</td>
                <td>${p.freebie ? p.freebie : 'ไม่มี'}</td>
                <td>${p.unit}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center"
value="${p.qty}" min="1"
oninput="this.value = Math.abs(this.value)"
onchange="updateQty(${i}, this.value)">
                </td>
                <td>${p.price.toFixed(2)} ฿</td>
                <td>${(p.qty * p.price).toFixed(2)} ฿</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeProduct(${i})">ลบ</button>
                </td>
            </tr>
        `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function removeProduct(index) {
            products.splice(index, 1); // ลบสินค้าออกจากตะกร้า
            renderProductList(); // แสดงรายการสินค้าหลังจากลบ
            updateTotalAmount(); // อัปเดตยอดรวม
            updateQRCode(); // อัปเดต QR Code
        }


        function updateQty(index, qty) {
            let quantity = parseInt(qty);

            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
            }

            products[index].qty = quantity;

            renderProductList();
            updateTotalAmount();
            updateQRCode();
        }

        function updateTotalAmount() {
            totalAmount = products.reduce((sum, p) => sum + (p.qty * p.price), 0);
            document.getElementById('totalAmount').innerText = totalAmount.toFixed(2) + " บาท";
        }

        function updateQRCode() {
            document.getElementById('qrImage').src = `https://promptpay.io/${shopPromptPayID}/${totalAmount.toFixed(2)}`;
        }

        function payCash() {
            if (products.length === 0 || totalAmount === 0) {
                return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าในตะกร้าก่อนชำระเงิน');
            }

            const cash = parseFloat(document.getElementById('cash').value);
            const changeInput = document.getElementById('change');

            if (isNaN(cash) || cash < totalAmount) {
                return showAlert('error', 'ยอดเงินไม่พอ', 'กรุณารับเงินมาให้มากกว่าหรือเท่ากับยอดรวม');
            }

            Swal.fire({
                title: 'ยืนยันการชำระเงิน?',
                text: `ยอดเงิน: ${totalAmount.toFixed(2)} บาท`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((res) => {
                if (res.isConfirmed) {
                    const change = cash - totalAmount;
                    changeInput.value = change.toFixed(2) + ' บาท';
                    playSound();

                   submitSaleData('cash', () => {
    const serverChange = cash - totalAmount;
    playSound();

    if (autoPrintEnabled) {
        printReceipt(cash, serverChange, 'เงินสด');
    }

    clearCart();

    showAlert(
        'success',
        'ชำระเงินสดสำเร็จ!',
        `เงินทอน: ${serverChange.toFixed(2)} บาท`
    );
});
                }
            });
        }

        function payQRCode() {
            if (products.length === 0 || totalAmount === 0) {
                return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าในตะกร้าก่อนสร้าง QR Code');
            }

            document.getElementById('qrBox').style.display = 'block';
            document.getElementById('qrTotalAmount').innerText =
                totalAmount.toFixed(2) + ' บาท';
            document.getElementById('confirmQRButton').classList.remove('d-none');
            showAlert('info', 'แสดง QR ให้ลูกค้าสแกน', 'หลังจากลูกค้าจ่ายเสร็จ กด "ยืนยันรับเงินแล้ว"');
        }


        function confirmPaymentByQR() {
            submitSaleData('qr', () => {
                showAlert('success', 'รับชำระเรียบร้อย').then(() => {
    playSound();

    if (autoPrintEnabled) {
        printReceipt(0, 0, 'คิวอาร์โค้ด / พร้อมเพย์');
    }

    clearCart();
});
            });
        }


        function printReceipt(cash, change, paymentMethod) {

    const receiptWindow = window.open(
        '',
        '',
        'width=400,height=600'
    );

            let content = `
<html>
<head>

<style>

@page {
    size: 58mm 100mm;
    margin: 0;
}

html, body {
    width: 58mm;
    margin:0;
    padding:0;
    font-family: Tahoma, sans-serif;
    font-size: 11px;
}

body {
    padding: 2mm;
}


h2 {
    text-align:center;
    font-size:16px;
    margin:5px 0;
}


p {
    margin:3px 0;
}


hr {
    border:none;
    border-top:1px dashed #000;
    margin:5px 0;
}


</style>

</head>

<body>

<h2>{{ $shopSettings->shop_name ?: 'POS' }} POS</h2>

<p>วันที่: ${new Date().toLocaleString()}</p>

<p>พนักงาน: ${sellerName}</p>

<p>ชำระโดย: ${paymentMethod}</p>

<hr>

`;

            products.forEach((p, i) => {
                content +=
                    `${i + 1}. ${p.name} (${p.qty} ${p.unit}) - ${p.price}฿ x ${p.qty} = ${(p.qty * p.price).toFixed(2)}฿<br>`;
            });

            content += `
            <hr>
            <p>ยอดรวม: ${totalAmount.toFixed(2)} บาท</p>
            ${cash ? `<p>เงินสดรับมา: ${cash.toFixed(2)} บาท</p><p>เงินทอน: ${change.toFixed(2)} บาท</p>` : ''}
            <hr><p>ขอบคุณที่ใช้บริการ</p></body></html>
        `;

            receiptWindow.document.write(content);
            receiptWindow.document.close();
            receiptWindow.print();
        }

        function clearCart() {
            products = [];
            renderProductList();
            updateTotalAmount();
            updateQRCode();
            document.getElementById('cash').value = '';
            document.getElementById('change').value = '';
            document.getElementById('qrBox').style.display = 'none';
            document.getElementById('confirmQRButton').classList.add('d-none');

            barcode = ''; // ✅ ล้างบาร์โค้ดเก่าที่อาจยังค้างอยู่
        }

        function playSound() {
            const audio = new Audio('/sounds/cash.mp3');
            audio.onerror = () => console.warn('ไม่พบไฟล์เสียง cash.mp3');
            audio.play();
        }

        function showAlert(icon, title, text = '') {
            return Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: true,
                allowEnterKey: false,
                showConfirmButton: true,
                timer: undefined,
                timerProgressBar: false
            });
        }

       function submitSaleData(paymentMethod, callback) {
    if (saleSubmissionInFlight) {
        return;
    }

    saleSubmissionInFlight = true;
    const priceType =
        document.querySelector('input[name="priceType"]:checked')?.value
        ?? 'retail';

    fetch('{{ route('update.stock') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            request_token: posSaleRequestToken,
            payment_method: paymentMethod,
            price_type: priceType,
            products: products.map(p => ({
                product_unit_id: p.unit_id,
                qty: p.qty
            }))
        })
    })
                .then(async res => {
                    const text = await res.text(); // รับเป็น text ชั่วคราว
                    console.log('🔴 ข้อมูลตอบกลับจากเซิร์ฟเวอร์:', text);
                    return JSON.parse(text); // ✅ return data ออกไป
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            callback();
                        } else {
                            showAlert('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกการขายได้');
                        }
                    } catch (e) {
                        showAlert(
                            'error',
                            'ข้อมูลตอบกลับไม่ถูกต้อง',
                            'เซิร์ฟเวอร์ส่งข้อมูลกลับมาในรูปแบบที่ระบบอ่านไม่ได้ กรุณาลองใหม่อีกครั้ง'
                        );
                    }
                })

                .then(data => {
                    if (data.success) {
                        products = data.items.map(item => {
                            const current = products.find(product =>
                                Number(
                                    product.product_unit_id
                                    || product.unit_id
                                ) === Number(item.product_unit_id)
                            );
                            const catalog = productCatalog.find(product =>
                                Number(
                                    product.product_unit_id
                                    || product.unit_id
                                ) === Number(item.product_unit_id)
                            );

                            return {
                                ...catalog,
                                ...current,
                                product_unit_id: item.product_unit_id,
                                unit_id: item.product_unit_id,
                                qty: item.quantity,
                                price: Number(item.price),
                                price_type: priceType
                            };
                        });
                        totalAmount = Number(data.total);
                        renderProductList();
                        updateTotalAmount();
                        updateQRCode();
                        posSaleRequestToken = data.next_request_token;
                        callback(data);
                    } else {
                        showAlert('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกการขายได้');
                    }
                })
                .catch((err) => {
                    console.error("เกิดข้อผิดพลาดขณะบันทึกการขาย", err);
                    showAlert('error', 'ข้อผิดพลาด', 'การเชื่อมต่อกับเซิร์ฟเวอร์ล้มเหลว');
                })
                .finally(() => {
                    saleSubmissionInFlight = false;
                });
        }


        function addCash(amount) {
            const cashInput = document.getElementById('cash');

            let currentCash = parseFloat(cashInput.value) || 0;

            currentCash += amount;

            cashInput.value = currentCash;

            updateChange();
        }

        function clearCash() {
            document.getElementById('cash').value = '';
            document.getElementById('change').value = '0.00 บาท';
        }

        function updateChange() {
            const cash = parseFloat(document.getElementById('cash').value) || 0;

            const change = cash - totalAmount;

            document.getElementById('change').value =
                (change >= 0 ? change : 0).toFixed(2) + ' บาท';
        }

        function updatePriceTypeForAll(priceType) {
            products.forEach(p => {
                p.price_type = priceType;
                p.price = parseFloat(priceType === 'wholesale' ? p.wholesale_price : p.retail_price);
            });
            renderProductList();
            updateTotalAmount();
            updateQRCode();
        }

        function updateTotalAmount() {
            totalAmount = products.reduce((sum, p) => sum + (p.qty * p.price), 0);
            document.getElementById('totalAmount').innerText = totalAmount.toFixed(2) + " บาท";

            const hasProduct = products.length > 0 && totalAmount > 0;
            document.querySelector('button[onclick="payCash()"]').disabled = !hasProduct;
            document.querySelector('button[onclick="payQRCode()"]').disabled = !hasProduct;
        }

        function openCreditModal() {
            if (products.length === 0) {
                return showAlert('warning', 'ไม่มีสินค้า');
            }

            document.getElementById('creditTotal').innerText =
                totalAmount.toFixed(2);

            const modal = new bootstrap.Modal(
                document.getElementById('creditModal')
            );

            modal.show();
        }

        function showAddCustomerForm() {
            document.getElementById('addCustomerBox')
                .classList.toggle('d-none');
        }

        function addCustomer() {

    const name = document.getElementById('newCustomerName').value;
    const phone = document.getElementById('newCustomerPhone').value;

    if (!name) {
        return showAlert('warning', 'กรุณากรอกชื่อ');
    }

    fetch('/members/customers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: name,
            phone: phone
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

    Swal.fire({
        icon: 'success',
        title: 'เพิ่มลูกค้าสำเร็จ',
        text: `ลูกค้า "${name}" ถูกบันทึกเข้าระบบแล้ว`,
        confirmButtonColor: '#198754',
        timer: 2000,
        showConfirmButton: false
    });

    // เพิ่ม option เข้า select ทันที
    const select = document.getElementById('creditCustomer');

    const option = document.createElement('option');
    option.value = data.customer.id;
    option.text = `${data.customer.name} (ยอดค้างเดิม: 0.00 บ.)`;

    option.selected = true;

    select.appendChild(option);

    // ปิดฟอร์มเพิ่มลูกค้า
    toggleAddCustomerForm(false);

    // ไม่ต้อง reload หน้า
}
    });
}

       function confirmCreditSale() {
    const customerSelect = document.getElementById('creditCustomer');
    const customerId = customerSelect.value;

    const selectedOption =
    customerSelect.options[customerSelect.selectedIndex];

const isActive = selectedOption.dataset.active;

if (isActive == 0) {
    return showAlert(
        'error',
        'ลูกค้าถูกระงับ',
        'ไม่สามารถขายเงินเชื่อให้ลูกค้านี้ได้'
    );
}
    const customerName = customerSelect.options[customerSelect.selectedIndex]?.text || 'ไม่ได้ระบุ';
    const dueDateInput = document.getElementById('creditDueDate').value;
    
    // แปลงฟอร์แมตวันที่ให้อ่านง่าย (ถ้ามีค่า)
    const dueDateFormatted = dueDateInput ? new Date(dueDateInput).toLocaleDateString('th-TH', {
        year: 'numeric', month: 'long', day: 'numeric'
    }) : '<span class="text-danger">ไม่ได้กำหนดวันที่</span>';

    if (!customerId) {
        return showAlert('warning', 'กรุณาเลือกลูกค้า');
    }
    if (!dueDateInput) {
    return showAlert(
        'warning',
        'กรุณาเลือกวันครบกำหนด',
        'กรุณากำหนดวันที่ครบกำหนดชำระก่อนบันทึกเงินเชื่อ'
    );
}

    if (products.length === 0) {
        return showAlert('warning', 'ไม่มีสินค้าในบิล');
    }

    // 🎨 ดีไซน์หน้าต่างสรุปรายละเอียดเงินเชื่อก่อนกดยืนยัน
    const confirmHtml = `
        <div class="text-center p-2" style="font-family: 'Kanit', sans-serif;">
            <p class="text-muted mb-4">โปรดตรวจสอบข้อมูลใบสรุปค้างชำระด้านล่างให้ถูกต้อง</p>
            
            <div class="card border shadow-sm rounded-3 text-start overflow-hidden">
                <div class="bg-warning bg-opacity-10 p-3 border-bottom border-warning border-opacity-25">
                    <h5 class="mb-0 text-warning-emphasis fw-bold">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>รายละเอียดใบเสร็จเงินเชื่อ
                    </h5>
                </div>
                <div class="card-body p-3 bg-white">
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                        <span class="text-muted"><i class="bi bi-person-fill me-1"></i> ชื่อลูกค้า:</span>
                        <span class="fw-bold text-dark">${customerName}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                        <span class="text-muted"><i class="bi bi-calendar-event-fill me-1"></i> กำหนดชำระ:</span>
                        <span class="fw-bold text-dark">${dueDateFormatted}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                        <span class="text-muted fw-bold">ยอดเงินเชื่อรวม:</span>
                        <span class="fs-3 fw-bolder text-danger">${totalAmount.toFixed(2)} บาท</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 small text-secondary">
                <i class="bi bi-exclusively-circle-fill text-warning"></i> เมื่อยืนยันแล้ว ยอดหนี้จะถูกบันทึกเข้าสู่บัญชีของลูกค้าท่านนี้ทันที
            </div>
        </div>
    `;

    Swal.fire({
        title: '🔒 ยืนยันการขายเงินเชื่อ?',
        html: confirmHtml,
        icon: 'warning',
        iconColor: '#ffc107',
        showCancelButton: true,
        confirmButtonText: '📦 ยืนยันบันทึกหนี้',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ffc107', // สีส้ม/เหลืองตามธีมเงินเชื่อเดิม แต่เนียนตาขึ้น
        cancelButtonColor: '#6c757d',
        customClass: {
            confirmButton: 'text-dark fw-bold px-4 py-2 rounded-3',
            cancelButton: 'px-4 py-2 rounded-3'
        },
        allowOutsideClick: false
    }).then((res) => {
        if (res.isConfirmed) {
            
            // ⏳ แสดงหน้าต่างกำลังประมวลผล (Loading) เพื่อความปลอดภัยและดูโปรขึ้น
            Swal.fire({
                title: 'กำลังบันทึกข้อมูล...',
                text: 'กรุณารอสักครู่ ระบบกำลังสื่อสารกับเซิร์ฟเวอร์',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            fetch('/staff/credit-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    total_price: totalAmount,
                    payment_method:'credit',
                    due_date: dueDateInput,
                    products: products.map(p => ({
                        product_unit_id: p.product_unit_id || p.unit_id,
                        qty: p.qty,
                        price: p.price
                    }))
                })
            })
            .then(async response => {
                const text = await response.text();
                console.log('ข้อมูลตอบกลับ:', text);
                return JSON.parse(text);
            })
            .then(data => {
                Swal.close(); // ปิดตัว Loading สปินเนอร์

                if (data.success) {
                    // ปิด Modal บันทึกเครดิตของบูทสแตรป
                    const creditModalElement = document.getElementById('creditModal');
                    if (creditModalElement) {
                        const modalInstance = bootstrap.Modal.getInstance(creditModalElement);
                        if (modalInstance) modalInstance.hide();
                    }

                    // แจ้งเตือนความสำเร็จแบบพรีเมียม
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 ขายเงินเชื่อสำเร็จ!',
                        text: 'ระบบได้ทำการบันทึกข้อมูลค้างชำระเรียบร้อยแล้ว',
                        confirmButtonColor: '#198754',
                        confirmButtonText: 'ตกลง'
                    });

                    if (typeof playSound === "function") playSound();
                    clearCart();

                } else {
                    showAlert(
                        'error',
                        'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์',
                        data.message || 'ไม่สามารถบันทึกเงินเชื่อได้'
                    );
                }
            })
            .catch(error => {
                Swal.close(); // ปิดตัว Loading สปินเนอร์
                console.error(error);
                showAlert(
                    'error',
                    'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์',
                    'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ กรุณาตรวจสอบอินเทอร์เน็ต'
                );
            });
        }
    });
}

// ฟังก์ชันเมื่อกดปุ่ม "ขายเงินเชื่อ" จากหน้าหลัก ให้เปิด Modal บูทสแตรปพร้อมอัปเดตยอดรวม
function openCreditModal() {
    if (totalAmount <= 0) {
        showAlert('warning', 'ไม่สามารถบันทึกเงินเชื่อได้', 'ยอดรวมต้องมากกว่า 0 บาท');
        return;
    }
    
    // อัปเดตยอดเงินลงในสปันของ Modal เครดิต
    document.getElementById('creditTotal').innerText = totalAmount.toFixed(2);
    
    // รีเซ็ตค่าเริ่มต้นในฟอร์มเครดิต
    document.getElementById('creditCustomer').value = "";
    document.getElementById('creditDueDate').value = "";
    toggleAddCustomerForm(false); // ซ่อนกล่องเพิ่มลูกค้าใหม่ไว้ก่อน
    
    // เปิดโมดอล
    const myModal = new bootstrap.Modal(document.getElementById('creditModal'));
    myModal.show();
}

// ฟังก์ชันสลับเปิด/ปิด กล่องรับข้อมูลลูกค้าใหม่
function toggleAddCustomerForm(show = null) {
    const box = document.getElementById('addCustomerBox');
    
    if (show === true || (show === null && box.classList.contains('d-none'))) {
        box.classList.remove('d-none');
        document.getElementById('newCustomerName').focus();
    } else {
        box.classList.add('d-none');
        // เคลียร์ค่าที่ค้างอยู่ข้างใน
        document.getElementById('newCustomerName').value = "";
        document.getElementById('newCustomerPhone').value = "";
    }
}

// ฟังก์ชันจำลองเมื่อกดบันทึกลูกค้าใหม่สำเร็จ (ให้ปรับตาม Ajax บันทึกลูกค้าจริงของคุณ)

    </script>
<div class="modal fade" id="creditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0 overflow-hidden">
            
            <div class="modal-header bg-dark text-white py-3 border-0">
                <h5 class="modal-title d-flex align-items-center gap-2 fw-bold" style="font-family: 'Kanit', sans-serif;">
                    <span class="fs-4"></span> ขายเงินเชื่อ (บันทึกยอดหนี้)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>

            <div class="modal-body p-4 bg-light/50" style="font-family: 'Kanit', sans-serif;">
                
                <div class="card border-0 rounded-3 shadow-sm mb-4" style="background: linear-gradient(135deg, #fff5f5 0%, #fff0f0 100%); border-left: 5px solid #dc3545 !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-danger small fw-bold text-uppercase tracking-wider">ยอดเงินที่จะบันทึกเป็นหนี้</span>
                            <h2 class="text-danger mb-0 fw-bolder mt-1" style="font-size: 2.2rem;">
                                <span id="creditTotal">0.00</span> <span class="fs-5 fw-normal">บาท</span>
                            </h2>
                        </div>
                        <div class="text-danger opacity-25">
                            <i class="bi bi-calculator-fill" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="creditCustomer" class="form-label fw-bold text-secondary mb-0">
                            <i class="bi bi-person-fill me-1"></i> เลือกลูกค้าค้างชำระ
                        </label>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold p-0" onclick="toggleAddCustomerForm()">
                            <i class="bi bi-person-plus-fill me-1"></i> เพิ่มลูกค้าใหม่
                        </button>
                    </div>
                    <select id="creditCustomer" class="form-select form-select-lg rounded-3 border-1 bg-white shadow-xs fw-semibold" style="font-size: 1rem;">
                        <option value="">-- กรุณาเลือกลูกค้าในระบบ --</option>
                        @foreach ($customers as $customer)
                            <option 
    value="{{ $customer->id }}"
    data-active="{{ $customer->is_active }}"
>
                                {{ $customer->name }} (ยอดค้างเดิม: {{ number_format($customer->current_debt, 2) }} บ.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="addCustomerBox" class="card border border-primary border-opacity-25 rounded-3 mb-4 d-none" style="background-color: #f8faff;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span class="fw-bold small">กรอกข้อมูลลูกค้าใหม่</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-font"></i></span>
                                    <input type="text" id="newCustomerName" class="form-control border-start-0" placeholder="ชื่อ-นามสกุล">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" id="newCustomerPhone" class="form-control border-start-0" placeholder="เบอร์โทรศัพท์">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-light border px-3" onclick="toggleAddCustomerForm(false)">ยกเลิก</button>
                            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="addCustomer()">
                                <i class="bi bi-check-lg me-1"></i>บันทึกรายชื่อ
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="creditDueDate" class="form-label fw-bold text-secondary mb-2">
                        <i class="bi bi-calendar-check-fill me-1"></i> วันครบกำหนดชำระเงิน
                    </label>
                    <div class="input-group shadow-xs rounded-3 overflow-hidden">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" id="creditDueDate" class="form-control border-start-0 fw-semibold">
                    </div>
                    <div class="form-text text-muted small mt-2">
                       
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-light p-3 border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-bold" data-bs-dismiss="modal">
                    ปิดหน้าต่าง
                </button>
                <button type="button" class="btn btn-warning btn-lg px-4 py-2 rounded-3 text-dark fw-bold shadow-sm d-flex align-items-center gap-2" onclick="confirmCreditSale()">
                    <span>ยืนยันบันทึกเงินเชื่อ</span>
                </button>
            </div>

        </div>
    </div>
</div>
@endsection