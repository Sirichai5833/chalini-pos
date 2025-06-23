@extends('layouts.layout')

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
    <h3 class="text-center mb-4">แก้ไขข้อมูลส่วนตัว: {{ $member->name }}</h3>

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <form action="{{ route('staff.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
            
                <!-- ชื่อสมาชิก -->
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อสมาชิก</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $member->name) }}" required>
                    
                    @error('name')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            
                <!-- อีเมล -->
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $member->email) }}" required>
                    
                    @error('email')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            
                <div class="mb-3">
                    <label for="image" class="form-label text-gold">รูปโปรไฟล์</label>
                    <input type="file" name="image" class="form-control fancy-input" accept="image/*">
                    @error('image') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>
            
                <!-- รหัสผ่าน (ถ้าต้องการเปลี่ยน) -->
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน (ถ้าต้องการเปลี่ยน)</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="กรอกเพื่อเปลี่ยนรหัสผ่าน">
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่าน">
                </div>
            
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
                    <a href="{{ url('sale') }}" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>            
        </div>
    </div>
</div>
@endsection
