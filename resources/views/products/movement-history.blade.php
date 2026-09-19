@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4 movement-history-page">
    <section class="movement-hero">
        <div class="movement-hero-content">
            <div class="movement-hero-icon">
                <i class="bi bi-arrow-left-right"></i>
            </div>

            <div class="flex-grow-1">
                <div class="movement-kicker">STOCK MOVEMENT</div>
                <h2 class="movement-title">ประวัติการเคลื่อนย้ายสินค้า</h2>
                <p class="movement-subtitle">
                    ตรวจสอบรายการย้ายคลัง การคืนสต็อกจากการยกเลิกการขาย และเหตุผลของแต่ละรายการ
                </p>
            </div>

            <div class="movement-summary-badge">
                <i class="bi bi-clock-history me-1"></i>
                {{ number_format($movements->total()) }} รายการ
            </div>
        </div>
    </section>

    <div class="movement-card mb-4">
        <div class="movement-card-header">
            <div>
                <div class="movement-card-kicker">FILTER</div>
                <h5 class="movement-card-title">
                    <i class="bi bi-funnel-fill me-2"></i>
                    ตัวกรองการค้นหา
                </h5>
            </div>
        </div>

        <div class="movement-card-body">
            <form
                action="{{ route('product.movement-history') }}"
                method="GET"
                class="row g-3 align-items-end"
            >
                <div class="col-xl-4 col-md-6">
                    <label for="search" class="form-label">
                        ค้นหาชื่อสินค้า
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            placeholder="ชื่อสินค้า "
                            value="{{ $search }}"
                        >
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label for="from" class="form-label">
                        ตั้งแต่วันที่
                    </label>

                    <input
                        type="date"
                        name="from"
                        id="from"
                        class="form-control"
                        value="{{ $from }}"
                    >
                </div>

                <div class="col-xl-3 col-md-6">
                    <label for="to" class="form-label">
                        ถึงวันที่
                    </label>

                    <input
                        type="date"
                        name="to"
                        id="to"
                        class="form-control"
                        value="{{ $to }}"
                    >
                </div>

                <div class="col-xl-2 col-md-6">
                    <button type="submit" class="btn btn-movement-search w-100">
                        <i class="bi bi-search me-2"></i>
                        ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="movement-card">
        <div class="movement-card-header">
            <div>
                <div class="movement-card-kicker">HISTORY</div>
                <h5 class="movement-card-title">
                    <i class="bi bi-list-check me-2"></i>
                    รายการเคลื่อนไหว
                </h5>
            </div>
        </div>

        <div class="table-responsive movement-table-wrap">
            <table class="table movement-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>วันที่/เวลา</th>
                        <th>สินค้า</th>
                        <th class="text-center">ประเภท</th>
                        <th class="text-end">จำนวน</th>
                        <th class="text-center">คลัง/ตำแหน่ง</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($movements as $m)
                        @php
                            $isCancel = str_contains($m->note ?? '', '[ยกเลิกการขาย]');
                            $isTransfer = str_contains($m->note ?? '', '[ย้ายคลัง]');
                        @endphp

                        <tr class="{{ $isCancel ? 'movement-row-cancel' : ($isTransfer ? 'movement-row-transfer' : '') }}">
                            <td>
                                <div class="movement-date">
                                    {{ $m->created_at->format('d/m/Y') }}
                                </div>
                                <small class="movement-time">
                                    {{ $m->created_at->format('H:i') }} น.
                                </small>
                            </td>

                            <td>
                                <div class="movement-product-name">
                                    {{ $m->product->name ?? 'N/A' }}
                                </div>

                                @if (!empty($m->product->barcode))
                                    <small class="movement-barcode">
                                        <i class="bi bi-upc-scan me-1"></i>
                                        {{ $m->product->barcode }}
                                    </small>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($isCancel)
                                    <span class="movement-type movement-type-cancel">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        ยกเลิกการขาย
                                    </span>
                                @elseif ($isTransfer)
                                    <span class="movement-type movement-type-transfer">
                                        <i class="bi bi-box-arrow-right"></i>
                                        ย้ายคลังสินค้า
                                    </span>
                                @else
                                    <span class="movement-type movement-type-other">
                                        <i class="bi bi-three-dots"></i>
                                        อื่น ๆ
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                <span class="movement-quantity {{ $m->type === 'in' ? 'quantity-in' : 'quantity-out' }}">
                                    {{ $m->type === 'in' ? '+' : '-' }}
                                    {{ number_format($m->quantity) }}
                                </span>

                                <small class="movement-unit">
                                    {{ $m->unit }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="movement-location">
                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                    {{ $m->location ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="movement-note">
                                    {{ trim(str_replace(
                                        ['[ยกเลิกการขาย]', '[ย้ายคลัง]'],
                                        '',
                                        $m->note ?? '-'
                                    )) ?: '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="movement-empty-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <div class="movement-empty-title">
                                    ไม่พบประวัติการเคลื่อนย้ายสินค้า
                                </div>
                                <div class="movement-empty-subtitle">
                                    ลองเปลี่ยนคำค้นหรือช่วงวันที่ใหม่
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($movements->hasPages())
            <div class="movement-pagination">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    :root {
        --movement-yellow: #ffd43b;
        --movement-yellow-soft: #ffe978;
        --movement-black: #080808;
        --movement-panel: #151515;
        --movement-border: rgba(255, 212, 59, .22);
        --movement-text: #f7f7f7;
        --movement-muted: #a8a8a8;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--movement-black);
    }

    .movement-history-page {
        max-width: 1500px;
        color: var(--movement-text);
    }

    .movement-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.5rem;
        border: 1px solid var(--movement-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 26%),
            linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
        box-shadow: 0 20px 48px rgba(0,0,0,.38);
    }

    .movement-hero::after {
        content: "";
        position: absolute;
        right: -90px;
        bottom: -150px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .movement-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .movement-hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--movement-yellow), var(--movement-yellow-soft));
        color: #111;
        font-size: 1.45rem;
        box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
    }

    .movement-kicker,
    .movement-card-kicker {
        color: var(--movement-yellow);
        font-size: .7rem;
        font-weight: 900;
        letter-spacing: .16em;
    }

    .movement-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: clamp(1.45rem, 2.8vw, 2rem);
        font-weight: 950;
    }

    .movement-subtitle {
        margin: .35rem 0 0;
        color: #c7c7c7;
        font-size: .9rem;
    }

    .movement-summary-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: none;
        padding: .65rem .9rem;
        border: 1px solid rgba(255, 212, 59, .32);
        border-radius: 999px;
        background: rgba(255, 212, 59, .09);
        color: var(--movement-yellow);
        font-size: .78rem;
        font-weight: 900;
    }

    .movement-card {
        overflow: hidden;
        border: 1px solid #2e2e2e;
        border-radius: 22px;
        background: linear-gradient(180deg, #181818, #101010);
        box-shadow: 0 18px 44px rgba(0,0,0,.32);
    }

    .movement-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--movement-border);
        background: linear-gradient(135deg, #202020, #111);
    }

    .movement-card-title {
        margin: .1rem 0 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .movement-card-body {
        padding: 1.25rem;
    }

    .movement-history-page .form-label {
        color: #ddd;
        font-size: .78rem;
        font-weight: 850;
    }

    .movement-history-page .form-control,
    .movement-history-page .input-group-text {
        min-height: 46px;
        border: 1px solid #3b3b3b;
        background: #0d0d0d;
        color: #fff;
        box-shadow: none;
    }

    .movement-history-page .input-group-text {
        border-right: 0;
        color: var(--movement-yellow);
    }

    .movement-history-page .input-group .form-control {
        border-left: 0;
    }

    .movement-history-page .form-control:focus {
        border-color: var(--movement-yellow);
        background: #0b0b0b;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .movement-history-page input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .btn-movement-search {
        min-height: 46px;
        border: 1px solid var(--movement-yellow);
        border-radius: 12px;
        background: linear-gradient(135deg, var(--movement-yellow), var(--movement-yellow-soft));
        color: #111;
        font-weight: 900;
    }

    .btn-movement-search:hover {
        color: #000;
        transform: translateY(-1px);
    }

    .movement-table-wrap {
        overflow: hidden;
    }

    .movement-table {
        --bs-table-bg: transparent;
        --bs-table-color: #eee;
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .045);
        --bs-table-hover-color: #fff;
    }

    .movement-table thead th {
        padding: .9rem .85rem;
        border-bottom: 1px solid #343434;
        background: #0c0c0c !important;
        color: var(--movement-yellow) !important;
        font-size: .72rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .movement-table td {
        padding: .9rem .85rem;
        border-color: #303030;
        color: #ececec;
    }

    .movement-row-cancel td {
        background: rgba(239, 68, 68, .025) !important;
    }

    .movement-row-transfer td {
        background: rgba(59, 130, 246, .02) !important;
    }

    .movement-date {
        color: #fff;
        font-weight: 850;
    }

    .movement-time,
    .movement-barcode,
    .movement-unit {
        color: var(--movement-muted);
    }

    .movement-product-name {
        color: #fff;
        font-weight: 900;
    }

    .movement-type {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .7rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .movement-type-cancel {
        border: 1px solid rgba(239, 68, 68, .36);
        background: rgba(239, 68, 68, .1);
        color: #fca5a5;
    }

    .movement-type-transfer {
        border: 1px solid rgba(59, 130, 246, .36);
        background: rgba(59, 130, 246, .1);
        color: #93c5fd;
    }

    .movement-type-other {
        border: 1px solid #454545;
        background: #202020;
        color: #ccc;
    }

    .movement-quantity {
        display: block;
        font-size: .95rem;
        font-weight: 950;
    }

    .quantity-in {
        color: #86efac;
    }

    .quantity-out {
        color: #fca5a5;
    }

    .movement-location {
        display: inline-flex;
        align-items: center;
        padding: .4rem .65rem;
        border: 1px solid #444;
        border-radius: 999px;
        background: #191919;
        color: #ddd;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .movement-note {
        max-width: 440px;
        color: #d3d3d3;
        line-height: 1.5;
    }

    .movement-empty-icon {
        color: var(--movement-yellow);
        font-size: 2.3rem;
    }

    .movement-empty-title {
        margin-top: .5rem;
        color: #fff;
        font-weight: 900;
    }

    .movement-empty-subtitle {
        margin-top: .2rem;
        color: var(--movement-muted);
        font-size: .85rem;
    }

    .movement-pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid #303030;
        background: #111;
    }

    .movement-pagination .pagination {
        margin-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .movement-history-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .movement-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .movement-hero-content {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .movement-hero-icon {
            width: 52px;
            height: 52px;
        }

        .movement-summary-badge {
            width: 100%;
            justify-content: center;
        }

        .movement-card {
            border-radius: 18px;
        }
    }
</style>
@endsection