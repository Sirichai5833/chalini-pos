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
    <h3 class="text-center mb-4">แก้ไขข้อมูลสมาชิก: {{ $member->name }}</h3>

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <form action="{{ route('members.update', $member->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- ชื่อสมาชิก -->
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อสมาชิก</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $member->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
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

                <!-- รหัสผ่าน (ถ้าต้องการเปลี่ยน) -->
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน (ถ้าต้องการเปลี่ยน)</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="กรอกเพื่อเปลี่ยนรหัสผ่าน">
                     <small class="text-muted d-block mt-1">
                        รหัสผ่านต้องมีอย่างน้อย 8 ตัว, ตัวอักษรพิมพ์ใหญ่ 1 ตัว และอักขระพิเศษอย่างน้อย 1 ตัว เช่น @#$%
                    </small>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- เลขห้อง -->
                <div class="mb-3">
                    <label for="room_number" class="form-label">เลขห้อง</label>
                    <input type="text" name="room_number" id="room_number" class="form-control" value="{{ old('room_number', $member->room_number) }}" required>

                    @error('room_number')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
                    <a href="{{ route('members.show', $member->id) }}" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
