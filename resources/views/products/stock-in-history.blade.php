@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 stock-in-history-page">
    <section class="stock-in-hero">
        <div class="stock-in-hero-content">
            <div class="stock-in-hero-icon">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>

            <div class="flex-grow-1">
                <div class="stock-in-kicker">STOCK IN HISTORY</div>
                <h2 class="stock-in-title">ประวัติการนำสินค้าเข้า</h2>
                <p class="stock-in-subtitle">
                    แสดงเฉพาะการรับสินค้าเข้า พร้อมค้นหาตามเอกสาร ผู้จำหน่าย สินค้า และช่วงวันที่
                </p>
            </div>

            <div class="stock-in-summary-badge">
                <i class="bi bi-list-check me-1"></i>
                {{ number_format($movements->count()) }} รายการ
            </div>
        </div>
    </section>

    <div class="stock-in-card mb-4">
        <div class="stock-in-card-header">
            <div>
                <div class="stock-in-card-kicker">FILTER</div>
                <h5 class="stock-in-card-title">
                    <i class="bi bi-funnel-fill me-2"></i>
                    ตัวกรองการค้นหา
                </h5>
            </div>

            @if (
                request()->filled('search') ||
                request()->filled('receipt_number') ||
                request()->filled('reference_number') ||
                request()->filled('supplier_id') ||
                request()->filled('location') ||
                request()->filled('is_free') ||
                request()->filled('from') ||
                request()->filled('to')
            )
                <a
                    href="{{ route('product.stock-in-history') }}"
                    class="btn btn-clear-filter"
                >
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    ล้างตัวกรอง
                </a>
            @endif
        </div>

        <div class="stock-in-card-body">
            <form
                method="GET"
                action="{{ route('product.stock-in-history') }}"
                class="row g-3 align-items-end"
            >
                <div class="col-xl-4 col-md-6">
                    <label for="search" class="form-label">
                        สินค้า / บาร์โค้ด / หน่วย
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="ชื่อสินค้า บาร์โค้ด หรือหน่วย"
                            value="{{ $search }}"
                        >
                    </div>
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="receipt_number" class="form-label">
                        เลขที่รับสินค้า
                    </label>

                    <input
                        type="text"
                        name="receipt_number"
                        id="receipt_number"
                        class="form-control"
                        placeholder="REC-..."
                        value="{{ $receiptNumber }}"
                    >
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="reference_number" class="form-label">
                        เอกสารอ้างอิง
                    </label>

                    <input
                        type="text"
                        name="reference_number"
                        id="reference_number"
                        class="form-control"
                        placeholder="PO / ใบส่งของ"
                        value="{{ $referenceNumber }}"
                    >
                </div>

                <div class="col-xl-4 col-md-6">
                    <label for="supplier_id" class="form-label">
                        ผู้จำหน่าย
                    </label>

                    <select
                        name="supplier_id"
                        id="supplier_id"
                        class="form-select"
                    >
                        <option value="">ผู้จำหน่ายทั้งหมด</option>

                        @foreach ($suppliers as $supplier)
                            <option
                                value="{{ $supplier->id }}"
                                @selected(
                                    (string) $supplierId ===
                                    (string) $supplier->id
                                )
                            >
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="location" class="form-label">
                        เพิ่มเข้าที่
                    </label>

                    <select
                        name="location"
                        id="location"
                        class="form-select"
                    >
                        <option value="">ทุกสถานที่</option>
                        <option
                            value="warehouse"
                            @selected($location === 'warehouse')
                        >
                            คลัง
                        </option>
                        <option
                            value="store"
                            @selected($location === 'store')
                        >
                            หน้าร้าน
                        </option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="is_free" class="form-label">
                        ประเภทรายการ
                    </label>

                    <select
                        name="is_free"
                        id="is_free"
                        class="form-select"
                    >
                        <option value="">ทั้งหมด</option>
                        <option
                            value="0"
                            @selected((string) $isFree === '0')
                        >
                            สินค้าปกติ
                        </option>
                        <option
                            value="1"
                            @selected((string) $isFree === '1')
                        >
                            ของแถม
                        </option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="from" class="form-label">
                        วันที่รับเริ่มต้น
                    </label>

                    <input
                        type="date"
                        name="from"
                        id="from"
                        class="form-control"
                        value="{{ $from }}"
                    >
                </div>

                <div class="col-xl-2 col-md-6">
                    <label for="to" class="form-label">
                        วันที่รับสิ้นสุด
                    </label>

                    <input
                        type="date"
                        name="to"
                        id="to"
                        class="form-control"
                        value="{{ $to }}"
                    >
                </div>

                <div class="col-xl-2 col-md-6">
                    <button
                        type="submit"
                        class="btn btn-stock-search w-100"
                    >
                        <i class="bi bi-search me-2"></i>
                        ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($movements->count() > 0)
        <div class="stock-in-toolbar">
            <div class="toolbar-info">
                <i class="bi bi-info-circle me-1"></i>
                แสดงรายการรับสินค้าเข้าตามเงื่อนไขที่เลือก
            </div>

            <div class="toolbar-actions">
                <button
                    type="button"
                    class="btn btn-export-excel"
                    onclick="exportToExcel()"
                >
                    <i class="bi bi-file-earmark-excel-fill me-2"></i>
                    Export Excel
                </button>

                <a
                    href="{{ route('product.stock-in-history', array_merge(request()->all(), ['print' => 1])) }}"
                    class="btn btn-print-report"
                    target="_blank"
                >
                    <i class="bi bi-printer-fill me-2"></i>
                    พิมพ์รายการ
                </a>
            </div>
        </div>

        <div class="stock-in-card">
            <div class="stock-in-card-header">
                <div>
                    <div class="stock-in-card-kicker">HISTORY</div>
                    <h5 class="stock-in-card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        รายการนำสินค้าเข้า
                    </h5>
                </div>
            </div>

            <div class="table-responsive stock-in-table-wrap printable-area">
                <div class="print-report-header">
                    <h2>รายงานประวัติการนำสินค้าเข้า</h2>
                    <div>
                        ช่วงวันที่:
                        {{ $from ?: 'ทั้งหมด' }}
                        ถึง
                        {{ $to ?: 'ทั้งหมด' }}
                    </div>

                    @if ($search)
                        <div>คำค้นหา: {{ $search }}</div>
                    @endif

                    @if ($receiptNumber)
                        <div>เลขที่รับสินค้า: {{ $receiptNumber }}</div>
                    @endif

                    @if ($referenceNumber)
                        <div>เอกสารอ้างอิง: {{ $referenceNumber }}</div>
                    @endif
                </div>

                <table
                    id="stockHistoryTable"
                    class="table stock-in-table align-middle mb-0"
                >
                    <thead>
                        <tr>
                            <th>วันที่รับ</th>
                            <th>เลขที่รับสินค้า</th>
                            <th>เอกสารอ้างอิง</th>
                            <th>ผู้จำหน่าย</th>
                            <th>สินค้า</th>
                            <th class="text-end">จำนวน</th>
                            <th>หน่วย</th>
                            <th class="text-center">สถานที่</th>
                            <th class="text-center">ประเภท</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($movements as $move)
                            <tr>
                                <td>
                                    <div class="stock-date">
                                        {{ \Illuminate\Support\Carbon::parse(
                                            $move->received_date
                                        )->format('d/m/Y') }}
                                    </div>

                                    <small class="stock-time">
                                        บันทึก
                                        {{ $move->created_at->format('H:i') }} น.
                                    </small>
                                </td>

                                <td>
                                    <strong class="text-warning">
                                        {{ $move->receipt_number }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $move->reference_number ?: '-' }}
                                </td>

                                <td>
                                    {{ $move->supplier_name ?: '-' }}
                                </td>

                                <td>
                                    <div class="stock-product-name">
                                        {{ $move->product->name ?? '-' }}
                                    </div>

                                    @if (!empty($move->product->barcode))
                                        <small class="stock-barcode">
                                            <i class="bi bi-upc-scan me-1"></i>
                                            {{ $move->product->barcode }}
                                        </small>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <span class="stock-quantity">
                                        +{{ number_format($move->quantity) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="stock-unit">
                                        {{ $move->unit ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($move->location === 'warehouse')
                                        <span class="stock-location stock-location-warehouse">
                                            <i class="bi bi-building me-1"></i>
                                            คลัง
                                        </span>
                                    @elseif ($move->location === 'store')
                                        <span class="stock-location stock-location-store">
                                            <i class="bi bi-shop me-1"></i>
                                            หน้าร้าน
                                        </span>
                                    @else
                                        <span class="stock-location stock-location-other">
                                            {{ $move->location ?: '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ((int) $move->is_free === 1)
                                        <span class="badge text-bg-success">
                                            ของแถม
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            สินค้าปกติ
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="stock-note">
                                        {{ $move->note ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (
                !$isPrint &&
                $movements instanceof \Illuminate\Pagination\LengthAwarePaginator &&
                $movements->hasPages()
            )
                <div class="stock-in-pagination">
                    {{ $movements->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="stock-in-empty">
            <div class="stock-in-empty-icon">
                <i class="bi bi-inbox"></i>
            </div>

            <h4>ไม่พบรายการนำสินค้าเข้า</h4>

            <p>
                ลองเปลี่ยนชื่อสินค้า บาร์โค้ด หรือช่วงวันที่ใหม่
            </p>
        </div>
    @endif
</div>

<style>
    :root {
        --stock-yellow: #ffd43b;
        --stock-yellow-soft: #ffe978;
        --stock-black: #080808;
        --stock-panel: #151515;
        --stock-border: rgba(255, 212, 59, .22);
        --stock-text: #f7f7f7;
        --stock-muted: #a8a8a8;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--stock-black);
    }

    .stock-in-history-page {
        max-width: 1500px;
        color: var(--stock-text);
    }

    .stock-in-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid var(--stock-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 26%),
            linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
        box-shadow: 0 20px 48px rgba(0,0,0,.38);
    }

    .stock-in-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .stock-in-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stock-in-hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--stock-yellow), var(--stock-yellow-soft));
        color: #111;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
    }

    .stock-in-kicker,
    .stock-in-card-kicker {
        color: var(--stock-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .16em;
    }

    .stock-in-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 2rem);
        font-weight: 950;
    }

    .stock-in-subtitle {
        margin: .35rem 0 0;
        color: #c7c7c7;
        font-size: .9rem;
    }

    .stock-in-summary-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: none;
        padding: .65rem .9rem;
        border: 1px solid rgba(255, 212, 59, .32);
        border-radius: 999px;
        background: rgba(255, 212, 59, .09);
        color: var(--stock-yellow);
        font-size: .78rem;
        font-weight: 900;
    }

    .stock-in-card {
        overflow: hidden;
        border: 1px solid #2e2e2e;
        border-radius: 22px;
        background: linear-gradient(180deg, #181818, #101010);
        box-shadow: 0 18px 44px rgba(0,0,0,.32);
    }

    .stock-in-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--stock-border);
        background: linear-gradient(135deg, #202020, #111);
    }

    .stock-in-card-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .stock-in-card-body {
        padding: 1.25rem;
    }

    .stock-in-history-page .form-label {
        color: #ddd;
        font-size: .78rem;
        font-weight: 850;
    }

    .stock-in-history-page .form-control,
    .stock-in-history-page .form-select,
    .stock-in-history-page .input-group-text {
        min-height: 46px;
        border: 1px solid #3b3b3b;
        background: #0d0d0d;
        color: #fff;
        box-shadow: none;
    }

    .stock-in-history-page .input-group-text {
        border-right: 0;
        color: var(--stock-yellow);
    }

    .stock-in-history-page .input-group .form-control {
        border-left: 0;
    }

    .stock-in-history-page .form-control:focus,
    .stock-in-history-page .form-select:focus {
        border-color: var(--stock-yellow);
        background: #0b0b0b;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .stock-in-history-page .form-select option {
        background: #151515;
        color: #fff;
    }

    .stock-in-history-page input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .btn-stock-search,
    .btn-clear-filter,
    .btn-export-excel,
    .btn-print-report {
        min-height: 44px;
        border-radius: 12px;
        font-weight: 900;
    }

    .btn-stock-search {
        border: 1px solid var(--stock-yellow);
        background: linear-gradient(135deg, var(--stock-yellow), var(--stock-yellow-soft));
        color: #111;
    }

    .btn-clear-filter {
        border: 1px solid #454545;
        background: #181818;
        color: #ddd;
    }

    .btn-export-excel {
        border: 1px solid rgba(34, 197, 94, .4);
        background: rgba(34, 197, 94, .1);
        color: #86efac;
    }

    .btn-print-report {
        border: 1px solid rgba(255, 212, 59, .36);
        background: rgba(255, 212, 59, .08);
        color: var(--stock-yellow);
    }

    .stock-in-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border: 1px solid #2e2e2e;
        border-radius: 16px;
        background: #121212;
    }

    .toolbar-info {
        color: var(--stock-muted);
        font-size: .82rem;
    }

    .toolbar-actions {
        display: flex;
        gap: .6rem;
    }

    .print-report-header {
        display: none;
    }

    .stock-in-table {
        --bs-table-bg: transparent;
        --bs-table-color: #eee;
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .045);
        --bs-table-hover-color: #fff;
    }

    .stock-in-table thead th {
        padding: .9rem .85rem;
        border-bottom: 1px solid #343434;
        background: #0c0c0c !important;
        color: var(--stock-yellow) !important;
        font-size: .72rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .stock-in-table td {
        padding: .9rem .85rem;
        border-color: #303030;
        color: #ececec;
    }

    .stock-date,
    .stock-product-name {
        color: #fff;
        font-weight: 900;
    }

    .stock-time,
    .stock-barcode {
        color: var(--stock-muted);
    }

    .stock-quantity {
        color: #86efac;
        font-size: .95rem;
        font-weight: 950;
    }

    .stock-unit {
        color: #ddd;
        font-weight: 800;
    }

    .stock-location {
        display: inline-flex;
        align-items: center;
        padding: .42rem .68rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .stock-location-warehouse {
        border: 1px solid rgba(59, 130, 246, .36);
        background: rgba(59, 130, 246, .1);
        color: #93c5fd;
    }

    .stock-location-store {
        border: 1px solid rgba(255, 212, 59, .34);
        background: rgba(255, 212, 59, .09);
        color: var(--stock-yellow);
    }

    .stock-location-other {
        border: 1px solid #454545;
        background: #202020;
        color: #ccc;
    }

    .stock-note {
        max-width: 420px;
        color: #d3d3d3;
        line-height: 1.5;
    }

    .stock-in-pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid #303030;
        background: #111;
    }

    .stock-in-empty {
        padding: 3rem 1rem;
        border: 1px dashed #3b3b3b;
        border-radius: 20px;
        background: #111;
        text-align: center;
    }

    .stock-in-empty-icon {
        color: var(--stock-yellow);
        font-size: 2.5rem;
    }

    .stock-in-empty h4 {
        margin-top: .7rem;
        color: #fff;
        font-weight: 900;
    }

    .stock-in-empty p {
        margin: .3rem 0 0;
        color: var(--stock-muted);
    }

    @media (max-width: 767.98px) {
        .stock-in-history-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .stock-in-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .stock-in-hero-content {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .stock-in-hero-icon {
            width: 52px;
            height: 52px;
        }

        .stock-in-summary-badge {
            width: 100%;
            justify-content: center;
        }

        .stock-in-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .toolbar-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .toolbar-actions .btn {
            width: 100%;
        }
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        .printable-area,
        .printable-area * {
            visibility: visible !important;
        }

        .printable-area {
            position: absolute;
            inset: 0;
            width: 100%;
            color: #000 !important;
            background: #fff !important;
        }

        .print-report-header {
            display: block;
            margin-bottom: 20px;
            text-align: center;
            color: #000 !important;
        }

        .print-report-header h2 {
            font-size: 20px;
            font-weight: 700;
        }

        .stock-in-table {
            color: #000 !important;
        }

        .stock-in-table thead th,
        .stock-in-table td {
            border: 1px solid #bbb !important;
            color: #000 !important;
            background: #fff !important;
        }

        .stock-location,
        .stock-quantity,
        .stock-unit,
        .stock-date,
        .stock-product-name,
        .stock-time,
        .stock-note {
            color: #000 !important;
        }

        thead {
            display: table-header-group;
        }

        tr,
        td,
        th {
            page-break-inside: avoid;
        }
    }
</style>

<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
function exportToExcel() {
    const table = document.getElementById('stockHistoryTable');

    if (!table || typeof XLSX === 'undefined') {
        alert('ไม่สามารถส่งออกไฟล์ Excel ได้');
        return;
    }

    const workbook = XLSX.utils.book_new();

    const worksheet = XLSX.utils.aoa_to_sheet([
        ['ประวัติการนำสินค้าเข้า'],
        ['ค้นหา', @json($search ?: 'ทั้งหมด')],
        ['เลขที่รับสินค้า', @json($receiptNumber ?: 'ทั้งหมด')],
        ['เอกสารอ้างอิง', @json($referenceNumber ?: 'ทั้งหมด')],
        ['วันที่เริ่ม', @json($from ?: 'ทั้งหมด')],
        ['วันที่สิ้นสุด', @json($to ?: 'ทั้งหมด')],
        []
    ]);

    XLSX.utils.sheet_add_dom(
        worksheet,
        table,
        {
            origin: 'A8'
        }
    );

    worksheet['!cols'] = [
        { wch: 16 },
        { wch: 24 },
        { wch: 22 },
        { wch: 26 },
        { wch: 30 },
        { wch: 12 },
        { wch: 15 },
        { wch: 15 },
        { wch: 14 },
        { wch: 40 }
    ];

    XLSX.utils.book_append_sheet(
        workbook,
        worksheet,
        'ประวัตินำสินค้าเข้า'
    );

    const today = new Date().toISOString().slice(0, 10);

    XLSX.writeFile(
        workbook,
        `stock-in-history-${today}.xlsx`
    );
}
</script>

@if ($isPrint)
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
@endif
@endsection