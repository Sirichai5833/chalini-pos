@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2>รายละเอียดการขาย #{{ $sale->id }}</h2>
    <p><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</p>
    <p><strong>ผู้ขาย:</strong> {{ $sale->staff->name ?? 'N/A' }}</p>
    <p><strong>ประเภทการขาย:</strong> {{ ucfirst($sale->sale_type) }}</p>
    <p><strong>ยอดรวม:</strong> {{ number_format($sale->total_price, 2) }} ฿</p>

    <h4>รายการสินค้า</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>สินค้า</th>
                <th>หน่วย</th>
                <th>จำนวน</th>
                <th>ราคาต่อหน่วย</th>
                <th>รวม</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ optional($item->product)->name ?? 'ไม่พบสินค้า' }}</td>
                    <td>{{ optional($item->unit)->unit_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅️ กลับ</a>

        <button class="btn btn-success" onclick="printReceiptย้อนหลัง()">
            🧾 ปริ้นใบเสร็จ
        </button>

        @if (Auth::user()->is_admin)
            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $sale->id }})">
                🗑️ ยกเลิกรายการขาย
            </button>

            <form id="delete-form-{{ $sale->id }}" action="{{ route('staff.sales.destroy', $sale->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
                <input type="hidden" name="cancel_reason" id="cancel-reason-{{ $sale->id }}">
            </form>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ✅ ใช้ foreach Blade → ไม่ error แน่นอน
const saleData = {
    id: "{{ $sale->id }}",
    date: "{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}",
    staff: "{{ $sale->staff->name ?? 'N/A' }}",
    total: "{{ $sale->total_price }}",
    items: [
        @foreach ($sale->items as $item)
        {
            name: "{{ optional($item->product)->name ?? 'ไม่พบสินค้า' }}",
            unit: "{{ optional($item->unit)->unit_name }}",
            qty: {{ $item->quantity }},
            price: {{ $item->price }}
        },
        @endforeach
    ]
};

// ------------------- ลบ -------------------
function confirmDelete(saleId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกรายการขาย',
        text: "กรุณาระบุเหตุผลที่ต้องการยกเลิก",
        input: 'textarea',
        inputPlaceholder: 'ระบุเหตุผลที่นี่...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        preConfirm: (reason) => {
            if (!reason || reason.trim() === "") {
                Swal.showValidationMessage('กรุณากรอกเหตุผลก่อนกดยืนยัน')
            }
            return reason
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-reason-' + saleId).value = result.value;
            document.getElementById('delete-form-' + saleId).submit();
        }
    })
}

// ------------------- ปริ้นย้อนหลัง -------------------
function printReceiptย้อนหลัง() {
    const receiptWindow = window.open('', '', 'width=400,height=600');

    let content = `
<html>
<head>
<title>ใบเสร็จย้อนหลัง</title>
<style>
    body { font-family: monospace; font-size: 14px; }
    h2 { text-align: center; }
    .center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 8px 0; }
</style>
</head>
<body>

<h2>{{ $shopSettings->shop_name ?: 'ชาลินี' }} POS</h2>
<p class="center">*** ใบเสร็จย้อนหลัง ***</p>

<p>เลขที่บิล: ${saleData.id}</p>
<p>วันที่: ${saleData.date}</p>
<p>พนักงาน: ${saleData.staff}</p>

<div class="line"></div>
`;

    saleData.items.forEach((p, i) => {
        content += `
${i + 1}. ${p.name}
${p.qty} ${p.unit} x ${parseFloat(p.price).toFixed(2)} = ${(p.qty * p.price).toFixed(2)} บาท
`;
    });

    content += `
<div class="line"></div>

<p><b>ยอดรวม: ${parseFloat(saleData.total).toFixed(2)} บาท</b></p>

<div class="line"></div>
<p class="center">ขอบคุณที่ใช้บริการ</p>

</body>
</html>
`;

    receiptWindow.document.write(content);
    receiptWindow.document.close();
    receiptWindow.print();
}
</script>
@endsection