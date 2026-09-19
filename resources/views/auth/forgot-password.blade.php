<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<style>
    :root {
        --forgot-yellow: #ffc107;
        --forgot-yellow-soft: #ffe27a;
        --forgot-bg: #070707;
        --forgot-panel: #151515;
        --forgot-panel-soft: #1f1f1f;
        --forgot-border: #373737;
        --forgot-text: #f8f8f8;
        --forgot-muted: #9d9d9d;
        --forgot-danger: #ff8d97;
        --forgot-success: #79dfa6;
    }

    html,
    body {
        min-height: 100%;
        margin: 0;
        background:
            radial-gradient(circle at 88% 10%, rgba(255, 193, 7, .11), transparent 24%),
            radial-gradient(circle at 10% 92%, rgba(255, 193, 7, .055), transparent 26%),
            var(--forgot-bg);
        color: var(--forgot-text);
        font-family: Inter, "Segoe UI", Tahoma, sans-serif;
    }

    .forgot-password-page {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 1.25rem;
    }

    .forgot-password-shell {
        width: 100%;
        max-width: 980px;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        overflow: hidden;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 28px;
        background: #111;
        box-shadow: 0 30px 90px rgba(0, 0, 0, .5);
    }

    .forgot-form-panel {
        padding: 2.2rem;
        background: linear-gradient(180deg, #181818, #111);
    }

    .form-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1rem;
        padding: .42rem .72rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 999px;
        background: rgba(255, 193, 7, .06);
        color: var(--forgot-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .1em;
    }

    .form-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 950;
        line-height: 1.15;
    }

    .form-description {
        max-width: 460px;
        margin: .85rem 0 1.5rem;
        color: var(--forgot-muted);
        font-size: .88rem;
        line-height: 1.7;
    }

    .status-alert {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border: 1px solid rgba(25, 135, 84, .36);
        border-radius: 14px;
        background: rgba(25, 135, 84, .1);
        color: var(--forgot-success);
        font-size: .8rem;
        line-height: 1.5;
    }

    .field-group {
        margin-bottom: 1rem;
    }

    .field-label {
        display: block;
        margin-bottom: .55rem;
        color: #ededed;
        font-size: .82rem;
        font-weight: 800;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        top: 50%;
        left: .95rem;
        transform: translateY(-50%);
        color: var(--forgot-yellow);
        pointer-events: none;
    }

    .email-input {
        width: 100%;
        min-height: 54px;
        padding: .85rem 1rem .85rem 2.85rem;
        border: 1px solid #444;
        border-radius: 14px;
        background: #0c0c0c;
        color: #fff;
        font-size: .92rem;
        outline: none;
        transition: .2s ease;
    }

    .email-input::placeholder {
        color: #666;
    }

    .email-input:focus {
        border-color: var(--forgot-yellow);
        background: #090909;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .11);
    }

    .email-input.is-invalid {
        border-color: #dc3545;
    }

    .field-help {
        display: flex;
        align-items: flex-start;
        gap: .45rem;
        margin-top: .55rem;
        color: #818181;
        font-size: .74rem;
        line-height: 1.5;
    }

    .field-help i {
        margin-top: 2px;
        color: var(--forgot-yellow);
    }

    .error-text {
        display: none;
        margin-top: .5rem;
        color: var(--forgot-danger);
        font-size: .78rem;
    }

    .error-text.show {
        display: block;
    }

    .submit-button {
        width: 100%;
        min-height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        margin-top: .4rem;
        border: 1px solid var(--forgot-yellow);
        border-radius: 14px;
        background: linear-gradient(
            135deg,
            var(--forgot-yellow),
            var(--forgot-yellow-soft)
        );
        color: #111;
        font-size: .92rem;
        font-weight: 900;
        transition: .2s ease;
    }

    .submit-button:hover:not(:disabled) {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(255, 193, 7, .20);
    }

    .submit-button:disabled {
        cursor: not-allowed;
        opacity: .6;
    }

    .divider {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 1.3rem 0;
        color: #666;
        font-size: .72rem;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #303030;
    }

    .back-link {
        text-align: center;
    }

    .back-link a {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: #919191;
        font-size: .8rem;
        text-decoration: none;
        transition: .2s ease;
    }

    .back-link a:hover {
        color: var(--forgot-yellow);
    }

    .forgot-info-panel {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 590px;
        padding: 2rem;
        background:
            radial-gradient(circle at 75% 15%, rgba(255, 193, 7, .22), transparent 30%),
            linear-gradient(145deg, #202020, #090909);
    }

    .forgot-info-panel::after {
        content: "";
        position: absolute;
        right: -120px;
        bottom: -120px;
        width: 330px;
        height: 330px;
        border: 1px solid rgba(255, 193, 7, .15);
        border-radius: 50%;
        box-shadow:
            0 0 0 42px rgba(255, 193, 7, .024),
            0 0 0 84px rgba(255, 193, 7, .016);
    }

    .info-chip {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        width: fit-content;
        padding: .52rem .8rem;
        border: 1px solid rgba(255, 193, 7, .26);
        border-radius: 999px;
        background: rgba(255, 193, 7, .07);
        color: var(--forgot-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .09em;
    }

    .info-content {
        position: relative;
        z-index: 2;
    }

    .info-icon {
        width: 82px;
        height: 82px;
        display: grid;
        place-items: center;
        margin-bottom: 1.25rem;
        border-radius: 25px;
        background: linear-gradient(
            135deg,
            var(--forgot-yellow),
            var(--forgot-yellow-soft)
        );
        color: #111;
        font-size: 2rem;
        box-shadow: 0 15px 34px rgba(255, 193, 7, .2);
    }

    .info-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.8rem, 3vw, 2.4rem);
        font-weight: 950;
        line-height: 1.15;
    }

    .info-text {
        max-width: 340px;
        margin: 1rem 0 0;
        color: #aaa;
        font-size: .88rem;
        line-height: 1.7;
    }

    .step-list {
        position: relative;
        z-index: 2;
        display: grid;
        gap: .75rem;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .8rem .9rem;
        border: 1px solid rgba(255, 193, 7, .14);
        border-radius: 14px;
        background: rgba(255, 193, 7, .045);
        color: #c8c8c8;
        font-size: .78rem;
    }

    .step-number {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: var(--forgot-yellow);
        color: #111;
        font-weight: 900;
    }

    @media (max-width: 820px) {
        .forgot-password-shell {
            max-width: 520px;
            grid-template-columns: 1fr;
        }

        .forgot-info-panel {
            min-height: auto;
            padding: 1.45rem;
            order: -1;
        }

        .info-content {
            margin: 2rem 0 1.25rem;
        }

        .info-title {
            font-size: 1.65rem;
        }

        .forgot-form-panel {
            padding: 1.45rem;
        }
    }

    @media (max-width: 520px) {
        .forgot-password-page {
            padding: .75rem;
        }

        .forgot-password-shell {
            border-radius: 21px;
        }

        .forgot-info-panel,
        .forgot-form-panel {
            padding: 1.1rem;
        }
    }
</style>

<div class="forgot-password-page">
    <div class="forgot-password-shell">
        <section class="forgot-form-panel">
            <div class="form-badge">
                <i class="bi bi-envelope-paper-fill"></i>
                PASSWORD RECOVERY
            </div>

            <h1 class="form-title">
                กู้คืนรหัสผ่าน
            </h1>

            <p class="form-description">
                กรอกอีเมลที่ลงทะเบียนไว้ในระบบ
                ระบบจะส่งรหัส OTP สำหรับตั้งรหัสผ่านใหม่ให้คุณ
            </p>

            @if (session('status'))
                <div class="status-alert" role="status">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form
                id="forgotPasswordForm"
                method="POST"
                action="{{ route('password.email') }}"
                novalidate
            >
                @csrf

                <div class="field-group">
                    <label for="email" class="field-label">
                        อีเมลที่ลงทะเบียน
                    </label>

                    <div class="input-wrap">
                        <i class="bi bi-envelope-fill input-icon"></i>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="email-input @error('email') is-invalid @enderror"
                            placeholder="name@example.com"
                            maxlength="190"
                            autocomplete="email"
                            inputmode="email"
                            spellcheck="false"
                            required
                            autofocus
                        >
                    </div>

                    <div class="field-help">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>
                            กรุณาใช้อีเมลจริงที่เคยสมัครในระบบ
                            และตรวจสอบการสะกดให้ถูกต้อง
                        </span>
                    </div>

                    <div
                        id="emailClientError"
                        class="error-text"
                        role="alert"
                    ></div>

                    @error('email')
                        <div class="error-text show" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="submit-button"
                    id="submitButton"
                >
                    <i class="bi bi-send-fill"></i>
                    <span>ส่งรหัส OTP</span>
                </button>
            </form>

            <div class="divider">
                หรือ
            </div>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="bi bi-arrow-left"></i>
                    กลับไปหน้าเข้าสู่ระบบ
                </a>
            </div>
        </section>

        <aside class="forgot-info-panel">
            <div class="info-content">
                <div class="info-chip">
                    <i class="bi bi-shield-lock-fill"></i>
                    ปลอดภัยและเชื่อถือได้
                </div>

                <h2 class="info-title">
                    รับ OTP<br>
                    อย่างปลอดภัย
                </h2>

                <p class="info-text">
                    รหัส OTP จะมีอายุ 10 นาที
                    และใช้ได้เพียงครั้งเดียวเพื่อปกป้องบัญชีของคุณ
                </p>
            </div>

            <div class="step-list">
                <div class="step-item">
                    <span class="step-number">1</span>
                    กรอกอีเมลที่ลงทะเบียน
                </div>

                <div class="step-item">
                    <span class="step-number">2</span>
                    ตรวจสอบรหัส OTP ในอีเมล
                </div>

                <div class="step-item">
                    <span class="step-number">3</span>
                    ตั้งรหัสผ่านใหม่
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('forgotPasswordForm');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailClientError');
    const submitButton = document.getElementById('submitButton');

    let submitting = false;

    const blockedPatterns = [
        /^test@/i,
        /^admin@admin/i,
        /^aaa+@/i,
        /^123+@/i,
        /^qwerty@/i
    ];

    function normalizeEmail(value) {
        return value
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '');
    }

    function validateEmail() {
        const email = normalizeEmail(emailInput.value);
        emailInput.value = email;

        let message = '';

        if (!email) {
            message = 'กรุณากรอกอีเมล';
        } else if (email.length > 190) {
            message = 'อีเมลยาวเกินไป';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            message = 'รูปแบบอีเมลไม่ถูกต้อง';
        } else if (email.includes('..')) {
            message = 'อีเมลต้องไม่มีจุดซ้ำกัน';
        } else if (
            blockedPatterns.some(function (pattern) {
                return pattern.test(email);
            })
        ) {
            message = 'กรุณากรอกอีเมลที่ใช้งานจริง';
        }

        emailInput.classList.toggle(
            'is-invalid',
            message !== ''
        );

        emailError.textContent = message;
        emailError.classList.toggle(
            'show',
            message !== ''
        );

        return message === '';
    }

    emailInput.addEventListener('input', function () {
        emailInput.value = emailInput.value
            .replace(/\s/g, '')
            .slice(0, 190);

        emailInput.classList.remove('is-invalid');
        emailError.classList.remove('show');
    });

    emailInput.addEventListener('blur', validateEmail);

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (submitting || !validateEmail()) {
            emailInput.focus();
            return;
        }

        const submitForm = function () {
            submitting = true;
            submitButton.disabled = true;

            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm"
                    aria-hidden="true"
                ></span>
                <span>กำลังส่งรหัส OTP</span>
            `;

            form.submit();
        };

        if (typeof Swal === 'undefined') {
            submitForm();
            return;
        }

        Swal.fire({
            title: 'ยืนยันอีเมล?',
            html: `
                ระบบจะส่งรหัส OTP ไปที่<br>
                <strong style="color:#ffc107">
                    ${emailInput.value}
                </strong>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ส่งรหัส OTP',
            cancelButtonText: 'แก้ไขอีเมล',
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