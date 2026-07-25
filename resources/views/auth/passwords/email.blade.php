@extends('layouts.app')

@section('content')
<style>
    /* บังคับเต็มจอ ห้ามสกรอล */
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

    /* กล่องการ์ดดีไซน์เดียวกับหน้า Login */
    .reset-box {
        background: rgba(22, 22, 22, 0.8);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 204, 0, 0.2);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        position: relative;
    }

    /* แถบคาดสีเหลือง */
    .reset-box::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 5px; background: #ffcc00;
        border-radius: 24px 24px 0 0;
    }

    .reset-header h3 { color: #ffffff; font-weight: 800; text-align: center; margin-bottom: 10px; }
    .reset-header p { color: rgba(255, 255, 255, 0.5); font-size: 14px; text-align: center; margin-bottom: 30px; }

    .form-label { color: #ffcc00; font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; }
    .form-control {
        width: 100%; padding: 14px 18px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        box-sizing: border-box;
    }
    .form-control:focus { outline: none; border-color: #ffcc00; }

    .btn-submit {
        width: 100%; padding: 15px;
        background: #ffcc00; border: none;
        border-radius: 12px; color: #121212;
        font-weight: 700; cursor: pointer;
        transition: 0.3s; margin-top: 20px;
    }
    .btn-submit:hover { background: #ffe066; }

    .alert-success { background: rgba(76, 175, 80, 0.2); color: #81c784; padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 20px; border: 1px solid rgba(76, 175, 80, 0.3); }
    .invalid-feedback { color: #ff8a80; font-size: 12px; margin-top: 5px; }
</style>

<div class="fullscreen-wrapper">
    <div class="reset-box">
        <div class="reset-header">
            <h3>กู้คืนรหัสผ่าน</h3>
            <p>กรอกอีเมลเพื่อรับลิงก์สำหรับรีเซ็ตรหัสผ่านใหม่</p>
        </div>

        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">อีเมลของคุณ</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="username@email.com">

                @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                {{ __('ส่งลิงก์รีเซ็ตรหัสผ่าน') }}
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ url('/') }}" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 13px;">กลับไปหน้าเข้าสู่ระบบ</a>
        </div>
    </div>
</div>
@endsection