@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 stock-history">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <div class="page-eyebrow">INVENTORY MANAGEMENT</div>
            <h3>ประวัติการรับสินค้าเข้า</h3>
            <div class="page-subtitle">
                ตรวจสอบเอกสารรับสินค้าและรายละเอียดสินค้าแต่ละใบรับ
            </div>
        </div>

        @if(!$isPrint)
            <div class="d-flex gap-2">
                <a href="{{ route('product.stock-in-history', array_merge(request()->query(), ['print' => 1])) }}"
                   target="_blank"
                   class="btn btn-outline-light">
                    <i class="bi bi-printer"></i> พิมพ์
                </a>

                <button type="button"
                        class="btn btn-warning fw-bold"
                        onclick="exportExcel()">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </button>
            </div>
        @endif
    </div>

    {{-- SUMMARY --}}
    <div class="summary-card mb-4">
        <div class="summary-label">จำนวนใบรับสินค้าที่พบ</div>

        <div class="summary-number">
            {{ $isPrint ? $receipts->count() : $receipts->total() }}
            <span>ใบรับสินค้า</span>
        </div>
    </div>

    {{-- FILTER --}}
    @if(!$isPrint)
    <form method="GET"
          action="{{ route('product.stock-in-history') }}"
          class="filter-card mb-4">

        <div class="filter-heading">
            <i class="bi bi-funnel"></i>
            ตัวกรองข้อมูล
        </div>

        <div class="row g-3">

            <div class="col-lg-4 col-md-6">
                <label class="form-label">ค้นหาสินค้า / บาร์โค้ด / หน่วยสินค้า</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       value="{{ $search }}"
                       placeholder="พิมพ์ชื่อสินค้าหรือบาร์โค้ด">
            </div>

            <div class="col-lg-4 col-md-6">
                <label class="form-label">เลขที่ใบรับสินค้า</label>
                <input type="text"
                       name="receipt_number"
                       class="form-control"
                       value="{{ $receiptNumber }}"
                       placeholder="REC-...">
            </div>

            <div class="col-lg-4 col-md-6">
                <label class="form-label">เลขที่อ้างอิง</label>
                <input type="text"
                       name="reference_number"
                       class="form-control"
                       value="{{ $referenceNumber }}"
                       placeholder="เลขที่เอกสารอ้างอิง">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">ผู้จำหน่าย</label>
                <select name="supplier_id" class="form-select">
                    <option value="">ผู้จำหน่ายทั้งหมด</option>

                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            @selected((string)$supplierId === (string)$supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">ผู้รับสินค้า</label>
                <select name="received_by" class="form-select">
                    <option value="">ผู้รับสินค้าทั้งหมด</option>

                    @foreach($receivers as $receiver)
                        <option value="{{ $receiver->id }}"
                            @selected((string)$receivedBy === (string)$receiver->id)>
                            {{ $receiver->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">สถานที่จัดเก็บ</label>
                <select name="location" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="warehouse" @selected($location === 'warehouse')>
                        คลังสินค้า
                    </option>
                    <option value="store" @selected($location === 'store')>
                        หน้าร้าน
                    </option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">ประเภทสินค้า</label>
                <select name="is_free" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="0" @selected($isFree === '0')>
                        สินค้าปกติ
                    </option>
                    <option value="1" @selected($isFree === '1')>
                        สินค้าแถม
                    </option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">ตั้งแต่วันที่</label>
                <input type="date"
                       name="from"
                       class="form-control"
                       value="{{ $from }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">ถึงวันที่</label>
                <input type="date"
                       name="to"
                       class="form-control"
                       value="{{ $to }}">
            </div>

            <div class="col-12 d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-warning fw-bold">
                    <i class="bi bi-search"></i> ค้นหาข้อมูล
                </button>

                <a href="{{ route('product.stock-in-history') }}"
                   class="btn btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> ล้างตัวกรอง
                </a>
            </div>

        </div>
    </form>
    @endif

    {{-- RECEIPT LIST --}}
    <div class="receipt-list">

        @forelse($receipts as $receipt)

            <div class="receipt-card mb-3">

                {{-- RECEIPT HEADER --}}
                <div class="receipt-main">

                    <div class="receipt-info">

                        <div class="receipt-label">
                            เลขที่ใบรับสินค้า
                        </div>

                        <div class="receipt-number">
                            {{ $receipt->receipt_number }}
                        </div>

                        <div class="receipt-meta">

                            <span>
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($receipt->received_date)->format('d/m/Y') }}
                            </span>

                            <span>
                                <i class="bi bi-truck"></i>
                                {{ $receipt->supplier_name ?: 'ไม่ระบุผู้จำหน่าย' }}
                            </span>

                            <span>
                                <i class="bi bi-person"></i>
                                {{ $receipt->received_by_name ?: 'ไม่ระบุผู้รับ' }}
                            </span>

                        </div>
                    </div>

                    <div class="receipt-stats">

                        <div class="stat-item">
                            <strong>{{ number_format($receipt->item_count) }}</strong>
                            <small>รายการสินค้า</small>
                        </div>

                        <div class="stat-item">
                            <strong>{{ number_format($receipt->total_quantity) }}</strong>
                            <small>จำนวนรวม</small>
                        </div>

                    </div>

                    @if(!$isPrint)
                    <button class="btn btn-outline-warning detail-toggle"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#receipt-{{ $receipt->id }}"
                            aria-expanded="false">

                        <i class="bi bi-chevron-down"></i>
                        ดูรายละเอียด

                    </button>
                    @endif

                </div>

                {{-- RECEIPT FOOTER --}}
                <div class="receipt-extra">

                    <span>
                        <i class="bi bi-file-text"></i>
                        เลขอ้างอิง:
                        {{ $receipt->reference_number ?: '-' }}
                    </span>

                    <span class="status-completed">
                        <i class="bi bi-check-circle-fill"></i>
                        รับสินค้าแล้ว
                    </span>

                </div>

                {{-- DETAIL --}}
                <div class="{{ $isPrint ? '' : 'collapse' }}"
                     id="receipt-{{ $receipt->id }}">

                    <div class="detail-area">

                        <div class="detail-heading">
                            <i class="bi bi-box-seam"></i>
                            รายการสินค้าในใบรับนี้
                        </div>

                        <div class="table-responsive">

                            <table class="table detail-table">

                                <thead>
                                    <tr>
                                        <th width="50">ลำดับ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>บาร์โค้ด</th>
                                        <th class="text-end">จำนวน</th>
                                        <th>หน่วย</th>
                                        <th>สถานที่จัดเก็บ</th>
                                        <th>ประเภท</th>
                                        <th>เลขล็อต</th>
                                        <th>วันหมดอายุ</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($receipt->items as $index => $item)

                                        <tr>
                                            <td>{{ $index + 1 }}</td>

                                            <td class="product-name">
                                                {{ $item->product_name }}
                                            </td>

                                            <td>
                                                {{ $item->unit_barcode ?: $item->product_barcode ?: '-' }}
                                            </td>

                                            <td class="text-end quantity-cell">
                                                {{ number_format($item->quantity) }}
                                            </td>

                                            <td>
                                                {{ $item->unit_name ?: '-' }}
                                            </td>

                                            <td>
                                                @if($item->location === 'warehouse')
                                                    <span class="location-label warehouse">
                                                        <i class="bi bi-box"></i>
                                                        คลังสินค้า
                                                    </span>
                                                @else
                                                    <span class="location-label store">
                                                        <i class="bi bi-shop"></i>
                                                        หน้าร้าน
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($item->is_free)
                                                    <span class="free-label">
                                                        สินค้าแถม
                                                    </span>
                                                @else
                                                    <span class="normal-label">
                                                        ปกติ
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $item->lot_number ?: '-' }}
                                            </td>

                                            <td>
                                                {{ $item->expiry_date
                                                    ? \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y')
                                                    : '-' }}
                                            </td>
                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="9" class="text-center empty-row">
                                                ไม่พบรายการสินค้าในใบรับนี้
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>
                            </table>

                        </div>

                        @if($receipt->note)
                            <div class="receipt-note">
                                <i class="bi bi-chat-left-text"></i>
                                <strong>หมายเหตุ:</strong>
                                {{ $receipt->note }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>

        @empty

            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>ไม่พบประวัติการรับสินค้า</h5>
                <p>ลองเปลี่ยนเงื่อนไขการค้นหาอีกครั้ง</p>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if(!$isPrint)
        <div class="pagination-wrapper mt-4">
            {{ $receipts->links() }}
        </div>
    @endif

</div>

<style>
/* =========================================
   CHALINI POS - STOCK HISTORY
   Modern Dashboard / Black & Yellow
========================================= */

.stock-history {
    font-family: "Leelawadee UI", "Noto Sans Thai", Tahoma, sans-serif;
    font-size: 14px;
    line-height: 1.65;
    color: #eeeeee;
}

/* HEADER */
.stock-history .page-eyebrow {
    color: #ffc107;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.stock-history .page-header h3 {
    color: #ffffff;
    font-size: 25px;
    font-weight: 750;
    line-height: 1.4;
    margin: 0 0 5px;
    letter-spacing: -.3px;
}

.stock-history .page-subtitle {
    color: #b8b8b8;
    font-size: 14px;
}

/* SUMMARY */
.stock-history .summary-card {
    display: inline-block;
    min-width: 245px;
    padding: 19px 24px;
    background: #171717;
    border: 1px solid #343434;
    border-radius: 12px;
}

.stock-history .summary-label {
    color: #c4c4c4;
    font-size: 14px;
    font-weight: 500;
}

.stock-history .summary-number {
    color: #ffc107;
    font-size: 34px;
    font-weight: 800;
    line-height: 1.4;
    margin-top: 3px;
}

.stock-history .summary-number span {
    color: #bdbdbd;
    font-size: 14px;
    font-weight: 500;
}

/* FILTER */
.stock-history .filter-card {
    padding: 23px;
    background: #171717;
    border: 1px solid #343434;
    border-radius: 12px;
}

.stock-history .filter-heading {
    color: #ffffff;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
}

.stock-history .filter-heading i {
    color: #ffc107;
    margin-right: 6px;
}

.stock-history .form-label {
    display: block;
    color: #dedede;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
}

.stock-history .form-control,
.stock-history .form-select {
    min-height: 43px;
    background-color: #222222;
    border: 1px solid #444444;
    border-radius: 7px;
    color: #f5f5f5;
    font-family: inherit;
    font-size: 14px;
    font-weight: 400;
    padding: 9px 12px;
}

.stock-history .form-control::placeholder {
    color: #929292;
    opacity: 1;
}

.stock-history .form-control:focus,
.stock-history .form-select:focus {
    background-color: #222222;
    color: #ffffff;
    border-color: #ffc107;
    box-shadow: 0 0 0 .18rem rgba(255,193,7,.12);
}

.stock-history .form-select option {
    background: #222222;
    color: #ffffff;
}

/* BUTTONS */
.stock-history .btn {
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    border-radius: 7px;
    padding: 9px 16px;
}

.stock-history .btn-warning {
    color: #171717;
    background: #ffc107;
    border-color: #ffc107;
}

.stock-history .btn-warning:hover {
    background: #ffcf3f;
    border-color: #ffcf3f;
}

.stock-history .btn-reset {
    color: #dddddd;
    background: #292929;
    border: 1px solid #484848;
}

.stock-history .btn-reset:hover {
    color: #ffffff;
    background: #383838;
}

/* RECEIPT CARD */
.stock-history .receipt-card {
    overflow: hidden;
    background: #171717;
    border: 1px solid #353535;
    border-radius: 12px;
    transition: border-color .2s ease;
}

.stock-history .receipt-card:hover {
    border-color: #555555;
}

/* RECEIPT MAIN */
.stock-history .receipt-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 21px 22px;
    flex-wrap: wrap;
}

.stock-history .receipt-label {
    color: #aaaaaa;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 3px;
}

.stock-history .receipt-number {
    color: #ffd04a;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.5;
    letter-spacing: .2px;
    margin-bottom: 10px;
    overflow-wrap: anywhere;
}

.stock-history .receipt-meta {
    display: flex;
    align-items: center;
    gap: 10px 20px;
    flex-wrap: wrap;
    color: #c6c6c6;
    font-size: 13px;
    line-height: 1.7;
}

.stock-history .receipt-meta i {
    color: #ffc107;
    margin-right: 5px;
}

/* RECEIPT STATS */
.stock-history .receipt-stats {
    display: flex;
    align-items: center;
    gap: 30px;
}

.stock-history .stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 75px;
}

.stock-history .receipt-stats strong {
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
    line-height: 1.3;
}

.stock-history .receipt-stats small {
    color: #aaaaaa;
    font-size: 12px;
    font-weight: 500;
    margin-top: 5px;
}

.stock-history .detail-toggle {
    white-space: nowrap;
    border-radius: 7px;
    font-size: 13px;
}

/* RECEIPT FOOTER */
.stock-history .receipt-extra {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 11px 22px;
    background: #202020;
    border-top: 1px solid #303030;
    color: #c1c1c1;
    font-size: 13px;
}

.stock-history .receipt-extra i {
    margin-right: 5px;
}

.stock-history .status-completed {
    color: #65d99a;
    font-size: 13px;
    font-weight: 700;
}

/* DETAIL */
.stock-history .detail-area {
    padding: 21px;
    border-top: 1px solid #353535;
}

.stock-history .detail-heading {
    color: #ffd04a;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 16px;
}

.stock-history .detail-heading i {
    margin-right: 6px;
}

/* TABLE */
.stock-history .detail-table {
    width: 100%;
    margin-bottom: 0;
    color: #eeeeee;
    font-family: inherit;
    font-size: 13px;
    vertical-align: middle;
}

.stock-history .detail-table thead th {
    padding: 13px 12px;
    background: #292929;
    border-color: #444444;
    color: #ffd04a;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.stock-history .detail-table tbody td {
    padding: 13px 12px;
    background: #191919;
    border-color: #353535;
    color: #dddddd;
    font-size: 13px;
    font-weight: 400;
    vertical-align: middle;
}

.stock-history .detail-table tbody tr:hover td {
    background: #242424;
}

.stock-history .detail-table .product-name {
    color: #ffffff;
    font-weight: 600;
    min-width: 150px;
}

.stock-history .quantity-cell {
    color: #ffffff;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

/* LABELS */
.stock-history .location-label,
.stock-history .free-label,
.stock-history .normal-label {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.stock-history .location-label.warehouse {
    color: #8fcaff;
    background: #173047;
}

.stock-history .location-label.store {
    color: #a9e6bd;
    background: #193b29;
}

.stock-history .free-label {
    color: #ffd04a;
    background: #443711;
}

.stock-history .normal-label {
    color: #cccccc;
    background: #303030;
}

.stock-history .empty-row {
    color: #aaaaaa !important;
    padding: 25px !important;
}

/* NOTE */
.stock-history .receipt-note {
    color: #cccccc;
    border-top: 1px solid #353535;
    margin-top: 15px;
    padding-top: 13px;
    font-size: 13px;
    line-height: 1.8;
}

.stock-history .receipt-note i {
    color: #ffc107;
    margin-right: 5px;
}

/* EMPTY STATE */
.stock-history .empty-state {
    text-align: center;
    padding: 55px 15px;
    background: #171717;
    border: 1px dashed #444444;
    border-radius: 12px;
    color: #aaaaaa;
}

.stock-history .empty-state i {
    display: block;
    color: #ffc107;
    font-size: 42px;
    margin-bottom: 12px;
}

.stock-history .empty-state h5 {
    color: #ffffff;
    font-size: 18px;
    font-weight: 700;
}

.stock-history .empty-state p {
    color: #aaaaaa;
    font-size: 14px;
    margin-bottom: 0;
}

/* PAGINATION */
.stock-history .pagination-wrapper {
    font-size: 14px;
}

.stock-history .pagination .page-link {
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
}

/* RESPONSIVE */
@media (max-width: 768px) {

    .stock-history .page-header h3 {
        font-size: 21px;
    }

    .stock-history .page-subtitle {
        font-size: 13px;
    }

    .stock-history .filter-card {
        padding: 16px;
    }

    .stock-history .receipt-main {
        align-items: flex-start;
        padding: 17px;
        gap: 15px;
    }

    .stock-history .receipt-number {
        font-size: 16px;
    }

    .stock-history .receipt-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        font-size: 13px;
    }

    .stock-history .receipt-stats {
        width: 100%;
        justify-content: flex-start;
        gap: 25px;
    }

    .stock-history .receipt-stats strong {
        font-size: 21px;
    }

    .stock-history .receipt-extra {
        padding: 11px 17px;
    }

    .stock-history .detail-area {
        padding: 15px;
    }

    .stock-history .detail-table tbody td {
        font-size: 12px;
    }
}

/* PRINT */
@media print {

    @page {
        size: A4 landscape;
        margin: 12mm;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: "Leelawadee UI", Tahoma, sans-serif !important;
    }

    .stock-history {
        color: #000000 !important;
        font-size: 12px;
    }

    .stock-history .page-header .btn,
    .stock-history form,
    .stock-history .detail-toggle,
    .stock-history .pagination-wrapper,
    .stock-history nav {
        display: none !important;
    }

    .stock-history .page-header h3,
    .stock-history .page-subtitle,
    .stock-history .receipt-number,
    .stock-history .receipt-label,
    .stock-history .receipt-meta,
    .stock-history .receipt-extra,
    .stock-history .receipt-stats strong,
    .stock-history .receipt-stats small,
    .stock-history .detail-heading,
    .stock-history .receipt-note {
        color: #000000 !important;
    }

    .stock-history .summary-card,
    .stock-history .receipt-card {
        background: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #aaaaaa !important;
        box-shadow: none !important;
        break-inside: avoid;
    }

    .stock-history .receipt-extra,
    .stock-history .detail-table thead th,
    .stock-history .detail-table tbody td {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .stock-history .detail-table th,
    .stock-history .detail-table td {
        border: 1px solid #bbbbbb !important;
        font-size: 10px !important;
        padding: 7px !important;
    }

    .stock-history .status-completed,
    .stock-history .free-label,
    .stock-history .location-label {
        color: #000000 !important;
        background: #eeeeee !important;
    }

    .stock-history .collapse {
        display: block !important;
        height: auto !important;
    }

    .stock-history .detail-area {
        display: block !important;
    }
}
</style>

{{-- PRINT SCRIPT --}}
@if($isPrint)
<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
@endif

{{-- EXCEL EXPORT --}}
@if(!$isPrint)
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
function exportExcel() {

    const rows = [[
        'เลขที่ใบรับ',
        'วันที่รับ',
        'ผู้จำหน่าย',
        'ผู้รับสินค้า',
        'จำนวนรายการ',
        'จำนวนรวม',
        'เลขที่อ้างอิง',
        'หมายเหตุ',
        'สินค้า',
        'บาร์โค้ด',
        'จำนวน',
        'หน่วย',
        'สถานที่จัดเก็บ',
        'ประเภทสินค้า',
        'เลขล็อต',
        'วันหมดอายุ'
    ]];

    @foreach($receipts as $receipt)
        @foreach($receipt->items as $item)

            rows.push([
                @json($receipt->receipt_number),
                @json($receipt->received_date),
                @json($receipt->supplier_name),
                @json($receipt->received_by_name),
                @json($receipt->item_count),
                @json($receipt->total_quantity),
                @json($receipt->reference_number),
                @json($receipt->note),
                @json($item->product_name),
                @json($item->unit_barcode ?: $item->product_barcode),
                @json($item->quantity),
                @json($item->unit_name),
                @json($item->location === 'warehouse' ? 'คลังสินค้า' : 'หน้าร้าน'),
                @json($item->is_free ? 'สินค้าแถม' : 'ปกติ'),
                @json($item->lot_number),
                @json($item->expiry_date)
            ]);

        @endforeach
    @endforeach

    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.aoa_to_sheet(rows);

    worksheet['!cols'] = [
        { wch: 30 },
        { wch: 15 },
        { wch: 25 },
        { wch: 20 },
        { wch: 15 },
        { wch: 15 },
        { wch: 20 },
        { wch: 30 },
        { wch: 30 },
        { wch: 20 },
        { wch: 12 },
        { wch: 15 },
        { wch: 18 },
        { wch: 15 },
        { wch: 20 },
        { wch: 15 }
    ];

    XLSX.utils.book_append_sheet(
        workbook,
        worksheet,
        'ประวัติรับสินค้า'
    );

    XLSX.writeFile(
        workbook,
        'stock-in-history.xlsx'
    );
}
</script>
@endif

@endsection
