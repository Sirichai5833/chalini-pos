@extends('layouts.layout')

@section('content')
    <div class="container-fluid py-4 px-4">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold m-0">
                <i class="bi bi-clock-history me-2"></i> ประวัติการขาย
            </h2>
        </div>

        {{-- Filter Form --}}
        <div class="card shadow-sm rounded-3 mb-4 border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="card-title m-0 text-dark fw-bold">
                    <i class="bi bi-funnel-fill text-primary me-2"></i> ตัวกรองการค้นหา
                </h5>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-xl-3 col-md-6">
                        <label for="from_date" class="form-label fw-semibold text-secondary small">ตั้งแต่วันที่</label>
                        <input type="date" name="from_date" id="from_date" class="form-control border-2" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <label for="to_date" class="form-label fw-semibold text-secondary small">ถึงวันที่</label>
                        <input type="date" name="to_date" id="to_date" class="form-control border-2" value="{{ request('to_date') }}">
                    </div>

                    @php $isAdmin = Auth::user()->is_admin; @endphp
                    @if($isAdmin)
                        <div class="col-xl-3 col-md-6">
                            <label for="staff_id" class="form-label fw-semibold text-secondary small">ผู้ขาย</label>
                            <select name="staff_id" id="staff_id" class="form-select border-2">
                                <option value="">-- ทั้งหมด --</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-xl-3 col-md-6">
                        <label for="sale_type" class="form-label fw-semibold text-secondary small">ประเภทการขาย</label>
                        <select name="sale_type" id="sale_type" class="form-select border-2">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="retail" {{ request('sale_type') == 'retail' ? 'selected' : '' }}>ขายปลีก</option>
                            <option value="wholesale" {{ request('sale_type') == 'wholesale' ? 'selected' : '' }}>ขายส่ง</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-search me-2"></i> ค้นหา
                        </button>
                        <button type="button" onclick="printTable()" class="btn btn-outline-secondary px-4 shadow-sm">
                            <i class="bi bi-printer-fill me-2"></i> พิมพ์รายงาน
                        </button>
                        <button type="button" onclick="exportToExcel()" class="btn btn-outline-success px-4 shadow-sm">
                            <i class="bi bi-file-earmark-excel-fill me-2"></i> ส่งออก Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="card-title m-0 text-dark fw-bold">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i> รายการประวัติการขาย
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" id="print-area">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom text-secondary">
                            <tr>
                                <th scope="col" class="py-3 text-center" style="width: 80px;">ลำดับ</th>
                                <th scope="col" class="py-3 text-center">รหัสการขาย</th>
                                <th scope="col" class="py-3 text-center">วันที่-เวลา</th>
                                <th scope="col" class="py-3">ผู้ขาย</th>
                                <th scope="col" class="py-3 text-center">ประเภทการขาย</th>
                                <th scope="col" class="py-3 text-end" style="width: 120px;">จำนวนสินค้า</th>
                                <th scope="col" class="py-3 text-end" style="width: 150px;">ยอดรวมสุทธิ</th>
                                <th scope="col" class="py-3 text-center action-column" style="width: 140px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-semibold text-dark">#{{ $sale->id }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $sale->staff->name ?? 'ไม่ระบุชื่อ' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($sale->sale_type == 'retail')
                                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">ขายปลีก</span>
                                        @elseif($sale->sale_type == 'wholesale')
                                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">ขายส่ง</span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">{{ ucfirst($sale->sale_type) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($sale->items->sum('quantity')) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($sale->total_price, 2) }} ฿</td>
                                    <td class="text-center action-column">
                                        <a href="{{ route('staff.sales.show', $sale->id) }}" class="btn btn-sm btn-light border text-dark shadow-sm" title="ดูรายละเอียด">
                                            <i class="bi bi-eye-fill me-1 text-primary"></i> รายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-exclamation-circle fs-3 d-block mb-2 text-secondary"></i>
                                        ไม่พบข้อมูลประวัติการขายในช่วงเวลาที่เลือก
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
        // 1. ฟังก์ชันพิมพ์รายงาน (ปรับปรุงดีไซน์ให้สะอาดตา คอลัมน์ปุ่มไม่โผล่)
        function printTable() {
            const printContents = document.getElementById('print-area').innerHTML;
            const printWindow = window.open('', '_blank');
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>รายงานประวัติการขาย</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
                        body { font-family: 'Sarabun', sans-serif; padding: 30px; color: #333; }
                        h2 { text-align: center; font-weight: 700; margin-bottom: 5px; }
                        .print-meta { text-align: center; font-size: 14px; color: #666; margin-bottom: 25px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid #dee2e6 !important; padding: 10px 8px !important; font-size: 14px; }
                        th { background-color: #f8f9fa !important; color: #333 !important; font-weight: 600; text-align: center !important; }
                        .text-center { text-align: center !important; }
                        .text-end { text-align: right !important; }
                        .fw-bold { font-weight: bold; }
                        .badge { border: 1px solid #ccc; padding: 4px 8px; border-radius: 4px; font-size: 12px; background: none !important; color: #000 !important; }
                        /* ซ่อนคอลัมน์ปุ่มกด "รายละเอียด" ขณะพิมพ์ */
                        .action-column { display: none !important; }
                    </style>
                </head>
                <body>
                    <h2>รายงานประวัติการขาย</h2>
                    <div class="print-meta">พิมพ์เมื่อวันที่: ${new Date().toLocaleDateString('th-TH')} เวลา ${new Date().toLocaleTimeString('th-TH')}</div>
                    ${printContents}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            
            // รอให้สไตล์ชีทโหลดเสร็จก่อนสั่งพิมพ์
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        // 2. ฟังก์ชันส่งออก Excel (CSV Format รองรับภาษาไทย 100%)
        function exportToExcel() {
            const table = document.querySelector("#print-area table");
            if (!table) return;

            // ดึงแถวทั้งหมดของตาราง
            const rows = table.querySelectorAll("tr");
            let csvContent = "\uFEFF"; // ใส่ BOM เพื่อให้ Excel เปิดอ่านภาษาไทยได้ถูกต้อง ไม่เป็นต่างดาว

            rows.forEach(row => {
                const cols = row.querySelectorAll("th, td");
                let rowData = [];

                // วนลูปดึงข้อมูลทีละคอลัมน์ (เว้นคอลัมน์สุดท้ายที่เป็นปุ่มจัดการออกไป)
                for (let i = 0; i < cols.length; i++) {
                    if (cols[i].classList.contains('action-column')) continue; 

                    let text = cols[i].innerText.trim();
                    // จัดการเครื่องหมายคำพูดคู่คู่ เพื่อไม่ให้โครงสร้างไฟล์พัง
                    text = text.replace(/"/g, '""');
                    // ใส่เครื่องหมายอัญประกาศครอบข้อความไว้เพื่อความปลอดภัยของฟอร์แมต
                    rowData.push(`"${text}"`);
                }

                if (rowData.length > 0) {
                    csvContent += rowData.join(",") + "\r\n";
                }
            });

            // สร้างลิงก์ดาวน์โหลดไฟล์
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            
            // ตั้งชื่อไฟล์ตามวันที่ปัจจุบัน
            const today = new Date().toISOString().slice(0, 10);
            link.setAttribute("href", url);
            link.setAttribute("download", `รายงานประวัติการขาย_${today}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection