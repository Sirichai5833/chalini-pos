<div wire:poll.5s="pollOrders" class="container py-4">
    <h3 class="mb-4 text-center text-primary fw-bold">
        <i class="bi bi-bell-fill me-2"></i> คำสั่งซื้อล่าสุด
    </h3>
    <hr class="mb-5">

    @if ($orders->count())
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Responsive grid for cards, up to 3 columns --}}
            @foreach ($orders as $order)
                <div class="col">
                    <div class="card h-100 shadow-lg border-primary rounded-3 position-relative overflow-hidden">
                        {{-- Decorative stripe based on status --}}
                        <div class="status-stripe {{
                            ($order->status == 'กำลังจัดส่ง') ? 'bg-info' :
                            (($order->status == 'รอดำเนินการ') ? 'bg-warning' :
                            (($order->status == 'เสร็จสิ้น') ? 'bg-success' :
                            'bg-secondary'))
                        }}"></div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-dark mb-3">
                                คำสั่งซื้อ #{{ $order->order_code }}
                                <span class="badge {{
                                    ($order->status == 'กำลังจัดส่ง') ? 'bg-info text-dark' :
                                    (($order->status == 'รอดำเนินการ') ? 'bg-warning text-dark' :
                                    (($order->status == 'เสร็จสิ้น') ? 'bg-success' :
                                    'bg-secondary'))
                                }} ms-2 fs-6 px-3 py-2 rounded-pill float-end">
                                    {{ $order->status }}
                                </span>
                            </h5>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-2">
                                    <strong class="text-secondary"><i class="bi bi-calendar-event me-2"></i> วันที่:</strong>
                                    <span class="float-end">{{ $order->created_at->format('d M Y H:i') }}</span>
                                </li>
                                <li class="mb-2">
                                    <strong class="text-secondary"><i class="bi bi-credit-card-fill me-2"></i> ชำระเงิน:</strong>
                                    <span class="float-end">{{ $order->payment_method }}</span>
                                </li>
                                @if ($order->user)
                                    <li class="mb-2">
                                        <strong class="text-secondary"><i class="bi bi-person-circle me-2"></i> ลูกค้า:</strong>
                                        <span class="float-end">{{ $order->user->name }}</span>
                                    </li>
                                    @if ($order->user->room_number)
                                        <li class="mb-2">
                                            <strong class="text-secondary"><i class="bi bi-house-door-fill me-2"></i> ห้อง:</strong>
                                            <span class="float-end">{{ $order->user->room_number }}</span>
                                        </li>
                                    @endif
                                @endif
                                @if ($order->tracking_number)
                                    <li class="mb-2">
                                        <strong class="text-secondary"><i class="bi bi-phone-fill me-2"></i> ติดต่อ:</strong>
                                        <span class="float-end">{{ $order->tracking_number }}</span>
                                    </li>
                                @endif
                            </ul>

                            <hr class="my-3">

                            <h6 class="text-primary mb-3"><i class="bi bi-basket-fill me-2"></i> รายการสินค้า</h6>
                            <div class="flex-grow-1 overflow-auto pe-2" style="max-height: 200px;"> {{-- Scrollable item list --}}
                                <ul class="list-group list-group-flush border-top border-bottom rounded">
                                    @foreach ($order->orderItems as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                            <div>
                                                @if ($item->product)
                                                    <strong class="text-dark">{{ $item->product->name }}</strong>
                                                    <small class="d-block text-muted">
                                                        x{{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}
                                                        (<span class="text-muted">฿{{ number_format($item->price, 2) }}/หน่วย</span>)
                                                    </small>
                                                @else
                                                    <strong class="text-danger">[สินค้าถูกลบ]</strong>
                                                    <small class="d-block text-muted">
                                                        x{{ $item->quantity }}
                                                        (<span class="text-muted">฿{{ number_format($item->price, 2) }}/หน่วย</span>)
                                                    </small>
                                                @endif
                                            </div>
                                            <span class="fw-bold text-success text-nowrap">฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <p class="text-end mt-3 fw-bold fs-5 text-primary">
    <i class="bi bi-currency-dollar me-1"></i> รวม: 
    <span class="text-success">฿{{ number_format($order->total_amount, 2) }}</span>
</p>

{{-- ✅ แสดงรูปหลักฐานถ้ามี --}}
@if ($order->proof_image)
    <div class="mt-3">
        <strong class="text-secondary"><i class="bi bi-image-fill me-2"></i> หลักฐานการจัดส่ง:</strong><br>
        <a href="{{ asset('storage/' . $order->proof_image) }}" target="_blank">
            <img src="{{ asset('storage/' . $order->proof_image) }}" alt="Proof Image"
                 class="img-fluid mt-2 rounded shadow-sm"
                 style="max-width: 150px; border: 1px solid #ddd;">
        </a>
    </div>
@endif

{{-- ✅ แสดงหมายเหตุถ้ามี --}}
@if ($order->cancel_reason)
    <div class="mt-3">
        <strong class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> หมายเหตุการยกเลิก:</strong>
        <p class="mb-0 text-muted">{{ $order->cancel_reason }}</p>
    </div>
@endif

                         
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center mt-5 p-4 rounded-3 shadow-sm">
            <h4 class="alert-heading mb-3"><i class="bi bi-info-circle-fill me-2"></i> ไม่มีคำสั่งซื้อในระบบ</h4>
            <p class="mb-0">ขณะนี้ยังไม่มีคำสั่งซื้อใหม่เข้ามา กรุณารอสักครู่</p>
        </div>
    @endif

    {{-- Sound notification --}}
    <audio id="orderSound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>
</div>



{{-- Make sure you have Bootstrap Icons included in your main layout file: --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> --}}