@php
    use App\Models\Setting;
@endphp
@extends('layouts.layout')
@section('content')
    <div class="member-create-page py-4"><div class="container-fluid"><div class="member-page-shell">
        <div class="member-main-card">
            <div class="member-hero mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="member-hero-icon"><i class="bi bi-person-plus-fill"></i></span>
                        <div>
                            <h3 class="mb-1 fw-bold">เพิ่มสมาชิกใหม่</h3>
                            <p class="mb-0">กรอกข้อมูลสมาชิก เลือกห้อง และระบบจะสร้างรหัสผ่านเริ่มต้นให้อัตโนมัติ</p>
                        </div>
                    </div>
                </div>
            </div>

@if(session('generated_password'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'เพิ่มสมาชิกสำเร็จ 🎉',
        html: `
            <p>รหัสผ่านเริ่มต้น</p>
            <h4 style="color:#f8b500">{{ session('generated_password') }}</h4>
            <small>กรุณาแจ้งให้สมาชิกเปลี่ยนรหัสผ่านหลังเข้าสู่ระบบ</small>
        `,
        confirmButtonText: 'รับทราบ'
    });
});
</script>
@endif

            <form id="memberForm" action="{{ route('members.store') }}" method="POST">
                @csrf

                <!-- แยก ชื่อ - นามสกุล -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="first_name" class="form-label text-gold">ชื่อ</label>
        <input type="text" name="first_name" id="first_name" class="form-control fancy-input" placeholder="กรอกชื่อสมาชิก" value="{{ old('first_name') }}" required>
        @error('first_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="last_name" class="form-label text-gold">นามสกุล</label>
        <input type="text" name="last_name" id="last_name" class="form-control fancy-input" placeholder="กรอกนามสกุล" value="{{ old('last_name') }}" required>
        @error('last_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- เพิ่มเบอร์โทรศัพท์ -->
<div class="mb-3">
    <label for="phone" class="form-label text-gold">เบอร์โทรศัพท์</label>
    <input type="tel" name="phone" id="phone" class="form-control fancy-input" value="{{ old('phone') }}" placeholder="08X-XXX-XXXX" required>
    @error('phone')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

                <div class="mb-3">
                    <label for="room_number" class="form-label text-gold">เลือกห้อง</label>
                    <select name="room_number" id="room_number" class="form-select fancy-input" required>
                        <option value="">-- กรุณาเลือกห้อง --</option>


                        @php
                            $floors = Setting::get('floors', 5);
                            $roomsPerFloor = Setting::get('roomsPerFloor', []);
                        @endphp


                        @php
                            $usedRooms = \App\Models\User::where('role', 'member')->pluck('room_number')->toArray();
                        @endphp

                        @for ($floor = 1; $floor <= $floors; $floor++)
                            @php
                                $roomCount = $roomsPerFloor[$floor - 1] ?? 24;
                            @endphp
                            @for ($room = 1; $room <= $roomCount; $room++)
                                @php
                                    $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                                @endphp
                                @if (!in_array($roomNumber, $usedRooms))
                                    <option value="{{ $roomNumber }}" {{ old('room_number') == $roomNumber ? 'selected' : '' }}>
                                        ชั้น {{ $floor }} ห้อง {{ $roomNumber }}
                                    </option>
                                @endif
                            @endfor
                        @endfor

                    </select>
                    @error('room_number')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-gold">อีเมล</label>
                    <input type="email" name="email" id="email" class="form-control fancy-input" placeholder="member@example.com" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="role" value="member">
                <div class="mb-4 text-muted small text-center">
    ระบบจะสร้างรหัสผ่านเริ่มต้นให้อัตโนมัติ
</div>

                <div class="member-actions">
                    <a href="{{ route('members.index') }}" class="btn member-btn-secondary">
                        <i class="bi bi-arrow-left"></i> กลับหน้ารายชื่อ
                    </a>
                    <button type="submit" class="btn member-btn-primary" id="submitMemberButton">
                        <i class="bi bi-floppy-fill"></i> บันทึกข้อมูลสมาชิก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




    <style>
        body {
            background: #fdfdfd;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            animation: fadeIn 0.5s ease-in-out;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <style>
        .member-create-page{--my:#ffc107;--my2:#ffd95a;--panel:#171717;--panel2:#202020;--border:#3a3a3a;--text:#f7f7f7;--muted:#aaa;color:var(--text)}
        .member-page-shell{max-width:1050px;margin:0 auto}
        .member-main-card{padding:1.4rem;border:1px solid var(--border);border-radius:22px;background:var(--panel);box-shadow:0 22px 55px rgba(0,0,0,.35);animation:fadeIn .45s ease}
        .member-hero{padding:1.25rem;border:1px solid rgba(255,193,7,.25);border-radius:18px;background:radial-gradient(circle at 92% 10%,rgba(255,193,7,.16),transparent 25%),linear-gradient(135deg,#202020,#0d0d0d)}
        .member-hero h3{color:#fff}.member-hero p{color:var(--muted);font-size:.92rem}
        .member-hero-icon{width:54px;height:54px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:17px;background:linear-gradient(135deg,var(--my),var(--my2));color:#111;font-size:1.45rem;box-shadow:0 10px 24px rgba(255,193,7,.2)}
        .member-create-page .text-gold{color:var(--my)!important}
        .member-create-page .form-label{color:#e8e8e8;font-size:.88rem;font-weight:700}
        .member-create-page .form-control,.member-create-page .form-select{min-height:48px;border:1px solid #454545;border-radius:12px;background:#101010;color:#fff;box-shadow:none}
        .member-create-page .form-control::placeholder{color:#737373}
        .member-create-page .form-control:focus,.member-create-page .form-select:focus{border-color:var(--my);background:#0d0d0d;color:#fff;box-shadow:0 0 0 .2rem rgba(255,193,7,.14)}
        .member-create-page .form-select option{background:#111;color:#fff}
        .member-create-page .text-muted{color:var(--muted)!important}
        .member-btn-primary{border:1px solid var(--my)!important;background:linear-gradient(135deg,var(--my),var(--my2))!important;color:#111!important;font-weight:800;border-radius:12px!important;transition:.2s ease}
        .member-btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 22px rgba(255,193,7,.22)}
        .member-actions{display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.25rem;padding-top:1.2rem;border-top:1px solid var(--border)}
        .member-actions .btn{min-height:46px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;padding:.65rem 1.15rem}
        .member-btn-secondary{border:1px solid #4b4b4b!important;border-radius:12px!important;background:transparent!important;color:#ddd!important;font-weight:700}
        .member-btn-secondary:hover{border-color:var(--my)!important;color:var(--my)!important;background:rgba(255,193,7,.06)!important}
        .member-create-page .custom-alert{border-color:rgba(255,90,90,.4);background:rgba(170,30,30,.18);color:#ff9a9a}
        @media(max-width:767.98px){.member-main-card{padding:1rem}.member-hero{padding:1rem}.member-actions{flex-direction:column-reverse}.member-actions .btn{width:100%}}
    </style>
    <script>
document.addEventListener('DOMContentLoaded', function () {

    const memberForm = document.getElementById('memberForm');

    if (memberForm) {
        memberForm.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: 'คุณต้องการเพิ่มสมาชิกคนนี้ใช่หรือไม่',
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

                    memberForm.submit();
                }

            });
        });
    }

});
</script>
@endsection