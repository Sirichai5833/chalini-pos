@extends('layouts.app')

@section('title', 'ใบเสร็จ')
@section('page-title', 'ใบเสร็จ')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 shadow">
<h2 class="text-center font-bold text-xl">ใบเสร็จรับเงิน</h2>
<p class="text-center">{{ config('app.name') }}</p>
<hr class="my-4">
<p>เลขที่บิล: {{ $sale->sale_number ?? '-' }}</p>
<p>วันที่: {{ $sale->created_at ?? '-' }}</p>
<hr class="my-4">
@foreach($sale->items ?? [] as $item)
<div class="flex justify-between"><span>{{ $item->product_name ?? $item->product->name ?? '-' }} x {{ $item->quantity }}</span><span>{{ number_format($item->net_total, 2) }}</span></div>
@endforeach
<hr class="my-4">
<div class="flex justify-between font-bold"><span>รวมสุทธิ</span><span>{{ number_format($sale->grand_total ?? 0, 2) }}</span></div>
</div>
@endsection
