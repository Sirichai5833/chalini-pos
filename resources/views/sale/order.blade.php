@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">📋 รายการคำสั่งซื้อทั้งหมด (สำหรับคนขาย)</h2>
        <livewire:orders-list />
    </div>
@endsection

