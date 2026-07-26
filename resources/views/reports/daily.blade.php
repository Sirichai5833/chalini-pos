@extends('layouts.layout')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

@push('styles')
<style>
    :root {
        --primary-color: #4e73df;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --dark-color: #5a5c69;
        --bg-color: #f8f9fc;
    }

    body {
        font-family: 'Prompt', sans-serif;
        background-color: var(--bg-color);
        color: var(--dark-color);
    }

    /* Cards Styling */
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-3px);
    }
    
    /* Stat Icons */
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    /* Scrollbar สำหรับตาราง */
    .custom-scrollbar {
        max-height: 400px;
        overflow-y: auto;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }

    /* Tables */
    .table th {
        font-weight: 600;
        color: #858796;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        background-color: #f8f9fc;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #e3e6f0;
    }

    @media print {
        body * { visibility: hidden; }
        body { background-color: white; }
        #printHeader, #printHeader * { visibility: visible; display: block !important; }
        #reportSection, #reportSection * { visibility: visible; }
        #reportSection { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eaecf4 !important; transform: none !important; }
        .custom-scrollbar { max-height: none; overflow-y: visible; }
        
        .kpi-row { display: flex !important; flex-wrap: nowrap !important; gap: 15px !important; margin-bottom: 20px !important; }
        .kpi-col { flex: 1 !important; width: 25% !important; max-width: 25% !important; padding: 0 !important; margin: 0 !important; }
        .kpi-col .card-body { padding: 1rem !important; }
        .kpi-col h3 { font-size: 1.25rem !important; margin-bottom: 5px !important; }
        .kpi-col h6 { font-size: 0.65rem !important; }
        .kpi-col .stat-icon { width: 40px !important; height: 40px !important; font-size: 1.2rem !important; }
        .kpi-col .small { font-size: 0.7rem !important; }
    }
</style>
@endpush

<div class="container-fluid px-xl-5 mt-4 mb-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1" style="color: #4e73df;">
                <i class="bi bi-pie-chart-fill me-2"></i>Dashboard ยอดขาย
            </h3>
            <p class="text-muted small mb-0">รายงานสรุปผลประกอบการประจำวันและแนวโน้มการขาย</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-success shadow-sm px-4 rounded-pill fw-bold" onclick="exportToExcel()">
                <i class="bi bi-file-earmark-excel me-2"></i> ส่งออก Excel
            </button>
            <button type="button" class="btn btn-primary shadow-sm px-4 rounded-pill" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> พิมพ์รายงาน
            </button>
        </div>
    </div>

    <div class="card mb-4 no-print rounded-4">
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
                    <button type="submit" class="btn btn-dark px-4 w-100 rounded-pill">
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
                <div class="card h-100 border-start border-4 border-primary">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-xs fw-bold text-primary text-uppercase mb-1">ยอดขายรวมทั้งหมด</h6>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalSales, 2) }} ฿</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <div class="mt-3 small">
                            <span class="text-muted me-2"><i class="bi bi-shop"></i> หน้าร้าน: <b>{{ number_format($sales->sum('total_price'), 0) }} ฿</b></span>
                            <span class="text-muted"><i class="bi bi-globe"></i> ออนไลน์: <b>{{ number_format($onlineSalesTotal ?? 0, 0) }} ฿</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 kpi-col">
                <div class="card h-100 border-start border-4 border-success">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-xs fw-bold text-success text-uppercase mb-1">กำไรสุทธิ</h6>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($netProfit, 2) }} ฿</h3>
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
                <div class="card h-100 border-start border-4 border-danger">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-xs fw-bold text-danger text-uppercase mb-1">ต้นทุนรวม</h6>
                                <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalCost, 2) }} ฿</h3>
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
                <div class="card h-100 border-start border-4 border-info">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-xs fw-bold text-info text-uppercase mb-1">อัตรากำไร (Margin)</h6>
                                <h3 class="fw-bold mb-0 text-dark">
                                    {{ number_format(($netProfit / max($totalSales, 1)) * 100, 2) }} <span class="fs-5">%</span>
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
                            <select id="categorySelect" class="form-select form-select-sm shadow-none border-light bg-light rounded-pill px-3">
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
                                    @forelse (collect($topAllProducts)->take(10) as $index => $product)
                                    <tr>
                                        <td style="width: 30px;">
                                            <span class="badge {{ $index < 3 ? 'bg-warning text-dark' : 'bg-secondary' }} rounded-circle p-2">
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
                                    @forelse ($sales as $sale)
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
                                    @forelse ($onlineOrders as $order)
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
        ["ยอดขายรวมทั้งหมด", {{ $totalSales }}, ""],
        ["- ยอดขายหน้าร้าน", {{ $sales->sum('total_price') }}, ""],
        ["- ยอดขายออนไลน์", {{ $onlineSalesTotal ?? 0 }}, ""],
        ["กำไรสุทธิ", {{ $netProfit }}, "หักต้นทุนแล้ว"],
        ["ต้นทุนรวม", {{ $totalCost }}, ""],
        ["อัตรากำไร (Margin)", "{{ number_format(($netProfit / max($totalSales, 1)) * 100, 2) }}%", "สัดส่วนกำไรต่อยอดขาย"],
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
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.9)'); 
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0.1)'); 

    Chart.defaults.font.family = "'Prompt', sans-serif";
    Chart.defaults.color = '#858796';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'ยอดขายรวม (บาท)',
                data: {!! json_encode($chartData) !!},
                backgroundColor: gradient,
                hoverBackgroundColor: '#2e59d9',
                borderColor: '#4e73df',
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
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#5a5c69',
                    bodyColor: '#5a5c69',
                    bodyFont: { weight: 'bold', size: 14 },
                    borderColor: '#dddfeb',
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
                        color: '#eaecf4', 
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
