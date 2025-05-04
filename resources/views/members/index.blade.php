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
</div>

<style>
      body {
        background-color: #a7a7a7; /* สีเทา */
    }
    .room-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        background-color: #ffffff;
        position: relative;
        overflow: hidden;
        color: #000; /* default text color */
    }

    .room-card:hover {
        background: linear-gradient(145deg, #000000, #1a1a1a, #2a2a2a);
        color: #fff; /* ตัวอักษรขาว */
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .room-card:hover a,
    .room-card:hover p,
    .room-card:hover h5 {
        color: #fff !important; /* ทำให้ลิงก์และข้อความเป็นสีขาวทั้งหมด */
    }

    .room-card .card-body {
        position: relative;
        z-index: 1;
    }

    .btn-modern {
        border: none;
        border-radius: 8px; /* 👈 เปลี่ยนจาก 30px เป็นทรงหัวมน */
        padding: 6px 14px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.25s ease;
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.1);   /* แสงนุ่มขาว */

        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view {
        background: linear-gradient(135deg, #17a2b8, #138496); /* ฟ้า */
    }

    .btn-edit {
        background: linear-gradient(135deg, #ffc107, #e0a800); /* เหลือง */
    }

    .btn-delete {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: rgb(0, 0, 0);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        opacity: 0.95;
    }

    .btn-modern:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .room-card:hover .btn-modern {
        color: #fff !important;
    }


</style>

@endsection
