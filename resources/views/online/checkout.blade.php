@extends('layouts.online')

@php
    use Illuminate\Support\Str;
@endphp

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

        {{-- หัวข้อ --}}
        <h2 class="mb-4 section-title">
            <i class="bi bi-credit-card-2-front-fill me-2"></i>
            ชำระเงิน
        </h2>


        {{-- แจ้งเตือน --}}
        <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <div>
                🚨 <strong>แจ้งเตือน:</strong>
                เมื่อทำการสั่งซื้อแล้ว
                <u>
                    จะไม่สามารถยกเลิกเองได้
                    หากต้องการยกเลิกกรุณาติดต่อที่เบอร์
                    084-386-0015
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
            <form action="{{ route('online.checkout') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf


                {{-- Product Unit --}}
                <input type="hidden"
                       name="product_unit_id"
                       value="{{ $product_unit_id->product_id ?? '0' }}">



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



                {{-- ยอดรวม --}}
                <h5 class="mb-3">

                    ยอดรวมทั้งหมด:

                    <strong class="text-orange">

                        {{ number_format($total, 2) }}

                    </strong>

                    บาท

                </h5>



                {{-- วิธีชำระเงิน --}}
                <div class="mb-3">

                    <label class="form-label">
                        วิธีการชำระเงิน
                    </label>


                    <select class="form-select"
                            name="payment_method"
                            id="payment_method"
                            required
                            onchange="showQRCode(this.value, {{ $total }})">


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
                <div class="text-center my-4"
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

                        {{ $shopSettings->promptpay ?? env('PROMPTPAY_ID') }}

                    </p>


                    <img id="qr-code"
                         class="qr-code"
                         src=""
                         alt="QR Code">


                </div>



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



                {{-- ยอดรวม --}}
                <input type="hidden"
                       name="total_amount"
                       value="{{ $total }}">



                {{-- Order Token --}}
                <input type="hidden"
                       name="order_token"
                       value="{{ Str::uuid() }}">



                {{-- ปุ่มยืนยัน --}}
                <button type="submit"
                        id="submitBtn"
                        class="btn btn-orange px-4 py-2">

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
                    qrCodeImg.src =
    `/online/generate-qr?amount=${total}`;


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

        document.querySelector('form').addEventListener('submit', function() {


            const btn =
                document.getElementById('submitBtn');


            btn.disabled = true;


            btn.innerHTML =
                '⏳ กำลังดำเนินการ...';


        });

    </script>

@endsection