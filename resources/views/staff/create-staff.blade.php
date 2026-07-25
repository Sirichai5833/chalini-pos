@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 p-4 bg-white">
        <h3 class="mb-4 text-center text-gold fw-bold">เพิ่มพนักงานใหม่</h3>

        {{-- success ปกติ --}}
        @if (session('success'))
        <x-sweet-alert 
            icon="success" 
            title="สำเร็จ!" 
            text="{{ session('success') }}" 
            confirm-button-text="ตกลง"
        />
        @endif

        {{-- error --}}
        @if (session('error'))
        <x-sweet-alert 
            icon="error" 
            title="เกิดข้อผิดพลาด" 
            text="{{ session('error') }}" 
            confirm-button-text="ตกลง"
        />
        @endif

        {{-- 🔥 แสดงรหัสผ่านที่ generate --}}
        @if(session('generated_password'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'เพิ่มพนักงานสำเร็จ 🎉',
                    html: `
                        <p>รหัสผ่านเริ่มต้น:</p>
                        <h4 style="color:#f8b500">{{ session('generated_password') }}</h4>
                        <small>กรุณาแจ้งให้พนักงานเปลี่ยนรหัสผ่านทันที</small>
                    `,
                    confirmButtonText: 'รับทราบ'
                });
            });
        </script>
        @endif

        <form id="staffForm" action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
    <label class="form-label text-gold">ชื่อพนักงาน</label>
    <input
        type="text"
        name="name"
        class="form-control fancy-input"
        placeholder="กรอกชื่อ-นามสกุล"
        required
    >
    @error('name')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

            <div class="mb-3">
    <label class="form-label text-gold">อีเมล</label>
    <input
        type="email"
        name="email"
        class="form-control fancy-input"
        placeholder="เช่น somchai@example.com"
        required
    >
    @error('email')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

            <div class="mb-3">
                <label class="form-label text-gold">ตำแหน่ง</label>
                <select name="role" class="form-select fancy-input" required>
                    <option value="admin">แอดมิน</option>
                    <option value="staff">พนักงานขาย</option>
                </select>
                @error('role') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-gold">รูปโปรไฟล์</label>
                <input type="file" name="image" class="form-control fancy-input" accept="image/*">
                @error('image') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4 text-muted small text-center">
                🔐 ระบบจะสร้างรหัสผ่านเริ่มต้นให้อัตโนมัติ
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-gold px-4 py-2 rounded-pill shadow">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background: #fdfdfd;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .text-gold {
        color: #b38f00;
    }

    .btn-gold {
        background: linear-gradient(135deg, #fceabb, #f8b500);
        color: #000;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-gold:hover {
        background: linear-gradient(135deg, #ffe57f, #fdd835);
        transform: translateY(-2px);
    }

    .fancy-input {
        border-radius: 10px;
        border: 1px solid #ddd;
        transition: 0.3s;
    }

    .fancy-input:focus {
        border-color: #f8b500;
        box-shadow: 0 0 0 0.25rem rgba(248, 181, 0, 0.25);
    }

    .card {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('staffForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: 'คุณต้องการเพิ่มพนักงานคนนี้ใช่หรือไม่',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f8b500',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, บันทึก',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'กำลังบันทึก...',
                    text: 'กรุณารอสักครู่',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });

    });

});
</script>
@endsection
