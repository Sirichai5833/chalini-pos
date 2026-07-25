<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<style>
    /* 1. ล็อกหน้าจอถาวร ห้ามสกรอลเด็ดขาด */
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #141414 0%, #0b0b0b 100%);
    }

    /* 2. Container หลัก จัดกึ่งกลางเต็มจอ 100vh */
    .fullscreen-wrapper {
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 20px;
        box-sizing: border-box;
    }

    /* แสงไฟนีออนสีเหลืองเรืองแสงที่พื้นหลัง */
    .bg-glow-yellow {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255, 204, 0, 0.05) 0%, rgba(0,0,0,0) 70%);
        top: -10%;
        right: -10%;
        z-index: 1;
    }
    .bg-glow-yellow-2 {
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 204, 0, 0.03) 0%, rgba(0,0,0,0) 70%);
        bottom: -5%;
        left: -5%;
        z-index: 1;
    }

    /* 3. การ์ดกู้คืนรหัสผ่าน (Dark Glassmorphism) */
    .reset-box {
        background: rgba(22, 22, 22, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 204, 0, 0.2); 
        border-radius: 24px;
        padding: 45px 40px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        z-index: 2;
        box-sizing: border-box;
        position: relative;
    }

    /* แถบสีเหลืองด้านบนสุดของการ์ด */
    .reset-box::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: #ffcc00;
        border-radius: 24px 24px 0 0;
    }

    /* 4. หัวข้อตัวอักษร */
    .reset-box h3 {
        color: #ffffff;
        font-weight: 800;
        font-size: 26px;
        text-align: center;
        margin: 10px 0 8px 0;
        letter-spacing: 0.5px;
    }
    .reset-box p {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        text-align: center;
        margin: 0 0 30px 0;
        line-height: 1.5;
    }

    /* 5. ฟอร์มและช่องกรอกข้อมูล */
    .input-group-custom {
        margin-bottom: 24px;
        position: relative;
    }
    .input-group-custom label {
        display: block;
        color: #ffcc00;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    .input-field {
        width: 100%;
        padding: 14px 18px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    .input-field::placeholder {
        color: rgba(255, 255, 255, 0.25);
    }
    .input-field:focus {
        outline: none;
        border-color: #ffcc00;
        box-shadow: 0 0 10px rgba(255, 204, 0, 0.2);
        background: rgba(0, 0, 0, 0.6);
    }

    /* 6. ปุ่มส่ง OTP สีเหลืองสะดุดตา */
    .btn-submit {
        width: 100%;
        padding: 15px;
        background: #ffcc00;
        border: none;
        border-radius: 12px;
        color: #121212;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .btn-submit:hover {
        background: #ffe066;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 204, 0, 0.3);
    }

    /* 7. ลิงก์ย้อนกลับด้านล่าง */
    .back-to-login {
        text-align: center;
        margin-top: 25px;
    }
    .back-to-login a {
        color: rgba(255, 255, 255, 0.4);
        text-decoration: none;
        font-size: 14px;
        transition: color 0.2s;
    }
    .back-to-login a:hover {
        color: #ffcc00;
        text-decoration: underline;
    }

    /* 8. การแจ้งเตือนสถานะความสำเร็จและข้อผิดพลาด */
    .alert-success-custom {
        background: rgba(76, 175, 80, 0.15);
        border: 1px solid rgba(76, 175, 80, 0.3);
        color: #81c784;
        padding: 14px;
        border-radius: 12px;
        font-size: 13px;
        text-align: center;
        margin-bottom: 20px;
    }
    .invalid-feedback-custom {
        color: #ff8a80;
        font-size: 13px;
        margin-top: 8px;
        display: block;
    }

    /* 9. Responsive บนมือถือ */
    @media (max-width: 480px) {
        .reset-box {
            padding: 35px 24px 30px 24px;
            border-radius: 20px;
        }
        .reset-box h3 { font-size: 22px; }
        .reset-box p { margin-bottom: 20px; font-size: 13px; }
        .input-field { padding: 12px 16px; }
    }
</style>

<!-- โหลด FontAwesome สำหรับไอคอนลูกศรและเครื่องหมายต่าง ๆ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="fullscreen-wrapper">
    <!-- แสงออร่าพื้นหลังแบบนิ่ง -->
    <div class="bg-glow-yellow"></div>
    <div class="bg-glow-yellow-2"></div>

    <!-- กล่องกู้คืนรหัสผ่าน -->
    <div class="reset-box">
        <h3>กู้คืนรหัสผ่าน</h3>
        <p>กรอกอีเมลของคุณเพื่อรับรหัส OTP สำหรับรีเซ็ตรหัสผ่าน<br><span style="color: #ffcc00;">(หมดอายุใน 10 นาที)</span></p>

        <!-- แสดงสถานะเมื่อส่งสำเร็จ -->
        @if (session('status'))
            <div class="alert-success-custom">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- ช่องกรอกอีเมล -->
            <div class="input-group-custom">
                <label for="email">อีเมล</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="input-field @error('email') is-invalid @enderror"
                       placeholder="username@email.com" required autofocus>

                <!-- แสดงข้อความแจ้งเตือนเมื่ออีเมลไม่ถูกต้อง -->
                @error('email')
                    <span class="invalid-feedback-custom" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- ปุ่มกดส่ง -->
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i>
                <span>ส่งรหัส OTP</span>
            </button>
        </form>

        <!-- ลิงก์กลับหน้า Login -->
        <div class="back-to-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-chevron-left" style="font-size: 11px; margin-right: 4px;"></i> กลับไปหน้าเข้าสู่ระบบ
            </a>
        </div>
    </div>
</div>
@endsection