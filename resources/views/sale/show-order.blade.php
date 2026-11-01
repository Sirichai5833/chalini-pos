@extends('layouts.layout')

@section('content')

    <style>
        :root {
            --yellow: #ffd43b;
            --yellow-dark: #eab900;
            --black: #090909;
            --surface: #151515;
            --card: #1c1c1c;
            --border: #333;
            --text: #f5f5f5;
            --muted: #999;
            --green: #22c55e;
            --red: #ef4444;
            --blue: #38bdf8;
        }

        body {
            background: var(--black);
            color: var(--text);
        }

        /* ===== Layout ===== */

        .orders-page {
            max-width: 1250px;
            margin: auto;
        }

        .orders-header {
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 212, 59, .25);
            border-radius: 18px;
            background: linear-gradient(120deg, #171717, #292200);
        }

        .orders-header h3 {
            color: var(--yellow);
            font-weight: 900;
            margin: 0;
        }

        .orders-header p {
            color: #bbb;
            margin: 8px 0 0;
        }

        /* ===== Filters ===== */

        .filter-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
        }

        .filter-btn {
            padding: 9px 16px;
            border: 1px solid #444;
            border-radius: 30px;
            background: #202020;
            color: #ddd;
            font-weight: 750;
            transition: .2s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--yellow);
            color: #111;
            border-color: var(--yellow);
        }

        .filter-btn[data-status="late"].active {
            background: var(--red);
            border-color: var(--red);
            color: white;
        }

        /* ===== Order grid ===== */

        .orders-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            align-items: start;
        }

        .order-card {
            overflow: hidden;
            border: 1px solid #333;
            border-radius: 17px;
            background: var(--card);
            color: var(--text);
            transition: .2s;
        }

        .order-card:hover {
            border-color: rgba(255, 212, 59, .6);
            transform: translateY(-2px);
        }

        .order-card.is-late {
            border-color: rgba(239, 68, 68, .7);
        }

        /* ===== Card header ===== */

        .order-header {
            padding: 17px;
            border-bottom: 1px solid #383838;
            background: #111;
        }

        .order-header-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }

        .order-code {
            color: var(--yellow);
            font-size: 1.25rem;
            font-weight: 950;
        }

        .order-date {
            color: var(--muted);
            font-size: .8rem;
            text-align: right;
        }

        .customer-info {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            margin-top: 15px;
            padding-top: 13px;
            border-top: 1px dashed #444;
        }

        .info-label {
            display: block;
            color: var(--muted);
            font-size: .75rem;
            margin-bottom: 3px;
        }

        .customer-name {
            font-size: 1.2rem;
            font-weight: 900;
            color: white;
            overflow-wrap: anywhere;
        }

        .room-number {
            color: var(--yellow);
            font-size: 1.8rem;
            font-weight: 950;
            text-align: right;
        }

        /* ===== Card body ===== */

        .order-body {
            padding: 15px;
        }

        .late-alert {
            padding: 10px 12px;
            margin-bottom: 13px;
            border: 1px solid rgba(239, 68, 68, .4);
            border-radius: 10px;
            background: rgba(239, 68, 68, .1);
            color: #ffaaaa;
            font-size: .85rem;
            font-weight: 800;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
            margin-bottom: 15px;
        }

        .detail-box {
            padding: 10px;
            border: 1px solid #333;
            border-radius: 10px;
            background: #121212;
            min-width: 0;
        }

        .detail-value {
            color: white;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        /* ===== Products ===== */

        .products-box {
            overflow: hidden;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 212, 59, .3);
            border-radius: 12px;
            background: #131313;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 11px 13px;
            border-bottom: 1px solid #333;
            background: rgba(255, 212, 59, .06);
        }

        .products-header strong {
            color: var(--yellow);
            font-size: 1rem;
        }

        .products-count {
            color: var(--yellow);
            font-size: .85rem;
            font-weight: 800;
        }

        .product-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: 10px;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #292929;
        }

        .product-row:last-child {
            border-bottom: 0;
        }

        .product-name {
            color: white;
            font-weight: 850;
            overflow-wrap: anywhere;
        }

        .product-meta {
            color: var(--muted);
            font-size: .78rem;
            margin-top: 3px;
        }

        .gift-text {
            color: #86efac;
            font-size: .78rem;
            margin-top: 4px;
        }

        .product-qty {
            padding: 7px 9px;
            border-radius: 8px;
            background: #080808;
            color: white;
            font-weight: 850;
            white-space: nowrap;
        }

        .product-price {
            color: var(--yellow);
            font-weight: 900;
            white-space: nowrap;
            text-align: right;
        }

        /* ===== Total ===== */

        .total-box {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 9px;
            padding: 13px;
            margin-bottom: 15px;
            border: 1px solid #333;
            border-radius: 11px;
            background: #101010;
        }

        .total-label {
            color: var(--muted);
        }

        .total-value {
            color: white;
            font-weight: 800;
            text-align: right;
        }

        .final-label {
            padding-top: 10px;
            border-top: 1px solid #444;
            color: white;
            font-weight: 900;
        }

        .final-value {
            padding-top: 7px;
            border-top: 1px solid #444;
            color: var(--yellow);
            font-size: 1.35rem;
            font-weight: 950;
            text-align: right;
        }

        /* ===== Status ===== */

        .status-heading {
            color: white;
            font-weight: 850;
            font-size: .9rem;
            margin-bottom: 10px;
        }

        .order-progress {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 12px 5px;
            margin-bottom: 14px;
            border: 1px solid #333;
            border-radius: 12px;
            background: #111;
        }

        .order-progress::before {
            content: "";
            position: absolute;
            top: 25px;
            left: 16.66%;
            right: 16.66%;
            height: 3px;
            background: #444;
        }

        .progress-fill {
            position: absolute;
            top: 25px;
            left: 16.66%;
            height: 3px;
            background: var(--yellow);
            transition: width .2s;
        }

        .progress-step {
            z-index: 1;
            text-align: center;
        }

        .progress-dot {
            display: grid;
            place-items: center;
            width: 25px;
            height: 25px;
            margin: 0 auto 7px;
            border: 2px solid #555;
            border-radius: 50%;
            background: #171717;
            color: #999;
            font-size: .7rem;
            font-weight: 900;
        }

        .progress-step.done .progress-dot,
        .progress-step.current .progress-dot {
            background: var(--yellow);
            border-color: var(--yellow);
            color: #111;
        }

        .progress-label {
            color: #888;
            font-size: .7rem;
            font-weight: 750;
        }

        .progress-step.current .progress-label,
        .progress-step.done .progress-label {
            color: var(--yellow);
        }

        .progress-cancelled {
            padding: 12px;
            margin-bottom: 14px;
            border: 1px solid rgba(239, 68, 68, .4);
            border-radius: 10px;
            background: rgba(239, 68, 68, .1);
            color: #ffaaaa;
            text-align: center;
            font-weight: 850;
        }

        /* ===== Action panel ===== */

        .status-panel {
            padding: 13px;
            border: 1px solid #383838;
            border-radius: 12px;
            background: #111;
        }

        .action-note {
            color: var(--muted);
            font-size: .8rem;
            margin-bottom: 10px;
        }

        .status-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
        }

        .action-btn {
            min-height: 46px;
            padding: 9px 12px;
            border-radius: 9px;
            font-weight: 900;
            transition: .2s;
        }

        .action-next {
            background: var(--yellow);
            border: 1px solid var(--yellow);
            color: #111;
        }

        .action-next:hover {
            background: #ffe477;
            color: #111;
        }

        .action-cancel {
            min-width: 95px;
            background: transparent;
            border: 1px solid var(--red);
            color: #ff9999;
        }

        .action-cancel:hover {
            background: var(--red);
            color: white;
        }

        .cancel-reason,
        .upload-proof {
            display: none;
            margin-top: 13px;
        }

        .form-label {
            color: #ddd;
            font-weight: 800;
        }

        .form-control {
            background: #1b1b1b;
            border: 1px solid #444;
            color: white;
            border-radius: 9px;
        }

        .form-control:focus {
            background: #202020;
            color: white;
            border-color: var(--yellow);
            box-shadow: 0 0 0 2px rgba(255, 212, 59, .12);
        }

        .form-control::file-selector-button {
            background: var(--yellow);
            color: #111;
            border: 0;
        }

        .confirm-cancel-btn {
            min-height: 43px;
            font-weight: 850;
        }

        .locked-status {
            color: #aaa;
            font-size: .85rem;
        }

        /* ===== Empty ===== */

        .empty-orders {
            grid-column: 1/-1;
            padding: 55px 20px;
            border: 1px dashed #555;
            border-radius: 16px;
            background: #151515;
            text-align: center;
        }

        .empty-orders i {
            display: block;
            color: var(--yellow);
            font-size: 3rem;
            margin-bottom: 15px;
        }

        /* ===== Responsive ===== */

        @media(max-width: 900px) {
            .orders-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 575px) {
            .orders-header {
                padding: 18px;
            }

            .order-body {
                padding: 11px;
            }

            .product-row {
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 7px;
            }

            .product-price {
                grid-column: 1/-1;
                text-align: left;
            }

            .status-actions {
                grid-template-columns: 1fr;
            }

            .action-cancel {
                width: 100%;
            }

            .order-date {
                font-size: .7rem;
            }
        }
    </style>


    <div class="container py-4 orders-page">

        {{-- ===== Heading ===== --}}

        <section class="orders-header">
            <h3>
                <i class="bi bi-clipboard-check-fill me-2"></i>
                ออเดอร์ที่ฉันรับไว้
            </h3>

            <p>
                จัดการคำสั่งซื้อ ตรวจสอบสินค้า และอัปเดตสถานะการจัดส่ง
            </p>
        </section>


        {{-- ===== Filters ===== --}}

        <div class="filter-panel">

            <button class="filter-btn active" data-status="all" type="button">
                <i class="bi bi-grid-fill me-1"></i>
                ทั้งหมด
            </button>

            <button class="filter-btn" data-status="กำลังจัดสินค้า" type="button">
                <i class="bi bi-box-seam me-1"></i>
                กำลังจัดสินค้า
            </button>

            <button class="filter-btn" data-status="กำลังจัดส่ง" type="button">
                <i class="bi bi-truck me-1"></i>
                กำลังจัดส่ง
            </button>

            <button class="filter-btn" data-status="late" type="button">
                <i class="bi bi-alarm-fill me-1"></i>
                ล่าช้าเกิน 15 นาที
            </button>

        </div>


        {{-- ===== Alerts ===== --}}

        @if (session('success'))
            <div class="alert alert-success rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger rounded-3">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- ===== Orders ===== --}}

        <div class="orders-grid">

            @forelse ($orders as $order)
                @php
                    $isLate =
                        now()->greaterThan($order->updated_at->copy()->addMinutes(15)) &&
                        !in_array($order->status, ['เสร็จสิ้น', 'ยกเลิก'], true);

                    $shippingFee = max(0, (float) ($order->shipping_fee ?? 0));

                    $itemsSubtotal = $order->orderItems->sum(function ($item) {
                        return (float) $item->price * (int) $item->quantity;
                    });

                    $orderGrandTotal = (float) $order->total_amount;

                    if ($orderGrandTotal <= 0) {
                        $orderGrandTotal = $itemsSubtotal + $shippingFee;
                    }

                    $totalQty = $order->orderItems->sum(fn($item) => (int) $item->quantity);

                    $statusFlow = [
                        'รอเจ้าหน้าที่รับคำสั่งซื้อ' => 'กำลังจัดสินค้า',
                        'กำลังจัดสินค้า' => 'กำลังจัดส่ง',
                        'กำลังจัดส่ง' => 'เสร็จสิ้น',
                    ];

                    $nextStatus = $statusFlow[$order->status] ?? null;

                    $isFinalStatus = in_array($order->status, ['เสร็จสิ้น', 'ยกเลิก'], true);

                    $progressSteps = ['กำลังจัดสินค้า', 'กำลังจัดส่ง', 'เสร็จสิ้น'];

                    $progressIndex = match ($order->status) {
                        'กำลังจัดสินค้า' => 0,
                        'กำลังจัดส่ง' => 1,
                        'เสร็จสิ้น' => 2,
                        default => -1,
                    };

                    $progressWidth = match ($progressIndex) {
                        0 => '0%',
                        1 => '33.334%',
                        2 => '66.668%',
                        default => '0%',
                    };
                @endphp


                <article class="order-card {{ $isLate ? 'is-late' : '' }}" data-status="{{ $order->status }}"
                    data-late="{{ $isLate ? '1' : '0' }}">

                    {{-- Header --}}

                    <div class="order-header">

                        <div class="order-header-top">

                            <div>
                                <div class="info-label">หมายเลขคำสั่งซื้อ</div>

                                <div class="order-code">
                                    <i class="bi bi-ticket-perforated-fill me-1"></i>
                                    #{{ $order->order_code }}
                                </div>
                            </div>

                            <div class="order-date">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </div>

                        </div>


                        <div class="customer-info">

                            <div>
                                <span class="info-label">
                                    <i class="bi bi-person-fill me-1"></i>
                                    ชื่อลูกค้า
                                </span>

                                <div class="customer-name">
                                    {{ $order->user->name ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <span class="info-label">ห้อง / ROOM</span>

                                <div class="room-number">
                                    {{ $order->user->room_number ?? '-' }}
                                </div>
                            </div>

                        </div>

                    </div>


                    {{-- Body --}}

                    <div class="order-body">

                        @if ($isLate)
                            <div class="late-alert">
                                <i class="bi bi-alarm-fill me-1"></i>
                                ออเดอร์นี้ล่าช้าเกิน 15 นาที กรุณาดำเนินการ
                            </div>
                        @endif


                        {{-- Customer details --}}

                        <div class="order-details">

                            <div class="detail-box">
                                <span class="info-label">เบอร์โทร / Tracking</span>

                                <div class="detail-value">
                                    {{ $order->tracking_number ?? '-' }}
                                </div>
                            </div>

                            <div class="detail-box">
                                <span class="info-label">วิธีชำระเงิน</span>

                                <div class="detail-value">
                                    {{ $order->payment_method ?? '-' }}
                                </div>
                            </div>

                        </div>


                        {{-- Products --}}

                        <section class="products-box">

                            <div class="products-header">

                                <strong>
                                    <i class="bi bi-cart-check-fill me-1"></i>
                                    รายการสินค้า
                                </strong>

                                <span class="products-count">
                                    {{ number_format($totalQty) }} ชิ้น
                                </span>

                            </div>


                            @foreach ($order->orderItems as $item)
                                <div class="product-row">

                                    <div>

                                        <div class="product-name">
                                            {{ $item->product->name ?? '[ลบแล้ว]' }}
                                        </div>

                                        <div class="product-meta">
                                            {{ $item->productUnit->unit_name ?? 'หน่วย' }}
                                            · ฿{{ number_format($item->price, 2) }}/หน่วย
                                        </div>

                                        @if (!empty($item->product->description))
                                            <div class="gift-text">
                                                <i class="bi bi-gift-fill me-1"></i>
                                                ของแถม: {{ $item->product->description }}
                                            </div>
                                        @endif

                                    </div>

                                    <div class="product-qty">
                                        × {{ number_format((int) $item->quantity) }}
                                    </div>

                                    <div class="product-price">
                                        ฿{{ number_format($item->price * $item->quantity, 2) }}
                                    </div>

                                </div>
                            @endforeach

                        </section>


                        {{-- Total --}}

                        <div class="total-box">

                            <span class="total-label">ยอดรวมสินค้า</span>

                            <span class="total-value">
                                ฿{{ number_format($itemsSubtotal, 2) }}
                            </span>


                            <span class="total-label">ค่าจัดส่ง</span>

                            <span class="total-value">
                                {{ $shippingFee > 0 ? '฿' . number_format($shippingFee, 2) : 'ฟรี' }}
                            </span>


                            <span class="final-label">ยอดสุทธิ</span>

                            <span class="final-value">
                                ฿{{ number_format($orderGrandTotal, 2) }}
                            </span>

                        </div>


                        {{-- Progress --}}

                        <div class="status-heading">
                            <i class="bi bi-diagram-3-fill me-1"></i>
                            ความคืบหน้าของออเดอร์
                        </div>


                        @if ($order->status === 'ยกเลิก')
                            <div class="progress-cancelled">
                                <i class="bi bi-x-circle-fill me-1"></i>
                                ออเดอร์นี้ถูกยกเลิกแล้ว
                            </div>
                        @else
                            <div class="order-progress">

                                <div class="progress-fill" style="width: {{ $progressWidth }}"></div>

                                @foreach ($progressSteps as $index => $step)
                                    <div
                                        class="progress-step
                                    {{ $index < $progressIndex ? 'done' : '' }}
                                    {{ $index === $progressIndex ? 'current' : '' }}">

                                        <div class="progress-dot">

                                            @if ($index < $progressIndex)
                                                <i class="bi bi-check-lg"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif

                                        </div>

                                        <div class="progress-label">
                                            {{ $step }}
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        @endif


                        {{-- Actions --}}

                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                            enctype="multipart/form-data" class="status-form status-panel">

                            @csrf
                            @method('PATCH')


                            @if ($isFinalStatus)
                                <div class="locked-status">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    ออเดอร์นี้ดำเนินการเสร็จแล้ว ไม่สามารถเปลี่ยนสถานะได้
                                </div>
                            @else
                                <input type="hidden" name="status" class="status-input" value="">

                                <div class="action-note">
                                    <i class="bi bi-info-circle me-1"></i>
                                    เลือกการดำเนินการสำหรับออเดอร์นี้
                                </div>


                                <div class="status-actions">

                                    @if ($nextStatus)
                                        <button type="button" class="btn action-btn action-next direct-status-btn"
                                            data-status-value="{{ $nextStatus }}">

                                            <i class="bi bi-arrow-right-circle-fill me-1"></i>

                                            {{ $nextStatus }}

                                        </button>
                                    @endif


                                    <button type="button" class="btn action-btn action-cancel direct-status-btn"
                                        data-status-value="ยกเลิก">

                                        <i class="bi bi-x-circle-fill me-1"></i>
                                        ยกเลิก

                                    </button>

                                </div>


                                {{-- Upload proof --}}

                                <div class="upload-proof">

                                    <label class="form-label">
                                        <i class="bi bi-camera-fill me-1"></i>
                                        รูปหลักฐานการจัดส่ง
                                    </label>

                                    <input type="file" name="proof_image" accept="image/*"
                                        class="form-control proof-input">

                                    <small class="text-secondary">
                                        กรุณาเลือกรูปหลักฐานก่อนปิดออเดอร์
                                    </small>

                                </div>


                                {{-- Cancel reason --}}

                                <div class="cancel-reason">

                                    <label class="form-label">
                                        <i class="bi bi-chat-left-text-fill me-1"></i>
                                        เหตุผลการยกเลิก
                                    </label>

                                    <textarea name="cancel_reason" rows="3" class="form-control cancel-textarea"
                                        placeholder="ระบุเหตุผลการยกเลิก..."></textarea>

                                    <button type="button" class="btn btn-danger w-100 mt-2 confirm-cancel-btn">
                                        <i class="bi bi-x-circle-fill me-1"></i>
                                        ยืนยันยกเลิกออเดอร์
                                    </button>

                                </div>
                            @endif

                        </form>

                    </div>

                </article>

            @empty

                <div class="empty-orders">

                    <i class="bi bi-inbox-fill"></i>

                    <h4 class="text-white fw-bold">
                        ยังไม่มีออเดอร์ที่รับไว้
                    </h4>

                    <p class="text-secondary mb-0">
                        เมื่อมีการรับออเดอร์ รายการจะแสดงที่นี่
                    </p>

                </div>
            @endforelse

        </div>

    </div>


    {{-- ===== JavaScript ===== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ===== Update order status ===== */

            document.querySelectorAll('.status-form').forEach(form => {

                const statusInput = form.querySelector('.status-input');

                const proofWrap = form.querySelector('.upload-proof');
                const proofInput = form.querySelector('.proof-input');

                const cancelWrap = form.querySelector('.cancel-reason');
                const cancelTextarea = form.querySelector('.cancel-textarea');

                const cancelConfirm = form.querySelector('.confirm-cancel-btn');

                let submitting = false;

                function submitForm(status) {

                    if (!statusInput || submitting) return;

                    statusInput.value = status;
                    submitting = true;

                    form.querySelectorAll('button').forEach(button => {
                        button.disabled = true;
                    });

                    form.submit();
                }


                /* Click status buttons */

                form.querySelectorAll('.direct-status-btn').forEach(button => {

                    button.addEventListener('click', function() {

                        const status = this.dataset.statusValue || '';

                        if (!status) return;


                        /* Finish order: require proof image */

                        if (status === 'เสร็จสิ้น') {

                            if (cancelWrap) {
                                cancelWrap.style.display = 'none';
                            }

                            if (proofWrap) {
                                proofWrap.style.display = 'block';
                            }

                            if (proofInput && proofInput.files.length === 0) {

                                proofInput.click();

                                return;
                            }

                            submitForm(status);

                            return;
                        }


                        /* Cancel order: require reason */

                        if (status === 'ยกเลิก') {

                            if (proofWrap) {
                                proofWrap.style.display = 'none';
                            }

                            if (cancelWrap) {
                                cancelWrap.style.display = 'block';
                            }

                            cancelTextarea?.focus();

                            return;
                        }


                        /* Other status */

                        submitForm(status);

                    });

                });


                /* Upload proof */

                proofInput?.addEventListener('change', function() {

                    if (this.files.length > 0) {
                        submitForm('เสร็จสิ้น');
                    }

                });


                /* Confirm cancellation */

                cancelConfirm?.addEventListener('click', function() {

                    const reason = cancelTextarea?.value.trim() || '';

                    if (!reason) {

                        if (typeof Swal !== 'undefined') {

                            Swal.fire({
                                icon: 'warning',
                                title: 'กรุณากรอกเหตุผล',
                                text: 'ระบุเหตุผลก่อนยกเลิกออเดอร์',
                                confirmButtonColor: '#ffd43b'
                            });

                        } else {

                            alert('กรุณากรอกเหตุผลก่อนยกเลิกออเดอร์');

                        }

                        return;
                    }

                    submitForm('ยกเลิก');

                });

            });


            /* ===== Filter orders ===== */

            const filterButtons = document.querySelectorAll('.filter-btn');
            const orderCards = document.querySelectorAll('.order-card');

            filterButtons.forEach(button => {

                button.addEventListener('click', function() {

                    const selectedStatus = this.dataset.status;

                    filterButtons.forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');


                    orderCards.forEach(card => {

                        const cardStatus = card.dataset.status;
                        const isLate = card.dataset.late === '1';

                        let show = false;

                        if (selectedStatus === 'all') {

                            show = true;

                        } else if (selectedStatus === 'late') {

                            show = isLate;

                        } else {

                            show = cardStatus === selectedStatus;

                        }

                        card.style.display = show ? '' : 'none';

                    });

                });

            });

        });
    </script>

@endsection
