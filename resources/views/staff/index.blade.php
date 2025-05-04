@extends('layouts.layout')

@section('content')
@if (session('success'))
<x-sweet-alert 
    icon="success" 
    title="สำเร็จ!" 
    text="{{ session('success') }}" 
    confirm-button-text="ตกลง"
/>
@endif

@if (session('error'))
<x-sweet-alert 
    icon="error" 
    title="ผิดพลาด" 
    text="{{ session('error') }}" 
    confirm-button-text="ตกลง"
/>
@endif

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">รายการพนักงาน</h2>
        <a href="{{ route('staff.create') }}" class="btn btn-success">
            <i class="fas fa-user-plus"></i> เพิ่มพนักงาน
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ชื่อ</th>
                    <th>อีเมล</th>
                    <th>บทบาท</th>
                    <th>รูปภาพ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }}">
                                {{ $user->role === 'admin' ? 'แอดมิน' : 'พนักงาน' }}
                            </span>
                        </td>
                        <td>
                            @if ($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="รูปพนักงาน" width="60" class="rounded-circle">
                            @else
                                <span class="text-muted">ไม่มีรูป</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <form action="#" method="POST" class="d-inline-block" onsubmit="return confirm('ยืนยันการลบพนักงานคนนี้หรือไม่?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">ไม่มีข้อมูลพนักงาน</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
