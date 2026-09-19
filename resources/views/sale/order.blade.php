@extends('layouts.layout')

@section('content')
    <style>
        :root {
            --seller-yellow: #ffd43b;
            --seller-yellow-dark: #caa400;
            --seller-black: #090909;
            --seller-surface: #121212;
            --seller-card: #181818;
            --seller-border: rgba(255, 212, 59, .22);
            --seller-text: #f7f7f7;
            --seller-muted: #aaa;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--seller-black);
            color: var(--seller-text);
        }

        .seller-orders-page {
            max-width: 1500px;
        }

        .seller-orders-hero {
            position: relative;
            overflow: hidden;
            padding: 1.6rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--seller-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #080808 0%, #171717 58%, #2c2400 100%);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .35);
        }

        .seller-orders-hero::after {
            content: "";
            position: absolute;
            right: -80px;
            bottom: -125px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .10);
            pointer-events: none;
        }

        .seller-orders-hero-content {
            position: relative;
            z-index: 1;
        }

        .seller-orders-title {
            margin: 0;
            color: var(--seller-yellow);
            font-weight: 950;
            letter-spacing: .2px;
        }

        .seller-orders-subtitle {
            margin: .45rem 0 0;
            color: #d1d1d1;
            font-size: .92rem;
        }

        .seller-orders-badge {
            border: 1px solid var(--seller-border);
            background: rgba(255, 212, 59, .12);
            color: var(--seller-yellow);
            font-weight: 850;
        }

        .seller-orders-content {
            padding: 1rem;
            border: 1px solid var(--seller-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at right top, rgba(255, 212, 59, .06), transparent 24%),
                linear-gradient(180deg, #171717, #101010);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .30);
        }

        @media (max-width: 576px) {
            .seller-orders-page {
                padding-top: 1rem !important;
            }

            .seller-orders-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .seller-orders-hero .d-flex {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .seller-orders-content {
                padding: .75rem;
                border-radius: 19px;
            }
        }
    </style>

    <div class="container-fluid py-4 seller-orders-page">
        <section class="seller-orders-hero">
            <div class="seller-orders-hero-content d-flex align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="seller-orders-title">
                        <i class="bi bi-clipboard2-check-fill me-2"></i>
                        รายการคำสั่งซื้อทั้งหมด
                    </h2>

                    <p class="seller-orders-subtitle">
                        หน้าจัดการออเดอร์สำหรับผู้ขาย ตรวจสอบสถานะและรับคำสั่งซื้อได้จากที่นี่
                    </p>
                </div>

                <span class="badge rounded-pill px-3 py-2 seller-orders-badge">
                    <i class="bi bi-shop me-1"></i>
                    สำหรับคนขาย
                </span>
            </div>
        </section>

        <section class="seller-orders-content">
            <livewire:orders-list />
        </section>
    </div>
@endsection