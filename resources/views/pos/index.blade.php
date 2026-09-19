@extends('layouts.app')

@section('title', 'POS')
@section('page-title', 'POS')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
<div class="xl:col-span-2 bg-white rounded-lg shadow p-4">
    <div class="flex gap-2 mb-4">
        <input type="text" id="barcode" placeholder="สแกนบาร์โค้ดหรือค้นหาสินค้า" class="flex-1 border rounded px-3 py-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">ค้นหา</button>
    </div>
    <div id="product-results" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
</div>

<div class="bg-white rounded-lg shadow p-4">
    <h3 class="font-semibold mb-4">รายการขาย</h3>
    <div id="cart-items" class="space-y-3"></div>
    <hr class="my-4">
    <div class="space-y-2 text-sm">
        <div class="flex justify-between"><span>ยอดสินค้า</span><span id="subtotal">0.00</span></div>
        <div class="flex justify-between"><span>ส่วนลด</span><span id="discount">0.00</span></div>
        <div class="flex justify-between"><span>VAT</span><span id="tax">0.00</span></div>
        <div class="flex justify-between text-lg font-bold"><span>ยอดสุทธิ</span><span id="grand-total">0.00</span></div>
    </div>
    <button class="w-full mt-4 py-3 bg-green-600 text-white rounded">ชำระเงิน</button>
</div>
</div>
@endsection
