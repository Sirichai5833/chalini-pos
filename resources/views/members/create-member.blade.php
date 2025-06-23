@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 p-4 bg-white">
        <h3 class="mb-4 text-center text-gold fw-bold"> เพิ่มสมาชิกใหม่ </h3>

        <form action="{{ route('members.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label text-gold">ชื่อสมาชิก</label>
                <input type="text" name="name" class="form-control fancy-input" required>
            </div>

            <div class="mb-3">
                <label for="room_number" class="form-label text-gold">เลือกห้อง</label>
                <select name="room_number" class="form-select fancy-input" required>
                    <option value="">-- กรุณาเลือกห้อง --</option>
                   @php
    $usedRooms = \App\Models\User::where('role', 'member',)->pluck('room_number')->toArray();
@endphp

                    @for ($floor = 1; $floor <= 5; $floor++)
                        @for ($room = 1; $room <= 24; $room++)
                            @php
                                $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                            @endphp
                            @if (!in_array($roomNumber, $usedRooms))
                                <option value="{{ $roomNumber }}" {{ old('room_number') == $roomNumber ? 'selected' : '' }}>
                                    ห้อง {{ $roomNumber }}
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

            <div class="mb-3">
                <label for="password" class="form-label text-gold">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control fancy-input" required>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label text-gold">ยืนยันรหัสผ่าน</label>
                <input type="password" name="password_confirmation" class="form-control fancy-input" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-gold px-4 py-2 rounded-pill shadow">💾 บันทึกข้อมูล</button>
            </div>
        </form>
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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
