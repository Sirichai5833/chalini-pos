@extends('layouts.layout')

@section('content')

@php
    $labels = [
        'name' => 'ชื่อสมาชิก',
        'first_name' => 'ชื่อ',
        'last_name' => 'นามสกุล',
        'phone' => 'เบอร์โทรศัพท์',
        'email' => 'อีเมล',
        'room_number' => 'หมายเลขห้อง',
        'role' => 'ประเภทสมาชิก',
    ];

    $createdCount = $audits->where('event', 'created')->count();
    $updatedCount = $audits->where('event', 'updated')->count();
    $deletedCount = $audits->where('event', 'deleted')->count();
@endphp

<style>
    .member-audit-page {
        --audit-black: #0b0b0b;
        --audit-panel: #171717;
        --audit-panel-soft: #202020;
        --audit-yellow: #ffc107;
        --audit-yellow-soft: #ffdc68;
        --audit-border: #3a3a3a;
        --audit-text: #f7f7f7;
        --audit-muted: #aaa;
        --audit-danger: #dc3545;
        --audit-success: #30c878;
        color: var(--audit-text);
    }

    .member-audit-page .page-shell {
        max-width: 1500px;
        margin: 0 auto;
    }

    .member-audit-page .audit-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid rgba(255, 193, 7, 0.26);
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

    .member-audit-page .audit-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 193, 7, 0.07);
    }

    .member-audit-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .member-audit-page .hero-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 19px;
        background: linear-gradient(
            135deg,
            var(--audit-yellow),
            var(--audit-yellow-soft)
        );
        color: #111;
        font-size: 1.6rem;
        box-shadow: 0 11px 27px rgba(255, 193, 7, 0.22);
    }

    .member-audit-page .hero-title {
        margin: 0;
        color: #fff;
        font-weight: 850;
    }

    .member-audit-page .hero-subtitle {
        margin: 0.4rem 0 0;
        color: var(--audit-muted);
        font-size: 0.92rem;
    }

    .member-audit-page .summary-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--audit-border);
        border-radius: 18px;
        background: var(--audit-panel);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
    }

    .member-audit-page .summary-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 14px;
        background: rgba(255, 193, 7, 0.1);
        color: var(--audit-yellow);
        font-size: 1.15rem;
    }

    .member-audit-page .summary-label {
        color: var(--audit-muted);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .member-audit-page .summary-value {
        margin-top: 0.15rem;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 850;
        line-height: 1;
    }

    .member-audit-page .filter-panel {
        margin: 1.25rem 0;
        padding: 1rem;
        border: 1px solid var(--audit-border);
        border-radius: 18px;
        background: var(--audit-panel);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }

    .member-audit-page .form-label {
        margin-bottom: 0.5rem;
        color: #e9e9e9;
        font-size: 0.85rem;
        font-weight: 750;
    }

    .member-audit-page .input-icon-group {
        position: relative;
    }

    .member-audit-page .input-icon {
        position: absolute;
        top: 50%;
        left: 0.95rem;
        z-index: 3;
        transform: translateY(-50%);
        color: var(--audit-yellow);
        pointer-events: none;
    }

    .member-audit-page .form-control,
    .member-audit-page .form-select {
        min-height: 47px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .member-audit-page .input-icon-group .form-control,
    .member-audit-page .input-icon-group .form-select {
        padding-left: 2.65rem;
    }

    .member-audit-page .form-control:focus,
    .member-audit-page .form-select:focus {
        border-color: var(--audit-yellow);
        background: #0d0d0d;
        color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.13);
    }

    .member-audit-page input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }

    .member-audit-page .btn-yellow,
    .member-audit-page .btn-dark-outline {
        min-height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.65rem 1.15rem;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .member-audit-page .btn-yellow {
        border: 1px solid var(--audit-yellow);
        background: linear-gradient(
            135deg,
            var(--audit-yellow),
            var(--audit-yellow-soft)
        );
        color: #111;
    }

    .member-audit-page .btn-yellow:hover {
        color: #000;
        transform: translateY(-1px);
        box-shadow: 0 8px 21px rgba(255, 193, 7, 0.22);
    }

    .member-audit-page .btn-dark-outline {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .member-audit-page .btn-dark-outline:hover {
        border-color: var(--audit-yellow);
        background: rgba(255, 193, 7, 0.06);
        color: var(--audit-yellow);
    }

    .member-audit-page .table-panel {
        overflow: hidden;
        border: 1px solid var(--audit-border);
        border-radius: 20px;
        background: var(--audit-panel);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.28);
    }

    .member-audit-page .table-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid var(--audit-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .member-audit-page .table-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0;
        color: var(--audit-yellow);
        font-size: 1rem;
        font-weight: 850;
    }

    .member-audit-page .record-count {
        padding: 0.35rem 0.65rem;
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 999px;
        background: rgba(255, 193, 7, 0.08);
        color: var(--audit-yellow);
        font-size: 0.75rem;
        font-weight: 800;
    }

    .member-audit-page .table-responsive {
        margin: 0;
    }

    .member-audit-page .audit-table {
        min-width: 1000px;
        margin: 0;
        color: var(--audit-text);
        border-color: var(--audit-border);
        --bs-table-bg: transparent;
        --bs-table-color: var(--audit-text);
        --bs-table-hover-bg: rgba(255, 193, 7, 0.045);
        --bs-table-hover-color: #fff;
        --bs-table-border-color: var(--audit-border);
    }

    .member-audit-page .audit-table thead th {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 193, 7, 0.3);
        background: #101010;
        color: var(--audit-yellow);
        font-size: 0.8rem;
        font-weight: 800;
        white-space: nowrap;
        vertical-align: middle;
    }

    .member-audit-page .audit-table tbody td {
        padding: 1rem;
        border-color: var(--audit-border);
        vertical-align: middle;
    }

    .member-audit-page .event-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        min-width: 105px;
        padding: 0.42rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 850;
    }

    .member-audit-page .event-created {
        border: 1px solid rgba(48, 200, 120, 0.4);
        background: rgba(48, 200, 120, 0.1);
        color: #55df98;
    }

    .member-audit-page .event-updated {
        border: 1px solid rgba(255, 193, 7, 0.4);
        background: rgba(255, 193, 7, 0.1);
        color: var(--audit-yellow);
    }

    .member-audit-page .event-deleted {
        border: 1px solid rgba(220, 53, 69, 0.45);
        background: rgba(220, 53, 69, 0.1);
        color: #ff8590;
    }

    .member-audit-page .member-cell {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .member-audit-page .member-avatar {
        width: 41px;
        height: 41px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 13px;
        background: linear-gradient(
            135deg,
            var(--audit-yellow),
            var(--audit-yellow-soft)
        );
        color: #111;
        font-weight: 900;
    }

    .member-audit-page .member-name {
        max-width: 220px;
        overflow: hidden;
        color: #fff;
        font-size: 0.88rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .member-audit-page .change-list {
        display: grid;
        gap: 0.65rem;
    }

    .member-audit-page .change-item {
        padding: 0.7rem;
        border: 1px solid #363636;
        border-radius: 12px;
        background: #131313;
    }

    .member-audit-page .change-label {
        margin-bottom: 0.4rem;
        color: var(--audit-yellow);
        font-size: 0.76rem;
        font-weight: 800;
    }

    .member-audit-page .change-values {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.45rem;
        font-size: 0.8rem;
    }

    .member-audit-page .old-value,
    .member-audit-page .new-value {
        max-width: 250px;
        overflow-wrap: anywhere;
        padding: 0.3rem 0.5rem;
        border-radius: 8px;
    }

    .member-audit-page .old-value {
        background: rgba(220, 53, 69, 0.09);
        color: #ff8792;
    }

    .member-audit-page .new-value {
        background: rgba(48, 200, 120, 0.09);
        color: #55df98;
    }

    .member-audit-page .change-arrow {
        color: var(--audit-yellow);
    }

    .member-audit-page .audit-message {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.5rem 0.7rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .member-audit-page .message-created {
        background: rgba(48, 200, 120, 0.08);
        color: #55df98;
    }

    .member-audit-page .message-deleted {
        background: rgba(220, 53, 69, 0.08);
        color: #ff8792;
    }

    .member-audit-page .operator-name {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #e8e8e8;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .member-audit-page .operator-name i {
        color: var(--audit-yellow);
    }

    .member-audit-page .date-cell {
        color: #ddd;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .member-audit-page .date-cell i {
        color: var(--audit-yellow);
    }

    .member-audit-page .empty-state {
        padding: 3rem 1rem;
        border: 1px dashed #474747;
        border-radius: 20px;
        background: var(--audit-panel);
        color: var(--audit-muted);
        text-align: center;
    }

    .member-audit-page .empty-icon {
        width: 70px;
        height: 70px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        border-radius: 22px;
        background: rgba(255, 193, 7, 0.09);
        color: var(--audit-yellow);
        font-size: 1.8rem;
    }

    .member-audit-page .bottom-actions {
        margin-top: 1.25rem;
    }

    .member-audit-page .pagination {
        margin-top: 1.25rem;
        margin-bottom: 0;
    }

    .member-audit-page .page-link {
        border-color: #3b3b3b;
        background: #171717;
        color: #ddd;
    }

    .member-audit-page .page-link:hover {
        border-color: var(--audit-yellow);
        background: rgba(255, 193, 7, 0.1);
        color: var(--audit-yellow);
    }

    .member-audit-page .page-item.active .page-link {
        border-color: var(--audit-yellow);
        background: var(--audit-yellow);
        color: #111;
    }

    .member-audit-page .page-item.disabled .page-link {
        background: #111;
        color: #666;
    }

    @media (max-width: 767.98px) {
        .member-audit-page .audit-hero {
            padding: 1rem;
        }

        .member-audit-page .hero-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
        }

        .member-audit-page .hero-title {
            font-size: 1.25rem;
        }

        .member-audit-page .filter-panel .btn {
            width: 100%;
        }

        .member-audit-page .table-panel-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="member-audit-page py-4">
    <div class="container-fluid">
        <div class="page-shell">

            <section class="audit-hero">
                <div class="hero-content">
                    <div class="d-flex align-items-center gap-3">
                        <span class="hero-icon">
                            <i class="bi bi-clock-history"></i>
                        </span>

                        <div>
                            <h2 class="hero-title">
                                ประวัติการแก้ไขข้อมูลสมาชิก
                            </h2>

                            <p class="hero-subtitle">
                                ตรวจสอบการเพิ่ม แก้ไข และลบข้อมูลสมาชิกภายในระบบ
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-list-check"></i>
                            </span>

                            <div>
                                <div class="summary-label">รายการทั้งหมด</div>
                                <div class="summary-value">
                                    {{ number_format($audits->count()) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-person-plus-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">เพิ่มสมาชิก</div>
                                <div class="summary-value">
                                    {{ number_format($createdCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-pencil-square"></i>
                            </span>

                            <div>
                                <div class="summary-label">แก้ไขข้อมูล</div>
                                <div class="summary-value">
                                    {{ number_format($updatedCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="summary-card">
                        <div class="d-flex align-items-center gap-3">
                            <span class="summary-icon">
                                <i class="bi bi-trash-fill"></i>
                            </span>

                            <div>
                                <div class="summary-label">ลบสมาชิก</div>
                                <div class="summary-value">
                                    {{ number_format($deletedCount) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form
                method="GET"
                action="{{ route('staff.audits') }}"
                class="filter-panel"
            >
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-lg-4">
                        <label for="date" class="form-label">
                            เลือกวันที่
                        </label>

                        <div class="input-icon-group">
                            <i class="bi bi-calendar3 input-icon"></i>

                            <input
                                type="date"
                                name="date"
                                id="date"
                                value="{{ request('date') }}"
                                class="form-control"
                            >
                        </div>
                    </div>

                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-yellow">
                            <i class="bi bi-search"></i>
                            ค้นหา
                        </button>
                    </div>

                    <div class="col-md-auto">
                        <a
                            href="{{ route('staff.audits') }}"
                            class="btn btn-dark-outline"
                        >
                            <i class="bi bi-arrow-clockwise"></i>
                            รีเซ็ต
                        </a>
                    </div>
                </div>
            </form>

            @if ($audits->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <h4 class="text-white fw-bold">
                        ไม่พบประวัติการแก้ไข
                    </h4>

                    <p class="mb-0">
                        ยังไม่มีรายการในวันที่เลือก หรือลองรีเซ็ตตัวกรองแล้วค้นหาใหม่
                    </p>
                </div>
            @else
                <section class="table-panel">
                    <div class="table-panel-header">
                        <h3 class="table-title">
                            <i class="bi bi-table"></i>
                            รายการประวัติ
                        </h3>

                        <span class="record-count">
                            {{ number_format($audits->count()) }} รายการ
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover audit-table align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th>เหตุการณ์</th>
                                    <th>สมาชิก</th>
                                    <th>รายละเอียดการเปลี่ยนแปลง</th>
                                    <th>ผู้ดำเนินการ</th>
                                    <th>วันที่ / เวลา</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($audits as $audit)
                                    @php
                                        $memberName =
                                            $audit->new_values['name']
                                            ?? $audit->old_values['name']
                                            ?? trim(
                                                ($audit->new_values['first_name']
                                                    ?? $audit->old_values['first_name']
                                                    ?? '')
                                                . ' ' .
                                                ($audit->new_values['last_name']
                                                    ?? $audit->old_values['last_name']
                                                    ?? '')
                                            )
                                            ?: '-';
                                    @endphp

                                    <tr>
                                        <td class="text-center">
                                            @switch($audit->event)
                                                @case('created')
                                                    <span class="event-badge event-created">
                                                        <i class="bi bi-person-plus-fill"></i>
                                                        เพิ่มสมาชิก
                                                    </span>
                                                    @break

                                                @case('updated')
                                                    <span class="event-badge event-updated">
                                                        <i class="bi bi-pencil-square"></i>
                                                        แก้ไขข้อมูล
                                                    </span>
                                                    @break

                                                @case('deleted')
                                                    <span class="event-badge event-deleted">
                                                        <i class="bi bi-trash-fill"></i>
                                                        ลบสมาชิก
                                                    </span>
                                                    @break

                                                @default
                                                    <span class="event-badge event-updated">
                                                        {{ ucfirst($audit->event) }}
                                                    </span>
                                            @endswitch
                                        </td>

                                        <td>
                                            <div class="member-cell">
                                                <span class="member-avatar">
                                                    {{ mb_strtoupper(
                                                        mb_substr(
                                                            $memberName !== '-'
                                                                ? $memberName
                                                                : 'M',
                                                            0,
                                                            1
                                                        )
                                                    ) }}
                                                </span>

                                                <span
                                                    class="member-name"
                                                    title="{{ $memberName }}"
                                                >
                                                    {{ $memberName }}
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            @if ($audit->event === 'created')
                                                <span class="audit-message message-created">
                                                    <i class="bi bi-person-check-fill"></i>
                                                    เพิ่มสมาชิกใหม่เข้าสู่ระบบ
                                                </span>
                                            @elseif ($audit->event === 'deleted')
                                                <span class="audit-message message-deleted">
                                                    <i class="bi bi-person-x-fill"></i>
                                                    ลบสมาชิกออกจากระบบ
                                                </span>
                                            @else
                                                @php
                                                    $hasChanges = false;
                                                @endphp

                                                <div class="change-list">
                                                    @foreach ($labels as $field => $label)
                                                        @php
                                                            $old =
                                                                $audit->old_values[$field]
                                                                ?? null;

                                                            $new =
                                                                $audit->new_values[$field]
                                                                ?? null;
                                                        @endphp

                                                        @if ($old != $new)
                                                            @php
                                                                $hasChanges = true;
                                                            @endphp

                                                            <div class="change-item">
                                                                <div class="change-label">
                                                                    {{ $label }}
                                                                </div>

                                                                <div class="change-values">
                                                                    <span class="old-value">
                                                                        {{ filled($old) ? $old : '-' }}
                                                                    </span>

                                                                    <i class="bi bi-arrow-right change-arrow"></i>

                                                                    <span class="new-value">
                                                                        {{ filled($new) ? $new : '-' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    @unless ($hasChanges)
                                                        <span class="text-muted small">
                                                            ไม่มีรายละเอียดการเปลี่ยนแปลง
                                                        </span>
                                                    @endunless
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <span class="operator-name">
                                                <i class="bi bi-person-circle"></i>
                                                {{ $audit->user?->name ?? 'ไม่ทราบ' }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="date-cell">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $audit->created_at->format('d/m/Y') }}

                                                <br>

                                                <i class="bi bi-clock me-1"></i>
                                                {{ $audit->created_at->format('H:i') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                @if (method_exists($audits, 'links'))
                    <div>
                        {{ $audits->withQueryString()->links() }}
                    </div>
                @endif
            @endif

            <div class="bottom-actions">
                <a
                    href="{{ route('staff.index') }}"
                    class="btn btn-dark-outline"
                >
                    <i class="bi bi-arrow-left"></i>
                    กลับหน้ารายชื่อ
                </a>
            </div>

        </div>
    </div>
</div>

@endsection

