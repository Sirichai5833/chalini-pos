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
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.15);
        z-index: 1;
    }

    .reset-container {
        position: relative;
        z-index: 2;
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
</style>

<div class="blur-overlay"></div>

<div class="row justify-content-center align-items-center reset-container" style="min-height: 100vh;">
    <div class="col-md-4">
        <div class="card">
            <h3 class="text-center mb-4">ตั้งรหัสผ่านใหม่</h3>

            @if ($errors->any())
                <div class="alert alert-danger text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" class="form-control" placeholder="กรอกอีเมล" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่านใหม่" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="กรอกยืนยันรหัสผ่าน" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">ตั้งรหัสผ่านใหม่</button>
            </form>
        </div>
    </div>
</div>

@endsection
