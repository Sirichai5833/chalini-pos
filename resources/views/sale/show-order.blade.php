@extends('layouts.layout')

@section('content')

    <div class="container py-4">

        
        <h3 class="mb-4 text-primary">
            <i class="bi bi-person-check-fill me-2"></i> ออเดอร์ที่ฉันรับไว้ทั้งหมด
        </h3>

        <div class="mb-3">
            <button class="btn btn-outline-warning btn-sm filter-btn" data-status="late">
                ล่าช้า (เกิน 15 นาที)
            </button>

            <button class="btn btn-outline-primary btn-sm filter-btn" data-status="กำลังจัดสินค้า">
                กำลังจัดสินค้า
            </button>

            <button class="btn btn-outline-primary btn-sm filter-btn" data-status="กำลังจัดส่ง">
                กำลังจัดส่ง
            </button>

            <button class="btn btn-outline-secondary btn-sm filter-btn" data-status="all">
                ทั้งหมด
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @forelse ($orders as $order)
            {{-- เช็คว่าล่าช้าไหม --}}

            @php
                $isLate =
                    now()->greaterThan($order->updated_at->addMinutes(15)) &&
                    !in_array($order->status, ['เสร็จสิ้น', 'ยกเลิก']);
            @endphp

            <div class="card shadow-sm mb-4 border-primary order-card" data-status="{{ $order->status }}"
                data-late="{{ $isLate ? '1' : '0' }}">

                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <strong>คำสั่งซื้อ #{{ $order->order_code }}</strong>

                        @if ($isLate)
                            <span class="badge bg-danger ms-2">⏰ เกิน 15 นาที</span>
                        @endif
                    </div>

                    <span class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</span>
                </div>

                <div class="card-body">

                    {{-- แจ้งเตือนในหน้า --}}
                    @if ($isLate)
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <span class="fs-4 me-2">⚠</span>
                            <div>
                                คำสั่งซื้อนี้เกิน 15 นาทีแล้ว! กรุณาดำเนินการโดยเร็วที่สุด
                            </div>
                        </div>
                    @endif

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

                                    <small class="text-muted">
                                        ฿{{ number_format($item->price, 2) }} ×
                                        {{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}
                                    </small>

                                    {{-- 🎁 แสดงของแถม / รายละเอียด --}}
                                    @if (!empty($item->product->description))
                                        <div class="text-success mt-1">
                                            ของแถม: {{ $item->product->description }}
                                        </div>
                                    @endif
                                </div>

                                <span class="text-success fw-bold">
                                    ฿{{ number_format($item->price * $item->quantity, 2) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="text-end mb-3">
                        <strong>รวมทั้งหมด:</strong>
                        <span class="text-success">฿{{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    {{-- ฟอร์มอัปเดตสถานะ --}}
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                        enctype="multipart/form-data" class="d-flex flex-column gap-2 status-form">
                        @csrf
                        @method('PATCH')

                        <div class="d-flex gap-2 align-items-center">
                            <label class="fw-bold">เปลี่ยนสถานะ:</label>
                            <select name="status" class="form-select w-auto status-select">
                                <option value="กำลังจัดสินค้า" @selected($order->status == 'กำลังจัดสินค้า')>กำลังจัดสินค้า</option>
                                <option value="กำลังจัดส่ง" @selected($order->status == 'กำลังจัดส่ง')>กำลังจัดส่ง</option>
                                <option value="เสร็จสิ้น" @selected($order->status == 'เสร็จสิ้น')>เสร็จสิ้น</option>
                                <option value="ยกเลิก" @selected($order->status == 'ยกเลิก')>ยกเลิก</option>
                            </select>
                        </div>

                        <div class="upload-proof mt-2" style="display:none;">
                            <label class="fw-bold">แนบรูปหลักฐาน:</label>
                            <input type="file" name="proof_image" accept="image/*" class="form-control">
                        </div>

                        <div class="cancel-reason mt-2" style="display:none;">
                            <label class="fw-bold">หมายเหตุ:</label>
                            <textarea name="cancel_reason" rows="2" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="bi bi-save2-fill me-1"></i> อัปเดต
                        </button>
                    </form>

                </div>
            </div>

        @empty
            <div class="alert alert-info">คุณยังไม่มีออเดอร์ที่รับไว้</div>
        @endforelse
        

    </div>

    {{-- แจ้งเตือนแบบ Popup --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // toggle ช่องอัปโหลด / หมายเหตุ
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const form = this.closest('form');
                    form.querySelector('.upload-proof').style.display = 'none';
                    form.querySelector('.cancel-reason').style.display = 'none';

                    if (this.value === 'เสร็จสิ้น') {
                        form.querySelector('.upload-proof').style.display = 'block';
                    }
                    if (this.value === 'ยกเลิก') {
                        form.querySelector('.cancel-reason').style.display = 'block';
                    }
                });
            });

            // ตรวจสอบก่อน submit
            document.querySelectorAll('.status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const status = this.querySelector('.status-select').value;
                    const proofImage = this.querySelector('input[name="proof_image"]');
                    const cancelReason = this.querySelector('textarea[name="cancel_reason"]');

                    if (status === 'เสร็จสิ้น' && proofImage.files.length === 0) {
                        e.preventDefault();
                        alert('กรุณาแนบรูปหลักฐานก่อนบันทึก!');
                    }

                    if (status === 'ยกเลิก' && cancelReason.value.trim() === '') {
                        e.preventDefault();
                        alert('กรุณากรอกหมายเหตุการยกเลิก!');
                    }
                });
            });



            // 🟢 Filter ในหน้าเดียว
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    const status = this.dataset.status;

                    document.querySelectorAll('.order-card').forEach(card => {

                        const cardStatus = card.dataset.status;
                        const isLate = card.dataset.late === '1';

                        if (status === 'all') {
                            card.style.display = '';
                        } else if (status === 'late') {
                            card.style.display = isLate ? '' : 'none';
                        } else {
                            card.style.display = (cardStatus === status) ? '' : 'none';
                        }
                    });
                });
            });

        });
    </script>

@endsection
