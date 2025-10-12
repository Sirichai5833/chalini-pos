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

    .qr-code {
        width: 200px;
        height: 200px;
        margin-bottom: 20px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="container py-4">
    <h2 class="mb-4 section-title"><i class="bi bi-credit-card-2-front-fill me-2"></i>ชำระเงิน</h2>
     <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                🚨 <strong>แจ้งเตือน:</strong> เมื่อทำการสั่งซื้อแล้ว <u>จะไม่สามารถยกเลิกเองได้ถ้าต้องการจะยกเลิกกรุณาติดต่อที่เบอร์ 084-386-0015</u>
            </div>
        </div>
    <div class="checkout-card">
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li> {{-- แสดงทุกข้อความผิดพลาด --}}
            @endforeach
        </ul>
    </div>
@endif
        <form action="{{ route('online.online.checkout') }}" method="POST" enctype="multipart/form-data">
            @csrf
           <input type="hidden" name="product_unit_id" value="{{ $product_unit_id->product_id ?? '0' }}">
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
             <h5 class="mb-3">ยอดรวมทั้งหมด: <strong class="text-orange">{{ number_format($total, 2) }}</strong> บาท</h5>
        
            <div class="mb-3">
                <label class="form-label">วิธีการชำระเงิน</label>
               <select class="form-select" name="payment_method" required onchange="showQRCode(this.value, {{ $total }})">
    <option selected disabled>-- เลือกวิธีชำระเงิน --</option>
    <option value="โอนผ่านบัญชีธนาคาร">โอนผ่านบัญชีธนาคาร</option>
    <option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
</select>

            </div>

           <div class="mb-3" id="slip-upload">
    <label class="form-label">อัปโหลดสลิป</label>
    <input type="file" name="slip" class="form-control" accept="image/*" disabled required>
</div>


<div class="text-center my-4" id="qr-container" style="display: none;">
    <p><strong>QR พร้อมเพย์:</strong></p>
    <p><strong>เบอร์</strong>084-386-0015</p>
    <img
     class="qr-code"
        src="https://promptpay.io/{{ env('PROMPTPAY_ID') }}/{{ number_format($total, 2, '.', '') }}"
        alt="QR Code" />
</div>



            <div class="text-end mt-4">
                <button class="btn btn-orange px-4 py-2">
                    <i class="bi bi-check-circle me-1"></i> ยืนยันคำสั่งซื้อ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
 function showQRCode(paymentMethod, total) {
    const qrContainer = document.getElementById('qr-container');
    const slipUpload = document.getElementById('slip-upload');
    const slipInput = slipUpload.querySelector('input[name="slip"]');
    const qrCodeImg = document.getElementById('qr-code');

    if (paymentMethod === 'โอนผ่านบัญชีธนาคาร') {
        qrContainer.style.display = 'block';
        slipUpload.style.display = 'block';
        slipInput.disabled = false;

        // ตั้งค่า src ของ QR Code จาก backend endpoint หรือ URL สร้าง QR
        qrCodeImg.src = `/online/online/generate-qr?amount=${total}`;
    } else {
        qrContainer.style.display = 'none';
        slipUpload.style.display = 'none';
        slipInput.disabled = true;
        qrCodeImg.src = '';
    }
}



</script>
@endsection
