
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
    .member-edit-page {
        --edit-black: #0b0b0b;
        --edit-panel: #171717;
        --edit-panel-soft: #202020;
        --edit-yellow: #ffc107;
        --edit-yellow-soft: #ffdc68;
        --edit-border: #3b3b3b;
        --edit-text: #f7f7f7;
        --edit-muted: #a8a8a8;
        color: var(--edit-text);
    }

    .member-edit-page .page-shell {
        max-width: 1050px;
        margin: 0 auto;
    }

    .member-edit-page .edit-hero {
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

    .member-edit-page .edit-hero::after {
        content: "";
        position: absolute;
        right: -75px;
        bottom: -110px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .member-edit-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .member-edit-page .member-avatar {
        width: 66px;
        height: 66px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 20px;
        background: linear-gradient(
            135deg,
            var(--edit-yellow),
            var(--edit-yellow-soft)
        );
        color: #111;
        font-size: 1.7rem;
        font-weight: 900;
        box-shadow: 0 12px 28px rgba(255, 193, 7, 0.23);
    }

    .member-edit-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .member-edit-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--edit-muted);
        font-size: 0.92rem;
    }

    .member-edit-page .room-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.65rem;
        padding: 0.38rem 0.72rem;
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--edit-yellow);
        font-size: 0.78rem;
        font-weight: 800;
    }

    .member-edit-page .main-card {
        overflow: hidden;
        border: 1px solid var(--edit-border);
        border-radius: 22px;
        background: var(--edit-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.36);
    }

    .member-edit-page .card-header-custom {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--edit-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .member-edit-page .section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: var(--edit-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .member-edit-page .section-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, 0.12);
    }

    .member-edit-page .card-body-custom {
        padding: 1.5rem;
    }

    .member-edit-page .field-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--edit-border);
        border-radius: 16px;
        background: var(--edit-panel-soft);
        transition: 0.2s ease;
    }

    .member-edit-page .field-card:hover {
        border-color: rgba(255, 193, 7, 0.55);
        transform: translateY(-1px);
    }

    .member-edit-page .form-label {
        margin-bottom: 0.5rem;
        color: #e7e7e7;
        font-size: 0.88rem;
        font-weight: 750;
    }

    .member-edit-page .required-star {
        color: var(--edit-yellow);
    }

    .member-edit-page .optional-text {
        color: var(--edit-muted);
        font-size: 0.76rem;
        font-weight: 500;
    }

    .member-edit-page .input-icon-group {
        position: relative;
    }

    .member-edit-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.9rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--edit-yellow);
        pointer-events: none;
    }

    .member-edit-page .form-control {
        min-height: 48px;
        padding-left: 2.65rem;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .member-edit-page .form-control::placeholder {
        color: #707070;
    }

    .member-edit-page .form-control:focus {
        border-color: var(--edit-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .member-edit-page .form-control.is-invalid {
        border-color: #dc3545;
    }

    .member-edit-page .error-text {
        margin-top: 0.45rem;
        color: #ff818c;
        font-size: 0.8rem;
    }

    .member-edit-page .password-wrapper {
        position: relative;
    }

    .member-edit-page .password-wrapper .form-control {
        padding-right: 3.2rem;
    }

    .member-edit-page .password-toggle {
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
        transition: 0.18s ease;
    }

    .member-edit-page .password-toggle:hover {
        background: rgba(255, 193, 7, 0.1);
        color: var(--edit-yellow);
    }

    .member-edit-page .password-help {
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

    .member-edit-page .password-help i {
        margin-top: 2px;
        color: var(--edit-yellow);
    }

    .member-edit-page .password-strength {
        display: none;
        margin-top: 0.65rem;
    }

    .member-edit-page .strength-track {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: #353535;
    }

    .member-edit-page .strength-bar {
        width: 0;
        height: 100%;
        border-radius: inherit;
        transition: 0.2s ease;
    }

    .member-edit-page .strength-label {
        display: block;
        margin-top: 0.35rem;
        color: var(--edit-muted);
        font-size: 0.75rem;
    }

    .member-edit-page .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--edit-border);
    }

    .member-edit-page .btn-yellow,
    .member-edit-page .btn-dark-outline {
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

    .member-edit-page .btn-yellow {
        border: 1px solid var(--edit-yellow);
        background: linear-gradient(
            135deg,
            var(--edit-yellow),
            var(--edit-yellow-soft)
        );
        color: #111;
    }

    .member-edit-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .member-edit-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .member-edit-page .btn-dark-outline:hover {
        border-color: var(--edit-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--edit-yellow);
    }

    @media (max-width: 767.98px) {
        .member-edit-page .edit-hero,
        .member-edit-page .card-body-custom {
            padding: 1rem;
        }

        .member-edit-page .member-avatar {
            width: 56px;
            height: 56px;
            border-radius: 17px;
            font-size: 1.4rem;
        }

        .member-edit-page .hero-title {
            font-size: 1.2rem;
        }

        .member-edit-page .form-actions {
            flex-direction: column-reverse;
        }

        .member-edit-page .form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="member-edit-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="edit-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="member-avatar">
                            {{ mb_strtoupper(
                                mb_substr(
                                    $member->first_name
                                        ?: $member->name
                                        ?: 'M',
                                    0,
                                    1
                                )
                            ) }}
                        </span>

                        <div>
                            <h2 class="hero-title">
                                แก้ไขข้อมูลสมาชิก
                            </h2>

                            <p class="hero-subtitle">
                                {{ $member->first_name }}
                                {{ $member->last_name }}
                            </p>

                            <span class="room-badge">
                                <i class="bi bi-door-closed-fill"></i>
                                ห้อง {{ $member->room_number ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="main-card">
                <div class="card-header-custom">
                    <h3 class="section-title">
                        <i class="bi bi-pencil-square"></i>
                        แบบฟอร์มแก้ไขข้อมูล
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form
                        id="memberEditForm"
                        action="{{ route('members.update', $member->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="field-card">
                                    <label for="first_name" class="form-label">
                                        ชื่อ <span class="required-star">*</span>
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
                                        นามสกุล <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-person input-icon"></i>

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
                                            pattern="0[0-9]{8,9}"
                                            inputmode="numeric"
                                            autocomplete="tel"
                                            required
                                        >
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
                                    <label for="email" class="form-label">
                                        อีเมล <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-envelope input-icon"></i>

                                        <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $member->email) }}"
                                            placeholder="member@example.com"
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
                                    <label for="room_number" class="form-label">
                                        เลขห้อง
                                        <span class="required-star">*</span>
                                    </label>

                                    <div class="input-icon-group">
                                        <i class="bi bi-door-open input-icon"></i>

                                        <input
                                            type="text"
                                            name="room_number"
                                            id="room_number"
                                            class="form-control @error('room_number') is-invalid @enderror"
                                            value="{{ old('room_number', $member->room_number) }}"
                                            placeholder="เช่น 101"
                                            inputmode="numeric"
                                            required
                                        >
                                    </div>

                                    @error('room_number')
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
                                            autocomplete="new-password"
                                        >

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            id="togglePassword"
                                            aria-label="แสดงหรือซ่อนรหัสผ่าน"
                                            title="แสดงหรือซ่อนรหัสผ่าน"
                                        >
                                            <i class="bi bi-eye" id="passwordEyeIcon"></i>
                                        </button>
                                    </div>

                                    <div class="password-help">
                                        <i class="bi bi-shield-lock-fill"></i>

                                        <span>
                                            อย่างน้อย 6 ตัว มีตัวพิมพ์ใหญ่ 1 ตัว
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

                                    @error('password')
                                        <div class="error-text">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a
                                href="{{ route('members.show', $member->id) }}"
                                class="btn btn-dark-outline"
                            >
                                <i class="bi bi-x-circle"></i>
                                ยกเลิก
                            </a>

                            <button
                                type="submit"
                                class="btn btn-yellow"
                                id="saveMemberButton"
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
    const form = document.getElementById('memberEditForm');
    const saveButton = document.getElementById('saveMemberButton');

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const passwordEyeIcon = document.getElementById('passwordEyeIcon');

    const passwordStrength = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const strengthLabel = document.getElementById('strengthLabel');

    togglePassword?.addEventListener('click', function () {
        if (!passwordInput) {
            return;
        }

        const isPassword =
            passwordInput.type === 'password';

        passwordInput.type =
            isPassword ? 'text' : 'password';

        passwordEyeIcon?.classList.toggle(
            'bi-eye',
            !isPassword
        );

        passwordEyeIcon?.classList.toggle(
            'bi-eye-slash',
            isPassword
        );
    });

    passwordInput?.addEventListener('input', function () {
        const value = passwordInput.value;

        if (!value) {
            passwordStrength.style.display = 'none';
            strengthBar.style.width = '0';
            return;
        }

        passwordStrength.style.display = 'block';

        let score = 0;

        if (value.length >= 6) {
            score++;
        }

        if (/[A-Z]/.test(value)) {
            score++;
        }

        if (/[a-z]/.test(value)) {
            score++;
        }

        if (/[0-9]/.test(value)) {
            score++;
        }

        if (/[@$!%*?&#\-]/.test(value)) {
            score++;
        }

        const strengthData = [
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

        const selected =
            strengthData[Math.max(0, score - 1)];

        strengthBar.style.width = selected.width;
        strengthBar.style.background = selected.color;

        strengthLabel.textContent = selected.label;
        strengthLabel.style.color = selected.color;
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();

        const submitForm = function () {
            saveButton.disabled = true;

            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                กำลังบันทึก
            `;

            form.submit();
        };

        if (typeof Swal === 'undefined') {
            if (window.confirm('ยืนยันการแก้ไขข้อมูลสมาชิกหรือไม่?')) {
                submitForm();
            }

            return;
        }

        Swal.fire({
            title: 'ยืนยันการแก้ไขข้อมูล?',
            text: 'กรุณาตรวจสอบข้อมูลสมาชิกก่อนบันทึก',
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

