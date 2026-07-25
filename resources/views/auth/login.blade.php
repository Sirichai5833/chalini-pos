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

    /* 2. Container หลัก จัดกึ่งกลางเต็มจอ */
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
        background: radial-gradient(circle, rgba(255, 204, 0, 0.06) 0%, rgba(0,0,0,0) 70%);
        top: -10%;
        right: -10%;
        z-index: 1;
    }
    .bg-glow-yellow-2 {
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 204, 0, 0.04) 0%, rgba(0,0,0,0) 70%);
        bottom: -5%;
        left: -5%;
        z-index: 1;
    }

    /* 3. การ์ดล็อกอินสีดำดุดัน (Dark Glassmorphism) */
    .login-box {
        background: rgba(22, 22, 22, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        /* เส้นขอบเหลืองบางๆ แบบเรียบหรู */
        border: 1px solid rgba(255, 204, 0, 0.2); 
        border-radius: 24px;
        padding: 45px 40px;
        width: 100%;
        max-width: 430px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        z-index: 2;
        box-sizing: border-box;
        position: relative;
    }

    /* แถบสีเหลืองด้านบนสุดของการ์ด เพิ่มลูกเล่นความสปอร์ต */
    .login-box::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: #ffcc00;
        border-radius: 24px 24px 0 0;
    }

    /* 4. หัวข้อตัวอักษร */
    .login-box h3 {
        color: #ffffff;
        font-weight: 800;
        font-size: 26px;
        text-align: center;
        margin: 10px 0 6px 0;
        letter-spacing: 0.5px;
    }
    .login-box p {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        text-align: center;
        margin: 0 0 35px 0;
    }

    /* 5. ฟอร์มและช่องกรอกข้อมูล */
    .input-group-custom {
        margin-bottom: 24px;
        position: relative;
    }
    .input-group-custom label {
        display: block;
        color: #ffcc00; /* ใช้สีเหลืองเน้นให้เห็นชัดเจน */
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
    /* เมื่อคลิกช่องกรอก ให้เปลี่ยนขอบเป็นสีเหลืองสว่างพร้อมเงาเรืองแสง */
    .input-field:focus {
        outline: none;
        border-color: #ffcc00;
        box-shadow: 0 0 10px rgba(255, 204, 0, 0.2);
        background: rgba(0, 0, 0, 0.6);
    }

    /* 6. ฟังก์ชันเปิด-ปิดตารหัสผ่าน */
    .password-container {
        position: relative;
    }
    .password-container .input-field {
        padding-right: 55px;
    }
    .eye-btn {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        z-index: 3;
        transition: color 0.2s;
    }
    .eye-btn:hover {
        color: #ffcc00;
    }

    /* 7. ฟังก์ชันเพิ่มเติม (จดจำฉัน / ลืมรหัส) */
    .flex-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-bottom: 30px;
    }
    .remember-label {
        color: rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .remember-label input {
        accent-color: #ffcc00; /* ติ๊กถูกสีเหลือง */
        margin: 0;
    }
    .forgot-link {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    .forgot-link:hover {
        color: #ffcc00;
        text-decoration: underline;
    }

    /* 8. ปุ่มล็อกอินสีเหลืองสะดุดตา */
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
    }
    .btn-submit:hover {
        background: #ffe066;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 204, 0, 0.3);
    }

    /* 9. การแจ้งเตือน Error */
    .custom-alert {
        background: rgba(211, 47, 47, 0.2);
        border: 1px solid rgba(211, 47, 47, 0.4);
        color: #ff8a80;
        padding: 12px;
        border-radius: 10px;
        font-size: 13px;
        text-align: center;
        margin-bottom: 20px;
    }

    /* 10. ปุ่มลอยบนขวา "เข้าสู่เว็บไซต์" */
    .floating-nav {
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 10;
    }
    .floating-nav a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #ffffff;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        backdrop-filter: blur(10px);
        transition: all 0.3s;
    }
    .floating-nav a:hover {
        background: #ffcc00;
        color: #121212;
        border-color: #ffcc00;
    }

    /* 11. Responsive (บีบอัดพื้นที่บนจอมือถือไม่ให้หลุดขอบจอ) */
    @media (max-width: 480px) {
        .login-box {
            padding: 35px 24px 30px 24px;
            border-radius: 20px;
        }
        .login-box h3 { font-size: 22px; }
        .login-box p { margin-bottom: 25px; }
        .input-field { padding: 12px 16px; }
        .floating-nav { top: 15px; right: 15px; }
        .floating-nav a { padding: 8px 16px; font-size: 13px; }
    }
</style>

<!-- โหลด FontAwesome สำหรับไอคอนลูกศรและดวงตา -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- ปุ่มลอยบนขวา -->
<div class="floating-nav">
    <a href="{{ route('online.index') }}">
        <i class="fas fa-arrow-left"></i>
        <span>เข้าสู่เว็บไซต์</span>
    </a>
</div>

<div class="fullscreen-wrapper">
    <!-- แสงออร่านีออนเหลืองหลังฉาก -->
    <div class="bg-glow-yellow"></div>
    <div class="bg-glow-yellow-2"></div>

    <!-- กล่องล็อกอินหลัก -->
    <div class="login-box">
        <h3>เข้าสู่ระบบ {{ $shopSettings->shop_name ?: '...' }} POS</h3>
        <p>กรุณาลงชื่อเข้าใช้ระบบของคุณ</p>

        @if ($errors->any())
            <div class="custom-alert">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="post" action="{{ url('/login') }}">
            @csrf
            
            <!-- ส่วนอีเมล -->
            <div class="input-group-custom">
                <label for="email">อีเมลผู้ใช้งาน</label>
                <input type="email" name="email" id="email" class="input-field" placeholder="username@email.com" required autofocus>
            </div>
            
            <!-- ส่วนรหัสผ่าน + เปิดตารหัส -->
            <div class="input-group-custom">
                <label for="password">รหัสผ่าน</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="input-field" placeholder="••••••••" required>
                    <button type="button" class="eye-btn" id="togglePasswordBtn">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- จดจำฉัน & ลืมรหัส -->
            <div class="flex-actions">
                <label class="remember-label">
                    <input type="checkbox" name="remember">
                    <span>จดจำฉันไว้</span>
                </label>
                <a href="{{ url('/forgot-password') }}" class="forgot-link">ลืมรหัสผ่าน?</a>
            </div>

            <!-- ปุ่มล็อกอินสีเหลือง -->
            <button type="submit" class="btn-submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>

<script>
    // สคริปต์สลับเปิด-ปิดดูรหัสผ่าน
    const togglePasswordBtn = document.querySelector('#togglePasswordBtn');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePasswordBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        
        if (isPassword) {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
</script>
@endsection