@extends('layouts.layout')

@section('content')

<style>
    .settings-page {
        --setting-panel: #171717;
        --setting-panel-soft: #202020;
        --setting-yellow: #ffc107;
        --setting-yellow-soft: #ffdc68;
        --setting-border: #3a3a3a;
        --setting-text: #f7f7f7;
        --setting-muted: #a9a9a9;
        color: var(--setting-text);
    }

    .settings-page .page-shell {
        max-width: 1150px;
        margin: 0 auto;
    }

    .settings-page .settings-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.26);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, 0.2),
                transparent 25%
            ),
            linear-gradient(135deg, #1d1d1d, #0b0b0b);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.32);
    }

    .settings-page .settings-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .settings-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .settings-page .hero-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 19px;
        background: linear-gradient(
            135deg,
            var(--setting-yellow),
            var(--setting-yellow-soft)
        );
        color: #111;
        font-size: 1.6rem;
        box-shadow: 0 11px 27px rgba(255, 193, 7, 0.22);
    }

    .settings-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .settings-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--setting-muted);
        font-size: 0.92rem;
    }

    .settings-page .settings-card {
        overflow: hidden;
        border: 1px solid var(--setting-border);
        border-radius: 22px;
        background: var(--setting-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.35);
    }

    .settings-page .settings-card-header {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--setting-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .settings-page .section-heading {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: var(--setting-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .settings-page .section-heading i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, 0.12);
    }

    .settings-page .settings-card-body {
        padding: 1.5rem;
    }

    .settings-page .setting-section {
        margin-bottom: 1.25rem;
        padding: 1.15rem;
        border: 1px solid var(--setting-border);
        border-radius: 17px;
        background: var(--setting-panel-soft);
    }

    .settings-page .setting-section:last-child {
        margin-bottom: 0;
    }

    .settings-page .setting-section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 1rem;
        color: #fff;
        font-size: 0.98rem;
        font-weight: 800;
    }

    .settings-page .setting-section-title i {
        color: var(--setting-yellow);
    }

    .settings-page .setting-section-description {
        margin: -0.55rem 0 1rem;
        color: var(--setting-muted);
        font-size: 0.79rem;
    }

    .settings-page .form-label {
        margin-bottom: 0.5rem;
        color: #e8e8e8;
        font-size: 0.86rem;
        font-weight: 750;
    }

    .settings-page .required-star {
        color: var(--setting-yellow);
    }

    .settings-page .input-icon-group {
        position: relative;
    }

    .settings-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.9rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--setting-yellow);
        pointer-events: none;
    }

    .settings-page .form-control,
    .settings-page .form-select {
        min-height: 49px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .settings-page .input-icon-group .form-control,
    .settings-page .input-icon-group .form-select {
        padding-left: 2.65rem;
    }

    .settings-page .form-control::placeholder {
        color: #707070;
    }

    .settings-page .form-control:focus,
    .settings-page .form-select:focus {
        border-color: var(--setting-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .settings-page input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }

    .settings-page .field-help {
        display: block;
        margin-top: 0.45rem;
        color: var(--setting-muted);
        font-size: 0.76rem;
        line-height: 1.5;
    }

    .settings-page .error-text {
        margin-top: 0.45rem;
        color: #ff818c;
        font-size: 0.8rem;
    }

    .settings-page .time-preview {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-top: 0.9rem;
        padding: 0.75rem 0.9rem;
        border: 1px solid rgba(255, 193, 7, 0.22);
        border-radius: 12px;
        background: rgba(255, 193, 7, 0.06);
        color: #d7d7d7;
        font-size: 0.8rem;
    }

    .settings-page .time-preview i {
        color: var(--setting-yellow);
    }

    .settings-page .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.4rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--setting-border);
    }

    .settings-page .btn-yellow,
    .settings-page .btn-dark-outline {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.2rem;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .settings-page .btn-yellow {
        border: 1px solid var(--setting-yellow);
        background: linear-gradient(
            135deg,
            var(--setting-yellow),
            var(--setting-yellow-soft)
        );
        color: #111;
    }

    .settings-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .settings-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .settings-page .btn-dark-outline:hover {
        border-color: var(--setting-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--setting-yellow);
    }

    .settings-page .alert {
        border-radius: 14px;
        border: 0;
    }

    .settings-page .alert-success {
        border: 1px solid rgba(25, 135, 84, 0.35);
        background: rgba(25, 135, 84, 0.13);
        color: #7ee2aa;
    }

    .settings-page .alert-danger {
        border: 1px solid rgba(220, 53, 69, 0.35);
        background: rgba(220, 53, 69, 0.13);
        color: #ff9aa3;
    }

    @media (max-width: 767.98px) {
        .settings-page .settings-hero,
        .settings-page .settings-card-body {
            padding: 1rem;
        }

        .settings-page .hero-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
        }

        .settings-page .hero-title {
            font-size: 1.25rem;
        }

        .settings-page .setting-section {
            padding: 1rem;
        }

        .settings-page .form-actions {
            flex-direction: column-reverse;
        }

        .settings-page .form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="settings-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="settings-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="hero-icon">
                            <i class="bi bi-gear-fill"></i>
                        </span>

                        <div>
                            <h2 class="hero-title">
                                ตั้งค่าระบบ
                            </h2>

                            <p class="hero-subtitle">
                                จัดการข้อมูลร้าน การชำระเงิน เงินทอน และเวลาทำการ
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <div class="fw-bold mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        กรุณาตรวจสอบข้อมูล
                    </div>

                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="settings-card">
                <div class="settings-card-header">
                    <h3 class="section-heading">
                        <i class="bi bi-sliders"></i>
                        การตั้งค่าทั่วไป
                    </h3>
                </div>

                <div class="settings-card-body">
                    <form
                        id="settingsForm"
                        method="POST"
                        action="{{ route('staff.settings.update') }}"
                    >
                        @csrf
                        @method('PUT')

                        <section class="setting-section">
                            <h4 class="setting-section-title">
                                <i class="bi bi-shop"></i>
                                ข้อมูลร้าน
                            </h4>

                            <p class="setting-section-description">
                                ข้อมูลส่วนนี้สามารถนำไปแสดงบนหัวระบบและใบเสร็จ
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="shop_name" class="form-label">
                                        ชื่อร้าน
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-shop-window input-icon"></i>

                                        <input
                                            type="text"
                                            name="shop_name"
                                            id="shop_name"
                                            class="form-control @error('shop_name') is-invalid @enderror"
                                            value="{{ old('shop_name', $settings['shop_name'] ?? '') }}"
                                            placeholder="กรอกชื่อร้าน"
                                            required
                                        >
                                    </div>

                                    @error('shop_name')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">
                                        เบอร์โทรศัพท์ร้าน
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-telephone-fill input-icon"></i>

                                        <input
                                            type="tel"
                                            name="phone"
                                            id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $settings['phone'] ?? '') }}"
                                            placeholder="0812345678"
                                            inputmode="tel"
                                        >
                                    </div>

                                    @error('phone')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <section class="setting-section">
                            <h4 class="setting-section-title">
                                <i class="bi bi-wallet2"></i>
                                การรับชำระเงิน
                            </h4>

                            <p class="setting-section-description">
                                ตั้งค่าบัญชีพร้อมเพย์และเงินสดเริ่มต้นสำหรับเปิดกะ
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="promptpay" class="form-label">
                                        หมายเลขพร้อมเพย์
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-qr-code input-icon"></i>

                                        <input
                                            type="text"
                                            name="promptpay"
                                            id="promptpay"
                                            class="form-control @error('promptpay') is-invalid @enderror"
                                            value="{{ old('promptpay', $settings['promptpay'] ?? '') }}"
                                            placeholder="เบอร์โทรศัพท์หรือเลขประจำตัว"
                                            inputmode="numeric"
                                        >
                                    </div>

                                    <small class="field-help">
                                        ใช้สำหรับสร้าง QR Code รับชำระเงิน
                                    </small>

                                    @error('promptpay')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="opening_cash" class="form-label">
                                        เงินทอนเริ่มต้น
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-cash-stack input-icon"></i>

                                        <input
                                            type="number"
                                            name="opening_cash"
                                            id="opening_cash"
                                            class="form-control @error('opening_cash') is-invalid @enderror"
                                            min="0"
                                            step="0.01"
                                            value="{{ old('opening_cash', $settings['opening_cash'] ?? 0) }}"
                                            placeholder="0.00"
                                        >
                                    </div>

                                    <small class="field-help">
                                        จำนวนเงินสดเริ่มต้นเมื่อพนักงานเปิดกะขาย
                                    </small>

                                    @error('opening_cash')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_fee" class="form-label">
                                        ค่าจัดส่ง
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-truck input-icon"></i>

                                        <input
                                            type="number"
                                            name="shipping_fee"
                                            id="shipping_fee"
                                            class="form-control @error('shipping_fee') is-invalid @enderror"
                                            min="0"
                                            step="0.01"
                                            value="{{ old('shipping_fee', $settings['shipping_fee'] ?? 0) }}"
                                            placeholder="0.00"
                                            required
                                        >
                                    </div>

                                    <small class="field-help">
                                        ค่าจัดส่งสำหรับคำสั่งซื้อออนไลน์
                                    </small>

                                    @error('shipping_fee')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- เปิดใช้งานส่งฟรี --}}
<div class="col-md-6">
    <label class="form-label">โปรโมชั่นจัดส่งฟรี</label>

    <div class="form-check form-switch mt-2">
        <input
            type="hidden"
            name="shipping_free_enabled"
            value="0"
        >

        <input
            class="form-check-input"
            type="checkbox"
            name="shipping_free_enabled"
            id="shipping_free_enabled"
            value="1"
            {{ old(
                'shipping_free_enabled',
                $settings['shipping_free_enabled'] ?? 0
            ) ? 'checked' : '' }}
        >

        <label class="form-check-label" for="shipping_free_enabled">
            เปิดใช้งานซื้อครบส่งฟรี
        </label>
    </div>
</div>

{{-- ยอดซื้อขั้นต่ำ --}}
<div class="col-md-6">
    <label for="shipping_free_minimum" class="form-label">
        ยอดซื้อขั้นต่ำสำหรับส่งฟรี (บาท)
    </label>

    <div class="input-icon-group">
        <i class="bi bi-gift-fill input-icon"></i>

        <input
            type="number"
            name="shipping_free_minimum"
            id="shipping_free_minimum"
            class="form-control"
            min="0"
            step="0.01"
            value="{{ old(
                'shipping_free_minimum',
                $settings['shipping_free_minimum'] ?? 300
            ) }}"
            placeholder="เช่น 300"
        >
    </div>

    <small class="field-help">
        เมื่อลูกค้าซื้อสินค้าครบยอดที่กำหนด จะได้รับสิทธิ์จัดส่งฟรี
    </small>
</div>
                            </div>
                        </section>
{{-- การตั้งค่าการขายส่ง --}}
<section class="setting-section">
    <h4 class="setting-section-title">
        <i class="bi bi-box-seam-fill"></i>
        การตั้งค่าการขายส่ง
    </h4>


    <div class="row g-3">

        <div class="col-md-6">
            <label for="wholesale_minimum_amount" class="form-label">
                ยอดซื้อขั้นต่ำสำหรับขายส่ง (บาท)
                <span class="required-star">*</span>
            </label>

            <div class="input-icon-group">
                <i class="bi bi-cash-stack input-icon"></i>

                <input
                    type="number"
                    name="wholesale_minimum_amount"
                    id="wholesale_minimum_amount"
                    class="form-control @error('wholesale_minimum_amount') is-invalid @enderror"
                    min="0"
                    step="0.01"
                    value="{{ old(
                        'wholesale_minimum_amount',
                        $settings['wholesale_minimum_amount'] ?? 500
                    ) }}"
                    placeholder="เช่น 500"
                    required
                >
            </div>



            @error('wholesale_minimum_amount')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="time-preview">
        <i class="bi bi-info-circle-fill"></i>

        <span id="wholesalePreview">
            ยอดซื้อขั้นต่ำสำหรับขายส่ง:
            ฿{{ number_format(
                old(
                    'wholesale_minimum_amount',
                    $settings['wholesale_minimum_amount'] ?? 500
                ),
                2
            ) }}
        </span>
    </div>
</section>
                        <section class="setting-section">
                            <h4 class="setting-section-title">
                                <i class="bi bi-clock-fill"></i>
                                เวลาทำการ
                            </h4>

                            <p class="setting-section-description">
                                กำหนดเวลาเปิดและปิดร้านสำหรับแสดงสถานะร้าน
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="open_time" class="form-label">
                                        เวลาเปิดร้าน
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-sunrise-fill input-icon"></i>

                                        <input
                                            type="time"
                                            name="open_time"
                                            id="open_time"
                                            class="form-control @error('open_time') is-invalid @enderror"
                                            value="{{ old('open_time', $settings['open_time'] ?? '') }}"
                                        >
                                    </div>

                                    @error('open_time')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="close_time" class="form-label">
                                        เวลาปิดร้าน
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-sunset-fill input-icon"></i>

                                        <input
                                            type="time"
                                            name="close_time"
                                            id="close_time"
                                            class="form-control @error('close_time') is-invalid @enderror"
                                            value="{{ old('close_time', $settings['close_time'] ?? '') }}"
                                        >
                                    </div>

                                    @error('close_time')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="time-preview">
                                <i class="bi bi-info-circle-fill"></i>

                                <span id="businessHoursPreview">
                                    เวลาทำการ:
                                    {{ $settings['open_time'] ?? '--:--' }}
                                    ถึง
                                    {{ $settings['close_time'] ?? '--:--' }}
                                </span>
                            </div>
                        </section>

                        <section class="setting-section">
                            <h4 class="setting-section-title">
                                <i class="bi bi-building-fill"></i>
                                การตั้งค่าชั้นและห้อง
                            </h4>

                            <p class="setting-section-description">
                                กำหนดจำนวนชั้นและจำนวนห้องของแต่ละชั้น
                            </p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="floors" class="form-label">
                                        จำนวนชั้น
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-layers-fill input-icon"></i>

                                        <input
                                            type="number"
                                            name="floors"
                                            id="floors"
                                            class="form-control @error('floors') is-invalid @enderror"
                                            min="1"
                                            max="100"
                                            step="1"
                                            value="{{ old('floors', $settings['floors'] ?? 3) }}"
                                            placeholder="เช่น 3"
                                            required
                                        >
                                    </div>

                                    <small class="field-help">
                                        จำนวนชั้นทั้งหมดของอาคาร
                                    </small>

                                    @error('floors')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="roomsPerFloor" class="form-label">
                                        จำนวนห้องในแต่ละชั้น
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-door-open-fill input-icon"></i>

                                        <input
                                            type="text"
                                            name="roomsPerFloor"
                                            id="roomsPerFloor"
                                            class="form-control @error('roomsPerFloor') is-invalid @enderror"
                                            value="{{ old(
                                                'roomsPerFloor',
                                                is_array($settings['roomsPerFloor'] ?? null)
                                                    ? implode(',', $settings['roomsPerFloor'])
                                                    : trim(
                                                        (string) ($settings['roomsPerFloor'] ?? ''),
                                                        '[]'
                                                    )
                                            ) }}"
                                            placeholder="เช่น 24,24,24"
                                            required
                                        >
                                    </div>

                                    <small class="field-help">
                                        กรอกจำนวนห้องแต่ละชั้นคั่นด้วยจุลภาค เช่น
                                        24,24,24 สำหรับอาคาร 3 ชั้น
                                    </small>

                                    @error('roomsPerFloor')
                                        <div class="error-text">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="time-preview">
                                <i class="bi bi-info-circle-fill"></i>

                                <span id="buildingPreview">
                                    อาคาร {{ $settings['floors'] ?? 3 }} ชั้น —
                                    ห้องต่อชั้น:
                                    {{
                                        is_array($settings['roomsPerFloor'] ?? null)
                                            ? implode(', ', $settings['roomsPerFloor'])
                                            : trim(
                                                (string) ($settings['roomsPerFloor'] ?? ''),
                                                '[]'
                                            )
                                    }}
                                </span>
                            </div>
                        </section>

                        <div class="form-actions">
                            <button
                                type="button"
                                class="btn btn-dark-outline"
                                id="resetSettingsButton"
                            >
                                <i class="bi bi-arrow-counterclockwise"></i>
                                คืนค่าเดิม
                            </button>

                            <button
                                type="submit"
                                class="btn btn-yellow"
                                id="saveSettingsButton"
                            >
                                <i class="bi bi-floppy-fill"></i>
                                บันทึกการตั้งค่า
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const settingsForm = document.getElementById('settingsForm');
    const saveButton = document.getElementById('saveSettingsButton');
    const resetButton = document.getElementById('resetSettingsButton');

    const openTimeInput = document.getElementById('open_time');
    const closeTimeInput = document.getElementById('close_time');
    const businessHoursPreview =
        document.getElementById('businessHoursPreview');

    const floorsInput = document.getElementById('floors');
    const roomsPerFloorInput =
        document.getElementById('roomsPerFloor');
    const buildingPreview =
        document.getElementById('buildingPreview');

    function updateBusinessHoursPreview() {
        const openTime = openTimeInput?.value || '--:--';
        const closeTime = closeTimeInput?.value || '--:--';

        if (businessHoursPreview) {
            businessHoursPreview.textContent =
                `เวลาทำการ: ${openTime} ถึง ${closeTime}`;
        }
    }
    const wholesaleMinimumInput = document.getElementById(
    'wholesale_minimum_amount'
);

const wholesalePreview = document.getElementById(
    'wholesalePreview'
);

function updateWholesalePreview() {
    const amount = parseFloat(wholesaleMinimumInput?.value) || 0;

    if (wholesalePreview) {
        wholesalePreview.textContent =
            `ยอดซื้อขั้นต่ำสำหรับขายส่ง: ฿${amount.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
    }
}

wholesaleMinimumInput?.addEventListener(
    'input',
    updateWholesalePreview
);

updateWholesalePreview();

    function updateBuildingPreview() {
        const floors = floorsInput?.value || '-';
        const rooms = roomsPerFloorInput?.value || '-';

        if (buildingPreview) {
            buildingPreview.textContent =
                `อาคาร ${floors} ชั้น — ห้องต่อชั้น: ${rooms}`;
        }
    }

    openTimeInput?.addEventListener(
        'change',
        updateBusinessHoursPreview
    );

    closeTimeInput?.addEventListener(
        'change',
        updateBusinessHoursPreview
    );

    floorsInput?.addEventListener(
        'input',
        updateBuildingPreview
    );

    roomsPerFloorInput?.addEventListener(
        'input',
        updateBuildingPreview
    );

    resetButton?.addEventListener('click', function () {
        if (typeof Swal === 'undefined') {
            if (window.confirm('คืนค่าข้อมูลกลับเป็นค่าก่อนแก้ไขหรือไม่?')) {
                settingsForm.reset();
                updateBusinessHoursPreview();
                updateBuildingPreview();
            }

            return;
        }

        Swal.fire({
            title: 'คืนค่าข้อมูลเดิม?',
            text: 'ข้อมูลที่แก้ไขแต่ยังไม่ได้บันทึกจะถูกยกเลิก',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'คืนค่าเดิม',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true,
            background: '#171717',
            color: '#ffffff',
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#555555',
            customClass: {
                confirmButton: 'text-dark fw-bold'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                settingsForm.reset();
                updateBusinessHoursPreview();
                updateBuildingPreview();
            }
        });
    });

    settingsForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        const submitForm = function () {
            saveButton.disabled = true;

            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                กำลังบันทึก
            `;

            settingsForm.submit();
        };

        if (typeof Swal === 'undefined') {
            if (window.confirm('ยืนยันการบันทึกการตั้งค่าหรือไม่?')) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: 'การตั้งค่าใหม่จะถูกนำไปใช้กับระบบ',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึกการตั้งค่า',
            cancelButtonText: 'ตรวจสอบอีกครั้ง',
            reverseButtons: true,
            background: '#171717',
            color: '#ffffff',
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#555555',
            customClass: {
                confirmButton: 'text-dark fw-bold'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    updateBusinessHoursPreview();
    updateBuildingPreview();
});
</script>

@endsection