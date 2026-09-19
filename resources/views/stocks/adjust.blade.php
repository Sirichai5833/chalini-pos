@extends('layouts.app')

@section('title', 'ปรับสต็อก')
@section('page-title', 'ปรับสต็อก')

@section('content')
<form method="POST" action="{{ route('stocks.adjust.store', $stock) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
@csrf
<div><label class="block mb-1">ประเภท</label><select name="type" class="w-full border rounded px-3 py-2">
<option value="increase">เพิ่ม</option><option value="decrease">ลด</option>
</select></div>
<div><label class="block mb-1">จำนวนหน่วยพื้นฐาน</label><input type="number" step="0.0001" name="quantity" class="w-full border rounded px-3 py-2"></div>
<div><label class="block mb-1">หมายเหตุ</label><textarea name="note" class="w-full border rounded px-3 py-2"></textarea></div>
<button class="px-4 py-2 bg-blue-600 text-white rounded">บันทึก</button>
</form>
@endsection
