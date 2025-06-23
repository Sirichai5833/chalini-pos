@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📊 รายงานยอดขาย</h2>

    <!-- ฟอร์มเลือกช่วงเวลา -->
    <form id="reportForm" class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <label for="reportType" class="form-label">ประเภทช่วงเวลา</label>
            <select class="form-select" id="reportType" name="report_type">
                <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>รายวัน</option>
                <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>รายเดือน</option>
                <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>รายปี</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="reportDate" class="form-label">เลือกวันที่</label>
            <input type="date" class="form-control" id="reportDate" name="report_date" value="{{ request('report_date', date('Y-m-d')) }}">
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary">แสดงรายงาน</button>
        </div>
        <div class="col-md-3 align-self-end text-end">
            <button type="button" class="btn btn-success" onclick="window.print()">🖨 พิมพ์รายงาน</button>
        </div>
    </form>

    <!-- ยอดขายรวม -->
   <!-- ยอดขายรวม -->
   <div id="reportSection">
    <div id="printHeader" style="display:none; text-align:right; margin-bottom: 10px;">
    รายงานวันที่: {{ \Carbon\Carbon::parse(request('report_date', date('Y-m-d')))->format('d M Y') }}
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5>ยอดขายรวม: <span id="totalSales">{{ number_format($totalSales, 2) }}</span> บาท</h5>
        <p class="mb-1">💼 หน้าร้าน: {{ number_format($sales->sum('total_price'), 2) }} บาท</p>
        <p class="mb-0">🛒 ออนไลน์: {{ number_format($onlineSalesTotal ?? 0, 2) }} บาท</p>
    </div>
</div>

<!-- ใต้ยอดขายรวม -->
<div class="card mb-4">
    <div class="card-body">
        <h6>ต้นทุนรวม: <span>{{ number_format($totalCost, 2) }}</span> บาท</h6>
        <h5 class="mt-2 text-success">กำไรสุทธิ: <span>{{ number_format($netProfit, 2) }}</span> บาท</h5>
        <h6>อัตรากำไร: {{ number_format(($netProfit / max($totalSales, 1)) * 100, 2) }}%</h6>

    </div>
</div>



<!-- กราฟยอดขาย -->
<div class="card mb-4">
    <div class="card-header">📈 กราฟแสดงยอดขาย</div>
    <div class="card-body">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>


    <!-- ตารางยอดขาย -->
    <div class="card mb-4">
        <div class="card-header">📅 รายการยอดขาย</div>
        <div class="card-body">
            <table class="table table-bordered" id="salesTable">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>รหัสการขาย</th>
                        <th>พนักงาน</th>
                        <th>ยอดรวม (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d H:i') }}</td>
                        <td>#{{ str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $sale->staff->name ?? 'ไม่ระบุ' }}</td>
                        <td>{{ number_format($sale->total_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">ไม่มีข้อมูลยอดขายในช่วงเวลาที่เลือก</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- รายการยอดขายออนไลน์ -->
<div class="card mb-4">
    <div class="card-header">🛒 รายการยอดขายออนไลน์</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>รหัสคำสั่งซื้อ</th>
                    <th>ลูกค้า</th>
                    <th>ยอดรวม (บาท)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($onlineOrders as $order)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
                        <td>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->user->name ?? 'ไม่ระบุ' }}</td>
                        <td>{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">ไม่มีข้อมูลยอดขายออนไลน์</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


    <!-- รายงานสินค้าขายดี -->
    <div class="card">
        <div class="card-header">🏆 สินค้าขายดี</div>
        <div class="card-body">
            <table class="table table-bordered" id="topProductsTable">
                <thead>
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>จำนวนที่ขาย</th>
                        <th>ยอดขายรวม (บาท)</th>
                        <div class="card mb-3">
</div>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_quantity }}</td>
                        <td>{{ number_format($product->total_sales, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">ไม่มีข้อมูลสินค้าขายดี</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- สินค้าขายดีออนไลน์ -->
<div class="card">
    <div class="card-header">🌐 สินค้าขายดี (ออนไลน์)</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวนที่ขาย</th>
                    <th>ยอดขายรวม (บาท)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topOnlineProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_quantity }}</td>
                        <td>{{ number_format($product->total_sales, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">ไม่มีข้อมูลสินค้าขายดีออนไลน์</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>





    </div>
</div>



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
            top: 30px; /* เว้นไว้ให้ header */
            width: 100%;
        }

        body {
            zoom: 85%;
        }
    }
</style>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!}, // ['01/06', '02/06', ...]
            datasets: [{
                label: 'ยอดขายรวม (บาท)',
                data: {!! json_encode($chartData) !!}, // [1200, 1450, ...]
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display:false,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'แนวโน้มยอดขาย'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' ฿';
                        }
                    }
                }
            }
        }
    });
</script>


@endsection
