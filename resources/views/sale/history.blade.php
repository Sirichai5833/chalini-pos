@extends('layouts.layout')

@section('content')
    <div class="container-fluid py-4 sales-history-page">
        {{-- Page Header --}}
        <section class="sales-hero">
            <div class="sales-hero-content">
                <div class="sales-hero-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="sales-kicker">SALES REPORT</div>
                    <h2 class="sales-title">ประวัติการขาย</h2>
                   
                </div>
            </div>
        </section>

        {{-- Filter Form --}}
        <div class="card sales-card mb-4">
            <div class="card-header sales-card-header">
                <h5 class="sales-card-title">
                    <i class="bi bi-funnel-fill text-primary me-2"></i> ตัวกรองการค้นหา
                </h5>
            </div>
            <div class="card-body p-4">
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
                        <button type="submit" class="btn btn-sales-primary px-4">
                            <i class="bi bi-search me-2"></i> ค้นหา
                        </button>
                       
                        <button type="button" onclick="exportToExcel()" class="btn btn-sales-excel px-4">
                            <i class="bi bi-file-earmark-excel-fill me-2"></i> ส่งออก Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sales Table --}}
        <div class="card sales-card">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="card-title m-0 text-dark fw-bold">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i> รายการประวัติการขาย
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" id="print-area">
                    <table class="table sales-table table-hover align-middle mb-0">
                        <thead class="sales-table-head">
                            <tr>
                                <th scope="col" class="py-3 text-center" style="width: 80px;">ลำดับ</th>
                                <th scope="col" class="py-3 text-center">รหัสการขาย</th>
                                <th scope="col" class="py-3 text-center">วันที่-เวลา</th>
                                <th scope="col" class="py-3">ผู้ขาย</th>
                                <th scope="col" class="py-3 text-center">ประเภทการขาย</th>
                                <th scope="col" class="py-3 text-center">สถานะ</th>
                                <th scope="col" class="py-3 text-end" style="width: 120px;">จำนวนสินค้า</th>
                                <th scope="col" class="py-3 text-end" style="width: 150px;">ยอดรวมสุทธิ</th>
                                <th scope="col" class="py-3 text-center action-column" style="width: 140px;">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr class="{{ $sale->status === 'cancelled' ? 'sale-row-cancelled' : '' }}">
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-center sale-code">#{{ $sale->id }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $sale->staff->name ?? 'ไม่ระบุชื่อ' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($sale->sale_type == 'retail')
                                            <span class="badge sale-badge sale-retail">ขายปลีก</span>
                                        @elseif($sale->sale_type == 'wholesale')
                                            <span class="badge sale-badge sale-wholesale">ขายส่ง</span>
                                        @else
                                            <span class="badge sale-badge sale-other">{{ ucfirst($sale->sale_type) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($sale->status === 'cancelled')
                                            <span class="badge sale-badge sale-cancelled">
                                                <i class="bi bi-x-circle-fill me-1"></i> ยกเลิกแล้ว
                                            </span>
                                        @else
                                            <span class="badge sale-badge sale-completed">
                                                <i class="bi bi-check-circle-fill me-1"></i> สำเร็จ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">{{ number_format($sale->items->sum('quantity')) }}</td>
                                    <td class="text-end sale-total">{{ number_format($sale->total_price, 2) }} ฿</td>
                                    <td class="text-center action-column">
                                        <a href="{{ route('staff.sales.show', $sale->id) }}"
                                           class="btn btn-sm {{ $sale->status === 'cancelled' ? 'btn-sale-cancelled-detail' : 'btn-sale-detail' }}"
                                           title="ดูรายละเอียด">
                                            <i class="bi bi-eye-fill me-1"></i>
                                            {{ $sale->status === 'cancelled' ? 'ดูรายการที่ยกเลิก' : 'รายละเอียด' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
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


    <style>
        :root {
            --sales-yellow: #ffd43b;
            --sales-yellow-soft: #ffe978;
            --sales-black: #080808;
            --sales-panel: #151515;
            --sales-border: rgba(255, 212, 59, .22);
            --sales-text: #f7f7f7;
            --sales-muted: #f7f7f7;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--sales-black);
        }

        .sales-history-page {
            max-width: 1500px;
            color: var(--sales-text);
        }

        .sales-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--sales-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 90% 10%, rgba(255, 212, 59, .22), transparent 26%),
                linear-gradient(135deg, #0a0a0a 0%, #171717 62%, #2b2300 100%);
            box-shadow: 0 20px 48px rgba(0,0,0,.38);
        }

        .sales-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -130px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .09);
        }

        .sales-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sales-hero-icon {
            width: 62px;
            height: 62px;
            display: grid;
            place-items: center;
            flex: none;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--sales-yellow), var(--sales-yellow-soft));
            color: #111;
            font-size: 1.45rem;
            box-shadow: 0 12px 28px rgba(255, 212, 59, .22);
        }

        .sales-kicker {
            color: var(--sales-yellow);
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .16em;
        }

        .sales-title {
            margin: .1rem 0 0;
            color: #fff;
            font-size: clamp(1.45rem, 2.7vw, 2rem);
            font-weight: 950;
        }

        .sales-subtitle {
            margin: .35rem 0 0;
            color: #c3c3c3;
            font-size: .9rem;
        }

        .sales-card {
            overflow: hidden;
            border: 1px solid #2e2e2e !important;
            border-radius: 20px !important;
            background: linear-gradient(180deg, #181818, #101010) !important;
            color: var(--sales-text);
            box-shadow: 0 16px 40px rgba(0,0,0,.3);
        }

        .sales-card-header {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--sales-border);
            background: linear-gradient(135deg, #202020, #111);
        }

        .sales-card-title {
            display: flex;
            align-items: center;
            gap: .55rem;
            margin: 0;
            color: var(--sales-yellow);
            font-size: .98rem;
            font-weight: 900;
        }

        .sales-history-page .form-label {
            color: #ddd !important;
            font-size: .78rem;
            font-weight: 800 !important;
        }

        .sales-history-page .form-control,
        .sales-history-page .form-select {
            min-height: 45px;
            border: 1px solid #3d3d3d !important;
            border-radius: 12px;
            background: #0d0d0d !important;
            color: #fff !important;
            box-shadow: none !important;
        }

        .sales-history-page .form-control:focus,
        .sales-history-page .form-select:focus {
            border-color: var(--sales-yellow) !important;
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12) !important;
        }

        .sales-history-page .form-select option { background: #111; color: #fff; }
        .sales-history-page input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }

        .btn-sales-primary,
        .btn-sales-outline,
        .btn-sales-excel {
            min-height: 44px;
            border-radius: 12px;
            font-weight: 850;
        }

        .btn-sales-primary {
            border: 1px solid var(--sales-yellow);
            background: linear-gradient(135deg, var(--sales-yellow), var(--sales-yellow-soft));
            color: #111;
        }

        .btn-sales-outline {
            border: 1px solid #494949;
            background: #171717;
            color: #ddd;
        }

        .btn-sales-excel {
            border: 1px solid rgba(34, 197, 94, .5);
            background: rgba(34, 197, 94, .08);
            color: #86efac;
        }

        .sales-table {
            --bs-table-bg: transparent;
            --bs-table-color: #eee;
            --bs-table-border-color: #2e2e2e;
            --bs-table-hover-bg: rgba(255, 212, 59, .05);
            --bs-table-hover-color: #fff;
        }

        .sales-table thead th {
            background: #0e0e0e !important;
            color: var(--sales-yellow) !important;
            border-color: #303030;
            font-size: .73rem;
            font-weight: 900;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .sales-table td {
            color: #e9e9e9;
            border-color: #303030;
            padding-top: .8rem;
            padding-bottom: .8rem;
        }

        .sale-code { color: #fff !important; font-weight: 900; }
        .sale-total { color: #86efac !important; font-weight: 950; }

        .sale-badge {
            padding: .4rem .75rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 850;
        }

        .sale-retail {
            border: 1px solid rgba(255, 212, 59, .35);
            background: rgba(255, 212, 59, .1);
            color: var(--sales-yellow);
        }

        .sale-wholesale {
            border: 1px solid rgba(34, 197, 94, .35);
            background: rgba(34, 197, 94, .1);
            color: #86efac;
        }

        .sale-other {
            border: 1px solid #484848;
            background: #222;
            color: #ccc;
        }


        .sale-completed { border:1px solid rgba(34,197,94,.35); background:rgba(34,197,94,.1); color:#86efac; }
        .sale-cancelled { border:1px solid rgba(239,68,68,.42); background:rgba(239,68,68,.12); color:#fca5a5; }
        .sale-row-cancelled td { opacity:.68; background:rgba(239,68,68,.025)!important; }
        .sale-row-cancelled .sale-total { color:#fca5a5!important; text-decoration:line-through; }
        .btn-sale-cancelled-detail { border:1px solid rgba(239,68,68,.32); border-radius:10px; background:rgba(239,68,68,.08); color:#fca5a5; font-weight:800; }
        .btn-sale-cancelled-detail:hover { background:#ef4444; color:#fff; }

        .btn-sale-detail {
            border: 1px solid rgba(255, 212, 59, .32);
            border-radius: 10px;
            background: rgba(255, 212, 59, .07);
            color: var(--sales-yellow);
            font-weight: 800;
        }

        .btn-sale-detail:hover {
            background: var(--sales-yellow);
            color: #111;
        }

        @media (max-width: 767.98px) {
            .sales-history-page {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            }

            .sales-hero { padding: 1.2rem; border-radius: 19px; }
            .sales-hero-icon { width: 52px; height: 52px; }
            .sales-card { border-radius: 17px !important; }

            .sales-history-page .col-12.d-flex {
                display: grid !important;
                grid-template-columns: 1fr;
            }

            .sales-history-page .col-12.d-flex .btn { width: 100%; }
        }

        /* ===== เพิ่มความชัดเจนของตัวอักษรทั้งหน้า ===== */
        .sales-history-page,
        .sales-history-page * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sales-title {
            font-size: clamp(1.7rem, 3vw, 2.25rem);
            font-weight: 950;
        }

        .sales-subtitle {
            color: #e0e0e0;
            font-size: 1rem;
            font-weight: 600;
        }

        .sales-kicker {
            font-size: .8rem;
            font-weight: 950;
        }

        .sales-card-title {
            font-size: 1.08rem;
            font-weight: 950;
        }

        .sales-history-page .form-label {
            color: #f0f0f0 !important;
            font-size: .9rem;
            font-weight: 900 !important;
        }

        .sales-history-page .form-control,
        .sales-history-page .form-select {
            min-height: 48px;
            font-size: .95rem;
            font-weight: 700;
        }

        .btn-sales-primary,
        .btn-sales-outline,
        .btn-sales-excel {
            min-height: 46px;
            font-size: .92rem;
            font-weight: 900;
        }

        .sales-table thead th {
            color: var(--sales-yellow) !important;
            font-size: .85rem;
            font-weight: 950;
        }

        .sales-table td {
            color: #fff;
            font-size: .92rem;
            font-weight: 650;
        }

        .sales-table td.text-muted {
            color: #d0d0d0 !important;
        }

        .sale-code {
            color: #fff !important;
            font-size: 1rem;
            font-weight: 950;
        }

        .sale-total {
            color: #9af0bc !important;
            font-size: 1rem;
            font-weight: 950;
        }

        .sale-badge {
            padding: .45rem .78rem;
            font-size: .82rem;
            font-weight: 900;
        }

        .btn-sale-detail,
        .btn-sale-cancelled-detail {
            font-size: .84rem;
            font-weight: 900;
        }

        .sale-row-cancelled td {
            opacity: .86;
        }

        .sale-row-cancelled .sale-total {
            opacity: 1;
        }

        .card-title {
            font-size: 1.08rem;
            font-weight: 950 !important;
        }

        @media (max-width: 767.98px) {
            .sales-table thead th {
                font-size: .8rem;
            }

            .sales-table td {
                font-size: .88rem;
            }
        }

    </style>

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