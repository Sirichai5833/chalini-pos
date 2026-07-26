@extends('layouts.layout')

@section('content') <div class="container mt-5"> <div class="card shadow-lg"> <div class="card-body">


            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="card-title mb-0">รายการบาร์โค้ดสินค้า</h2>

                <button type="button"
                    class="btn btn-primary"
                    onclick="printSelectedBarcodes()">
                    🖨️ พิมพ์ที่เลือก
                </button>
            </div>

            {{-- ค้นหา + หมวดหมู่ --}}
            <div class="row mb-3 g-2">

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
                        class="btn btn-outline-secondary w-100"
                        onclick="selectAllVisible()">
                        ☑️ เลือกทั้งหมด
                    </button>
                </div>

            </div>

            <div class="mb-3">
                <span class="text-muted">
                    เลือกแล้ว:
                    <strong id="selectedCount">0</strong>
                    รายการ
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox"
                                    id="selectAllCheckbox">
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
                                            class="barcode-checkbox"
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
                                        {{ $unit->barcode }}
                                    </td>

                                    <td>
                                        <svg id="barcode-{{ $unit->id }}"></svg>
                                    </td>

                                    <td>
                                        <button type="button"
                                            onclick="printBarcode(
                                                {{ $unit->id }},
                                                '{{ $unit->barcode }}',
                                                '{{ addslashes($product->name) }}',
                                                '{{ addslashes($unit->unit_name) }}'
                                            )"
                                            class="btn btn-secondary">

                                            🖨️ พิมพ์
                                        </button>
                                    </td>

                                </tr>

                            @endforeach

                        @endforeach

                    </tbody>

                </table>
            </div>

        </div>
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
    | พิมพ์รายการที่เลือก
    |--------------------------------------------------------------------------
    */

    function printSelectedBarcodes() {

        const selected =
            document
                .querySelectorAll(
                    '.barcode-checkbox:checked'
                );


        if (
            selected.length === 0
        ) {

            alert(
                'กรุณาเลือกบาร์โค้ดที่ต้องการพิมพ์'
            );

            return;

        }


        let barcodeImages = [];


        selected.forEach(function (checkbox) {

            const id =
                checkbox.value;

            const svg =
                document
                    .getElementById(
                        `barcode-${id}`
                    );


            const svgData =
                new XMLSerializer()
                    .serializeToString(svg);


            const blob =
                new Blob(
                    [svgData],
                    {
                        type:
                            'image/svg+xml'
                    }
                );


            const url =
                URL.createObjectURL(
                    blob
                );


            barcodeImages.push({

                url: url,

                name:
                    checkbox.dataset.name,

                unit:
                    checkbox.dataset.unit,

                code:
                    checkbox.dataset.code

            });

        });


        let printWindow =
            window.open(
                '',
                '_blank'
            );


        let html = `

            <html>

            <head>

                <style>

                    body {
                        font-family: Arial, sans-serif;
                        margin: 10px;
                    }

                    .barcode-item {
                        display: inline-block;
                        width: 220px;
                        text-align: center;
                        margin: 10px;
                        padding: 8px;
                        page-break-inside: avoid;
                    }

                    img {
                        width: 200px;
                        height: auto;
                    }

                    p {
                        margin: 3px 0;
                        font-size: 14px;
                    }

                </style>

            </head>

            <body>

        `;


        barcodeImages.forEach(function (barcode) {

            html += `

                <div class="barcode-item">

                    <img src="${barcode.url}">

                    <p>
                        <strong>
                            ${barcode.name}
                        </strong>
                    </p>

                    <p>
                        ${barcode.unit}
                    </p>

                    <p>
                        ${barcode.code}
                    </p>

                </div>

            `;

        });


        html += `

                <script>

                    window.onload = function () {

                        window.print();

                        window.onafterprint =
                            function () {

                                window.close();

                            };

                    };

                <\/script>

            </body>

            </html>

        `;


        printWindow.document.write(
            html
        );

        printWindow.document.close();

    }


    /*
    |--------------------------------------------------------------------------
    | พิมพ์ทีละรายการ
    |--------------------------------------------------------------------------
    */

    function printBarcode(
        productId,
        code,
        name,
        unit
    ) {

        const svg =
            document
                .getElementById(
                    `barcode-${productId}`
                );


        const svgClone =
            svg.cloneNode(
                true
            );


        const svgData =
            new XMLSerializer()
                .serializeToString(
                    svgClone
                );


        const svgBlob =
            new Blob(
                [svgData],
                {
                    type:
                        'image/svg+xml;charset=utf-8'
                }
            );


        const url =
            URL.createObjectURL(
                svgBlob
            );


        const printWindow =
            window.open(
                '',
                '_blank'
            );


        printWindow.document.write(`

            <html>

            <head>

                <style>

                    body {
                        text-align: center;
                        font-family: sans-serif;
                        margin-top: 10px;
                    }

                    img {
                        max-width: 100%;
                        height: auto;
                    }

                    p {
                        font-size: 18px;
                        margin: 2px 0;
                    }

                </style>

            </head>

            <body>

                <img src="${url}">

                <p>
                    ${name} / ${unit}
                </p>

                <p>
                    ${code}
                </p>

                <script>

                    window.onload = function () {

                        window.print();

                        window.onafterprint =
                            () => window.close();

                    }

                <\/script>

            </body>

            </html>

        `);


        printWindow.document.close();

    }

</script>


@endsection
