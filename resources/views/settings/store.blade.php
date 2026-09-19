@extends('layouts.app')

@section('title', 'ตั้งค่าร้าน')
@section('page-title', 'ตั้งค่าร้าน')

@section('content')
<form method="POST" action="{{ route('settings.store.update') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
@csrf
@method('PUT')
<div class="text-gray-500 mb-4">ฟอร์มตั้งค่าร้าน</div>
<button class="px-4 py-2 bg-blue-600 text-white rounded">บันทึกการตั้งค่า</button>
</form>
@endsection
