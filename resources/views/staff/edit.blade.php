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

    <h3 class="text-center mb-4">
        แก้ไขข้อมูลส่วนตัว: {{ $member->name }}
    </h3>


    <div class="card shadow-sm rounded">

        <div class="card-body">

            <form
                action="{{ route('staff.update', $member->id) }}"
                method="POST"
                enctype="multipart/form-data"
                id="profileForm">

                @method('PUT')

                @csrf


                {{-- ชื่อสมาชิก --}}
                <div class="mb-3">

                    <label
                        for="name"
                        class="form-label">

                        ชื่อสมาชิก

                    </label>


                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $member->name) }}"
                        required>


                    @error('name')

                        <div class="text-danger mt-2">

                            {{ $message }}

                        </div>

                    @enderror

                </div>


                {{-- อีเมล --}}
                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label">

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


                {{-- รูปโปรไฟล์ --}}
                <div class="mb-3">

                    <label
                        for="image"
                        class="form-label">

                        รูปโปรไฟล์

                    </label>


                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="form-control"
                        accept="image/*">


                    @error('image')

                        <div class="text-danger mt-1">

                            {{ $message }}

                        </div>

                    @enderror

                </div>


                {{-- รหัสผ่าน --}}
                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label">

                        รหัสผ่านใหม่

                    </label>


                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="อย่างน้อย 6 ตัว มีตัวใหญ่และอักขระพิเศษ"
                        minlength="8">


                    <div
                        class="form-text">

                        รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร
                        มีตัวพิมพ์ใหญ่ และอักขระพิเศษ

                    </div>


                    <div
                        id="passwordError"
                        class="text-danger mt-2 d-none">

                        รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร
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
                        placeholder="ยืนยันรหัสผ่าน"
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

                        บันทึกการเปลี่ยนแปลง

                    </button>


                    <a
                        href="{{ url('sale') }}"
                        class="btn btn-secondary">

                        ยกเลิก

                    </a>

                </div>


            </form>

        </div>

    </div>

</div>


<script>

    document
        .getElementById('profileForm')
        .addEventListener('submit', function(event) {


            const password =
                document
                    .getElementById('password')
                    .value;


            const confirmPassword =
                document
                    .getElementById('password_confirmation')
                    .value;


            const passwordError =
                document
                    .getElementById('passwordError');


            const confirmPasswordError =
                document
                    .getElementById('confirmPasswordError');


            let valid = true;


            /*
            |--------------------------------------------------------------------------
            | ตรวจสอบรหัสผ่าน
            |--------------------------------------------------------------------------
            */


            // ถ้ากรอกรหัสผ่านใหม่
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

                    passwordError
                        .classList
                        .remove('d-none');


                    valid = false;

                } else {

                    passwordError
                        .classList
                        .add('d-none');

                }


                /*
                |--------------------------------------------------------------------------
                | ตรวจสอบยืนยันรหัสผ่าน
                |--------------------------------------------------------------------------
                */


                if (
                    password !==
                    confirmPassword
                ) {

                    confirmPasswordError
                        .classList
                        .remove('d-none');


                    valid = false;

                } else {

                    confirmPasswordError
                        .classList
                        .add('d-none');

                }

            }


            if (!valid) {

                event.preventDefault();

            }


        });

</script>


@endsection
