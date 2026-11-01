
@extends('layouts.layout')

@section('content')
<style>
    .low-stock-page {
        color: #f5f5f5;
    }

    .low-stock-page .page-shell {
        max-width: 1200px;
        margin: 0 auto;
    }

    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #383838;
        border-left: 5px solid #ffc107;
        border-radius: 12px;
        background: #171717;
    }

    .report-title {
        margin: 0;
        color: #ffc107;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .report-subtitle {
        margin: 5px 0 0;
        color: #aaa;
        font-size: .88rem;
    }

    .report-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .report-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 40px;
        border-radius: 8px;
        font-weight: 700;
    }

    .report-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        margin-bottom: 18px;
        border: 1px solid #383838;
        border-radius: 10px;
        background: #171717;
    }

    .report-summary i {
        color: #ffc107;
        font-size: 25px;
    }

    .report-summary-label {
        color: #aaa;
        font-size: .82rem;
    }

    .report-summary-value {
        color: #fff;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .report-card {
        overflow: hidden;
        border: 1px solid #383838;
        border-radius: 12px;
        background: #171717;
    }

    .report-card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #383838;
    }

    .report-card-header h5 {
        margin: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
    }

    .report-card-header p {
        margin: 5px 0 0;
        color: #aaa;
        font-size: .8rem;
    }

    .report-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        color: #eee;
    }

    .report-table th {
        padding: 13px 15px;
        background: #252525;
        color: #ffc107;
        font-size: .85rem;
        font-weight: 800;
        text-align: center;
        white-space: nowrap;
        border: 1px solid #444;
    }

    .report-table td {
        padding: 13px 15px;
        border: 1px solid #383838;
        font-size: .88rem;
        vertical-align: middle;
    }

    .report-table tbody tr:hover {
        background: rgba(255, 193, 7, .05);
    }

    .report-table .number {
        text-align: center;
        width: 70px;
    }

    .report-table .quantity {
        text-align: center;
        white-space: nowrap;
    }

    .product-name {
        color: #fff;
        font-weight: 700;
    }

    .stock-badge {
        display: inline-block;
        min-width: 90px;
        padding: 5px 10px;
        border-radius: 20px;
        background: rgba(239, 68, 68, .15);
        color: #ff8585;
        font-size: .78rem;
        font-weight: 800;
    }

    .empty-row {
        padding: 40px 15px !important;
        color: #aaa;
        text-align: center;
    }

    .report-footer {
        padding: 13px 18px;
        border-top: 1px solid #383838;
        color: #aaa;
        font-size: .8rem;
    }

    /* รูปแบบสำหรับพิมพ์ */
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            background: #fff !important;
            color: #000 !important;
        }

        body * {
            visibility: hidden;
        }

        #stockReport,
        #stockReport * {
            visibility: visible;
        }

        #stockReport {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            max-width: none;
            color: #000 !important;
        }

        .no-print,
        .report-actions,
        nav,
        aside,
        header,
        footer {
            display: none !important;
        }

        .report-card,
        .report-summary {
            border: none !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .report-title,
        .report-card-header h5,
        .product-name,
        .report-summary-value {
            color: #000 !important;
        }

        .report-subtitle,
        .report-card-header p,
        .report-summary-label,
        .report-footer {
            color: #555 !important;
        }

        .report-table {
            color: #000 !important;
            font-size: 11pt;
        }

        .report-table th {
            background: #eee !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-table td {
            color: #000 !important;
            border: 1px solid #555 !important;
        }

        .stock-badge {
            background: #fff !important;
            color: #000 !important;
            border: 1px solid #555;
        }

        .report-table tr {
            page-break-inside: avoid;
        }
    }

    @media(max-width: 767px) {
        .report-actions {
            width: 100%;
        }

        .report-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<div class="low-stock-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <div id="stockReport">

                {{-- หัวรายงาน --}}
                <div class="report-header">
                    <div>
                        <h2 class="report-title">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            รายงานสินค้าใกล้หมด
                        </h2>

                        <p class="report-subtitle">
                            รายการสินค้าที่มีจำนวนคงเหลือต่ำกว่าหรือเท่ากับจุดแจ้งเตือน
                        </p>
                    </div>

                    <div class="report-actions no-print">
                        

                        <button type="button" class="btn btn-primary"
                            onclick="exportStockWord()">
                            <i class="bi bi-file-earmark-word"></i>
                            Word
                        </button>

                        <button type="button" class="btn btn-success"
                            onclick="exportStockExcel()">
                            <i class="bi bi-file-earmark-excel"></i>
                            Excel
                        </button>
                    </div>
                </div>

                {{-- สรุปข้อมูล --}}
                <div class="report-summary">
                    <i class="bi bi-box-seam-fill"></i>

                    <div>
                        <div class="report-summary-label">
                            จำนวนสินค้าที่ต้องตรวจสอบ
                        </div>

                        <div class="report-summary-value">
                            {{ $lowStockProducts->count() }} รายการ
                        </div>
                    </div>
                </div>

                {{-- ตารางรายงาน --}}
                <section class="report-card">
                    <div class="report-card-header">
                        <h5>
                            <i class="bi bi-table text-warning me-2"></i>
                            รายการสินค้าใกล้หมด
                        </h5>

                        <p>ข้อมูลจำนวนสินค้าคงเหลือและจำนวนขั้นต่ำ</p>
                    </div>

                    <div class="table-responsive">
                        <table class="report-table" id="lowStockTable">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">ลำดับ</th>
                                    <th>ชื่อสินค้า</th>
                                    <th style="width: 160px;">สต็อกขั้นต่ำ</th>
                                    <th style="width: 160px;">จำนวนคงเหลือ</th>
                                    <th style="width: 150px;">สถานะ</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($lowStockProducts as $index => $item)
                                    <tr>
                                        <td class="number">
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            <span class="product-name">
                                                {{ $item->product_name }}
                                            </span>
                                        </td>

                                        <td class="quantity">
                                            {{ number_format($item->min_stock) }} ชิ้น
                                        </td>

                                        <td class="quantity">
                                            <strong>
                                                {{ number_format($item->total_stock) }} ชิ้น
                                            </strong>
                                        </td>

                                        <td class="quantity">
                                            <span class="stock-badge">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                สินค้าใกล้หมด
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-row">
                                            <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                                            ไม่พบสินค้าที่ใกล้หมด
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="report-footer">
                        จำนวนทั้งหมด {{ $lowStockProducts->count() }} รายการ
                    </div>
                </section>

                <div class="text-end mt-3 report-footer">
                    วันที่ออกรายงาน: {{ now()->format('d/m/Y H:i') }}
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // พิมพ์รายงาน
    function printStockReport() {
        window.print();
    }

    // สร้าง HTML สำหรับ Word และ Excel
    function getStockTableHTML() {
        const table = document.getElementById('lowStockTable');
        return table.outerHTML;
    }

    // ดาวน์โหลดไฟล์
    function downloadStockFile(content, filename, mimeType) {
        const blob = new Blob(['\ufeff', content], {
            type: mimeType
        });

        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');

        link.href = url;
        link.download = filename;

        document.body.appendChild(link);
        link.click();
        link.remove();

        URL.revokeObjectURL(url);
    }

    // ส่งออก Word
    function exportStockWord() {
        const table = getStockTableHTML();

        const html = `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body {
                        font-family: Tahoma, sans-serif;
                    }

                    h2 {
                        text-align: center;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: center;
                    }

                    th {
                        background: #eeeeee;
                    }

                    td:nth-child(2) {
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <h2>รายงานสินค้าใกล้หมด</h2>
                <p>วันที่ออกรายงาน: ${new Date().toLocaleDateString('th-TH')}</p>
                ${table}
            </body>
            </html>
        `;

        downloadStockFile(
            html,
            'รายงานสินค้าใกล้หมด.doc',
            'application/msword;charset=utf-8'
        );
    }

    // ส่งออก Excel
    function exportStockExcel() {
        const table = getStockTableHTML();

        const html = `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th, td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: center;
                    }

                    th {
                        background: #eeeeee;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <h2>รายงานสินค้าใกล้หมด</h2>
                <p>วันที่ออกรายงาน: ${new Date().toLocaleDateString('th-TH')}</p>
                ${table}
            </body>
            </html>
        `;

        downloadStockFile(
            html,
            'รายงานสินค้าใกล้หมด.xls',
            'application/vnd.ms-excel;charset=utf-8'
        );
    }
</script>
@endsection
