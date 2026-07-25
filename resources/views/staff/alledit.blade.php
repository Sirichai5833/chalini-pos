@extends('layouts.layout')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg border-0 rounded-4 p-4 bg-white">
            <h3 class="mb-4 text-center text-gold fw-bold">👥 แก้ไขข้อมูลพนักงาน</h3>

            @if (session('success'))
                <x-sweet-alert icon="success" title="Oh Yeah!" text="{{ session('success') }}" confirm-button-text="Ok" />
            @endif

            @if (session('error'))
                <x-sweet-alert icon="error" title="Oops..." text="{{ session('error') }}" confirm-button-text="Ok" />
            @endif

            <!-- ฟอร์มแก้ไขข้อมูลพนักงาน -->
            <form action="{{ route('staff.allupdate', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- ชื่อพนักงาน -->
                <div class="mb-3">
                    <label for="name" class="form-label text-gold">ชื่อพนักงาน</label>
                    <input type="text" name="name" class="form-control fancy-input"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- อีเมล -->
                <div class="mb-3">
    <label for="email" class="form-label text-gold">อีเมล</label>

    <input
        type="email"
        name="email"
        id="email"
        class="form-control fancy-input"
        value="{{ old('email', $user->email) }}"
        placeholder="example@gmail.com"
        required
    >

    @error('email')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
                <!-- ตำแหน่ง -->
                <div class="mb-3">
                    <label for="role" class="form-label text-gold">ตำแหน่ง</label>
                    <select name="role" class="form-select fancy-input" required>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>แอดมิน</option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>พนักงานขาย
                        </option>
                    </select>
                    @error('role')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- รูปโปรไฟล์ -->
                <div class="mb-3">
                    <label for="image" class="form-label text-gold">รูปโปรไฟล์</label>
                    <input type="file" name="image" class="form-control fancy-input" accept="image/*">
                    @error('image')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror

                    <!-- แสดงรูปเดิมหากมี -->
                    @if ($user->image)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $user->image) }}" alt="รูปพนักงาน" class="img-fluid rounded-3"
                                style="max-height: 200px; object-fit: cover;">
                        </div>
                    @endif
                </div>

                <!-- รหัสผ่าน -->
                <div class="mb-3">
                    <label for="password" class="form-label text-gold">รหัสผ่าน</label>

                    <input type="password" name="password" class="form-control fancy-input" minlength="6"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{6,}$"
                        title="รหัสผ่านต้องมีอย่างน้อย 6 ตัว และต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และอักขระพิเศษ">

                    <small class="text-muted">
                        รหัสผ่านต้องมีอย่างน้อย 6 ตัว และประกอบด้วย:
                        ตัวพิมพ์ใหญ่, ตัวพิมพ์เล็ก และอักขระพิเศษ
                    </small>

                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ยืนยันรหัสผ่าน -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label text-gold">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="password_confirmation" class="form-control fancy-input">
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="d-flex justify-content-center gap-2">
    <a href="{{ url()->previous() }}" class="btn btn-secondary px-4 py-2 rounded-pill shadow">
        ← ย้อนกลับ
    </a>

    <button type="submit" class="btn btn-gold px-4 py-2 rounded-pill shadow">
        💾 บันทึกข้อมูล
    </button>
</div>
            </form>
        </div>
    </div>

    <style>
        /* สไตล์ต่างๆ */
    </style>
@endsection
