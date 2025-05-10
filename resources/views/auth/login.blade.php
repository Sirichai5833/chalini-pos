@extends('layouts.app')

@section('content')
<style>
    body {
        background: url('/images/blur-bg.jpg') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .blur-overlay {
        position: fixed; /* ใช้ fixed เพื่อให้เบลอพื้นหลัง */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.15);
        z-index: 1; /* ปล่อยให้ทับพื้นหลัง */
    }

    .login-container {
        position: relative;
        z-index: 2; /* ทำให้ฟอร์มอยู่ด้านหน้า */
    }

    .card {
        background-color: rgba(255, 255, 255, 0.92);
        border-radius: 12px;
        border: none;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .form-control {
        border-radius: 10px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #ccc;
    }

    .btn-primary {
        border-radius: 10px;
        background: linear-gradient(to right, #4b6cb7, #182848);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, #3a539b, #1a1f71);
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    h3 {
        font-weight: bold;
        color: #2c3e50;
    }

    .top-right-button {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 3;
    }

    .top-right-button a {
        padding: 10px 20px;
        background: linear-gradient(to right, #4b6cb7, #182848);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: background 0.3s ease;
        display: inline-block;
    }

    .top-right-button a:hover {
        background: linear-gradient(to right, #3a539b, #1a1f71);
    }
</style>

<!-- ปุ่มลอยบนขวา -->
<div class="top-right-button">
    <a href="{{ route('online.pagenologin') }}">เข้าสู่เว็บไซต์</a>
</div>

<!-- ส่วน blur และ login form เดิม -->
<div class="blur-overlay"></div>
<div class="row justify-content-center align-items-center login-container" style="min-height: 100vh;">
    <div class="col-md-4">
        <div class="card">
            <h3 class="text-center mb-4">ร้านค้าตึกชาลินี</h3>
            @if ($errors->any())
                <div class="alert alert-danger text-center">
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="post" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control" placeholder="กรอกอีเมล" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่าน" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ url('/forgot-password') }}" class="small text-decoration-none">ลืมรหัสผ่าน?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
</div>


@endsection
