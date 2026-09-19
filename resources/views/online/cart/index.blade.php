@extends('layouts.app')

@section('title', 'ตะกร้าสินค้า')
@section('page-title', 'ตะกร้าสินค้า')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
@forelse($cartItems ?? [] as $item)
<div class="flex justify-between py-3 border-b">
<div><p class="font-semibold">{{ $item['name'] }}</p><p class="text-sm text-gray-500">จำนวน {{ $item['quantity'] }}</p></div>
<p>{{ number_format($item['total'], 2) }}</p>
</div>
@empty
<p class="text-center text-gray-500">ตะกร้าว่าง</p>
@endforelse
<div class="mt-4 flex justify-between font-bold"><span>รวม</span><span>{{ number_format($cartTotal ?? 0, 2) }}</span></div>
<a href="{{ route('online.checkout.index') }}" class="block text-center mt-4 py-3 bg-green-600 text-white rounded">ไปชำระเงิน</a>
</div>
@endsection
