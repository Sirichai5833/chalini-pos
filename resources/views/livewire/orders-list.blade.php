<div wire:poll.60s class="container-fluid py-4 online-orders-page">
    <style>
        :root {
            --order-yellow: #ffd43b;
            --order-yellow-dark: #caa400;
            --order-black: #090909;
            --order-surface: #121212;
            --order-card: #181818;
            --order-soft: #222222;
            --order-border: rgba(255, 212, 59, .22);
            --order-text: #f7f7f7;
            --order-muted: #aaa;
            --order-success: #22c55e;
            --order-danger: #ef4444;
            --order-info: #38bdf8;
        }

        .online-orders-page {
            color: var(--order-text);
        }

        .orders-hero {
            padding: 1.25rem 1.4rem;
            margin-bottom: 1rem;
            border: 1px solid var(--order-border);
            border-radius: 18px;
            background: #111;
        }

        .orders-title {
            margin: 0;
            color: var(--order-yellow);
            font-size: 1.25rem;
            font-weight: 950;
        }

        .orders-subtitle {
            margin: .35rem 0 0;
            color: #c9c9c9;
            font-size: .84rem;
        }

        .orders-count {
            border: 1px solid var(--order-border);
            background: rgba(255, 212, 59, .10);
            color: var(--order-yellow);
            font-weight: 850;
        }

        .orders-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.15rem;
            align-items: start;
        }

        .order-card {
            overflow: hidden;
            border: 2px solid rgba(255, 212, 59, .24) !important;
            border-radius: 18px !important;
            background: #151515;
            color: var(--order-text);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .30) !important;
            transition:
                border-color .18s ease,
                transform .18s ease,
                box-shadow .18s ease;
        }

        .order-card:hover {
            border-color: rgba(255, 212, 59, .70) !important;
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(0, 0, 0, .40) !important;
        }

        .order-card-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 212, 59, .18);
            background:
                linear-gradient(
                    135deg,
                    rgba(255, 212, 59, .06),
                    transparent 55%
                ),
                #101010;
        }

        .order-code-wrap {
            min-width: 0;
        }

        .order-code-label {
            margin-bottom: .2rem;
            color: var(--order-muted);
            font-size: .68rem;
            font-weight: 800;
        }

        .order-code {
            margin: 0;
            color: var(--order-yellow);
            font-size: 1.15rem;
            font-weight: 950;
            word-break: break-word;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 170px;
            min-height: 52px;
            padding: .7rem 1rem;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 950;
            letter-spacing: .2px;
            white-space: nowrap;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .22);
        }

        .status-shipping {
            background: rgba(56, 189, 248, .14);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, .35);
        }

        .status-packing {
            background: rgba(255, 212, 59, .12);
            color: var(--order-yellow);
            border: 1px solid rgba(255, 212, 59, .35);
        }

        .status-complete {
            background: rgba(34, 197, 94, .13);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, .35);
        }

        .status-default {
            background: rgba(148, 163, 184, .12);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, .3);
        }

        .order-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: .9rem;
            padding: 1rem;
        }

        .summary-panel,
        .customer-panel {
            min-width: 0;
            border: 1px solid #2d2d2d;
            border-radius: 13px;
            background: #101010;
        }

        .customer-panel {
            padding: .8rem;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .65rem;
            color: #fff;
            font-size: .78rem;
            font-weight: 900;
        }

        .info-list {
            display: grid;
            gap: .5rem;
        }

        .customer-highlight {
            display: grid;
            gap: .65rem;
            margin-bottom: .9rem;
            padding: .85rem;
            border: 1px solid rgba(255, 212, 59, .34);
            border-radius: 12px;
            background: rgba(255, 212, 59, .05);
        }

        .customer-highlight-label {
            color: var(--order-muted);
            font-size: .68rem;
            font-weight: 800;
        }

        .customer-name-big {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 950;
            line-height: 1.15;
            word-break: break-word;
        }

        .customer-room-big {
            color: var(--order-yellow);
            font-size: 1.6rem;
            font-weight: 950;
            line-height: 1;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: .7rem;
            font-size: .74rem;
        }

        .info-label {
            color: var(--order-muted);
            white-space: nowrap;
        }

        .info-value {
            color: #fff;
            font-weight: 750;
            text-align: right;
            word-break: break-word;
        }

        .info-value.payment {
            color: var(--order-yellow);
        }

        /* PRODUCT LIST: visual priority */
        .products-panel {
            min-width: 0;
            padding: .9rem;
            border: 1px solid rgba(255, 212, 59, .42);
            border-radius: 14px;
            background: rgba(255, 212, 59, .045);
        }

        .products-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .7rem;
        }

        .products-title {
            display: flex;
            align-items: center;
            gap: .45rem;
            margin: 0;
            color: var(--order-yellow);
            font-size: .9rem;
            font-weight: 950;
        }

        .products-count {
            padding: .28rem .55rem;
            border-radius: 999px;
            background: rgba(255, 212, 59, .12);
            color: var(--order-yellow);
            font-size: .68rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .product-list {
            overflow: hidden;
            border: 1px solid #343434;
            border-radius: 12px;
            background: #0f0f0f;
        }

        .product-list .list-group-item {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: center !important;
            gap: .75rem !important;
            padding: .72rem .8rem !important;
            border-color: #292929;
            background: #111;
            color: var(--order-text);
        }

        .product-name {
            color: #fff;
            font-size: .88rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .product-meta {
            margin-top: .2rem;
            color: var(--order-muted);
            font-size: .7rem;
        }

        .gift-text {
            margin-top: .18rem;
            color: #86efac;
            font-size: .7rem;
        }

        .quantity-badge {
            min-width: 54px;
            padding: .35rem .48rem;
            border: 1px solid #393939;
            border-radius: 9px;
            background: #0c0c0c;
            color: #fff;
            font-size: .74rem;
            font-weight: 900;
            text-align: center;
            white-space: nowrap;
        }

        .line-total {
            min-width: 92px;
            color: var(--order-yellow);
            font-size: .82rem;
            font-weight: 950;
            text-align: right;
            white-space: nowrap;
        }

        .summary-panel {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            padding: .8rem;
        }

        .summary-label {
            color: var(--order-muted);
            font-size: .7rem;
            font-weight: 800;
        }

        .grand-total-amount {
            margin-top: .15rem;
            color: var(--order-yellow);
            font-size: 1.35rem;
            font-weight: 950;
            line-height: 1.1;
        }

        .order-total-breakdown {
            display: grid;
            gap: .45rem;
        }

        .order-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            font-size: .74rem;
        }

        .order-total-row.total-final {
            padding-top: .55rem;
            margin-top: .1rem;
            border-top: 1px solid #303030;
        }

        .slip-preview {
            display: block;
            width: 100%;
            max-height: 120px;
            object-fit: cover;
            border: 1px solid #343434;
            border-radius: 10px;
            background: #0d0d0d;
        }

        .slip-link {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .accept-order-btn {
            min-height: 42px;
            border: 1px solid var(--order-yellow);
            border-radius: 10px;
            background: var(--order-yellow);
            color: #111;
            font-weight: 900;
        }

        .accept-order-btn:hover {
            background: #ffe36a;
            border-color: #ffe36a;
            color: #000;
        }

        .order-alert {
            border-radius: 10px;
            font-size: .78rem;
        }

        .empty-orders {
            padding: 3rem 1rem;
            border: 1px dashed var(--order-border);
            border-radius: 16px;
            background: #111;
            text-align: center;
        }

        .empty-orders-icon {
            width: 68px;
            height: 68px;
            display: grid;
            place-items: center;
            margin: 0 auto .8rem;
            border-radius: 18px;
            background: rgba(255, 212, 59, .08);
            color: var(--order-yellow);
            font-size: 1.8rem;
        }

        @media (max-width: 1200px) {
            .orders-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .order-card-header {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .status-badge {
                width: 100%;
                min-width: 0;
                min-height: 48px;
                font-size: .98rem;
            }

            .order-body {
                grid-template-columns: 1fr;
            }

            .summary-panel {
                display: flex;
            }
        }

        @media (max-width: 575.98px) {
            .orders-hero {
                padding: 1rem;
            }

            .orders-hero .d-flex {
                align-items: flex-start !important;
                flex-direction: column;
            }

            .product-list .list-group-item {
                grid-template-columns: minmax(0, 1fr) auto !important;
            }

            .line-total {
                grid-column: 1 / -1;
                min-width: 0;
                text-align: left;
            }
        }
    </style>

    <section class="orders-hero">
        <div class="orders-hero-content d-flex align-items-center justify-content-between gap-3">
            <div>
                <h3 class="orders-title">
                    <i class="bi bi-box-seam-fill me-2"></i>
                    รายการคำสั่งซื้อออนไลน์
                </h3>
                <p class="orders-subtitle">
                    ติดตาม ตรวจสอบ และรับออเดอร์จากลูกค้าแบบเรียลไทม์
                </p>
            </div>

            <span class="badge rounded-pill px-3 py-2 orders-count">
                {{ $orders->count() }} ออเดอร์
            </span>
        </div>
    </section>

    @if ($orders->count())
        <div class="orders-grid">
            @foreach ($orders as $order)
                @php
                    $statusClass = match ($order->status) {
                        'กำลังจัดส่ง' => 'status-shipping',
                        'กำลังจัดสินค้า' => 'status-packing',
                        'เสร็จสิ้น' => 'status-complete',
                        default => 'status-default',
                    };

                    $shippingFee = max(0, (float) ($order->shipping_fee ?? 0));

                    $itemsSubtotal = $order->orderItems->sum(function ($item) {
                        return (float) $item->price * (int) $item->quantity;
                    });

                    $orderGrandTotal = (float) $order->total_amount;

                    if ($orderGrandTotal <= 0) {
                        $orderGrandTotal = $itemsSubtotal + $shippingFee;
                    }

                    $totalQty = $order->orderItems->sum(
                        fn ($item) => (int) $item->quantity
                    );
                @endphp

                <article class="card order-card">
                    {{-- Priority 1: Order code + status --}}
                    <div class="order-card-header d-flex justify-content-between align-items-center gap-3">
                        <div class="order-code-wrap">
                            <div class="order-code-label">เลขคำสั่งซื้อ</div>
                            <h5 class="order-code">
                                <i class="bi bi-receipt-cutoff me-1"></i>
                                #{{ $order->order_code }}
                            </h5>
                        </div>

                        <span class="status-badge {{ $statusClass }}">
                            {{ $order->status }}
                        </span>
                    </div>

                    <div class="order-body">
                        {{-- Priority 2: Customer/contact --}}
                        <section class="customer-panel">
                            <div class="panel-title">
                                <i class="bi bi-person-fill text-warning"></i>
                                ข้อมูลลูกค้า
                            </div>

                            <div class="customer-highlight">
                                <div>
                                    <div class="customer-highlight-label">ชื่อลูกค้า</div>
                                    <div class="customer-name-big">
                                        {{ $order->user->name ?? 'ไม่ทราบชื่อ' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="customer-highlight-label">เลขห้อง</div>
                                    <div class="customer-room-big">
                                        {{ $order->user->room_number ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="info-list">
                                <div class="info-row">
                                    <span class="info-label">โทร</span>
                                    <span class="info-value">
                                        {{ $order->tracking_number ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">เวลาสั่ง</span>
                                    <span class="info-value">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">ชำระเงิน</span>
                                    <span class="info-value payment">
                                        {{ $order->payment_method }}
                                    </span>
                                </div>
                            </div>

                            @if ($order->slip_path)
                                <div class="mt-3">
                                    <div class="panel-title mb-2">
                                        <i class="bi bi-image-fill text-warning"></i>
                                        สลิป
                                    </div>

                                    <a
                                        href="{{ asset('storage/' . $order->slip_path) }}"
                                        target="_blank"
                                        class="slip-link"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $order->slip_path) }}"
                                            alt="สลิปคำสั่งซื้อ {{ $order->order_code }}"
                                            class="slip-preview"
                                            loading="lazy"
                                        >
                                    </a>
                                </div>
                            @endif
                        </section>

                        {{-- Priority 3: Products = strongest visual section --}}
                        <section class="products-panel">
                            <div class="products-heading">
                                <h4 class="products-title">
                                    <i class="bi bi-cart-check-fill"></i>
                                    รายการสินค้า
                                </h4>

                                <span class="products-count">
                                    {{ number_format($totalQty) }} ชิ้น
                                </span>
                            </div>

                            @if ($order->orderItems->count())
                                <ul class="list-group list-group-flush product-list">
                                    @foreach ($order->orderItems as $item)
                                        <li class="list-group-item">
                                            <div>
                                                <div class="product-name">
                                                    {{ $item->product->name ?? '[สินค้าโดนลบ]' }}
                                                </div>

                                                @if (!empty($item->product->description))
                                                    <div class="gift-text">
                                                        <i class="bi bi-gift-fill me-1"></i>
                                                        ของแถม: {{ $item->product->description }}
                                                    </div>
                                                @endif

                                                <div class="product-meta">
                                                    {{ $item->productUnit->unit_name ?? 'หน่วย' }}
                                                    · ฿{{ number_format((float) $item->price, 2) }}/หน่วย
                                                </div>
                                            </div>

                                            <span class="quantity-badge">
                                                × {{ number_format((int) $item->quantity) }}
                                            </span>

                                            <span class="line-total">
                                                ฿{{ number_format(
                                                    (float) $item->price * (int) $item->quantity,
                                                    2
                                                ) }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-muted small">
                                    ไม่มีรายการสินค้าในคำสั่งซื้อนี้
                                </div>
                            @endif
                        </section>

                        {{-- Priority 4: Totals + action --}}
                        <aside class="summary-panel">
                            <div>
                                <div class="panel-title">
                                    <i class="bi bi-cash-stack text-warning"></i>
                                    ยอดคำสั่งซื้อ
                                </div>

                                <div class="order-total-breakdown">
                                    <div class="order-total-row">
                                        <span class="summary-label">สินค้า</span>
                                        <strong>
                                            ฿{{ number_format($itemsSubtotal, 2) }}
                                        </strong>
                                    </div>

                                    <div class="order-total-row">
                                        <span class="summary-label">ค่าจัดส่ง</span>
                                        <strong>
                                            @if ($shippingFee > 0)
                                                ฿{{ number_format($shippingFee, 2) }}
                                            @else
                                                ฟรี
                                            @endif
                                        </strong>
                                    </div>

                                    <div class="order-total-row total-final">
                                        <span class="summary-label">รวมทั้งสิ้น</span>
                                        <span class="grand-total-amount">
                                            ฿{{ number_format($orderGrandTotal, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                @if (!$order->assigned_to)
                                    <form
                                        action="{{ route('orders.accept', $order->id) }}"
                                        method="POST"
                                        class="accept-order-form"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn accept-order-btn w-100"
                                        >
                                            <i class="bi bi-person-check-fill me-1"></i>
                                            รับออเดอร์นี้
                                        </button>
                                    </form>
                                @elseif ($order->assigned_to === auth()->id())
                                    <div class="alert alert-success order-alert text-center py-2 mb-0">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        คุณรับออเดอร์นี้แล้ว
                                    </div>
                                @else
                                    <div class="alert alert-secondary order-alert text-center py-2 mb-0">
                                        <i class="bi bi-person-fill-lock me-1"></i>
                                        มีพนักงานคนอื่นรับออเดอร์นี้แล้ว
                                    </div>
                                @endif
                            </div>
                        </aside>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-orders">
            <div class="empty-orders-icon">
                <i class="bi bi-inbox-fill"></i>
            </div>

            <h4 class="fw-bold text-white">ไม่มีคำสั่งซื้อในระบบ</h4>
            <p class="text-muted mb-0">
                ขณะนี้ยังไม่มีออเดอร์ใหม่ กรุณารอสักครู่ ระบบจะอัปเดตให้อัตโนมัติ
            </p>
        </div>
    @endif
</div>

<script>
    if (!window.orderAcceptSubmitBound) {
        window.orderAcceptSubmitBound = true;

        document.addEventListener('submit', function (event) {
            const form = event.target.closest('.accept-order-form');

            if (!form) {
                return;
            }

            const button = form.querySelector('button[type="submit"]');

            if (!button || button.disabled) {
                event.preventDefault();
                return;
            }

            button.disabled = true;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                กำลังรับออเดอร์...
            `;
        });
    }

    window.addEventListener('notify', event => {
        const message = event.detail?.message || 'มีการอัปเดตคำสั่งซื้อ';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'แจ้งเตือน',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    });
</script>