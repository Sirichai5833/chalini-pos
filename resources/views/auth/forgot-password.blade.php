<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" placeholder="กรอกอีเมล" required>
    <button type="submit">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
</form>
@endsection
