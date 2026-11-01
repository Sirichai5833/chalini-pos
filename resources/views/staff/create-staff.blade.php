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

@if (session('generated_password'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') {
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'เพิ่มพนักงานสำเร็จ',
                html: `
                    <p class="mb-2">
                        รหัสผ่านเริ่มต้น
                    </p>

                    <div
                        style="
                            margin: 12px 0;
                            padding: 12px;
                            border: 1px solid rgba(255,193,7,.4);
                            border-radius: 12px;
                            background: rgba(255,193,7,.09);
                            color: #ffc107;
                            font-size: 1.35rem;
                            font-weight: 800;
                            letter-spacing: 1px;
                        "
                    >
                        {{ session('generated_password') }}
                    </div>

                    <small>
                        กรุณาแจ้งให้พนักงานเปลี่ยนรหัสผ่านหลังเข้าสู่ระบบครั้งแรก
                    </small>
                `,
                confirmButtonText: 'รับทราบ',
                background: '#171717',
                color: '#ffffff',
                confirmButtonColor: '#ffc107',
                customClass: {
                    confirmButton: 'text-dark fw-bold'
                }
            });
        });
    </script>
@endif

<style>
    .staff-create-page {
        --staff-panel: #171717;
        --staff-panel-soft: #202020;
        --staff-yellow: #ffc107;
        --staff-yellow-soft: #ffe27a;
        --staff-border: #3b3b3b;
        --staff-text: #f7f7f7;
        --staff-muted: #aaa;
        color: var(--staff-text);
    }

    .staff-create-page .page-shell {
        max-width: 1050px;
        margin: 0 auto;
    }

    .staff-create-page .page-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.28);
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

    .staff-create-page .page-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .staff-create-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .staff-create-page .hero-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 20px;
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
        font-size: 1.65rem;
        box-shadow: 0 11px 27px rgba(255, 193, 7, 0.22);
    }

    .staff-create-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .staff-create-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--staff-muted);
        font-size: 0.92rem;
    }

    .staff-create-page .main-card {
        overflow: hidden;
        border: 1px solid var(--staff-border);
        border-radius: 22px;
        background: var(--staff-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.36);
    }

    .staff-create-page .card-header-custom {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--staff-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .staff-create-page .section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: var(--staff-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .staff-create-page .section-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, 0.12);
    }

    .staff-create-page .card-body-custom {
        padding: 1.5rem;
    }

    .staff-create-page .field-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--staff-border);
        border-radius: 16px;
        background: var(--staff-panel-soft);
        transition: 0.2s ease;
    }

    .staff-create-page .field-card:hover {
        border-color: rgba(255, 193, 7, 0.55);
        transform: translateY(-1px);
    }

    .staff-create-page .form-label {
        margin-bottom: 0.5rem;
        color: #e8e8e8;
        font-size: 0.88rem;
        font-weight: 750;
    }

    .staff-create-page .required-star {
        color: var(--staff-yellow);
    }

    .staff-create-page .optional-text {
        color: var(--staff-muted);
        font-size: 0.76rem;
        font-weight: 500;
    }

    .staff-create-page .input-icon-group {
        position: relative;
    }

    .staff-create-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.9rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--staff-yellow);
        pointer-events: none;
    }

    .staff-create-page .textarea-icon {
        top: 1rem;
        transform: none;
    }

    .staff-create-page .form-control,
    .staff-create-page .form-select {
        min-height: 49px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .staff-create-page .input-icon-group .form-control,
    .staff-create-page .input-icon-group .form-select {
        padding-left: 2.65rem;
    }

    .staff-create-page textarea.form-control {
        min-height: 125px;
        padding-top: 0.8rem;
        resize: vertical;
    }

    .staff-create-page .form-control::placeholder {
        color: #707070;
    }

    .staff-create-page .form-control:focus,
    .staff-create-page .form-select:focus {
        border-color: var(--staff-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .staff-create-page .form-select option {
        background: #111;
        color: #fff;
    }

    .staff-create-page .form-control.is-invalid,
    .staff-create-page .form-select.is-invalid {
        border-color: #dc3545;
    }

    .staff-create-page .error-text {
        margin-top: 0.45rem;
        color: #ff818c;
        font-size: 0.8rem;
    }

    .staff-create-page .field-help {
        display: block;
        margin-top: 0.45rem;
        color: var(--staff-muted);
        font-size: 0.76rem;
        line-height: 1.5;
    }

    .staff-create-page input[type="file"] {
        padding-left: 0.8rem;
    }

    .staff-create-page input[type="file"]::file-selector-button {
        margin-right: 0.8rem;
        border: 0;
        border-radius: 8px;
        background: var(--staff-yellow);
        color: #111;
        font-weight: 750;
    }

    .staff-create-page .image-preview-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.9rem;
        padding: 0.8rem;
        border: 1px dashed #4a4a4a;
        border-radius: 14px;
        background: #131313;
    }

    .staff-create-page .image-preview {
        width: 78px;
        height: 78px;
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

    .staff-create-page .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .staff-create-page .security-note {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        margin-top: 1.25rem;
        padding: 0.9rem 1rem;
        border: 1px solid rgba(255, 193, 7, 0.22);
        border-radius: 14px;
        background: rgba(255, 193, 7, 0.06);
        color: #cfcfcf;
        font-size: 0.8rem;
        line-height: 1.55;
    }

    .staff-create-page .security-note i {
        margin-top: 2px;
        color: var(--staff-yellow);
        font-size: 1rem;
    }

    .staff-create-page .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--staff-border);
    }

    .staff-create-page .btn-yellow,
    .staff-create-page .btn-dark-outline {
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

    .staff-create-page .btn-yellow {
        border: 1px solid var(--staff-yellow);
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
    }

    .staff-create-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .staff-create-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .staff-create-page .btn-dark-outline:hover {
        border-color: var(--staff-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--staff-yellow);
    }

    @media (max-width: 767.98px) {
        .staff-create-page .page-hero,
        .staff-create-page .card-body-custom {
            padding: 1rem;
        }

        .staff-create-page .hero-icon {
            width: 54px;
            height: 54px;
            border-radius: 17px;
        }

        .staff-create-page .hero-title {
            font-size: 1.25rem;
        }

        .staff-create-page .form-actions {
            flex-direction: column-reverse;
        }

        .staff-create-page .form-actions .btn {
            width: 100%;
        }

        .staff-create-page .image-preview-box {
            align-items: flex-start;
        }
    }
</style>

<div class="staff-create-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="page-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="hero-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>

                        <div>
                            <h2 class="hero-title">
                                เพิ่มพนักงานใหม่
                            </h2>

                            <p class="hero-subtitle">
                                กรอกข้อมูลพนักงาน ตำแหน่ง และข้อมูลสำหรับติดต่อ
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="main-card">
                <div class="card-header-custom">
                    <h3 class="section-title">
                        <i class="bi bi-person-vcard-fill"></i>
                        ข้อมูลพนักงาน
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form
                        id="staffForm"
                        action="{{ route('staff.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

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
                value="{{ old('first_name') }}"
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
                value="{{ old('last_name') }}"
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
                                            value="{{ old('email') }}"
                                            placeholder="somchai@example.com"
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
                                        <i class="bi bi-telephone-fill input-icon"></i>

                                        <input
                                            type="tel"
                                            name="phone"
                                            id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}"
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
                                        เบอร์โทรศัพท์ต้องเป็นตัวเลข 9-10 หลัก
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
                                            <option value="">
                                                เลือกตำแหน่ง
                                            </option>


                                            <option
                                                value="admin"
                                                {{ old('role') === 'admin' ? 'selected' : '' }}
                                            >
                                                แอดมิน
                                            </option>


                                            <option
                                                value="staff"
                                                {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}
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
                                            placeholder="กรอกบ้านเลขที่ ถนน ตำบล/แขวง อำเภอ/เขต จังหวัด และรหัสไปรษณีย์"
                                            rows="4"
                                            autocomplete="street-address"
                                            required
                                        >{{ old('address') }}</textarea>
                                    </div>

                                    <small class="field-help">
                                        ใช้สำหรับข้อมูลพนักงานและการติดต่อในกรณีฉุกเฉิน
                                    </small>

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
                                            ไม่บังคับ
                                        </span>
                                    </label>

                                    <input
                                        type="file"
                                        name="image"
                                        id="image"
                                        class="form-control @error('image') is-invalid @enderror"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        aria-describedby="imageHelp"
                                    >

                                    <div class="image-preview-box">
                                        <div class="image-preview">
                                            <i
                                                class="bi bi-person-fill"
                                                id="defaultProfileIcon"
                                            ></i>

                                            <img
                                                id="profileImagePreview"
                                                src=""
                                                alt="ตัวอย่างรูปโปรไฟล์"
                                                class="d-none"
                                            >
                                        </div>

                                        <div>
                                            <div class="fw-bold text-white mb-1">
                                                ตัวอย่างรูปโปรไฟล์
                                            </div>

                                            <div
                                                class="field-help mt-0"
                                                id="imageHelp"
                                            >
                                                รองรับ JPG, JPEG, PNG และ WEBP<br>
                                                ขนาดไฟล์ไม่เกิน 2 MB
                                            </div>
                                        </div>
                                    </div>

                                    @error('image')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="security-note">
                            <i class="bi bi-shield-lock-fill"></i>

                            <span>
                                ระบบจะสร้างรหัสผ่านเริ่มต้นให้อัตโนมัติ
                                และควรให้พนักงานเปลี่ยนรหัสผ่านหลังเข้าสู่ระบบครั้งแรก
                            </span>
                        </div>

                        <div class="form-actions">
                            <a
                                href="{{ route('staff.index') }}"
                                class="btn btn-dark-outline"
                            >
                                <i class="bi bi-arrow-left"></i>
                                กลับหน้าพนักงาน
                            </a>

                            <button
                                type="submit"
                                class="btn btn-yellow"
                                id="saveStaffButton"
                            >
                                <i class="bi bi-floppy-fill"></i>
                                บันทึกข้อมูลพนักงาน
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
    const form = document.getElementById('staffForm');
    const saveButton = document.getElementById('saveStaffButton');

    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');

    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('profileImagePreview');
    const defaultProfileIcon =
        document.getElementById('defaultProfileIcon');

    phoneInput?.addEventListener('input', function () {
        phoneInput.value = phoneInput.value
            .replace(/\D/g, '')
            .slice(0, 10);

        phoneInput.classList.remove('is-invalid');
        phoneError?.classList.add('d-none');
    });

    emailInput?.addEventListener('input', function () {
        emailInput.classList.remove('is-invalid');
        emailError?.classList.add('d-none');
    });

    imageInput?.addEventListener('change', function () {
        const file = imageInput.files?.[0];

        if (!file) {
            imagePreview?.classList.add('d-none');
            defaultProfileIcon?.classList.remove('d-none');
            return;
        }

        if (!file.type.startsWith('image/')) {
            imageInput.value = '';

            if (typeof Swal !== 'undefined') {
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
            }

            return;
        }

        const maxSize = 2 * 1024 * 1024;

        if (file.size > maxSize) {
            imageInput.value = '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'กรุณาเลือกรูปภาพขนาดไม่เกิน 2 MB',
                    background: '#171717',
                    color: '#ffffff',
                    confirmButtonColor: '#ffc107',
                    customClass: {
                        confirmButton: 'text-dark fw-bold'
                    }
                });
            }

            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', function (event) {
            imagePreview.src = event.target.result;
            imagePreview.classList.remove('d-none');
            defaultProfileIcon?.classList.add('d-none');
        });

        reader.readAsDataURL(file);
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();

        let valid = true;

        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const phonePattern =
            /^0[0-9]{8,9}$/;

        if (!emailPattern.test(emailInput.value.trim())) {
            emailInput.classList.add('is-invalid');
            emailError.classList.remove('d-none');
            valid = false;
        } else {
            emailInput.classList.remove('is-invalid');
            emailError.classList.add('d-none');
        }

        if (!phonePattern.test(phoneInput.value.trim())) {
            phoneInput.classList.add('is-invalid');
            phoneError.classList.remove('d-none');
            valid = false;
        } else {
            phoneInput.classList.remove('is-invalid');
            phoneError.classList.add('d-none');
        }

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            valid = false;
        }

        if (!valid) {
            form.querySelector(
                '.is-invalid, :invalid'
            )?.focus();

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
            if (
                window.confirm(
                    'ยืนยันการเพิ่มพนักงานคนนี้หรือไม่?'
                )
            ) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการเพิ่มพนักงาน?',
            text: 'กรุณาตรวจสอบข้อมูลก่อนบันทึก',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'เพิ่มพนักงาน',
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
            if (!result.isConfirmed) {
                return;
            }

            Swal.fire({
                title: 'กำลังบันทึกข้อมูล',
                text: 'กรุณาอย่าปิดหน้าต่างนี้',
                allowOutsideClick: false,
                allowEscapeKey: false,
                background: '#171717',
                color: '#ffffff',
                didOpen: function () {
                    Swal.showLoading();
                }
            });

            submitForm();
        });
    });
});
</script>

@endsection
