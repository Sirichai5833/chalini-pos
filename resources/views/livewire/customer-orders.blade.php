<div wire:poll.5s="pollOrders">
    @if ($orders->count())
        @foreach ($orders as $order)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">คำสั่งซื้อ #{{ $order->order_code }}</h5>
                    <p>สถานะ:
                        <span class="badge 
                            @if ($order->status == 'กำลังจัดส่ง') bg-info 
                            @elseif($order->status == 'รอดำเนินการ') bg-warning 
                            @elseif($order->status == 'เสร็จสิ้น') bg-success 
                            @else bg-secondary @endif
                        ">
                            {{ $order->status }}
                        </span>
                    </p>
                    <p>วันที่: {{ $order->created_at->format('d M Y') }}</p>
                    <p><strong>ชำระเงิน:</strong> {{ $order->payment_method }}</p>

                  <h6>🛒 รายการสินค้า</h6>
@foreach ($order->orderItems as $item)
    <div class="d-flex justify-content-between">
        <div>
            @if ($item->product)
                <strong>{{ $item->product->name }}</strong>
                (x{{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }})
            @else
                <strong class="text-danger">[ลบแล้ว]</strong>
                (x{{ $item->quantity }})
            @endif
        </div>
        <span>฿{{ number_format($item->price * $item->quantity, 2) }}</span>
    </div>
@endforeach



                    <p class="text-end mt-2 fw-bold">รวม: ฿{{ number_format($order->total_amount, 2) }}</p>
                </div>
            </div>
        @endforeach
    @else
        <p>คุณยังไม่มีคำสั่งซื้อในระบบ</p>
    @endif

    {{-- เสียงแจ้งเตือน --}}
    <audio id="orderSound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>
</div>

@push('scripts')
<script>
    Livewire.on('order-status-changed', ({ id, status }) => {
        const sound = document.getElementById('orderSound');
        if (sound) sound.play().catch(() => {});

        Swal.fire({
            icon: 'info',
            title: 'สถานะอัปเดต',
            text: `คำสั่งซื้อ #${id} ถูกเปลี่ยนเป็น ${status}`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
@endpush
