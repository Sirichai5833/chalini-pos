@extends('layouts.online')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">📦 คำสั่งซื้อของคุณ</h2>

        @if ($orders->count())
            @foreach ($orders as $order)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">คำสั่งซื้อ #{{ $order->order_code }}</h5>
                        <p class="mb-1">สถานะ:
                            <span
                                class="badge 
                        @if ($order->status == 'กำลังจัดส่ง') bg-info 
                        @elseif($order->status == 'รอดำเนินการ') bg-warning 
                        @elseif($order->status == 'เสร็จสิ้น') bg-success 
                        @else bg-secondary @endif
                    ">
                                {{ $order->status }}
                            </span>
                        </p>
                        {{-- <p>รหัสพัสดุ: {{ $order->tracking_number ?? '-' }}</p> --}}
                        <p>วันที่สั่งซื้อ: {{ $order->created_at->format('d M Y') }}</p>
                        <p><strong>วิธีชำระเงิน:</strong> {{ $order->payment_method }}</p>

                        <div class="mt-3">
                            <h6>🛒 รายการสินค้า</h6>
                            @if ($order->orderItems->count())
                                <ul class="list-group">
                                    @foreach ($order->orderItems as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                @if ($item->product)
                                                    <strong>{{ $item->product->name }}</strong>
                                                @else
                                                    <strong class="text-danger">[สินค้าโดนลบ]</strong>
                                                @endif

                                                (จำนวน: {{ $item->quantity }})
                                            </div>
                                            <span>฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-3 fw-bold text-end">รวมทั้งสิ้น: ฿{{ number_format($order->total_amount, 2) }}
                                </p>
                            @else
                                <p>ไม่มีรายการสินค้า</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p>คุณยังไม่มีคำสั่งซื้อในระบบ</p>
        @endif
    </div>
@endsection
