@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2>รายละเอียดการขาย #{{ $sale->id }}</h2>
    <p><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</p>
    <p><strong>ผู้ขาย:</strong> {{ $sale->staff->name ?? 'N/A' }}</p>
    <p><strong>ประเภทการขาย:</strong> {{ ucfirst($sale->sale_type) }}</p>
    <p><strong>ยอดรวม:</strong> {{ number_format($sale->total_price, 2) }} ฿</p>

    <h4>รายการสินค้า</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>สินค้า</th>
                <th>หน่วย</th>
                <th>จำนวน</th>
                <th>ราคาต่อหน่วย</th>
                <th>รวม</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ optional($item->product)->name ?? 'ไม่พบสินค้า' }}</td>
                    <td>{{ optional($item->unit)->unit_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅️ กลับ</a>

    {{-- <a href="{{ route('staff.sales.edit', $sale->id) }}" class="btn btn-warning">✏️ แก้ไข</a> --}}

    @if (Auth::user()->is_admin)
        <form action="{{ route('staff.sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการขายนี้?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">🗑️ ยกเลิกรายการขาย</button>
        </form>
    @endif
</div>
</div>
@endsection
