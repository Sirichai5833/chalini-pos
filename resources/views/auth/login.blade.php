@extends('layouts.app')

@section('content')
<style>
    :root {
        --login-yellow: #ffc107;
        --login-yellow-soft: #ffe27a;
        --login-black: #070707;
        --login-panel: #151515;
        --login-panel-soft: #202020;
        --login-border: rgba(255, 193, 7, .22);
        --login-text: #f8f8f8;
        --login-muted: #9f9f9f;
        --login-danger: #ff8e98;
    }

    html,
    body {
        min-height: 100%;
        margin: 0;
        background:
            radial-gradient(
                circle at 12% 10%,
                rgba(255, 193, 7, .12),
                transparent 28%
            ),
            radial-gradient(
                circle at 90% 88%,
                rgba(255, 193, 7, .07),
                transparent 26%
            ),
            var(--login-black);
        color: var(--login-text);
        font-family: Inter, "Segoe UI", Tahoma, sans-serif;
    }

    .login-page {
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: 1.25rem;
    }

    .login-shell {
        width: 100%;
        max-width: 980px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        overflow: hidden;
        border: 1px solid var(--login-border);
        border-radius: 28px;
        background: #111;
        box-shadow: 0 30px 90px rgba(0, 0, 0, .5);
    }

    .welcome-panel {
        position: relative;
        overflow: hidden;
        min-height: 600px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2rem;
        background:
            radial-gradient(
                circle at 78% 18%,
                rgba(255, 193, 7, .22),
                transparent 30%
            ),
            linear-gradient(145deg, #202020, #090909);
    }

    .welcome-panel::after {
        content: "";
        position: absolute;
        right: -120px;
        bottom: -120px;
        width: 340px;
        height: 340px;
        border: 1px solid rgba(255, 193, 7, .15);
        border-radius: 50%;
        box-shadow:
            0 0 0 45px rgba(255, 193, 7, .024),
            0 0 0 90px rgba(255, 193, 7, .016);
    }

    .brand-chip {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        width: fit-content;
        padding: .5rem .8rem;
        border: 1px solid rgba(255, 193, 7, .27);
        border-radius: 999px;
        background: rgba(255, 193, 7, .07);
        color: var(--login-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .09em;
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-icon {
        width: 82px;
        height: 82px;
        display: grid;
        place-items: center;
        margin-bottom: 1.25rem;
        border-radius: 25px;
        background: linear-gradient(
            135deg,
            var(--login-yellow),
            var(--login-yellow-soft)
        );
        color: #111;
        font-size: 2rem;
        box-shadow: 0 15px 34px rgba(255, 193, 7, .2);
    }

    .welcome-title {
        max-width: 470px;
        margin: 0;
        color: #fff;
        font-size: clamp(2rem, 4.3vw, 3.8rem);
        font-weight: 950;
        line-height: 1.08;
        letter-spacing: -.03em;
    }

    .welcome-title span {
        display: block;
        margin-top: .35rem;
        color: var(--login-yellow);
    }

    .welcome-note {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        padding: .85rem .95rem;
        border: 1px solid rgba(255, 193, 7, .16);
        border-radius: 14px;
        background: rgba(255, 193, 7, .045);
        color: #c8c8c8;
        font-size: .77rem;
        line-height: 1.55;
    }

    .welcome-note i {
        margin-top: 2px;
        color: var(--login-yellow);
    }

    .service-register-card {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: .85rem;
        margin-top: 1rem;
        padding: 1rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 16px;
        background:
            linear-gradient(
                135deg,
                rgba(255, 193, 7, .10),
                rgba(255, 193, 7, .03)
            );
    }

    .service-register-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 14px;
        background: var(--login-yellow);
        color: #111;
        font-size: 1.15rem;
    }

    .service-register-card strong {
        display: block;
        color: #fff;
        font-size: .85rem;
    }

    .service-register-card small {
        display: block;
        margin-top: .2rem;
        color: #aaa;
        font-size: .74rem;
        line-height: 1.5;
    }

    .form-panel {
        display: flex;
        align-items: center;
        padding: 2rem;
        background: linear-gradient(180deg, #181818, #111);
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
    }

    .login-header {
        margin-bottom: 1.5rem;
    }

    .login-kicker {
        margin-bottom: .35rem;
        color: var(--login-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .12em;
    }

    .login-title {
        margin: 0;
        color: #fff;
        font-size: 1.7rem;
        font-weight: 900;
    }

    .login-description {
        margin: .5rem 0 0;
        color: var(--login-muted);
        font-size: .84rem;
    }

    .login-alert {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        margin-bottom: 1rem;
        padding: .85rem .95rem;
        border: 1px solid rgba(220, 53, 69, .35);
        border-radius: 14px;
        background: rgba(220, 53, 69, .1);
        color: var(--login-danger);
        font-size: .8rem;
        line-height: 1.5;
    }

    .form-field {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        margin-bottom: .5rem;
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
        color: var(--login-yellow);
        pointer-events: none;
    }

    .input-control {
        width: 100%;
        min-height: 52px;
        padding: .8rem 3.1rem .8rem 2.8rem;
        border: 1px solid #444;
        border-radius: 13px;
        background: #0d0d0d;
        color: #fff;
        font-size: .92rem;
        outline: none;
        transition: .2s ease;
    }

    .input-control::placeholder {
        color: #666;
    }

    .input-control:focus {
        border-color: var(--login-yellow);
        background: #090909;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .11);
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
        color: var(--login-yellow);
    }

    .form-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: .3rem 0 1.25rem;
        font-size: .8rem;
    }

    .remember-option {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: var(--login-muted);
        cursor: pointer;
    }

    .remember-option input {
        accent-color: var(--login-yellow);
    }

    .forgot-link {
        color: var(--login-yellow);
        font-weight: 800;
        text-decoration: none;
    }

    .forgot-link:hover {
        color: var(--login-yellow-soft);
        text-decoration: underline;
    }

    .login-button {
        width: 100%;
        min-height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        border: 1px solid var(--login-yellow);
        border-radius: 13px;
        background: linear-gradient(
            135deg,
            var(--login-yellow),
            var(--login-yellow-soft)
        );
        color: #111;
        font-size: .92rem;
        font-weight: 900;
        transition: .2s ease;
    }

    .login-button:hover:not(:disabled) {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(255, 193, 7, .20);
    }

    .login-button:disabled {
        cursor: not-allowed;
        opacity: .65;
    }

    .back-link {
        margin-top: 1rem;
        text-align: center;
    }

    .back-link a {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #909090;
        font-size: .8rem;
        text-decoration: none;
        transition: .2s ease;
    }

    .back-link a:hover {
        color: var(--login-yellow);
    }

    @media (max-width: 820px) {
        .login-shell {
            max-width: 520px;
            grid-template-columns: 1fr;
        }

        .welcome-panel {
            min-height: auto;
            padding: 1.4rem;
        }

        .welcome-content {
            margin: 2rem 0 1.3rem;
        }

        .welcome-title {
            font-size: 2rem;
        }

        .form-panel {
            padding: 1.4rem;
        }
    }

    @media (max-width: 520px) {
        .login-page {
            padding: .75rem;
        }

        .login-shell {
            border-radius: 21px;
        }

        .welcome-panel,
        .form-panel {
            padding: 1.1rem;
        }

        .form-options {
            align-items: flex-start;
            flex-direction: column;
            gap: .7rem;
        }
    }
</style>

<section class="login-page">
    <div class="login-shell">
        <aside class="welcome-panel">
            <div class="brand-chip">
                <i class="bi bi-shop"></i>
                STORE LOGIN
            </div>

            <div class="welcome-content">
             

                <h1 class="welcome-title">
                    
                ยินดีต้อนรับสู่ร้าน {{ $shopSettings->shop_name ?: 'ของเรา' }}
                </h1>

                <div class="service-register-card">

                    <div>
                        <strong>
                            สนใจเข้าใช้บริการ?
                        </strong>

                        <small>
                            กรุณาสมัครสมาชิกได้ที่หน้าร้าน
                            แล้วจึงใช้บัญชีที่ได้รับเพื่อเข้าสู่ระบบ
                        </small>
                    </div>
                </div>
            </div>

            <div>


            </div>
        </aside>

        <section class="form-panel">
            <div class="login-card">
                <div class="login-header">


                    <h2 class="login-title">
                        เข้าสู่ระบบ
                    </h2>

                    <p class="login-description">
                        เข้าสู่ระบบด้วยบัญชีที่ได้รับจากทางร้าน
                    </p>
                </div>

                @if ($errors->any())
                    <div class="login-alert" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ url('/login') }}"
                    id="loginForm"
                >
                    @csrf

                    <div class="form-field">
                        <label for="email" class="form-label">
                            อีเมลผู้ใช้งาน
                        </label>

                        <div class="input-wrap">
                            <i class="bi bi-envelope-fill input-icon"></i>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="input-control"
                                value="{{ old('email') }}"
                                placeholder="name@example.com"
                                maxlength="190"
                                autocomplete="email"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="password" class="form-label">
                            รหัสผ่าน
                        </label>

                        <div class="input-wrap">
                            <i class="bi bi-lock-fill input-icon"></i>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="input-control"
                                placeholder="กรอกรหัสผ่าน"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                id="togglePasswordButton"
                                aria-label="แสดงหรือซ่อนรหัสผ่าน"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-option">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <span>จดจำการเข้าสู่ระบบ</span>
                        </label>

                        <a
                            href="{{ url('/forgot-password') }}"
                            class="forgot-link"
                        >
                            ลืมรหัสผ่าน?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="login-button"
                        id="loginButton"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>เข้าสู่ระบบ</span>
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('online.index') }}">
                        <i class="bi bi-arrow-left"></i>
                        กลับสู่เว็บไซต์
                    </a>
                </div>
            </div>
        </section>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton =
        document.getElementById('togglePasswordButton');

    const passwordInput =
        document.getElementById('password');

    const loginForm =
        document.getElementById('loginForm');

    const loginButton =
        document.getElementById('loginButton');

    toggleButton?.addEventListener('click', function () {
        const show = passwordInput.type === 'password';

        passwordInput.type = show
            ? 'text'
            : 'password';

        const icon = toggleButton.querySelector('i');

        icon.classList.toggle('bi-eye', !show);
        icon.classList.toggle('bi-eye-slash', show);
    });

    loginForm?.addEventListener('submit', function () {
        loginButton.disabled = true;

        loginButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm"
                aria-hidden="true"
            ></span>
            <span>กำลังเข้าสู่ระบบ</span>
        `;
    });
});
</script>
@endsection