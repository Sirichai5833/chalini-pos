@extends('layouts.app')

@section('title', 'เพิ่มโปรโมชั่น')
@section('page-title', 'เพิ่มโปรโมชั่น')

@section('content')
<form method="POST" action="{{ route('promotions.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
    @csrf
    

    <div>
        <label class="block text-sm mb-1">ชื่อ</label>
        <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm mb-1">สถานะ</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            <option value="active">ใช้งาน</option>
            <option value="inactive">ไม่ใช้งาน</option>
        </select>
    </div>

    <div class="flex gap-2">
        <button class="px-4 py-2 bg-blue-600 text-white rounded">บันทึก</button>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 rounded">ยกเลิก</a>
    </div>
</form>
@endsection
