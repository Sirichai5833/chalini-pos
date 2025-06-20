@extends('layouts.layout')

@section('content')
<h2>⏳ สินค้าใกล้หมดอายุ</h2>
<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ชื่อสินค้า</th>
            <th>รหัสล็อต</th>
            <th>จำนวน</th>
            <th>วันหมดอายุ</th>
            <th>เหลืออีก (วัน)</th>
            <th>จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($nearExpiryProducts as $item)
            <tr class="{{ $item->expiry_date->diffInDays(now()) <= 7 ? 'table-danger' : ($item->expiry_date->diffInDays(now()) <= 14 ? 'table-warning' : '') }}">
                <td>{{ $item->name }}</td>
                <td>{{ $item->batch_code ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->expiry_date->format('d/m/Y') }}</td>
               
  @php
    $expiryDate = \Carbon\Carbon::parse($item->expiry_date)->startOfDay(); // ตัดเวลาออกให้เทียบเฉพาะวัน
    $today = now()->startOfDay(); // ตัดเวลาออกด้วย
    $diffDays = $today->diffInDays($expiryDate, false); // วันที่หมดอายุ - วันนี้
@endphp

<td>
    @if ($diffDays < 0)
        <span class="text-danger">หมดอายุแล้ว</span>
    @elseif ($diffDays == 0)
        <span>หมดอายุวันนี้</span>
    @else
        <span>เหลืออีก {{ $diffDays }} วัน</span>
    @endif
</td>
<td>
    <form action="{{ route('notification.expiry.acknowledge', $item->id) }}" method="POST" onsubmit="return confirm('ยืนยันว่าจัดการแล้ว?')">
        @csrf
        <button class="btn btn-sm btn-outline-success">✅ จัดการแล้ว</button>
    </form>
</td>



            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-success">✅ ไม่มีสินค้าใกล้หมดอายุ</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
