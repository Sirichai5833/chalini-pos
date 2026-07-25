@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">🔍 ประวัติการนำสินค้าเข้า</h3>

    <form method="GET" action="{{ route('product.stock-in-history') }}" class="mb-4">
    <div class="row g-2 align-items-end">
        {{-- ช่องค้นหาด้วยชื่อหรือบาร์โค้ด --}}
        <div class="col-md-4">
            <label class="form-label">🔍 ค้นหาชื่อสินค้า หรือบาร์โค้ด</label>
            <input type="text" name="search" class="form-control" placeholder="ชื่อสินค้า หรือบาร์โค้ด" value="{{ $search }}">
        </div>

        {{-- วันที่เริ่มต้น --}}
        <div class="col-md-3">
            <label class="form-label">📅 วันที่เริ่มต้น</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>

        {{-- วันที่สิ้นสุด --}}
        <div class="col-md-3">
            <label class="form-label">📅 ถึงวันที่</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>

        {{-- ปุ่มค้นหา --}}
        <div class="col-md-2 d-grid">
            <label class="form-label invisible">ค้นหา</label>
            <button type="submit" class="btn btn-primary">
                🔎 ค้นหา
            </button>
        </div>
    </div>
</form>


    @if($movements->count() > 0)
       <div class="mb-3">
    <button type="button"
        class="btn btn-success btn-sm"
        onclick="exportToExcel()">
        📊 Export Excel
    </button>

    <a href="{{ route('product.stock-in-history', array_merge(request()->all(), ['print'=>1])) }}"
        class="btn btn-outline-dark btn-sm">
        🖨️ พิมพ์รายการ
    </a>
</div>




        <div class="table-responsive">
            <table id="stockHistoryTable"
       class="table table-bordered text-center align-middle printable-area">
                <thead class="table-dark">
                    <tr>
                        <th>วันที่</th>
                        <th>สินค้า</th>
                        <th>จำนวน</th>
                        <th>หน่วย</th>
                        <th>สถานที่</th>
                        {{-- <th>หมายเหตุ</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $move)
                        <tr>
                            <td>{{ $move->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $move->product->name ?? '-' }}</td>
                            <td>{{ $move->quantity }}</td>
                            <td>{{ $move->unit ?? '-' }}</td>
                            <td>{{ $move->location === 'warehouse' ? 'คลัง' : 'หน้าร้าน' }}</td>
                            {{-- <td>{{ $move->note ?? '-' }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(!$isPrint && $movements instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $movements->withQueryString()->links() }}
@endif
    @else
        <p class="text-muted">ไม่พบรายการ</p>
    @endif
</div>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }

    .printable-area, .printable-area * {
        visibility: visible !important;
    }

    .printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        page-break-inside: auto;
    }

    thead {
        display: table-header-group;
    }

    tr, td, th {
        page-break-inside: avoid;
    }
}
</style>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
function exportToExcel() {

    // สร้าง Workbook
    let wb = XLSX.utils.book_new();

    // สร้าง Sheet
    let ws = XLSX.utils.aoa_to_sheet([
        ["ประวัติการนำสินค้าเข้า"],
        ["ค้นหา", "{{ $search }}"],
        ["วันที่เริ่ม", "{{ $from }}"],
        ["วันที่สิ้นสุด", "{{ $to }}"],
        []
    ]);

    // เพิ่มตาราง HTML ลงใน Sheet
    XLSX.utils.sheet_add_dom(
        ws,
        document.getElementById("stockHistoryTable"),
        {
            origin: "A6"
        }
    );

    // เพิ่ม Sheet เข้า Workbook
    XLSX.utils.book_append_sheet(
        wb,
        ws,
        "ประวัตินำสินค้าเข้า"
    );

    // ดาวน์โหลดไฟล์
    XLSX.writeFile(
        wb,
        "stock-in-history.xlsx"
    );
}
</script>
@if($isPrint)
<script>
    window.onload = function () {
        window.print();
    };
</script>

@endif

@endsection
