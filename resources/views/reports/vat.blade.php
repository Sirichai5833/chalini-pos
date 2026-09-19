@extends('layouts.app')

@section('title', 'รายงาน VAT')
@section('page-title', 'รายงาน VAT')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
<form method="GET" class="flex flex-wrap gap-3 mb-6">
<input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-3 py-2">
<input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-3 py-2">
<button class="px-4 py-2 bg-blue-600 text-white rounded">ค้นหา</button>
</form>
<div class="text-gray-500">พื้นที่แสดงรายงาน VAT</div>
</div>
@endsection
