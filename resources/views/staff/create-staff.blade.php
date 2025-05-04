@extends('layouts.layout') {{-- ใช้ layout หลักของคุณ --}}

@section('content')
@if (session('success'))
<x-sweet-alert 
    icon="success" 
    title="Oh Yeah!" 
    text="{{ session('success') }}" 
    confirm-button-text="Ok"
/>
@endif

@if (session('error'))
<x-sweet-alert 
    icon="error" 
    title="Oops..." 
    text="{{ session('error') }}" 
    confirm-button-text="Ok"
/>
@endif
<div class="container mt-4">
    <h3 class="mb-4">เพิ่มพนักงานใหม่</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อพนักงาน</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">อีเมล</label>
            <input type="email" name="email" class="form-control" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">ตำแหน่ง</label>
            <select name="role" class="form-select" required>
                <option value="admin">แอดมิน</option>
                <option value="staff">พนักงานขาย</option>
            </select>
            @error('role') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">รูปโปรไฟล์</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @error('image') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">รหัสผ่าน</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
</div>
@endsection
