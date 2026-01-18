@extends('layouts.layout')

@section('content')
    <div class="container-fluid py-4 px-3"> {{-- Use container-fluid for full width, py-4 for vertical padding, px-3 for horizontal --}}
        <h2 class="mb-4 text-primary fw-bold text-center">
            <i class="bi bi-clock-history me-2"></i> ประวัติการขาย
        </h2>

        {{-- Filter Form --}}
        <div class="card shadow-lg rounded-3 mb-4 border-0"> {{-- Card for the filter form with shadow and rounded corners --}}
            <div class="card-body p-4"> {{-- More padding inside the card --}}
                <h5 class="card-title mb-3 text-dark fw-bold border-bottom pb-2">
                    <i class="bi bi-funnel-fill me-2"></i> ตัวกรองการค้นหา
                </h5>
                <form method="GET" class="row g-3 align-items-end"> {{-- Use g-3 for more gutter space, align-items-end for button alignment --}}
                    <div class="col-md-3 col-sm-6">
                        <label for="from_date" class="form-label text-muted">ตั้งแต่วันที่</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label for="to_date" class="form-label text-muted">ถึงวันที่</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    @php $isAdmin = Auth::user()->is_admin; @endphp
                    @if($isAdmin)
                        <div class="col-md-3 col-sm-6">
                            <label for="staff_id" class="form-label text-muted">ผู้ขาย</label>
                            <select name="staff_id" id="staff_id" class="form-select"> {{-- Use form-select for select inputs --}}
                                <option value="">-- ทั้งหมด --</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-3 col-sm-6">
                        <label for="sale_type" class="form-label text-muted">ประเภทการขาย</label>
                        <select name="sale_type" id="sale_type" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="retail" {{ request('sale_type') == 'retail' ? 'selected' : '' }}>ขายปลีก</option>
                            <option value="wholesale" {{ request('sale_type') == 'wholesale' ? 'selected' : '' }}>ขายส่ง</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2"> {{-- Use flexbox for button alignment and gap --}}
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-search me-2"></i> ค้นหา
                        </button>
                        <button type="button" onclick="printTable()" class="btn btn-outline-secondary btn-lg shadow-sm">
                            <i class="bi bi-printer-fill me-2"></i> พิมพ์
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sales Table --}}
        <div id="print-area" class="card shadow-lg rounded-3 border-0"> {{-- Card for the table with shadow and rounded corners --}}
            <div class="card-body p-4">
                <h5 class="card-title mb-3 text-dark fw-bold border-bottom pb-2">
                    <i class="bi bi-receipt-cutoff me-2"></i> รายการประวัติการขาย
                </h5>
                <div class="table-responsive"> {{-- Ensures table is scrollable on small screens --}}
                    <table class="table table-striped table-hover text-center align-middle mb-0"> {{-- Striped, hover, and aligned middle --}}
                        <thead class="bg-primary text-white"> {{-- Primary background for header --}}
                            <tr>
                                <th scope="col" class="py-3">ลำดับที่</th>
                                <th scope="col" class="py-3">รหัสการขาย</th>
                                <th scope="col" class="py-3">วันที่</th>
                                <th scope="col" class="py-3">ผู้ขาย</th>
                                <th scope="col" class="py-3">ประเภทการขาย</th>
                                <th scope="col" class="py-3">จำนวนสินค้า</th>
                                <th scope="col" class="py-3">ยอดรวม</th>
                                <th scope="col" class="py-3"></th> {{-- For details button --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td> {{-- Use $loop->iteration for cleaner numbering --}}
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sale->staff->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($sale->sale_type == 'retail')
                                            <span class="badge bg-primary">ขายปลีก</span>
                                        @elseif($sale->sale_type == 'wholesale')
                                            <span class="badge bg-success">ขายส่ง</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($sale->sale_type) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $sale->items->sum('quantity') }}</td>
                                    <td class="fw-bold text-success">{{ number_format($sale->total_price, 2) }} ฿</td>
                                    <td>
                                        <a href="{{ route('staff.sales.show', $sale->id) }}" class="btn btn-sm btn-info text-white shadow-sm" title="ดูรายละเอียด">
                                            <i class="bi bi-info-circle-fill me-1"></i> รายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-exclamation-circle me-2"></i> ไม่พบข้อมูลประวัติการขายในช่วงเวลาที่เลือก.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printTable() {
            // Get the content of the print area
            const printContents = document.getElementById('print-area').innerHTML;
            const originalContents = document.body.innerHTML;

            // Create a new window for printing to ensure only the desired content is printed
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>รายงานประวัติการขาย</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { font-family: 'Sarabun', sans-serif; margin: 20px; }
                        h2 { text-align: center; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
                        th { background-color: #e9ecef; } /* Light grey for print header */
                        .text-center { text-align: center; }
                        .fw-bold { font-weight: bold; }
                        .text-success { color: #198754; }
                        .badge {
                            display: inline-block;
                            padding: .35em .65em;
                            font-size: .75em;
                            font-weight: 700;
                            line-height: 1;
                            color: #fff;
                            text-align: center;
                            white-space: nowrap;
                            vertical-align: baseline;
                            border-radius: .25rem;
                        }
                        .bg-primary { background-color: #0d6efd !important; }
                        .bg-success { background-color: #198754 !important; }
                        .bg-secondary { background-color: #6c757d !important; }
                        /* Hide details button in print view */
                        .btn-info { display: none; }
                    </style>
                </head>
                <body>
                    <h2 class="text-center">รายงานประวัติการขาย</h2>
                    ${printContents}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close(); // Close the print window after printing

            // No need to reload the original page as we didn't change its DOM
            // location.reload();
        }
    </script>
@endsection