@extends('layouts.layout')

@section('content')
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
<div class="container mt-4">
    <h3 class="text-center mb-4">รายละเอียดสมาชิก: {{ $member->name }}</h3>

    <div class="card shadow-sm rounded">
        <div class="card-body">
            <h5 class="card-title">ข้อมูลสมาชิก</h5>
            <p class="card-text"><strong>ชื่อ:</strong> {{ $member->name }}</p>
            <p class="card-text"><strong>อีเมล:</strong> {{ $member->email }}</p>
            <p class="card-text"><strong>วันที่เข้าร่วม:</strong> {{ $member->created_at->format('d/m/Y') }}</p>
            <p class="card-text"><strong>เลขห้อง:</strong> {{ $member->room_number }}</p>  <!-- เพิ่มเลขห้องที่นี่ -->

            <a href="{{ route('members.edit', $member->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
            <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">กลับไป</a>
        </div>
    </div>
</div>
@endsection
