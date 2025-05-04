@extends('layouts.layout')

@section('content')
<h3 class="mb-4">เพิ่มสมาชิก</h3>

<form action="{{ route('members.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">ชื่อสมาชิก</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="room_number" class="form-label">เลือกห้อง</label>
        <select name="room_number" class="form-control" required>
            <option value="">-- กรุณาเลือกห้อง --</option>
            @php
                // ดึงห้องที่มีอยู่แล้วจากฐานข้อมูล
                $usedRooms = \App\Models\User::pluck('room_number')->toArray();
            @endphp
            @for ($floor = 1; $floor <= 5; $floor++)
                @for ($room = 1; $room <= 24; $room++)
                    @php
                        $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                    @endphp
                    {{-- เช็คห้องที่ถูกใช้แล้วให้ไม่แสดงใน dropdown --}}
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
            <label for="email">อีเมล</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
    </div>

    <div class="mb-3">
            <input type="hidden" name="role" value="member">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">บันทึก</button>
</form>
@endsection
