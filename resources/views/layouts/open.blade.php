@extends('layouts.layout')

@section('content')
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>chalini</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/blur-bg.jpg') }}">
    
    {{-- 🧩 Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- 🎨 Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- ⚡ Custom CSS สำหรับธีม ดำ-เหลือง --}}
    <style>
        /* ตั้งค่าพื้นหลังของระบบเป็นสีดำเทาเข้มโดย default */
        body {
            background-color: #121212;
            color: #e0e0e0;
        }

        /* ดีไซน์แถบเมนูด้านบน */
        .dy-navbar {
            background-color: #1e1e1e !important; /* ดำเทา */
            border-bottom: 2px solid #ffc107; /* เส้นขอบล่างสีเหลืองทอง */
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.1) !important; /* เงาสะท้อนสีเหลืองบางๆ */
        }

        /* ปุ่มแสดงข้อมูลพนักงาน */
        .btn-staff-info {
            color: #ffffff;
            border-color: #444444;
            background-color: #2d2d2d;
        }
        .btn-staff-info:hover {
            border-color: #ffc107;
            color: #ffc107;
            background-color: #252525;
        }
    </style>
</head>
<body>

    {{-- 🌐 Navbar ธีมดำ-เหลือง (แบบ Fixed-Top เคลื่อนตามหน้าจอ) --}}
    <nav class="navbar navbar-expand-lg navbar-dark dy-navbar fixed-top">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            
            {{-- ฝั่งซ้าย: โลโก้ (แก้ไขโครงสร้างแท็กที่พังให้สมบูรณ์แล้ว) --}}
            <div class="d-flex align-items-center">
                <a href="{{ url('/') }}" class="navbar-brand fw-bold mb-0 text-warning fs-4">
                    <i class="bi bi-shop"></i> ชาลินี
                </a>
            </div>

            {{-- ฝั่งขวา: ข้อมูลพนักงาน และ ปุ่มออกจากระบบ --}}
            <div class="d-flex align-items-center">
                @auth
                    <!-- ปุ่มแสดงชื่อพนักงาน -->
                    <button class="btn btn-staff-info me-2 d-flex align-items-center" type="button">
                        <i class="bi bi-person-circle me-1 text-warning"></i> 
                        {{ auth()->user()->name }}
                    </button>

                    <!-- ปุ่มออกจากระบบ (เซ็ตความปลอดภัยป้องกันกดพลาด) -->
                    <a href="{{ route('logout') }}" class="btn btn-danger d-flex align-items-center"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-1"></i> 
                        <span class="d-none d-sm-inline">ออกจากระบบ</span>
                    </a>
                    
                    {{-- ฟอร์มซ่อนสำหรับส่งค่า POST Logout ไปยัง Laravel backend --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
            </div>

        </div>
    </nav>

    {{-- 📥 ส่วนเนื้อหาหลัก --}}
    <div class="container py-4" style="margin-top: 75px;">
        @yield('content')
    </div>

    {{-- 🧩 Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@endsection

