@extends('layouts.layout')

@section('flash-messages')
    @include('layouts.partials.flash-messages')
@endsection

@section('content')

<style>
    .staff-index-page {
        --staff-panel: #171717;
        --staff-panel-soft: #202020;
        --staff-yellow: #ffc107;
        --staff-yellow-soft: #ffe27a;
        --staff-border: #393939;
        --staff-text: #f7f7f7;
        --staff-muted: #aaa;
        color: var(--staff-text);
    }

    .staff-index-page .page-shell {
        max-width: 1450px;
        margin: 0 auto;
    }

    .staff-index-page .page-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.27);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, 0.2),
                transparent 25%
            ),
            linear-gradient(135deg, #1d1d1d, #0b0b0b);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.32);
    }

    .staff-index-page .page-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .staff-index-page .hero-content,
    .staff-index-page .hero-actions {
        position: relative;
        z-index: 2;
    }

    .staff-index-page .hero-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 19px;
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
        font-size: 1.6rem;
        box-shadow: 0 11px 27px rgba(255, 193, 7, 0.22);
    }

    .staff-index-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .staff-index-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--staff-muted);
        font-size: 0.92rem;
    }

    .staff-index-page .btn-yellow,
    .staff-index-page .btn-dark-outline {
        min-height: 45px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.65rem 1.1rem;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .staff-index-page .btn-yellow {
        border: 1px solid var(--staff-yellow);
        background: linear-gradient(
            135deg,
            var(--staff-yellow),
            var(--staff-yellow-soft)
        );
        color: #111;
    }

    .staff-index-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .staff-index-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: rgba(255, 255, 255, 0.03);
        color: #ddd;
    }

    .staff-index-page .btn-dark-outline:hover {
        border-color: var(--staff-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--staff-yellow);
    }

    .staff-index-page .search-panel {
        margin-bottom: 1.25rem;
        padding: 1rem;
        border: 1px solid var(--staff-border);
        border-radius: 18px;
        background: var(--staff-panel);
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.25);
    }

    .staff-index-page .search-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        color: var(--staff-yellow);
        font-size: 0.9rem;
        font-weight: 800;
    }

    .staff-index-page .input-icon-group {
        position: relative;
    }

    .staff-index-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.95rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--staff-yellow);
        pointer-events: none;
    }

    .staff-index-page .form-control {
        min-height: 47px;
        padding-left: 2.65rem;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .staff-index-page .form-control::placeholder {
        color: #707070;
    }

    .staff-index-page .form-control:focus {
        border-color: var(--staff-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .staff-index-page .summary-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--staff-border);
        border-radius: 17px;
        background: var(--staff-panel);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
    }

    .staff-index-page .summary-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 13px;
        background: rgba(255, 193, 7, 0.1);
        color: var(--staff-yellow);
        font-size: 1.1rem;
    }

    .staff-index-page .summary-label {
        color: var(--staff-muted);
        font-size: 0.75rem;
        font-weight: 700;
    }

    .staff-index-page .summary-value {
        margin-top: 0.15rem;
        color: #fff;
        font-size: 1.35rem;
        font-weight: 850;
        line-height: 1;
    }

    .staff-index-page .staff-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--staff-border);
        border-radius: 20px;
        background: linear-gradient(160deg, var(--staff-panel), #111);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
        transition: 0.22s ease;
    }

    .staff-index-page .staff-card:hover {
        transform: translateY(-6px);
        border-color: rgba(255, 193, 7, 0.55);
        box-shadow: 0 22px 52px rgba(0, 0, 0, 0.45);
    }

    .staff-index-page .profile-image-wrapper {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: #0b0b0b;
    }

    .staff-index-page .profile-image-wrapper::after {
        content: "";
        position: absolute;
        inset: auto 0 0;
        height: 40%;
        pointer-events: none;
        background: linear-gradient(
            transparent,
            rgba(0, 0, 0, 0.65)
        );
    }

    .staff-index-page .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: 0.3s ease;
    }

    .staff-index-page .staff-card:hover .profile-image {
        transform: scale(1.04);
    }

    .staff-index-page .profile-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(
                circle,
                rgba(255, 193, 7, 0.13),
                transparent 65%
            ),
            #0b0b0b;
        color: var(--staff-yellow);
        font-size: 3rem;
        font-weight: 900;
    }

    .staff-index-page .role-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        z-index: 4;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.65rem;
        border-radius: 999px;
        backdrop-filter: blur(8px);
        font-size: 0.7rem;
        font-weight: 850;
    }

    .staff-index-page .role-admin {
        border: 1px solid rgba(255, 193, 7, 0.48);
        background: rgba(255, 193, 7, 0.15);
        color: var(--staff-yellow);
    }

    .staff-index-page .role-staff {
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(20, 20, 20, 0.72);
        color: #ddd;
    }

    .staff-index-page .staff-body {
        padding: 1rem;
    }

    .staff-index-page .staff-name {
        margin-bottom: 0.75rem;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 850;
    }

    .staff-index-page .info-list {
        display: grid;
        gap: 0.55rem;
    }

    .staff-index-page .info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        color: var(--staff-muted);
        font-size: 0.8rem;
        line-height: 1.45;
    }

    .staff-index-page .info-row i {
        margin-top: 2px;
        color: var(--staff-yellow);
        flex-shrink: 0;
    }

    .staff-index-page .info-value {
        overflow-wrap: anywhere;
    }

    .staff-index-page .staff-footer {
        display: flex;
        gap: 0.65rem;
        padding: 1rem;
        border-top: 1px solid var(--staff-border);
        background: var(--staff-panel-soft);
    }

    .staff-index-page .btn-edit,
    .staff-index-page .btn-delete {
        min-height: 41px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex: 1;
        border-radius: 11px;
        font-size: 0.8rem;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .staff-index-page .btn-edit {
        border: 1px solid rgba(255, 193, 7, 0.55);
        background: rgba(255, 193, 7, 0.06);
        color: var(--staff-yellow);
        text-decoration: none;
    }

    .staff-index-page .btn-edit:hover {
        background: var(--staff-yellow);
        color: #111;
    }

    .staff-index-page .btn-delete {
        border: 1px solid rgba(220, 53, 69, 0.55);
        background: rgba(220, 53, 69, 0.08);
        color: #ff8792;
    }

    .staff-index-page .btn-delete:hover {
        background: #dc3545;
        color: #fff;
    }

    .staff-index-page .empty-state {
        padding: 3rem 1rem;
        border: 1px dashed #484848;
        border-radius: 20px;
        background: var(--staff-panel);
        color: var(--staff-muted);
        text-align: center;
    }

    .staff-index-page .empty-icon {
        width: 72px;
        height: 72px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border-radius: 22px;
        background: rgba(255, 193, 7, 0.09);
        color: var(--staff-yellow);
        font-size: 1.8rem;
    }

    @media (max-width: 767.98px) {
        .staff-index-page .page-hero {
            padding: 1rem;
        }

        .staff-index-page .hero-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
        }

        .staff-index-page .hero-title {
            font-size: 1.25rem;
        }

        .staff-index-page .hero-actions {
            width: 100%;
        }

        .staff-index-page .hero-actions .btn {
            width: 100%;
        }

        .staff-index-page .search-panel .btn {
            width: 100%;
        }

        .staff-index-page .profile-image-wrapper {
            height: 240px;
        }
    }
</style>

@php
    $staffCollection = collect($staff->items() ?? $staff);

    $adminCount = $staffCollection
        ->where('role', 'admin')
        ->count();

    $employeeCount = $staffCollection
        ->where('role', 'staff')
        ->count();
@endphp

<div class="staff-index-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section
                class="page-hero d-flex flex-column flex-lg-row
                       justify-content-between align-items-lg-center gap-3"
            >
                <div class="hero-content d-flex align-items-center gap-3">
                    <span class="hero-icon">
                        <i class="bi bi-people-fill"></i>
                    </span>

                    <div>
                        <h2 class="hero-title">
                            รายชื่อพนักงาน
                        </h2>

                        <p class="hero-subtitle">
                            จัดการข้อมูลพนักงาน แอดมิน และข้อมูลการติดต่อ
                        </p>
                    </div>
                </div>

                <div class="hero-actions">
                    <a
                        href="{{ route('staff.create') }}"
                        class="btn btn-yellow"
                    >
                        <i class="bi bi-person-plus-fill"></i>
                        เพิ่มพนักงานใหม่
                    </a>
                </div>
            </section>

            <div class="row g-3 my-4">
                <div class="col-4">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-people-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">
                                    ทั้งหมด
                                </div>

                                <div class="summary-value">
                                    {{ number_format($staffCollection->count()) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-shield-fill-check"></i>
                            </span>

                            <div>
                                <div class="summary-label">
                                    แอดมิน
                                </div>

                                <div class="summary-value">
                                    {{ number_format($adminCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-person-badge-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">
                                    พนักงานขาย
                                </div>

                                <div class="summary-value">
                                    {{ number_format($employeeCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="search-panel">
                <div class="search-title">
                    <i class="bi bi-search"></i>
                    ค้นหาพนักงาน
                </div>

                <form
                    action="{{ route('staff.index') }}"
                    method="GET"
                    class="row g-3 align-items-center"
                >
                    <div class="col-md">
                        <div class="input-icon-group">
                            <i class="bi bi-person-search input-icon"></i>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="ค้นหาจากชื่อ นามสกุล อีเมล หรือเบอร์โทรศัพท์"
                                value="{{ request('search') }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-auto">
                        <button
                            type="submit"
                            class="btn btn-yellow"
                        >
                            <i class="bi bi-search"></i>
                            ค้นหา
                        </button>
                    </div>

                    @if (request()->filled('search'))
                        <div class="col-md-auto">
                            <a
                                href="{{ route('staff.index') }}"
                                class="btn btn-dark-outline"
                            >
                                <i class="bi bi-arrow-clockwise"></i>
                                รีเซ็ต
                            </a>
                        </div>
                    @endif
                </form>
            </section>

            @if ($staff->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">
                        <i class="bi bi-person-x-fill"></i>
                    </span>

                    <h4 class="text-white fw-bold">
                        ไม่พบข้อมูลพนักงาน
                    </h4>

                    <p class="mb-0">
                        ยังไม่มีพนักงานในระบบ
                        หรือไม่พบข้อมูลที่ตรงกับคำค้นหา
                    </p>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($staff as $user)
                        @php
                            $fullName = trim(
                                ($user->first_name ?? '') . ' ' .
                                ($user->last_name ?? '')
                            );

                            if ($fullName === '') {
                                $fullName = $user->name ?? '-';
                            }

                            $avatarLetter = mb_strtoupper(
                                mb_substr(
                                    $user->first_name
                                        ?? $user->name
                                        ?? 'U',
                                    0,
                                    1
                                )
                            );
                        @endphp

                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <article class="staff-card">
                                <div class="profile-image-wrapper">
                                    <span
                                        class="role-badge
                                        {{ $user->role === 'admin'
                                            ? 'role-admin'
                                            : 'role-staff' }}"
                                    >
                                        <i class="bi
                                            {{ $user->role === 'admin'
                                                ? 'bi-shield-fill-check'
                                                : 'bi-person-badge-fill' }}">
                                        </i>

                                        {{ $user->role === 'admin'
                                            ? 'แอดมิน'
                                            : 'พนักงานขาย' }}
                                    </span>

                                    @if ($user->image)
                                        <img
                                            src="{{ asset('storage/' . $user->image) }}"
                                            alt="รูปพนักงาน {{ $fullName }}"
                                            class="profile-image"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="profile-placeholder">
                                            {{ $avatarLetter }}
                                        </div>
                                    @endif
                                </div>

                                <div class="staff-body">
                                    <h5
                                        class="staff-name text-truncate"
                                        title="{{ $fullName }}"
                                    >
                                        {{ $fullName }}
                                    </h5>

                                    <div class="info-list">
                                        <div class="info-row">
                                            <i class="bi bi-envelope-fill"></i>

                                            <span class="info-value">
                                                {{ $user->email ?? '-' }}
                                            </span>
                                        </div>

                                        <div class="info-row">
                                            <i class="bi bi-telephone-fill"></i>

                                            <span class="info-value">
                                                {{ $user->phone ?? '-' }}
                                            </span>
                                        </div>

                                        <div class="info-row">
                                            <i class="bi bi-geo-alt-fill"></i>

                                            <span
                                                class="info-value"
                                                title="{{ $user->address }}"
                                            >
                                                {{ \Illuminate\Support\Str::limit(
                                                    $user->address ?? '-',
                                                    70
                                                ) }}
                                            </span>
                                        </div>

                                        <div class="info-row">
                                            <i class="bi bi-calendar-check-fill"></i>

                                            <span class="info-value">
                                                เริ่มงานเมื่อ
                                                {{ $user->created_at?->format('d/m/Y') ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="staff-footer">
                                    <a
                                        href="{{ route('staff.alledit', $user->id) }}"
                                        class="btn-edit"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        แก้ไข
                                    </a>

                                    <form
                                        action="{{ route('staff.delete', $user->id) }}"
                                        method="POST"
                                        class="flex-fill delete-staff-form"
                                        data-staff-name="{{ $fullName }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-delete w-100"
                                        >
                                            <i class="bi bi-trash-fill"></i>
                                            ลบ
                                        </button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                @if (method_exists($staff, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $staff->withQueryString()->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document
        .querySelectorAll('.delete-staff-form')
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const staffName =
                    form.dataset.staffName || 'พนักงานคนนี้';

                const submitDelete = function () {
                    const button =
                        form.querySelector('button[type="submit"]');

                    if (button) {
                        button.disabled = true;

                        button.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>
                            กำลังลบ
                        `;
                    }

                    form.submit();
                };

                if (typeof Swal === 'undefined') {
                    if (
                        window.confirm(
                            `ยืนยันการลบ ${staffName} หรือไม่?`
                        )
                    ) {
                        submitDelete();
                    }

                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการลบพนักงาน?',
                    html: `
                        ต้องการลบ
                        <strong>${staffName}</strong>
                        ออกจากระบบหรือไม่?
                        <br>
                        <small style="color:#aaa">
                            การดำเนินการนี้ไม่สามารถย้อนกลับได้
                        </small>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบพนักงาน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    background: '#171717',
                    color: '#ffffff',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#555555'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitDelete();
                    }
                });
            });
        });
});
</script>

@endsection