@extends('layouts.layout')

@section('content')
<style>
    .online-users-page {
        --ou-yellow: #ffc107;
        --ou-bg: #090909;
        --ou-panel: #171717;
        --ou-soft: #202020;
        --ou-border: rgba(255, 255, 255, .09);
        --ou-text: #f5f5f5;
        --ou-muted: #a8a8a8;
        color: var(--ou-text);
    }

    .online-users-page .page-shell {
        max-width: 1440px;
        margin: 0 auto;
    }

    .online-users-page .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1.25rem 1.4rem;
        border: 1px solid rgba(255, 193, 7, .28);
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 90% 5%,
                rgba(255, 193, 7, .18),
                transparent 28%
            ),
            linear-gradient(135deg, #202020, #090909);
    }

    .online-users-page .hero-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.25rem, 2vw, 1.75rem);
        font-weight: 900;
    }

    .online-users-page .hero-subtitle {
        margin: .35rem 0 0;
        color: var(--ou-muted);
        font-size: .82rem;
    }

    .online-users-page .live-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .8rem;
        border: 1px solid rgba(25, 135, 84, .35);
        border-radius: 999px;
        background: rgba(25, 135, 84, .10);
        color: #72e5a2;
        font-size: .75rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .online-users-page .live-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, .12);
    }

    .online-users-page .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .8rem;
        margin-bottom: 1rem;
    }

    .online-users-page .summary-card {
        padding: 1rem;
        border: 1px solid var(--ou-border);
        border-radius: 17px;
        background: var(--ou-panel);
    }

    .online-users-page .summary-label {
        color: var(--ou-muted);
        font-size: .73rem;
    }

    .online-users-page .summary-value {
        margin-top: .25rem;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 950;
    }

    .online-users-page .panel {
        overflow: hidden;
        border: 1px solid var(--ou-border);
        border-radius: 20px;
        background: var(--ou-panel);
    }

    .online-users-page .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--ou-border);
        background: #121212;
    }

    .online-users-page .filter-form {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .online-users-page .form-select {
        min-width: 165px;
        border-color: #444;
        background-color: #0d0d0d;
        color: #fff;
    }

    .online-users-page .btn-refresh {
        border: 1px solid var(--ou-yellow);
        background: var(--ou-yellow);
        color: #111;
        font-weight: 850;
    }

    .online-users-page .table {
        --bs-table-bg: transparent;
        --bs-table-color: #eee;
        --bs-table-border-color: #303030;
        margin: 0;
    }

    .online-users-page .table thead th {
        padding: .85rem;
        background: #101010;
        color: var(--ou-yellow);
        font-size: .73rem;
        white-space: nowrap;
    }

    .online-users-page .table td {
        padding: .85rem;
        vertical-align: middle;
    }

    .online-users-page .avatar {
        width: 43px;
        height: 43px;
        display: grid;
        place-items: center;
        overflow: hidden;
        flex: none;
        border: 1px solid rgba(255, 193, 7, .25);
        border-radius: 13px;
        background: #252525;
        color: var(--ou-yellow);
        font-weight: 900;
    }

    .online-users-page .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .online-users-page .user-name {
        color: #fff;
        font-weight: 850;
    }

    .online-users-page .user-email {
        color: var(--ou-muted);
        font-size: .72rem;
    }

    .online-users-page .badge-role,
    .online-users-page .badge-shift {
        display: inline-flex;
        align-items: center;
        padding: .38rem .58rem;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 850;
    }

    .online-users-page .badge-role {
        background: rgba(255, 193, 7, .10);
        color: var(--ou-yellow);
    }

    .online-users-page .badge-shift.open {
        background: rgba(25, 135, 84, .12);
        color: #65dda0;
    }

    .online-users-page .badge-shift.closed {
        background: rgba(108, 117, 125, .12);
        color: #aaa;
    }

    .online-users-page .muted {
        color: var(--ou-muted);
        font-size: .72rem;
    }

    .online-users-page .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--ou-muted);
    }

    @media (max-width: 991.98px) {
        .online-users-page .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .online-users-page .hero,
        .online-users-page .panel-header {
            align-items: stretch;
            flex-direction: column;
        }

        .online-users-page .filter-form {
            align-items: stretch;
            flex-direction: column;
        }

        .online-users-page .form-select,
        .online-users-page .btn-refresh {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .online-users-page .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="online-users-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="hero">
                <div>
                    <h1 class="hero-title">
                        <i class="bi bi-people-fill me-2"></i>
                        ผู้ใช้งานออนไลน์ปัจจุบัน
                    </h1>

                    <p class="hero-subtitle">
                        แสดงผู้ใช้ที่มีกิจกรรมภายใน
                        {{ $onlineMinutes }} นาทีล่าสุด
                        และรีเฟรชอัตโนมัติทุก 30 วินาที
                    </p>
                </div>

                <div class="live-chip">
                    <span class="live-dot"></span>
                    กำลังออนไลน์ {{ $summary['total_online'] }} คน
                </div>
            </section>

            <section class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">
                        ออนไลน์ทั้งหมด
                    </div>
                    <div class="summary-value">
                        {{ number_format($summary['total_online']) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">
                        กำลังเปิดกะ
                    </div>
                    <div class="summary-value">
                        {{ number_format($summary['open_shift_count']) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">
                        พนักงานออนไลน์
                    </div>
                    <div class="summary-value">
                        {{ number_format($summary['staff_count']) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">
                        ผู้ดูแลออนไลน์
                    </div>
                    <div class="summary-value">
                        {{ number_format($summary['admin_count']) }}
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <div class="fw-bold text-white">
                            รายชื่อผู้ใช้งาน
                        </div>
                        <div class="muted mt-1">
                            อัปเดตล่าสุด:
                            {{ now()->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('admin.online-users.index') }}"
                        class="filter-form"
                    >
                        <select
                            name="minutes"
                            class="form-select"
                            aria-label="ช่วงเวลาออนไลน์"
                        >
                            @foreach ([1, 3, 5, 10, 15, 30, 60] as $minute)
                                <option
                                    value="{{ $minute }}"
                                    {{ $onlineMinutes === $minute ? 'selected' : '' }}
                                >
                                    ภายใน {{ $minute }} นาที
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="btn btn-refresh"
                        >
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            รีเฟรช
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ผู้ใช้งาน</th>
                                <th>บทบาท</th>
                                <th>อุปกรณ์</th>
                                {{-- <th>สถานะกะ</th> --}}
                                <th>ใช้งานล่าสุด</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($onlineUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar">
                                                @if (!empty($user->image))
                                                    <img
                                                        src="{{ asset('storage/' . ltrim($user->image, '/')) }}"
                                                        alt="{{ $user->display_name }}"
                                                        onerror="this.remove();"
                                                    >
                                                @else
                                                    {{ mb_substr($user->display_name, 0, 1) }}
                                                @endif
                                            </div>

                                            <div>
                                                <div class="user-name">
                                                    {{ $user->display_name }}

                                                    @if ((int) $user->id === (int) auth()->id())
                                                        <span class="badge text-bg-warning ms-1">
                                                            คุณ
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="user-email">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge-role">
                                            {{ $user->role_label }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="text-white">
                                            {{ $user->device }}
                                        </div>
                                        <div class="muted">
                                            {{ $user->browser }}
                                        </div>
                                    </td>

                                    {{-- <td>
                                        @if ($user->open_shift_id)
                                            <span class="badge-shift open">
                                                <i class="bi bi-circle-fill me-1"></i>
                                                กำลังเปิดกะ
                                            </span>

                                            <div class="muted mt-1">
                                                ตั้งแต่
                                                {{
                                                    optional(
                                                        \Carbon\Carbon::parse(
                                                            $user->opened_at
                                                        )
                                                    )->format('H:i')
                                                }}
                                            </div>
                                        @else
                                            <span class="badge-shift closed">
                                                ไม่ได้เปิดกะ
                                            </span>
                                        @endif
                                    </td> --}}

                                    <td>
                                        <div class="text-white">
                                            {{ $user->last_seen_at->diffForHumans() }}
                                        </div>
                                        <div class="muted">
                                            {{ $user->last_seen_at->format('H:i:s') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                                            ไม่พบผู้ใช้งานออนไลน์ในช่วงเวลาที่เลือก
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    window.setTimeout(() => {
        window.location.reload();
    }, 30000);
</script>
@endsection