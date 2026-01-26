@extends('layouts.layout')

@section('content')
<style>
    .stock-table-container {
        height: 70vh; /* กำหนดความสูงของตารางเพื่อให้ Scroll ได้ */
        overflow-y: auto;
    }
    .sticky-header th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa; /* bg-light */
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
    .actual-input:focus {
        background-color: #f0f8ff; /* สีฟ้าอ่อนเมื่อ Click */
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold text-dark d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded p-2" style="width: 50px; height: 50px;">
                    📦
                </span>
                ตรวจนับสต็อกสินค้า
            </h2>
            <p class="text-muted small ms-1 mb-0">จัดการและตรวจสอบความถูกต้องของสินค้าคงคลัง</p>
        </div>

        <div class="d-flex flex-column align-items-end gap-2">
            <div class="d-flex gap-2">
                <div class="card px-3 py-2 border bg-white d-flex flex-row align-items-center shadow-sm">
                    <span class="text-muted small me-1">📅 วันที่:</span>
                    <span class="fw-bold text-dark">{{ now()->format('d/m/Y') }}</span>
                </div>
                <a href="{{ route('staff.stock.check.report') }}" class="btn btn-outline-primary fw-bold shadow-sm">
                    📊 ดูรายงานสรุป
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    🔍
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="ค้นหาชื่อสินค้า หรือ Barcode..." autocomplete="off">
            </div>
        </div>
    </div>

    <form action="{{ route('staff.stock.check.store') }}" method="POST">
        @csrf
        <div class="card shadow border-0 rounded-3">
            
            <div class="card-body p-0 stock-table-container">
                <table class="table table-hover table-nowrap align-middle mb-0">
                    <thead class="sticky-header">
                        <tr>
                            <th class="ps-4 py-3" style="width: 40%;">สินค้า</th>
                            <th class="text-center py-3">หน่วย</th>
                            <th class="text-center py-3 bg-light">คงเหลือ (ระบบ)</th>
                            <th class="text-center py-3 text-primary bg-primary bg-opacity-10" style="width: 150px;">นับจริง</th>
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
                                            <small class="text-muted font-monospace">Barcode: {{ $stock->unit->barcode ?? '-' }}</small>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                            {{ $stock->unit->unit_name }}
                                        </span>
                                    </td>

                                    <td class="text-center bg-light">
                                        <span class="fw-bold text-secondary system-qty font-monospace fs-5">
                                            {{ $stock->warehouse_stock + $stock->store_stock }}
                                        </span>
                                        <input type="hidden" name="items[{{ $stock->id }}][system_qty]" 
                                               value="{{ $stock->warehouse_stock + $stock->store_stock }}">
                                    </td>

                                    <td class="text-center bg-primary bg-opacity-10">
                                        <input type="number" 
                                               name="items[{{ $stock->id }}][actual_qty]"
                                               class="form-control form-control-lg text-center fw-bold text-primary actual-input mx-auto"
                                               style="width: 100px;"
                                               value="{{ $stock->warehouse_stock + $stock->store_stock }}" 
                                               min="0"
                                               oninput="calculateDiff(this)">
                                    </td>

                                    <td class="text-center">
                                        <span class="diff-display badge bg-light text-secondary fs-6 border" style="min-width: 50px;">
                                            0
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center border-top">
                <small class="text-muted">* กรุณาตรวจสอบความถูกต้องก่อนบันทึก</small>
                
                <div class="d-flex gap-2">
                    <button type="button" onclick="window.history.back()" class="btn btn-light border text-secondary fw-semibold px-4">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm d-flex align-items-center gap-2">
                        💾 บันทึกผลการตรวจนับ
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // ฟังก์ชันคำนวณส่วนต่าง
    function calculateDiff(input) {
        const row = input.closest('.stock-row');
        // ลบเครื่องหมายจุลภาคออก (ถ้ามี) ก่อนแปลงเป็นตัวเลข
        const systemQtyText = row.querySelector('.system-qty').innerText.replace(/,/g, ''); 
        const systemQty = parseFloat(systemQtyText) || 0;
        const actualQty = parseFloat(input.value) || 0;
        
        const diff = actualQty - systemQty;
        const diffDisplay = row.querySelector('.diff-display');

        // แสดงผลตัวเลข (+ หรือ -)
        diffDisplay.innerText = diff > 0 ? `+${diff}` : diff;

        // Reset Classes (Bootstrap Classes)
        diffDisplay.className = 'diff-display badge fs-6 border'; // Base classes
        diffDisplay.style.minWidth = '50px';

        if (diff === 0) {
            diffDisplay.classList.add('bg-light', 'text-secondary');
        } else if (diff > 0) {
            // สินค้าเกิน (สีเขียว)
            diffDisplay.classList.add('bg-success', 'bg-opacity-25', 'text-success', 'border-success');
        } else {
            // สินค้าขาด (สีแดง)
            diffDisplay.classList.add('bg-danger', 'bg-opacity-25', 'text-danger', 'border-danger');
        }
    }

    // ฟังก์ชันค้นหา (Search)
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll('.stock-row');

        rows.forEach(row => {
            let text = row.querySelector('.product-name').innerText.toUpperCase();
            
            if (text.indexOf(filter) > -1) {
                row.style.display = ""; // แสดงบรรทัด
            } else {
                row.style.display = "none"; // ซ่อนบรรทัด
            }
        });
    });
</script>
@endsection