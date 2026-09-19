@extends('layouts.layout')

@section('content')
    @php
        $isCancelled = ($sale->status ?? 'completed') === 'cancelled';
    @endphp
    <div class="container py-4 sale-detail-page">
        <section class="sale-detail-hero">
            <div class="sale-detail-hero-content">
                <div class="sale-detail-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="sale-detail-kicker">SALE DETAIL</div>
                    <h2 class="sale-detail-title">
                        รายละเอียดการขาย #{{ $sale->id }}
                    </h2>
                    <div class="sale-detail-subtitle">
                        ตรวจสอบข้อมูลบิล รายการสินค้า และสถานะการขาย
                    </div>
                </div>

                @if ($isCancelled)
                    <span class="status-pill status-cancelled">
                        <i class="bi bi-x-circle-fill"></i>
                        ยกเลิกแล้ว
                    </span>
                @else
                    <span class="status-pill status-completed">
                        <i class="bi bi-check-circle-fill"></i>
                        สำเร็จ
                    </span>
                @endif
            </div>
        </section>

        @if ($isCancelled)
            <div class="cancelled-alert">
                <div class="cancel-reason-line">
                    <strong>เหตุผล:</strong>
                    {{ $sale->cancel_reason ?? '-' }}
                </div>

                <div class="cancel-date-line">
                    <strong>ยกเลิกเมื่อ:</strong>
                    {{ $sale->cancelled_at
                        ? \Carbon\Carbon::parse($sale->cancelled_at)->format('d/m/Y H:i')
                        : '-' }}
                </div>
            </div>
        @endif

        <div class="sale-summary-grid">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div>
                    <div class="summary-label">วันที่และเวลา</div>
                    <div class="summary-value">
                        {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <div class="summary-label">ผู้ขาย</div>
                    <div class="summary-value">
                        {{ $sale->staff->name ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <div class="summary-label">ประเภทการขาย</div>
                    <div class="summary-value">
                        {{ ucfirst($sale->sale_type) }}
                    </div>
                </div>
            </div>

            <div class="summary-card summary-total-card">
                <div class="summary-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="summary-label">ยอดรวมสุทธิ</div>
                    <div class="summary-value summary-total {{ $isCancelled ? 'is-cancelled' : '' }}">
                        {{ number_format($sale->total_price, 2) }} ฿
                    </div>
                </div>
            </div>
        </div>

        <div class="section-heading">
            <div>
                <div class="section-kicker">ITEMS</div>
                <h4 class="section-title">รายการสินค้า</h4>
            </div>
            <span class="item-count">
                {{ number_format($sale->items->sum('quantity')) }} รายการ
            </span>
        </div>

        <div class="table-responsive sale-table-wrap">
            <table class="table sale-detail-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th>หน่วย</th>
                        <th class="text-end">จำนวน</th>
                        <th class="text-end">ราคาต่อหน่วย</th>
                        <th class="text-end">รวม</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sale->items as $item)
                        <tr>
                            <td>
                                {{ optional(optional($item->unit)->product)->name ?? optional($item->product)->name ?? 'ไม่พบสินค้า' }}
                            </td>

                            <td>
                                {{ optional($item->unit)->unit_name ?? '-' }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item->quantity) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($item->price, 2) }}
                            </td>

                            <td class="text-end fw-bold">
                                {{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                ไม่พบรายการสินค้า
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="action-bar">
            <a href="{{ url()->previous() }}" class="btn btn-action-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                กลับ
            </a>

            @unless ($isCancelled)
                <button
                    type="button"
                    class="btn btn-action-print"
                    onclick="printReceiptHistory()"
                >
                    <i class="bi bi-receipt me-1"></i>
                    ปริ้นใบเสร็จย้อนหลัง
                </button>
            @endunless

            @auth
                @if ((bool) auth()->user()->is_admin && !$isCancelled)
                    <button
                        type="button"
                        class="btn btn-action-danger"
                        onclick="confirmDelete({{ $sale->id }})"
                    >
                        <i class="bi bi-trash3-fill me-1"></i>
                        ยกเลิกรายการขาย
                    </button>

                    <form
                        id="delete-form-{{ $sale->id }}"
                        action="{{ route('staff.sales.destroy', $sale->id) }}"
                        method="POST"
                        class="d-none"
                    >
                        @csrf
                        @method('DELETE')

                        <input
                            type="hidden"
                            name="cancel_reason"
                            id="cancel-reason-{{ $sale->id }}"
                        >
                    </form>
                @endif
            @endauth
        </div>
    </div>


    <style>
        :root {
            --detail-yellow: #ffd43b;
            --detail-yellow-soft: #ffe978;
            --detail-black: #090909;
            --detail-panel: #151515;
            --detail-panel-soft: #1d1d1d;
            --detail-border: rgba(255, 212, 59, .22);
            --detail-text: #f7f7f7;
            --detail-muted: #a8a8a8;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 30%),
                var(--detail-black);
        }

        .sale-detail-page {
            max-width: 1300px;
            color: var(--detail-text);
        }

        .sale-detail-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 1.25rem;
            padding: 1.5rem;
            border: 1px solid var(--detail-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 24%),
                linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
            box-shadow: 0 20px 48px rgba(0,0,0,.38);
        }

        .sale-detail-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -150px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .08);
        }

        .sale-detail-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sale-detail-icon {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            flex: none;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--detail-yellow), var(--detail-yellow-soft));
            color: #111;
            font-size: 1.5rem;
            box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
        }

        .sale-detail-kicker,
        .section-kicker {
            color: var(--detail-yellow);
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .16em;
        }

        .sale-detail-title {
            margin: .1rem 0 0;
            color: #fff;
            font-size: clamp(1.45rem, 2.8vw, 2rem);
            font-weight: 950;
        }

        .sale-detail-subtitle {
            margin-top: .35rem;
            color: #c7c7c7;
            font-size: .9rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            flex: none;
            padding: .6rem .9rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 900;
        }

        .status-completed {
            border: 1px solid rgba(34,197,94,.38);
            background: rgba(34,197,94,.12);
            color: #86efac;
        }

        .status-cancelled {
            border: 1px solid rgba(239,68,68,.4);
            background: rgba(239,68,68,.12);
            color: #fca5a5;
        }

        .cancelled-alert {
            margin-bottom: 1.25rem;
            padding: 1rem 1.1rem;
            border: 1px solid rgba(239,68,68,.35);
            border-left: 4px solid #ef4444;
            border-radius: 16px;
            background: rgba(239,68,68,.08);
            color: #fecaca;
        }

        .cancelled-alert .cancel-reason-line {
            margin-bottom: .45rem;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 950;
            line-height: 1.45;
            word-break: break-word;
        }

        .cancelled-alert .cancel-reason-line strong {
            color: var(--detail-yellow);
            font-size: 1rem;
            font-weight: 900;
        }

        .cancelled-alert .cancel-date-line {
            color: #d9d9d9;
            font-size: .82rem;
            font-weight: 700;
        }

        .sale-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.4rem;
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-height: 104px;
            padding: 1rem;
            border: 1px solid #2e2e2e;
            border-radius: 18px;
            background: linear-gradient(180deg, #191919, #101010);
            box-shadow: 0 12px 28px rgba(0,0,0,.25);
        }

        .summary-card:hover {
            border-color: rgba(255, 212, 59, .38);
            transform: translateY(-1px);
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            flex: none;
            border-radius: 13px;
            background: rgba(255, 212, 59, .09);
            color: var(--detail-yellow);
            font-size: 1.05rem;
        }

        .summary-label {
            color: var(--detail-muted);
            font-size: .74rem;
            font-weight: 800;
        }

        .summary-value {
            margin-top: .2rem;
            color: #fff;
            font-size: .95rem;
            font-weight: 900;
        }

        .summary-total {
            color: #86efac;
            font-size: 1.15rem;
        }

        .summary-total.is-cancelled {
            color: #fca5a5;
            text-decoration: line-through;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .8rem;
        }

        .section-title {
            margin: .1rem 0 0;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 900;
        }

        .item-count {
            padding: .45rem .75rem;
            border: 1px solid var(--detail-border);
            border-radius: 999px;
            background: rgba(255, 212, 59, .08);
            color: var(--detail-yellow);
            font-size: .75rem;
            font-weight: 850;
        }

        .sale-table-wrap {
            overflow: hidden;
            margin-bottom: 1.2rem;
            border: 1px solid #2e2e2e;
            border-radius: 18px;
            background: #101010;
            box-shadow: 0 14px 34px rgba(0,0,0,.28);
        }

        .sale-detail-table {
            --bs-table-bg: transparent;
            --bs-table-color: #eee;
            --bs-table-border-color: #303030;
            --bs-table-hover-bg: rgba(255, 212, 59, .045);
            --bs-table-hover-color: #fff;
        }

        .sale-detail-table thead th {
            padding: .9rem .85rem;
            border-bottom: 1px solid #343434;
            background: #0c0c0c !important;
            color: var(--detail-yellow) !important;
            font-size: .73rem;
            font-weight: 900;
            letter-spacing: .02em;
            white-space: nowrap;
        }

        .sale-detail-table td {
            padding: .85rem;
            border-color: #303030;
            color: #ececec;
        }

        .action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding: 1rem;
            border: 1px solid #2d2d2d;
            border-radius: 18px;
            background: linear-gradient(180deg, #171717, #101010);
        }

        .btn-action-secondary,
        .btn-action-print,
        .btn-action-danger {
            min-height: 44px;
            border-radius: 12px;
            font-weight: 850;
        }

        .btn-action-secondary {
            border: 1px solid #444;
            background: #1b1b1b;
            color: #ddd;
        }

        .btn-action-secondary:hover {
            border-color: var(--detail-yellow);
            color: var(--detail-yellow);
        }

        .btn-action-print {
            border: 1px solid rgba(34,197,94,.45);
            background: rgba(34,197,94,.1);
            color: #86efac;
        }

        .btn-action-print:hover {
            background: #22c55e;
            color: #08140c;
        }

        .btn-action-danger {
            border: 1px solid rgba(239,68,68,.45);
            background: rgba(239,68,68,.1);
            color: #fca5a5;
        }

        .btn-action-danger:hover {
            background: #ef4444;
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .sale-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .sale-detail-page {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            }

            .sale-detail-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .sale-detail-hero-content {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .sale-detail-icon {
                width: 52px;
                height: 52px;
            }

            .status-pill {
                width: 100%;
                justify-content: center;
            }

            .sale-summary-grid {
                grid-template-columns: 1fr;
            }

            .action-bar {
                display: grid;
                grid-template-columns: 1fr;
            }

            .action-bar .btn {
                width: 100%;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const saleData = {{ Illuminate\Support\Js::from([
            'id' => $sale->id,
            'date' => \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i'),
            'staff' => $sale->staff->name ?? 'N/A',
            'saleType' => $sale->sale_type,
            'total' => $sale->total_price,
            'shopName' => $shopSettings->shop_name ?? 'ชาลินี',
            'items' => $sale->items->map(function ($item) {
                return [
                    'name' => optional(optional($item->unit)->product)->name
                    ?? optional($item->product)->name
                    ?? 'ไม่พบสินค้า',
                    'unit' => optional($item->unit)->unit_name ?? '-',
                    'qty' => (float) $item->quantity,
                    'price' => (float) $item->price,
                ];
            })->values(),
        ]) }};

        function confirmDelete(saleId) {
            const form = document.getElementById(`delete-form-${saleId}`);
            const reasonInput = document.getElementById(`cancel-reason-${saleId}`);

            if (!form || !reasonInput) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบฟอร์มยกเลิก',
                    text: 'กรุณาตรวจสอบสิทธิ์ผู้ใช้งานหรือโครงสร้างฟอร์ม'
                });

                return;
            }

            Swal.fire({
                title: 'ยืนยันการยกเลิกรายการขาย',
                text: 'กรุณาระบุเหตุผลที่ต้องการยกเลิก',
                input: 'textarea',
                inputPlaceholder: 'ระบุเหตุผลที่นี่...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ยืนยันการยกเลิก',
                cancelButtonText: 'กลับ',
                allowOutsideClick: false,
                inputValidator: value => {
                    const reason = String(value ?? '').trim();

                    if (!reason) {
                        return 'กรุณากรอกเหตุผลก่อนยืนยัน';
                    }

                    if (reason.length < 3) {
                        return 'เหตุผลต้องมีอย่างน้อย 3 ตัวอักษร';
                    }

                    return null;
                },
                preConfirm: value => String(value ?? '').trim()
            }).then(result => {
                if (!result.isConfirmed) {
                    return;
                }

                reasonInput.value = result.value;

                HTMLFormElement.prototype.submit.call(form);
            });
        }

        function printReceiptHistory() {
            if ({{ $isCancelled ? 'true' : 'false' }}) {
                Swal.fire({
                    icon: 'info',
                    title: 'ไม่สามารถพิมพ์ใบเสร็จได้',
                    text: 'รายการขายนี้ถูกยกเลิกแล้ว'
                });
                return;
            }

            const receiptWindow = window.open(
                '',
                'sale-receipt',
                'width=420,height=700'
            );

            if (!receiptWindow) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถเปิดหน้าต่างพิมพ์ได้',
                    text: 'กรุณาอนุญาต Popup สำหรับเว็บไซต์นี้'
                });

                return;
            }

            const escapeHtml = value => {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            };

            let itemsHtml = '';

            saleData.items.forEach((item, index) => {
                const quantity = Number(item.qty || 0);
                const price = Number(item.price || 0);
                const lineTotal = quantity * price;

                itemsHtml += `
                    <div class="item">
                        <div>
                            ${index + 1}. ${escapeHtml(item.name)}
                        </div>
                        <div class="item-detail">
                            ${quantity} ${escapeHtml(item.unit)}
                            × ${price.toFixed(2)}
                            = ${lineTotal.toFixed(2)} บาท
                        </div>
                    </div>
                `;
            });

            const receiptHtml = `
                <!DOCTYPE html>
                <html lang="th">
                <head>
                    <meta charset="UTF-8">
                    <title>ใบเสร็จย้อนหลัง</title>

                    <style>
                        * {
                            box-sizing: border-box;
                        }

                        body {
                            width: 80mm;
                            margin: 0 auto;
                            padding: 12px;
                            color: #000;
                            background: #fff;
                            font-family: monospace;
                            font-size: 13px;
                            line-height: 1.5;
                        }

                        h2 {
                            margin: 0 0 4px;
                            text-align: center;
                            font-size: 19px;
                        }

                        .center {
                            text-align: center;
                        }

                        .line {
                            margin: 9px 0;
                            border-top: 1px dashed #000;
                        }

                        .meta {
                            margin: 2px 0;
                        }

                        .item {
                            margin: 8px 0;
                        }

                        .item-detail {
                            padding-left: 12px;
                        }

                        .total {
                            display: flex;
                            justify-content: space-between;
                            gap: 10px;
                            font-size: 15px;
                            font-weight: 700;
                        }

                        @media print {
                            body {
                                width: 80mm;
                                padding: 0;
                            }
                        }
                    </style>
                </head>

                <body>
                    <h2>${escapeHtml(saleData.shopName)} POS</h2>
                    <div class="center">*** ใบเสร็จย้อนหลัง ***</div>

                    <div class="line"></div>

                    <div class="meta">
                        เลขที่บิล: ${escapeHtml(saleData.id)}
                    </div>

                    <div class="meta">
                        วันที่: ${escapeHtml(saleData.date)}
                    </div>

                    <div class="meta">
                        พนักงาน: ${escapeHtml(saleData.staff)}
                    </div>

                    <div class="line"></div>

                    ${itemsHtml || '<div class="center">ไม่พบรายการสินค้า</div>'}

                    <div class="line"></div>

                    <div class="total">
                        <span>ยอดรวม</span>
                        <span>${Number(saleData.total || 0).toFixed(2)} บาท</span>
                    </div>

                    <div class="line"></div>

                    <div class="center">
                        ขอบคุณที่ใช้บริการ
                    </div>

                    <script>
                        window.onload = function () {
                            window.print();
                            window.onafterprint = function () {
                                window.close();
                            };
                        };
                    <\/script>
                </body>
                </html>
            `;

            receiptWindow.document.open();
            receiptWindow.document.write(receiptHtml);
            receiptWindow.document.close();
        }
    </script>
@endsection