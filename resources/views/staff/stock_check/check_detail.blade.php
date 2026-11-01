
@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 stock-check-detail-page">
    <section class="detail-hero">
        <div class="detail-hero-content">
            <div class="detail-hero-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>

            <div class="flex-grow-1">
                <div class="detail-kicker">STOCK CHECK DETAIL</div>
                <h2 class="detail-title">รายละเอียดการตรวจนับสต็อก</h2>
                <p class="detail-subtitle">
                    ตรวจสอบผลการนับจริง เปรียบเทียบกับจำนวนในระบบ และสถานะของแต่ละสินค้า
                </p>
            </div>

            <div class="detail-cycle-badge">
                <i class="bi bi-arrow-repeat me-1"></i>
                รอบที่ {{ $check->cycle }}
            </div>
        </div>
    </section>

    <div class="detail-toolbar">
        <div class="detail-breadcrumb">
            <a href="{{ route('staff.stock.check.report') }}">
                รายงานตรวจนับ
            </a>
            <i class="bi bi-chevron-right"></i>
            <span>รอบที่ {{ $check->cycle }}</span>
        </div>

        <a href="{{ route('staff.stock.check.report') }}"
           class="btn btn-detail-back">
            <i class="bi bi-arrow-left me-1"></i>
            กลับหน้ารายงาน
        </a>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                <div class="summary-label">วันที่ตรวจสอบ</div>
                <div class="summary-value">
                    {{ \Carbon\Carbon::parse($check->check_date)->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div>
                <div class="summary-label">รอบการนับ</div>
                <div class="summary-value summary-cycle">
                    {{ $check->cycle }}
                </div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <div class="summary-label">ผู้ตรวจสอบ</div>
                <div class="summary-value">
                    {{ $check->user->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="summary-label">จำนวนรายการ</div>
                <div class="summary-value">
                    {{ number_format($check->items->count()) }} รายการ
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card">

        {{-- Header --}}
        <div class="detail-card-header">
            <div>
                <div class="detail-card-kicker">ITEMS</div>
                <h5 class="detail-card-title">
                    <i class="bi bi-list-check me-2"></i>
                    รายการสินค้าที่ตรวจนับ
                </h5>
            </div>

            <button type="button"
                    class="btn btn-export-excel"
                    onclick="exportStockCheckExcel()">
                <i class="bi bi-file-earmark-excel me-2"></i>
                Export Excel
            </button>
        </div>

        {{-- Table --}}
        <div class="table-responsive detail-table-wrap">
            <table id="stockCheckTable"
                   class="table detail-table align-middle mb-0">

                <thead>
                    <tr>
                        <th style="width: 32%;">สินค้า</th>
                        <th class="text-center">หน่วย</th>
                        <th class="text-center">ในระบบ</th>
                        <th class="text-center">นับจริง</th>
                        <th class="text-center">ส่วนต่าง</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($check->items as $item)
                        @php
                            $isEqual = (float) $item->diff_qty === 0.0;
                            $isOver = (float) $item->diff_qty > 0;

                            $rowClass = $isEqual
                                ? 'row-equal'
                                : ($isOver ? 'row-over' : 'row-short');
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td>
                                <div class="product-name">
                                    {{ $item->product->name ?? '-' }}
                                </div>

                                @if (!empty($item->product->barcode))
                                    <small class="product-barcode">
                                        <i class="bi bi-upc-scan me-1"></i>
                                        {{ $item->product->barcode }}
                                    </small>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="unit-badge">
                                    {{ $item->unit->unit_name ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="system-qty">
                                    {{ number_format($item->system_qty) }}
                                </span>
                            </td>

                            <td class="text-center actual-cell">
                                <span class="actual-qty">
                                    {{ number_format($item->real_qty) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="diff-value {{ $isEqual ? 'diff-equal' : ($isOver ? 'diff-over' : 'diff-short') }}">
                                    {{ $item->diff_qty > 0 ? '+' : '' }}{{ number_format($item->diff_qty) }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if ($isEqual)
                                    <span class="status-badge status-equal">
                                        <i class="bi bi-check-circle-fill"></i>
                                        ตรง
                                    </span>
                                @elseif ($isOver)
                                    <span class="status-badge status-over">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                        เกิน
                                    </span>
                                @else
                                    <span class="status-badge status-short">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                        ขาด
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-data-row">
                            <td colspan="6" class="text-center py-5">
                                <div class="detail-empty-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <div class="detail-empty-title">
                                    ไม่พบรายการตรวจนับ
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="detail-card-footer">
            <span>รายการทั้งหมด</span>
            <strong>
                {{ number_format($check->items->count()) }} รายการ
            </strong>
        </div>
    </div>
</div>

<style>
    :root {
        --detail-yellow: #ffd43b;
        --detail-yellow-soft: #ffe978;
        --detail-black: #080808;
        --detail-panel: #151515;
        --detail-border: rgba(255, 212, 59, .22);
        --detail-text: #f7f7f7;
        --detail-muted: #a8a8a8;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--detail-black);
    }

    .stock-check-detail-page {
        max-width: 1500px;
        color: var(--detail-text);
    }

    .detail-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1rem;
        padding: 1.5rem;
        border: 1px solid var(--detail-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 26%),
            linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
        box-shadow: 0 20px 48px rgba(0,0,0,.38);
    }

    .detail-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .detail-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .detail-hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--detail-yellow), var(--detail-yellow-soft));
        color: #111;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
    }

    .detail-kicker,
    .detail-card-kicker {
        color: var(--detail-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .16em;
    }

    .detail-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 2rem);
        font-weight: 950;
    }

    .detail-subtitle {
        margin: .35rem 0 0;
        color: #c7c7c7;
        font-size: .9rem;
    }

    .detail-cycle-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: none;
        padding: .65rem .9rem;
        border: 1px solid rgba(255, 212, 59, .32);
        border-radius: 999px;
        background: rgba(255, 212, 59, .09);
        color: var(--detail-yellow);
        font-size: .78rem;
        font-weight: 900;
    }

    .detail-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-breadcrumb {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: var(--detail-muted);
        font-size: .82rem;
    }

    .detail-breadcrumb a {
        color: var(--detail-yellow);
        text-decoration: none;
        font-weight: 800;
    }

    .btn-detail-back {
        min-height: 42px;
        border: 1px solid #454545;
        border-radius: 11px;
        background: #191919;
        color: #ddd;
        font-weight: 850;
    }

    .btn-detail-back:hover {
        border-color: var(--detail-yellow);
        color: var(--detail-yellow);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .summary-card {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-height: 104px;
        padding: 1rem;
        border: 1px solid #2e2e2e;
        border-radius: 18px;
        background: linear-gradient(180deg, #191919, #101010);
        box-shadow: 0 12px 28px rgba(0,0,0,.25);
    }

    .summary-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 13px;
        background: rgba(255, 212, 59, .09);
        color: var(--detail-yellow);
        font-size: 1.05rem;
    }

    .summary-label {
        color: var(--detail-muted);
        font-size: .74rem;
        font-weight: 800;
    }

    .summary-value {
        margin-top: .2rem;
        color: #fff;
        font-size: .95rem;
        font-weight: 900;
    }

    .summary-cycle {
        color: var(--detail-yellow);
        font-size: 1.1rem;
    }

    .detail-card {
        overflow: hidden;
        border: 1px solid #2e2e2e;
        border-radius: 22px;
        background: linear-gradient(180deg, #181818, #101010);
        box-shadow: 0 18px 44px rgba(0,0,0,.32);
    }

    .detail-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--detail-border);
        background: linear-gradient(135deg, #202020, #111);
    }

    .detail-card-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    /* Export Excel Button */
    .btn-export-excel {
        min-height: 42px;
        padding: .65rem 1rem;
        border: 1px solid #198754;
        border-radius: 11px;
        background: #198754;
        color: #fff;
        font-size: .85rem;
        font-weight: 900;
        transition: all .2s ease;
    }

    .btn-export-excel:hover {
        background: #157347;
        border-color: #157347;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(25, 135, 84, .25);
    }

    .detail-table {
        --bs-table-bg: transparent;
        --bs-table-color: #eee;
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .045);
        --bs-table-hover-color: #fff;
    }

    .detail-table thead th {
        padding: .9rem .85rem;
        border-bottom: 1px solid #343434;
        background: #0c0c0c !important;
        color: var(--detail-yellow) !important;
        font-size: .72rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .detail-table td {
        padding: .9rem .85rem;
        border-color: #303030;
        color: #ececec;
    }

    .row-over td {
        background: rgba(59, 130, 246, .025) !important;
    }

    .row-short td {
        background: rgba(239, 68, 68, .025) !important;
    }

    .product-name {
        color: #fff;
        font-weight: 900;
    }

    .product-barcode {
        color: var(--detail-muted);
    }

    .unit-badge {
        display: inline-flex;
        padding: .42rem .68rem;
        border: 1px solid #454545;
        border-radius: 999px;
        background: #202020;
        color: #ddd;
        font-size: .72rem;
        font-weight: 850;
    }

    .system-qty {
        color: #ddd;
        font-weight: 850;
    }

    .actual-cell {
        background: rgba(255, 212, 59, .035) !important;
    }

    .actual-qty {
        color: var(--detail-yellow);
        font-size: 1rem;
        font-weight: 950;
    }

    .diff-value {
        display: inline-block;
        min-width: 58px;
        font-size: .95rem;
        font-weight: 950;
    }

    .diff-equal {
        color: #aaa;
    }

    .diff-over {
        color: #93c5fd;
    }

    .diff-short {
        color: #fca5a5;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .7rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .status-equal {
        border: 1px solid rgba(34, 197, 94, .36);
        background: rgba(34, 197, 94, .1);
        color: #86efac;
    }

    .status-over {
        border: 1px solid rgba(59, 130, 246, .36);
        background: rgba(59, 130, 246, .1);
        color: #93c5fd;
    }

    .status-short {
        border: 1px solid rgba(239, 68, 68, .36);
        background: rgba(239, 68, 68, .1);
        color: #fca5a5;
    }

    .detail-card-footer {
        display: flex;
        justify-content: flex-end;
        gap: .4rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid #303030;
        background: #111;
        color: var(--detail-muted);
        font-size: .8rem;
    }

    .detail-card-footer strong {
        color: var(--detail-yellow);
    }

    .detail-empty-icon {
        color: var(--detail-yellow);
        font-size: 2.4rem;
    }

    .detail-empty-title {
        margin-top: .6rem;
        color: #fff;
        font-weight: 900;
    }

    @media (max-width: 991.98px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .stock-check-detail-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .detail-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .detail-hero-content,
        .detail-toolbar {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .detail-hero-icon {
            width: 52px;
            height: 52px;
        }

        .detail-cycle-badge,
        .btn-detail-back {
            width: 100%;
            justify-content: center;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .btn-export-excel {
            width: 100%;
        }
    }
</style>

<script>
function exportStockCheckExcel() {
    const table = document.getElementById('stockCheckTable');

    if (!table) {
        alert('ไม่พบตารางข้อมูล');
        return;
    }

    // ตรวจสอบข้อมูลก่อน Export
    const rows = table.querySelectorAll('tbody tr');

    if (
        rows.length === 0 ||
        table.querySelector('.empty-data-row')
    ) {
        alert('ไม่มีข้อมูลสำหรับ Export');
        return;
    }

    // สร้างตารางใหม่สำหรับ Excel
    const exportTable = document.createElement('table');

    // คัดลอกหัวตาราง
    const thead = table.querySelector('thead').cloneNode(true);
    exportTable.appendChild(thead);

    // สร้างข้อมูลใหม่โดยไม่เอา HTML และ Icon ไปด้วย
    const tbody = document.createElement('tbody');

    table.querySelectorAll('tbody tr:not(.empty-data-row)').forEach(row => {
        const newRow = document.createElement('tr');

        row.querySelectorAll('td').forEach(cell => {
            const newCell = document.createElement('td');

            // ดึงเฉพาะข้อความ ไม่เอาไอคอน
            newCell.textContent = cell.innerText
                .replace(/\s+/g, ' ')
                .trim();

            newRow.appendChild(newCell);
        });

        tbody.appendChild(newRow);
    });

    exportTable.appendChild(tbody);

    // ข้อมูลส่วนหัวรายงาน
    const cycle = @json($check->cycle);
    const checkDate = @json(\Carbon\Carbon::parse($check->check_date)->format('d/m/Y'));
    const checker = @json($check->user->name ?? '-');
    const itemCount = @json($check->items->count());

    // สร้าง HTML สำหรับ Excel
    const html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns="http://www.w3.org/TR/REC-html40">

    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Tahoma, sans-serif;
                font-size: 11pt;
            }

            h2 {
                font-size: 18pt;
                font-weight: bold;
            }

            .info {
                font-size: 11pt;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th {
                background-color: #198754;
                color: #ffffff;
                font-weight: bold;
                text-align: center;
                border: 1px solid #333333;
                padding: 10px;
            }

            td {
                border: 1px solid #999999;
                padding: 8px;
                vertical-align: middle;
            }

            td:nth-child(n+2) {
                text-align: center;
            }
        </style>
    </head>

    <body>
        <h2>รายงานตรวจนับสต็อก</h2>

        <p class="info">รอบการตรวจนับ: ${cycle}</p>
        <p class="info">วันที่ตรวจสอบ: ${checkDate}</p>
        <p class="info">ผู้ตรวจสอบ: ${checker}</p>
        <p class="info">จำนวนรายการ: ${itemCount} รายการ</p>

        ${exportTable.outerHTML}

    </body>
    </html>`;

    // สร้างไฟล์ Excel
    const blob = new Blob(
        ['\ufeff', html],
        {
            type: 'application/vnd.ms-excel;charset=utf-8;'
        }
    );

    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = `รายงานตรวจนับสต็อก_รอบที่_${cycle}.xls`;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => URL.revokeObjectURL(url), 1000);
}
</script>

@endsection
