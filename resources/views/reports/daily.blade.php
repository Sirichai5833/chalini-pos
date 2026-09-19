@extends('layouts.layout')

@section('content')

@php
    /*
     * รายงานยอดขายต้องไม่รวมรายการที่ถูกยกเลิก
     *
     * หมายเหตุ:
     * - ตารางและยอดรวมพื้นฐานกรองซ้ำใน View เพื่อความปลอดภัย
     * - กำไร ต้นทุน กราฟ และสินค้าขายดี ต้องคำนวณจาก Controller
     *   โดยส่งตัวแปรที่ลงท้ายด้วย Net มาให้หน้านี้
     */
    $cancelledStatuses = [
        'cancelled',
        'canceled',
        'void',
        'ยกเลิก',
        'ถูกยกเลิก',
    ];

    $isCancelled = static function ($record) use ($cancelledStatuses): bool {
        $status = mb_strtolower(
            trim((string) ($record->status ?? ''))
        );

        return in_array($status, $cancelledStatuses, true)
            || !empty($record->cancelled_at)
            || !empty($record->canceled_at)
            || (bool) ($record->is_cancelled ?? false);
    };

    $validSales = collect($sales ?? [])
        ->reject($isCancelled)
        ->values();

    $validOnlineOrders = collect($onlineOrders ?? [])
        ->reject($isCancelled)
        ->values();

    $cancelledSales = collect($sales ?? [])
        ->filter($isCancelled)
        ->values();

    $cancelledOnlineOrders = collect($onlineOrders ?? [])
        ->filter($isCancelled)
        ->values();

    $validOfflineSalesTotal = (float) $validSales->sum('total_price');
    $validOnlineSalesTotal = (float) $validOnlineOrders->sum('total_amount');
    $validTotalSales = $validOfflineSalesTotal + $validOnlineSalesTotal;

    $cancelledOfflineTotal = (float) $cancelledSales->sum('total_price');
    $cancelledOnlineTotal = (float) $cancelledOnlineOrders->sum('total_amount');
    $cancelledTotal = $cancelledOfflineTotal + $cancelledOnlineTotal;

    /*
     * ตัวแปรเหล่านี้ต้องมาจาก Controller ที่กรองรายการยกเลิกแล้ว
     * ไม่ fallback ไปใช้ค่ารวมเดิม เพราะอาจทำให้รายงานผิด
     */
    $netProfitDisplay = (float) ($netProfitNet ?? 0);
    $totalCostDisplay = (float) ($totalCostNet ?? 0);
    $chartLabelsDisplay = $chartLabelsNet ?? [];
    $chartDataDisplay = $chartDataNet ?? [];
    $topProductsDisplay = collect($topAllProductsNet ?? []);

    $hasNetMetrics =
        isset($netProfitNet)
        && isset($totalCostNet)
        && isset($chartLabelsNet)
        && isset($chartDataNet)
        && isset($topAllProductsNet);

    $marginDisplay = $validTotalSales > 0
        ? ($netProfitDisplay / $validTotalSales) * 100
        : 0;
@endphp
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

@push('styles')
<style>
    :root {
        --dash-yellow: #ffd43b;
        --dash-yellow-soft: rgba(255, 212, 59, .12);
        --dash-black: #090909;
        --dash-panel: #151515;
        --dash-panel-soft: #1d1d1d;
        --dash-border: rgba(255, 212, 59, .20);
        --dash-text: #f7f7f7;
        --dash-muted: #aaa;
        --dash-success: #22c55e;
        --dash-danger: #ef4444;
        --dash-info: #38bdf8;
    }

    body {
        font-family: 'Prompt', sans-serif;
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 26%),
            var(--dash-black);
        color: var(--dash-text);
    }

    .sales-dashboard-page {
        color: var(--dash-text);
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        padding: 1.6rem;
        margin-bottom: 1.4rem;
        border: 1px solid var(--dash-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
            linear-gradient(135deg, #080808 0%, #171717 58%, #2b2300 100%);
        box-shadow: 0 20px 46px rgba(0, 0, 0, .38);
    }

    .dashboard-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -130px;
        width: 290px;
        height: 290px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .10);
        pointer-events: none;
    }

    .dashboard-hero-content {
        position: relative;
        z-index: 1;
    }

    .dashboard-title {
        margin: 0;
        color: var(--dash-yellow);
        font-weight: 950;
    }

    .dashboard-subtitle {
        margin: .4rem 0 0;
        color: #d3d3d3;
        font-size: .9rem;
    }

    .dashboard-actions .btn {
        min-height: 44px;
        border-radius: 999px;
        font-weight: 850;
    }

    .btn-export {
        border: 1px solid rgba(34, 197, 94, .42);
        background: rgba(34, 197, 94, .12);
        color: #86efac;
    }

    .btn-export:hover {
        border-color: var(--dash-success);
        background: var(--dash-success);
        color: #08130c;
    }

    .btn-print {
        border: 1px solid var(--dash-yellow);
        background: linear-gradient(135deg, #ffe66a, var(--dash-yellow));
        color: #111;
    }

    .btn-print:hover {
        border-color: #fff08c;
        background: linear-gradient(135deg, #fff08c, #ffc800);
        color: #000;
    }

    .card {
        border: 1px solid #2d2d2d;
        border-radius: 20px;
        background: linear-gradient(180deg, #191919, #111);
        color: var(--dash-text);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .30);
        transition: .2s ease;
    }

    .card:hover {
        border-color: rgba(255, 212, 59, .5);
        transform: translateY(-3px);
        box-shadow: 0 24px 48px rgba(0, 0, 0, .44);
    }

    .card-header {
        border-bottom: 1px solid #2e2e2e !important;
        background: linear-gradient(135deg, #202020, #141414) !important;
    }

    .card-header h6 {
        color: var(--dash-yellow) !important;
    }

    .filter-card {
        border-color: var(--dash-border);
    }

    .form-control,
    .form-select {
        min-height: 46px;
        border: 1px solid #3d3d3d;
        border-radius: 13px;
        background: #111;
        color: #fff !important;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--dash-yellow);
        background: #151515;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .form-select option {
        background: #151515;
        color: #fff;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .btn-filter {
        min-height: 46px;
        border: 1px solid var(--dash-yellow);
        border-radius: 13px;
        background: linear-gradient(135deg, #ffe66a, var(--dash-yellow));
        color: #111;
        font-weight: 900;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #fff08c, #ffc800);
        color: #000;
    }

    .kpi-card {
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .kpi-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 4px;
        background: var(--dash-yellow);
    }

    .kpi-label {
        color: var(--dash-muted);
        font-size: .72rem;
        font-weight: 850;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .kpi-value {
        color: #fff;
        font-size: 1.6rem;
        font-weight: 950;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        display: grid;
        place-items: center;
        border-radius: 17px;
        background: var(--dash-yellow-soft);
        color: var(--dash-yellow);
        font-size: 1.6rem;
    }

    .text-muted {
        color: var(--dash-muted) !important;
    }

    .text-dark {
        color: #fff !important;
    }

    .text-primary,
    .text-info {
        color: var(--dash-yellow) !important;
    }

    .custom-scrollbar {
        max-height: 410px;
        overflow-y: auto;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 7px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #101010;
        border-radius: 999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 999px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--dash-yellow);
    }

    .table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--dash-text);
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .045);
        --bs-table-hover-color: #fff;
        color: var(--dash-text);
    }

    .table th {
        position: sticky;
        top: 0;
        z-index: 1;
        border-color: #303030;
        background: #101010 !important;
        color: var(--dash-yellow) !important;
        font-size: .72rem;
        font-weight: 850;
        letter-spacing: .35px;
        text-transform: uppercase;
    }

    .table td {
        border-color: #303030;
        color: #e8e8e8;
        vertical-align: middle;
        font-size: .88rem;
    }

    .rank-badge {
        width: 30px;
        height: 30px;
        display: inline-grid;
        place-items: center;
        border-radius: 50%;
        background: rgba(255, 212, 59, .14);
        color: var(--dash-yellow);
        font-weight: 900;
    }

    .section-card-title {
        color: var(--dash-yellow);
        font-weight: 900;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        body {
            background: #fff !important;
        }

        #printHeader,
        #printHeader *,
        #reportSection,
        #reportSection * {
            visibility: visible;
        }

        #reportSection {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }

        .sales-dashboard-page,
        .sales-dashboard-page .card,
        .sales-dashboard-page .table,
        .sales-dashboard-page .table td,
        .sales-dashboard-page .text-dark,
        .sales-dashboard-page .text-muted {
            color: #000 !important;
            background: #fff !important;
        }

        .sales-dashboard-page .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            transform: none !important;
        }

        .sales-dashboard-page .card-header,
        .sales-dashboard-page .table th {
            color: #000 !important;
            background: #f2f2f2 !important;
        }

        .custom-scrollbar {
            max-height: none;
            overflow: visible;
        }

        .kpi-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 12px !important;
        }

        .kpi-col {
            flex: 1 !important;
            max-width: 25% !important;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .dashboard-hero .d-flex {
            align-items: flex-start !important;
            flex-direction: column;
        }

        .dashboard-actions {
            width: 100%;
        }

        .dashboard-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

<div class="sales-dashboard-page">
<div class="container-fluid px-xl-5 mt-4 mb-5">
    
    <section class="dashboard-hero no-print">
        <div class="dashboard-hero-content d-flex align-items-center justify-content-between gap-3">
            <div>
                <h3 class="dashboard-title">
                    <i class="bi bi-pie-chart-fill me-2"></i>
                    Dashboard ยอดขาย
                </h3>
                <p class="dashboard-subtitle">
                    สรุปผลประกอบการ ยอดขาย กำไร ต้นทุน และแนวโน้มการขาย
                </p>

                <div class="mt-2 small text-warning">
                    <i class="bi bi-info-circle me-1"></i>
                    ยอดขายในรายงานนี้ไม่รวมรายการที่ถูกยกเลิก
                </div>
            </div>

            <div class="dashboard-actions d-flex flex-wrap gap-2">
                <button type="button"
                    class="btn btn-export px-4"
                    onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel me-2"></i>
                    ส่งออก Excel
                </button>

                <button type="button"
                    class="btn btn-print px-4"
                    onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>
                    พิมพ์รายงาน
                </button>
            </div>
        </div>
    </section>

    @if (!$hasNetMetrics)
        <div
            class="alert alert-warning no-print rounded-4 mb-3"
            role="alert"
        >
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                ข้อมูลกำไร ต้นทุน กราฟ และสินค้าขายดียังไม่พร้อม
            </div>

            <div class="small">
                Controller ต้องส่งตัวแปร
                <code>$netProfitNet</code>,
                <code>$totalCostNet</code>,
                <code>$chartLabelsNet</code>,
                <code>$chartDataNet</code>
                และ <code>$topAllProductsNet</code>
                ที่กรองรายการยกเลิกแล้ว
            </div>
        </div>
    @endif

    @if ($cancelledTotal > 0)
        <div
            class="alert alert-danger no-print rounded-4 mb-3"
            role="alert"
        >
            <i class="bi bi-x-octagon-fill me-1"></i>
            มีรายการยกเลิกมูลค่า
            <strong>฿{{ number_format($cancelledTotal, 2) }}</strong>
            และไม่นำมารวมในยอดขายของรายงานนี้
        </div>
    @endif

    <div class="card filter-card mb-4 no-print rounded-4">
        <div class="card-body p-4">
            <form id="reportForm" class="row g-3 align-items-center" method="GET">
                <div class="col-12 col-md-auto text-muted fw-bold">
                    <i class="bi bi-calendar-range me-2"></i>เลือกช่วงเวลา:
                </div>
                <div class="col-12 col-md-3">
                    <input type="date" class="form-control text-muted" id="startDate" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">
                </div>
                <div class="col-12 col-md-auto text-center text-muted">ถึง</div>
                <div class="col-12 col-md-3">
                    <input type="date" class="form-control text-muted" id="endDate" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-12 col-md-auto ms-auto">
                    <button type="submit" class="btn btn-filter px-4 w-100">
                        <i class="bi bi-search me-2"></i> ดูข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="reportSection">
        
        <div id="printHeader" style="display:none; text-align:center; margin-bottom: 30px;">
            <h3 class="fw-bold">รายงานสรุปยอดขาย (Sales Report)</h3>
            <p class="text-muted">
                ข้อมูลตั้งแต่วันที่: {{ \Carbon\Carbon::parse(request('start_date', date('Y-m-d')))->format('d/m/Y') }} 
                ถึง {{ \Carbon\Carbon::parse(request('end_date', date('Y-m-d')))->format('d/m/Y') }}
            </p>
            <hr>
        </div>

        <div class="row g-4 mb-4 kpi-row">
            <div class="col-xl-3 col-md-6 kpi-col">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="kpi-label mb-1">ยอดขายสุทธิทั้งหมด</h6>
                                <h3 class="kpi-value mb-0">{{ number_format($validTotalSales, 2) }} ฿</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <div class="mt-3 small">
                            <span class="text-muted me-2"><i class="bi bi-shop"></i> หน้าร้าน: <b>{{ number_format($validOfflineSalesTotal, 0) }} ฿</b></span>
                            <span class="text-muted"><i class="bi bi-globe"></i> ออนไลน์: <b>{{ number_format($validOnlineSalesTotal, 0) }} ฿</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 kpi-col">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="kpi-label mb-1">กำไรสุทธิ (ไม่รวมยกเลิก)</h6>
                                <h3 class="kpi-value mb-0">{{ number_format($netProfitDisplay, 2) }} ฿</h3>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">
                            กำไรหลังจากหักต้นทุนสินค้าทั้งหมดแล้ว
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 kpi-col">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="kpi-label mb-1">ต้นทุนสุทธิ (ไม่รวมยกเลิก)</h6>
                                <h3 class="kpi-value mb-0">{{ number_format($totalCostDisplay, 2) }} ฿</h3>
                            </div>
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">
                            มูลค่าต้นทุนของสินค้าที่ขายออกไป
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 kpi-col">
                <div class="card kpi-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="kpi-label mb-1">อัตรากำไร (Margin)</h6>
                                <h3 class="kpi-value mb-0">
                                    {{ number_format($marginDisplay, 2) }} <span class="fs-5">%</span>
                                </h3>
                            </div>
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-percent"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">
                            สัดส่วนกำไรต่อยอดขายทั้งหมด
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between border-0">
                        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-bar-chart-line me-2"></i>กราฟแสดงยอดขาย</h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 380px; width: 100%;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 d-flex flex-column border-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-star-fill text-warning me-2"></i>10 อันดับสินค้าขายดี</h6>
                        </div>
                        <div class="no-print">
                            <input type="hidden" id="filterStartDate" value="{{ request('start_date') }}">
                            <input type="hidden" id="filterEndDate" value="{{ request('end_date') }}">
                            <select id="categorySelect" class="form-select form-select-sm shadow-none border-light  rounded-pill px-3">
                                <option value="">หมวดหมู่: ทั้งหมด</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        หมวดหมู่: {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="custom-scrollbar px-3 pb-3">
                            <table id="tableTopProducts" class="table table-borderless mb-0">
                                <thead>
                                    <tr class="d-none"> <th>ลำดับ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>จำนวน (ชิ้น)</th>
                                        <th>ยอดขายรวม (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topProductsDisplay->take(10) as $index => $product)
                                    <tr>
                                        <td style="width: 30px;">
                                            <span class="rank-badge">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 150px;" title="{{ $product['name'] }}">
                                                {{ $product['name'] }}
                                            </div>
                                            <div class="small text-muted">{{ $product['total_quantity'] }} ชิ้น</div>
                                        </td>
                                        <td class="d-none">{{ $product['total_quantity'] }}</td>
                                        
                                        <td class="text-end fw-bold text-success align-middle">
                                            {{ number_format($product['total_sales'], 0) }} ฿
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-light"></i>
                                            ไม่มีข้อมูลสินค้าขายดีในช่วงเวลานี้
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-secondary"><i class="bi bi-shop me-2"></i>รายละเอียดการขาย (หน้าร้าน)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="custom-scrollbar px-3 pb-3">
                            <table id="tableOfflineSales" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>วันเวลา</th>
                                        <th>รหัส / พนักงาน</th>
                                        <th class="text-end">ยอดรวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($validSales as $sale)
                                    <tr>
                                        <td class="text-muted small">
                                            <div>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</div>
                                            <div>{{ \Carbon\Carbon::parse($sale->sale_date)->format('H:i') }} น.</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                                #{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div class="small text-muted mt-1"><i class="bi bi-person me-1"></i>{{ $sale->staff->name ?? '-' }}</div>
                                        </td>
                                        <td class="text-end fw-bold text-dark align-middle">{{ number_format($sale->total_price, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">ไม่มีประวัติการขายหน้าร้าน</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-info"><i class="bi bi-globe me-2"></i>รายละเอียดการสั่งซื้อ (ออนไลน์)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="custom-scrollbar px-3 pb-3">
                            <table id="tableOnlineSales" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>วันเวลา</th>
                                        <th>ออเดอร์ / ลูกค้า</th>
                                        <th class="text-end">ยอดรวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($validOnlineOrders as $order)
                                    <tr>
                                        <td class="text-muted small">
                                            <div>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</div>
                                            <div>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }} น.</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-info bg-info bg-opacity-10 px-2 py-1 rounded small">
                                                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div class="small text-muted mt-1"><i class="bi bi-person me-1"></i>{{ $order->user->name ?? 'ลูกค้าทั่วไป' }}</div>
                                        </td>
                                        <td class="text-end fw-bold text-dark align-middle">{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">ไม่มีประวัติคำสั่งซื้อออนไลน์</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ==========================================
// ฟังก์ชันสำหรับส่งออกเป็น Excel (SheetJS)
// ==========================================
function exportToExcel() {
    // สร้าง Workbook ใหม่
    let wb = XLSX.utils.book_new();
    
    // ดึงค่าตัวแปรจากหน้าจอเพื่อใส่หน้าสรุป
    let startDate = document.getElementById('startDate').value;
    let endDate = document.getElementById('endDate').value;
    
    // 1. สร้าง Sheet แรก (สรุปภาพรวม KPI) โดยอ้างอิงจากตัวแปร Blade
    let ws_kpi = XLSX.utils.aoa_to_sheet([
        ["รายงานสรุปผลประกอบการ (Sales Report)"],
        ["ตั้งแต่วันที่:", startDate, "ถึงวันที่:", endDate],
        [],
        ["หัวข้อ", "ยอดเงิน (บาท)", "หมายเหตุ"],
        ["ยอดขายรวมทั้งหมด", {{ $validTotalSales }}, "ไม่รวมรายการยกเลิก"],
        ["- ยอดขายหน้าร้าน", {{ $validOfflineSalesTotal }}, "ไม่รวมรายการยกเลิก"],
        ["- ยอดขายออนไลน์", {{ $validOnlineSalesTotal }}, "ไม่รวมรายการยกเลิก"],
        ["กำไรสุทธิ", {{ $netProfitDisplay }}, "ไม่รวมรายการยกเลิก"],
        ["ต้นทุนรวม", {{ $totalCostDisplay }}, "ไม่รวมรายการยกเลิก"],
        ["อัตรากำไร (Margin)", "{{ number_format($marginDisplay, 2) }}%", "สัดส่วนกำไรต่อยอดขาย"],
    ]);
    XLSX.utils.book_append_sheet(wb, ws_kpi, "สรุปภาพรวม (KPI)");

    // 2. ดึงตารางสินค้าขายดีแปลงเป็น Sheet
    let tblProducts = document.getElementById('tableTopProducts');
    if (tblProducts) {
        let ws_products = XLSX.utils.table_to_sheet(tblProducts);
        XLSX.utils.book_append_sheet(wb, ws_products, "สินค้าขายดี");
    }

    // 3. ดึงตารางขายหน้าร้านแปลงเป็น Sheet
    let tblOffline = document.getElementById('tableOfflineSales');
    if (tblOffline) {
        let ws_offline = XLSX.utils.table_to_sheet(tblOffline);
        XLSX.utils.book_append_sheet(wb, ws_offline, "รายการขายหน้าร้าน");
    }

    // 4. ดึงตารางออนไลน์แปลงเป็น Sheet
    let tblOnline = document.getElementById('tableOnlineSales');
    if (tblOnline) {
        let ws_online = XLSX.utils.table_to_sheet(tblOnline);
        XLSX.utils.book_append_sheet(wb, ws_online, "รายการออเดอร์ออนไลน์");
    }

    // สั่งดาวน์โหลดไฟล์ Excel
    let fileName = "รายงานยอดขาย_" + startDate + "_ถึง_" + endDate + ".xlsx";
    XLSX.writeFile(wb, fileName);
}

document.addEventListener('DOMContentLoaded', function () {
    // ระบบ Filter สินค้าตามหมวดหมู่
    const select = document.getElementById('categorySelect');
    if (select) {
        select.addEventListener('change', function () {
            const categoryId = this.value;
            const startDate = document.getElementById('filterStartDate').value;
            const endDate = document.getElementById('filterEndDate').value;

            let url = `?start_date=${startDate}&end_date=${endDate}`;
            if (categoryId) {
                url += `&category_id=${categoryId}`;
            }
            this.disabled = true; 
            window.location.href = url;
        });
    }

    // Chart.js Configuration
    const ctx = document.getElementById('salesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, 'rgba(255, 193, 7, 0.92)'); 
    gradient.addColorStop(1, 'rgba(255, 193, 7, 0.08)'); 

    Chart.defaults.font.family = "'Prompt', sans-serif";
    Chart.defaults.color = '#bcbcbc';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabelsDisplay),
            datasets: [{
                label: 'ยอดขายรวม (บาท)',
                data: @json($chartDataDisplay),
                backgroundColor: gradient,
                hoverBackgroundColor: '#ffdc68',
                borderColor: '#ffc107',
                borderWidth: 0,
                borderRadius: {
                    topLeft: 6,
                    topRight: 6,
                    bottomLeft: 0,
                    bottomRight: 0
                },
                barPercentage: 0.5,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(20, 20, 20, 0.96)',
                    titleColor: '#ffc107',
                    bodyColor: '#ffffff',
                    bodyFont: { weight: 'bold', size: 14 },
                    borderColor: '#444444',
                    borderWidth: 1,
                    padding: 15,
                    displayColors: false,
                    cornerRadius: 8,
                    callbacks: {
                        title: function(context) { return 'วันที่: ' + context[0].label; },
                        label: function(context) { return 'ยอดขาย: ' + context.parsed.y.toLocaleString() + ' ฿'; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { 
                        color: '#333333', 
                        drawBorder: false,
                        borderDash: [2, 2] 
                    },
                    ticks: {
                        maxTicksLimit: 6,
                        padding: 10,
                        callback: function(value) { return value.toLocaleString() + ' ฿'; }
                    }
                },
                x: {
                    grid: { 
                        display: false, 
                        drawBorder: false 
                    },
                    ticks: { maxTicksLimit: 7 }
                }
            }
        }
    });
});
</script>
@endsection