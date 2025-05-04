@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ตั้งรหัสผ่านใหม่</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ url('/reset-password') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3">
            <label>รหัสผ่านใหม่</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">ตั้งรหัสผ่านใหม่</button>
    </form>
</div>
@endsection
