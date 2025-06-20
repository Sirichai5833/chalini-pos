@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">📋 รายการคำสั่งซื้อทั้งหมด (สำหรับคนขาย)</h2>

        @if ($orders->count())
            @foreach ($orders as $order)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">คำสั่งซื้อ #{{ $order->order_code }}</h5>
                        <p><strong>สถานะ:</strong>
                            <span
                                class="badge 
                            @if ($order->status == 'กำลังจัดส่ง') bg-info
                            @elseif ($order->status == 'รอดำเนินการ') bg-warning
                            @elseif ($order->status == 'เสร็จสิ้น') bg-success
                            @else bg-secondary @endif
                        ">
                                {{ $order->status }}
                            </span>
                        </p>
                        <p><strong>วันที่สั่งซื้อ:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                        <p><strong>ชื่อลูกค้า:</strong> {{ $order->user->name ?? 'ไม่ทราบชื่อ' }}</p>
                        <p><strong>หมายเลขห้อง:</strong> {{ $order->user->room_number ?? '-' }}</p>
                        <p><strong>วิธีชำระเงิน:</strong> {{ $order->payment_method }}</p>
                        <p><strong>เบอร์โทรติดต่อ</strong> {{ $order->tracking_number }}</p>
                        @if ($order->slip_path)
                            <hr>
                            <h6>📷 สลิปการชำระเงิน</h6>
                            <a href="{{ asset('storage/' . $order->slip_path) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ asset('storage/' . $order->slip_path) }}" alt="Slip Image"
                                    style="max-width: 200px; height: auto; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.2); cursor: pointer;">
                            </a>
                        @else
                            <p class="text-muted">ไม่มีสลิปการชำระเงิน</p>
                        @endif
                        <hr>

                        <h6>🛒 รายการสินค้า</h6>
                        @if ($order->orderItems->count())
                            <ul class="list-group">
                                @foreach ($order->orderItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->product ? $item->product->name : '[สินค้าโดนลบ]' }}</strong>
                                            <br>
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

                        {{-- เพิ่มปุ่มเปลี่ยนสถานะ (ถ้าต้องการ) --}}
                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                            class="mt-3 d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label for="status" class="mb-0 fw-bold">เปลี่ยนสถานะ:</label>
                            <select name="status" id="status" class="form-select w-auto">
                                <option value="รอดำเนินการ" {{ $order->status == 'รอดำเนินการ' ? 'selected' : '' }}>
                                    รอดำเนินการ</option>
                                <option value="กำลังจัดส่ง" {{ $order->status == 'กำลังจัดส่ง' ? 'selected' : '' }}>
                                    กำลังจัดส่ง</option>
                                <option value="เสร็จสิ้น" {{ $order->status == 'เสร็จสิ้น' ? 'selected' : '' }}>เสร็จสิ้น
                                </option>
                                <option value="ยกเลิก" {{ $order->status == 'ยกเลิก' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">อัปเดต</button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- แสดง pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <p class="text-center">ไม่มีคำสั่งซื้อในระบบ</p>
        @endif
    </div>
@endsection
