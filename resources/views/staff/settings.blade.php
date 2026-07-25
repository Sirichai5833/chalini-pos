@extends('layouts.layout')

@section('content')

<style>
body {
    background-color: #121212;
    color: #e0e0e0;
}
.bg-dark-card {
    background-color: #1e1e1e !important;
    border: 1px solid #333;
}
.text-yellow {
    color: #ffdd00 !important;
}
.btn-yellow {
    background: #ffdd00;
    color: #121212;
    font-weight: bold;
}
.form-control-dark {
    background: #2a2a2a;
    color: #fff;
    border: 1px solid #444;
}
.form-control-dark:focus {
    border-color: #ffdd00;
    box-shadow: 0 0 0 0.2rem rgba(255,221,0,.25);
}
</style>

<div class="container py-4">

    <h2 class="mb-4 text-yellow fw-bold">
        <i class="bi bi-gear-fill me-2"></i> ตั้งค่า
    </h2>

    <div class="card bg-dark-card shadow rounded-4">
        <div class="card-body p-4">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('staff.settings.update') }}">
                @csrf
                @method('PUT')

                {{-- ร้าน --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white">ชื่อร้าน</label>
                        <input type="text" name="shop_name"
                               class="form-control form-control-dark"
                               value="{{ $settings['shop_name'] }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white">เบอร์โทร</label>
                        <input type="text" name="phone"
                               class="form-control form-control-dark"
                               value="{{ $settings['phone'] }}">
                    </div>
                </div>

                {{-- พร้อมเพย์ --}}
                <div class="mb-3">
                    <label class="text-white">พร้อมเพย์</label>
                    <input type="text" name="promptpay"
                           class="form-control form-control-dark"
                           value="{{ $settings['promptpay'] }}">
                </div>
 {{-- เงินทอนเริ่มต้น --}}
<div class="mb-3">
    <label class="text-white">เงินทอนเริ่มต้น (บาท)</label>
    <input type="number"
           name="opening_cash"
           class="form-control form-control-dark"
           min="0"
           step="0.01"
          value="{{ old('opening_cash', $settings['opening_cash']) }}">
    <small class="text-secondary">
        จำนวนเงินทอนเริ่มต้นที่จะใช้เมื่อพนักงานเปิดกะ
    </small>
</div>

                {{-- เวลา --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-white">เวลาเปิดร้าน</label>
                        <input type="time" name="open_time"
                               class="form-control form-control-dark"
                               value="{{ $settings['open_time'] }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="text-white">เวลาปิดร้าน</label>
                        <input type="time" name="close_time"
                               class="form-control form-control-dark"
                               value="{{ $settings['close_time'] }}">
                    </div>
                </div>
               
                <button class="btn btn-yellow px-4">
                    <i class="bi bi-save me-2"></i> บันทึก
                </button>

            </form>

        </div>
    </div>

</div>

@endsection