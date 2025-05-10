@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="card-title mb-4">สร้างบาร์โค้ด</h2>

            <div class="mb-3">
                <label class="form-label">บาร์โค้ดที่สร้าง:</label>
                <div class="input-group">
                    <input type="text" id="barcode" readonly class="form-control bg-light" />
                    <button onclick="generateBarcode()" class="btn btn-success">
                        🔄 สร้างใหม่
                    </button>
                </div>
            </div>

            <div class="text-center my-4">
                <canvas id="barcodeCanvas"></canvas>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button onclick="copyBarcode()" class="btn btn-primary">
                    📋 คัดลอก
                </button>
                <button onclick="printBarcode()" class="btn btn-secondary">
                    🖨️ พิมพ์
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
function generateBarcode() {
    const timestamp = Date.now().toString().slice(-8); // 8 หลักท้ายของเวลา
    const random = Math.floor(Math.random() * 9000) + 1000; // 4 หลักสุ่ม
    const code = 'BC' + timestamp + random;

    document.getElementById('barcode').value = code;
    JsBarcode("#barcodeCanvas", code, {
        format: "CODE128",
        displayValue: true,
        fontSize: 16,
        height: 60
    });
}

function copyBarcode() {
    const input = document.getElementById('barcode');
    input.select();
    input.setSelectionRange(0, 99999); // รองรับมือถือ
    document.execCommand('copy');
    alert('คัดลอกบาร์โค้ดแล้ว: ' + input.value);
}

function printBarcode() {
    const canvas = document.getElementById('barcodeCanvas');
    const barcodeImage = canvas.toDataURL("image/png");

    const barcodeValue = document.getElementById('barcode').value;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>พิมพ์บาร์โค้ด</title>
                <style>
                    body { text-align: center; font-family: sans-serif; margin-top: 40px; }
                    img { max-width: 100%; height: auto; margin-bottom: 10px; }
                    p { font-size: 18px; }
                </style>
            </head>
            <body>
                <h3>บาร์โค้ดสินค้า</h3>
                <img src="${barcodeImage}" />
                <p>${barcodeValue}</p>
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

document.addEventListener('DOMContentLoaded', generateBarcode);
</script>
@endsection
