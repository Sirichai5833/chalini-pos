@extends('layouts.layout')

@section('content')
<style>
    .shift-summary-page {
        --ss-yellow: #ffc107;
        --ss-yellow-soft: #ffe27a;
        --ss-bg: #090909;
        --ss-panel: #171717;
        --ss-panel-soft: #202020;
        --ss-border: #353535;
        --ss-text: #f7f7f7;
        --ss-muted: #a8a8a8;
        --ss-green: #2ecc71;
        --ss-red: #ff6b6b;
        --ss-blue: #74b9ff;
        color: var(--ss-text);
    }

    .shift-summary-page .page-shell {
        max-width: 1280px;
        margin: 0 auto;
    }

    .shift-summary-page .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding: 1.4rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 8%,
                rgba(255, 193, 7, .18),
                transparent 28%
            ),
            linear-gradient(135deg, #202020, #0b0b0b);
        box-shadow: 0 22px 52px rgba(0, 0, 0, .32);
    }

    .shift-summary-page .hero-icon {
        width: 64px;
        height: 64px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 20px;
        background: linear-gradient(
            135deg,
            var(--ss-yellow),
            var(--ss-yellow-soft)
        );
        color: #111;
        font-size: 1.65rem;
    }

    .shift-summary-page .hero h2 {
        margin: 0;
        color: #fff;
        font-size: 1.5rem;
        font-weight: 900;
    }

    .shift-summary-page .hero p {
        margin: .35rem 0 0;
        color: var(--ss-muted);
        font-size: .84rem;
    }

    .shift-summary-page .hero-date {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .8rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 999px;
        background: rgba(255, 193, 7, .07);
        color: var(--ss-yellow);
        font-size: .76rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .shift-summary-page .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin: 1.25rem 0 .85rem;
    }

    .shift-summary-page .section-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .shift-summary-page .section-title i {
        color: var(--ss-yellow);
    }

    .shift-summary-page .count-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .65rem;
        border-radius: 999px;
        background: rgba(255, 193, 7, .09);
        color: var(--ss-yellow);
        font-size: .72rem;
        font-weight: 850;
    }

    .shift-summary-page .kpi-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: .9rem;
        margin-bottom: 1.25rem;
    }

    .shift-summary-page .kpi-card {
        position: relative;
        overflow: hidden;
        padding: 1.05rem;
        border: 1px solid var(--ss-border);
        border-radius: 18px;
        background: linear-gradient(180deg, #1b1b1b, #121212);
        box-shadow: 0 16px 38px rgba(0, 0, 0, .25);
    }

    .shift-summary-page .kpi-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto;
        height: 3px;
        background: var(--ss-yellow);
    }

    .shift-summary-page .kpi-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        margin-bottom: .8rem;
        border-radius: 13px;
        background: rgba(255, 193, 7, .09);
        color: var(--ss-yellow);
        font-size: 1.1rem;
    }

    .shift-summary-page .kpi-label {
        color: var(--ss-muted);
        font-size: .72rem;
    }

    .shift-summary-page .kpi-value {
        margin-top: .25rem;
        color: #fff;
        font-size: 1.25rem;
        font-weight: 900;
    }

    .shift-summary-page .filter-card,
    .shift-summary-page .table-card,
    .shift-summary-page .shift-card {
        overflow: hidden;
        border: 1px solid var(--ss-border);
        border-radius: 20px;
        background: var(--ss-panel);
        box-shadow: 0 18px 44px rgba(0, 0, 0, .26);
    }

    .shift-summary-page .filter-card {
        margin-bottom: 1rem;
    }

    .shift-summary-page .card-header-custom {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--ss-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .shift-summary-page .card-title-custom {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #fff;
        font-size: .92rem;
        font-weight: 850;
    }

    .shift-summary-page .card-title-custom i {
        color: var(--ss-yellow);
    }

    .shift-summary-page .card-body-custom {
        padding: 1.15rem;
    }

    .shift-summary-page .form-label {
        color: #e8e8e8;
        font-size: .78rem;
        font-weight: 800;
    }

    .shift-summary-page .form-control,
    .shift-summary-page .form-select {
        min-height: 47px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #0e0e0e;
        color: #fff;
        box-shadow: none;
    }

    .shift-summary-page .form-control:focus,
    .shift-summary-page .form-select:focus {
        border-color: var(--ss-yellow);
        background: #0a0a0a;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .1);
    }

    .shift-summary-page .form-select option {
        background: #111;
        color: #fff;
    }

    .shift-summary-page .btn-filter {
        min-height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        width: 100%;
        border: 1px solid var(--ss-yellow);
        border-radius: 12px;
        background: linear-gradient(
            135deg,
            var(--ss-yellow),
            var(--ss-yellow-soft)
        );
        color: #111;
        font-weight: 900;
    }

    .shift-summary-page .open-grid {
        display: grid;
        gap: 1rem;
    }

    .shift-summary-page .shift-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem;
        border-bottom: 1px solid var(--ss-border);
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, .12),
                transparent 28%
            ),
            linear-gradient(135deg, #202020, #151515);
    }

    .shift-summary-page .shift-name {
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .shift-summary-page .shift-meta {
        margin-top: .3rem;
        color: var(--ss-muted);
        font-size: .73rem;
    }

    .shift-summary-page .status-open {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .6rem;
        border-radius: 999px;
        background: rgba(46, 204, 113, .11);
        color: #7ee2aa;
        font-size: .7rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .shift-summary-page .shift-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
        padding: 1rem;
    }

    .shift-summary-page .metric-box {
        padding: .85rem;
        border: 1px solid var(--ss-border);
        border-radius: 14px;
        background: var(--ss-panel-soft);
    }

    .shift-summary-page .metric-label {
        color: var(--ss-muted);
        font-size: .7rem;
    }

    .shift-summary-page .metric-value {
        margin-top: .3rem;
        color: #fff;
        font-size: .95rem;
        font-weight: 850;
    }

    .shift-summary-page .cash-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
        margin: 0 1rem 1rem;
        padding: .9rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 15px;
        background: rgba(255, 193, 7, .045);
    }

    .shift-summary-page .cash-summary .metric-box {
        border: 0;
        background: transparent;
        padding: .45rem;
    }

    .shift-summary-page .text-positive {
        color: #7ee2aa;
    }

    .shift-summary-page .text-negative {
        color: #ff929c;
    }

    .shift-summary-page .text-blue {
        color: #8ec5ff;
    }

    .shift-summary-page .empty-state {
        padding: 2.5rem 1rem;
        text-align: center;
        color: var(--ss-muted);
    }

    .shift-summary-page .empty-icon {
        width: 70px;
        height: 70px;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        border-radius: 22px;
        background: rgba(255, 193, 7, .08);
        color: var(--ss-yellow);
        font-size: 1.65rem;
    }

    .shift-summary-page .history-table {
        margin: 0;
        color: #e8e8e8;
        vertical-align: middle;
    }

    .shift-summary-page .history-table thead th {
        padding: .85rem;
        border-bottom: 1px solid var(--ss-border);
        background: #121212;
        color: #bdbdbd;
        font-size: .7rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .shift-summary-page .history-table tbody td {
        padding: .85rem;
        border-color: #2f2f2f;
        background: transparent;
        color: #e7e7e7;
        font-size: .8rem;
    }

    .shift-summary-page .history-table tbody tr:hover td {
        background: rgba(255, 193, 7, .035);
    }

    .shift-summary-page .shift-code {
        display: inline-flex;
        padding: .32rem .5rem;
        border: 1px solid rgba(255, 193, 7, .22);
        border-radius: 8px;
        background: rgba(255, 193, 7, .07);
        color: var(--ss-yellow);
        font-size: .7rem;
        font-weight: 900;
    }

    .shift-summary-page .difference-badge {
        display: inline-flex;
        padding: .34rem .55rem;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 850;
    }

    .shift-summary-page .difference-zero {
        background: rgba(168, 168, 168, .11);
        color: #d0d0d0;
    }

    .shift-summary-page .difference-plus {
        background: rgba(46, 204, 113, .11);
        color: #7ee2aa;
    }

    .shift-summary-page .difference-minus {
        background: rgba(255, 107, 107, .11);
        color: #ff929c;
    }

    .shift-summary-page .pagination-wrap {
        padding: 1rem 1.15rem;
        border-top: 1px solid var(--ss-border);
    }

    @media (max-width: 991.98px) {
        .shift-summary-page .kpi-grid,
        .shift-summary-page .shift-metrics,
        .shift-summary-page .cash-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .shift-summary-page .hero,
        .shift-summary-page .shift-card-header,
        .shift-summary-page .section-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 575.98px) {
        .shift-summary-page .kpi-grid,
        .shift-summary-page .shift-metrics,
        .shift-summary-page .cash-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="shift-summary-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="hero">
                <div class="d-flex align-items-center gap-3">
                    <div class="hero-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>

                    <div>
                        <h2>สรุปยอดขายแต่ละกะ</h2>
                        <p>
                            ตรวจสอบกะที่กำลังเปิด ยอดรับเงินจริงรวมทุกช่องทาง
                            เงินสด QR/เงินโอน และประวัติปิดกะย้อนหลัง
                        </p>
                    </div>
                </div>

                <div class="hero-date">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </div>
            </section>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="kpi-label">ยอดขายวันนี้</div>
                    <div class="kpi-value">
                        ฿{{ number_format($totalSales ?? 0, 2) }}
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="kpi-label">จำนวนบิลวันนี้</div>
                    <div class="kpi-value">
                        {{ number_format($totalBills ?? 0) }} บิล
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="kpi-label">เงินสดรวม</div>
                    <div class="kpi-value">
                        ฿{{ number_format(
                            $combinedCashSales
                                ?? $cashSales
                                ?? 0,
                            2
                        ) }}
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="kpi-label">QR / เงินโอนรวม</div>
                    <div class="kpi-value">
                        ฿{{ number_format(
                            $combinedQrSales
                                ?? $qrSales
                                ?? 0,
                            2
                        ) }}
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="kpi-label">ผลต่างเงินสดรวม</div>
                    <div class="kpi-value">
                        ฿{{ number_format($totalCashDifference ?? 0, 2) }}
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div class="kpi-label">ผลต่าง QR / โอนรวม</div>
                    <div class="kpi-value">
                        ฿{{ number_format($totalQrDifference ?? 0, 2) }}
                    </div>
                </div>
            </div>

            <div class="section-head">
                <h3 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    กะที่กำลังเปิดอยู่
                </h3>

                <span class="count-badge">
                    <i class="bi bi-circle-fill"></i>
                    {{ $openShifts->count() }} กะ
                </span>
            </div>

            <div class="open-grid">
                @forelse ($openShifts as $shift)
                    <section class="shift-card">
                        <div class="shift-card-header">
                            <div>
                                <div class="shift-name">
                                    {{ $shift->shiftType->name
                                        ?? 'ไม่ระบุชื่อกะ' }}
                                </div>

                                <div class="shift-meta">
                                    ผู้เปิดกะ:
                                    <strong>
                                        {{ $shift->user->name ?? '-' }}
                                    </strong>
                                    · เปิดเมื่อ
                                    {{
                                        optional($shift->opened_at)
                                            ->format('d/m/Y H:i')
                                        ?? '-'
                                    }}
                                    น.
                                </div>
                            </div>

                            <span class="status-open">
                                <i class="bi bi-circle-fill"></i>
                                กำลังเปิด
                            </span>
                        </div>

                        <div class="shift-metrics">
                            <div class="metric-box">
                                <div class="metric-label">เงินเริ่มต้น</div>
                                <div class="metric-value">
                                    ฿{{ number_format(
                                        $shift->opening_cash ?? 0,
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">
                                    ยอดขายรวมระหว่างกะ
                                </div>
                                <div class="metric-value text-positive">
                                    ฿{{ number_format(
                                        $shift->total_sales ?? 0,
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">
                                    เงินสดรวมทุกช่องทาง
                                </div>
                                <div class="metric-value">
                                    ฿{{ number_format(
                                        $shift->combined_cash_sales
                                            ?? (
                                                ($shift->cash_sales ?? 0)
                                                + ($shift->online_cod_sales ?? 0)
                                            ),
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">
                                    QR / เงินโอนรวมทุกช่องทาง
                                </div>
                                <div class="metric-value text-blue">
                                    ฿{{ number_format(
                                        $shift->combined_qr_sales
                                            ?? (
                                                ($shift->qr_sales ?? 0)
                                                + ($shift->online_qr_sales ?? 0)
                                            ),
                                        2
                                    ) }}
                                </div>
                            </div>
                        </div>

                        <div class="cash-summary">
                            <div class="metric-box">
                                <div class="metric-label">
                                    เงินสดที่ควรมี
                                </div>
                                <div class="metric-value">
                                    ฿{{ number_format(
                                        $shift->calculated_expected_cash
                                            ?? $shift->expected_cash
                                            ?? 0,
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">
                                    QR / โอนที่ควรได้รับ
                                </div>
                                <div class="metric-value text-blue">
                                    ฿{{ number_format(
                                        $shift->expected_qr
                                            ?? $shift->combined_qr_sales
                                            ?? 0,
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">Cash In / Out สุทธิ</div>
                                <div class="metric-value">
                                    ฿{{ number_format(
                                        ($shift->cash_in_total ?? 0)
                                        - ($shift->cash_out_total ?? 0),
                                        2
                                    ) }}
                                </div>
                            </div>

                            <div class="metric-box">
                                <div class="metric-label">
                                    ระยะเวลาเปิดกะ
                                </div>
                                <div class="metric-value">
                                    {{
                                        $shift->opened_at
                                            ? $shift->opened_at
                                                ->diffForHumans(
                                                    now(),
                                                    true
                                                )
                                            : '-'
                                    }}
                                </div>
                            </div>
                        </div>
                    </section>
                @empty
                    <section class="shift-card">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-moon-stars"></i>
                            </div>
                            <h4 class="text-white fw-bold">
                                ยังไม่มีกะที่กำลังเปิด
                            </h4>
                            <p class="mb-0">
                                เมื่อมีการเปิดกะ ข้อมูลจะแสดงในส่วนนี้
                            </p>
                        </div>
                    </section>
                @endforelse
            </div>

            @if ($openShifts->hasPages())
                <div class="mt-3">
                    {{ $openShifts->withQueryString()->links() }}
                </div>
            @endif

            <div class="section-head">
                <h3 class="section-title">
                    <i class="bi bi-archive-fill"></i>
                    ประวัติกะย้อนหลัง
                </h3>
            </div>

            <section class="filter-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="bi bi-funnel-fill"></i>
                        ค้นหาประวัติกะ
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form
                        method="GET"
                        action="{{ route('manager.dashboard') }}"
                        class="row g-3 align-items-end"
                    >
                        <div class="col-lg-3 col-md-4">
                            <label class="form-label">ช่วงเวลา</label>
                            <select
                                name="range"
                                class="form-select"
                                onchange="toggleCustomDate(this.value)"
                            >
                                <option value="today" @selected($range === 'today')>
                                    วันนี้
                                </option>
                                <option value="yesterday" @selected($range === 'yesterday')>
                                    เมื่อวาน
                                </option>
                                <option value="7days" @selected($range === '7days')>
                                    7 วันที่ผ่านมา
                                </option>
                                <option value="month" @selected($range === 'month')>
                                    เดือนนี้
                                </option>
                                <option value="custom" @selected($range === 'custom')>
                                    กำหนดช่วงวันที่เอง
                                </option>
                            </select>
                        </div>

                        <div
                            id="custom-date"
                            class="col-lg-6 col-md-5 {{ $range !== 'custom' ? 'd-none' : '' }}"
                        >
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">ตั้งแต่</label>
                                    <input
                                        type="date"
                                        name="from"
                                        value="{{ request('from') }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-6">
                                    <label class="form-label">ถึง</label>
                                    <input
                                        type="date"
                                        name="to"
                                        value="{{ request('to') }}"
                                        class="form-control"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <button type="submit" class="btn btn-filter">
                                <i class="bi bi-search"></i>
                                ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="table-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="bi bi-table"></i>
                        รายการกะที่ปิดแล้ว
                    </h3>
                </div>

                <div class="table-responsive">
                    <table class="table history-table">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>รหัสกะ</th>
                                <th>ประเภทกะ</th>
                                <th>ผู้เปิดกะ</th>
                                <th>เวลาเปิด–ปิด</th>
                                <th class="text-end">เงินสดรวม</th>
                                <th class="text-end">QR / โอนรวม</th>
                                <th class="text-end">ยอดขายรวม</th>
                                <th class="text-end">เงินสดควรมี</th>
                                <th class="text-end">เงินสดจริง</th>
                                <th class="text-center">ผลต่างเงินสด</th>
                                <th class="text-end">QR ควรได้</th>
                                <th class="text-end">QR ตรวจจริง</th>
                                <th class="text-center">ผลต่าง QR</th>
                                <th>ผู้ปิดกะ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($closedShifts as $shift)
                                @php
                                    $difference =
                                        (float) ($shift->cash_difference ?? 0);

                                    $qrDifference =
                                        (float) ($shift->qr_difference ?? 0);

                                    $combinedCash =
                                        (float) (
                                            $shift->combined_cash_sales
                                            ?? (
                                                ($shift->cash_sales ?? 0)
                                                + ($shift->online_cod_sales ?? 0)
                                            )
                                        );

                                    $combinedQr =
                                        (float) (
                                            $shift->combined_qr_sales
                                            ?? (
                                                ($shift->qr_sales ?? 0)
                                                + ($shift->online_qr_sales ?? 0)
                                            )
                                        );
                                @endphp

                                <tr>
                                    <td>
                                        {{
                                            optional($shift->opened_at)
                                                ->format('d/m/Y')
                                            ?? '-'
                                        }}
                                    </td>

                                    <td>
                                        <span class="shift-code">
                                            #{{ str_pad(
                                                $shift->id,
                                                5,
                                                '0',
                                                STR_PAD_LEFT
                                            ) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $shift->shiftType->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $shift->user->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{
                                            optional($shift->opened_at)
                                                ->format('H:i')
                                            ?? '-'
                                        }}
                                        –
                                        {{
                                            optional($shift->closed_at)
                                                ->format('H:i')
                                            ?? '-'
                                        }}
                                    </td>

                                    <td class="text-end">
                                        ฿{{ number_format($combinedCash, 2) }}
                                    </td>

                                    <td class="text-end text-blue">
                                        ฿{{ number_format($combinedQr, 2) }}
                                    </td>

                                    <td class="text-end fw-bold">
                                        ฿{{ number_format(
                                            $shift->total_sales ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="text-end">
                                        ฿{{ number_format(
                                            $shift->expected_cash ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="text-end">
                                        ฿{{ number_format(
                                            $shift->closing_cash ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="text-center">
                                        @if ($difference < 0)
                                            <span class="difference-badge difference-minus">
                                                -฿{{ number_format(
                                                    abs($difference),
                                                    2
                                                ) }}
                                            </span>
                                        @elseif ($difference > 0)
                                            <span class="difference-badge difference-plus">
                                                +฿{{ number_format(
                                                    $difference,
                                                    2
                                                ) }}
                                            </span>
                                        @else
                                            <span class="difference-badge difference-zero">
                                                ฿0.00
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end text-blue">
                                        ฿{{ number_format(
                                            $shift->expected_qr ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="text-end text-blue">
                                        ฿{{ number_format(
                                            $shift->closing_qr ?? 0,
                                            2
                                        ) }}
                                    </td>

                                    <td class="text-center">
                                        @if ($qrDifference < 0)
                                            <span class="difference-badge difference-minus">
                                                -฿{{ number_format(
                                                    abs($qrDifference),
                                                    2
                                                ) }}
                                            </span>
                                        @elseif ($qrDifference > 0)
                                            <span class="difference-badge difference-plus">
                                                +฿{{ number_format(
                                                    $qrDifference,
                                                    2
                                                ) }}
                                            </span>
                                        @else
                                            <span class="difference-badge difference-zero">
                                                ฿0.00
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $shift->closedBy->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="bi bi-inbox"></i>
                                            </div>
                                            <h4 class="text-white fw-bold">
                                                ไม่พบประวัติกะ
                                            </h4>
                                            <p class="mb-0">
                                                ลองเปลี่ยนช่วงวันที่แล้วค้นหาใหม่
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($closedShifts->hasPages())
                    <div class="pagination-wrap">
                        {{ $closedShifts->withQueryString()->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<script>
function toggleCustomDate(value) {
    const box = document.getElementById('custom-date');

    if (!box) {
        return;
    }

    box.classList.toggle('d-none', value !== 'custom');
}
</script>
@endsection