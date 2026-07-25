@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">ประวัติคำสั่งซื้อ</h2>
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="date" name="start_date" class="form-control"
            value="{{ request('start_date') }}">
    </div>

    <div class="col-md-4">
        <input type="date" name="end_date" class="form-control"
            value="{{ request('end_date') }}">
    </div>

    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">🔍 ค้นหา</button>
        <a href="{{ url()->current() }}" class="btn btn-secondary w-100">รีเซ็ต</a>
    </div>
</form>
        @if ($orders->count())
            @foreach ($orders as $order)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">คำสั่งซื้อ #{{ $order->order_code }}</h5>

                        <p><strong>สถานะ:</strong>
                            <span
                                class="badge 
        @if ($order->status == 'เสร็จสิ้น') bg-success
        @elseif ($order->status == 'ยกเลิก') bg-danger
        @else bg-secondary @endif
    ">
                                {{ $order->status }}
                            </span>
                        </p>

                        @if ($order->proof_image)
                            <p>
                                <strong>รูปหลักฐาน:</strong><br>
                                <a href="{{ asset('storage/' . $order->proof_image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $order->proof_image) }}" alt="Proof Image"
                                        class="img-thumbnail mt-2" style="max-width: 200px;">
                                </a>
                            </p>
                        @endif

                        @if ($order->cancel_reason)
                            <p><strong>หมายเหตุการยกเลิก:</strong> {{ $order->cancel_reason }}</p>
                        @endif

                        <p><strong>รหัสพนักงาน:</strong> {{ $order->assigned_to ?? 'ไม่ทราบชื่อ' }}</p>
                        <p><strong>วันที่สั่งซื้อ:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                        <p><strong>ชื่อลูกค้า:</strong> {{ $order->user->name ?? 'ไม่ทราบชื่อ' }}</p>
                        <p><strong>ที่อยู่จัดส่ง:</strong> {{ $order->user->room_number ?? '-' }}</p>
                        <p><strong>วิธีชำระเงิน:</strong> {{ $order->payment_method }}</p>



                        <hr>

                        <h6>🛒 รายการสินค้า</h6>
                        @if ($order->orderItems->count())
                            <ul class="list-group">
                                @foreach ($order->orderItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->product ? $item->product->name : '[สินค้าโดนลบ]' }}</strong><br>
                                            ราคา: ฿{{ number_format($item->price, 2) }} x {{ $item->quantity }}
                                        </div>
                                        <span>฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <p class="mt-3 fw-bold text-end">รวมทั้งสิ้น: ฿{{ number_format($order->total_amount, 2) }}</p>
                        @else
                            <p>ไม่มีรายการสินค้า</p>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <p class="text-center">ยังไม่มีประวัติคำสั่งซื้อ</p>
        @endif
    </div>
@endsection
