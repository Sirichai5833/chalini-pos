@extends('layouts.layout')

@section('validation-errors')
    @include('layouts.partials.validation-errors')
@endsection

@section('content')

@php
    /*
     * ยอดสุทธิสำหรับปิดกะ ต้องไม่รวมรายการที่ถูกยกเลิก
     * Controller ควรคำนวณตัวแปร *Net มาให้โดยตรง
     * ไม่ fallback ไปใช้ยอดเดิม เพราะยอดเดิมอาจรวมรายการยกเลิก
     */
    $cashSalesNet = (float) ($cashSalesNet ?? 0);
    $qrSalesNet = (float) ($qrSalesNet ?? 0);
    $cardSalesNet = (float) ($cardSalesNet ?? 0);
    $creditSalesNet = (float) ($creditSalesNet ?? 0);

    $offlineSalesNet = (float) (
        $offlineSalesNet
        ?? (
            $cashSalesNet
            + $qrSalesNet
            + $cardSalesNet
            + $creditSalesNet
        )
    );

    $onlineQrSalesNet = (float) ($onlineQrSalesNet ?? 0);
    $onlineCodSalesNet = (float) ($onlineCodSalesNet ?? 0);

    $onlineSalesNet = (float) (
        $onlineSalesNet
        ?? ($onlineQrSalesNet + $onlineCodSalesNet)
    );

    /*
     * รวมยอดตามช่องทางรับเงินจริง ไม่แยกหน้าร้าน/ออนไลน์
     * COD จะนับเป็นเงินสดเมื่อมีการรับเงินจริงแล้ว
     */
    $combinedCashSalesNet = (float) (
        $combinedCashSalesNet
        ?? ($cashSalesNet + $onlineCodSalesNet)
    );

    $combinedQrSalesNet = (float) (
        $combinedQrSalesNet
        ?? $combinedQrSalesNet
    );

    $combinedOtherSalesNet = (float) (
        $combinedOtherSalesNet
        ?? ($cardSalesNet + $creditSalesNet)
    );

    $totalSalesNet = (float) (
        $totalSalesNet
        ?? (
            $combinedCashSalesNet
            + $combinedQrSalesNet
            + $combinedOtherSalesNet
        )
    );

    $cancelledCashSales = (float) ($cancelledCashSales ?? 0);
    $cancelledQrSales = (float) ($cancelledQrSales ?? 0);
    $cancelledOnlineQrSales = (float) ($cancelledOnlineQrSales ?? 0);
    $cancelledOnlineCodSales = (float) ($cancelledOnlineCodSales ?? 0);

    $cancelledSalesTotal =
        $cancelledCashSales
        + $cancelledQrSales
        + $cancelledOnlineQrSales
        + $cancelledOnlineCodSales;

    $expectedCashNet = (float) (
        $expectedCashNet
        ?? (
            (float) ($shift->opening_cash ?? 0)
            + $combinedCashSalesNet
        )
    );

    /*
     * ยอด QR/เงินโอนที่ระบบคาดหวังสำหรับการกระทบยอดตอนปิดกะ
     * รวมทั้งหน้าร้านและออนไลน์ที่ชำระแล้ว
     */
    $expectedQrNet = (float) (
        $expectedQrNet
        ?? $combinedQrSalesNet
    );
@endphp
<style>
    .shift-close-page {
        --sc-yellow: #ffc107;
        --sc-yellow-soft: #ffe27a;
        --sc-panel: #171717;
        --sc-panel-soft: #202020;
        --sc-border: #3a3a3a;
        --sc-text: #f7f7f7;
        --sc-muted: #a8a8a8;
        color: var(--sc-text);
    }

    .shift-close-page .page-shell {
        max-width: 1180px;
        margin: 0 auto;
    }

    .shift-close-page .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.2rem;
        padding: 1.3rem;
        border: 1px solid rgba(255, 193, 7, .24);
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, .17),
                transparent 26%
            ),
            linear-gradient(135deg, #202020, #0c0c0c);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .3);
    }

    .shift-close-page .hero-icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 18px;
        background: linear-gradient(
            135deg,
            var(--sc-yellow),
            var(--sc-yellow-soft)
        );
        color: #111;
        font-size: 1.45rem;
    }

    .shift-close-page .hero h2 {
        margin: 0;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 900;
    }

    .shift-close-page .hero p {
        margin: .35rem 0 0;
        color: var(--sc-muted);
        font-size: .84rem;
    }

    .shift-close-page .status-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .75rem;
        border: 1px solid rgba(255, 193, 7, .25);
        border-radius: 999px;
        background: rgba(255, 193, 7, .08);
        color: var(--sc-yellow);
        font-size: .75rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .shift-close-page .panel {
        overflow: hidden;
        border: 1px solid var(--sc-border);
        border-radius: 20px;
        background: var(--sc-panel);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .28);
    }

    .shift-close-page .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--sc-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .shift-close-page .panel-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #fff;
        font-size: .95rem;
        font-weight: 850;
    }

    .shift-close-page .panel-title i {
        color: var(--sc-yellow);
    }

    .shift-close-page .panel-body {
        padding: 1.15rem;
    }

    .shift-close-page .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .8rem;
    }

    .shift-close-page .summary-item {
        padding: .9rem;
        border: 1px solid var(--sc-border);
        border-radius: 14px;
        background: var(--sc-panel-soft);
    }

    .shift-close-page .summary-label {
        display: block;
        margin-bottom: .3rem;
        color: var(--sc-muted);
        font-size: .74rem;
    }

    .shift-close-page .summary-value {
        color: #fff;
        font-size: .95rem;
        font-weight: 850;
    }

    .shift-close-page .amount-card {
        padding: 1rem;
        border: 1px solid var(--sc-border);
        border-radius: 15px;
        background: var(--sc-panel-soft);
    }

    .shift-close-page .amount-card + .amount-card {
        margin-top: .75rem;
    }

    .shift-close-page .amount-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .shift-close-page .amount-label {
        display: flex;
        align-items: center;
        gap: .55rem;
        color: #ddd;
        font-size: .82rem;
    }

    .shift-close-page .amount-label i {
        color: var(--sc-yellow);
    }

    .shift-close-page .amount-value {
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .shift-close-page .subtext {
        margin-top: .35rem;
        color: var(--sc-muted);
        font-size: .7rem;
        line-height: 1.5;
    }

    .shift-close-page .cash-highlight {
        border-color: rgba(255, 193, 7, .28);
        background: rgba(255, 193, 7, .06);
    }

    .shift-close-page .form-label {
        margin-bottom: .5rem;
        color: #e8e8e8;
        font-size: .82rem;
        font-weight: 800;
    }

    .shift-close-page .form-control {
        min-height: 50px;
        border: 1px solid #454545;
        border-radius: 13px;
        background: #0e0e0e;
        color: #fff;
        box-shadow: none;
    }

    .shift-close-page textarea.form-control {
        min-height: 105px;
        resize: vertical;
    }

    .shift-close-page .form-control:focus {
        border-color: var(--sc-yellow);
        background: #0a0a0a;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .12);
    }

    .shift-close-page .difference-box {
        margin-top: .8rem;
        padding: .9rem;
        border: 1px solid var(--sc-border);
        border-radius: 13px;
        background: #121212;
    }

    .shift-close-page .difference-box.is-positive {
        border-color: rgba(25, 135, 84, .35);
        background: rgba(25, 135, 84, .08);
    }

    .shift-close-page .difference-box.is-negative {
        border-color: rgba(220, 53, 69, .35);
        background: rgba(220, 53, 69, .08);
    }

    .shift-close-page .difference-label {
        color: var(--sc-muted);
        font-size: .74rem;
    }

    .shift-close-page .difference-value {
        margin-top: .25rem;
        color: #fff;
        font-size: 1.15rem;
        font-weight: 900;
    }

    .shift-close-page .notice {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-top: .85rem;
        padding: .8rem .9rem;
        border: 1px solid rgba(255, 193, 7, .18);
        border-radius: 13px;
        background: rgba(255, 193, 7, .045);
        color: #c6c6c6;
        font-size: .73rem;
        line-height: 1.55;
    }

    .shift-close-page .notice i {
        margin-top: 2px;
        color: var(--sc-yellow);
    }

    .shift-close-page .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1.1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--sc-border);
    }

    .shift-close-page .btn-secondary-custom,
    .shift-close-page .btn-close-shift {
        min-height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1.1rem;
        border-radius: 12px;
        font-weight: 850;
        text-decoration: none;
    }

    .shift-close-page .btn-secondary-custom {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .shift-close-page .btn-close-shift {
        border: 1px solid #dc3545;
        background: linear-gradient(135deg, #dc3545, #ff6b78);
        color: #fff;
    }

    .shift-close-page .btn-close-shift:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 9px 20px rgba(220, 53, 69, .2);
    }

    @media (max-width: 767.98px) {
        .shift-close-page .hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .shift-close-page .summary-grid {
            grid-template-columns: 1fr;
        }

        .shift-close-page .form-actions {
            flex-direction: column-reverse;
        }

        .shift-close-page .form-actions .btn,
        .shift-close-page .form-actions a {
            width: 100%;
        }
    }
</style>

<div class="shift-close-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="hero">
                <div class="d-flex align-items-center gap-3">
                    <div class="hero-icon">
                        <i class="bi bi-door-closed-fill"></i>
                    </div>

                    <div>
                        <h2>ปิดกะการขาย</h2>
                        <p>
                            ตรวจสอบยอดรับเงินจริงรวมทุกช่องทาง
                            หลังหักรายการยกเลิกก่อนยืนยันปิดกะ
                        </p>
                    </div>
                </div>

                <div class="status-chip">
                    <i class="bi bi-circle-fill"></i>
                    กะกำลังเปิด
                </div>
            </section>

            <form
                id="closeShiftForm"
                action="{{ route('shift.update') }}"
                method="POST"
            >
                @csrf

                <div class="row g-3">
                    <div class="col-lg-4">
                        <section class="panel h-100">
                            <div class="panel-header">
                                <h3 class="panel-title">
                                    <i class="bi bi-person-badge-fill"></i>
                                    ข้อมูลกะ
                                </h3>
                            </div>

                            <div class="panel-body">
                                <div class="summary-grid">
                                    <div class="summary-item">
                                        <span class="summary-label">
                                            ผู้ขาย
                                        </span>

                                        <span class="summary-value">
                                            {{ auth()->user()->name }}
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span class="summary-label">
                                            ประเภทกะ
                                        </span>

                                        <span class="summary-value">
                                            {{ $shift->shiftType->name ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span class="summary-label">
                                            เวลาเปิดกะ
                                        </span>

                                        <span class="summary-value">
                                            {{
                                                optional($shift->opened_at)
                                                    ->format('H:i')
                                                ?? '-'
                                            }}
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span class="summary-label">
                                            เงินทอนเริ่มต้น
                                        </span>

                                        <span class="summary-value">
                                            ฿{{ number_format($shift->opening_cash, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="notice">
                                    <i class="bi bi-info-circle-fill"></i>

                                    <span>
                                        ยอดทุกส่วนในหน้านี้เป็นยอดสุทธิ
                                        และไม่รวมคำสั่งซื้อหรือการขายที่ถูกยกเลิก
                                    </span>
                                </div>

                                @if ($cancelledSalesTotal > 0)
                                    <div
                                        class="notice"
                                        style="
                                            border-color: rgba(220, 53, 69, .28);
                                            background: rgba(220, 53, 69, .07);
                                        "
                                    >
                                        <i
                                            class="bi bi-x-octagon-fill"
                                            style="color: #ff7b86;"
                                        ></i>

                                        <span>
                                            มีรายการยกเลิกในกะนี้
                                            ฿{{ number_format($cancelledSalesTotal, 2) }}
                                            ระบบไม่นำยอดนี้มารวมในการปิดกะ
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-8">
                        <section class="panel h-100">
                            <div class="panel-header">
                                <h3 class="panel-title">
                                    <i class="bi bi-wallet-fill"></i>
                                    สรุปยอดรับเงินจริงรวมทุกช่องทาง
                                </h3>
                            </div>

                            <div class="panel-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="amount-card cash-highlight h-100">
                                            <div class="amount-row">
                                                <div class="amount-label">
                                                    <i class="bi bi-cash-stack"></i>
                                                    เงินสดรวมสุทธิ
                                                </div>

                                                <div class="amount-value">
                                                    ฿{{ number_format($combinedCashSalesNet, 2) }}
                                                </div>
                                            </div>

                                            <div class="subtext">
                                                รวมเงินสดหน้าร้านและยอดเก็บเงินปลายทาง
                                                ที่รับเงินจริงแล้ว
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="amount-card h-100">
                                            <div class="amount-row">
                                                <div class="amount-label">
                                                    <i class="bi bi-qr-code"></i>
                                                    QR / พร้อมเพย์ / เงินโอนรวมสุทธิ
                                                </div>

                                                <div class="amount-value">
                                                    ฿{{ number_format($combinedQrSalesNet, 2) }}
                                                </div>
                                            </div>

                                            <div class="subtext">
                                                รวมยอดโอนจากหน้าร้านและออนไลน์เข้าด้วยกัน
                                            </div>
                                        </div>
                                    </div>

                                    @if ($combinedOtherSalesNet > 0)
                                        <div class="col-md-6">
                                            <div class="amount-card h-100">
                                                <div class="amount-row">
                                                    <div class="amount-label">
                                                        <i class="bi bi-credit-card"></i>
                                                        บัตร / เครดิตรวมสุทธิ
                                                    </div>

                                                    <div class="amount-value">
                                                        ฿{{ number_format($combinedOtherSalesNet, 2) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6">
                                        <div class="amount-card h-100">
                                            <div class="amount-row">
                                                <div class="amount-label">
                                                    <i class="bi bi-receipt"></i>
                                                    ยอดขายรวมสุทธิ
                                                </div>

                                                <div class="amount-value">
                                                    ฿{{ number_format($totalSalesNet, 2) }}
                                                </div>
                                            </div>

                                            <div class="subtext">
                                                รวมทุกช่องทางรับเงิน โดยไม่รวมรายการยกเลิก
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-12">
                        <section class="panel">
                            <div class="panel-header">
                                <h3 class="panel-title">
                                    <i class="bi bi-calculator-fill"></i>
                                    ตรวจนับเงินสดและปิดกะ
                                </h3>
                            </div>

                            <div class="panel-body">
                                <div class="amount-card mb-3">
                                    <div class="amount-row">
                                        <div class="amount-label">
                                            <i class="bi bi-graph-up-arrow"></i>
                                            ยอดขายรวมสุทธิทั้งหมด
                                        </div>

                                        <div class="amount-value">
                                            ฿{{ number_format($totalSalesNet, 2) }}
                                        </div>
                                    </div>

                                    <div class="subtext">
                                        รวมยอดรับเงินจริงจากทุกช่องทาง
                                        โดยไม่รวมรายการที่ถูกยกเลิก
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="amount-card cash-highlight">
                                            <div class="amount-row">
                                                <div class="amount-label">
                                                    <i class="bi bi-wallet2"></i>
                                                    เงินสดที่ควรมี
                                                </div>

                                                <div class="amount-value">
                                                    ฿{{ number_format($expectedCashNet, 2) }}
                                                </div>
                                            </div>

                                            <div class="subtext">
                                                เงินทอนเริ่มต้น + เงินสดรวมทุกช่องทาง
                                                โดยหักรายการยกเลิกแล้ว
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6">
                                        <label
                                            for="closing_cash"
                                            class="form-label"
                                        >
                                            เงินสดที่นับจริง
                                        </label>

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="closing_cash"
                                            id="closing_cash"
                                            class="form-control @error('closing_cash') is-invalid @enderror"
                                            value="{{ old('closing_cash') }}"
                                            placeholder="0.00"
                                            required
                                        >

                                        @error('closing_cash')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div
                                            class="difference-box"
                                            id="cashDifferenceBox"
                                        >
                                            <div class="difference-label">
                                                ผลต่างเงินสด
                                            </div>

                                            <div
                                                class="difference-value"
                                                id="cashDifferenceValue"
                                            >
                                                ฿0.00
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6">
                                        <label
                                            for="closing_qr"
                                            class="form-label"
                                        >
                                            QR / เงินโอนที่ตรวจสอบจริง
                                        </label>

                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="closing_qr"
                                            id="closing_qr"
                                            class="form-control @error('closing_qr') is-invalid @enderror"
                                            value="{{ old('closing_qr', number_format($expectedQrNet, 2, '.', '')) }}"
                                            placeholder="0.00"
                                            required
                                        >

                                        @error('closing_qr')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        <div class="subtext mt-2">
                                            ยอดโอนรวมตามระบบ ฿{{ number_format($expectedQrNet, 2) }}
                                        </div>

                                        <div
                                            class="difference-box"
                                            id="qrDifferenceBox"
                                        >
                                            <div class="difference-label">
                                                ผลต่าง QR / เงินโอน
                                            </div>

                                            <div
                                                class="difference-value"
                                                id="qrDifferenceValue"
                                            >
                                                ฿0.00
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3 col-md-6">
                                        <label for="note" class="form-label">
                                            หมายเหตุ
                                        </label>

                                        <textarea
                                            name="note"
                                            id="note"
                                            class="form-control"
                                            placeholder="ระบุเหตุผลเมื่อยอดเงินสดหรือยอดโอนขาด/เกิน"
                                        >{{ old('note') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <a
                                        href="{{ url()->previous() }}"
                                        class="btn btn-secondary-custom"
                                    >
                                        <i class="bi bi-arrow-left"></i>
                                        กลับ
                                    </a>

                                    <button
                                        type="submit"
                                        class="btn btn-close-shift"
                                        id="closeShiftButton"
                                    >
                                        <i class="bi bi-door-closed-fill"></i>
                                        ยืนยันปิดกะ
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('closeShiftForm');
    const closingCashInput =
        document.getElementById('closing_cash');

    const cashDifferenceBox =
        document.getElementById('cashDifferenceBox');

    const cashDifferenceValue =
        document.getElementById('cashDifferenceValue');

    const closingQrInput =
        document.getElementById('closing_qr');

    const qrDifferenceBox =
        document.getElementById('qrDifferenceBox');

    const qrDifferenceValue =
        document.getElementById('qrDifferenceValue');

    const closeShiftButton =
        document.getElementById('closeShiftButton');

    const expectedCash = Number(
        @json($expectedCashNet)
    );

    const expectedQr = Number(
        @json($expectedQrNet)
    );

    function formatCurrency(value) {
        return new Intl.NumberFormat('th-TH', {
            style: 'currency',
            currency: 'THB'
        }).format(value);
    }

    function updateDifferenceBox(box, valueElement, difference) {
        valueElement.textContent = formatCurrency(difference);

        box.classList.toggle(
            'is-positive',
            difference > 0
        );

        box.classList.toggle(
            'is-negative',
            difference < 0
        );
    }

    function updateDifferences() {
        const closingCash = Number(
            closingCashInput.value || 0
        );

        const closingQr = Number(
            closingQrInput.value || 0
        );

        updateDifferenceBox(
            cashDifferenceBox,
            cashDifferenceValue,
            closingCash - expectedCash
        );

        updateDifferenceBox(
            qrDifferenceBox,
            qrDifferenceValue,
            closingQr - expectedQr
        );
    }

    closingCashInput.addEventListener(
        'input',
        updateDifferences
    );

    closingQrInput.addEventListener(
        'input',
        updateDifferences
    );

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const submitForm = function () {
            closeShiftButton.disabled = true;

            closeShiftButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm"
                    aria-hidden="true"
                ></span>
                <span>กำลังปิดกะ</span>
            `;

            form.submit();
        };

        if (typeof Swal === 'undefined') {
            if (window.confirm('ยืนยันการปิดกะหรือไม่?')) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการปิดกะ?',
            text: 'โปรดตรวจสอบยอดขายและเงินสดให้ถูกต้องก่อนดำเนินการ',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยันปิดกะ',
            cancelButtonText: 'ตรวจสอบอีกครั้ง',
            reverseButtons: true,
            background: '#171717',
            color: '#ffffff',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#555555'
        }).then(function (result) {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    updateDifferences();
});
</script>
@endsection