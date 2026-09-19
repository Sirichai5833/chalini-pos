@extends('layouts.app')

@section('title', 'รายละเอียดห้องพัก')
@section('page-title', 'รายละเอียดห้องพัก')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold mb-4">รายละเอียดห้องพัก</h3>
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><dt class="text-gray-500">รหัส</dt><dd>{{ $item->id ?? '-' }}</dd></div>
        <div><dt class="text-gray-500">ชื่อ</dt><dd>{{ $item->name ?? '-' }}</dd></div>
        <div><dt class="text-gray-500">สถานะ</dt><dd>{{ $item->status ?? '-' }}</dd></div>
    </dl>
</div>
@endsection
