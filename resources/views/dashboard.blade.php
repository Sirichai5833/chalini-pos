@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach([
        ['label' => 'ยอดขายวันนี้', 'value' => $todaySales ?? 0],
        ['label' => 'ออเดอร์ออนไลน์', 'value' => $onlineOrders ?? 0],
        ['label' => 'สินค้าใกล้หมด', 'value' => $lowStockCount ?? 0],
        ['label' => 'สินค้าใกล้หมดอายุ', 'value' => $expiringCount ?? 0],
    ] as $card)
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold mt-2">{{ number_format($card['value'], 2) }}</p>
        </div>
    @endforeach
</div>
@endsection
