@extends('layouts.layout')

@section('flash-messages')
    @include('layouts.partials.flash-messages')
@endsection

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-warning">รายชื่อพนักงาน</h2>

            <!-- ฟอร์มค้นหาตามชื่อ -->
            <form action="{{ route('staff.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาพนักงาน"
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </form>
        </div>

        @if ($staff->isEmpty())
            <div class="alert alert-info text-center">ยังไม่มีพนักงานในระบบ</div>
        @else
            <div class="row">
                @foreach ($staff as $user)
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card border-0 shadow-sm h-100 text-center p-3">
                            <div class="mb-3">
                                @if ($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="รูปพนักงาน {{ $user->name }}"
                                        class="img-fluid rounded-3 shadow border"
                                        style="width: 100%; height: 180px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-3"
                                        style="width: 100%; height: 180px;">
                                        ไม่มีรูป
                                    </div>
                                @endif
                            </div>
                            <h5 class="mb-1 fw-bold">{{ $user->name }}</h5>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }}">
                                {{ $user->role === 'admin' ? 'แอดมิน' : 'พนักงาน' }}
                            </span>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <a href="{{ route('staff.alledit', $user->id) }}"
                                    class="btn btn-warning   text-white text-center fw-bold d-block">
                                     แก้ไข
                                </a>

                                <form action="{{ route('staff.delete', $user->id) }}" method="POST"
                                    onsubmit="return confirm('ยืนยันการลบพนักงานคนนี้หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">ลบ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        h2 {
            color: #444;
            font-weight: 700;
        }

        .card:hover {
            transform: scale(1.02);
            transition: 0.2s ease-in-out;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.6em;
        }

        .btn-sm {
            font-size: 0.85rem;
            border-radius: 0.5rem;
        }

        .btn-warning {
            color: #000;
        }
    </style>
    @endpush
@endsection
