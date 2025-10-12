@extends('layouts.layout')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h2 class="card-title mb-4">รายการบาร์โค้ดสินค้า</h2>
                <div class="mb-3">
    <input type="text" class="form-control" id="searchInput" placeholder="🔍 ค้นหาชื่อสินค้า...">
</div>


                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อสินค้า</th>
                            <th>หน่วย</th>
                            <th>รหัสบาร์โค้ด</th>
                            <th>ตัวอย่าง</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
   @foreach ($products as $product)
   @foreach ($product->productUnits as $unit)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $unit->unit_name }}</td>
            <td>{{ $unit->barcode }}</td>
            <td>
                <svg id="barcode-{{ $unit->id }}"></svg>
            </td>
            <td>
                <button
                    onclick="printBarcode({{ $unit->id }}, '{{ $unit->barcode }}', '{{ $product->name }}', '{{ $unit->unit_name }}')"
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

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const productName = row.querySelector('td:first-child').textContent.toLowerCase();
        if (productName.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});


        document.addEventListener('DOMContentLoaded', () => {
          @foreach ($products as $product)
    @foreach ($product->productUnits as $unit)
        JsBarcode("#barcode-{{ $unit->id }}", "{{ $unit->barcode }}", {
            format: "CODE128",
            displayValue: false,
            height: 50,
            width: 2
        });
    @endforeach
@endforeach

        });

        function printBarcode(productId, code, name, unit) {
            const svg = document.getElementById(`barcode-${productId}`);
            const svgClone = svg.cloneNode(true);

            // แปลง SVG เป็น base64 image
            const svgData = new XMLSerializer().serializeToString(svgClone);
            const svgBlob = new Blob([svgData], {
                type: 'image/svg+xml;charset=utf-8'
            });
            const url = URL.createObjectURL(svgBlob);

            const printWindow = window.open('', '_blank');
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
                margin-bottom: 1px; /* ลดระยะห่างลง */
            }
            p {
                font-size: 18px;
                margin: 2px 0; /* ลด margin บน-ล่าง ให้พอดี */
            }
        </style>
            </head>
            <body>
                <img src="${url}" />
                <p>${name} / ${unit}</p>
                <p>${code}</p>
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = () => window.close();
                    }
                <\/script>
            </body>
        </html>
    `);
            printWindow.document.close();
        }
    </script>
@endsection
