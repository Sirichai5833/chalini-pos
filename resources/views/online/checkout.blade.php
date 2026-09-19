@extends('layouts.online')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

@php
    $shippingFee = max(0, (float) ($shippingFee ?? 0));

    $grandTotal = round(
        (float) ($grandTotal ?? ((float) $total + $shippingFee)),
        2
    );

    $promptpayNumber = $shopSettings->promptpay
        ?? \App\Models\Setting::get(
            'promptpay',
            env('PROMPTPAY_ID')
        );

    $shopPhone = $shopSettings->phone
        ?? \App\Models\Setting::get('phone', '-');
@endphp

    <style>
    :root {
        --checkout-yellow: #ffd43b;
        --checkout-yellow-dark: #c99f00;
        --checkout-black: #090909;
        --checkout-surface: #121212;
        --checkout-card: #181818;
        --checkout-soft: #222222;
        --checkout-border: rgba(255, 212, 59, .22);
        --checkout-text: #f7f7f7;
        --checkout-muted: #a8a8a8;
        --checkout-danger: #ef4444;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--checkout-black);
        color: var(--checkout-text);
    }

    .checkout-page {
        max-width: 980px;
    }

    .checkout-hero {
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid var(--checkout-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
            linear-gradient(135deg, #080808 0%, #171717 58%, #2d2500 100%);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .34);
    }

    .checkout-hero::after {
        content: "";
        position: absolute;
        right: -70px;
        bottom: -115px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .10);
        pointer-events: none;
    }

    .checkout-hero-content {
        position: relative;
        z-index: 1;
    }

    .checkout-title {
        margin: 0;
        color: var(--checkout-yellow);
        font-weight: 900;
        letter-spacing: .2px;
    }

    .checkout-subtitle {
        margin: .35rem 0 0;
        color: #d2d2d2;
        font-size: .9rem;
    }

    .checkout-alert {
        border: 1px solid rgba(255, 212, 59, .30);
        border-radius: 18px;
        background: #211c07;
        color: #ffe992;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
    }

    .checkout-card {
        padding: 1.4rem;
        border: 1px solid var(--checkout-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at right top, rgba(255, 212, 59, .07), transparent 26%),
            linear-gradient(180deg, #191919, #111111);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .34);
    }

    .checkout-section {
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #2d2d2d;
        border-radius: 18px;
        background: rgba(12, 12, 12, .72);
    }

    .checkout-section-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: 1rem;
        color: var(--checkout-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .form-label {
        margin-bottom: .45rem;
        color: #dedede;
        font-weight: 750;
    }

    .form-control,
    .form-select {
        min-height: 48px;
        border: 1px solid #383838;
        border-radius: 14px;
        background: #141414;
        color: #fff;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--checkout-yellow);
        background: #171717;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .form-control[readonly] {
        background: #101010;
        color: #cfcfcf;
    }

    .form-select option {
        background: #171717;
        color: #fff;
    }

    .total-panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
        border: 1px solid var(--checkout-border);
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(255, 212, 59, .10), rgba(255, 212, 59, .03));
    }

    .total-label {
        margin: 0;
        color: var(--checkout-muted);
        font-size: .9rem;
    }

    .total-amount {
        margin: 0;
        color: var(--checkout-yellow);
        font-size: 1.8rem;
        font-weight: 950;
        text-shadow: 0 4px 16px rgba(255, 212, 59, .12);
    }

    .checkout-total-breakdown {
        width: 100%;
    }

    .checkout-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .55rem;
        color: #d7d7d7;
    }

    .checkout-total-row:last-child {
        margin-bottom: 0;
    }

    .checkout-total-divider {
        margin: .8rem 0;
        border-color: rgba(255, 255, 255, .16);
    }

    .checkout-total-row.grand-total {
        align-items: flex-end;
        color: #fff;
        font-weight: 850;
    }

    .payment-box {
        padding: 1rem;
        border: 1px dashed rgba(255, 212, 59, .34);
        border-radius: 18px;
        background: rgba(255, 212, 59, .05);
    }

    .qr-panel {
        padding: 1.25rem;
        border: 1px solid var(--checkout-border);
        border-radius: 20px;
        background: #0f0f0f;
    }

    .qr-code {
        width: min(220px, 100%);
        height: auto;
        aspect-ratio: 1 / 1;
        display: block;
        margin: 1rem auto 0;
        padding: .65rem;
        border-radius: 18px;
        background: #fff;
        object-fit: contain;
    }

    .btn-orange {
        min-height: 50px;
        border: 1px solid var(--checkout-yellow);
        border-radius: 14px;
        background: linear-gradient(135deg, #ffe56b, var(--checkout-yellow));
        color: #111;
        font-weight: 900;
        box-shadow: 0 9px 22px rgba(255, 212, 59, .17);
        transition: .2s ease;
    }

    .btn-orange:hover:not(:disabled) {
        border-color: #fff08f;
        background: linear-gradient(135deg, #fff08f, #ffc800);
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 13px 26px rgba(255, 212, 59, .23);
    }

    .btn-orange:disabled {
        opacity: .65;
        cursor: wait;
    }

    .alert-danger {
        border: 1px solid rgba(239, 68, 68, .30);
        border-radius: 16px;
        background: rgba(239, 68, 68, .10);
        color: #ffb8b8;
    }

    @media (max-width: 576px) {
        .checkout-page {
            padding-top: 1rem !important;
        }

        .checkout-hero,
        .checkout-card {
            border-radius: 19px;
        }

        .checkout-hero {
            padding: 1.2rem;
        }

        .checkout-card {
            padding: 1rem;
        }

        .total-panel {
            align-items: flex-start;
            flex-direction: column;
        }

        .total-amount {
            font-size: 1.55rem;
        }

        .btn-orange {
            width: 100%;
        }
    }
</style>


    <div class="container py-4 checkout-page">

        {{-- หัวข้อ --}}
        <section class="checkout-hero">
            <div class="checkout-hero-content">
                <h2 class="checkout-title">
                    <i class="bi bi-credit-card-2-front-fill me-2"></i>
                    ชำระเงิน
                </h2>
                <p class="checkout-subtitle">
                    ตรวจสอบข้อมูล เลือกวิธีชำระเงิน และยืนยันคำสั่งซื้อ
                </p>
            </div>
        </section>


        {{-- แจ้งเตือน --}}
        <div class="alert checkout-alert d-flex align-items-start shadow-sm" role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <div>
                🚨 <strong>แจ้งเตือน:</strong>
                เมื่อทำการสั่งซื้อแล้ว
                <u>
                    จะไม่สามารถยกเลิกเองได้
                    หากต้องการยกเลิกกรุณาติดต่อที่เบอร์
                    {{ $shopPhone }}
                </u>
            </div>

        </div>


        <div class="checkout-card">


            {{-- แสดง Error --}}
            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- FORM --}}
            <form
                  id="checkoutForm"
                  action="{{ route('online.checkout.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
            >

                @csrf


                <section class="checkout-section">
                    <div class="checkout-section-title">
                        <i class="bi bi-person-badge-fill"></i>
                        ข้อมูลผู้สั่งซื้อ
                    </div>

                {{-- ชื่อ และ เบอร์โทร --}}
                <div class="row mb-3">


                    {{-- ชื่อ --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            ชื่อ-นามสกุล
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $member->name) }}"
                               readonly>

                    </div>


                    {{-- เบอร์โทรจาก Database --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            เบอร์โทร
                        </label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone', $member->phone) }}"
                               readonly>

                    </div>

                </div>



                {{-- ห้อง --}}
                <div class="mb-3">

                    <label class="form-label">
                        ห้อง
                    </label>

                    <input type="text"
                           name="room"
                           class="form-control"
                           value="{{ old('room', $member->room_number) }}"
                           readonly>

                </div>
                </section>
                {{-- สรุปยอดชำระ --}}
                <div class="total-panel">
                    <div class="checkout-total-breakdown">
                        <div class="checkout-total-row">
                            <span class="total-label">ยอดรวมสินค้า</span>
                            <strong>
                                ฿{{ number_format($total, 2) }}
                            </strong>
                        </div>

                        <div class="checkout-total-row">
                            <span class="total-label">
                                <i class="bi bi-truck me-1"></i>
                                ค่าจัดส่ง
                            </span>

                            <strong>
                                @if ($shippingFee > 0)
                                    ฿{{ number_format($shippingFee, 2) }}
                                @else
                                    ฟรี
                                @endif
                            </strong>
                        </div>

                        <hr class="checkout-total-divider">

                        <div class="checkout-total-row grand-total">
                            <span>ยอดชำระทั้งหมด</span>

                            <p class="total-amount">
                                ฿{{ number_format($grandTotal, 2) }}
                            </p>
                        </div>
                    </div>
                </div>



                <section class="checkout-section">
                    <div class="checkout-section-title">
                        <i class="bi bi-wallet2"></i>
                        วิธีการชำระเงิน
                    </div>

                {{-- วิธีชำระเงิน --}}
                <div class="mb-3 payment-box">

                    <label class="form-label">
                        วิธีการชำระเงิน
                    </label>


                    <select class="form-select"
                            name="payment_method"
                            id="payment_method"
                            required
                            onchange="showQRCode(this.value, {{ $grandTotal }})">


                        <option value=""
                                selected
                                disabled>

                            -- เลือกวิธีชำระเงิน --

                        </option>


                        <option value="โอนผ่านบัญชีธนาคาร">

                            โอนผ่านบัญชีธนาคาร

                        </option>


                        <option value="เก็บเงินปลายทาง">

                            เก็บเงินปลายทาง

                        </option>


                    </select>

                </div>



                {{-- อัปโหลดสลิป --}}
                <div class="mb-3"
                     id="slip-upload"
                     style="display: none;">


                    <label class="form-label">

                        อัปโหลดสลิป

                    </label>


                    <input type="file"
                           name="slip"
                           id="slip"
                           class="form-control"
                           accept="image/*"
                           disabled>


                </div>



                {{-- QR CODE --}}
                <div class="text-center my-4 qr-panel"
                     id="qr-container"
                     style="display: none;">


                    <p>

                        <strong>
                            QR พร้อมเพย์
                        </strong>

                    </p>


                    <p>

                        <strong>
                            เบอร์
                        </strong>

                        {{ $promptpayNumber }}

                    </p>


                    <img id="qr-code"
                         class="qr-code"
                         src=""
                         alt="QR Code">


                </div>



                </section>

                {{-- รายการสินค้า --}}
                @foreach ($cart as $id => $item)

                    <input type="hidden"
                           name="items[{{ $id }}][product_unit_id]"
                           value="{{ $item['product_unit_id'] }}">


                    <input type="hidden"
                           name="items[{{ $id }}][quantity]"
                           value="{{ $item['quantity'] }}">


                    <input type="hidden"
                           name="items[{{ $id }}][price]"
                           value="{{ $item['price'] }}">

                @endforeach



                {{-- ยอดรวมสำหรับอ้างอิงหน้าเว็บเท่านั้น
                     Backend จะคำนวณใหม่จากฐานข้อมูล --}}
                <input type="hidden"
                       name="subtotal"
                       value="{{ number_format($total, 2, '.', '') }}">

                <input type="hidden"
                       name="shipping_fee"
                       value="{{ number_format($shippingFee, 2, '.', '') }}">

                <input type="hidden"
                       name="total_amount"
                       value="{{ number_format($grandTotal, 2, '.', '') }}">



                {{-- Order Token --}}
                <input type="hidden"
                       name="order_token"
                       value="{{ Str::uuid() }}">



                {{-- ปุ่มยืนยัน --}}
                <button type="submit"
                        id="submitBtn"
                        class="btn btn-orange px-4 py-2 w-100">

                    <i class="bi bi-check-circle me-1"></i>

                    ยืนยันคำสั่งซื้อ

                </button>


            </form>

        </div>

    </div>



    <script>

        function showQRCode(paymentMethod, total) {


            const qrContainer =
                document.getElementById('qr-container');


            const slipUpload =
                document.getElementById('slip-upload');


            const slipInput =
                document.getElementById('slip');


            const qrCodeImg =
                document.getElementById('qr-code');



            /*
            |--------------------------------------------------------------------------
            | โอนผ่านบัญชีธนาคาร
            |--------------------------------------------------------------------------
            */

            if (paymentMethod === 'โอนผ่านบัญชีธนาคาร') {


                // แสดง QR
                qrContainer.style.display = 'block';


                // แสดงอัปโหลดสลิป
                slipUpload.style.display = 'block';


                // เปิดใช้งาน Input
                slipInput.disabled = false;


                // บังคับให้อัปโหลดสลิป
                slipInput.required = true;


                // สร้าง QR Code
                qrCodeImg.src =
                    `/online/generate-qr?amount=${encodeURIComponent(total)}`;


            }



            /*
            |--------------------------------------------------------------------------
            | เก็บเงินปลายทาง
            |--------------------------------------------------------------------------
            */

            else if (paymentMethod === 'เก็บเงินปลายทาง') {


                // ซ่อน QR
                qrContainer.style.display = 'none';


                // ซ่อนอัปโหลดสลิป
                slipUpload.style.display = 'none';


                // ปิด Input
                slipInput.disabled = true;


                // ไม่บังคับอัปโหลด
                slipInput.required = false;


                // ล้างไฟล์ที่เลือก
                slipInput.value = '';


                // ล้าง QR
                qrCodeImg.src = '';

            }

        }



        /*
        |--------------------------------------------------------------------------
        | ป้องกันกดปุ่มซ้ำ
        |--------------------------------------------------------------------------
        */

        const checkoutForm = document.getElementById('checkoutForm');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function () {
                const btn = document.getElementById('submitBtn');

                if (!btn || btn.disabled) {
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    กำลังดำเนินการ...
                `;
            });
        }

    </script>

@endsection