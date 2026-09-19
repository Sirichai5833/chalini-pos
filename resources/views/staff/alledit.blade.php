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
    .staff-edit-page {
        --staff-panel: #171717;
        --staff-panel-soft: #202020;
        --staff-yellow: #ffc107;
        --staff-yellow-soft: #ffe27a;
        --staff-border: #3b3b3b;
        --staff-text: #f7f7f7;
        --staff-muted: #aaa;
        color: var(--staff-text);
    }

    .staff-edit-page .page-shell {
        max-width: 1080px;
        margin: 0 auto;
    }

    .staff-edit-page .page-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.27);
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

    .staff-edit-page .page-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .staff-edit-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .staff-edit-page .hero-avatar {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid var(--staff-yellow);
        border-radius: 21px;
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
        font-size: 1.7rem;
        font-weight: 900;
    }

    .staff-edit-page .hero-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .staff-edit-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .staff-edit-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--staff-muted);
        font-size: 0.92rem;
    }

    .staff-edit-page .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.65rem;
        padding: 0.38rem 0.72rem;
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--staff-yellow);
        font-size: 0.76rem;
        font-weight: 800;
    }

    .staff-edit-page .main-card {
        overflow: hidden;
        border: 1px solid var(--staff-border);
        border-radius: 22px;
        background: var(--staff-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.36);
    }

    .staff-edit-page .card-header-custom {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--staff-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .staff-edit-page .section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: var(--staff-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .staff-edit-page .section-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, 0.12);
    }

    .staff-edit-page .card-body-custom {
        padding: 1.5rem;
    }

    .staff-edit-page .field-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--staff-border);
        border-radius: 16px;
        background: var(--staff-panel-soft);
        transition: 0.2s ease;
    }

    .staff-edit-page .field-card:hover {
        border-color: rgba(255, 193, 7, 0.55);
        transform: translateY(-1px);
    }

    .staff-edit-page .form-label {
        margin-bottom: 0.5rem;
        color: #e8e8e8;
        font-size: 0.88rem;
        font-weight: 750;
    }

    .staff-edit-page .required-star {
        color: var(--staff-yellow);
    }

    .staff-edit-page .optional-text {
        color: var(--staff-muted);
        font-size: 0.76rem;
        font-weight: 500;
    }

    .staff-edit-page .input-icon-group {
        position: relative;
    }

    .staff-edit-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.9rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--staff-yellow);
        pointer-events: none;
    }

    .staff-edit-page .textarea-icon {
        top: 1rem;
        transform: none;
    }

    .staff-edit-page .form-control,
    .staff-edit-page .form-select {
        min-height: 49px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .staff-edit-page .input-icon-group .form-control,
    .staff-edit-page .input-icon-group .form-select {
        padding-left: 2.65rem;
    }

    .staff-edit-page textarea.form-control {
        min-height: 125px;
        padding-top: 0.8rem;
        resize: vertical;
    }

    .staff-edit-page .form-control::placeholder {
        color: #707070;
    }

    .staff-edit-page .form-control:focus,
    .staff-edit-page .form-select:focus {
        border-color: var(--staff-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .staff-edit-page .form-select option {
        background: #111;
        color: #fff;
    }

    .staff-edit-page .form-control.is-invalid,
    .staff-edit-page .form-select.is-invalid {
        border-color: #dc3545;
    }

    .staff-edit-page .error-text {
        margin-top: 0.45rem;
        color: #ff818c;
        font-size: 0.8rem;
    }

    .staff-edit-page .field-help {
        display: block;
        margin-top: 0.45rem;
        color: var(--staff-muted);
        font-size: 0.76rem;
        line-height: 1.5;
    }

    .staff-edit-page input[type="file"] {
        padding-left: 0.8rem;
    }

    .staff-edit-page input[type="file"]::file-selector-button {
        margin-right: 0.8rem;
        border: 0;
        border-radius: 8px;
        background: var(--staff-yellow);
        color: #111;
        font-weight: 750;
    }

    .staff-edit-page .image-preview-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.9rem;
        padding: 0.8rem;
        border: 1px dashed #4a4a4a;
        border-radius: 14px;
        background: #131313;
    }

    .staff-edit-page .image-preview {
        width: 82px;
        height: 82px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid var(--staff-yellow);
        border-radius: 18px;
        background: #222;
        color: var(--staff-yellow);
        font-size: 1.8rem;
    }

    .staff-edit-page .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .staff-edit-page .password-wrapper {
        position: relative;
    }

    .staff-edit-page .password-wrapper .form-control {
        padding-right: 3.2rem;
    }

    .staff-edit-page .password-toggle {
        position: absolute;
        top: 50%;
        right: 0.75rem;
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
    }

    .staff-edit-page .password-toggle:hover {
        background: rgba(255, 193, 7, 0.1);
        color: var(--staff-yellow);
    }

    .staff-edit-page .password-note {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        margin-top: 0.75rem;
        padding: 0.75rem 0.85rem;
        border: 1px solid rgba(255, 193, 7, 0.2);
        border-radius: 12px;
        background: rgba(255, 193, 7, 0.06);
        color: #c7c7c7;
        font-size: 0.78rem;
        line-height: 1.55;
    }

    .staff-edit-page .password-note i {
        margin-top: 2px;
        color: var(--staff-yellow);
    }

    .staff-edit-page .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--staff-border);
    }

    .staff-edit-page .btn-yellow,
    .staff-edit-page .btn-dark-outline {
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

    .staff-edit-page .btn-yellow {
        border: 1px solid var(--staff-yellow);
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
    }

    .staff-edit-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .staff-edit-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .staff-edit-page .btn-dark-outline:hover {
        border-color: var(--staff-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--staff-yellow);
    }

    @media (max-width: 767.98px) {
        .staff-edit-page .page-hero,
        .staff-edit-page .card-body-custom {
            padding: 1rem;
        }

        .staff-edit-page .hero-avatar {
            width: 56px;
            height: 56px;
            border-radius: 17px;
        }

        .staff-edit-page .hero-title {
            font-size: 1.25rem;
        }

        .staff-edit-page .form-actions {
            flex-direction: column-reverse;
        }

        .staff-edit-page .form-actions .btn {
            width: 100%;
        }
    }
</style>

@php
    $fullName = trim(
        ($user->first_name ?? '') . ' ' .
        ($user->last_name ?? '')
    );

    if ($fullName === '') {
        $fullName = $user->name ?? '-';
    }

    $avatarLetter = mb_strtoupper(
        mb_substr(
            $user->first_name
                ?? $user->name
                ?? 'U',
            0,
            1
        )
    );
@endphp

<div class="staff-edit-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="page-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="hero-avatar">
                            @if ($user->image)
                                <img
                                    src="{{ asset('storage/' . $user->image) }}"
                                    alt="{{ $fullName }}"
                                >
                            @else
                                {{ $avatarLetter }}
                            @endif
                        </span>

                        <div>
                            <h2 class="hero-title">
                                แก้ไขข้อมูลพนักงาน
                            </h2>

                            <p class="hero-subtitle">
                                {{ $fullName }}
                            </p>

                            <span class="role-badge">
                                <i class="bi bi-person-badge-fill"></i>

                                {{ $user->role === 'admin'
                                    ? 'แอดมิน'
                                    : 'พนักงานขาย' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="main-card">
                <div class="card-header-custom">
                    <h3 class="section-title">
                        <i class="bi bi-person-gear"></i>
                        ข้อมูลพนักงาน
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form
                        id="staffEditForm"
                        action="{{ route('staff.allupdate', $user->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
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
                                        <i class="bi bi-person-fill input-icon"></i>

                                        <input
                                            type="text"
                                            name="first_name"
                                            id="first_name"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            value="{{ old('first_name', $user->first_name) }}"
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
                                            value="{{ old('last_name', $user->last_name) }}"
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
                                        <i class="bi bi-envelope-fill input-icon"></i>

                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}"
                                            placeholder="example@gmail.com"
                                            autocomplete="email"
                                            required
                                        >
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
                                        <i class="bi bi-telephone-fill input-icon"></i>

                                        <input
                                            type="tel"
                                            name="phone"
                                            id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $user->phone) }}"
                                            placeholder="0812345678"
                                            maxlength="10"
                                            inputmode="numeric"
                                            autocomplete="tel"
                                            required
                                        >
                                    </div>

                                    <div
                                        id="phoneError"
                                        class="error-text d-none"
                                    >
                                        เบอร์โทรศัพท์ต้องขึ้นต้นด้วย 0 และมี 9-10 หลัก
                                    </div>

                                    @error('phone')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="role" class="form-label">
                                        ตำแหน่ง
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-person-badge-fill input-icon"></i>

                                        <select
                                            name="role"
                                            id="role"
                                            class="form-select @error('role') is-invalid @enderror"
                                            required
                                        >
                                            <option
                                                value="admin"
                                                {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}
                                            >
                                                แอดมิน
                                            </option>

                                            <option
                                                value="staff"
                                                {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}
                                            >
                                                พนักงานขาย
                                            </option>
                                        </select>
                                    </div>

                                    @error('role')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
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

                                    @error('image')
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
                                        <i class="bi bi-geo-alt-fill input-icon textarea-icon"></i>

                                        <textarea
                                            name="address"
                                            id="address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            rows="4"
                                            placeholder="กรอกที่อยู่ของพนักงาน"
                                            autocomplete="street-address"
                                            required
                                        >{{ old('address', $user->address) }}</textarea>
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
                                    <label class="form-label">
                                        ตัวอย่างรูปโปรไฟล์
                                    </label>

                                    <div class="image-preview-box">
                                        <div class="image-preview">
                                            @if ($user->image)
                                                <img
                                                    id="profileImagePreview"
                                                    src="{{ asset('storage/' . $user->image) }}"
                                                    alt="{{ $fullName }}"
                                                >
                                            @else
                                                <span id="profilePlaceholder">
                                                    {{ $avatarLetter }}
                                                </span>

                                                <img
                                                    id="profileImagePreview"
                                                    src=""
                                                    alt="รูปโปรไฟล์"
                                                    class="d-none"
                                                >
                                            @endif
                                        </div>

                                        <div>
                                            <div class="text-white fw-bold mb-1">
                                                รูปปัจจุบัน
                                            </div>

                                            <span class="field-help mt-0">
                                                เมื่อเลือกรูปใหม่ ตัวอย่างจะเปลี่ยนทันที
                                                รองรับ JPG, PNG และ WEBP ขนาดไม่เกิน 2 MB
                                            </span>
                                        </div>
                                    </div>
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
                                        <i class="bi bi-lock-fill input-icon"></i>

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

                                    <div class="password-note">
                                        <i class="bi bi-shield-lock-fill"></i>

                                        <span>
                                            อย่างน้อย 8 ตัว มีตัวพิมพ์ใหญ่
                                            ตัวพิมพ์เล็ก และอักขระพิเศษ
                                        </span>
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
                                href="{{ route('staff.index') }}"
                                class="btn btn-dark-outline"
                            >
                                <i class="bi bi-arrow-left"></i>
                                กลับหน้ารายชื่อ
                            </a>

                            <button
                                type="submit"
                                class="btn btn-yellow"
                                id="saveStaffButton"
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
    const form = document.getElementById('staffEditForm');
    const saveButton = document.getElementById('saveStaffButton');

    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('profileImagePreview');
    const profilePlaceholder =
        document.getElementById('profilePlaceholder');

    const passwordInput = document.getElementById('password');
    const confirmationInput =
        document.getElementById('password_confirmation');

    const passwordError = document.getElementById('passwordError');
    const confirmationError =
        document.getElementById('confirmPasswordError');

    document
        .querySelectorAll('.password-toggle')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = button.dataset.passwordTarget;
                const input = document.getElementById(targetId);
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

    phoneInput?.addEventListener('input', function () {
        phoneInput.value = phoneInput.value
            .replace(/\D/g, '')
            .slice(0, 10);

        phoneInput.classList.remove('is-invalid');
        phoneError?.classList.add('d-none');
    });

    imageInput?.addEventListener('change', function () {
        const file = imageInput.files?.[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            imageInput.value = '';

            Swal.fire({
                icon: 'error',
                title: 'ประเภทไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์ JPG, PNG หรือ WEBP',
                background: '#171717',
                color: '#ffffff',
                confirmButtonColor: '#ffc107',
                customClass: {
                    confirmButton: 'text-dark fw-bold'
                }
            });

            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            imageInput.value = '';

            Swal.fire({
                icon: 'warning',
                title: 'ไฟล์มีขนาดใหญ่เกินไป',
                text: 'รูปภาพต้องมีขนาดไม่เกิน 2 MB',
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
            imagePreview.classList.remove('d-none');
            profilePlaceholder?.classList.add('d-none');
        });

        reader.readAsDataURL(file);
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();

        let valid = true;

        const phonePattern = /^0[0-9]{8,9}$/;
        const password = passwordInput.value;
        const confirmation = confirmationInput.value;

        if (!phonePattern.test(phoneInput.value.trim())) {
            phoneInput.classList.add('is-invalid');
            phoneError.classList.remove('d-none');
            valid = false;
        } else {
            phoneInput.classList.remove('is-invalid');
            phoneError.classList.add('d-none');
        }

        if (password.length > 0) {
            const validPassword =
                password.length >= 8 &&
                /[A-Z]/.test(password) &&
                /[a-z]/.test(password) &&
                /[^A-Za-z0-9]/.test(password);

            if (!validPassword) {
                passwordInput.classList.add('is-invalid');
                passwordError.classList.remove('d-none');
                valid = false;
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordError.classList.add('d-none');
            }

            if (password !== confirmation) {
                confirmationInput.classList.add('is-invalid');
                confirmationError.classList.remove('d-none');
                valid = false;
            } else {
                confirmationInput.classList.remove('is-invalid');
                confirmationError.classList.add('d-none');
            }
        } else {
            passwordInput.classList.remove('is-invalid');
            confirmationInput.classList.remove('is-invalid');
            passwordError.classList.add('d-none');
            confirmationError.classList.add('d-none');
        }

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            valid = false;
        }

        if (!valid) {
            form.querySelector('.is-invalid, :invalid')?.focus();
            return;
        }

        const submitForm = function () {
            saveButton.disabled = true;

            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                กำลังบันทึก
            `;

            form.submit();
        };

        if (typeof Swal === 'undefined') {
            if (window.confirm('ยืนยันการแก้ไขข้อมูลพนักงานหรือไม่?')) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการแก้ไขข้อมูล?',
            text: 'กรุณาตรวจสอบข้อมูลพนักงานก่อนบันทึก',
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