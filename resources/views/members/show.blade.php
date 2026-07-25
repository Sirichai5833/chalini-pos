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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            รายละเอียดสมาชิก
        </h3>

    </div>

    <div class="card shadow-sm rounded-4 border-0">

        <div class="card-header bg-warning bg-opacity-75">
            <h5 class="mb-0 fw-bold">
                👤 ข้อมูลสมาชิก
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- ชื่อ --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        ชื่อ
                    </label>

                    <div class="fw-bold fs-5">
                        {{ $member->first_name ?? '-' }}
                    </div>
                </div>


                {{-- นามสกุล --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        นามสกุล
                    </label>

                    <div class="fw-bold fs-5">
                        {{ $member->last_name ?? '-' }}
                    </div>
                </div>


                {{-- เบอร์โทรศัพท์ --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        เบอร์โทรศัพท์
                    </label>

                    <div class="fw-bold">
                        @if ($member->phone)
                            <a href="tel:{{ $member->phone }}"
                               class="text-decoration-none">
                                📞 {{ $member->phone }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>


                {{-- อีเมล --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        อีเมล
                    </label>

                    <div class="fw-bold">
                        {{ $member->email ?? '-' }}
                    </div>
                </div>


                {{-- ห้อง --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        เลขห้อง
                    </label>

                    <div class="fw-bold fs-5">
                        🏠 {{ $member->room_number ?? '-' }}
                    </div>
                </div>


                {{-- วันที่เข้าร่วม --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        วันที่เข้าร่วม
                    </label>

                    <div class="fw-bold">
                        {{ $member->created_at?->format('d/m/Y H:i') ?? '-' }}
                    </div>
                </div>

            </div>


            <hr>


            <div class="d-flex gap-2">

                <a href="{{ route('members.edit', $member->id) }}"
                   class="btn btn-warning btn-sm">
                    ✏️ แก้ไขข้อมูล
                </a>

                <a href="{{ route('members.index') }}"
                   class="btn btn-secondary btn-sm">
                    ← กลับไปหน้าสมาชิก
                </a>

            </div>

        </div>

    </div>

</div>


@endsection
