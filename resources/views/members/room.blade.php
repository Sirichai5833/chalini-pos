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
    <h2 class="mb-4">สมาชิกในตึกชาลินี</h2>

    <div class="row">
        @forelse ($members as $member)
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-info">
                    <div class="card-body text-center">
                        <h5 class="card-title">ห้อง {{ $member->room_number }}</h5>
                        <p class="card-text">
                            {{ $member->name }} <br>
                            <small>{{ $member->email }}</small>
                        </p>
                        <a href="{{ route('members.show', $member->id) }}" class="btn btn-primary btn-sm">ดูข้อมูล</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p>ยังไม่มีสมาชิกในระบบ</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
