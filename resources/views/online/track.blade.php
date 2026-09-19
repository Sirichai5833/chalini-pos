@extends('layouts.online')

@section('content')

@php
    $shopPhone = \App\Models\Setting::get('phone', '-');
    $shopPhoneLink = preg_replace('/\D+/', '', (string) $shopPhone);
@endphp
    <style>
        :root {
            --order-yellow: #ffd43b;
            --order-yellow-dark: #e0b400;
            --order-black: #090909;
            --order-surface: #121212;
            --order-card: #181818;
            --order-soft: #222222;
            --order-text: #f7f7f7;
            --order-muted: #a9a9a9;
            --order-border: rgba(255, 212, 59, .22);
            --order-danger: #ef4444;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 30%),
                var(--order-black);
            color: var(--order-text);
        }

        .orders-page {
            min-height: calc(100vh - 70px);
            padding: 1.5rem 0 4rem;
        }

        .orders-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 1.25rem;
            padding: clamp(1.35rem, 3vw, 2.3rem);
            border: 1px solid var(--order-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 88% 20%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #090909 0%, #171717 62%, #2a2200 100%);
            box-shadow: 0 18px 45px rgba(0, 0, 0, .36);
        }

        .orders-hero::after {
            content: "";
            position: absolute;
            right: -65px;
            bottom: -100px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .10);
        }

        .orders-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .orders-kicker {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .75rem;
            padding: .34rem .68rem;
            border: 1px solid var(--order-border);
            border-radius: 999px;
            background: rgba(255, 212, 59, .08);
            color: var(--order-yellow);
            font-size: .72rem;
            font-weight: 850;
            letter-spacing: .03em;
        }

        .orders-title {
            margin: 0;
            color: var(--order-yellow);
            font-size: clamp(1.45rem, 4vw, 2.35rem);
            font-weight: 900;
            line-height: 1.15;
        }

        .orders-subtitle {
            margin: .65rem 0 0;
            color: #c8c8c8;
            font-size: .92rem;
        }

        .orders-hero-icon {
            width: 82px;
            height: 82px;
            display: grid;
            place-items: center;
            flex: none;
            border: 1px solid var(--order-border);
            border-radius: 24px;
            background: rgba(255, 212, 59, .10);
            color: var(--order-yellow);
            font-size: 2.15rem;
            box-shadow: inset 0 0 24px rgba(255, 212, 59, .06);
        }

        .order-warning {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            margin-bottom: 1.25rem;
            padding: 1rem 1.05rem;
            border: 1px solid rgba(255, 212, 59, .30);
            border-left: 5px solid var(--order-yellow);
            border-radius: 16px;
            background: linear-gradient(135deg, #201b05, #15130b);
            color: #ffe992;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .25);
        }

        .order-warning-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            flex: none;
            border-radius: 12px;
            background: var(--order-yellow);
            color: #111;
            font-size: 1rem;
        }

        .order-warning strong {
            color: var(--order-yellow);
        }

        .order-warning a {
            color: #fff1a6;
            font-weight: 850;
            text-decoration: none;
        }

        .order-warning a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .orders-livewire {
            padding: 1rem;
            border: 1px solid var(--order-border);
            border-radius: 22px;
            background: rgba(18, 18, 18, .88);
            box-shadow: 0 16px 38px rgba(0, 0, 0, .32);
            backdrop-filter: blur(10px);
        }

        /* รองรับ Card ภายใน Livewire */
        .orders-livewire .card {
            overflow: hidden;
            border: 1px solid #2d2d2d !important;
            border-radius: 18px !important;
            background: linear-gradient(180deg, #1a1a1a, #131313) !important;
            color: var(--order-text);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .30) !important;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .orders-livewire .card:hover {
            border-color: rgba(255, 212, 59, .55) !important;
            transform: translateY(-3px);
            box-shadow: 0 20px 42px rgba(0, 0, 0, .42) !important;
        }

        .orders-livewire .card-header,
        .orders-livewire .card-footer {
            border-color: #2d2d2d !important;
            background: #151515 !important;
            color: var(--order-text);
        }

        .orders-livewire .card-body {
            color: var(--order-text);
        }

        .orders-livewire .text-muted {
            color: var(--order-muted) !important;
        }

        .orders-livewire .list-group-item {
            border-color: #2b2b2b !important;
            background: transparent !important;
            color: var(--order-text) !important;
        }

        .orders-livewire .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--order-text);
            --bs-table-border-color: #303030;
            --bs-table-striped-bg: rgba(255, 255, 255, .025);
            --bs-table-striped-color: var(--order-text);
            --bs-table-hover-bg: rgba(255, 212, 59, .06);
            --bs-table-hover-color: var(--order-text);
            color: var(--order-text);
        }

        .orders-livewire .btn-primary,
        .orders-livewire .btn-warning {
            border-color: var(--order-yellow) !important;
            background: linear-gradient(135deg, #ffe56b, var(--order-yellow)) !important;
            color: #111 !important;
            font-weight: 850;
        }

        .orders-livewire .btn-outline-primary,
        .orders-livewire .btn-outline-warning {
            border-color: var(--order-yellow) !important;
            color: var(--order-yellow) !important;
        }

        .orders-livewire .btn-outline-primary:hover,
        .orders-livewire .btn-outline-warning:hover {
            background: var(--order-yellow) !important;
            color: #111 !important;
        }

        .orders-livewire .form-control,
        .orders-livewire .form-select {
            border-color: #373737;
            background: #121212;
            color: var(--order-text);
        }

        .orders-livewire .form-control:focus,
        .orders-livewire .form-select:focus {
            border-color: var(--order-yellow);
            background: #161616;
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .13);
        }

        .status-stripe {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            width: 100%;
            height: 7px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .card.position-relative {
            padding-top: 7px;
        }

        .list-group-flush .list-group-item {
            padding-left: 0;
            padding-right: 0;
        }

        .overflow-auto {
            scrollbar-width: thin;
            scrollbar-color: var(--order-yellow) #222;
        }

        .overflow-auto::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #222;
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: var(--order-yellow);
            border-radius: 10px;
        }

        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #ffe46b;
        }

        .orders-livewire .pagination {
            --bs-pagination-color: var(--order-yellow);
            --bs-pagination-bg: #171717;
            --bs-pagination-border-color: #333;
            --bs-pagination-hover-color: #111;
            --bs-pagination-hover-bg: var(--order-yellow);
            --bs-pagination-hover-border-color: var(--order-yellow);
            --bs-pagination-active-color: #111;
            --bs-pagination-active-bg: var(--order-yellow);
            --bs-pagination-active-border-color: var(--order-yellow);
            --bs-pagination-disabled-color: #666;
            --bs-pagination-disabled-bg: #111;
            --bs-pagination-disabled-border-color: #2a2a2a;
        }

        @media (max-width: 767.98px) {
            .orders-page {
                padding-top: .8rem;
            }

            .orders-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .orders-hero-icon {
                display: none;
            }

            .order-warning {
                padding: .85rem;
                font-size: .85rem;
            }

            .orders-livewire {
                padding: .65rem;
                border-radius: 18px;
            }
        }
    </style>

    <div class="orders-page">
        <div class="container">
            <section class="orders-hero">
                <div class="orders-hero-content">
                    <div>
                        <div class="orders-kicker">
                            <i class="bi bi-receipt-cutoff"></i>
                            ORDER TRACKING
                        </div>

                        <h1 class="orders-title">
                            คำสั่งซื้อของคุณ
                        </h1>

                        <p class="orders-subtitle">
                            ตรวจสอบรายละเอียดและติดตามสถานะคำสั่งซื้อของคุณได้จากหน้านี้
                        </p>
                    </div>

                    <div class="orders-hero-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </section>

            <div class="order-warning" role="alert">

                <div>
                    <strong>กรุณาตรวจสอบก่อนสั่งซื้อ</strong>
                    <div class="mt-1">
                        เมื่อยืนยันคำสั่งซื้อแล้วจะไม่สามารถยกเลิกด้วยตนเองได้
                        หากต้องการยกเลิก กรุณาติดต่อ
                        <a href="tel:{{ $shopPhoneLink }}">{{ $shopPhone }}</a>
                    </div>
                </div>
            </div>

            <section class="orders-livewire">
                @livewire('customer-orders')
            </section>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('order-status-changed', ({ id, status }) => {
            const sound = document.getElementById('orderSound');

            if (sound) {
                sound.play().catch(() => {
                    console.warn('Audio autoplay prevented. User interaction required.');
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'สถานะคำสั่งซื้ออัปเดต',
                    html: `คำสั่งซื้อ <strong>#${id}</strong><br>เปลี่ยนสถานะเป็น <strong>${status}</strong>`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3200,
                    timerProgressBar: true,
                    background: '#171717',
                    color: '#f7f7f7',
                    iconColor: '#ffd43b',
                    customClass: {
                        popup: 'border border-warning'
                    }
                });
            }
        });
    });
</script>
@endpush