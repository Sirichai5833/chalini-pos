@extends('layouts.app')

@section('content')
<style>
    /* 1. ล็อกหน้าจอถาวร ห้ามสกรอล */
    html, body {
        margin: 0; padding: 0;
        width: 100%; height: 100vh;
        overflow: hidden;
        background: linear-gradient(135deg, #141414 0%, #0b0b0b 100%);
        font-family: 'Inter', sans-serif;
    }

    .fullscreen-wrapper {
        width: 100%; height: 100vh;
        display: flex; align-items: center; justify-content: center;
        padding: 20px; box-sizing: border-box;
    }

    /* 2. การ์ดตั้งรหัสผ่าน สไตล์ Dark Glassmorphism */
    .reset-pass-box {
        background: rgba(22, 22, 22, 0.8);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 204, 0, 0.2);
        border-radius: 24px;
        padding: 45px 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        position: relative;
    }

    .reset-pass-box::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 5px; background: #ffcc00;
        border-radius: 24px 24px 0 0;
    }

    .reset-header h3 { color: #ffffff; font-weight: 800; text-align: center; margin-bottom: 25px; }

    /* 3. สไตล์ Input และปุ่มตารหัส */
    .input-group-custom { margin-bottom: 20px; position: relative; }
    .form-label { color: #ffcc00; font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; }
    
    .password-container { position: relative; }
    .form-control {
        width: 100%; padding: 14px 18px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        box-sizing: border-box;
    }
    .form-control:focus { outline: none; border-color: #ffcc00; background: rgba(0, 0, 0, 0.6); }

    .eye-btn {
        position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: rgba(255, 255, 255, 0.4);
        cursor: pointer; font-size: 18px;
    }
    .eye-btn:hover { color: #ffcc00; }

    /* 4. ปุ่มกด */
    .btn-submit {
        width: 100%; padding: 15px;
        background: #ffcc00; border: none;
        border-radius: 12px; color: #121212;
        font-weight: 700; cursor: pointer;
        transition: 0.3s; margin-top: 10px;
    }
    .btn-submit:hover { background: #ffe066; }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="fullscreen-wrapper">
    <div class="reset-pass-box">
        <div class="reset-header">
            <h3>ตั้งรหัสผ่านใหม่</h3>
        </div>

        @if ($errors->any())
            <div style="background: rgba(211, 47, 47, 0.2); color: #ff8a80; padding: 10px; border-radius: 10px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(211, 47, 47, 0.3); font-size: 13px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <div class="input-group-custom">
                <label for="password" class="form-label">รหัสผ่านใหม่</label>
                <div class="password-container">
                    <input id="password" type="password" name="password" class="form-control" required>
                    <button type="button" class="eye-btn" onclick="togglePass('password', 'icon1')">
                        <i class="fas fa-eye" id="icon1"></i>
                    </button>
                </div>
            </div>

            <div class="input-group-custom">
                <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                <div class="password-container">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                    <button type="button" class="eye-btn" onclick="togglePass('password_confirmation', 'icon2')">
                        <i class="fas fa-eye" id="icon2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">ตั้งรหัสผ่านใหม่</button>
        </form>

        <div style="text-align: center; margin-top: 25px;">
            <a href="{{ route('login') }}" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 13px;">กลับไปหน้าเข้าสู่ระบบ</a>
        </div>
    </div>
</div>

<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
@endsection