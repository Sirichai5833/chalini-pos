@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 stock-check-report-page">
    <section class="report-hero">
        <div class="report-hero-content">
            <div class="report-hero-icon">
                <i class="bi bi-clipboard-data"></i>
            </div>

            <div class="flex-grow-1">
                <div class="report-kicker">STOCK CHECK REPORT</div>
                <h2 class="report-title">รายงานตรวจนับสต็อก</h2>
                <p class="report-subtitle">
                    ตรวจสอบประวัติการตรวจนับย้อนหลัง ผู้ตรวจ และรายละเอียดของแต่ละรอบ
                </p>
            </div>

            <div class="report-count-badge">
                <i class="bi bi-list-check me-1"></i>
                {{ number_format($checks->count()) }} รอบ
            </div>
        </div>
    </section>

    <div class="report-card">
        <div class="report-card-header">
            <div>
                <div class="report-card-kicker">HISTORY</div>
                <h5 class="report-card-title">
                    <i class="bi bi-clock-history me-2"></i>
                    ประวัติรอบตรวจนับ
                </h5>
            </div>

            <a
                href="{{ route('staff.stock.check.index') }}"
                class="btn btn-report-back"
            >
                <i class="bi bi-arrow-left me-1"></i>
                กลับหน้าตรวจนับ
            </a>
        </div>

        <div class="table-responsive report-table-wrap">
            <table class="table report-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">รอบ</th>
                        <th>วันที่ตรวจ</th>
                        <th>ผู้ตรวจ</th>
                        <th class="text-center" style="width: 150px;">การจัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($checks as $check)
                        <tr>
                            <td>
                                <span class="cycle-badge">
                                    #{{ $check->cycle }}
                                </span>
                            </td>

                            <td>
                                <div class="check-date">
                                    {{ \Carbon\Carbon::parse($check->check_date)->format('d/m/Y') }}
                                </div>

                                <small class="check-date-raw">
                                    {{ $check->check_date }}
                                </small>
                            </td>

                            <td>
                                <div class="checker-info">
                                    <div class="checker-avatar">
                                        {{ mb_substr($check->user->name ?? '?', 0, 1) }}
                                    </div>

                                    <div>
                                        <div class="checker-name">
                                            {{ $check->user->name ?? 'ไม่ระบุชื่อ' }}
                                        </div>

                                        <small class="checker-label">
                                            ผู้ตรวจนับสต็อก
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <a
                                    href="{{ route('staff.stock.check.detail', $check) }}"
                                    class="btn btn-report-detail"
                                >
                                    <i class="bi bi-eye-fill me-1"></i>
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="report-empty-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>

                                <div class="report-empty-title">
                                    ยังไม่มีรายงานตรวจนับสต็อก
                                </div>

                                <div class="report-empty-subtitle">
                                    เมื่อมีการบันทึกผลตรวจนับ รายการจะแสดงในหน้านี้
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (
            method_exists($checks, 'hasPages') &&
            $checks->hasPages()
        )
            <div class="report-pagination">
                {{ $checks->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    :root {
        --report-yellow: #ffd43b;
        --report-yellow-soft: #ffe978;
        --report-black: #080808;
        --report-panel: #151515;
        --report-border: rgba(255, 212, 59, .22);
        --report-text: #f7f7f7;
        --report-muted: #a8a8a8;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--report-black);
    }

    .stock-check-report-page {
        max-width: 1450px;
        color: var(--report-text);
    }

    .report-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid var(--report-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 26%),
            linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
        box-shadow: 0 20px 48px rgba(0,0,0,.38);
    }

    .report-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .report-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .report-hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--report-yellow), var(--report-yellow-soft));
        color: #111;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
    }

    .report-kicker,
    .report-card-kicker {
        color: var(--report-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .16em;
    }

    .report-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 2rem);
        font-weight: 950;
    }

    .report-subtitle {
        margin: .35rem 0 0;
        color: #c7c7c7;
        font-size: .9rem;
    }

    .report-count-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: none;
        padding: .65rem .9rem;
        border: 1px solid rgba(255, 212, 59, .32);
        border-radius: 999px;
        background: rgba(255, 212, 59, .09);
        color: var(--report-yellow);
        font-size: .78rem;
        font-weight: 900;
    }

    .report-card {
        overflow: hidden;
        border: 1px solid #2e2e2e;
        border-radius: 22px;
        background: linear-gradient(180deg, #181818, #101010);
        box-shadow: 0 18px 44px rgba(0,0,0,.32);
    }

    .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--report-border);
        background: linear-gradient(135deg, #202020, #111);
    }

    .report-card-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .btn-report-back,
    .btn-report-detail {
        min-height: 42px;
        border-radius: 11px;
        font-weight: 850;
    }

    .btn-report-back {
        border: 1px solid #454545;
        background: #191919;
        color: #ddd;
    }

    .btn-report-back:hover {
        border-color: var(--report-yellow);
        color: var(--report-yellow);
    }

    .btn-report-detail {
        border: 1px solid rgba(255, 212, 59, .34);
        background: rgba(255, 212, 59, .08);
        color: var(--report-yellow);
    }

    .btn-report-detail:hover {
        background: var(--report-yellow);
        color: #111;
    }

    .report-table {
        --bs-table-bg: transparent;
        --bs-table-color: #eee;
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .045);
        --bs-table-hover-color: #fff;
    }

    .report-table thead th {
        padding: .9rem .85rem;
        border-bottom: 1px solid #343434;
        background: #0c0c0c !important;
        color: var(--report-yellow) !important;
        font-size: .72rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .report-table td {
        padding: .9rem .85rem;
        border-color: #303030;
        color: #ececec;
    }

    .cycle-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        padding: .45rem .65rem;
        border: 1px solid rgba(255, 212, 59, .3);
        border-radius: 999px;
        background: rgba(255, 212, 59, .08);
        color: var(--report-yellow);
        font-size: .75rem;
        font-weight: 900;
    }

    .check-date {
        color: #fff;
        font-weight: 900;
    }

    .check-date-raw,
    .checker-label {
        color: var(--report-muted);
    }

    .checker-info {
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .checker-avatar {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        flex: none;
        border: 1px solid rgba(255, 212, 59, .25);
        border-radius: 12px;
        background: rgba(255, 212, 59, .08);
        color: var(--report-yellow);
        font-weight: 900;
    }

    .checker-name {
        color: #fff;
        font-weight: 900;
    }

    .report-empty-icon {
        color: var(--report-yellow);
        font-size: 2.4rem;
    }

    .report-empty-title {
        margin-top: .6rem;
        color: #fff;
        font-weight: 900;
    }

    .report-empty-subtitle {
        margin-top: .25rem;
        color: var(--report-muted);
        font-size: .85rem;
    }

    .report-pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid #303030;
        background: #111;
    }

    @media (max-width: 767.98px) {
        .stock-check-report-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .report-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .report-hero-content {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .report-hero-icon {
            width: 52px;
            height: 52px;
        }

        .report-count-badge {
            width: 100%;
            justify-content: center;
        }

        .report-card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .btn-report-back {
            width: 100%;
        }
    }
</style>
@endsection