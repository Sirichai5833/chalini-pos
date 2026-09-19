<!-- resources/views/online/orders/history.blade.php -->
@extends('layouts.layout')

@section('content')
<style>
    .order-history-page {
        --oh-yellow: #ffc107;
        --oh-yellow-soft: #ffe27a;
        --oh-panel: #171717;
        --oh-panel-soft: #202020;
        --oh-border: #3a3a3a;
        --oh-text: #f7f7f7;
        --oh-muted: #a8a8a8;
        color: var(--oh-text);
    }

    .order-history-page .page-shell {
        max-width: 1240px;
        margin: 0 auto;
    }

    .order-history-page .history-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.2rem;
        padding: 1.35rem;
        border: 1px solid rgba(255, 193, 7, .24);
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 92% 10%,
                rgba(255, 193, 7, .17),
                transparent 26%
            ),
            linear-gradient(135deg, #202020, #0d0d0d);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .3);
    }

    .order-history-page .hero-icon {
        width: 60px;
        height: 60px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 19px;
        background: linear-gradient(
            135deg,
            var(--oh-yellow),
            var(--oh-yellow-soft)
        );
        color: #111;
        font-size: 1.5rem;
    }

    .order-history-page .history-hero h2 {
        margin: 0;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 900;
    }

    .order-history-page .history-hero p {
        margin: .35rem 0 0;
        color: var(--oh-muted);
        font-size: .84rem;
    }

    .order-history-page .hero-total {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .8rem;
        border: 1px solid rgba(255, 193, 7, .24);
        border-radius: 999px;
        background: rgba(255, 193, 7, .07);
        color: var(--oh-yellow);
        font-size: .76rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .order-history-page .filter-card,
    .order-history-page .history-card {
        overflow: hidden;
        border: 1px solid var(--oh-border);
        border-radius: 20px;
        background: var(--oh-panel);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .28);
    }

    .order-history-page .filter-card {
        margin-bottom: 1rem;
    }

    .order-history-page .card-header-custom {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--oh-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .order-history-page .card-title-custom {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #fff;
        font-size: .95rem;
        font-weight: 850;
    }

    .order-history-page .card-title-custom i {
        color: var(--oh-yellow);
    }

    .order-history-page .card-body-custom {
        padding: 1.15rem;
    }

    .order-history-page .form-label {
        margin-bottom: .45rem;
        color: #e8e8e8;
        font-size: .79rem;
        font-weight: 800;
    }

    .order-history-page .form-control,
    .order-history-page .form-select {
        min-height: 47px;
        border: 1px solid #454545;
        border-radius: 12px;
        background: #0e0e0e;
        color: #fff;
        box-shadow: none;
    }

    .order-history-page .form-control::placeholder {
        color: #686868;
    }

    .order-history-page .form-control:focus,
    .order-history-page .form-select:focus {
        border-color: var(--oh-yellow);
        background: #0a0a0a;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 193, 7, .11);
    }

    .order-history-page .form-select option {
        background: #111;
        color: #fff;
    }

    .order-history-page .filter-actions {
        display: flex;
        align-items: end;
        gap: .6rem;
        height: 100%;
    }

    .order-history-page .btn-search,
    .order-history-page .btn-reset {
        min-height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .65rem 1rem;
        border-radius: 12px;
        font-weight: 850;
        text-decoration: none;
    }

    .order-history-page .btn-search {
        border: 1px solid var(--oh-yellow);
        background: linear-gradient(
            135deg,
            var(--oh-yellow),
            var(--oh-yellow-soft)
        );
        color: #111;
    }

    .order-history-page .btn-reset {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .order-history-page .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .8rem;
        margin-bottom: 1rem;
    }

    .order-history-page .summary-card {
        padding: 1rem;
        border: 1px solid var(--oh-border);
        border-radius: 16px;
        background: var(--oh-panel-soft);
    }

    .order-history-page .summary-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        margin-bottom: .75rem;
        border-radius: 12px;
        background: rgba(255, 193, 7, .1);
        color: var(--oh-yellow);
    }

    .order-history-page .summary-label {
        color: var(--oh-muted);
        font-size: .73rem;
    }

    .order-history-page .summary-value {
        margin-top: .25rem;
        color: #fff;
        font-size: 1.1rem;
        font-weight: 900;
    }

    .order-history-page .table-responsive {
        max-height: 620px;
    }

    .order-history-page .history-table {
        margin: 0;
        color: #e8e8e8;
        vertical-align: middle;
    }

    .order-history-page .history-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: .9rem;
        border-bottom: 1px solid var(--oh-border);
        background: #131313;
        color: #bdbdbd;
        font-size: .73rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .order-history-page .history-table tbody td {
        padding: .9rem;
        border-color: #2f2f2f;
        background: transparent;
        color: #e7e7e7;
    }

    .order-history-page .history-table tbody tr:hover td {
        background: rgba(255, 193, 7, .035);
    }

    .order-history-page .order-code {
        display: inline-flex;
        padding: .34rem .55rem;
        border: 1px solid rgba(255, 193, 7, .23);
        border-radius: 9px;
        background: rgba(255, 193, 7, .07);
        color: var(--oh-yellow);
        font-size: .75rem;
        font-weight: 900;
    }

    .order-history-page .customer-name {
        color: #fff;
        font-weight: 800;
    }

    .order-history-page .customer-meta {
        margin-top: .2rem;
        color: var(--oh-muted);
        font-size: .72rem;
    }

    .order-history-page .payment-badge,
    .order-history-page .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .38rem .58rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .order-history-page .payment-qr {
        border: 1px solid rgba(13, 202, 240, .3);
        background: rgba(13, 202, 240, .1);
        color: #74dfff;
    }

    .order-history-page .payment-cod {
        border: 1px solid rgba(255, 193, 7, .3);
        background: rgba(255, 193, 7, .09);
        color: #ffd75e;
    }

    .order-history-page .status-pending {
        background: rgba(255, 193, 7, .1);
        color: #ffd75e;
    }

    .order-history-page .status-processing {
        background: rgba(13, 110, 253, .12);
        color: #7fb1ff;
    }

    .order-history-page .status-shipped {
        background: rgba(111, 66, 193, .14);
        color: #b99aff;
    }

    .order-history-page .status-completed {
        background: rgba(25, 135, 84, .12);
        color: #7ee2aa;
    }

    .order-history-page .status-cancelled {
        background: rgba(220, 53, 69, .12);
        color: #ff929c;
    }

    .order-history-page .order-total {
        color: #fff;
        font-weight: 900;
        white-space: nowrap;
    }

    .order-history-page .btn-detail {
        width: 36px;
        height: 36px;
        display: inline-grid;
        place-items: center;
        border: 1px solid #474747;
        border-radius: 10px;
        background: #1b1b1b;
        color: #ddd;
        text-decoration: none;
        transition: .2s ease;
    }

    .order-history-page .btn-detail:hover {
        border-color: var(--oh-yellow);
        color: var(--oh-yellow);
        background: rgba(255, 193, 7, .06);
    }

    .order-history-page .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--oh-muted);
    }

    .order-history-page .empty-icon {
        width: 70px;
        height: 70px;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        border-radius: 22px;
        background: rgba(255, 193, 7, .08);
        color: var(--oh-yellow);
        font-size: 1.7rem;
    }

    .order-history-page .pagination-wrap {
        padding: 1rem 1.15rem;
        border-top: 1px solid var(--oh-border);
    }

    @media (max-width: 991.98px) {
        .order-history-page .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .order-history-page .history-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .order-history-page .filter-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .order-history-page .filter-actions .btn,
        .order-history-page .filter-actions a {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .order-history-page .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $orderCollection = $orders instanceof \Illuminate\Pagination\AbstractPaginator
        ? collect($orders->items())
        : collect($orders ?? []);

    $totalOrders = method_exists($orders ?? null, 'total')
        ? $orders->total()
        : $orderCollection->count();

    $qrMethods = [
        'qr',
        'qrcode',
        'promptpay',
        'โอนผ่านบัญชีธนาคาร',
        'โอนเงิน',
        'พร้อมเพย์',
    ];

    $codMethods = [
        'cod',
        'cash_on_delivery',
        'เก็บเงินปลายทาง',
    ];

    $completedStatuses = [
        'completed',
        'delivered',
        'สำเร็จ',
        'เสร็จสิ้น',
        'จัดส่งสำเร็จ',
    ];

    $qrTotal = $orderCollection
        ->filter(fn ($order) => in_array(
            trim((string) ($order->payment_method ?? '')),
            $qrMethods,
            true
        ))
        ->sum(fn ($order) => (float) ($order->total_amount ?? $order->total ?? 0));

    $codTotal = $orderCollection
        ->filter(fn ($order) => in_array(
            trim((string) ($order->payment_method ?? '')),
            $codMethods,
            true
        ))
        ->sum(fn ($order) => (float) ($order->total_amount ?? $order->total ?? 0));

    $completedCount = $orderCollection
        ->filter(fn ($order) => in_array(
            trim((string) ($order->status ?? '')),
            $completedStatuses,
            true
        ))
        ->count();
@endphp

<div class="order-history-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="history-hero">
                <div class="d-flex align-items-center gap-3">
                    <div class="hero-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>
                        <h2>ประวัติคำสั่งซื้อออนไลน์</h2>
                        <p>
                            ตรวจสอบคำสั่งซื้อย้อนหลัง การชำระเงิน
                            และสถานะการจัดส่ง
                        </p>
                    </div>
                </div>

                <div class="hero-total">
                    <i class="bi bi-bag-check-fill"></i>
                    ทั้งหมด {{ number_format($totalOrders) }} รายการ
                </div>
            </section>

            <section class="filter-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="bi bi-funnel-fill"></i>
                        ค้นหาและกรองข้อมูล
                    </h3>
                </div>

                <div class="card-body-custom">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label for="search" class="form-label">
                                    ค้นหา
                                </label>

                                <input
                                    type="text"
                                    name="search"
                                    id="search"
                                    class="form-control"
                                    value="{{ request('search') }}"
                                    placeholder="เลขออเดอร์ / รหัสออเดอร์ / ชื่อลูกค้า / เบอร์โทร / อีเมล"
                                >
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="status" class="form-label">
                                    สถานะ
                                </label>

                                <select
                                    name="status"
                                    id="status"
                                    class="form-select"
                                >
                                    <option value="">ทุกสถานะ</option>
                                    <option value="รอเจ้าหน้าที่รับคำสั่งซื้อ"
                                        @selected(request('status') === 'รอเจ้าหน้าที่รับคำสั่งซื้อ')>
                                        รอเจ้าหน้าที่รับคำสั่งซื้อ
                                    </option>
                                    <option value="กำลังดำเนินการ"
                                        @selected(request('status') === 'กำลังดำเนินการ')>
                                        กำลังดำเนินการ
                                    </option>
                                    <option value="จัดส่งแล้ว"
                                        @selected(request('status') === 'จัดส่งแล้ว')>
                                        จัดส่งแล้ว
                                    </option>
                                    <option value="สำเร็จ"
                                        @selected(request('status') === 'สำเร็จ')>
                                        สำเร็จ
                                    </option>
                                    <option value="ยกเลิก"
                                        @selected(request('status') === 'ยกเลิก')>
                                        ยกเลิก
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="payment_method" class="form-label">
                                    การชำระเงิน
                                </label>

                                <select
                                    name="payment_method"
                                    id="payment_method"
                                    class="form-select"
                                >
                                    <option value="">ทุกวิธี</option>
                                    <option value="โอนผ่านบัญชีธนาคาร"
                                        @selected(request('payment_method') === 'โอนผ่านบัญชีธนาคาร')>
                                        QR Code / โอนผ่านบัญชีธนาคาร
                                    </option>
                                    <option value="เก็บเงินปลายทาง"
                                        @selected(request('payment_method') === 'เก็บเงินปลายทาง')>
                                        เก็บเงินปลายทาง
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <label for="date_from" class="form-label">
                                    ตั้งแต่วันที่
                                </label>

                                <input
                                    type="date"
                                    name="date_from"
                                    id="date_from"
                                    class="form-control"
                                    value="{{ request('date_from') }}"
                                >
                            </div>

                            <div class="col-lg-3">
                                <div class="filter-actions">
                                    <button
                                        type="submit"
                                        class="btn btn-search"
                                    >
                                        <i class="bi bi-search"></i>
                                        ค้นหา
                                    </button>

                                    <a
                                        href="{{ url()->current() }}"
                                        class="btn btn-reset"
                                    >
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        ล้างค่า
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-bag-fill"></i>
                    </div>
                    <div class="summary-label">คำสั่งซื้อทั้งหมดในหน้านี้</div>
                    <div class="summary-value">
                        {{ number_format($orderCollection->count()) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="summary-label">ยอดชำระ QR Code</div>
                    <div class="summary-value">
                        ฿{{ number_format($qrTotal, 2) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div class="summary-label">ยอดเก็บเงินปลายทาง</div>
                    <div class="summary-value">
                        ฿{{ number_format($codTotal, 2) }}
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="summary-label">คำสั่งซื้อสำเร็จในหน้านี้</div>
                    <div class="summary-value">
                        {{ number_format($completedCount) }}
                    </div>
                </div>
            </div>

            <section class="history-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="bi bi-list-ul"></i>
                        รายการคำสั่งซื้อ
                    </h3>
                </div>

                <div class="table-responsive">
                    <table class="table history-table">
                        <thead>
                            <tr>
                                <th>วันเวลา</th>
                                <th>เลขออเดอร์</th>
                                <th>ลูกค้า</th>
                                <th>วิธีชำระ</th>
                                <th>สถานะ</th>
                                <th class="text-end">ยอดรวม</th>
                                <th class="text-center">รายละเอียด</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $paymentMethod = $order->payment_method ?? '';
                                    $orderStatus = $order->status ?? 'pending';
                                    $total = $order->total_amount
                                        ?? $order->total
                                        ?? 0;

                                    $statusLabels = [
                                        'pending' => 'รอดำเนินการ',
                                        'รอเจ้าหน้าที่รับคำสั่งซื้อ' => 'รอเจ้าหน้าที่รับคำสั่งซื้อ',
                                        'processing' => 'กำลังดำเนินการ',
                                        'กำลังดำเนินการ' => 'กำลังดำเนินการ',
                                        'shipped' => 'จัดส่งแล้ว',
                                        'จัดส่งแล้ว' => 'จัดส่งแล้ว',
                                        'completed' => 'สำเร็จ',
                                        'delivered' => 'สำเร็จ',
                                        'สำเร็จ' => 'สำเร็จ',
                                        'เสร็จสิ้น' => 'สำเร็จ',
                                        'จัดส่งสำเร็จ' => 'สำเร็จ',
                                        'cancelled' => 'ยกเลิก',
                                        'ยกเลิก' => 'ยกเลิก',
                                    ];

                                    $statusClass = match ($orderStatus) {
                                        'processing', 'กำลังดำเนินการ' => 'status-processing',
                                        'shipped', 'จัดส่งแล้ว' => 'status-shipped',
                                        'completed', 'delivered',
                                        'สำเร็จ', 'เสร็จสิ้น', 'จัดส่งสำเร็จ' => 'status-completed',
                                        'cancelled', 'ยกเลิก' => 'status-cancelled',
                                        default => 'status-pending',
                                    };

                                    $isQr = in_array(
                                        trim((string) $paymentMethod),
                                        [
                                            'qr',
                                            'qrcode',
                                            'promptpay',
                                            'โอนผ่านบัญชีธนาคาร',
                                            'โอนเงิน',
                                            'พร้อมเพย์',
                                        ],
                                        true
                                    );
                                @endphp

                                <tr>
                                    <td>
                                        <div>
                                            {{ optional($order->created_at)->format('d/m/Y') ?? '-' }}
                                        </div>
                                        <div class="customer-meta">
                                            {{ optional($order->created_at)->format('H:i') ?? '-' }} น.
                                        </div>
                                    </td>

                                    <td>
                                        <span class="order-code">
                                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="customer-name">
                                            {{ $order->user->name
                                                ?? $order->customer_name
                                                ?? 'ลูกค้าทั่วไป' }}
                                        </div>

                                        <div class="customer-meta">
                                            {{ $order->user->phone
                                                ?? $order->customer_phone
                                                ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="payment-badge {{ $isQr ? 'payment-qr' : 'payment-cod' }}">
                                            <i class="bi {{ $isQr ? 'bi-qr-code' : 'bi-truck' }}"></i>
                                            {{ $isQr ? 'QR Code' : 'เก็บเงินปลายทาง' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $statusLabels[$orderStatus] ?? $orderStatus }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <span class="order-total">
                                            ฿{{ number_format((float) $total, 2) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @php
                                            $detailUrl = \Illuminate\Support\Facades\Route::has('online.orders.show')
                                                ? route('online.orders.show', $order->id)
                                                : (
                                                    \Illuminate\Support\Facades\Route::has('orders.show')
                                                        ? route('orders.show', $order->id)
                                                        : '#'
                                                );
                                        @endphp

                                        <a
                                            href="{{ $detailUrl }}"
                                            class="btn-detail"
                                            title="ดูรายละเอียด"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="bi bi-inbox"></i>
                                            </div>

                                            <h4 class="text-white fw-bold">
                                                ยังไม่มีประวัติคำสั่งซื้อ
                                            </h4>

                                            <p class="mb-0">
                                                เมื่อมีคำสั่งซื้อออนไลน์
                                                รายการจะแสดงในหน้านี้
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (
                    $orders instanceof \Illuminate\Pagination\AbstractPaginator
                    && $orders->hasPages()
                )
                    <div class="pagination-wrap">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection