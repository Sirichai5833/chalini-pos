@extends('layouts.layout')

@section('content')

@if (session('success'))
    <x-sweet-alert
        icon="success"
        title="สำเร็จ!"
        text="{{ session('success') }}"
        confirm-button-text="ตกลง"
    />
@endif

@if (session('error'))
    <x-sweet-alert
        icon="error"
        title="เกิดข้อผิดพลาด"
        text="{{ session('error') }}"
        confirm-button-text="ตกลง"
    />
@endif

<style>
    .profile-edit-page {
        --profile-panel: #171717;
        --profile-panel-soft: #202020;
        --profile-yellow: #ffc107;
        --profile-yellow-soft: #ffdc68;
        --profile-border: #3b3b3b;
        --profile-text: #f7f7f7;
        --profile-muted: #aaa;
        color: var(--profile-text);
    }

    .profile-edit-page .page-shell {
        max-width: 1050px;
        margin: 0 auto;
    }

    .profile-edit-page .profile-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, .26);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, .2),
                transparent 25%
            ),
            linear-gradient(135deg, #1d1d1d, #0b0b0b);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .32);
    }

    .profile-edit-page .profile-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, .07);
    }

    .profile-edit-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .profile-edit-page .hero-avatar {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid var(--profile-yellow);
        border-radius: 21px;
        background: linear-gradient(
            135deg,
            var(--profile-yellow),
            var(--profile-yellow-soft)
        );
        color: #111;
        font-size: 1.75rem;
        font-weight: 900;
        box-shadow: 0 12px 28px rgba(255, 193, 7, .23);
    }

    .profile-edit-page .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-edit-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .profile-edit-page .hero-subtitle {
        margin: .4rem 0 0;
        color: var(--profile-muted);
        font-size: .92rem;
    }

    .profile-edit-page .role-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        margin-top: .65rem;
        padding: .38rem .72rem;
        border: 1px solid rgba(255, 193, 7, .3);
        border-radius: 999px;
        background: rgba(255, 193, 7, .08);
        color: var(--profile-yellow);
        font-size: .78rem;
        font-weight: 800;
    }

    .profile-edit-page .main-card {
        overflow: hidden;
        border: 1px solid var(--profile-border);
        border-radius: 22px;
        background: var(--profile-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, .36);
    }

    .profile-edit-page .card-header-custom {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--profile-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .profile-edit-page .section-title {
        display: flex;
        align-items: center;
        gap: .65rem;
        margin: 0;
        color: var(--profile-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .profile-edit-page .section-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, .12);
    }

    .profile-edit-page .card-body-custom {
        padding: 1.5rem;
    }

    .profile-edit-page .field-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--profile-border);
        border-radius: 16px;
        background: var(--profile-panel-soft);
        transition: .2s ease;
    }

    .profile-edit-page .field-card:hover {
        border-color: rgba(255, 193, 7, .55);
        transform: translateY(-1px);
    }

    .profile-edit-page .form-label {
        margin-bottom: .5rem;
        color: #e8e8e8;
        font-size: .88rem;
        font-weight: 750;
    }

    .profile-edit-page .required-star {
        color: var(--profile-yellow);
    }

    .profile-edit-page .optional-text {
        color: var(--profile-muted);
        font-size: .76rem;
        font-weight: 500;
    }

    .profile-edit-page .input-icon-group {
        position: relative;
    }

    .profile-edit-page .input-icon {
        position: absolute;
        top: 50%;
        left: .9rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--profile-yellow);
        pointer-events: none;
    }

    .profile-edit-page .form-control,
    .profile-edit-page .form-select {
        min-height: 48px;
        padding-left: 2.65rem;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .profile-edit-page .form-control::placeholder {
        color: #707070;
    }

    .profile-edit-page textarea.form-control {
        min-height: 120px;
        padding-top: .85rem;
        resize: vertical;
    }

    .profile-edit-page .textarea-icon {
        top: 1rem;
        transform: none;
    }

    .profile-edit-page .form-select option {
        background: #111;
        color: #fff;
    }

    .profile-edit-page .form-control:focus,
    .profile-edit-page .form-select:focus {
        border-color: var(--profile-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .13);
    }

    .profile-edit-page .form-control.is-invalid,
    .profile-edit-page .form-select.is-invalid {
        border-color: #dc3545;
    }

    .profile-edit-page input[type="file"] {
        padding-left: .8rem;
    }

    .profile-edit-page input[type="file"]::file-selector-button {
        margin-right: .8rem;
        border: 0;
        border-radius: 8px;
        background: var(--profile-yellow);
        color: #111;
        font-weight: 700;
    }

    .profile-edit-page .error-text {
        margin-top: .45rem;
        color: #ff818c;
        font-size: .8rem;
    }

    .profile-edit-page .image-preview-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: .85rem;
        padding: .8rem;
        border: 1px dashed #4a4a4a;
        border-radius: 14px;
        background: #131313;
    }

    .profile-edit-page .image-preview {
        width: 75px;
        height: 75px;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid var(--profile-yellow);
        border-radius: 17px;
        background: #222;
    }

    .profile-edit-page .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-edit-page .image-help {
        color: var(--profile-muted);
        font-size: .78rem;
        line-height: 1.55;
    }

    .profile-edit-page .password-wrapper {
        position: relative;
    }

    .profile-edit-page .password-wrapper .form-control {
        padding-right: 3.2rem;
    }

    .profile-edit-page .password-toggle {
        position: absolute;
        top: 50%;
        right: .75rem;
        z-index: 4;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #aaa;
        transition: .18s ease;
    }

    .profile-edit-page .password-toggle:hover {
        background: rgba(255, 193, 7, .1);
        color: var(--profile-yellow);
    }

    .profile-edit-page .password-help {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-top: .75rem;
        padding: .75rem .85rem;
        border: 1px solid rgba(255, 193, 7, .2);
        border-radius: 12px;
        background: rgba(255, 193, 7, .06);
        color: #c7c7c7;
        font-size: .78rem;
        line-height: 1.55;
    }

    .profile-edit-page .password-help i {
        margin-top: 2px;
        color: var(--profile-yellow);
    }

    .profile-edit-page .password-strength {
        display: none;
        margin-top: .65rem;
    }

    .profile-edit-page .strength-track {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: #353535;
    }

    .profile-edit-page .strength-bar {
        width: 0;
        height: 100%;
        border-radius: inherit;
        transition: .2s ease;
    }

    .profile-edit-page .strength-label {
        display: block;
        margin-top: .35rem;
        color: var(--profile-muted);
        font-size: .75rem;
    }

    .profile-edit-page .form-actions {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--profile-border);
    }

    .profile-edit-page .btn-yellow,
    .profile-edit-page .btn-dark-outline {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1.2rem;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .profile-edit-page .btn-yellow {
        border: 1px solid var(--profile-yellow);
        background: linear-gradient(
            135deg,
            var(--profile-yellow),
            var(--profile-yellow-soft)
        );
        color: #111;
    }

    .profile-edit-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, .22);
    }

    .profile-edit-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .profile-edit-page .btn-dark-outline:hover {
        border-color: var(--profile-yellow);
        background: rgba(255, 193, 7, .06);
        color: var(--profile-yellow);
    }

    @media (max-width: 767.98px) {
        .profile-edit-page .profile-hero,
        .profile-edit-page .card-body-custom {
            padding: 1rem;
        }

        .profile-edit-page .hero-avatar {
            width: 56px;
            height: 56px;
            border-radius: 17px;
            font-size: 1.4rem;
        }

        .profile-edit-page .hero-title {
            font-size: 1.2rem;
        }

        .profile-edit-page .form-actions {
            flex-direction: column-reverse;
        }

        .profile-edit-page .form-actions .btn {
            width: 100%;
        }
    }
</style>

@php
    $fullName = trim(
        ($member->first_name ?? '') . ' ' .
        ($member->last_name ?? '')
    );

    if ($fullName === '') {
        $fullName = $member->name ?? '-';
    }

    $avatarLetter = mb_strtoupper(
        mb_substr(
            $member->first_name
                ?? $member->name
                ?? 'U',
            0,
            1
        )
    );
@endphp

<div class="profile-edit-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="profile-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="hero-avatar">
                            @if ($member->image)
                                <img
                                    src="{{ asset('storage/' . $member->image) }}"
                                    alt="{{ $fullName }}"
                                >
                            @else
                                {{ $avatarLetter }}
                            @endif
                        </span>

                        <div>
                            <h2 class="hero-title">
                                แก้ไขข้อมูลส่วนตัว
                            </h2>

                            <p class="hero-subtitle">
                                {{ $fullName }}
                            </p>

                            <span class="role-badge">
                                <i class="bi bi-person-badge-fill"></i>
                                {{ ucfirst($member->role ?? 'user') }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="main-card">
                <div class="card-header-custom">
                    <h3 class="section-title">
                        <i class="bi bi-person-gear"></i>
                        ข้อมูลบัญชีผู้ใช้งาน
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form
                        action="{{ route('staff.update', $member->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="profileForm"
                    >
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="first_name" class="form-label">
                                        ชื่อ
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-person input-icon"></i>

                                        <input
                                            type="text"
                                            name="first_name"
                                            id="first_name"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            value="{{ old('first_name', $member->first_name) }}"
                                            placeholder="กรอกชื่อ"
                                            autocomplete="given-name"
                                            required
                                        >
                                    </div>

                                    @error('first_name')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="last_name" class="form-label">
                                        นามสกุล
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-person-lines-fill input-icon"></i>

                                        <input
                                            type="text"
                                            name="last_name"
                                            id="last_name"
                                            class="form-control @error('last_name') is-invalid @enderror"
                                            value="{{ old('last_name', $member->last_name) }}"
                                            placeholder="กรอกนามสกุล"
                                            autocomplete="family-name"
                                            required
                                        >
                                    </div>

                                    @error('last_name')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="email" class="form-label">
                                        อีเมล
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-envelope input-icon"></i>

                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $member->email) }}"
                                            placeholder="example@email.com"
                                            autocomplete="email"
                                            required
                                        >
                                    </div>

                                    <div
                                        id="emailError"
                                        class="error-text d-none"
                                    >
                                        กรุณากรอกอีเมลให้ถูกต้อง
                                    </div>

                                    @error('email')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="phone" class="form-label">
                                        เบอร์โทรศัพท์
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-telephone input-icon"></i>

                                        <input
                                            type="tel"
                                            name="phone"
                                            id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $member->phone) }}"
                                            placeholder="0812345678"
                                            maxlength="10"
                                            inputmode="numeric"
                                            autocomplete="tel"
                                            required
                                        >
                                    </div>

                                    <div id="phoneError" class="error-text d-none">
                                        เบอร์โทรศัพท์ต้องขึ้นต้นด้วย 0 และมี 9-10 หลัก
                                    </div>

                                    @error('phone')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="field-card">
                                    <label for="address" class="form-label">
                                        ที่อยู่
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-geo-alt input-icon textarea-icon"></i>

                                        <textarea
                                            name="address"
                                            id="address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            rows="4"
                                            placeholder="กรอกบ้านเลขที่ ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด และรหัสไปรษณีย์"
                                            autocomplete="street-address"
                                            required
                                        >{{ old('address', $member->address) }}</textarea>
                                    </div>

                                    @error('address')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="field-card">
                                    <label for="image" class="form-label">
                                        รูปโปรไฟล์
                                        <span class="optional-text">
                                            ไม่เปลี่ยนให้เว้นว่าง
                                        </span>
                                    </label>

                                    <input
                                        type="file"
                                        name="image"
                                        id="image"
                                        class="form-control @error('image') is-invalid @enderror"
                                        accept="image/jpeg,image/png,image/jpg,image/webp"
                                    >

                                    <div class="image-preview-box">
                                        <div class="image-preview">
                                            <img
                                                id="profileImagePreview"
                                                src="{{
                                                    $member->image
                                                        ? asset('storage/' . $member->image)
                                                        : 'https://ui-avatars.com/api/?name='
                                                            . urlencode($fullName)
                                                            . '&background=ffc107&color=111111'
                                                }}"
                                                alt="ตัวอย่างรูปโปรไฟล์"
                                            >
                                        </div>

                                        <div class="image-help">
                                            รองรับไฟล์ JPG, JPEG, PNG และ WEBP<br>
                                            แนะนำรูปสี่เหลี่ยมจัตุรัส และขนาดไม่เกิน 2 MB
                                        </div>
                                    </div>

                                    @error('image')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="password" class="form-label">
                                        รหัสผ่านใหม่
                                        <span class="optional-text">
                                            ไม่เปลี่ยนให้เว้นว่าง
                                        </span>
                                    </label>

                                    <div class="input-icon-group password-wrapper">
                                        <i class="bi bi-lock input-icon"></i>

                                        <input
                                            type="password"
                                            name="password"
                                            id="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="กรอกเฉพาะเมื่อต้องการเปลี่ยน"
                                            minlength="8"
                                            autocomplete="new-password"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-password-target="password"
                                            aria-label="แสดงหรือซ่อนรหัสผ่าน"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <div class="password-help">
                                        <i class="bi bi-shield-lock-fill"></i>

                                        <span>
                                            อย่างน้อย 8 ตัว มีตัวพิมพ์ใหญ่
                                            และอักขระพิเศษ เช่น @ # $ %
                                        </span>
                                    </div>

                                    <div
                                        class="password-strength"
                                        id="passwordStrength"
                                    >
                                        <div class="strength-track">
                                            <div
                                                class="strength-bar"
                                                id="strengthBar"
                                            ></div>
                                        </div>

                                        <span
                                            class="strength-label"
                                            id="strengthLabel"
                                        ></span>
                                    </div>

                                    <div
                                        id="passwordError"
                                        class="error-text d-none"
                                    >
                                        รหัสผ่านต้องมีอย่างน้อย 8 ตัว
                                        มีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และอักขระพิเศษ
                                    </div>

                                    @error('password')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label
                                        for="password_confirmation"
                                        class="form-label"
                                    >
                                        ยืนยันรหัสผ่านใหม่
                                    </label>

                                    <div class="input-icon-group password-wrapper">
                                        <i class="bi bi-lock-check input-icon"></i>

                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            id="password_confirmation"
                                            class="form-control"
                                            placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                                            minlength="8"
                                            autocomplete="new-password"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            data-password-target="password_confirmation"
                                            aria-label="แสดงหรือซ่อนรหัสผ่าน"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <div
                                        id="confirmPasswordError"
                                        class="error-text d-none"
                                    >
                                        รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a
                                href="{{ url('sale') }}"
                                class="btn btn-dark-outline"
                            >
                                <i class="bi bi-x-circle"></i>
                                ยกเลิก
                            </a>

                            <button
                                type="submit"
                                class="btn btn-yellow"
                                id="saveProfileButton"
                            >
                                <i class="bi bi-floppy-fill"></i>
                                บันทึกการเปลี่ยนแปลง
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
    const profileForm = document.getElementById('profileForm');
    const saveButton = document.getElementById('saveProfileButton');

    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const fullNamePreview = document.getElementById('fullNamePreview');

    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');

    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('profileImagePreview');

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput =
        document.getElementById('password_confirmation');

    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError =
        document.getElementById('confirmPasswordError');

    const passwordStrength =
        document.getElementById('passwordStrength');

    const strengthBar = document.getElementById('strengthBar');
    const strengthLabel = document.getElementById('strengthLabel');

    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const inputId = button.dataset.passwordTarget;
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (!input) {
                return;
            }

            const shouldShow = input.type === 'password';

            input.type = shouldShow ? 'text' : 'password';

            icon?.classList.toggle('bi-eye', !shouldShow);
            icon?.classList.toggle('bi-eye-slash', shouldShow);
        });
    });

    const updateFullNamePreview = function () {
        if (!fullNamePreview) {
            return;
        }

        fullNamePreview.value = [
            firstNameInput?.value.trim(),
            lastNameInput?.value.trim()
        ].filter(Boolean).join(' ');
    };

    firstNameInput?.addEventListener('input', updateFullNamePreview);
    lastNameInput?.addEventListener('input', updateFullNamePreview);

    phoneInput?.addEventListener('input', function () {
        phoneInput.value = phoneInput.value
            .replace(/\D/g, '')
            .slice(0, 10);

        phoneInput.classList.remove('is-invalid');
        phoneError?.classList.add('d-none');
    });

    imageInput?.addEventListener('change', function () {
        const file = imageInput.files?.[0];

        if (!file || !imagePreview) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            imageInput.value = '';

            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น',
                background: '#171717',
                color: '#ffffff',
                confirmButtonColor: '#ffc107',
                customClass: {
                    confirmButton: 'text-dark fw-bold'
                }
            });

            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function (event) {
            imagePreview.src = event.target.result;
        });

        reader.readAsDataURL(file);
    });

    passwordInput?.addEventListener('input', function () {
        const value = passwordInput.value;

        if (!value) {
            passwordStrength.style.display = 'none';
            strengthBar.style.width = '0';
            passwordError.classList.add('d-none');
            return;
        }

        passwordStrength.style.display = 'block';

        let score = 0;

        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const levels = [
            {
                width: '20%',
                label: 'รหัสผ่านอ่อนมาก',
                color: '#dc3545'
            },
            {
                width: '40%',
                label: 'รหัสผ่านอ่อน',
                color: '#fd7e14'
            },
            {
                width: '60%',
                label: 'รหัสผ่านปานกลาง',
                color: '#ffc107'
            },
            {
                width: '80%',
                label: 'รหัสผ่านดี',
                color: '#8bc34a'
            },
            {
                width: '100%',
                label: 'รหัสผ่านแข็งแรง',
                color: '#28a745'
            }
        ];

        const selected = levels[Math.max(0, score - 1)];

        strengthBar.style.width = selected.width;
        strengthBar.style.background = selected.color;

        strengthLabel.textContent = selected.label;
        strengthLabel.style.color = selected.color;
    });

    profileForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        let valid = true;

        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(emailInput.value.trim())) {
            emailError.classList.remove('d-none');
            emailInput.classList.add('is-invalid');
            valid = false;
        } else {
            emailError.classList.add('d-none');
            emailInput.classList.remove('is-invalid');
        }

        const phonePattern = /^0[0-9]{8,9}$/;

        if (!phonePattern.test(phoneInput.value.trim())) {
            phoneError.classList.remove('d-none');
            phoneInput.classList.add('is-invalid');
            valid = false;
        } else {
            phoneError.classList.add('d-none');
            phoneInput.classList.remove('is-invalid');
        }

        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (password.length > 0) {
            const hasMinimumLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasSpecialCharacter =
                /[^A-Za-z0-9]/.test(password);

            if (
                !hasMinimumLength ||
                !hasUppercase ||
                !hasLowercase ||
                !hasSpecialCharacter
            ) {
                passwordError.classList.remove('d-none');
                passwordInput.classList.add('is-invalid');
                valid = false;
            } else {
                passwordError.classList.add('d-none');
                passwordInput.classList.remove('is-invalid');
            }

            if (password !== confirmPassword) {
                confirmPasswordError.classList.remove('d-none');
                confirmPasswordInput.classList.add('is-invalid');
                valid = false;
            } else {
                confirmPasswordError.classList.add('d-none');
                confirmPasswordInput.classList.remove('is-invalid');
            }
        } else {
            passwordError.classList.add('d-none');
            confirmPasswordError.classList.add('d-none');
            passwordInput.classList.remove('is-invalid');
            confirmPasswordInput.classList.remove('is-invalid');
        }

        if (!valid) {
            profileForm
                .querySelector('.is-invalid')
                ?.focus();

            return;
        }

        const submitForm = function () {
            saveButton.disabled = true;

            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                กำลังบันทึก
            `;

            profileForm.submit();
        };

        if (typeof Swal === 'undefined') {
            if (window.confirm('ยืนยันการแก้ไขข้อมูลส่วนตัวหรือไม่?')) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการแก้ไขข้อมูล?',
            text: 'กรุณาตรวจสอบข้อมูลส่วนตัวก่อนบันทึก',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึกการแก้ไข',
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
});
</script>

@endsection
