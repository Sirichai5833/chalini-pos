@extends('layouts.online')

@section('content')
<style>
    .btn-orange {
        background-color: #ff5722;
        color: white;
    }

    .btn-orange:hover {
        background-color: #e64a19;
        color: white;
    }

    .form-label {
        font-weight: 500;
        color: #444;
    }

    .section-title {
        color: #ff5722;
    }

    .checkout-card {
        background-color: #fff9f5;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
    }

    select.form-select,
    input.form-control {
        border-radius: 8px;
    }
</style>

<div class="container py-4">
    <h2 class="mb-4 section-title"><i class="bi bi-credit-card-2-front-fill me-2"></i>ชำระเงิน</h2>

    <div class="checkout-card">
        <form action="{{ route('online.checkout') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="name" class="form-control" 
                           value="{{ old('name', $member->name) }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">เบอร์โทร</label>
                    <input type="text" name="phone" class="form-control" 
                           value="{{ old('phone') }}" placeholder="08XXXXXXXX" required>
                </div>
            </div>
        
            <div class="mb-3">
                <label class="form-label">ห้อง</label>
                <input type="text" name="room" class="form-control" 
                       value="{{ old('room', $member->room_number) }}" readonly>
            </div>
        
            <div class="mb-3">
                <label class="form-label">วิธีการชำระเงิน</label>
                <select class="form-select" name="payment_method" required>
                    <option selected disabled>-- เลือกวิธีชำระเงิน --</option>
                    <option value="โอนผ่านบัญชีธนาคาร">โอนผ่านบัญชีธนาคาร</option>
                    <option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
                </select>
            </div>
        
            <div class="text-end mt-4">
                <button class="btn btn-orange px-4 py-2">
                    <i class="bi bi-check-circle me-1"></i> ยืนยันคำสั่งซื้อ
                </button>
            </div>
        </form>
        
        
    </div>
</div>
@endsection
