@extends('layouts.layout')

@section('content')

<style>

    :root {
        --primary: #007849;
        --orange: #ff7f00;
        --danger: #ed1c24;
        --bg: #f6f8fa;
    }

    body {
        background: var(--bg);
    }

    .dashboard-card {
        border: none;
        border-radius: 18px;
        transition: .2s;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
    }

    .stat-card {
        border-radius: 18px;
        border: none;
        color: white;
        min-height: 145px;
    }

    .stat-title {
        font-size: 15px;
        opacity: .9;
    }

    .stat-value {
        font-size: 30px;
        font-weight: bold;
    }

    .section-title {
        font-weight: bold;
        color: #343a40;
    }

    .shift-card {
        border: none;
        border-radius: 18px;
        overflow: hidden;
    }

    .shift-header {
        background: linear-gradient(
            135deg,
            #007849,
            #00a66a
        );
        color: white;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 12px;
        height: 100%;
    }

    .info-label {
        font-size: 13px;
        color: #6c757d;
    }

    .info-value {
        font-size: 18px;
        font-weight: bold;
    }

    .cash-box {
        background: #fff8ed;
        border: 1px solid #ffe0b2;
        border-radius: 14px;
        padding: 18px;
    }

    .table {
        vertical-align: middle;
    }

</style>


<div class="container-fluid py-4">


    {{-- ================================================= --}}
    {{-- HEADER --}}
    {{-- ================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">
                📊 Dashboard ผู้จัดการ
            </h3>

            <div class="text-muted">
                ภาพรวมการขายและการจัดการกะ
            </div>

        </div>

        <div class="text-muted">

            {{ now()->translatedFormat('d F Y') }}

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- SUMMARY TODAY --}}
    {{-- ================================================= --}}

    <h5 class="section-title mb-3">

        📅 สรุปยอดขายวันนี้

    </h5>


    <div class="row g-4 mb-5">


        {{-- ยอดขาย --}}
        <div class="col-12 col-md-6 col-xl-3">

            <div
                class="card stat-card shadow-sm"
                style="background: linear-gradient(
                    135deg,
                    #007849,
                    #00a66a
                );">

                <div class="card-body">

                    <div class="stat-title">
                        💰 ยอดขายวันนี้
                    </div>

                    <div class="stat-value">

                        ฿{{ number_format(
                            $totalSales,
                            2
                        ) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- จำนวนบิล --}}
        <div class="col-12 col-md-6 col-xl-3">

            <div
                class="card stat-card shadow-sm"
                style="background: linear-gradient(
                    135deg,
                    #ff7f00,
                    #ff9f43
                );">

                <div class="card-body">

                    <div class="stat-title">
                        🧾 จำนวนบิลวันนี้
                    </div>

                    <div class="stat-value">

                        {{ number_format(
                            $totalBills
                        ) }}

                        <small>
                            บิล
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- เงินสด --}}
        <div class="col-12 col-md-6 col-xl-3">

            <div
                class="card stat-card shadow-sm"
                style="background: linear-gradient(
                    135deg,
                    #198754,
                    #20c997
                );">

                <div class="card-body">

                    <div class="stat-title">
                        💵 เงินสดที่ขายได้
                    </div>

                    <div class="stat-value">

                        ฿{{ number_format(
                            $cashSales,
                            2
                        ) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- QR --}}
        <div class="col-12 col-md-6 col-xl-3">

            <div
                class="card stat-card shadow-sm"
                style="background: linear-gradient(
                    135deg,
                    #0d6efd,
                    #6ea8fe
                );">

                <div class="card-body">

                    <div class="stat-title">
                        📱 QR / โอน
                    </div>

                    <div class="stat-value">

                        ฿{{ number_format(
                            $qrSales,
                            2
                        ) }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================= --}}
    {{-- OPEN SHIFTS --}}
    {{-- ================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h5 class="section-title mb-0">

            🕒 กะที่กำลังเปิดอยู่

        </h5>

        <span class="badge bg-success">

            {{ $openShifts->count() }} กะ

        </span>

    </div>


    @forelse ($openShifts as $shift)


        <div class="card shift-card shadow-sm mb-4">


            {{-- Header --}}
            <div class="shift-header p-4">


                <div class="d-flex justify-content-between">


                    <div>

                        <h5 class="mb-1">

                            🕒

                            {{ $shift->shiftType->name
                                ?? 'ไม่ระบุชื่อกะ' }}

                        </h5>

                        <div>

                            ผู้เปิดกะ:

                            <strong>

                                {{ $shift->user->name
                                    ?? '-' }}

                            </strong>

                        </div>

                    </div>


                    <div class="text-end">

                        <div>

                            เปิดกะ

                        </div>

                        <strong>

                            {{ optional(
                                $shift->opened_at
                            )->format('d/m/Y H:i') }}

                        </strong>

                    </div>


                </div>


            </div>


            {{-- Body --}}
            <div class="card-body">


                <div class="row g-3 mb-4">


                    <div class="col-6 col-md-3">

                        <div class="info-box">

                            <div class="info-label">

                                เงินเริ่มต้น

                            </div>

                            <div class="info-value">

                                ฿{{ number_format(
                                    $shift->opening_cash,
                                    2
                                ) }}

                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="info-box">

                            <div class="info-label">

                                ยอดขายระหว่างกะ

                            </div>

                            <div class="info-value text-success">

                                ฿{{ number_format(
                                    $shift->total_sales,
                                    2
                                ) }}

                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="info-box">

                            <div class="info-label">

                                เงินสด

                            </div>

                            <div class="info-value">

                                ฿{{ number_format(
                                    $shift->cash_sales,
                                    2
                                ) }}

                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-md-3">

                        <div class="info-box">

                            <div class="info-label">

                                QR / โอน

                            </div>

                            <div class="info-value text-primary">

                                ฿{{ number_format(
                                    $shift->qr_sales,
                                    2
                                ) }}

                            </div>

                        </div>

                    </div>


                </div>


                {{-- Cash Summary --}}
                <div class="cash-box">


                    <div class="row g-3">


                        <div class="col-6 col-md-3">

                            <div class="info-label">

                                Cash In

                            </div>

                            <div class="info-value text-success">

                                + ฿{{ number_format(
                                    $shift->cash_in_total,
                                    2
                                ) }}

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="info-label">

                                Cash Out

                            </div>

                            <div class="info-value text-danger">

                                - ฿{{ number_format(
                                    $shift->cash_out_total,
                                    2
                                ) }}

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="info-label">

                                เงินสดที่ควรมี

                            </div>

                            <div class="info-value text-orange">

                                ฿{{ number_format(
                                    $shift->calculated_expected_cash,
                                    2
                                ) }}

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="info-label">

                                สถานะ

                            </div>

                            <div class="info-value text-success">

                                🟢 กำลังเปิด

                            </div>

                        </div>


                    </div>


                </div>


            </div>


        </div>


    @empty


        <div class="card shadow-sm border-0 rounded-4 mb-5">

            <div class="card-body text-center py-5">

                <div style="font-size: 45px;">

                    💤

                </div>

                <h5>

                    ขณะนี้ไม่มีกะที่กำลังเปิดอยู่

                </h5>

                <p class="text-muted mb-0">

                    เมื่อมีการเปิดกะ ข้อมูลจะแสดงที่นี่

                </p>

            </div>

        </div>


    @endforelse


    {{-- ================================================= --}}
    {{-- SHIFT HISTORY --}}
    {{-- ================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-3">


        <h5 class="section-title mb-0">

            📜 ประวัติกะย้อนหลัง

        </h5>


    </div>


    {{-- Filter --}}
    <div class="card dashboard-card shadow-sm mb-4">


        <div class="card-body">


            <form
                method="GET"
                action="{{ route('manager.dashboard') }}"
                class="row g-3 align-items-end">


                <div class="col-12 col-md-4">


                    <label class="form-label">

                        ช่วงเวลา

                    </label>


                    <select
                        name="range"
                        class="form-select"
                        onchange="toggleCustomDate(this.value)">


                        <option
                            value="today"
                            @selected($range === 'today')>

                            วันนี้

                        </option>


                        <option
                            value="yesterday"
                            @selected($range === 'yesterday')>

                            เมื่อวาน

                        </option>


                        <option
                            value="7days"
                            @selected($range === '7days')>

                            7 วันที่ผ่านมา

                        </option>


                        <option
                            value="month"
                            @selected($range === 'month')>

                            เดือนนี้

                        </option>


                        <option
                            value="custom"
                            @selected($range === 'custom')>

                            กำหนดช่วงวันที่เอง

                        </option>


                    </select>


                </div>


                <div
                    id="custom-date"
                    class="col-12 col-md-5
                    {{ $range !== 'custom'
                        ? 'd-none'
                        : '' }}">


                    <div class="row g-2">


                        <div class="col-6">

                            <label class="form-label">

                                ตั้งแต่

                            </label>

                            <input
                                type="date"
                                name="from"
                                value="{{ request('from') }}"
                                class="form-control">

                        </div>


                        <div class="col-6">

                            <label class="form-label">

                                ถึง

                            </label>

                            <input
                                type="date"
                                name="to"
                                value="{{ request('to') }}"
                                class="form-control">

                        </div>


                    </div>


                </div>


                <div class="col-12 col-md-3">


                    <button
                        type="submit"
                        class="btn btn-primary w-100">


                        🔍 ค้นหา


                    </button>


                </div>


            </form>


        </div>


    </div>


    {{-- History Table --}}
    <div class="card dashboard-card shadow-sm">


        <div class="card-body">


            <div class="table-responsive">


                <table class="table table-hover">


                    <thead>


                        <tr>

                            <th>วันที่</th>

                            <th>กะ</th>

                            <th>ผู้เปิดกะ</th>

                            <th>เปิด</th>

                            <th>ปิด</th>

                            <th>ยอดขาย</th>

                            <th>เงินควรมี</th>

                            <th>เงินจริง</th>

                            <th>ผลต่าง</th>

                            <th>ผู้ปิดกะ</th>

                        </tr>


                    </thead>


                    <tbody>


                        @forelse (
                            $closedShifts
                            as $shift
                        )


                            <tr>


                                <td>

                                    {{ optional(
                                        $shift->opened_at
                                    )->format('d/m/Y') }}

                                </td>


                                <td>

                                    {{ $shift->shiftType->name
                                        ?? '-' }}

                                </td>


                                <td>

                                    {{ $shift->user->name
                                        ?? '-' }}

                                </td>


                                <td>

                                    {{ optional(
                                        $shift->opened_at
                                    )->format('H:i') }}

                                </td>


                                <td>

                                    {{ optional(
                                        $shift->closed_at
                                    )->format('H:i') }}

                                </td>


                                <td>

                                    ฿{{ number_format(
                                        $shift->total_sales
                                            ?? 0,
                                        2
                                    ) }}

                                </td>


                                <td>

                                    ฿{{ number_format(
                                        $shift->expected_cash
                                            ?? 0,
                                        2
                                    ) }}

                                </td>


                                <td>

                                    ฿{{ number_format(
                                        $shift->closing_cash
                                            ?? 0,
                                        2
                                    ) }}

                                </td>


                                <td>


                                    @php

                                        $difference =
                                            $shift->cash_difference
                                            ?? 0;

                                    @endphp


                                    @if ($difference < 0)

                                        <span class="text-danger fw-bold">

                                            -฿{{ number_format(
                                                abs($difference),
                                                2
                                            ) }}

                                        </span>

                                    @elseif ($difference > 0)

                                        <span class="text-success fw-bold">

                                            +฿{{ number_format(
                                                $difference,
                                                2
                                            ) }}

                                        </span>

                                    @else

                                        <span class="text-muted">

                                            ฿0.00

                                        </span>

                                    @endif


                                </td>


                                <td>

                                    {{ $shift->closedBy->name
                                        ?? '-' }}

                                </td>


                            </tr>


                        @empty


                            <tr>


                                <td
                                    colspan="10"
                                    class="text-center py-5">


                                    ไม่พบประวัติกะ


                                </td>


                            </tr>


                        @endforelse


                    </tbody>


                </table>


            </div>


            <div class="mt-3">

                {{ $closedShifts->links() }}

            </div>


        </div>


    </div>


</div>


<script>

    function toggleCustomDate(value) {

        const box =
            document.getElementById('custom-date');

        if (value === 'custom') {

            box.classList.remove('d-none');

        } else {

            box.classList.add('d-none');

        }

    }

</script>

@endsection