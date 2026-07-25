@extends('layouts.online')

@section('content')

@if (session('success')) <x-sweet-alert
     icon="success"
     title="Oh Yeah!"
     text="{{ session('success') }}"
     confirm-button-text="Ok"
 />
@endif

@if (session('error')) <x-sweet-alert
     icon="error"
     title="Oops..."
     text="{{ session('error') }}"
     confirm-button-text="Ok"
 />
@endif

<div class="container mt-4">

```
<h3 class="text-center mb-4">
    แก้ไขข้อมูลส่วนตัว
</h3>

<div class="card shadow-sm rounded">

    <div class="card-body">

        <form
            action="{{ route('online.update', $member->id) }}"
            method="POST"
            id="profileForm">

            @method('PUT')
            @csrf

            {{-- ชื่อ --}}

<div class="mb-3">


<label for="name" class="form-label">
    ชื่อ
</label>

<input
    type="text"
    name="first_name"
    id="first_name"
    class="form-control"
    value="{{ old('first_name', $member->first_name) }}"
    required>

@error('first_name')
    <div class="text-danger mt-2">
        {{ $message }}
    </div>
@enderror


</div>

{{-- นามสกุล --}}

<div class="mb-3">


<label for="lastname" class="form-label">
    นามสกุล
</label>

<input
    type="text"
    name="last_name"
    id="last_name"
    class="form-control"
    value="{{ old('last_name', $member->last_name) }}"
    required>

@error('last_name')
    <div class="text-danger mt-2">
        {{ $message }}
    </div>
@enderror


</div>



            {{-- เบอร์โทรศัพท์ --}}
            <div class="mb-3">

                <label for="phone" class="form-label">
                    เบอร์โทรศัพท์
                </label>

                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="{{ old('phone', $member->phone) }}"
                    placeholder="กรอกเบอร์โทรศัพท์"
                    maxlength="10"
                    required>

                @error('phone')
                    <div class="text-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- อีเมล --}}
            <div class="mb-3">

                <label for="email" class="form-label">
                    อีเมล
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ old('email', $member->email) }}"
                    placeholder="example@email.com"
                    required>

                <div
                    id="emailError"
                    class="text-danger mt-2 d-none">

                    กรุณากรอกอีเมลให้ถูกต้อง

                </div>

                @error('email')
                    <div class="text-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- รหัสผ่านใหม่ --}}
            <div class="mb-3">

                <label for="password" class="form-label">
                    รหัสผ่านใหม่
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน"
                    minlength="8">

                <div class="form-text">
                    รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร
                    มีตัวพิมพ์ใหญ่ และอักขระพิเศษ
                </div>

                <div
                    id="passwordError"
                    class="text-danger mt-2 d-none">

                    รหัสผ่านต้องมีอย่างน้อย 6 ตัว
                    มีตัวพิมพ์ใหญ่ และอักขระพิเศษ

                </div>

                @error('password')
                    <div class="text-danger mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- ยืนยันรหัสผ่าน --}}
            <div class="mb-3">

                <label
                    for="password_confirmation"
                    class="form-label">

                    ยืนยันรหัสผ่าน

                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                    placeholder="ยืนยันรหัสผ่านใหม่"
                    minlength="8">

                <div
                    id="confirmPasswordError"
                    class="text-danger mt-2 d-none">

                    รหัสผ่านไม่ตรงกัน

                </div>

            </div>


            {{-- ปุ่ม --}}
            <div class="d-flex justify-content-between">

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="bi bi-save me-1"></i>
                    บันทึกการเปลี่ยนแปลง

                </button>

                <a
                    href="{{ route('online.index') }}"
                    class="btn btn-secondary">

                    ยกเลิก

                </a>

            </div>

        </form>

    </div>

</div>
```

</div>

<script>

document
    .getElementById('profileForm')
    .addEventListener('submit', function(event) {

        const password =
            document.getElementById('password').value;

        const confirmPassword =
            document.getElementById('password_confirmation').value;

        const passwordError =
            document.getElementById('passwordError');

        const confirmPasswordError =
            document.getElementById('confirmPasswordError');

        let valid = true;


        /*
        |--------------------------------------------------------------------------
        | ตรวจสอบรหัสผ่านเฉพาะเมื่อกรอกใหม่
        |--------------------------------------------------------------------------
        */

        if (password.length > 0) {

            const hasMinimumLength =
                password.length >= 8;

            const hasUppercase =
                /[A-Z]/.test(password);

            const hasSpecialCharacter =
                /[^A-Za-z0-9]/.test(password);


            if (
                !hasMinimumLength ||
                !hasUppercase ||
                !hasSpecialCharacter
            ) {

                passwordError.classList.remove('d-none');

                valid = false;

            } else {

                passwordError.classList.add('d-none');

            }


            if (password !== confirmPassword) {

                confirmPasswordError.classList.remove('d-none');

                valid = false;

            } else {

                confirmPasswordError.classList.add('d-none');

            }

        }


        if (!valid) {

            event.preventDefault();

        }

    });

</script>

@endsection
