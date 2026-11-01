
@extends('layouts.layout')

@section('content')

<style>
    .shift-page {
        --yellow: #ffc107;
        --bg: #111;
        --card: #1b1b1b;
        --border: #333;
        --muted: #aaa;
        --green: #55d995;
        --red: #ff8585;
        --blue: #83bdff;
        color: #f5f5f5;
    }

    .shift-page .container-fluid {
        max-width: 1450px;
        margin: auto;
    }

    .shift-page .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 22px;
        margin-bottom: 22px;
        border: 1px solid #393328;
        border-radius: 15px;
        background: #1b1b1b;
    }

    .shift-page .page-header h3 {
        font-size: 1.3rem;
        font-weight: 800;
        margin: 0;
        color: #fff;
    }

    .shift-page .page-header p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: .85rem;
    }

    .shift-page .today {
        color: var(--yellow);
        font-size: .85rem;
        white-space: nowrap;
    }

    .shift-page .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin: 25px 0 13px;
    }

    .shift-page .section-title h5 {
        margin: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
    }

    .shift-page .section-title i {
        color: var(--yellow);
        margin-right: 5px;
    }

    .shift-page .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 13px;
    }

    .shift-page .kpi {
        padding: 17px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 13px;
    }

    .shift-page .kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .shift-page .kpi-label {
        color: var(--muted);
        font-size: .8rem;
    }

    .shift-page .kpi-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: #302a19;
        color: var(--yellow);
        font-size: 1.1rem;
    }

    .shift-page .kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #fff;
    }

    .shift-page .panel {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 18px;
    }

    .shift-page .panel-header {
        padding: 15px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .shift-page .panel-header h5 {
        margin: 0;
        color: #fff;
        font-size: .95rem;
        font-weight: 800;
    }

    .shift-page .panel-body {
        padding: 17px;
    }

    .shift-page .count {
        padding: 5px 10px;
        border-radius: 20px;
        background: #302a19;
        color: var(--yellow);
        font-size: .75rem;
        font-weight: 700;
    }

    .shift-page .open-shift {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 13px;
        background: #161616;
    }

    .shift-page .open-shift:last-child {
        margin-bottom: 0;
    }

    .shift-page .open-header {
        padding: 13px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid var(--border);
    }

    .shift-page .staff-name {
        font-weight: 800;
        color: #fff;
    }

    .shift-page .shift-time {
        color: var(--muted);
        font-size: .78rem;
        margin-top: 4px;
    }

    .shift-page .status-open {
        color: var(--green);
        background: #173126;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: .72rem;
        white-space: nowrap;
    }

    .shift-page .shift-data {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        padding: 15px;
    }

    .shift-page .data-item {
        padding: 11px;
        background: #222;
        border-radius: 9px;
    }

    .shift-page .data-label {
        color: var(--muted);
        font-size: .73rem;
        margin-bottom: 6px;
    }

    .shift-page .data-value {
        color: #fff;
        font-weight: 800;
        font-size: .95rem;
        overflow-wrap: anywhere;
    }

    .shift-page .green {
        color: var(--green);
    }

    .shift-page .blue {
        color: var(--blue);
    }

    .shift-page .filter-row {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .shift-page .form-label {
        color: #ddd;
        font-size: .8rem;
        font-weight: 700;
    }

    .shift-page .form-control,
    .shift-page .form-select {
        background: #111;
        border: 1px solid #444;
        color: #fff;
        min-height: 42px;
    }

    .shift-page .form-select option {
        background: #111;
    }

    .shift-page .btn-search {
        min-height: 42px;
        background: var(--yellow);
        color: #111;
        font-weight: 800;
        border: 0;
        padding: 0 22px;
    }

    .shift-page .table {
        color: #eee;
        margin: 0;
        vertical-align: middle;
        white-space: nowrap;
    }

    .shift-page .table th {
        background: #151515;
        color: #bbb;
        font-size: .75rem;
        padding: 13px;
        border-color: var(--border);
    }

    .shift-page .table td {
        background: transparent;
        color: #eee;
        font-size: .8rem;
        padding: 13px;
        border-color: var(--border);
    }

    .shift-page .table tbody tr:hover td {
        background: #25231d;
    }

    .shift-page .shift-code {
        color: var(--yellow);
        background: #302a19;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 800;
    }

    .shift-page .difference {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 800;
    }

    .shift-page .diff-zero {
        color: #ccc;
        background: #333;
    }

    .shift-page .diff-plus {
        color: var(--green);
        background: #173126;
    }

    .shift-page .diff-minus {
        color: var(--red);
        background: #391d21;
    }

    .shift-page .empty {
        text-align: center;
        padding: 35px 15px;
        color: var(--muted);
    }

    .shift-page .pagination-wrap {
        padding: 15px;
        border-top: 1px solid var(--border);
    }

    .shift-page .pagination {
        margin: 0;
    }

    @media(max-width: 992px) {
        .shift-page .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .shift-page .shift-data {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 576px) {
        .shift-page .page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .shift-page .kpi-value {
            font-size: 1.1rem;
        }

        .shift-page .shift-data {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="shift-page py-4">
    <div class="container-fluid">

       

        {{-- กะที่เปิดอยู่ --}}
        <div class="section-title">
            <h5><i class="bi bi-clock-history"></i> กะที่กำลังเปิดอยู่</h5>
            <span class="count">
                {{ $openShifts->count() }} กะ
            </span>
        </div>

        <div class="panel">
            <div class="panel-body">

                @forelse($openShifts as $shift)
                    <div class="open-shift">

                        <div class="open-header">
                            <div>
                                <div class="staff-name">
                                    <i class="bi bi-person-circle text-warning me-1"></i>
                                    {{ $shift->user->name ?? 'ไม่ระบุพนักงาน' }}
                                </div>

                                <div class="shift-time">
                                    เปิดกะเมื่อ
                                    {{ optional($shift->opened_at)->format('d/m/Y H:i') ?? '-' }}
                                    น.
                                </div>
                            </div>

                            <span class="status-open">
                                <i class="bi bi-circle-fill me-1"></i>
                                กำลังเปิด
                            </span>
                        </div>

                        @php
                            $cash = (float) (
                                $shift->combined_cash_sales
                                ?? (
                                    ($shift->cash_sales ?? 0)
                                    + ($shift->online_cod_sales ?? 0)
                                )
                            );

                            $qr = (float) (
                                $shift->combined_qr_sales
                                ?? (
                                    ($shift->qr_sales ?? 0)
                                    + ($shift->online_qr_sales ?? 0)
                                )
                            );
                        @endphp

                        <div class="shift-data">
                            <div class="data-item">
                                <div class="data-label">เงินเริ่มต้น</div>
                                <div class="data-value">
                                    ฿{{ number_format($shift->opening_cash ?? 0, 2) }}
                                </div>
                            </div>

                            <div class="data-item">
                                <div class="data-label">ยอดขายรวม</div>
                                <div class="data-value green">
                                    ฿{{ number_format($shift->total_sales ?? 0, 2) }}
                                </div>
                            </div>

                            <div class="data-item">
                                <div class="data-label">รับเงินสด</div>
                                <div class="data-value">
                                    ฿{{ number_format($cash, 2) }}
                                </div>
                            </div>

                            <div class="data-item">
                                <div class="data-label">QR / เงินโอน</div>
                                <div class="data-value blue">
                                    ฿{{ number_format($qr, 2) }}
                                </div>
                            </div>

                            <div class="data-item">
                                <div class="data-label">ระยะเวลาเปิดกะ</div>
                                <div class="data-value">
                                    {{ $shift->opened_at
                                        ? $shift->opened_at->diffForHumans(now(), true)
                                        : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty">
                        <i class="bi bi-moon-stars fs-1 d-block mb-2"></i>
                        <strong>ยังไม่มีกะที่กำลังเปิด</strong>
                        <div class="small mt-1">
                            เมื่อพนักงานเปิดกะ ข้อมูลจะแสดงที่นี่
                        </div>
                    </div>
                @endforelse

            </div>
        </div>

        @if ($openShifts->hasPages())
            <div class="mb-3">
                {{ $openShifts->withQueryString()->links() }}
            </div>
        @endif

        {{-- ประวัติกะ --}}
        <div class="section-title">
            <h5><i class="bi bi-archive"></i> ประวัติการปิดกะ</h5>
        </div>

        {{-- ตัวกรอง --}}
        <div class="panel">
            <div class="panel-header">
                <h5><i class="bi bi-funnel text-warning me-2"></i>ค้นหาประวัติกะ</h5>
            </div>

            <div class="panel-body">
                <form method="GET"
                      action="{{ route('manager.dashboard') }}"
                      class="filter-row">

                    <div style="min-width:190px;">
                        <label class="form-label">ช่วงเวลา</label>
                        <select name="range"
                                class="form-select"
                                onchange="toggleCustomDate(this.value)">
                            <option value="today" @selected($range === 'today')>วันนี้</option>
                            <option value="yesterday" @selected($range === 'yesterday')>เมื่อวาน</option>
                            <option value="7days" @selected($range === '7days')>7 วันที่ผ่านมา</option>
                            <option value="month" @selected($range === 'month')>เดือนนี้</option>
                            <option value="custom" @selected($range === 'custom')>กำหนดวันที่เอง</option>
                        </select>
                    </div>

                    <div id="custom-date"
                         class="d-flex gap-2 flex-wrap {{ $range !== 'custom' ? 'd-none' : '' }}">

                        <div>
                            <label class="form-label">ตั้งแต่</label>
                            <input type="date" name="from"
                                   value="{{ request('from') }}"
                                   class="form-control">
                        </div>

                        <div>
                            <label class="form-label">ถึง</label>
                            <input type="date" name="to"
                                   value="{{ request('to') }}"
                                   class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-search">
                        <i class="bi bi-search me-1"></i> ค้นหา
                    </button>

                    <a href="{{ route('manager.dashboard') }}"
                       class="btn btn-outline-secondary"
                       style="min-height:42px; display:flex; align-items:center;">
                        ล้างตัวกรอง
                    </a>
                </form>
            </div>
        </div>

        {{-- ตารางประวัติ --}}
        <div class="panel">
            <div class="panel-header">
                <h5>
                    <i class="bi bi-table text-warning me-2"></i>
                    รายการกะที่ปิดแล้ว
                </h5>
                <span class="count">
                    {{ $closedShifts->total() }} รายการ
                </span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>รหัสกะ</th>
                            <th>พนักงาน</th>
                            <th>เวลาเปิด–ปิด</th>
                            <th class="text-end">ยอดขายรวม</th>
                            <th class="text-end">เงินสด</th>
                            <th class="text-end">QR / โอน</th>
                            <th class="text-center">ผลต่างเงินสด</th>
                            <th class="text-center">ผลต่าง QR</th>
                            <th>ผู้ปิดกะ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($closedShifts as $shift)
                            @php
                                $cashDifference = (float) ($shift->cash_difference ?? 0);
                                $qrDifference = (float) ($shift->qr_difference ?? 0);

                                $combinedCash = (float) (
                                    $shift->combined_cash_sales
                                    ?? (
                                        ($shift->cash_sales ?? 0)
                                        + ($shift->online_cod_sales ?? 0)
                                    )
                                );

                                $combinedQr = (float) (
                                    $shift->combined_qr_sales
                                    ?? (
                                        ($shift->qr_sales ?? 0)
                                        + ($shift->online_qr_sales ?? 0)
                                    )
                                );
                            @endphp

                            <tr>
                                <td>
                                    {{ optional($shift->opened_at)->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    <span class="shift-code">
                                        #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <td>{{ $shift->user->name ?? '-' }}</td>

                                <td>
                                    {{ optional($shift->opened_at)->format('H:i') ?? '-' }}
                                    –
                                    {{ optional($shift->closed_at)->format('H:i') ?? '-' }}
                                </td>

                                <td class="text-end fw-bold">
                                    ฿{{ number_format($shift->total_sales ?? 0, 2) }}
                                </td>

                                <td class="text-end">
                                    ฿{{ number_format($combinedCash, 2) }}
                                </td>

                                <td class="text-end blue">
                                    ฿{{ number_format($combinedQr, 2) }}
                                </td>

                                <td class="text-center">
                                    @if($cashDifference > 0)
                                        <span class="difference diff-plus">
                                            +฿{{ number_format($cashDifference, 2) }}
                                        </span>
                                    @elseif($cashDifference < 0)
                                        <span class="difference diff-minus">
                                            -฿{{ number_format(abs($cashDifference), 2) }}
                                        </span>
                                    @else
                                        <span class="difference diff-zero">฿0.00</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($qrDifference > 0)
                                        <span class="difference diff-plus">
                                            +฿{{ number_format($qrDifference, 2) }}
                                        </span>
                                    @elseif($qrDifference < 0)
                                        <span class="difference diff-minus">
                                            -฿{{ number_format(abs($qrDifference), 2) }}
                                        </span>
                                    @else
                                        <span class="difference diff-zero">฿0.00</span>
                                    @endif
                                </td>

                                <td>{{ $shift->closedBy->name ?? '-' }}</td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        <strong>ไม่พบประวัติการปิดกะ</strong>
                                        <div class="small mt-1">
                                            ลองเลือกช่วงเวลาอื่นเพื่อค้นหา
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($closedShifts->hasPages())
                <div class="pagination-wrap">
                    {{ $closedShifts->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
function toggleCustomDate(value) {
    const box = document.getElementById('custom-date');

    if (box) {
        box.classList.toggle('d-none', value !== 'custom');
    }
}
</script>

@endsection
