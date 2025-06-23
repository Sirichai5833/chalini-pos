@extends('layouts.online')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">📦 คำสั่งซื้อของคุณ</h2>

      @livewire('customer-orders')
    </div>
@endsection
