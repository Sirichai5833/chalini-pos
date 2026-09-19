@extends('layouts.app')

@section('title', 'สต็อกสินค้า')
@section('page-title', 'สต็อกสินค้า')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-gray-50"><tr>
<th class="p-3 text-left">สินค้า</th><th class="p-3 text-right">คงเหลือ</th>
<th class="p-3 text-right">จอง</th><th class="p-3 text-right">พร้อมขาย</th><th class="p-3"></th>
</tr></thead>
<tbody>
@forelse($stocks ?? [] as $stock)
<tr class="border-t">
<td class="p-3">{{ $stock->product->name ?? '-' }}</td>
<td class="p-3 text-right">{{ number_format($stock->quantity_on_hand, 2) }}</td>
<td class="p-3 text-right">{{ number_format($stock->reserved_quantity, 2) }}</td>
<td class="p-3 text-right">{{ number_format($stock->available_quantity, 2) }}</td>
<td class="p-3 text-right"><a href="{{ route('stocks.adjust', $stock) }}" class="text-blue-600">ปรับสต็อก</a></td>
</tr>
@empty
<tr><td colspan="5" class="p-6 text-center">ยังไม่มีข้อมูล</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
