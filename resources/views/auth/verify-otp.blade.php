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

    /* 2. การ์ด OTP สไตล์ Dark Glassmorphism */
    .otp-box {
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

    .otp-box::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 5px; background: #ffcc00;
        border-radius: 24px 24px 0 0;
    }

    .otp-header h3 { color: #ffffff; font-weight: 800; text-align: center; margin-bottom: 10px; }
    .otp-header p { color: rgba(255, 255, 255, 0.5); font-size: 14px; text-align: center; margin-bottom: 30px; }

    /* 3. สไตล์ Input สีเหลือง */
    .form-label { color: #ffcc00; font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; }
    .form-control {
        width: 100%; padding: 14px 18px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        letter-spacing: 2px; /* เพิ่มระยะห่างตัวอักษรสำหรับ OTP */
        font-size: 16px;
        box-sizing: border-box;
    }
    .form-control:focus { outline: none; border-color: #ffcc00; background: rgba(0, 0, 0, 0.6); }

    /* 4. ปุ่มยืนยัน */
    .btn-submit {
        width: 100%; padding: 15px;
        background: #ffcc00; border: none;
        border-radius: 12px; color: #121212;
        font-weight: 700; cursor: pointer;
        transition: 0.3s; margin-top: 10px;
    }
    .btn-submit:hover { background: #ffe066; }

    .resend-link { text-align: center; margin-top: 20px; }
    .resend-link a { color: rgba(255, 255, 255, 0.4); text-decoration: none; font-size: 13px; }
    .resend-link a:hover { color: #ffcc00; }
    
    .invalid-feedback { color: #ff8a80; font-size: 12px; margin-top: 5px; display: block; }
</style>

<div class="fullscreen-wrapper">
    <div class="otp-box">
        <div class="otp-header">
            <h3>ยืนยันรหัส OTP</h3>
            <p>กรอกรหัส 6 หลักที่ส่งไปยังอีเมลของคุณ</p>
        </div>

        @if (session('status'))
            <div style="background: rgba(76, 175, 80, 0.2); color: #81c784; padding: 12px; border-radius: 10px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(76, 175, 80, 0.3);">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.check') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email', session('email')) }}"
                       class="form-control" readonly style="opacity: 0.7; cursor: not-allowed;">
            </div>

            <div class="mb-4">
                <label for="otp" class="form-label">รหัส OTP (6 หลัก)</label>
                <input id="otp" type="text" name="otp" value="{{ old('otp') }}"
                       class="form-control @error('otp') is-invalid @enderror" 
                       placeholder="0 0 0 0 0 0" maxlength="6" required autofocus>
                @error('otp')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">ยืนยันรหัส OTP</button>
        </form>

        <div class="resend-link">
            <a href="{{ route('password.request') }}">ไม่ได้รับรหัส? ส่ง OTP ใหม่</a>
        </div>
    </div>
</div>
@endsection