@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-primary">
        <i class="bi bi-person-check-fill me-2"></i> ออเดอร์ที่ฉันรับไว้ทั้งหมด
    </h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse ($orders as $order)
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-light">
                <strong>คำสั่งซื้อ #{{ $order->order_code }}</strong>
                <span class="float-end text-muted">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>ลูกค้า:</strong> {{ $order->user->name ?? '-' }}</p>
                        <p><strong>ห้อง:</strong> {{ $order->user->room_number ?? '-' }}</p>
                        <p><strong>เบอร์โทร:</strong> {{ $order->tracking_number }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>ชำระเงิน:</strong> {{ $order->payment_method }}</p>
                        <p><strong>สถานะ:</strong>
                            <span class="badge bg-info text-dark">{{ $order->status }}</span>
                        </p>
                    </div>
                </div>

                <ul class="list-group mb-3">
                    @foreach ($order->orderItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $item->product->name ?? '[ลบแล้ว]' }}</strong><br>
                                <small>฿{{ number_format($item->price, 2) }} x {{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}</small>
                            </div>
                            <span class="text-success fw-bold">฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="text-end mb-3">
                    <strong>รวมทั้งหมด:</strong> <span class="text-success">฿{{ number_format($order->total_amount, 2) }}</span>
                </div>

                {{-- อัปเดตสถานะ --}}
                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-2">
    @csrf
    @method('PATCH')

    <div class="d-flex gap-2 align-items-center">
        <label for="status-{{ $order->id }}" class="fw-bold">เปลี่ยนสถานะ:</label>
        <select name="status" id="status-{{ $order->id }}" class="form-select w-auto status-select">
            <option value="รอดำเนินการ" {{ $order->status == 'รอดำเนินการ' ? 'selected' : '' }}>รอดำเนินการ</option>
            <option value="กำลังจัดส่ง" {{ $order->status == 'กำลังจัดส่ง' ? 'selected' : '' }}>กำลังจัดส่ง</option>
            <option value="เสร็จสิ้น" {{ $order->status == 'เสร็จสิ้น' ? 'selected' : '' }}>เสร็จสิ้น</option>
            <option value="ยกเลิก" {{ $order->status == 'ยกเลิก' ? 'selected' : '' }}>ยกเลิก</option>
        </select>
    </div>

    <!-- ช่องอัปโหลดรูป (แสดงเฉพาะตอนเลือกเสร็จสิ้น) -->
    <div class="upload-proof mt-2" style="display:none;">
        <label class="fw-bold">แนบรูปหลักฐาน:</label>
        <input type="file" name="proof_image" accept="image/*" class="form-control">
    </div>

    <!-- ช่องหมายเหตุ (แสดงเฉพาะตอนเลือกยกเลิก) -->
    <div class="cancel-reason mt-2" style="display:none;">
        <label class="fw-bold">หมายเหตุ:</label>
        <textarea name="cancel_reason" rows="2" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary mt-2">
        <i class="bi bi-save2-fill me-1"></i> อัปเดต
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const uploadDiv = form.querySelector('.upload-proof');
            const reasonDiv = form.querySelector('.cancel-reason');

            // ซ่อนทุกอันก่อน
            uploadDiv.style.display = 'none';
            reasonDiv.style.display = 'none';

            if (this.value === 'เสร็จสิ้น') {
                uploadDiv.style.display = 'block';
            } else if (this.value === 'ยกเลิก') {
                reasonDiv.style.display = 'block';
            }
        });
    });

    // ตรวจสอบก่อน submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const status = this.querySelector('.status-select').value;
            const proofImage = this.querySelector('input[name="proof_image"]');
            const cancelReason = this.querySelector('textarea[name="cancel_reason"]');

            if (status === 'เสร็จสิ้น' && (!proofImage || proofImage.files.length === 0)) {
                e.preventDefault();
                alert('กรุณาแนบรูปหลักฐานก่อนบันทึก!');
            } else if (status === 'ยกเลิก' && (!cancelReason.value.trim())) {
                e.preventDefault();
                alert('กรุณากรอกหมายเหตุการยกเลิก!');
            }
        });
    });
});
</script>

            </div>
        </div>
    @empty
        <div class="alert alert-info">คุณยังไม่มีออเดอร์ที่รับไว้</div>
    @endforelse
</div>
@endsection
