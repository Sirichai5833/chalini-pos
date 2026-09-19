@extends('layouts.app')

@section('title', 'ประวัติสต็อก')
@section('page-title', 'ประวัติสต็อก')

@section('content')
<div class="bg-white rounded-lg shadow overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-gray-50"><tr><th class="p-3 text-left">วันที่</th><th class="p-3 text-left">สินค้า</th><th class="p-3 text-left">ประเภท</th><th class="p-3 text-right">จำนวน</th><th class="p-3 text-right">คงเหลือ</th></tr></thead>
<tbody>
@forelse($movements ?? [] as $movement)
<tr class="border-t"><td class="p-3">{{ $movement->created_at }}</td><td class="p-3">{{ $movement->product->name ?? '-' }}</td><td class="p-3">{{ $movement->movement_type }}</td><td class="p-3 text-right">{{ $movement->quantity_base }}</td><td class="p-3 text-right">{{ $movement->balance_after }}</td></tr>
@empty<tr><td colspan="5" class="p-6 text-center">ยังไม่มีข้อมูล</td></tr>@endforelse
</tbody>
</table></div>
@endsection
