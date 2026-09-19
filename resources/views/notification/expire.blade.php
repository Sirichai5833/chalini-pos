@extends('layouts.layout')

@section('title', 'สินค้าใกล้หมดอายุ')
@section('page-title', 'สินค้าใกล้หมดอายุ')

@section('content')
<style>
    .expiry-page {
        --expiry-yellow: #ffc107;
        --expiry-yellow-soft: #ffe27a;
        --expiry-black: #090909;
        --expiry-panel: #171717;
        --expiry-panel-soft: #202020;
        --expiry-border: rgba(255, 193, 7, .24);
        --expiry-text: #f7f7f7;
        --expiry-muted: #aaa;
        color: var(--expiry-text);
    }

    .expiry-page .page-shell {
        max-width: 1280px;
        margin: 0 auto;
    }

    .expiry-page .page-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding: 1.45rem;
        border: 1px solid var(--expiry-border);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 92% 12%,
                rgba(255, 193, 7, .2),
                transparent 26%
            ),
            linear-gradient(135deg, #1d1d1d, #090909);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .34);
    }

    .expiry-page .hero-content {
        position: relative;
        z-index: 2;
    }

    .expiry-page .hero-kicker {
        margin-bottom: .35rem;
        color: var(--expiry-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .14em;
    }

    .expiry-page .hero-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 950;
    }

    .expiry-page .hero-subtitle {
        margin: .45rem 0 0;
        color: var(--expiry-muted);
        font-size: .9rem;
    }

    .expiry-page .hero-summary {
        position: relative;
        z-index: 2;
        min-width: 160px;
        padding: .9rem 1rem;
        border: 1px solid rgba(255, 193, 7, .28);
        border-radius: 16px;
        background: rgba(255, 193, 7, .08);
        text-align: center;
    }

    .expiry-page .hero-summary-number {
        color: var(--expiry-yellow);
        font-size: 1.8rem;
        font-weight: 950;
        line-height: 1;
    }

    .expiry-page .hero-summary-label {
        margin-top: .35rem;
        color: #ddd;
        font-size: .76rem;
        font-weight: 750;
    }

    .expiry-page .expiry-card {
        overflow: hidden;
        border: 1px solid var(--expiry-border);
        border-radius: 22px;
        background: linear-gradient(180deg, #191919, #101010);
        box-shadow: 0 18px 44px rgba(0, 0, 0, .32);
    }

    .expiry-page .expiry-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid var(--expiry-border);
        background: rgba(255, 193, 7, .05);
    }

    .expiry-page .expiry-card-title {
        margin: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .expiry-page .legend {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
    }

    .expiry-page .legend-item {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #ccc;
        font-size: .72rem;
    }

    .expiry-page .legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
    }

    .expiry-page .dot-expired {
        background: #dc3545;
    }

    .expiry-page .dot-danger {
        background: #fd7e14;
    }

    .expiry-page .dot-warning {
        background: #ffc107;
    }

    .expiry-page .expiry-table {
        width: 100%;
        margin: 0;
        color: var(--expiry-text);
        border-collapse: collapse;
    }

    .expiry-page .expiry-table thead th {
        padding: .95rem 1rem;
        border-bottom: 1px solid #363636;
        background: #0d0d0d;
        color: var(--expiry-yellow);
        font-size: .73rem;
        font-weight: 900;
        white-space: nowrap;
        text-align: center;
    }

    .expiry-page .expiry-table tbody td {
        padding: 1rem;
        border-top: 1px solid #2d2d2d;
        color: #eee;
        vertical-align: middle;
        text-align: center;
    }

    .expiry-page .expiry-table tbody tr {
        transition: .2s ease;
    }

    .expiry-page .expiry-table tbody tr:hover {
        background: rgba(255, 193, 7, .045);
    }

    .expiry-page .product-name {
        color: #fff;
        font-weight: 900;
        text-align: left;
    }

    .expiry-page .batch-code {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .6rem;
        border: 1px solid #444;
        border-radius: 999px;
        background: #202020;
        color: #ddd;
        font-size: .74rem;
        font-weight: 800;
    }

    .expiry-page .quantity-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .38rem .65rem;
        border-radius: 999px;
        background: rgba(59, 130, 246, .1);
        color: #93c5fd;
        font-size: .74rem;
        font-weight: 850;
    }

    .expiry-page .expiry-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .42rem .7rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 900;
    }

    .expiry-page .badge-expired {
        border: 1px solid rgba(220, 53, 69, .45);
        background: rgba(220, 53, 69, .12);
        color: #ff9ca6;
    }

    .expiry-page .badge-today {
        border: 1px solid rgba(255, 193, 7, .45);
        background: rgba(255, 193, 7, .12);
        color: #ffe27a;
    }

    .expiry-page .badge-soon {
        border: 1px solid rgba(253, 126, 20, .4);
        background: rgba(253, 126, 20, .1);
        color: #fdba74;
    }

    .expiry-page .badge-normal {
        border: 1px solid rgba(34, 197, 94, .35);
        background: rgba(34, 197, 94, .09);
        color: #86efac;
    }

    .expiry-page .btn-acknowledge {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .45rem .72rem;
        border: 1px solid var(--expiry-yellow);
        border-radius: 10px;
        background: linear-gradient(
            135deg,
            var(--expiry-yellow),
            var(--expiry-yellow-soft)
        );
        color: #111;
        font-size: .75rem;
        font-weight: 900;
        transition: .2s ease;
    }

    .expiry-page .btn-acknowledge:hover {
        color: #000;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(255, 193, 7, .18);
    }

    .expiry-page .empty-state {
        padding: 3.5rem 1rem;
        text-align: center;
    }

    .expiry-page .empty-icon {
        width: 84px;
        height: 84px;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        border: 1px dashed rgba(34, 197, 94, .35);
        border-radius: 24px;
        background: rgba(34, 197, 94, .08);
        color: #86efac;
        font-size: 2.2rem;
    }

    .expiry-page .row-expired {
        background: rgba(220, 53, 69, .055);
    }

    .expiry-page .row-danger {
        background: rgba(253, 126, 20, .045);
    }

    .expiry-page .row-warning {
        background: rgba(255, 193, 7, .04);
    }

    @media (max-width: 767.98px) {
        .expiry-page .page-hero,
        .expiry-page .expiry-card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .expiry-page .hero-summary {
            width: 100%;
        }
    }
</style>

<div class="expiry-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="page-hero">
                <div class="hero-content">
                    <div class="hero-kicker">
                        EXPIRY MONITORING
                    </div>

                    <h2 class="hero-title">
                        <i class="bi bi-hourglass-split me-2 text-warning"></i>
                        สินค้าใกล้หมดอายุ
                    </h2>

                    <p class="hero-subtitle">
                        ตรวจสอบล็อตสินค้าที่ใกล้ถึงวันหมดอายุและบันทึกการจัดการ
                    </p>
                </div>

                <div class="hero-summary">
                    <div class="hero-summary-number">
                        {{ number_format(
                            method_exists($nearExpiryProducts, 'total')
                                ? $nearExpiryProducts->total()
                                : collect($nearExpiryProducts)->count()
                        ) }}
                    </div>

                    <div class="hero-summary-label">
                        รายการที่ต้องตรวจสอบ
                    </div>
                </div>
            </section>

            <section class="expiry-card">
                <div class="expiry-card-header">
                    <h3 class="expiry-card-title">
                        <i class="bi bi-list-check me-2 text-warning"></i>
                        รายการล็อตสินค้า
                    </h3>

                    <div class="legend">
                        <span class="legend-item">
                            <span class="legend-dot dot-expired"></span>
                            หมดอายุแล้ว
                        </span>

                        <span class="legend-item">
                            <span class="legend-dot dot-danger"></span>
                            ภายใน 7 วัน
                        </span>

                        <span class="legend-item">
                            <span class="legend-dot dot-warning"></span>
                            ภายใน 14 วัน
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="expiry-table">
                        <thead>
                            <tr>
                                <th class="text-start">ชื่อสินค้า</th>
                                <th>รหัสล็อต</th>
                                <th>จำนวน</th>
                                <th>หน่วยนับ</th>
                                <th>วันหมดอายุ</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($nearExpiryProducts as $item)
                                @php
                                    $expiryDate = \Carbon\Carbon::parse(
                                        $item->expiry_date
                                    )->startOfDay();

                                    $today = now()->startOfDay();

                                    $diffDays = $today->diffInDays(
                                        $expiryDate,
                                        false
                                    );

                                    $rowClass = match (true) {
                                        $diffDays < 0 => 'row-expired',
                                        $diffDays <= 7 => 'row-danger',
                                        $diffDays <= 14 => 'row-warning',
                                        default => '',
                                    };
                                @endphp

                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <div class="product-name">
                                            {{ $item->name }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="batch-code">
                                            <i class="bi bi-upc-scan"></i>
                                            {{ $item->batch_code ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="quantity-pill">
                                            <i class="bi bi-box-seam"></i>
                                            {{ number_format($item->quantity) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->unit_name ?? '-' }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $expiryDate->format('d/m/Y') }}
                                        </strong>
                                    </td>

                                    <td>
                                        @if ($diffDays < 0)
                                            <span class="expiry-badge badge-expired">
                                                <i class="bi bi-x-circle-fill"></i>
                                                หมดอายุแล้ว
                                            </span>
                                        @elseif ($diffDays === 0)
                                            <span class="expiry-badge badge-today">
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                                หมดอายุวันนี้
                                            </span>
                                        @elseif ($diffDays <= 7)
                                            <span class="expiry-badge badge-soon">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                เหลือ {{ $diffDays }} วัน
                                            </span>
                                        @else
                                            <span class="expiry-badge badge-normal">
                                                <i class="bi bi-clock-fill"></i>
                                                เหลือ {{ $diffDays }} วัน
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <form
                                            action="{{ route(
                                                'notification.expiry.acknowledge',
                                                $item->id
                                            ) }}"
                                            method="POST"
                                            class="acknowledge-form"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn-acknowledge"
                                            >
                                                <i class="bi bi-check-circle-fill"></i>
                                                จัดการแล้ว
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </div>

                                            <h4 class="text-white fw-bold">
                                                ไม่มีสินค้าใกล้หมดอายุ
                                            </h4>

                                            <p class="text-secondary mb-0">
                                                ขณะนี้ยังไม่มีล็อตสินค้าที่ต้องดำเนินการ
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (
                    method_exists($nearExpiryProducts, 'hasPages')
                    && $nearExpiryProducts->hasPages()
                )
                    <div class="p-3 border-top border-secondary">
                        {{ $nearExpiryProducts
                            ->withQueryString()
                            ->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document
        .querySelectorAll('.acknowledge-form')
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const submitForm = function () {
                    const button = form.querySelector('button');

                    if (button) {
                        button.disabled = true;
                        button.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>
                            กำลังบันทึก
                        `;
                    }

                    form.submit();
                };

                if (typeof Swal === 'undefined') {
                    if (window.confirm('ยืนยันว่าจัดการรายการนี้แล้ว?')) {
                        submitForm();
                    }

                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการจัดการ?',
                    text: 'รายการนี้จะถูกนำออกจากหน้าการแจ้งเตือน',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    background: '#171717',
                    color: '#ffffff',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#555555',
                    customClass: {
                        confirmButton: 'text-dark fw-bold'
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitForm();
                    }
                });
            });
        });
});
</script>
@endsection