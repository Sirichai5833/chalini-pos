
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
        title="เกิดข้อผิดพลาด"
        text="{{ session('error') }}"
        confirm-button-text="ตกลง"
    />
@endif

<style>
    .member-detail-page {
        --detail-black: #0b0b0b;
        --detail-panel: #171717;
        --detail-panel-soft: #202020;
        --detail-yellow: #ffc107;
        --detail-yellow-soft: #ffdc68;
        --detail-border: #393939;
        --detail-text: #f7f7f7;
        --detail-muted: #a9a9a9;
        color: var(--detail-text);
    }

    .member-detail-page .page-shell {
        max-width: 1050px;
        margin: 0 auto;
    }

    .member-detail-page .detail-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.26);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(255, 193, 7, 0.2),
                transparent 25%
            ),
            linear-gradient(135deg, #1d1d1d, #0b0b0b);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }

    .member-detail-page .detail-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .member-detail-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .member-detail-page .member-avatar {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 21px;
        background: linear-gradient(
            135deg,
            var(--detail-yellow),
            var(--detail-yellow-soft)
        );
        color: #111;
        font-size: 1.75rem;
        font-weight: 900;
        box-shadow: 0 12px 28px rgba(255, 193, 7, 0.23);
    }

    .member-detail-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
        letter-spacing: 0.2px;
    }

    .member-detail-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--detail-muted);
        font-size: 0.92rem;
    }

    .member-detail-page .room-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        margin-top: 0.65rem;
        padding: 0.4rem 0.75rem;
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.09);
        color: var(--detail-yellow);
        font-size: 0.8rem;
        font-weight: 800;
    }

    .member-detail-page .main-card {
        overflow: hidden;
        border: 1px solid var(--detail-border);
        border-radius: 22px;
        background: var(--detail-panel);
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.35);
    }

    .member-detail-page .card-header-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--detail-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .member-detail-page .section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin: 0;
        color: var(--detail-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .member-detail-page .section-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: rgba(255, 193, 7, 0.12);
    }

    .member-detail-page .member-status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.65rem;
        border: 1px solid rgba(255, 193, 7, 0.28);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--detail-yellow);
        font-size: 0.72rem;
        font-weight: 800;
    }

    .member-detail-page .card-body-custom {
        padding: 1.5rem;
    }

    .member-detail-page .info-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--detail-border);
        border-radius: 16px;
        background: var(--detail-panel-soft);
        transition: 0.2s ease;
    }

    .member-detail-page .info-card:hover {
        border-color: rgba(255, 193, 7, 0.55);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .member-detail-page .info-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.65rem;
    }

    .member-detail-page .info-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: rgba(255, 193, 7, 0.1);
        color: var(--detail-yellow);
        font-size: 1rem;
    }

    .member-detail-page .info-label {
        color: var(--detail-muted);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .member-detail-page .info-value {
        overflow-wrap: anywhere;
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
    }

    .member-detail-page .info-value.large {
        font-size: 1.15rem;
    }

    .member-detail-page .info-link {
        color: #fff;
        text-decoration: none;
        transition: 0.18s ease;
    }

    .member-detail-page .info-link:hover {
        color: var(--detail-yellow);
    }

    .member-detail-page .action-panel {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--detail-border);
    }

    .member-detail-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .member-detail-page .btn-yellow {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.15rem;
        border: 1px solid var(--detail-yellow);
        border-radius: 12px;
        background: linear-gradient(
            135deg,
            var(--detail-yellow),
            var(--detail-yellow-soft)
        );
        color: #111;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-detail-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 9px 22px rgba(255, 193, 7, 0.22);
    }

    .member-detail-page .btn-dark-outline {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.1rem;
        border: 1px solid #4b4b4b;
        border-radius: 12px;
        background: transparent;
        color: #ddd;
        font-weight: 750;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-detail-page .btn-dark-outline:hover {
        border-color: var(--detail-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--detail-yellow);
    }

    .member-detail-page .btn-contact {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1.1rem;
        border: 1px solid rgba(255, 193, 7, 0.45);
        border-radius: 12px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--detail-yellow);
        font-weight: 750;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-detail-page .btn-contact:hover {
        background: var(--detail-yellow);
        color: #111;
    }

    @media (max-width: 767.98px) {
        .member-detail-page .detail-hero,
        .member-detail-page .card-body-custom {
            padding: 1rem;
        }

        .member-detail-page .member-avatar {
            width: 56px;
            height: 56px;
            border-radius: 17px;
            font-size: 1.4rem;
        }

        .member-detail-page .hero-title {
            font-size: 1.25rem;
        }

        .member-detail-page .card-header-custom {
            align-items: flex-start;
            padding: 1rem;
        }

        .member-detail-page .action-panel,
        .member-detail-page .action-group {
            flex-direction: column;
        }

        .member-detail-page .action-panel .btn,
        .member-detail-page .action-group .btn {
            width: 100%;
        }
    }
</style>

<div class="member-detail-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="detail-hero">
                <div class="hero-content">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="member-avatar">
                                {{ mb_strtoupper(
                                    mb_substr(
                                        $member->first_name
                                            ?: $member->name
                                            ?: 'M',
                                        0,
                                        1
                                    )
                                ) }}
                            </span>

                            <div>
                                <h2 class="hero-title">
                                    {{ $member->first_name }}
                                    {{ $member->last_name }}
                                </h2>

                                <p class="hero-subtitle">
                                    รายละเอียดข้อมูลสมาชิกและข้อมูลการติดต่อ
                                </p>

                                <span class="room-badge">
                                    <i class="bi bi-door-closed-fill"></i>
                                    ห้อง {{ $member->room_number ?? '-' }}
                                </span>
                            </div>
                        </div>

                        @if (auth()->user()?->role === 'admin')
                            <a
                                href="{{ route('members.edit', $member->id) }}"
                                class="btn-yellow"
                            >
                                <i class="bi bi-pencil-square"></i>
                                แก้ไขข้อมูล
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <section class="main-card">
                <div class="card-header-custom">
                    <h3 class="section-title">
                        <i class="bi bi-person-vcard-fill"></i>
                        ข้อมูลสมาชิก
                    </h3>

                    <span class="member-status">
                        <i class="bi bi-check-circle-fill"></i>
                        สมาชิก
                    </span>
                </div>

                <div class="card-body-custom">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-person-fill"></i>
                                    </span>

                                    <div>
                                        <div class="info-label">ชื่อ</div>
                                        <div class="info-value large">
                                            {{ $member->first_name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-person-lines-fill"></i>
                                    </span>

                                    <div>
                                        <div class="info-label">นามสกุล</div>
                                        <div class="info-value large">
                                            {{ $member->last_name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-telephone-fill"></i>
                                    </span>

                                    <div>
                                        <div class="info-label">
                                            เบอร์โทรศัพท์
                                        </div>

                                        <div class="info-value">
                                            @if ($member->phone)
                                                <a
                                                    href="tel:{{ $member->phone }}"
                                                    class="info-link"
                                                >
                                                    {{ $member->phone }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>

                                    <div class="min-w-0">
                                        <div class="info-label">อีเมล</div>

                                        <div class="info-value">
                                            @if ($member->email)
                                                <a
                                                    href="mailto:{{ $member->email }}"
                                                    class="info-link"
                                                >
                                                    {{ $member->email }}
                                                </a>
                                            @else
                                                ไม่ได้ระบุอีเมล
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-door-open-fill"></i>
                                    </span>

                                    <div>
                                        <div class="info-label">เลขห้อง</div>
                                        <div class="info-value large">
                                            {{ $member->room_number ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <span class="info-icon">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </span>

                                    <div>
                                        <div class="info-label">
                                            วันที่เข้าร่วม
                                        </div>

                                        <div class="info-value">
                                            {{ $member->created_at
                                                ? $member->created_at->format('d/m/Y H:i')
                                                : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="action-panel">
                        <div class="action-group">
                            @if ($member->phone)
                                <a
                                    href="tel:{{ $member->phone }}"
                                    class="btn-contact"
                                >
                                    <i class="bi bi-telephone-fill"></i>
                                    โทรหาสมาชิก
                                </a>
                            @endif

                           
                        </div>

                        <div class="action-group">
                            <a
                                href="{{ route('members.index') }}"
                                class="btn-dark-outline"
                            >
                                <i class="bi bi-arrow-left"></i>
                                กลับหน้าสมาชิก
                            </a>

                            @if (auth()->user()?->role === 'admin')
                                <a
                                    href="{{ route('members.edit', $member->id) }}"
                                    class="btn-yellow"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                    แก้ไขข้อมูล
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

@endsection

