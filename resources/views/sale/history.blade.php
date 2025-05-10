@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2>ประวัติการขาย</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>วันที่</th>
                <th>ผู้ขาย</th>
                <th>จำนวนสินค้า</th>
                <th>ยอดรวม</th>
                <th>รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->staff->name ?? 'N/A' }}</td>
                    <td>{{ $sale->items->sum('quantity') }}</td>
                    <td>{{ number_format($sale->total_price, 2) }} ฿</td>
                    <td>
                        <ul>
                            @foreach($sale->items as $item)
                            <li>{{ optional($item->product)->name }} ({{ optional($item->unit)->unit_name }}) - {{ $item->quantity }} x {{ number_format($item->price, 2) }} ฿</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
