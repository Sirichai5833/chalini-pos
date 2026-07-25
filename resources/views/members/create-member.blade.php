@php
    use App\Models\Setting;
@endphp
@extends('layouts.layout')
@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg border-0 rounded-4 p-4 bg-white">
            <h3 class="mb-4 text-center text-gold fw-bold"> เพิ่มสมาชิกใหม่ </h3>
            <!-- Modal ตั้งค่าชั้น -->
            <div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-4">
                        <div class="modal-header bg-warning bg-opacity-75">
                            <h5 class="modal-title fw-bold" id="settingModalLabel">⚙️ ตั้งค่าชั้นและจำนวนห้อง</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="settingForm">
                                <div class="mb-3">
                                    <label class="form-label">จำนวนชั้น</label>
                                    <input type="number" id="totalFloors" class="form-control" min="1"
                                        value="{{ Setting::get('floors', 5) }}">
                                </div>

                                <div id="roomsContainer"></div>


                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="button" class="btn btn-gold" id="saveSetting">💾 บันทึก</button>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#settingModal">
                        ⚙️ ตั้งค่าชั้น / จำนวนห้อง
                    </button>
                </div>
            @endif
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
        <input type="text" name="first_name" class="form-control fancy-input" value="{{ old('first_name') }}" required>
        @error('first_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="last_name" class="form-label text-gold">นามสกุล</label>
        <input type="text" name="last_name" class="form-control fancy-input" value="{{ old('last_name') }}" required>
        @error('last_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- เพิ่มเบอร์โทรศัพท์ -->
<div class="mb-3">
    <label for="phone" class="form-label text-gold">เบอร์โทรศัพท์</label>
    <input type="tel" name="phone" class="form-control fancy-input" value="{{ old('phone') }}" placeholder="08X-XXX-XXXX" required>
    @error('phone')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

                <div class="mb-3">
                    <label for="room_number" class="form-label text-gold">เลือกห้อง</label>
                    <select name="room_number" class="form-select fancy-input" required>
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
                                    <option value="{{ $roomNumber }}">
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
                    <input type="email" name="email" class="form-control fancy-input" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="role" value="member">
                <div class="mb-4 text-muted small text-center">
    ระบบจะสร้างรหัสผ่านเริ่มต้นให้อัตโนมัติ
</div>

                <div class="text-center">
                    <button type="submit" class="btn btn-gold px-4 py-2 rounded-pill shadow">💾 บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const floorsInput = document.getElementById('totalFloors');
            const container = document.getElementById('roomsContainer');

            const roomsData = @json(Setting::get('roomsPerFloor', []));
            const defaultRooms = 24;

            function renderRoomInputs() {
                container.innerHTML = '';
                const floors = floorsInput.value;

                for (let i = 1; i <= floors; i++) {
                    const val = roomsData[i - 1] ?? defaultRooms;
                    container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">จำนวนห้องในชั้น ${i}</label>
                    <input type="number" class="form-control room-input" min="1" value="${val}">
                </div>`;
                }
            }

            floorsInput.addEventListener('input', renderRoomInputs);
            renderRoomInputs();

            document.getElementById('saveSetting').addEventListener('click', () => {
                const floors = parseInt(floorsInput.value);
                const rooms = Array.from(document.querySelectorAll('.room-input')).map(r => parseInt(r
                    .value));

                fetch("{{ route('members.members.setFloors') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            floors,
                            roomsPerFloor: rooms
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ บันทึกการตั้งค่าเรียบร้อย');
                            location.reload();
                        }
                    })
                    .catch(err => {
                        console.error("Fetch error:", err);
                        alert("❌ เกิดข้อผิดพลาด ดู console");
                    });
            });
        });
    </script>


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
