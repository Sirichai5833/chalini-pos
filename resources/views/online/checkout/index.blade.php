@extends('layouts.app')

@section('title', 'ชำระเงิน')
@section('page-title', 'ชำระเงิน')

@section('content')
<form method="POST" action="{{ route('online.orders.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
@csrf
<div><label class="block mb-1">รูปแบบรับสินค้า</label><select name="fulfillment_type" class="w-full border rounded px-3 py-2"><option value="pickup">รับเอง</option><option value="room_delivery">ส่งถึงห้อง</option></select></div>
<div><label class="block mb-1">เบอร์โทร</label><input type="text" name="recipient_phone" class="w-full border rounded px-3 py-2"></div>
<div><label class="block mb-1">หมายเหตุ</label><textarea name="delivery_note" class="w-full border rounded px-3 py-2"></textarea></div>
<div class="flex justify-between"><span>ค่าส่ง</span><span>{{ number_format($deliveryFee ?? 0, 2) }}</span></div>
<div class="flex justify-between font-bold"><span>ยอดสุทธิ</span><span>{{ number_format($grandTotal ?? 0, 2) }}</span></div>
<button class="w-full py-3 bg-green-600 text-white rounded">ยืนยันคำสั่งซื้อ</button>
</form>
@endsection
