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
        แก้ไขข้อมูลสมาชิก:
        {{ $member->first_name }} {{ $member->last_name }}
    </h3>

    <div class="card shadow-sm rounded-4">

        <div class="card-body">

            <form action="{{ route('members.update', $member->id) }}"
                  method="POST">

                @csrf
                @method('PUT')


                {{-- ชื่อ - นามสกุล --}}
                <div class="row">

                    {{-- ชื่อ --}}
                    <div class="col-md-6 mb-3">

                        <label for="first_name"
                               class="form-label">
                            ชื่อ
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            id="first_name"
                            class="form-control"
                            value="{{ old('first_name', $member->first_name) }}"
                            required
                        >

                        @error('first_name')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- นามสกุล --}}
                    <div class="col-md-6 mb-3">

                        <label for="last_name"
                               class="form-label">
                            นามสกุล
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            id="last_name"
                            class="form-control"
                            value="{{ old('last_name', $member->last_name) }}"
                            required
                        >

                        @error('last_name')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- เบอร์โทรศัพท์ --}}
                <div class="mb-3">

                    <label for="phone"
                           class="form-label">
                        เบอร์โทรศัพท์
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        class="form-control"
                        value="{{ old('phone', $member->phone) }}"
                        placeholder="0812345678"
                        maxlength="10"
                        pattern="[0-9]{9,10}"
                        inputmode="numeric"
                        required
                    >

                    @error('phone')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- อีเมล --}}
                <div class="mb-3">

                    <label for="email"
                           class="form-label">
                        อีเมล
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email', $member->email) }}"
                    >

                    @error('email')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- รหัสผ่าน --}}
                <div class="mb-3">

                    <label for="password"
                           class="form-label">
                        รหัสผ่าน
                        <span class="text-muted">
                            (ถ้าต้องการเปลี่ยน)
                        </span>
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="กรอกเพื่อเปลี่ยนรหัสผ่าน"
                    >

                    <small class="text-muted d-block mt-1">
                        รหัสผ่านต้องมีอย่างน้อย 6 ตัว,
                        ตัวอักษรพิมพ์ใหญ่ 1 ตัว
                        และอักขระพิเศษอย่างน้อย 1 ตัว
                        เช่น @#$%
                    </small>

                    @error('password')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- เลขห้อง --}}
                <div class="mb-3">

                    <label for="room_number"
                           class="form-label">
                        เลขห้อง
                    </label>

                    <input
                        type="text"
                        name="room_number"
                        id="room_number"
                        class="form-control"
                        value="{{ old('room_number', $member->room_number) }}"
                        required
                    >

                    @error('room_number')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ปุ่ม --}}
                <div class="d-flex justify-content-between">

                    <button
                        type="submit"
                        class="btn btn-success">

                        💾 บันทึกการเปลี่ยนแปลง

                    </button>

                    <a
                        href="{{ route('members.show', $member->id) }}"
                        class="btn btn-secondary">

                        ยกเลิก

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


@endsection
