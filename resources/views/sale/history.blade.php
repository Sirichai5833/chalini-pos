@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <h2>ประวัติการขาย</h2>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <label>ตั้งแต่วันที่</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label>ถึงวันที่</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            @php $isAdmin = Auth::user()->is_admin; @endphp

@if($isAdmin)
<div class="col-md-3">
    <label>ผู้ขาย</label>
    <select name="staff_id" class="form-control">
        <option value="">-- ทั้งหมด --</option>
        @foreach ($staffs as $staff)
            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                {{ $staff->name }}
            </option>
        @endforeach
    </select>
</div>
@endif
            <div class="col-md-3">
                <label>ประเภทการขาย</label>
                <select name="sale_type" class="form-control">
                    <option value="">-- ทั้งหมด --</option>
                    <option value="retail" {{ request('sale_type') == 'retail' ? 'selected' : '' }}>ขายปลีก</option>
                    <option value="wholesale" {{ request('sale_type') == 'wholesale' ? 'selected' : '' }}>ขายส่ง</option>
                </select>
            </div>
            <div class="text-end mb-3">
                <button onclick="printTable()" class="btn btn-outline-secondary">
                    🖨️ พิมพ์
                </button>
            </div>
            <div class="col-12 text-end mt-2">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </form>
        <div id="print-area">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ลำดับที่</th>
                        <th>รหัสการขาย</th>
                        <th>วันที่</th>
                        <th>ผู้ขาย</th>
                        <th>ประเภทการขาย</th>
                        <th>จำนวนสินค้า</th>
                        <th>ยอดรวม</th>
                        <th>รายละเอียด</th>
                    </tr>
                </thead>
                @php $number = 1; @endphp
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $number++ }}</td>
                            <td>{{ $sale->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->staff->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($sale->sale_type) }}</td>
                            <td>{{ $sale->items->sum('quantity') }}</td>
                            <td>{{ number_format($sale->total_price, 2) }} ฿</td>
                            <td>
                                {{-- <ul>
                                    @foreach ($sale->items as $item)
                                        <li>{{ optional($item->product)->name }} ({{ optional($item->unit)->unit_name }})
                                            - {{ $item->quantity }} x {{ number_format($item->price, 2) }} ฿</li>
                                    @endforeach
                                </ul> --}}
                                <div>
                                <a href="{{ route('staff.sale.show', $sale->id) }}" class="btn btn-sm btn-info" >
                                    ดูรายละเอียด
                                </a>
                                </div>
                            </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function printTable() {
            const printContents = document.getElementById('print-area').innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // โหลดใหม่เพื่อให้ JS กลับมา
        }
    </script>
@endsection
