@extends('layouts.app')

@section('title', 'คะแนนสมาชิก')
@section('page-title', 'คะแนนสมาชิก')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="bg-white rounded-lg shadow p-5"><p class="text-gray-500">คะแนนคงเหลือ</p><p class="text-3xl font-bold">{{ number_format($balance ?? 0) }}</p></div>
<div class="md:col-span-2 bg-white rounded-lg shadow p-5"><h3 class="font-semibold mb-4">ประวัติคะแนน</h3>
@forelse($transactions ?? [] as $transaction)
<div class="flex justify-between py-2 border-b"><span>{{ $transaction->description }}</span><span>{{ $transaction->points }}</span></div>
@empty<p class="text-gray-500">ยังไม่มีประวัติ</p>@endforelse
</div></div>
@endsection
