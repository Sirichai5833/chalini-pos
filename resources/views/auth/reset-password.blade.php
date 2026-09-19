<!-- resources/views/auth/reset-password.blade.php -->
@extends('layouts.app')

@section('content')
<style>
    :root {
        --reset-yellow: #ffc107;
        --reset-yellow-soft: #ffe27a;
        --reset-bg: #070707;
        --reset-panel: #151515;
        --reset-panel-soft: #1e1e1e;
        --reset-border: #353535;
        --reset-text: #f7f7f7;
        --reset-muted: #9d9d9d;
        --reset-danger: #ff8d97;
        --reset-success: #79dfa6;
    }

    html,
    body {
        min-height: 100%;
        margin: 0;
        background:
            radial-gradient(circle at 88% 12%, rgba(255, 193, 7, .10), transparent 24%),
            radial-gradient(circle at 8% 88%, rgba(255, 193, 7, .055), transparent 26%),
            var(--reset-bg);
        color: var(--reset-text);
        font-family: Inter, "Segoe UI", Tahoma, sans-serif;
    }

    .reset-password-page {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 1.25rem;
    }

    .reset-password-shell {
        width: 100%;
        max-width: 980px;
        display: grid;
        grid-template-columns: .9fr 1.1fr;
        overflow: hidden;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 28px;
        background: #111;
        box-shadow: 0 30px 90px rgba(0, 0, 0, .5);
    }

    .reset-password-aside {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 610px;
        padding: 2rem;
        background:
            radial-gradient(circle at 75% 15%, rgba(255, 193, 7, .22), transparent 30%),
            linear-gradient(145deg, #202020, #090909);
    }

    .reset-password-aside::after {
        content: "";
        position: absolute;
        right: -120px;
        bottom: -120px;
        width: 320px;
        height: 320px;
        border: 1px solid rgba(255, 193, 7, .16);
        border-radius: 50%;
        box-shadow:
            0 0 0 40px rgba(255, 193, 7, .025),
            0 0 0 80px rgba(255, 193, 7, .018);
    }

    .brand-chip {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .65rem;
        width: fit-content;
        padding: .55rem .8rem;
        border: 1px solid rgba(255, 193, 7, .28);
        border-radius: 999px;
        background: rgba(255, 193, 7, .07);
        color: var(--reset-yellow);
        font-size: .75rem;
        font-weight: 850;
        letter-spacing: .08em;
    }

    .aside-content {
        position: relative;
        z-index: 2;
    }

    .aside-icon {
        width: 78px;
        height: 78px;
        display: grid;
        place-items: center;
        margin-bottom: 1.2rem;
        border-radius: 24px;
        background: linear-gradient(
            135deg,
            var(--reset-yellow),
            var(--reset-yellow-soft)
        );
        color: #111;
        font-size: 2rem;
        box-shadow: 0 14px 32px rgba(255, 193, 7, .20);
    }

    .aside-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.8rem, 3vw, 2.45rem);
        font-weight: 950;
        line-height: 1.15;
    }

    .aside-text {
        max-width: 360px;
        margin: 1rem 0 0;
        color: #aaa;
        font-size: .9rem;
        line-height: 1.7;
    }

    .aside-tip {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        padding: .9rem;
        border: 1px solid rgba(255, 193, 7, .18);
        border-radius: 15px;
        background: rgba(255, 193, 7, .055);
        color: #c7c7c7;
        font-size: .77rem;
        line-height: 1.55;
    }

    .aside-tip i {
        margin-top: 2px;
        color: var(--reset-yellow);
    }

    .reset-password-form-panel {
        padding: 2rem;
        background: linear-gradient(180deg, #181818, #111);
    }

    .form-heading {
        margin-bottom: 1.3rem;
    }

    .form-kicker {
        margin-bottom: .35rem;
        color: var(--reset-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .12em;
    }

    .form-title {
        margin: 0;
        color: #fff;
        font-size: 1.55rem;
        font-weight: 900;
    }

    .form-description {
        margin: .45rem 0 0;
        color: var(--reset-muted);
        font-size: .84rem;
        line-height: 1.55;
    }

    .alert-error {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-bottom: 1rem;
        padding: .85rem .95rem;
        border: 1px solid rgba(220, 53, 69, .35);
        border-radius: 14px;
        background: rgba(220, 53, 69, .11);
        color: var(--reset-danger);
        font-size: .79rem;
        line-height: 1.5;
    }

    .field-group {
        margin-bottom: 1rem;
    }

    .field-label {
        display: block;
        margin-bottom: .5rem;
        color: #ededed;
        font-size: .82rem;
        font-weight: 800;
    }

    .password-wrap {
        position: relative;
    }

    .field-icon {
        position: absolute;
        top: 50%;
        left: .95rem;
        transform: translateY(-50%);
        color: var(--reset-yellow);
        pointer-events: none;
    }

    .password-input {
        width: 100%;
        min-height: 52px;
        padding: .8rem 3.2rem .8rem 2.8rem;
        border: 1px solid #444;
        border-radius: 13px;
        background: #0d0d0d;
        color: #fff;
        font-size: .92rem;
        outline: none;
        transition: .2s ease;
    }

    .password-input::placeholder {
        color: #666;
    }

    .password-input:focus {
        border-color: var(--reset-yellow);
        background: #090909;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .11);
    }

    .password-input.is-invalid {
        border-color: #dc3545;
    }

    .password-input.is-valid {
        border-color: #198754;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: .7rem;
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        transform: translateY(-50%);
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #888;
        transition: .2s ease;
    }

    .toggle-password:hover {
        background: rgba(255, 193, 7, .08);
        color: var(--reset-yellow);
    }

    .password-strength {
        margin-top: .7rem;
    }

    .strength-track {
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #303030;
    }

    .strength-bar {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: #dc3545;
        transition: .25s ease;
    }

    .strength-meta {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        margin-top: .4rem;
        color: #858585;
        font-size: .72rem;
    }

    .requirements-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
        margin: .9rem 0 1rem;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        gap: .45rem;
        padding: .58rem .65rem;
        border: 1px solid #353535;
        border-radius: 11px;
        background: #111;
        color: #8d8d8d;
        font-size: .72rem;
        transition: .2s ease;
    }

    .requirement-item.valid {
        border-color: rgba(25, 135, 84, .35);
        background: rgba(25, 135, 84, .07);
        color: var(--reset-success);
    }

    .requirement-item.invalid {
        border-color: rgba(220, 53, 69, .25);
        color: var(--reset-danger);
    }

    .error-text {
        display: none;
        margin-top: .45rem;
        color: var(--reset-danger);
        font-size: .77rem;
    }

    .error-text.show {
        display: block;
    }

    .submit-button {
        width: 100%;
        min-height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        margin-top: .3rem;
        border: 1px solid var(--reset-yellow);
        border-radius: 13px;
        background: linear-gradient(
            135deg,
            var(--reset-yellow),
            var(--reset-yellow-soft)
        );
        color: #111;
        font-size: .9rem;
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

    .back-link {
        margin-top: 1rem;
        text-align: center;
    }

    .back-link a {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #919191;
        font-size: .8rem;
        text-decoration: none;
        transition: .2s ease;
    }

    .back-link a:hover {
        color: var(--reset-yellow);
    }

    @media (max-width: 820px) {
        .reset-password-shell {
            max-width: 520px;
            grid-template-columns: 1fr;
        }

        .reset-password-aside {
            min-height: auto;
            padding: 1.45rem;
        }

        .aside-content {
            margin: 2rem 0 1.25rem;
        }

        .aside-title {
            font-size: 1.65rem;
        }

        .reset-password-form-panel {
            padding: 1.45rem;
        }
    }

    @media (max-width: 520px) {
        .reset-password-page {
            padding: .75rem;
        }

        .reset-password-shell {
            border-radius: 21px;
        }

        .requirements-grid {
            grid-template-columns: 1fr;
        }

        .reset-password-aside,
        .reset-password-form-panel {
            padding: 1.15rem;
        }
    }
</style>

<div class="reset-password-page">
    <div class="reset-password-shell">
        <aside class="reset-password-aside">
            <div class="brand-chip">
                <i class="bi bi-shield-lock-fill"></i>
                SECURE RESET
            </div>

            <div class="aside-content">
                <div class="aside-icon">
                    <i class="bi bi-key-fill"></i>
                </div>

                <h1 class="aside-title">
                    สร้างรหัสผ่านใหม่<br>
                    ที่ปลอดภัยกว่าเดิม
                </h1>

                <p class="aside-text">
                    ใช้รหัสผ่านที่ไม่ซ้ำกับบัญชีอื่น
                    และหลีกเลี่ยงข้อมูลที่คาดเดาได้ง่าย
                </p>
            </div>

            <div class="aside-tip">
                <i class="bi bi-lightbulb-fill"></i>
                <span>
                    แนะนำให้ใช้ตัวอักษร ตัวเลข และอักขระพิเศษร่วมกัน
                    แม้ระบบจะไม่บังคับตัวเลขก็ตาม
                </span>
            </div>
        </aside>

        <section class="reset-password-form-panel">
            <div class="form-heading">
                <div class="form-kicker">
                    PASSWORD RECOVERY
                </div>

                <h2 class="form-title">
                    ตั้งรหัสผ่านใหม่
                </h2>

                <p class="form-description">
                    รหัสผ่านต้องมีมากกว่า 6 ตัวอักษร
                    พร้อมตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และอักขระพิเศษ
                </p>
            </div>

            @if ($errors->any())
                <div class="alert-error" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form
                id="resetPasswordForm"
                method="POST"
                action="{{ route('password.update') }}"
                novalidate
            >
                @csrf

                <input
                    type="hidden"
                    name="email"
                    value="{{ old('email', $email) }}"
                >

                @isset($token)
                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token }}"
                    >
                @endisset

                <div class="field-group">
                    <label for="password" class="field-label">
                        รหัสผ่านใหม่
                    </label>

                    <div class="password-wrap">
                        <i class="bi bi-lock-fill field-icon"></i>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="password-input @error('password') is-invalid @enderror"
                            placeholder="กรอกรหัสผ่านใหม่"
                            minlength="6"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="password"
                            aria-label="แสดงหรือซ่อนรหัสผ่าน"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="password-strength">
                        <div class="strength-track">
                            <div
                                class="strength-bar"
                                id="strengthBar"
                            ></div>
                        </div>

                        <div class="strength-meta">
                            <span>ความแข็งแรงของรหัสผ่าน</span>
                            <strong id="strengthText">ยังไม่ระบุ</strong>
                        </div>
                    </div>

                    <div class="requirements-grid">
                        <div class="requirement-item" data-rule="length">
                            <i class="bi bi-circle"></i>
                            อย่างน้อย 6 ตัวอักษร
                        </div>

                        <div class="requirement-item" data-rule="uppercase">
                            <i class="bi bi-circle"></i>
                            ตัวพิมพ์ใหญ่ 1 ตัว
                        </div>

                        <div class="requirement-item" data-rule="lowercase">
                            <i class="bi bi-circle"></i>
                            ตัวพิมพ์เล็ก 1 ตัว
                        </div>

                        <div class="requirement-item" data-rule="special">
                            <i class="bi bi-circle"></i>
                            อักขระพิเศษ 1 ตัว
                        </div>

                        <div class="requirement-item" data-rule="spaces">
                            <i class="bi bi-circle"></i>
                            ไม่มีช่องว่าง
                        </div>
                    </div>

                    <div
                        id="passwordError"
                        class="error-text"
                        role="alert"
                    ></div>

                    @error('password')
                        <div class="error-text show" role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="field-group">
                    <label
                        for="password_confirmation"
                        class="field-label"
                    >
                        ยืนยันรหัสผ่านใหม่
                    </label>

                    <div class="password-wrap">
                        <i class="bi bi-lock-check field-icon"></i>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="password-input"
                            placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                            minlength="6"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="password_confirmation"
                            aria-label="แสดงหรือซ่อนรหัสผ่าน"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div
                        id="confirmationError"
                        class="error-text"
                        role="alert"
                    ></div>
                </div>

                <button
                    type="submit"
                    class="submit-button"
                    id="submitButton"
                >
                    <i class="bi bi-shield-check"></i>
                    <span>บันทึกรหัสผ่านใหม่</span>
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="bi bi-arrow-left"></i>
                    กลับไปหน้าเข้าสู่ระบบ
                </a>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetPasswordForm');
    const passwordInput = document.getElementById('password');
    const confirmationInput =
        document.getElementById('password_confirmation');

    const passwordError =
        document.getElementById('passwordError');

    const confirmationError =
        document.getElementById('confirmationError');

    const submitButton =
        document.getElementById('submitButton');

    const strengthBar =
        document.getElementById('strengthBar');

    const strengthText =
        document.getElementById('strengthText');

    let submitting = false;

    const rules = {
        length: value => value.length >= 7,
        uppercase: value => /[A-Z]/.test(value),
        lowercase: value => /[a-z]/.test(value),
        special: value => /[^A-Za-z0-9]/.test(value),
        spaces: value => !/\s/.test(value)
    };

    function updateRequirement(name, valid) {
        const item = document.querySelector(
            `[data-rule="${name}"]`
        );

        if (!item) {
            return;
        }

        const icon = item.querySelector('i');
        const hasValue = passwordInput.value.length > 0;

        item.classList.toggle('valid', valid);
        item.classList.toggle(
            'invalid',
            !valid && hasValue
        );

        icon.className = valid
            ? 'bi bi-check-circle-fill'
            : (
                hasValue
                    ? 'bi bi-x-circle-fill'
                    : 'bi bi-circle'
            );
    }

    function updateStrength(results) {
        const score = Object.values(results)
            .filter(Boolean)
            .length;

        const percentage = score * 20;
        strengthBar.style.width = `${percentage}%`;

        let label = 'ยังไม่ระบุ';
        let color = '#dc3545';

        if (passwordInput.value.length === 0) {
            percentage === 0;
            label = 'ยังไม่ระบุ';
            color = '#555';
        } else if (score <= 2) {
            label = 'อ่อน';
            color = '#dc3545';
        } else if (score <= 4) {
            label = 'ปานกลาง';
            color = '#ffc107';
        } else {
            label = 'แข็งแรง';
            color = '#198754';
        }

        strengthBar.style.background = color;
        strengthText.textContent = label;
        strengthText.style.color = color;
    }

    function validatePassword() {
        const value = passwordInput.value;

        const results = {
            length: rules.length(value),
            uppercase: rules.uppercase(value),
            lowercase: rules.lowercase(value),
            special: rules.special(value),
            spaces: rules.spaces(value)
        };

        Object.keys(results).forEach(function (rule) {
            updateRequirement(rule, results[rule]);
        });

        updateStrength(results);

        const valid = Object.values(results).every(Boolean);
        let message = '';

        if (!value) {
            message = 'กรุณากรอกรหัสผ่านใหม่';
        } else if (!valid) {
            message =
                'รหัสผ่านยังไม่ครบตามเงื่อนไขที่กำหนด';
        }

        passwordInput.classList.toggle(
            'is-invalid',
            message !== ''
        );

        passwordInput.classList.toggle(
            'is-valid',
            valid && value.length > 0
        );

        passwordError.textContent = message;
        passwordError.classList.toggle(
            'show',
            message !== ''
        );

        return valid;
    }

    function validateConfirmation() {
        const value = confirmationInput.value;
        let message = '';

        if (!value) {
            message = 'กรุณายืนยันรหัสผ่าน';
        } else if (value !== passwordInput.value) {
            message =
                'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
        }

        confirmationInput.classList.toggle(
            'is-invalid',
            message !== ''
        );

        confirmationInput.classList.toggle(
            'is-valid',
            message === '' && value.length > 0
        );

        confirmationError.textContent = message;
        confirmationError.classList.toggle(
            'show',
            message !== ''
        );

        return message === '';
    }

    passwordInput.addEventListener('input', function () {
        passwordInput.value =
            passwordInput.value.slice(0, 72);

        validatePassword();

        if (confirmationInput.value.length > 0) {
            validateConfirmation();
        }
    });

    confirmationInput.addEventListener(
        'input',
        function () {
            confirmationInput.value =
                confirmationInput.value.slice(0, 72);

            validateConfirmation();
        }
    );

    document
        .querySelectorAll('.toggle-password')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(
                    button.dataset.target
                );

                const icon = button.querySelector('i');

                if (!input) {
                    return;
                }

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';

                icon.classList.toggle('bi-eye', !show);
                icon.classList.toggle(
                    'bi-eye-slash',
                    show
                );
            });
        });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (submitting) {
            return;
        }

        const passwordValid = validatePassword();
        const confirmationValid =
            validateConfirmation();

        if (!passwordValid) {
            passwordInput.focus();
            return;
        }

        if (!confirmationValid) {
            confirmationInput.focus();
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
                <span>กำลังบันทึกรหัสผ่าน</span>
            `;

            form.submit();
        };

        if (typeof Swal === 'undefined') {
            submitForm();
            return;
        }

        Swal.fire({
            title: 'ยืนยันการตั้งรหัสผ่านใหม่?',
            text: 'หลังบันทึกแล้วให้ใช้รหัสผ่านใหม่นี้เข้าสู่ระบบ',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
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