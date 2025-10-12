<div wire:poll.5s class="container-fluid py-4">
    <h3 class="mb-4 text-center text-primary fw-bold">รายการคำสั่งซื้อออนไลน์ <i class="bi bi-box-seam-fill"></i></h3>
    <hr class="mb-5">

    @if ($orders->count())
        <div class="row g-4">
            @foreach ($orders as $order)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-lg border-primary rounded-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-receipt me-2"></i> คำสั่งซื้อ #{{ $order->order_code }}
                            </h5>
                            <span
                                class="badge {{ $order->status == 'กำลังจัดส่ง'
                                    ? 'bg-info text-dark'
                                    : ($order->status == 'รอดำเนินการ'
                                        ? 'bg-warning text-dark'
                                        : ($order->status == 'เสร็จสิ้น'
                                            ? 'bg-success'
                                            : 'bg-secondary')) }} fs-6 px-3 py-2 rounded-pill">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-secondary"><i
                                                class="bi bi-calendar-event me-2"></i> วันที่สั่งซื้อ:</strong>
                                        {{ $order->created_at->format('d M Y H:i') }}</p>
                                    <p class="mb-1"><strong class="text-secondary"><i
                                                class="bi bi-person-circle me-2"></i> ชื่อลูกค้า:</strong>
                                        {{ $order->user->name ?? 'ไม่ทราบชื่อ' }}</p>
                                    <p class="mb-1"><strong class="text-secondary"><i
                                                class="bi bi-telephone-fill me-2"></i> เบอร์โทรติดต่อ:</strong>
                                        {{ $order->tracking_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-secondary"><i
                                                class="bi bi-house-door-fill me-2"></i> หมายเลขห้อง:</strong>
                                        {{ $order->user->room_number ?? '-' }}</p>
                                    <p class="mb-1"><strong class="text-secondary"><i
                                                class="bi bi-credit-card-fill me-2"></i> วิธีชำระเงิน:</strong>
                                        {{ $order->payment_method }}</p>
                                </div>
                            </div>

                            @if ($order->slip_path)
                                <hr class="my-3">
                                <h6 class="text-primary mb-2"><i class="bi bi-image-fill me-2"></i> สลิปการชำระเงิน</h6>
                                <a href="{{ asset('storage/' . $order->slip_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $order->slip_path) }}" alt="Slip Image"
                                        class="img-fluid rounded shadow-sm mb-3"
                                        style="max-height: 150px; cursor: pointer;">
                                </a>
                            @else
                                <p class="text-muted fst-italic mt-3"><i class="bi bi-info-circle-fill me-2"></i>
                                    ไม่มีสลิปการชำระเงิน</p>
                            @endif

                            <hr class="my-3">

                            <h6 class="text-primary mb-3"><i class="bi bi-cart-fill me-2"></i> รายการสินค้า</h6>
                            @if ($order->orderItems->count())
                                <ul class="list-group list-group-flush mb-3 border-top border-bottom rounded">
                                    @foreach ($order->orderItems as $item)
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                            <div>
                                                <strong
                                                    class="text-dark">{{ $item->product ? $item->product->name : '[สินค้าโดนลบ]' }}</strong><br>
                                                <small class="text-muted">
                                                    ราคา: ฿{{ number_format($item->price, 2) }} x
                                                    {{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}
                                                </small>
                                            </div>
                                            <span
                                                class="fw-bold text-success">฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-3 fw-bold text-end fs-5 text-primary">รวมทั้งสิ้น: <span
                                        class="text-success">฿{{ number_format($order->total_amount, 2) }}</span></p>
                            @else
                                <p class="text-muted fst-italic">ไม่มีรายการสินค้าในคำสั่งซื้อนี้</p>
                            @endif

                            <hr class="my-3">

                            {{-- ปุ่มรับออเดอร์ --}}
                            @if (!$order->assigned_to)
                                <form action="{{ route('orders.accept', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 mb-3">
                                        <i class="bi bi-person-check-fill me-1"></i> รับออเดอร์นี้
                                    </button>
                                </form>
                            @elseif ($order->assigned_to === auth()->id())
                                <div class="alert alert-success text-center py-2 mb-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> คุณรับออเดอร์นี้แล้ว
                                </div>
                            @else
                                <div class="alert alert-secondary text-center py-2 mb-3">
                                    <i class="bi bi-person-fill-lock me-1"></i> มีพนักงานคนอื่นรับออเดอร์นี้แล้ว
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
            <p class="mb-0">ขณะนี้ยังไม่มีคำสั่งซื้อเข้ามา กรุณารอสักครู่หรือตรวจสอบภายหลัง</p>
        </div>
    @endif
</div>

{{-- แจ้งเตือนเมื่อมีการรับออเดอร์ --}}
<script>
    window.addEventListener('notify', event => {
        alert(event.detail.message);
    });
</script>
