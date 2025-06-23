@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4 text-center">รายชื่อสมาชิกตามห้อง</h3>

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

    {{-- สมาชิก role: member ชั้น 1-5 --}}
    @for ($floor = 1; $floor <= 5; $floor++)
        <h4 class="mt-5">ชั้น {{ $floor }}</h4>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @for ($room = 1; $room <= 24; $room++)
                @php
                    $roomNumber = $floor . str_pad($room, 2, '0', STR_PAD_LEFT);
                    $member = $members->get($roomNumber);
                @endphp
                <div class="col">
                    <div class="card h-100 room-card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">ห้อง {{ $roomNumber }}</h5>
                            @if ($member)
                                <p class="card-text mb-1">ชื่อ: {{ $member->name }}</p>
                                <p class="card-text mb-3">อีเมล: {{ $member->email }}</p>
                                <div class="d-flex flex-wrap ">
                                    <a href="{{ route('members.show', $member->id) }}" class="btn btn-info btn-sm me-2">ดู</a>
                                    <a href="{{ route('members.edit', $member->id) }}" class="btn btn-warning btn-sm me-2">แก้ไข</a>
                                    <form action="{{ route('members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-modern btn-delete">ลบ</button>
                                    </form>
                                </div>
                            @else
                                <p class="text-muted mt-3">ไม่มีสมาชิก</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    @endfor

    {{-- สมาชิก role: admin และ staff ชั้น 6 --}}
   {{-- สมาชิก role: admin และ staff ชั้น 6 --}}
<h4 class="mt-5">(สำหรับ Admin / Staff)</h4>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    @php
    $roomNumber = '601';  // กำหนดเลขห้องสำหรับ admin/staff
    $member = $members->get($roomNumber);
@endphp
    <div class="col">
        <div class="card h-100 room-card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">ห้องพนักงาน</h5>
                @if ($member)
                    <p class="card-text mb-1">ชื่อ: {{ $member->name }}</p>
                    <p class="card-text mb-1">อีเมล: {{ $member->email }}</p>
                    <p class="card-text mb-3">รหัส: 12345678</p>
                    {{-- <div class="d-flex flex-wrap ">
                        <a href="{{ route('members.show', $member->id) }}" class="btn btn-info btn-sm me-2">ดู</a>
                        <a href="{{ route('members.edit', $member->id) }}" class="btn btn-warning btn-sm me-2">แก้ไข</a>
                        <form action="{{ route('members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-modern btn-delete">ลบ</button>
                        </form>
                    </div> --}}
                @else
                    <p class="text-muted mt-3">ไม่มีสมาชิก</p>
                @endif
            </div>
        </div>
    </div>
</div>



</div>
<style>
    body {
        background-color: #f9f9f9; /* ขาวนวล */
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .room-card {
        border-radius: 15px;
        background: #ffffff;
        color: #333;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }

    .room-card:hover {
        background: linear-gradient(135deg, #fffbe6, #fdf6e3); /* ขาวอมทอง */
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.25); /* เงาทอง */
    }

    .room-card:hover p,
    .room-card:hover h5 {
        color: #b38f00 !important; /* ทองเข้ม */
    }

    .room-card .card-body {
        position: relative;
        z-index: 1;
    }

    .btn-modern {
        border: none;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.25s ease;
        box-shadow: 0 0 6px rgba(212, 175, 55, 0.1);
        color: white;
    }

    .btn-view {
    background: linear-gradient(135deg, #5bc0de, #0275d8);
    color: #000 !important;
}

.btn-edit {
    background: linear-gradient(135deg, #ffe082, #fdd835);
    color: #000 !important;
}

.btn-delete {
    background: linear-gradient(135deg, #ff8a80, #e53935);
    color: #000 !important;
}

.room-card:hover .btn-modern {
    color: #000 !important; /* คงไว้ให้ตัวหนังสือยังเป็นสีดำแม้ตอน hover */
}

    h3, h4 {
        color: #b38f00; /* ทอง */
        font-weight: 600;
    }
</style>



@endsection
