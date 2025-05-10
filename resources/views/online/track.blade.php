@extends('layouts.online')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📦 ติดตามคำสั่งซื้อ</h2>

    <form class="mb-4">
        <label>กรอกรหัสคำสั่งซื้อ</label>
        <input type="text" class="form-control" placeholder="เช่น #ORD1234">
    </form>

    {{-- จำลองสถานะคำสั่งซื้อ --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">คำสั่งซื้อ #ORD1234</h5>
            <p class="mb-1">สถานะ: <span class="badge bg-info">กำลังจัดส่ง</span></p>
            <p>รหัสพัสดุ: TH123456789</p>
            <p>วันที่สั่งซื้อ: 3 พ.ค. 2025</p>
        </div>
    </div>
</div>
@endsection
