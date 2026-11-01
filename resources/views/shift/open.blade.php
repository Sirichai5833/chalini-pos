@extends('layouts.layout')

@section('flash-messages')
    @include('layouts.partials.flash-messages')
@endsection

@section('validation-errors')
    @include('layouts.partials.validation-errors')
@endsection

@push('styles')
<style>
    /* ตั้งค่าพื้นหลังของหน้าจอเป็นสีดำกึ่งเข้ม */
    body {
        background-color: #121212;
        color: #e0e0e0;
    }

    /* ปรับแต่ง Card ให้เป็นสีดำเหลือง */
    .dy-card {
        background-color: #1e1e1e; /* สีพื้น card ดำเทา */
        border: 1px solid #ffc107; /* เส้นขอบสีเหลือง */
        border-radius: 10px;
    }

    .dy-card-header {
        background-color: #ffc107; /* พื้นหลังหัวข้อสีเหลือง */
        color: #000; /* ตัวหนังสือหัวข้อสีดำ */
        border-bottom: 1px solid #ffc107;
        border-radius: 10px 10px 0 0 !important;
        font-weight: bold;
    }

    /* ปรับแต่ง Form Control (Input, Select, Textarea) ให้เข้ากับธีมมืด */
    .dy-form .form-control, 
    .dy-form .form-select {
        background-color: #2d2d2d; /* พื้นหลัง input สีเทาเข้ม */
        color: #fff; /* ตัวหนังสือสีขาว */
        border: 1px solid #444; /* เส้นขอบปกติ */
    }

    /* เมื่อ Focus ที่ Input */
    .dy-form .form-control:focus, 
    .dy-form .form-select:focus {
        background-color: #333;
        color: #fff;
        border-color: #ffc107; /* เส้นขอบเปลี่ยนเป็นสีเหลือง */
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); /* แสงเรืองรองสีเหลือง */
    }

    /* ปรับแต่ง Label */
    .dy-form label {
        color: #ffc107; /* สี Label เป็นสีเหลือง */
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    /* สำหรับ Input ที่เป็น Readonly */
    .dy-form .form-control[readonly] {
        background-color: #252525; /* มืดลงกว่าปกติ */
        color: #aaa; /* ตัวหนังสือจางลง */
        border-color: #333;
    }

    /* ปรับแต่งปุ่มกด */
    .btn-dy-yellow {
        background-color: #ffc107;
        color: #000;
        font-weight: bold;
        border: 1px solid #ffc107;
    }

    .btn-dy-yellow:hover {
        background-color: #e0ac06; /* สีเหลืองเข้มขึ้นเมื่อชี้ */
        color: #000;
        border-color: #e0ac06;
    }
</style>
@endpush

@section('content')

<div class="container mt-5 dy-form">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- เพิ่ม class dy-card และเอา shadow ออกเพื่อให้ดู Clean --}}
            <div class="card dy-card shadow-lg">
                
                {{-- เปลี่ยน bg-primary เป็น dy-card-header --}}
                <div class="card-header dy-card-header d-flex align-items-center">
                    <i class="bi bi-door-open-fill me-2 fs-4"></i> {{-- เพิ่มไอคอนหน้าชื่อ --}}
                    <h4 class="mb-0">เปิดกะการขาย</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('shift.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>
                                    <i class="bi bi-person-circle me-1"></i> ชื่อพนักงาน
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    value="{{ auth()->user()->name }}" 
                                    readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>
                                    <i class="bi bi-cash-stack me-1"></i> เงินทอนเริ่มต้น
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control text-end fs-5 fw-bold text-success" {{-- ปรับให้ตัวเลขเด่น --}}
                                    value="{{ number_format($openingCash, 2) }}" 
                                    readonly>
                            </div>
                        </div>

                       

                        <div class="d-grid gap-2">
                            {{-- เปลี่ยน class btn-success เป็น btn-dy-yellow และทำให้เต็มความกว้าง --}}
                            <button class="btn btn-dy-yellow btn-lg">
                                <i class="bi bi-check-circle-fill me-2"></i> ยืนยันเปิดกะการขาย
                            </button>
                    
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
