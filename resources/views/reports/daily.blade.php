@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-primary fw-bold text-center">📊 รายงานยอดขาย <i class="bi bi-bar-chart-fill"></i></h2>
    <hr class="mb-5">

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-funnel-fill me-2"></i> ตัวกรองรายงาน</h5>
            <button type="button" class="btn btn-light btn-sm" onclick="window.print()"><i class="bi bi-printer-fill me-1"></i> พิมพ์รายงาน</button>
        </div>
        <div class="card-body">
            <form id="reportForm" class="row g-3" method="GET">
             
                <div class="col-md-4">
    <label for="startDate" class="form-label fw-bold">ตั้งแต่วันที่</label>
    <input type="date" class="form-control" id="startDate" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">
</div>
<div class="col-md-4">
    <label for="endDate" class="form-label fw-bold">ถึงวันที่</label>
    <input type="date" class="form-control" id="endDate" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
</div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-2"></i> แสดงรายงาน</button>
                </div>
            </form>
        </div>
    </div>

    <div id="reportSection">
        <div id="printHeader" style="display:none; text-align:right; margin-bottom: 20px;">
            <p class="mb-0">รายงานยอดขาย</p>
            <p class="mb-0">
    ช่วงวันที่: 
    {{ \Carbon\Carbon::parse(request('start_date', date('Y-m-d')))->format('d M Y') }}
    - 
    {{ \Carbon\Carbon::parse(request('end_date', date('Y-m-d')))->format('d M Y') }}
</p>

            <hr>
        </div>

        <div class="row mb-5"> 
            <div class="col-md-6">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title text-muted mb-3">ยอดขายรวมทั้งหมด <i class="bi bi-cash-stack"></i></h4>
                        <h2 class="display-4 fw-bold text-success mb-3" id="totalSales">{{ number_format($totalSales, 2) }} <small class="fw-normal text-muted fs-4">บาท</small></h2>
                        <div class="row">
                            <div class="col-6 border-end">
                                <p class="mb-1 text-primary fw-bold">หน้าร้าน</p>
                                <h5 class="mb-0">{{ number_format($sales->sum('total_price'), 2) }} บาท</h5>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 text-info fw-bold">ออนไลน์</p>
                                <h5 class="mb-0">{{ number_format($onlineSalesTotal ?? 0, 2) }} บาท</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title text-muted mb-3">ภาพรวมกำไรและต้นทุน <i class="bi bi-graph-up"></i></h4>
                        <p class="text-danger mb-1 fw-bold">ต้นทุนรวม: <span class="fs-5">{{ number_format($totalCost, 2) }}</span> บาท</p>
                        <h2 class="display-5 fw-bold text-dark mt-2 mb-3">กำไรสุทธิ: <span class="text-success">{{ number_format($netProfit, 2) }}</span> <small class="fw-normal text-muted fs-4">บาท</small></h2>
                        <h5 class="text-muted">อัตรากำไร: <span class="text-info">{{ number_format(($netProfit / max($totalSales, 1)) * 100, 2) }}%</span></h5>
                    </div>
                </div>
            </div>
        </div>
         <div class="row">
            <div class="col-lg-6 mb-4">
    <div class="card shadow h-100">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">🌐 สินค้าขายดี <i class="bi bi-award-fill"></i></h5>
        </div>
        <div class="card-body">
           <div class="mb-3">
   <input type="hidden" id="filterStartDate" value="{{ request('start_date') }}">
    <input type="hidden" id="filterEndDate" value="{{ request('end_date') }}">

    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="category_id" class="col-form-label">เลือกประเภทสินค้า:</label>
        </div>
        <div class="col">
            <select id="categorySelect" class="form-select">
                <option value="">ทั้งหมด</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อสินค้า</th>
                            <th class="text-end">จำนวนที่ขาย</th>
                            <th class="text-end">ยอดขายรวม (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse ($topAllProducts as $product)
    <tr>
        <td>{{ $product['name'] }}</td>
        <td class="text-end">{{ $product['total_quantity'] }}</td>
        <td class="text-end">{{ number_format($product['total_sales'], 2) }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="3" class="text-center text-muted py-4">ไม่มีข้อมูลสินค้าขายดีรวม</td>
    </tr>
    @endforelse
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>

    </div>


        <div class="card shadow mb-5">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📈 กราฟแสดงยอดขาย</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="500px"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📅 รายการยอดขายหน้าร้าน <i class="bi bi-shop"></i></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="salesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>รหัสการขาย</th>
                                        <th>พนักงาน</th>
                                        <th class="text-end">ยอดรวม (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sales as $sale)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge bg-secondary">#{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                        <td>{{ $sale->staff->name ?? 'ไม่ระบุ' }}</td>
                                        <td class="text-end">{{ number_format($sale->total_price, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่มีข้อมูลยอดขายหน้าร้านในช่วงเวลาที่เลือก</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">🛒 รายการยอดขายออนไลน์ <i class="bi bi-globe"></i></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>รหัสคำสั่งซื้อ</th>
                                        <th>ลูกค้า</th>
                                        <th class="text-end">ยอดรวม (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($onlineOrders as $order)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge bg-secondary">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                                        <td>{{ $order->user->name ?? 'ไม่ระบุ' }}</td>
                                        <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่มีข้อมูลยอดขายออนไลน์ในช่วงเวลาที่เลือก</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
            window.location.href = url;
        });
    }
});
</script>



<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #printHeader, #printHeader * {
            visibility: visible;
            display: block !important;
        }

        #reportSection, #reportSection * {
            visibility: visible;
        }

        #reportSection {
            position: absolute;
            left: 0;
            top: 60px; /* Adjust if header is taller */
            width: 100%;
            padding: 0 15px; /* Add some padding for print */
        }

        /* Hide elements not needed in print */
        .form-control, .form-label, .btn, .card-header button, .shadow-sm {
            display: none !important;
        }

        .card, .table {
            border: 1px solid #dee2e6 !important; /* Ensure borders are visible */
        }

        .card-header {
            background-color: #e9ecef !important; /* Light background for print */
            color: #212529 !important;
        }

        body {
            zoom: 85%; /* Adjust print scale */
        }
    }
</style>

{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Get the context for the chart
    const ctx = document.getElementById('salesChart').getContext('2d');

    // Create a linear gradient for the chart background
    const gradient = ctx.createLinearGradient(0, 0, 0, 400); // Top to bottom gradient
    gradient.addColorStop(0, 'rgba(75, 192, 192, 0.7)'); // Start color (teal)
    gradient.addColorStop(1, 'rgba(75, 192, 192, 0.2)'); // End color (lighter teal)

    new Chart(ctx, {
      type: 'bar', // Changed to bar chart for better visibility
data: {
    labels: {!! json_encode($chartLabels) !!},
    datasets: [{
        label: 'ยอดขายรวม (บาท)',
        data: {!! json_encode($chartData) !!},
        fill: true,
        backgroundColor: gradient,
        borderColor: 'rgba(75, 192, 192, 1)',
        tension: 0.4, // ทำให้เส้นโค้ง สวยงาม
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: 'white',
        pointBorderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 2,
    }]
}
,
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows you to control height
            animation: {
                duration: 1500, // Animation duration in milliseconds
                easing: 'easeOutQuart' // Smooth easing function
            },
            plugins: {
                legend: {
                    display: false, // Hide the legend as there's only one dataset
                },
                title: {
                    display: true,
                    text: 'แนวโน้มยอดขายตามช่วงเวลา',
                    font: {
                        size: 18,
                        weight: 'bold',
                        family: 'Arial, sans-serif' // Specify a font family
                    },
                    color: '#333' // Darker color for title
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)', // Darker background
                    titleColor: '#fff', // White title
                    bodyColor: '#fff', // White body text
                    borderColor: 'rgba(255, 255, 255, 0.3)', // Light border
                    borderWidth: 1,
                    cornerRadius: 5, // Rounded tooltips
                    displayColors: false, // Hide the color box in tooltip
                    callbacks: {
                        label: function(context) {
                            return 'ยอดขาย: ' + context.parsed.y.toLocaleString() + ' ฿';
                        },
                        title: function(context) {
                            return 'วันที่: ' + context.label; // Prepend "วันที่:" to the date
                        }
                    }
                }
            },
            scales: {
    y: {
        beginAtZero: true,
        ticks: {
            stepSize: 100, // <<< กำหนดระยะห่างต่อเส้น (เช่น 500 บาท)
            callback: function(value) {
                return value.toLocaleString() + ' ฿';
            },
            color: '#666',
            font: {
                size: 12
            }
        },
        grid: {
            color: 'rgba(0, 0, 0, 0.08)',
            drawBorder: false
        }
    },
    x: {
        ticks: {
            color: '#666',
            font: {
                size: 12
            }
        },
        grid: {
            display: false
        }
    }
}

        }
    });
</script>
@endsection