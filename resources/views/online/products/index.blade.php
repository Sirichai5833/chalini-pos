@extends('layouts.app')

@section('title', 'ร้านค้าออนไลน์')
@section('page-title', 'ร้านค้าออนไลน์')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
@forelse($products ?? [] as $product)
<div class="bg-white rounded-lg shadow p-4">
    <div class="aspect-square bg-gray-100 rounded mb-3"></div>
    <h3 class="font-semibold">{{ $product->name }}</h3>
    <p class="text-blue-600 mt-1">{{ number_format($product->baseUnit->selling_price ?? 0, 2) }} บาท</p>
    <form method="POST" action="{{ route('online.cart.store') }}" class="mt-3">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <button class="w-full py-2 bg-blue-600 text-white rounded">เพิ่มลงตะกร้า</button>
    </form>
</div>
@empty
<p class="col-span-full text-center text-gray-500">ยังไม่มีสินค้า</p>
@endforelse
</div>
@endsection
