@extends('layouts.layout')

@section('content')
    <style>
        :root {
            --count-yellow: #ffd43b;
            --count-yellow-soft: #ffe978;
            --count-black: #080808;
            --count-panel: #151515;
            --count-border: rgba(255, 212, 59, .22);
            --count-text: #f7f7f7;
            --count-muted: #a8a8a8;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--count-black);
        }

        .stock-count-page {
            max-width: 1500px;
            color: var(--count-text);
        }

        .stock-count-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 1.25rem;
            padding: 1.5rem;
            border: 1px solid var(--count-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 92% 12%, rgba(255, 212, 59, .22), transparent 26%),
                linear-gradient(135deg, #0b0b0b 0%, #181818 68%, #2b2300 100%);
            box-shadow: 0 20px 48px rgba(0,0,0,.38);
        }

        .stock-count-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stock-count-icon {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            flex: none;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--count-yellow), var(--count-yellow-soft));
            color: #111;
            font-size: 1.45rem;
            box-shadow: 0 12px 28px rgba(255, 212, 59, .2);
        }

        .stock-count-kicker {
            color: var(--count-yellow);
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .16em;
        }

        .stock-count-title {
            margin: .1rem 0 0;
            color: #fff;
            font-size: clamp(1.45rem, 2.8vw, 2rem);
            font-weight: 950;
        }

        .stock-count-subtitle {
            margin: .35rem 0 0;
            color: #c7c7c7;
            font-size: .9rem;
        }

        .count-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }

        .count-meta-card {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .6rem .8rem;
            border: 1px solid rgba(255, 212, 59, .25);
            border-radius: 999px;
            background: rgba(255, 212, 59, .08);
            color: var(--count-yellow);
            font-size: .75rem;
            font-weight: 850;
        }

        .count-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .count-search {
            max-width: 430px;
            flex: 1;
        }

        .count-search .input-group-text,
        .count-search .form-control {
            min-height: 46px;
            border: 1px solid #3b3b3b;
            background: #0d0d0d;
            color: #fff;
            box-shadow: none;
        }

        .count-search .input-group-text {
            border-right: 0;
            color: var(--count-yellow);
        }

        .count-search .form-control {
            border-left: 0;
        }

        .count-search .form-control:focus {
            border-color: var(--count-yellow);
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
        }

        .btn-count-report {
            min-height: 44px;
            border: 1px solid rgba(255, 212, 59, .35);
            border-radius: 12px;
            background: rgba(255, 212, 59, .08);
            color: var(--count-yellow);
            font-weight: 850;
        }

        .count-card {
            overflow: hidden;
            border: 1px solid #2e2e2e;
            border-radius: 22px;
            background: linear-gradient(180deg, #181818, #101010);
            box-shadow: 0 18px 44px rgba(0,0,0,.32);
        }

        .stock-table-container {
            max-height: 70vh;
            overflow-y: auto;
        }

        .count-table {
            --bs-table-bg: transparent;
            --bs-table-color: #eee;
            --bs-table-border-color: #303030;
            --bs-table-hover-bg: rgba(255, 212, 59, .045);
            --bs-table-hover-color: #fff;
        }

        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
            padding: .9rem .85rem;
            border-bottom: 1px solid #343434;
            background: #0c0c0c !important;
            color: var(--count-yellow) !important;
            font-size: .72rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .count-table td {
            padding: .9rem .85rem;
            border-color: #303030;
            color: #ececec;
        }

        .product-name {
            color: #fff !important;
            font-weight: 900;
        }

        .product-barcode {
            color: var(--count-muted) !important;
        }

        .unit-badge {
            display: inline-flex;
            align-items: center;
            padding: .42rem .68rem;
            border: 1px solid #454545;
            border-radius: 999px;
            background: #202020;
            color: #ddd;
            font-size: .72rem;
            font-weight: 850;
        }

        .system-cell {
            background: rgba(255,255,255,.025) !important;
        }

        .system-qty {
            color: #ddd !important;
            font-size: 1rem !important;
            font-weight: 900;
        }

        .actual-cell {
            background: rgba(255, 212, 59, .04) !important;
        }

        .actual-input {
            width: 110px;
            min-height: 44px;
            border: 1px solid #4a4a4a;
            border-radius: 11px;
            background: #0d0d0d;
            color: var(--count-yellow);
            box-shadow: none;
        }

        .actual-input:focus {
            border-color: var(--count-yellow);
            background: #0b0b0b;
            color: var(--count-yellow);
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
        }

        .diff-display {
            min-width: 56px;
            padding: .45rem .65rem;
            border-radius: 999px;
            font-weight: 900;
        }

        .count-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid #303030;
            background: #111;
        }

        .count-footer-note {
            color: var(--count-muted);
            font-size: .78rem;
        }

        .btn-count-back,
        .btn-count-save {
            min-height: 44px;
            border-radius: 12px;
            font-weight: 900;
        }

        .btn-count-back {
            border: 1px solid #444;
            background: #1b1b1b;
            color: #ddd;
        }

        .btn-count-save {
            border: 1px solid var(--count-yellow);
            background: linear-gradient(135deg, var(--count-yellow), var(--count-yellow-soft));
            color: #111;
        }

        @media (max-width: 767.98px) {
            .stock-count-page {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            }

            .stock-count-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .stock-count-hero-content,
            .count-toolbar,
            .count-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .count-search {
                max-width: none;
            }

            .count-footer .btn {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid py-4 stock-count-page">
        <section class="stock-count-hero">
            <div class="stock-count-hero-content">
                <div class="stock-count-icon">
                    <i class="bi bi-clipboard-check"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="stock-count-kicker">STOCK COUNT</div>
                    <h2 class="stock-count-title">ตรวจนับสต็อกสินค้า</h2>
                    <p class="stock-count-subtitle">
                        เปรียบเทียบจำนวนในระบบกับจำนวนที่ตรวจนับจริง
                    </p>
                </div>

                <div class="count-meta">
                    <div class="count-meta-card">
                        <i class="bi bi-calendar-event"></i>
                        {{ now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </section>

        <div class="count-toolbar">
            <div class="count-search">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="ค้นหาชื่อสินค้า หรือบาร์โค้ด"
                        autocomplete="off"
                    >
                </div>
            </div>

            <a
                href="{{ route('staff.stock.check.report') }}"
                class="btn btn-count-report"
            >
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                ดูรายงานสรุป
            </a>
        </div>

        <form action="{{ route('staff.stock.check.store') }}" method="POST">
            @csrf
            <div class="count-card">

                <div class="card-body p-0 stock-table-container">
                    <table class="table count-table table-hover table-nowrap align-middle mb-0">
                        <thead class="sticky-header">
                            <tr>
                                <th class="ps-4 py-3" style="width: 40%;">สินค้า</th>
                                <th class="text-center py-3">หน่วย</th>
                                <th class="text-center py-3 bg-light">คงเหลือ (ระบบ)</th>
                                <th class="text-center py-3 text-primary bg-primary bg-opacity-10" style="width: 150px;">
                                    นับจริง</th>
                                <th class="text-center py-3">ส่วนต่าง</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @foreach ($product->stocks as $stock)
                                    <tr class="stock-row">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark product-name">{{ $product->name }}</span>
                                                <small class="product-barcode font-monospace">Barcode:
                                                    {{ $stock->unit->barcode ?? '-' }}</small>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <span class="unit-badge">
                                                {{ $stock->unit->unit_name }}
                                            </span>
                                        </td>

                                        <td class="text-center system-cell">
                                            <span class="fw-bold text-secondary system-qty font-monospace fs-5">
                                                {{ $stock->warehouse_stock + $stock->store_stock }}
                                            </span>
                                            <input type="hidden" name="items[{{ $stock->id }}][system_qty]"
                                                value="{{ $stock->warehouse_stock + $stock->store_stock }}">
                                        </td>

                                        <td class="text-center actual-cell">
                                            <input type="number" name="items[{{ $stock->id }}][actual_qty]"
                                                class="form-control text-center fw-bold actual-input mx-auto"
                                                style="width: 100px;"
                                                value="{{ $stock->warehouse_stock + $stock->store_stock }}" min="0"
                                                oninput="calculateDiff(this)">
                                        </td>

                                        <td class="text-center">
                                            <span class="diff-display badge bg-light text-secondary fs-6 border"
                                                style="min-width: 50px;">
                                                0
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="count-footer">
                    <div class="count-footer-note">
                        <i class="bi bi-info-circle me-1"></i>
                        กรุณาตรวจสอบความถูกต้องก่อนบันทึก
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            onclick="window.history.back()"
                            class="btn btn-count-back px-4"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            ยกเลิก
                        </button>

                        <button
                            type="submit"
                            class="btn btn-count-save px-4"
                        >
                            <i class="bi bi-check2-circle me-2"></i>
                            บันทึกผลการตรวจนับ
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function calculateDiff(input) {
            const row = input.closest('.stock-row');
            const systemQtyText = row
                .querySelector('.system-qty')
                .innerText
                .replace(/,/g, '');

            const systemQty = Number.parseFloat(systemQtyText) || 0;
            const actualQty = Number.parseFloat(input.value) || 0;
            const diff = actualQty - systemQty;
            const diffDisplay = row.querySelector('.diff-display');

            diffDisplay.innerText = diff > 0 ? `+${diff}` : diff;
            diffDisplay.className = 'diff-display badge border';

            if (diff === 0) {
                diffDisplay.classList.add(
                    'bg-secondary',
                    'bg-opacity-10',
                    'text-secondary'
                );
            } else if (diff > 0) {
                diffDisplay.classList.add(
                    'bg-success',
                    'bg-opacity-25',
                    'text-success',
                    'border-success'
                );
            } else {
                diffDisplay.classList.add(
                    'bg-danger',
                    'bg-opacity-25',
                    'text-danger',
                    'border-danger'
                );
            }
        }

        document
            .getElementById('searchInput')
            .addEventListener('input', function () {
                const filter = this.value.trim().toUpperCase();
                const rows = document.querySelectorAll('.stock-row');

                rows.forEach(row => {
                    const productName = row
                        .querySelector('.product-name')
                        ?.innerText
                        .toUpperCase() ?? '';

                    const barcode = row
                        .querySelector('.product-barcode')
                        ?.innerText
                        .toUpperCase() ?? '';

                    row.style.display =
                        productName.includes(filter) ||
                        barcode.includes(filter)
                            ? ''
                            : 'none';
                });
            });

        document
            .querySelector('form')
            .addEventListener('submit', function () {
                const submitButton = this.querySelector(
                    'button[type="submit"]'
                );

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        '<i class="bi bi-hourglass-split me-2"></i>กำลังบันทึก...';
                }
            });
    </script>
@endsection