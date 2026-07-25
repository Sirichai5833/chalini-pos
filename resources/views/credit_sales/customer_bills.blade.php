@extends('layouts.layout')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- QR Code Library --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="container py-4" style="font-family: 'Kanit', sans-serif;">

```
{{-- ปุ่มย้อนกลับ --}}
<div class="mb-3">
    <a href="javascript:history.back()"
       class="btn btn-sm btn-light border text-secondary rounded-3">
        <i class="bi bi-arrow-left me-1"></i>
        ย้อนกลับ
    </a>
</div>

{{-- ข้อมูลลูกค้า --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">

    <div class="card-body p-4 d-flex flex-column flex-sm-row
                justify-content-between align-items-sm-center gap-3">

        <div class="d-flex align-items-center gap-3">

            <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3
                        d-flex align-items-center justify-content-center"
                 style="width:60px;height:60px;">

                <i class="bi bi-person-vcard-fill fs-2"></i>

            </div>

            <div>

                <span class="text-muted small fw-bold">
                    ข้อมูลบัญชีลูกหนี้
                </span>

                <h2 class="fw-bolder text-dark mb-0">
                    {{ $customer->name }}
                </h2>

                @if($customer->phone)

                    <span class="text-muted small">
                        <i class="bi bi-telephone me-1"></i>
                        {{ $customer->phone }}
                    </span>

                @endif

            </div>

        </div>

        <div class="bg-danger bg-opacity-10 rounded-3 p-3
                    text-sm-end border border-danger border-opacity-25"
             style="min-width:200px;">

            <span class="text-danger small fw-bold">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                ยอดหนี้คงเหลือรวม
            </span>

            <h3 class="fw-bolder text-danger mb-0 mt-1">

                {{ number_format($customer->current_debt, 2) }}

                <span class="fs-6 fw-normal">
                    บาท
                </span>

            </h3>

        </div>

    </div>

</div>


<div class="row g-4">

    {{-- ===================================================== --}}
    {{-- LEFT : รับชำระเงิน --}}
    {{-- ===================================================== --}}

    <div class="col-12 col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">

            <div class="card-header bg-white py-3 border-bottom">

                <h5 class="mb-0 fw-bold text-dark">

                    <i class="bi bi-cash-coin text-success me-2"></i>

                    ตัดยอดรับชำระเงิน

                </h5>

            </div>


            <div class="card-body p-4">

                <form method="POST"
                      action="{{ route('staff.credit.pay.selected', $customer->id) }}"
                      id="paymentForm">

                    @csrf


                    {{-- เลือกบิล --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold text-secondary">

                            เลือกบิลที่ต้องการชำระ

                        </label>


                        <div class="border rounded-4 p-3"
                             style="max-height:300px;overflow-y:auto;">

                            <div class="form-check mb-3 border-bottom pb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="checkAll"
                                >

                                <label
                                    class="form-check-label fw-bold"
                                    for="checkAll"
                                >

                                    ชำระทั้งหมด

                                </label>

                            </div>


                            @foreach($customer->creditSales->where('remaining_amount','>',0) as $sale)

                            <div class="form-check mb-3">

                                <input
                                    class="form-check-input bill-check"
                                    type="checkbox"
                                    name="sales[]"
                                    value="{{ $sale->id }}"
                                    data-amount="{{ $sale->remaining_amount }}"
                                    id="sale{{ $sale->id }}"
                                >


                                <label
                                    class="form-check-label w-100"
                                    for="sale{{ $sale->id }}"
                                >

                                    <div class="d-flex justify-content-between">

                                        <div>

                                            <div class="fw-bold">

                                                บิล #{{ $sale->id }}

                                            </div>

                                            <small class="text-muted">

                                                {{ $sale->created_at->format('d/m/Y') }}

                                            </small>

                                        </div>


                                        <div class="fw-bold text-danger">

                                            {{ number_format($sale->remaining_amount, 2) }}

                                            บาท

                                        </div>

                                    </div>

                                </label>

                            </div>

                            @endforeach

                        </div>

                    </div>


                    {{-- ยอดรวม --}}
                    <div class="alert alert-light border rounded-3 text-center py-2">

                        ยอดรวมที่ต้องชำระ:

                        <span
                            class="fw-bolder text-success fs-5"
                            id="totalPayAmount"
                        >
                            0.00 บาท
                        </span>

                    </div>


                    {{-- ช่องทางชำระ --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold text-secondary">

                            <i class="bi bi-wallet2 me-1"></i>

                            ช่องทางการชำระเงิน

                        </label>


                        <div class="row g-2">

                            <div class="col-6">

                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="payment_method"
                                    id="paymentCash"
                                    value="cash"
                                    checked
                                >

                                <label
                                    class="btn btn-outline-success w-100 py-3 rounded-3"
                                    for="paymentCash"
                                >

                                    <i class="bi bi-cash-coin fs-4 d-block mb-1"></i>

                                    เงินสด

                                </label>

                            </div>


                            <div class="col-6">

                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="payment_method"
                                    id="paymentQr"
                                    value="qr"
                                >

                                <label
                                    class="btn btn-outline-primary w-100 py-3 rounded-3"
                                    for="paymentQr"
                                >

                                    <i class="bi bi-qr-code fs-4 d-block mb-1"></i>

                                    QR / PromptPay

                                </label>

                            </div>

                        </div>

                    </div>


                    {{-- QR --}}
                    <div
                        id="qrPaymentBox"
                        class="text-center mb-3"
                        style="display:none;"
                    >

                        <div class="alert alert-primary">

                            <i class="bi bi-qr-code fs-3"></i>

                            <div class="fw-bold mt-2">

                                สแกน QR เพื่อชำระเงิน

                            </div>

                        </div>


                        <div
                            id="paymentQrCode"
                            class="d-flex justify-content-center mb-3"
                        >
                        </div>


                        <div
                            class="fw-bold text-primary"
                            id="qrAmountText"
                        >
                            ยอดชำระ 0.00 บาท
                        </div>

                    </div>


                    {{-- ปุ่มยืนยัน --}}
                    <button
                        type="submit"
                        class="btn btn-success btn-lg w-100 rounded-3 fw-bold shadow-sm"
                        id="submitPaymentBtn"
                    >

                        <i class="bi bi-check-circle-fill me-2"></i>

                        ยืนยันรับชำระเงิน

                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- RIGHT : ประวัติ --}}
    {{-- ===================================================== --}}

    <div class="col-12 col-lg-8">

        {{-- ตัวเลือก Section --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">

            <div class="card-body py-3">

                <label class="form-label fw-bold small text-muted">

                    เลือกข้อมูลที่ต้องการดู

                </label>

                <select
                    id="sectionSelector"
                    class="form-select rounded-3"
                >

                    <option value="billSection">

                        📄 ประวัติและสถานะบิลทั้งหมด

                    </option>

                    <option value="paymentSection">

                        💰 ประวัติการชำระเงิน

                    </option>

                </select>

            </div>

        </div>


        {{-- ตารางบิล --}}
        <div
            id="billSection"
            class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white"
        >

            <div class="card-header bg-white border-bottom">

                <div class="d-flex justify-content-between
                            align-items-center gap-3 py-2">

                    <div>

                        <h5 class="mb-1 fw-bold">

                            <i class="bi bi-receipt-cutoff text-secondary me-2"></i>

                            ประวัติและสถานะบิลทั้งหมด

                        </h5>

                        <span class="badge bg-light text-dark border rounded-pill">

                            ทั้งหมด {{ $customer->creditSales->count() }} บิล

                        </span>

                    </div>


                    <div class="d-flex gap-2">

                        <input
                            type="text"
                            id="searchBill"
                            class="form-control rounded-3"
                            placeholder="🔍 ค้นหาเลขบิล"
                        >

                        <select
                            id="statusFilter"
                            class="form-select rounded-3"
                        >

                            <option value="all">

                                ทุกสถานะ

                            </option>

                            <option value="paid">

                                ชำระแล้ว

                            </option>

                            <option value="unpaid">

                                ค้างชำระ

                            </option>

                            <option value="overdue">

                                เกินกำหนด

                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light text-center">

                            <tr>

                                <th>เลขบิล</th>

                                <th>วันที่สร้างบิล</th>

                                <th>วันครบกำหนด</th>

                                <th>ยอดรวม</th>

                                <th>คงเหลือ</th>

                                <th>สถานะ</th>

                                <th>ดู</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($customer->creditSales as $sale)

                            @php

                                $isOverdue =
                                    $sale->status != 'paid'
                                    &&
                                    $sale->due_date
                                    &&
                                    now()->gt($sale->due_date);

                                $statusValue =
                                    $sale->status == 'paid'
                                    ? 'paid'
                                    : ($isOverdue ? 'overdue' : 'unpaid');

                            @endphp


                            <tr
                                class="bill-row"
                                data-id="{{ $sale->id }}"
                                data-status="{{ $statusValue }}"
                            >

                                <td class="text-center fw-bold">

                                    #{{ $sale->id }}

                                </td>


                                <td class="text-center">

                                    {{ $sale->created_at->format('d/m/Y') }}

                                    <small class="d-block text-muted">

                                        {{ $sale->created_at->format('H:i') }}

                                    </small>

                                </td>


                                <td class="text-center">

                                    @if($sale->due_date)

                                        {{ \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                <td class="text-end">

                                    {{ number_format($sale->total_amount, 2) }}

                                </td>


                                <td class="text-end fw-bold text-danger">

                                    {{ number_format($sale->remaining_amount, 2) }}

                                </td>


                                <td class="text-center">

                                    @if($sale->status == 'paid')

                                        <span class="badge bg-success">

                                            ชำระแล้ว

                                        </span>

                                    @elseif($isOverdue)

                                        <span class="badge bg-danger">

                                            เลยกำหนด

                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">

                                            ค้างชำระ

                                        </span>

                                    @endif

                                </td>


                                <td class="text-center">

                                    <a
                                        href="{{ route('staff.credit.sales.show', $sale->id) }}"
                                        class="btn btn-sm btn-light border rounded-3"
                                    >

                                        <i class="bi bi-eye-fill text-primary"></i>

                                    </a>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- ประวัติชำระเงิน --}}
        <div
            id="paymentSection"
            class="card border-0 shadow-sm rounded-4"
            style="display:none;"
        >

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-clock-history me-2 text-primary"></i>

                    ประวัติการชำระเงิน

                </h5>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light text-center">

                            <tr>

                                <th>วันที่</th>

                                <th>เลขบิล</th>

                                <th>จำนวนเงิน</th>

                                <th>ช่องทาง</th>

                                <th>หมายเหตุ</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($customer->paymentHistories as $history)

                            <tr>

                                <td class="text-center">

                                    {{ $history->created_at->format('d/m/Y H:i') }}

                                </td>


                                <td class="text-center fw-bold">

                                    #{{ $history->credit_sale_id }}

                                </td>


                                <td class="text-end fw-bold text-success">

                                    {{ number_format($history->amount, 2) }}

                                </td>


                                <td class="text-center">

                                    @if($history->payment_method == 'cash')

                                        <span class="badge bg-success">

                                            <i class="bi bi-cash-coin"></i>

                                            เงินสด

                                        </span>

                                    @else

                                        <span class="badge bg-primary">

                                            <i class="bi bi-qr-code"></i>

                                            QR / PromptPay

                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $history->note ?? '-' }}

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >

                                    ยังไม่มีประวัติการชำระเงิน

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

<style>

.shadow-xs {

    box-shadow: 0 1px 2px rgba(0,0,0,.05);

}

</style>

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ตัวแปรหลัก
    |--------------------------------------------------------------------------
    */

    const checkAll =
        document.getElementById('checkAll');

    const billChecks =
        document.querySelectorAll('.bill-check');

    const totalText =
        document.getElementById('totalPayAmount');

    const paymentCash =
        document.getElementById('paymentCash');

    const paymentQr =
        document.getElementById('paymentQr');

    const qrPaymentBox =
        document.getElementById('qrPaymentBox');

    const paymentQrCode =
        document.getElementById('paymentQrCode');

    const qrAmountText =
        document.getElementById('qrAmountText');


    /*
    |--------------------------------------------------------------------------
    | คำนวณยอดรวม
    |--------------------------------------------------------------------------
    */

    function calculateTotal() {

        let total = 0;

        billChecks.forEach(function (item) {

            if (item.checked) {

                total += parseFloat(item.dataset.amount);

            }

        });


        totalText.innerText =
            total.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' บาท';


        if (paymentQr.checked) {

            generatePaymentQr(total);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | เลือกทั้งหมด
    |--------------------------------------------------------------------------
    */

    checkAll.addEventListener('change', function () {

        billChecks.forEach(function (item) {

            item.checked =
                checkAll.checked;

        });

        calculateTotal();

    });


    /*
    |--------------------------------------------------------------------------
    | เลือกบิลแต่ละใบ
    |--------------------------------------------------------------------------
    */

    billChecks.forEach(function (item) {

        item.addEventListener('change', function () {

            const allChecked =
                Array.from(billChecks)
                .every(item => item.checked);

            const noneChecked =
                Array.from(billChecks)
                .every(item => !item.checked);


            checkAll.checked =
                allChecked;

            checkAll.indeterminate =
                !allChecked && !noneChecked;


            calculateTotal();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | เงินสด
    |--------------------------------------------------------------------------
    */

    paymentCash.addEventListener('change', function () {

        if (this.checked) {

            qrPaymentBox.style.display = 'none';

        }

    });


    /*
    |--------------------------------------------------------------------------
    | QR PromptPay
    |--------------------------------------------------------------------------
    */

    paymentQr.addEventListener('change', function () {

        if (this.checked) {

            qrPaymentBox.style.display = 'block';

            calculateTotal();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | สร้าง QR PromptPay
    |--------------------------------------------------------------------------
    */

    function generatePaymentQr(amount) {

        paymentQrCode.innerHTML = '';

        if (!amount || amount <= 0) {

            paymentQrCode.innerHTML = `

                <div class="text-danger">

                    กรุณาเลือกบิลที่ต้องการชำระก่อน

                </div>

            `;

            qrAmountText.innerText =
                'ยอดชำระ 0.00 บาท';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | เปลี่ยนเป็น PromptPay ID ของร้าน
        |--------------------------------------------------------------------------
        */

        const promptPayId =
            'YOUR_PROMPTPAY_ID';


        /*
        |--------------------------------------------------------------------------
        | ตัวอย่าง Payload
        |--------------------------------------------------------------------------
        |
        | สำหรับระบบจริงควรใช้ PromptPay QR Generator
        |
        */

        const qrText =
            `PromptPay: ${promptPayId} Amount: ${amount.toFixed(2)}`;


        new QRCode(paymentQrCode, {

            text: qrText,

            width: 220,

            height: 220

        });


        qrAmountText.innerText =

            'ยอดชำระ ' +

            amount.toLocaleString('th-TH', {

                minimumFractionDigits: 2,

                maximumFractionDigits: 2

            })

            +

            ' บาท';

    }


    /*
    |--------------------------------------------------------------------------
    | ตรวจสอบก่อน Submit
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('paymentForm')
        .addEventListener('submit', function (e) {


            const selectedBills =
                document.querySelectorAll(
                    '.bill-check:checked'
                );


            if (selectedBills.length === 0) {

                e.preventDefault();

                alert('กรุณาเลือกบิลที่ต้องการชำระ');

                return;

            }


            const paymentMethod =
                document.querySelector(
                    'input[name="payment_method"]:checked'
                ).value;


            if (paymentMethod === 'qr') {

                const confirmPayment =
                    confirm(
                        'ตรวจสอบว่าได้รับเงินจาก QR แล้วหรือยัง?'
                    );


                if (!confirmPayment) {

                    e.preventDefault();

                }

            }

        });


    /*
    |--------------------------------------------------------------------------
    | ค้นหาบิล
    |--------------------------------------------------------------------------
    */

    const searchBill =
        document.getElementById('searchBill');

    const statusFilter =
        document.getElementById('statusFilter');

    const billRows =
        document.querySelectorAll('.bill-row');


    function filterBills() {

        const keyword =
            searchBill.value
            .toLowerCase()
            .trim();

        const status =
            statusFilter.value;


        billRows.forEach(function (row) {

            const id =
                row.dataset.id
                .toLowerCase();

            const rowStatus =
                row.dataset.status;


            let show = true;


            if (
                keyword
                &&
                !id.includes(keyword)
            ) {

                show = false;

            }


            if (
                status !== 'all'
                &&
                rowStatus !== status
            ) {

                show = false;

            }


            row.style.display =
                show ? '' : 'none';

        });

    }


    searchBill.addEventListener(
        'keyup',
        filterBills
    );


    statusFilter.addEventListener(
        'change',
        filterBills
    );


    /*
    |--------------------------------------------------------------------------
    | สลับ Section
    |--------------------------------------------------------------------------
    */

    const sectionSelector =
        document.getElementById(
            'sectionSelector'
        );

    const billSection =
        document.getElementById(
            'billSection'
        );

    const paymentSection =
        document.getElementById(
            'paymentSection'
        );


    sectionSelector.addEventListener(
        'change',
        function () {

            if (
                this.value === 'billSection'
            ) {

                billSection.style.display =
                    '';

                paymentSection.style.display =
                    'none';

            } else {

                billSection.style.display =
                    'none';

                paymentSection.style.display =
                    '';

            }

        }

    );

});

</script>

@endsection
