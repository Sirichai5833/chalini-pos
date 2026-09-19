<!-- resources/views/sale/show-order.blade.php -->
@extends('layouts.layout')

@section('title', 'พิมพ์ใบเสร็จย้อนหลัง')
@section('page-title', 'พิมพ์ใบเสร็จย้อนหลัง')

@section('content')
<style>
    .order-detail-page {
        --od-yellow: #ffc107;
        --od-yellow-soft: #ffe27a;
        --od-panel: #171717;
        --od-panel-soft: #202020;
        --od-border: #3a3a3a;
        --od-text: #f7f7f7;
        --od-muted: #a8a8a8;
        color: var(--od-text);
    }

    .order-detail-page .page-shell {
        max-width: 1050px;
        margin: 0 auto;
    }

    .order-detail-page .hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1.25rem;
        border: 1px solid rgba(255, 193, 7, .24);
        border-radius: 22px;
        background:
            radial-gradient(
                circle at 90% 10%,
                rgba(255, 193, 7, .16),
                transparent 28%
            ),
            linear-gradient(135deg, #202020, #0d0d0d);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .28);
    }

    .order-detail-page .hero-icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 18px;
        background: linear-gradient(
            135deg,
            var(--od-yellow),
            var(--od-yellow-soft)
        );
        color: #111;
        font-size: 1.45rem;
    }

    .order-detail-page .hero h2 {
        margin: 0;
        color: #fff;
        font-size: 1.4rem;
        font-weight: 900;
    }

    .order-detail-page .hero p {
        margin: .35rem 0 0;
        color: var(--od-muted);
        font-size: .83rem;
    }

    .order-detail-page .order-code {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .75rem;
        border: 1px solid rgba(255, 193, 7, .25);
        border-radius: 999px;
        background: rgba(255, 193, 7, .08);
        color: var(--od-yellow);
        font-size: .76rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .order-detail-page .panel {
        overflow: hidden;
        border: 1px solid var(--od-border);
        border-radius: 19px;
        background: var(--od-panel);
        box-shadow: 0 18px 45px rgba(0, 0, 0, .25);
    }

    .order-detail-page .panel-header {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--od-border);
        background: linear-gradient(135deg, #202020, #141414);
    }

    .order-detail-page .panel-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #fff;
        font-size: .93rem;
        font-weight: 850;
    }

    .order-detail-page .panel-title i {
        color: var(--od-yellow);
    }

    .order-detail-page .panel-body {
        padding: 1.15rem;
    }

    .order-detail-page .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .8rem;
    }

    .order-detail-page .info-item {
        padding: .9rem;
        border: 1px solid var(--od-border);
        border-radius: 14px;
        background: var(--od-panel-soft);
    }

    .order-detail-page .info-label {
        display: block;
        margin-bottom: .3rem;
        color: var(--od-muted);
        font-size: .72rem;
    }

    .order-detail-page .info-value {
        color: #fff;
        font-size: .9rem;
        font-weight: 800;
        word-break: break-word;
    }

    .order-detail-page .status-badge,
    .order-detail-page .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .62rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 850;
    }

    .order-detail-page .status-pending {
        background: rgba(255, 193, 7, .1);
        color: #ffd75e;
    }

    .order-detail-page .status-processing {
        background: rgba(13, 110, 253, .12);
        color: #7fb1ff;
    }

    .order-detail-page .status-completed {
        background: rgba(25, 135, 84, .12);
        color: #7ee2aa;
    }

    .order-detail-page .status-cancelled {
        background: rgba(220, 53, 69, .12);
        color: #ff929c;
    }

    .order-detail-page .payment-qr {
        border: 1px solid rgba(13, 202, 240, .3);
        background: rgba(13, 202, 240, .1);
        color: #74dfff;
    }

    .order-detail-page .payment-cod {
        border: 1px solid rgba(255, 193, 7, .3);
        background: rgba(255, 193, 7, .09);
        color: #ffd75e;
    }

    .order-detail-page .order-table {
        margin: 0;
        color: #e8e8e8;
        vertical-align: middle;
    }

    .order-detail-page .order-table thead th {
        padding: .9rem;
        border-bottom: 1px solid var(--od-border);
        background: #131313;
        color: #bdbdbd;
        font-size: .73rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .order-detail-page .order-table tbody td {
        padding: .9rem;
        border-color: #2f2f2f;
        background: transparent;
        color: #e7e7e7;
    }

    .order-detail-page .product-name {
        color: #fff;
        font-weight: 800;
    }

    .order-detail-page .product-meta {
        margin-top: .2rem;
        color: var(--od-muted);
        font-size: .72rem;
    }

    .order-detail-page .summary-list {
        display: grid;
        gap: .7rem;
    }

    .order-detail-page .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: .7rem;
        border-bottom: 1px dashed #3a3a3a;
        color: #cfcfcf;
        font-size: .82rem;
    }

    .order-detail-page .summary-row:last-child {
        border-bottom: 0;
    }

    .order-detail-page .summary-total {
        padding: .95rem;
        border: 1px solid rgba(255, 193, 7, .26);
        border-radius: 14px;
        background: rgba(255, 193, 7, .06);
    }

    .order-detail-page .summary-total .summary-row {
        border-bottom: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .order-detail-page .note-box {
        padding: .7rem .85rem;
        border: 1px solid var(--od-border);
        border-radius: 10px;
        background: var(--od-panel-soft);
        color: #f1f1f1;
        font-size: .9rem;
        line-height: 1.5;
        white-space: pre-line;
    }

    .order-detail-page .note-box.is-cancelled {
        border: 1px solid rgba(255, 193, 7, .34);
        background: #181818;
        color: #fff;
        box-shadow: none;
    }

    .order-detail-page .note-box .note-title {
        display: flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .45rem;
        color: var(--od-yellow);
        font-size: 1.08rem;
        font-weight: 950;
    }

    .order-detail-page .note-box .note-content {
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.5;
        word-break: break-word;
    }

    .order-detail-page .note-box.is-cancelled .note-content {
        color: #fff;
        font-size: 1.05rem;
        font-weight: 900;
    }


    .order-detail-page .note-panel .panel-header {
        padding: .65rem .85rem;
    }

    .order-detail-page .note-panel .panel-body {
        padding: .75rem .85rem;
    }

    .order-detail-page .note-panel .panel-title {
        font-size: .95rem;
    }

    .order-detail-page .note-panel-cancelled .panel-header {
        border-bottom-color: rgba(255, 193, 7, .32);
        background:
            linear-gradient(
                135deg,
                rgba(255, 193, 7, .10),
                rgba(255, 193, 7, .025)
            ),
            #171717;
    }

    .order-detail-page .note-panel-cancelled .panel-title {
        color: #fff;
        font-size: 1.08rem;
        font-weight: 950;
    }

    .order-detail-page .note-panel-cancelled .panel-title i {
        color: var(--od-yellow);
    }

    .order-detail-page .page-actions {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        margin-top: 1rem;
    }

    .order-detail-page .btn-back,
    .order-detail-page .btn-print {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .65rem 1rem;
        border-radius: 12px;
        font-weight: 850;
        text-decoration: none;
    }

    .order-detail-page .btn-back {
        border: 1px solid #4b4b4b;
        background: transparent;
        color: #ddd;
    }

    .order-detail-page .btn-print {
        border: 1px solid var(--od-yellow);
        background: linear-gradient(
            135deg,
            var(--od-yellow),
            var(--od-yellow-soft)
        );
        color: #111;
    }


    .order-detail-page .receipt-heading {
        display: none;
    }

    .order-detail-page .receipt-footer {
        display: none;
    }

    @media (max-width: 767.98px) {
        .order-detail-page .hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .order-detail-page .info-grid {
            grid-template-columns: 1fr;
        }

        .order-detail-page .page-actions {
            flex-direction: column-reverse;
        }

        .order-detail-page .page-actions a,
        .order-detail-page .page-actions button {
            width: 100%;
        }
    }

    @media print {
        @page {
            size: 80mm auto;
            margin: 4mm;
        }

        body {
            background: #fff !important;
            color: #000 !important;
        }

        body * {
            visibility: hidden;
        }

        .order-detail-page,
        .order-detail-page * {
            visibility: visible;
        }

        .order-detail-page {
            position: absolute;
            inset: 0;
            width: 72mm;
            margin: 0 auto;
            color: #000 !important;
            background: #fff !important;
            font-family: "Tahoma", sans-serif;
        }

        .order-detail-page .container-fluid,
        .order-detail-page .page-shell {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .order-detail-page .page-actions,
        .order-detail-page .hero,
        .order-detail-page .panel-header,
        .order-detail-page .info-grid {
            display: none !important;
        }

        .order-detail-page .note-box {
            display: block !important;
            margin-top: 8px;
            padding: 6px 0;
            border: 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            border-radius: 0;
            background: #fff !important;
            color: #000 !important;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.55;
            white-space: pre-line;
        }

        .order-detail-page .note-box .note-title {
            color: #000 !important;
            font-size: 12px;
            font-weight: 800;
        }

        .order-detail-page .note-box .note-content,
        .order-detail-page .note-box.is-cancelled .note-content {
            color: #000 !important;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.5;
        }

        .order-detail-page .receipt-heading,
        .order-detail-page .receipt-footer {
            display: block !important;
            color: #000 !important;
            text-align: center;
        }

        .order-detail-page .receipt-heading {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }

        .order-detail-page .receipt-heading h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
        }

        .order-detail-page .receipt-heading p {
            margin: 2px 0 0;
            font-size: 10px;
        }

        .order-detail-page .row {
            display: block !important;
        }

        .order-detail-page .col-lg-8,
        .order-detail-page .col-lg-4 {
            width: 100% !important;
            max-width: 100% !important;
        }

        .order-detail-page .panel {
            margin: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .order-detail-page .panel-body {
            padding: 0 !important;
        }

        .order-detail-page .order-table {
            width: 100%;
            color: #000 !important;
            font-size: 10px;
        }

        .order-detail-page .order-table thead th,
        .order-detail-page .order-table tbody td {
            padding: 4px 2px !important;
            border-color: #000 !important;
            background: #fff !important;
            color: #000 !important;
        }

        .order-detail-page .product-name,
        .order-detail-page .product-meta,
        .order-detail-page .summary-row,
        .order-detail-page .summary-total {
            color: #000 !important;
        }

        .order-detail-page .product-meta {
            font-size: 9px;
        }

        .order-detail-page .summary-list {
            margin-top: 8px;
            gap: 4px;
        }

        .order-detail-page .summary-row {
            padding: 3px 0;
            border-bottom: 1px dashed #000;
            font-size: 10px;
        }

        .order-detail-page .summary-total {
            padding: 6px 0;
            border: 0;
            border-radius: 0;
            background: #fff !important;
        }

        .order-detail-page .summary-total .summary-row {
            font-size: 13px;
        }

        .order-detail-page .receipt-footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 9px;
            line-height: 1.5;
        }
    }
</style>

@php
    $status = $order->status ?? 'รอดำเนินการ';
    $paymentMethod = $order->payment_method ?? '-';

    $statusClass = match ($status) {
        'สำเร็จ', 'completed', 'delivered' => 'status-completed',
        'กำลังดำเนินการ', 'processing', 'จัดส่งแล้ว', 'shipped' => 'status-processing',
        'ยกเลิก', 'cancelled' => 'status-cancelled',
        default => 'status-pending',
    };

    $isQr = in_array(
        $paymentMethod,
        ['โอนผ่านบัญชีธนาคาร', 'qr', 'qrcode', 'promptpay'],
        true
    );

    $items = $order->items ?? collect();

    $subtotal = (float) (
        $order->items_total
        ?? $items->sum(fn ($item) =>
            (float) ($item->total ?? (
                ($item->quantity ?? 0)
                * ($item->price ?? 0)
            ))
        )
    );

    $shippingFee = (float) ($order->shipping_fee ?? 0);
    $grandTotal = (float) (
        $order->total_amount
        ?? $subtotal + $shippingFee
    );

    /*
     * หมายเหตุจากพนักงานตอนยกเลิก
     * รองรับหลายชื่อคอลัมน์ที่มักใช้ในระบบ
     */
    $isCancelled = in_array(
        mb_strtolower(trim((string) $status)),
        ['cancelled', 'canceled', 'ยกเลิก'],
        true
    );

    $cancellationNote = collect([
        $order->cancellation_note ?? null,
        $order->cancel_note ?? null,
        $order->cancel_reason ?? null,
        $order->cancellation_reason ?? null,
        $order->cancelled_reason ?? null,
        $order->employee_note ?? null,
        $order->staff_note ?? null,
        $order->admin_note ?? null,
    ])->first(fn ($value) => filled($value));

    $generalNote = collect([
        $order->note ?? null,
        $order->customer_note ?? null,
        $order->notes ?? null,
        $order->remark ?? null,
    ])->first(fn ($value) => filled($value));

    $displayNote = $isCancelled
        ? ($cancellationNote ?? $generalNote)
        : ($generalNote ?? $cancellationNote);

    $noteTitle = $isCancelled
        ? 'เหตุผลที่ยกเลิก'
        : 'หมายเหตุ';
@endphp

<div class="order-detail-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <div class="receipt-heading">
                <h1>{{ $shopSettings->shop_name ?? config('app.name') }}</h1>
                <p>ใบเสร็จรับเงินย้อนหลัง</p>
                <p>
                    เลขที่:
                    {{ $order->order_code
                        ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </p>
                <p>
                    วันที่:
                    {{ optional($order->created_at)->format('d/m/Y H:i') ?? '-' }}
                    น.
                </p>
            </div>
            <section class="hero">
                <div class="d-flex align-items-center gap-3">
                    <div class="hero-icon">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>

                    <div>
                        <h2>ใบเสร็จคำสั่งซื้อย้อนหลัง</h2>
                        <p>
                            ตรวจสอบรายการย้อนหลังและพิมพ์ใบเสร็จ
                            สำหรับคำสั่งซื้อนี้
                        </p>
                    </div>
                </div>

                <div class="order-code">
                    <i class="bi bi-hash"></i>
                    {{ $order->order_code
                        ?? str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </section>

            @if ($isCancelled)
                <div class="mb-2">
                    <section class="panel note-panel {{ $isCancelled ? 'note-panel-cancelled' : '' }}">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="bi {{ $isCancelled ? 'bi-x-octagon-fill' : 'bi-card-text' }}"></i>
                                {{ $noteTitle }}
                            </h3>
                        </div>

                        <div class="panel-body">
                            <div class="note-box {{ $isCancelled ? 'is-cancelled' : '' }}">
                                <div class="note-content">
                                    {{ filled($displayNote)
                                        ? $displayNote
                                        : ($isCancelled
                                            ? 'ไม่ได้ระบุเหตุผลที่ยกเลิก'
                                            : 'ไม่มีหมายเหตุ') }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @endif

            <div class="row g-3">
                <div class="col-lg-8">
                    <section class="panel mb-3">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="bi bi-bag-check-fill"></i>
                                ข้อมูลคำสั่งซื้อ
                            </h3>
                        </div>

                        <div class="panel-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">วันที่สั่งซื้อ</span>
                                    <span class="info-value">
                                        {{ optional($order->created_at)->format('d/m/Y H:i') ?? '-' }} น.
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">สถานะ</span>
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">วิธีชำระเงิน</span>
                                    <span class="payment-badge {{ $isQr ? 'payment-qr' : 'payment-cod' }}">
                                        <i class="bi {{ $isQr ? 'bi-qr-code' : 'bi-truck' }}"></i>
                                        {{ $isQr ? 'QR Code' : 'เก็บเงินปลายทาง' }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">เลขติดตาม / เบอร์โทร</span>
                                    <span class="info-value">
                                        {{ $order->tracking_number ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="panel mb-3">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="bi bi-box-seam-fill"></i>
                                รายการสินค้า
                            </h3>
                        </div>

                        <div class="table-responsive">
                            <table class="table order-table">
                                <thead>
                                    <tr>
                                        <th>สินค้า</th>
                                        <th class="text-center">จำนวน</th>
                                        <th class="text-end">ราคาต่อหน่วย</th>
                                        <th class="text-end">รวม</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($order->items as $item)
                                        @php
                                            $productUnit = $item->productUnit ?? null;
                                            $product = $productUnit->product ?? null;
                                            $quantity = (float) ($item->quantity ?? 0);
                                            $price = (float) ($item->price ?? 0);
                                            $lineTotal = (float) (
                                                $item->total
                                                ?? $quantity * $price
                                            );
                                        @endphp

                                        <tr>
                                            <td>
                                                <div class="product-name">
                                                    {{ $product->name
                                                        ?? $item->product_name
                                                        ?? 'ไม่พบชื่อสินค้า' }}
                                                </div>

                                                <div class="product-meta">
                                                    หน่วย:
                                                    {{ $productUnit->unit_name ?? '-' }}
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($quantity, 0) }}
                                            </td>

                                            <td class="text-end">
                                                ฿{{ number_format($price, 2) }}
                                            </td>

                                            <td class="text-end fw-bold">
                                                ฿{{ number_format($lineTotal, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                ไม่พบรายการสินค้า
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                </div>

                <div class="col-lg-4">
                    <section class="panel mb-3">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="bi bi-person-fill"></i>
                                ข้อมูลลูกค้า
                            </h3>
                        </div>

                        <div class="panel-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">ชื่อลูกค้า</span>
                                    <span class="info-value">
                                        {{ $order->user->name ?? 'ลูกค้าทั่วไป' }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">อีเมล</span>
                                    <span class="info-value">
                                        {{ $order->user->email ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">เบอร์โทร</span>
                                    <span class="info-value">
                                        {{ $order->user->phone
                                            ?? $order->tracking_number
                                            ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="panel">
                        <div class="panel-header">
                            <h3 class="panel-title">
                                <i class="bi bi-calculator-fill"></i>
                                สรุปยอด
                            </h3>
                        </div>

                        <div class="panel-body">
                            <div class="summary-list">
                                <div class="summary-row">
                                    <span>ยอดสินค้า</span>
                                    <strong>
                                        ฿{{ number_format($subtotal, 2) }}
                                    </strong>
                                </div>

                                <div class="summary-row">
                                    <span>ค่าจัดส่ง</span>
                                    <strong>
                                        ฿{{ number_format($shippingFee, 2) }}
                                    </strong>
                                </div>

                                <div class="summary-total">
                                    <div class="summary-row">
                                        <span>ยอดรวมสุทธิ</span>
                                        <strong>
                                            ฿{{ number_format($grandTotal, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="receipt-footer">
                <div>ขอบคุณที่ใช้บริการ</div>
                <div>
                    วิธีชำระ:
                    {{ $isQr ? 'QR Code' : 'เก็บเงินปลายทาง' }}
                </div>
                <div>พิมพ์ย้อนหลังเมื่อ {{ now()->format('d/m/Y H:i') }} น.</div>
            </div>

            <div class="page-actions">
                <a
                    href="{{ url()->previous() }}"
                    class="btn btn-back"
                >
                    <i class="bi bi-arrow-left"></i>
                    ย้อนกลับ
                </a>

                <button
                    type="button"
                    class="btn btn-print"
                    onclick="window.print()"
                >
                    <i class="bi bi-printer-fill"></i>
                    พิมพ์ใบเสร็จย้อนหลัง
                </button>
            </div>
        </div>
    </div>
</div>
@endsection