
@php
    use App\Models\Setting;

    $totalFloors = Setting::get('floors', 5);
    $roomsPerFloor = Setting::get('roomsPerFloor', []);

    $totalRooms = 0;

    for ($floorIndex = 0; $floorIndex < $totalFloors; $floorIndex++) {
        $totalRooms += $roomsPerFloor[$floorIndex] ?? 24;
    }

    $occupiedRooms = $members->filter(function ($member) {
        return $member !== null;
    })->count();

    $availableRooms = max(0, $totalRooms - $occupiedRooms);
@endphp

@extends('layouts.layout')

@section('flash-messages')
    @include('layouts.partials.flash-messages', [
        'successTitle' => 'สำเร็จ!',
        'errorTitle' => 'เกิดข้อผิดพลาด',
        'confirmButtonText' => 'ตกลง',
    ])
@endsection

@push('styles')
<style>
    .member-room-page {
        --member-black: #0a0a0a;
        --member-panel: #171717;
        --member-panel-soft: #202020;
        --member-yellow: #ffc107;
        --member-yellow-soft: #ffdc68;
        --member-border: #393939;
        --member-text: #f5f5f5;
        --member-muted: #ababab;
        --member-danger: #dc3545;
        color: var(--member-text);
    }

    .member-room-page .page-shell {
        max-width: 1500px;
        margin: 0 auto;
    }

    .member-room-page .member-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.25);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, 0.2),
                transparent 25%
            ),
            linear-gradient(135deg, #1d1d1d, #0b0b0b);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }

    .member-room-page .member-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .member-room-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .member-room-page .hero-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 19px;
        background: linear-gradient(
            135deg,
            var(--member-yellow),
            var(--member-yellow-soft)
        );
        color: #111;
        font-size: 1.65rem;
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.22);
    }

    .member-room-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
        letter-spacing: 0.2px;
    }

    .member-room-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--member-muted);
        font-size: 0.93rem;
    }

    .member-room-page .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .member-room-page .btn-yellow {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.1rem;
        border: 1px solid var(--member-yellow);
        border-radius: 12px;
        background: linear-gradient(
            135deg,
            var(--member-yellow),
            var(--member-yellow-soft)
        );
        color: #111;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-room-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .member-room-page .btn-dark-outline {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.1rem;
        border: 1px solid #494949;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.035);
        color: #e3e3e3;
        font-weight: 700;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-room-page .btn-dark-outline:hover {
        border-color: var(--member-yellow);
        color: var(--member-yellow);
        background: rgba(255, 193, 7, 0.07);
    }

    .member-room-page .summary-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--member-border);
        border-radius: 18px;
        background: var(--member-panel);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
    }

    .member-room-page .summary-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 14px;
        background: rgba(255, 193, 7, 0.12);
        color: var(--member-yellow);
        font-size: 1.2rem;
    }

    .member-room-page .summary-label {
        color: var(--member-muted);
        font-size: 0.78rem;
        font-weight: 650;
    }

    .member-room-page .summary-value {
        margin-top: 0.15rem;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 850;
        line-height: 1;
    }

    .member-room-page .control-panel {
        margin: 1.25rem 0;
        padding: 1rem;
        border: 1px solid var(--member-border);
        border-radius: 18px;
        background: var(--member-panel);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .member-room-page .search-group {
        position: relative;
    }

    .member-room-page .search-icon {
        position: absolute;
        top: 50%;
        left: 1rem;
        z-index: 2;
        transform: translateY(-50%);
        color: var(--member-yellow);
    }

    .member-room-page .search-input {
        min-height: 48px;
        padding-left: 2.75rem;
        border: 1px solid #454545;
        border-radius: 13px;
        background: #101010;
        color: #fff;
    }

    .member-room-page .search-input::placeholder {
        color: #747474;
    }

    .member-room-page .search-input:focus {
        border-color: var(--member-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .member-room-page .floor-section {
        margin-top: 1.6rem;
    }

    .member-room-page .floor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
        padding: 0.9rem 1.05rem;
        border-left: 4px solid var(--member-yellow);
        border-radius: 0 14px 14px 0;
        background: linear-gradient(
            90deg,
            rgba(255, 193, 7, 0.12),
            transparent
        );
    }

    .member-room-page .floor-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: #fff;
        font-size: 1.1rem;
        font-weight: 850;
    }

    .member-room-page .floor-title i {
        color: var(--member-yellow);
    }

    .member-room-page .floor-count {
        padding: 0.35rem 0.65rem;
        border: 1px solid rgba(255, 193, 7, 0.28);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--member-yellow);
        font-size: 0.75rem;
        font-weight: 750;
    }

    .member-room-page .room-card {
        position: relative;
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--member-border);
        border-radius: 18px;
        background: var(--member-panel);
        color: var(--member-text);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        transition: 0.22s ease;
    }

    .member-room-page .room-card:hover {
        border-color: var(--member-yellow);
        transform: translateY(-5px);
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.32);
    }

    .member-room-page .room-card.occupied::before,
    .member-room-page .room-card.available::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .member-room-page .room-card.occupied::before {
        background: var(--member-yellow);
    }

    .member-room-page .room-card.available::before {
        background: #4b4b4b;
    }

    .member-room-page .room-card .card-body {
        display: flex;
        flex-direction: column;
        padding: 1.1rem;
    }

    .member-room-page .room-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .member-room-page .room-number {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin: 0;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 850;
    }

    .member-room-page .room-number i {
        color: var(--member-yellow);
    }

    .member-room-page .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.32rem 0.58rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
    }

    .member-room-page .status-occupied {
        border: 1px solid rgba(255, 193, 7, 0.35);
        background: rgba(255, 193, 7, 0.12);
        color: var(--member-yellow);
    }

    .member-room-page .status-available {
        border: 1px solid #444;
        background: rgba(255, 255, 255, 0.04);
        color: #999;
    }

    .member-room-page .member-avatar {
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 16px;
        background: linear-gradient(
            135deg,
            var(--member-yellow),
            var(--member-yellow-soft)
        );
        color: #111;
        font-size: 1.15rem;
        font-weight: 900;
    }

    .member-room-page .member-name {
        margin: 0;
        overflow: hidden;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .member-room-page .member-email {
        margin-top: 0.25rem;
        overflow: hidden;
        color: var(--member-muted);
        font-size: 0.78rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .member-room-page .empty-room {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 92px;
        color: #777;
        text-align: center;
    }

    .member-room-page .empty-room i {
        display: block;
        margin-bottom: 0.45rem;
        color: #555;
        font-size: 1.6rem;
    }

    .member-room-page .room-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.45rem;
        margin-top: auto;
        padding-top: 1rem;
    }

    .member-room-page .room-action-btn {
        min-height: 37px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        padding: 0.45rem;
        border-radius: 10px;
        font-size: 0.76rem;
        font-weight: 750;
        text-decoration: none;
        transition: 0.18s ease;
    }

    .member-room-page .btn-view {
        border: 1px solid var(--member-yellow);
        background: rgba(255, 193, 7, 0.08);
        color: var(--member-yellow);
    }

    .member-room-page .btn-view:hover {
        background: var(--member-yellow);
        color: #111;
    }

    .member-room-page .btn-edit {
        border: 1px solid #606060;
        background: rgba(255, 255, 255, 0.04);
        color: #ddd;
    }

    .member-room-page .btn-edit:hover {
        border-color: #fff;
        background: #fff;
        color: #111;
    }

    .member-room-page .btn-delete {
        width: 100%;
        border: 1px solid rgba(220, 53, 69, 0.7);
        background: rgba(220, 53, 69, 0.08);
        color: #ff8792;
    }

    .member-room-page .btn-delete:hover {
        border-color: var(--member-danger);
        background: var(--member-danger);
        color: #fff;
    }

    .member-room-page .staff-section {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--member-border);
    }

    .member-room-page .staff-card {
        max-width: 420px;
    }

    .member-room-page .no-result {
        display: none;
        margin-top: 1.2rem;
        padding: 2rem;
        border: 1px dashed #484848;
        border-radius: 18px;
        background: var(--member-panel);
        color: var(--member-muted);
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .member-room-page .member-hero {
            padding: 1rem;
        }

        .member-room-page .hero-icon {
            width: 51px;
            height: 51px;
            border-radius: 16px;
        }

        .member-room-page .hero-title {
            font-size: 1.25rem;
        }

        .member-room-page .hero-actions {
            width: 100%;
        }

        .member-room-page .hero-actions .btn-yellow,
        .member-room-page .hero-actions .btn-dark-outline {
            flex: 1;
        }

        .member-room-page .room-actions {
            grid-template-columns: 1fr;
        }

        .member-room-page .room-action-btn {
            min-height: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="member-room-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="member-hero">
                <div class="hero-content">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="hero-icon">
                                <i class="bi bi-building"></i>
                            </span>

                            <div>
                                <h2 class="hero-title">
                                    รายชื่อสมาชิกตามห้อง
                                </h2>

                                <p class="hero-subtitle">
                                    ตรวจสอบห้องว่าง ดูข้อมูลสมาชิก และจัดการข้อมูลสมาชิกแต่ละห้อง
                                </p>
                            </div>
                        </div>

                        <div class="hero-actions">
                            <a
                                href="{{ route('members.create') }}"
                                class="btn-yellow"
                            >
                                <i class="bi bi-person-plus-fill"></i>
                                เพิ่มสมาชิก
                            </a>

                            <button
                                type="button"
                                class="btn-dark-outline"
                                id="clearMemberSearch"
                            >
                                <i class="bi bi-arrow-clockwise"></i>
                                ล้างการค้นหา
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-buildings"></i>
                            </span>

                            <div>
                                <div class="summary-label">จำนวนชั้น</div>
                                <div class="summary-value">
                                    {{ number_format($totalFloors) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-door-open-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">ห้องทั้งหมด</div>
                                <div class="summary-value">
                                    {{ number_format($totalRooms) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-people-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">มีสมาชิก</div>
                                <div class="summary-value">
                                    {{ number_format($occupiedRooms) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">ห้องว่าง</div>
                                <div class="summary-value">
                                    {{ number_format($availableRooms) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="control-panel">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8">
                        <label
                            for="memberRoomSearch"
                            class="form-label small fw-bold text-light"
                        >
                            ค้นหาสมาชิกหรือหมายเลขห้อง
                        </label>

                        <div class="search-group">
                            <i class="bi bi-search search-icon"></i>

                            <input
                                type="search"
                                id="memberRoomSearch"
                                class="form-control search-input"
                                placeholder="ค้นหาจากชื่อ อีเมล หรือเลขห้อง..."
                            >
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label
                            for="roomStatusFilter"
                            class="form-label small fw-bold text-light"
                        >
                            สถานะห้อง
                        </label>

                        <select
                            id="roomStatusFilter"
                            class="form-select search-input"
                        >
                            <option value="all">แสดงทั้งหมด</option>
                            <option value="occupied">ห้องที่มีสมาชิก</option>
                            <option value="available">ห้องว่าง</option>
                        </select>
                    </div>
                </div>
            </section>

            <div id="roomSections">
                @for ($floor = 1; $floor <= $totalFloors; $floor++)
                    @php
                        $roomCount = $roomsPerFloor[$floor - 1] ?? 24;

                        $occupiedOnFloor = 0;

                        for ($roomIndex = 1; $roomIndex <= $roomCount; $roomIndex++) {
                            $currentRoomNumber =
                                $floor . str_pad($roomIndex, 2, '0', STR_PAD_LEFT);

                            if ($members->get($currentRoomNumber)) {
                                $occupiedOnFloor++;
                            }
                        }
                    @endphp

                    <section
                        class="floor-section"
                        data-floor-section
                    >
                        <div class="floor-header">
                            <h3 class="floor-title">
                                <i class="bi bi-layers-fill"></i>
                                ชั้น {{ $floor }}
                            </h3>

                            <span class="floor-count">
                                {{ $occupiedOnFloor }}/{{ $roomCount }} ห้องมีสมาชิก
                            </span>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 row-cols-xxl-4 g-3">
                            @for ($room = 1; $room <= $roomCount; $room++)
                                @php
                                    $roomNumber =
                                        $floor . str_pad($room, 2, '0', STR_PAD_LEFT);

                                    $member = $members->get($roomNumber);

                                    $searchText = strtolower(
                                        $roomNumber . ' ' .
                                        ($member?->name ?? '') . ' ' .
                                        ($member?->email ?? '')
                                    );
                                @endphp

                                <div
                                    class="col room-column"
                                    data-room-card
                                    data-status="{{ $member ? 'occupied' : 'available' }}"
                                    data-search="{{ $searchText }}"
                                >
                                    <article
                                        class="card room-card {{ $member ? 'occupied' : 'available' }}"
                                    >
                                        <div class="card-body">
                                            <div class="room-card-header">
                                                <h4 class="room-number">
                                                    <i class="bi bi-door-closed-fill"></i>
                                                    ห้อง {{ $roomNumber }}
                                                </h4>

                                                @if ($member)
                                                    <span class="status-badge status-occupied">
                                                        <i class="bi bi-person-check-fill"></i>
                                                        มีสมาชิก
                                                    </span>
                                                @else
                                                    <span class="status-badge status-available">
                                                        <i class="bi bi-house-dash"></i>
                                                        ห้องว่าง
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($member)
                                                <div class="d-flex align-items-center gap-3 mb-3">
                                                    <span class="member-avatar">
                                                        {{ mb_strtoupper(
                                                            mb_substr($member->name, 0, 1)
                                                        ) }}
                                                    </span>

                                                    <div class="min-w-0 flex-grow-1">
                                                        <h5
                                                            class="member-name"
                                                            title="{{ $member->name }}"
                                                        >
                                                            {{ $member->name }}
                                                        </h5>

                                                        <div
                                                            class="member-email"
                                                            title="{{ $member->email }}"
                                                        >
                                                            <i class="bi bi-envelope me-1"></i>
                                                            {{ $member->email ?: 'ไม่ได้ระบุอีเมล' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="room-actions">
                                                    <a
                                                        href="{{ route('members.show', $member->id) }}"
                                                        class="room-action-btn btn-view"
                                                    >
                                                        <i class="bi bi-eye-fill"></i>
                                                        ดู
                                                    </a>

                                                    @if (auth()->user()?->role === 'admin')
                                                        <a
                                                            href="{{ route('members.edit', $member->id) }}"
                                                            class="room-action-btn btn-edit"
                                                        >
                                                            <i class="bi bi-pencil-square"></i>
                                                            แก้ไข
                                                        </a>

                                                        <form
                                                            action="{{ route('members.destroy', $member->id) }}"
                                                            method="POST"
                                                            class="member-delete-form"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="room-action-btn btn-delete"
                                                                data-member-name="{{ $member->name }}"
                                                                data-room-number="{{ $roomNumber }}"
                                                            >
                                                                <i class="bi bi-trash-fill"></i>
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="empty-room">
                                                    <div>
                                                        <i class="bi bi-person-dash"></i>
                                                        ยังไม่มีสมาชิกในห้องนี้
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                </div>
                            @endfor
                        </div>
                    </section>
                @endfor

                @php
                    $staffRoomNumber = '601';
                    $staffMember = $members->get($staffRoomNumber);
                @endphp

                <section
                    class="staff-section"
                    data-floor-section
                >
                    <div class="floor-header">
                        <h3 class="floor-title">
                            <i class="bi bi-person-badge-fill"></i>
                            สำหรับ Admin / Staff
                        </h3>

                        <span class="floor-count">
                            ห้องพนักงาน
                        </span>
                    </div>

                    <div class="staff-card">
                        <div
                            class="room-column"
                            data-room-card
                            data-status="{{ $staffMember ? 'occupied' : 'available' }}"
                            data-search="{{
                                strtolower(
                                    '601 ห้องพนักงาน ' .
                                    ($staffMember?->name ?? '') . ' ' .
                                    ($staffMember?->email ?? '')
                                )
                            }}"
                        >
                            <article
                                class="card room-card {{ $staffMember ? 'occupied' : 'available' }}"
                            >
                                <div class="card-body">
                                    <div class="room-card-header">
                                        <h4 class="room-number">
                                            <i class="bi bi-shield-lock-fill"></i>
                                            ห้องพนักงาน
                                        </h4>

                                        @if ($staffMember)
                                            <span class="status-badge status-occupied">
                                                <i class="bi bi-person-check-fill"></i>
                                                มีผู้ใช้งาน
                                            </span>
                                        @else
                                            <span class="status-badge status-available">
                                                <i class="bi bi-person-dash"></i>
                                                ไม่มีผู้ใช้งาน
                                            </span>
                                        @endif
                                    </div>

                                    @if ($staffMember)
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="member-avatar">
                                                {{ mb_strtoupper(
                                                    mb_substr($staffMember->name, 0, 1)
                                                ) }}
                                            </span>

                                            <div class="min-w-0 flex-grow-1">
                                                <h5 class="member-name">
                                                    {{ $staffMember->name }}
                                                </h5>

                                                <div class="member-email">
                                                    <i class="bi bi-envelope me-1"></i>
                                                    {{ $staffMember->email ?: 'ไม่ได้ระบุอีเมล' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="empty-room">
                                            <div>
                                                <i class="bi bi-person-dash"></i>
                                                ไม่มีข้อมูลพนักงาน
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        </div>
                    </div>
                </section>
            </div>

            <div class="no-result" id="memberNoResult">
                <i class="bi bi-search fs-2 d-block mb-2 text-warning"></i>
                ไม่พบสมาชิกหรือห้องที่ตรงกับการค้นหา
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('memberRoomSearch');
    const statusFilter = document.getElementById('roomStatusFilter');
    const clearButton = document.getElementById('clearMemberSearch');
    const noResult = document.getElementById('memberNoResult');

    function filterRooms() {
        const searchValue = (
            searchInput?.value ?? ''
        ).trim().toLowerCase();

        const statusValue = statusFilter?.value ?? 'all';

        let visibleRooms = 0;

        document.querySelectorAll('[data-room-card]').forEach(function (room) {
            const roomSearch = (
                room.dataset.search ?? ''
            ).toLowerCase();

            const roomStatus = room.dataset.status ?? 'available';

            const matchesSearch =
                searchValue === '' ||
                roomSearch.includes(searchValue);

            const matchesStatus =
                statusValue === 'all' ||
                roomStatus === statusValue;

            const shouldShow = matchesSearch && matchesStatus;

            room.style.display = shouldShow ? '' : 'none';

            if (shouldShow) {
                visibleRooms++;
            }
        });

        document.querySelectorAll('[data-floor-section]').forEach(function (section) {
            const visibleCards = Array.from(
                section.querySelectorAll('[data-room-card]')
            ).some(function (card) {
                return card.style.display !== 'none';
            });

            section.style.display = visibleCards ? '' : 'none';
        });

        if (noResult) {
            noResult.style.display = visibleRooms === 0 ? 'block' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterRooms);
    statusFilter?.addEventListener('change', filterRooms);

    clearButton?.addEventListener('click', function () {
        if (searchInput) {
            searchInput.value = '';
        }

        if (statusFilter) {
            statusFilter.value = 'all';
        }

        filterRooms();
        searchInput?.focus();
    });

    document.querySelectorAll('.member-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const deleteButton = form.querySelector('.btn-delete');

            const memberName =
                deleteButton?.dataset.memberName ?? 'สมาชิกนี้';

            const roomNumber =
                deleteButton?.dataset.roomNumber ?? '-';

            if (typeof Swal === 'undefined') {
                if (
                    window.confirm(
                        `ยืนยันการลบ ${memberName} ออกจากห้อง ${roomNumber}?`
                    )
                ) {
                    form.submit();
                }

                return;
            }

            Swal.fire({
                title: 'ยืนยันการลบสมาชิก?',
                html: `
                    <p class="mb-1">คุณกำลังลบ</p>
                    <strong style="color:#ffc107;">
                        ${memberName}
                    </strong>
                    <p class="mt-2 mb-0">
                        ห้อง ${roomNumber}
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบสมาชิก',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                background: '#171717',
                color: '#ffffff',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#555555'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                deleteButton.disabled = true;
                deleteButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm"></span>
                    กำลังลบ
                `;

                form.submit();
            });
        });
    });

    filterRooms();
});
</script>
@endpush

