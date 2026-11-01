@extends('layouts.layout')


@push('styles')
<style>
[data-barcode-page]{--gold:#ffc107;--gold2:#ffe082;--panel:#151515;--muted:#9d9d9d;--border:rgba(255,255,255,.09);color:#f5f5f5}
[data-barcode-page] .barcode-shell{max-width:1550px;margin:auto}
[data-barcode-page] .barcode-hero{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;padding:1.35rem 1.5rem;border:1px solid rgba(255,193,7,.32);border-radius:24px;background:radial-gradient(circle at 88% 12%,rgba(255,193,7,.22),transparent 25%),linear-gradient(135deg,#222,#090909 72%);box-shadow:0 22px 55px rgba(0,0,0,.34)}
[data-barcode-page] .hero-copy{display:flex;align-items:center;gap:1rem}
[data-barcode-page] .hero-icon{width:62px;height:62px;display:grid;place-items:center;flex:none;border-radius:19px;background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111;font-size:1.55rem}
[data-barcode-page] .hero-kicker{color:var(--gold);font-size:.7rem;font-weight:900;letter-spacing:.14em;text-transform:uppercase}
[data-barcode-page] .hero-title{margin:.12rem 0 0;color:#fff;font-size:clamp(1.35rem,2.3vw,2rem);font-weight:900}
[data-barcode-page] .hero-subtitle{margin:.35rem 0 0;color:var(--muted);font-size:.9rem}
[data-barcode-page] .btn-gold,[data-barcode-page] .btn-dark-outline{min-height:44px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;padding:.65rem 1rem;border-radius:12px;font-weight:850;transition:.2s}
[data-barcode-page] .btn-gold{border:1px solid var(--gold);background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111}
[data-barcode-page] .btn-gold:hover{color:#000;transform:translateY(-2px);box-shadow:0 9px 23px rgba(255,193,7,.22)}
[data-barcode-page] .btn-dark-outline{border:1px solid #484848;background:rgba(255,255,255,.025);color:#ddd}
[data-barcode-page] .btn-dark-outline:hover{border-color:var(--gold);background:rgba(255,193,7,.07);color:var(--gold)}
[data-barcode-page] .barcode-panel{overflow:hidden;border:1px solid var(--border);border-radius:22px;background:linear-gradient(145deg,var(--panel),#101010);box-shadow:0 18px 46px rgba(0,0,0,.31)}
[data-barcode-page] .filter-panel{padding:1rem;border-bottom:1px solid var(--border);background:#121212}
[data-barcode-page] .filter-heading{display:flex;align-items:center;gap:.5rem;margin-bottom:.85rem;color:var(--gold);font-size:.86rem;font-weight:850}
[data-barcode-page] .form-control,[data-barcode-page] .form-select{min-height:46px;border-color:#3d3d3d!important;background:#0d0d0d!important;color:#fff!important;box-shadow:none!important}
[data-barcode-page] .form-control::placeholder{color:#6d6d6d}
[data-barcode-page] .form-control:focus,[data-barcode-page] .form-select:focus{border-color:var(--gold)!important;box-shadow:0 0 0 .2rem rgba(255,193,7,.12)!important}
[data-barcode-page] .form-select option{background:#111;color:#fff}
[data-barcode-page] .selected-summary{display:flex;align-items:center;gap:.6rem;padding:.8rem 1rem;border-bottom:1px solid var(--border);background:rgba(255,193,7,.045);color:var(--muted);font-size:.82rem}
[data-barcode-page] .selected-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .62rem;border:1px solid rgba(255,193,7,.28);border-radius:999px;background:rgba(255,193,7,.08);color:var(--gold);font-weight:850}
[data-barcode-page] .barcode-table{margin:0;color:#f5f5f5;border-color:var(--border)}
[data-barcode-page] .barcode-table thead th{padding:.85rem .75rem;border-color:rgba(255,193,7,.24);background:linear-gradient(135deg,#202020,#121212);color:var(--gold);font-size:.72rem;font-weight:850;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap}
[data-barcode-page] .barcode-table tbody td{padding:.85rem .75rem;border-color:var(--border);background:#151515;color:#e9e9e9}
[data-barcode-page] .barcode-table tbody tr:hover td{background:#202020}
[data-barcode-page] .barcode-code{color:var(--gold);font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:.82rem;font-weight:800;letter-spacing:.04em}
[data-barcode-page] .barcode-preview-box{display:inline-flex;align-items:center;justify-content:center;min-width:190px;min-height:72px;padding:.55rem .7rem;border-radius:12px;background:#fff}
[data-barcode-page] .form-check-input{width:1.08rem;height:1.08rem;border-color:#666;background-color:#111}
[data-barcode-page] .form-check-input:checked{border-color:var(--gold);background-color:var(--gold)}
@media(max-width:767.98px){[data-barcode-page] .barcode-hero{align-items:stretch;flex-direction:column;padding:1rem}[data-barcode-page] .hero-actions .btn{width:100%}[data-barcode-page] .barcode-table{min-width:980px}}
</style>
@endpush


@section('content')
<div class="container-fluid py-4" data-barcode-page>
    <div class="barcode-shell">
        <section class="barcode-hero">
            <div class="hero-copy">
                <span class="hero-icon"><i class="bi bi-upc-scan"></i></span>
                <div>
                    <div class="hero-kicker">Barcode Management</div>
                    <h1 class="hero-title">รายการบาร์โค้ดสินค้า</h1>
                    <p class="hero-subtitle">ค้นหา เลือก และพิมพ์บาร์โค้ดสินค้าได้จากหน้าเดียว</p>
                </div>
            </div>
            <div class="hero-actions">
                <button type="button" class="btn btn-gold" onclick="printSelectedBarcodes()">
                    <i class="bi bi-printer-fill"></i> พิมพ์รายการที่เลือก
                </button>
            </div>
        </section>
        <section class="barcode-panel">
            {{-- ค้นหา + หมวดหมู่ --}}
            <div class="filter-panel"><div class="filter-heading"><i class="bi bi-funnel-fill"></i> ค้นหาและกรองรายการบาร์โค้ด</div><div class="row g-2">

                <div class="col-md-6">
                    <input type="text"
                        class="form-control"
                        id="searchInput"
                        placeholder="🔍 ค้นหาชื่อสินค้า...">
                </div>

                <div class="col-md-4">
                    <select class="form-select" id="categoryFilter">
                        <option value="">📂 ทุกหมวดหมู่</option>

                        @foreach ($categories as $category)
                            <option value="{{ strtolower($category->name) }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button"
                        class="btn btn-dark-outline w-100"
                        onclick="selectAllVisible()">
                        <i class="bi bi-check2-all"></i> เลือกที่แสดงทั้งหมด
                    </button>
                </div>

            </div></div>

            <div class="selected-summary">
                <span class="text-muted">
                    เลือกแล้ว:
                    <span class="selected-badge"><strong id="selectedCount">0</strong> รายการ</span>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table barcode-table text-center align-middle">

                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox"
                                    id="selectAllCheckbox" class="form-check-input">
                            </th>

                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>หน่วย</th>
                            <th>รหัสบาร์โค้ด</th>
                            <th>บาร์โค้ด</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($products as $product)

                            @foreach ($product->productUnits as $unit)

                                <tr class="barcode-row"
                                    data-product="{{ strtolower($product->name) }}"
                                    data-category="{{ strtolower($product->category->name ?? '') }}">

                                    <td>
                                        <input type="checkbox"
                                            class="barcode-checkbox form-check-input"
                                            value="{{ $unit->id }}"
                                            data-code="{{ $unit->barcode }}"
                                            data-name="{{ $product->name }}"
                                            data-unit="{{ $unit->unit_name }}">
                                    </td>

                                    <td>
                                        {{ $product->name }}
                                    </td>

                                    <td>
                                        {{ $product->category->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $unit->unit_name }}
                                    </td>

                                    <td>
                                        <span class="barcode-code">{{ $unit->barcode }}</span>
                                    </td>

                                    <td>
                                        <span class="barcode-preview-box"><svg id="barcode-{{ $unit->id }}"></svg></span>
                                    </td>

                                    <td>
                                        <button type="button"
                                            onclick="printBarcode(
                                                {{ $unit->id }},
                                                '{{ $unit->barcode }}',
                                                '{{ addslashes($product->name) }}',
                                                '{{ addslashes($unit->unit_name) }}'
                                            )"
                                            class="btn btn-dark-outline btn-sm">

                                            🖨️ พิมพ์
                                        </button>
                                    </td>

                                </tr>

                            @endforeach

                        @endforeach

                    </tbody>

                </table>
            </div>

        </section>
    </div>
</div>

@include('layouts.partials.jsbarcode')


<script>

    /*
    |--------------------------------------------------------------------------
    | สร้าง Barcode
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {

        @foreach ($products as $product)
            @foreach ($product->productUnits as $unit)

                JsBarcode(
                    "#barcode-{{ $unit->id }}",
                    "{{ $unit->barcode }}",
                    {
                        format: "CODE128",
                        displayValue: false,
                        height: 50,
                        width: 2
                    }
                );

            @endforeach
        @endforeach

        updateSelectedCount();

    });


    /*
    |--------------------------------------------------------------------------
    | ค้นหา + Filter หมวดหมู่
    |--------------------------------------------------------------------------
    */

    function filterRows() {

        const searchValue =
            document
                .getElementById('searchInput')
                .value
                .toLowerCase();

        const categoryValue =
            document
                .getElementById('categoryFilter')
                .value
                .toLowerCase();


        document
            .querySelectorAll('.barcode-row')
            .forEach(function (row) {

                const product =
                    row.dataset.product;

                const category =
                    row.dataset.category;


                const matchProduct =
                    product.includes(searchValue);

                const matchCategory =
                    categoryValue === '' ||
                    category === categoryValue;


                row.style.display =
                    matchProduct && matchCategory
                        ? ''
                        : 'none';

            });

    }


    document
        .getElementById('searchInput')
        .addEventListener(
            'keyup',
            filterRows
        );


    document
        .getElementById('categoryFilter')
        .addEventListener(
            'change',
            filterRows
        );


    /*
    |--------------------------------------------------------------------------
    | เลือก Checkbox
    |--------------------------------------------------------------------------
    */

    document
        .addEventListener(
            'change',
            function (event) {

                if (
                    event.target.classList.contains(
                        'barcode-checkbox'
                    )
                ) {

                    updateSelectedCount();

                }

            }
        );


    /*
    |--------------------------------------------------------------------------
    | เลือกทั้งหมดที่กำลังแสดง
    |--------------------------------------------------------------------------
    */

    function selectAllVisible() {

        document
            .querySelectorAll('.barcode-row')
            .forEach(function (row) {

                if (
                    row.style.display !== 'none'
                ) {

                    const checkbox =
                        row.querySelector(
                            '.barcode-checkbox'
                        );

                    checkbox.checked = true;

                }

            });


        updateSelectedCount();

    }


    /*
    |--------------------------------------------------------------------------
    | Checkbox บนหัวตาราง
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('selectAllCheckbox')
        .addEventListener(
            'change',
            function () {

                const checked =
                    this.checked;


                document
                    .querySelectorAll('.barcode-row')
                    .forEach(function (row) {

                        if (
                            row.style.display !== 'none'
                        ) {

                            row.querySelector(
                                '.barcode-checkbox'
                            ).checked =
                                checked;

                        }

                    });


                updateSelectedCount();

            }
        );


    /*
    |--------------------------------------------------------------------------
    | นับรายการที่เลือก
    |--------------------------------------------------------------------------
    */

    function updateSelectedCount() {

        const count =
            document
                .querySelectorAll(
                    '.barcode-checkbox:checked'
                )
                .length;


        document
            .getElementById(
                'selectedCount'
            )
            .textContent =
            count;

    }


    /*
    |--------------------------------------------------------------------------
    | พิมพ์บาร์โค้ดลงสติกเกอร์ 50 x 20 มม.
    |--------------------------------------------------------------------------
    */

    function escapePrintText(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function printBarcodeLabels(items) {
        if (!items.length) {
            alert('ไม่พบรายการบาร์โค้ดที่ต้องการพิมพ์');
            return;
        }

        const printWindow = window.open('', '_blank');

        if (!printWindow) {
            alert('ไม่สามารถเปิดหน้าพิมพ์ได้ กรุณาอนุญาต Pop-up ในเบราว์เซอร์');
            return;
        }

        const objectUrls = [];
        const labelsHtml = items.map(function (item) {
            const svg = document.getElementById(`barcode-${item.id}`);
            if (!svg) return '';

            const svgClone = svg.cloneNode(true);
            svgClone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

            const svgData = new XMLSerializer().serializeToString(svgClone);
            const blob = new Blob([svgData], {
                type: 'image/svg+xml;charset=utf-8'
            });
            const imageUrl = URL.createObjectURL(blob);
            objectUrls.push(imageUrl);

            return `
                <section class="barcode-label">
                    <img class="barcode-image" src="${imageUrl}" alt="Barcode">
                    <div class="barcode-details">
                        <span class="product-name">${escapePrintText(item.name)}</span>
                        <span class="product-unit">${escapePrintText(item.unit)}</span>
                        <span class="barcode-number">${escapePrintText(item.code)}</span>
                    </div>
                </section>
            `;
        }).join('');

        printWindow.document.open();
        printWindow.document.write(`
            <!doctype html>
            <html lang="th">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>พิมพ์บาร์โค้ดสินค้า</title>
                <style>
                    @page {
                        size: 50mm 20mm;
                        margin: 0;
                    }

                    * { box-sizing: border-box; }

                    html, body {
                        width: 50mm;
                        margin: 0;
                        padding: 0;
                        font-family: Tahoma, Arial, sans-serif;
                        color: #000;
                    }

                    .barcode-label {
                        width: 50mm;
                        height: 20mm;
                        padding: 0.5mm 1mm;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        overflow: hidden;
                        text-align: center;
                        page-break-after: always;
                        break-after: page;
                    }

                    .barcode-label:last-child {
                        page-break-after: auto;
                        break-after: auto;
                    }

                    .barcode-image {
                        display: block;
                        width: 48mm;
                        max-width: 48mm;
                        height: 12mm;
                        object-fit: fill;
                        margin: 0 auto 0.7mm;
                    }

                    .barcode-details {
                        width: 100%;
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        justify-content: center;
                        gap: 1mm;
                        white-space: nowrap;
                        overflow: hidden;
                        line-height: 1;
                    }

                    .product-name {
                        max-width: 19mm;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        font-size: 5pt;
                        font-weight: 700;
                    }

                    .product-unit {
                        flex: none;
                        overflow: hidden;
                        font-size: 4.5pt;
                    }

                    .barcode-number {
                        flex: none;
                        overflow: hidden;
                        font-family: Arial, sans-serif;
                        font-size: 5pt;
                        font-weight: 700;
                        letter-spacing: 0.05pt;
                    }

                    @media screen {
                        body { background: #fff; }
                        .barcode-label {
                            background: #fff;
                            outline: none;
                            margin-bottom: 0;
                        }
                    }

                    @media print {
                        html, body { background: #fff; }
                        .barcode-label { border: none; outline: none; box-shadow: none; }
                    }
                </style>
            </head>
            <body>
                ${labelsHtml}
                <script>
                    window.addEventListener('load', async function () {
                        const images = Array.from(document.images);
                        await Promise.all(images.map(function (img) {
                            if (img.complete) return Promise.resolve();
                            return new Promise(function (resolve) {
                                img.onload = resolve;
                                img.onerror = resolve;
                            });
                        }));

                        setTimeout(function () {
                            window.focus();
                            window.print();
                        }, 250);
                    });

                    window.addEventListener('afterprint', function () {
                        window.close();
                    });
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();

        printWindow.addEventListener('beforeunload', function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
        });
    }

    // พิมพ์รายการที่ติ๊กเลือกไว้
    function printSelectedBarcodes() {
        const selected = document.querySelectorAll('.barcode-checkbox:checked');

        if (selected.length === 0) {
            alert('กรุณาเลือกบาร์โค้ดที่ต้องการพิมพ์');
            return;
        }

        const items = Array.from(selected).map(function (checkbox) {
            return {
                id: checkbox.value,
                code: checkbox.dataset.code || '',
                name: checkbox.dataset.name || '',
                unit: checkbox.dataset.unit || ''
            };
        });

        printBarcodeLabels(items);
    }

    // พิมพ์ทีละรายการ
    function printBarcode(productId, code, name, unit) {
        printBarcodeLabels([{
            id: productId,
            code: code,
            name: name,
            unit: unit
        }]);
    }

</script>


@endsection