@extends('layouts.online')

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
    :root {
        --profile-yellow: #ffd43b;
        --profile-yellow-dark: #d8ad00;
        --profile-black: #090909;
        --profile-surface: #131313;
        --profile-card: #191919;
        --profile-soft: #222;
        --profile-border: rgba(255, 212, 59, .22);
        --profile-text: #f7f7f7;
        --profile-muted: #a7a7a7;
        --profile-danger: #ef4444;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--profile-black);
        color: var(--profile-text);
    }

    .profile-page {
        min-height: calc(100vh - 70px);
        padding: 1.5rem 0 4rem;
    }

    .profile-wrap {
        max-width: 760px;
        margin: 0 auto;
    }

    .profile-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid var(--profile-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 88% 20%, rgba(255, 212, 59, .20), transparent 25%),
            linear-gradient(135deg, #090909 0%, #171717 62%, #2a2200 100%);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .35);
    }

    .profile-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        bottom: -100px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .10);
    }

    .profile-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .profile-icon {
        width: 70px;
        height: 70px;
        display: grid;
        place-items: center;
        flex: none;
        border: 1px solid var(--profile-border);
        border-radius: 22px;
        background: rgba(255, 212, 59, .10);
        color: var(--profile-yellow);
        font-size: 1.8rem;
    }

    .profile-title {
        margin: 0;
        color: var(--profile-yellow);
        font-size: clamp(1.45rem, 4vw, 2rem);
        font-weight: 900;
    }

    .profile-subtitle {
        margin: .4rem 0 0;
        color: #c8c8c8;
        font-size: .88rem;
    }

    .profile-card {
        border: 1px solid var(--profile-border) !important;
        border-radius: 22px !important;
        background: linear-gradient(180deg, #191919, #111) !important;
        color: var(--profile-text);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .34) !important;
    }

    .profile-card .card-body {
        padding: clamp(1.15rem, 3vw, 2rem);
    }

    .form-label {
        color: #e9e9e9;
        font-size: .88rem;
        font-weight: 800;
    }

    .form-label i {
        color: var(--profile-yellow);
    }

    .form-control {
        min-height: 48px;
        border: 1px solid #373737;
        border-radius: 13px;
        background: #111;
        color: #fff;
        box-shadow: none;
    }

    .form-control::placeholder {
        color: #747474;
    }

    .form-control:focus {
        border-color: var(--profile-yellow);
        background: #151515;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .form-control:-webkit-autofill,
    .form-control:-webkit-autofill:hover,
    .form-control:-webkit-autofill:focus {
        -webkit-text-fill-color: #fff;
        -webkit-box-shadow: 0 0 0 1000px #151515 inset;
        transition: background-color 9999s ease-in-out 0s;
    }

    .form-text {
        color: var(--profile-muted);
        font-size: .78rem;
    }

    .text-danger {
        color: #ff7f7f !important;
        font-size: .82rem;
    }

    .password-note {
        padding: .75rem .85rem;
        border: 1px solid rgba(255, 212, 59, .18);
        border-radius: 12px;
        background: rgba(255, 212, 59, .06);
        color: #d6d6d6;
        font-size: .78rem;
    }

    .btn-save {
        min-height: 46px;
        border: 1px solid var(--profile-yellow);
        border-radius: 13px;
        background: linear-gradient(135deg, #ffe56b, var(--profile-yellow));
        color: #111;
        font-weight: 850;
        box-shadow: 0 8px 20px rgba(255, 212, 59, .15);
        transition: .2s ease;
    }

    .btn-save:hover {
        border-color: #ffeb8a;
        background: linear-gradient(135deg, #fff08f, #ffc800);
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 212, 59, .22);
    }

    .btn-cancel {
        min-height: 46px;
        border: 1px solid #3b3b3b;
        border-radius: 13px;
        background: #202020;
        color: #d7d7d7;
        font-weight: 800;
        transition: .2s ease;
    }

    .btn-cancel:hover {
        border-color: #555;
        background: #2b2b2b;
        color: #fff;
        transform: translateY(-2px);
    }

    .profile-actions {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        margin-top: 1.5rem;
    }

    @media (max-width: 575.98px) {
        .profile-page {
            padding-top: .8rem;
        }

        .profile-hero {
            padding: 1.15rem;
            border-radius: 19px;
        }

        .profile-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            font-size: 1.35rem;
        }

        .profile-card {
            border-radius: 18px !important;
        }

        .profile-actions {
            flex-direction: column;
        }

        .profile-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="profile-page">
    <div class="container">
        <div class="profile-wrap">

            <section class="profile-hero">
                <div class="profile-hero-content">


                    <div>
                        <h1 class="profile-title">แก้ไขข้อมูลส่วนตัว</h1>
                        <p class="profile-subtitle">
                            อัปเดตชื่อ เบอร์โทร อีเมล และรหัสผ่านของคุณ
                        </p>
                    </div>
                </div>
            </section>

            <div class="card profile-card">
                <div class="card-body">

                    <form
                        action="{{ route('online.update', $member->id) }}"
                        method="POST"
                        id="profileForm"
                        novalidate
                    >
                        @method('PUT')
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    <i class="bi bi-person-fill me-1"></i>
                                    ชื่อ
                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    id="first_name"
                                    class="form-control"
                                    value="{{ old('first_name', $member->first_name) }}"
                                    placeholder="กรอกชื่อ"
                                    required
                                >

                                @error('first_name')
                                    <div class="text-danger mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">
                                    <i class="bi bi-person-fill me-1"></i>
                                    นามสกุล
                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    class="form-control"
                                    value="{{ old('last_name', $member->last_name) }}"
                                    placeholder="กรอกนามสกุล"
                                    required
                                >

                                @error('last_name')
                                    <div class="text-danger mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="bi bi-telephone-fill me-1"></i>
                                    เบอร์โทรศัพท์
                                </label>

                                <input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    class="form-control"
                                    value="{{ old('phone', $member->phone) }}"
                                    placeholder="กรอกเบอร์โทรศัพท์"
                                    maxlength="10"
                                    inputmode="numeric"
                                    required
                                >

                                @error('phone')
                                    <div class="text-danger mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope-fill me-1"></i>
                                    อีเมล
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    value="{{ old('email', $member->email) }}"
                                    placeholder="example@email.com"
                                    required
                                >

                                <div id="emailError" class="text-danger mt-2 d-none">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    กรุณากรอกอีเมลให้ถูกต้อง
                                </div>

                                @error('email')
                                    <div class="text-danger mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    รหัสผ่านใหม่
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยน"
                                    minlength="8"
                                >

                                @error('password')
                                    <div class="text-danger mt-2">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-shield-lock-fill me-1"></i>
                                    ยืนยันรหัสผ่าน
                                </label>

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control"
                                    placeholder="ยืนยันรหัสผ่านใหม่"
                                    minlength="8"
                                >

                                <div id="confirmPasswordError" class="text-danger mt-2 d-none">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    รหัสผ่านไม่ตรงกัน
                                </div>
                            </div>
                        </div>

                        <div class="password-note mt-3">
                            <i class="bi bi-info-circle-fill me-1 text-warning"></i>
                            รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร มีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว
                            และมีอักขระพิเศษอย่างน้อย 1 ตัว
                        </div>

                        <div id="passwordError" class="text-danger mt-2 d-none">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            รหัสผ่านต้องมีอย่างน้อย 6 ตัว มีตัวพิมพ์ใหญ่ และอักขระพิเศษ
                        </div>

                        <div class="profile-actions">
                            <button type="submit" class="btn btn-save px-4">
                                <i class="bi bi-save-fill me-1"></i>
                                บันทึกการเปลี่ยนแปลง
                            </button>

                            <a href="{{ route('online.index') }}" class="btn btn-cancel px-4">
                                <i class="bi bi-x-circle me-1"></i>
                                ยกเลิก
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', function (event) {
    const email = document.getElementById('email');
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;

    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    let valid = true;

    if (!email.checkValidity()) {
        emailError.classList.remove('d-none');
        valid = false;
    } else {
        emailError.classList.add('d-none');
    }

    if (password.length > 0) {
        const hasMinimumLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasSpecialCharacter = /[^A-Za-z0-9]/.test(password);

        if (!hasMinimumLength || !hasUppercase || !hasSpecialCharacter) {
            passwordError.classList.remove('d-none');
            valid = false;
        } else {
            passwordError.classList.add('d-none');
        }

        if (password !== confirmPassword) {
            confirmPasswordError.classList.remove('d-none');
            valid = false;
        } else {
            confirmPasswordError.classList.add('d-none');
        }
    } else {
        passwordError.classList.add('d-none');
        confirmPasswordError.classList.add('d-none');
    }

    if (!valid) {
        event.preventDefault();
    }
});
</script>

@endsection