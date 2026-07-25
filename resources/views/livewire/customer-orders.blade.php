<div wire:poll.5s="pollOrders" class="container py-4">

    {{-- ส่วนหัวและสถิติย่อ --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-2">
            <i class="bi bi-box-seam-fill me-2"></i> ระบบจัดการคำสั่งซื้อ
        </h2>
        
    </div>

    {{-- ส่วนตัวกรองสถานะ (Filter) --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">กรองตามสถานะ</label>
                    <select class="form-select border-0 bg-light fw-bold" wire:model.live="statusFilter">
                        <option value="all">ทั้งหมด (ทุกสถานะ)</option>
                        <option value="รอเจ้าหน้าที่รับคำสั่งซื้อ">รอเจ้าหน้าที่ฯ</option>
                        <option value="กำลังจัดสินค้า">กำลังจัดสินค้า</option>
                        <option value="กำลังจัดส่ง">กำลังจัดส่ง</option>
                        <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                        <option value="ยกเลิก">ยกเลิก</option>
                    </select>
                </div>
            </div>
            
            {{-- Badge แสดงสถานะปัจจุบัน --}}
           
        </div>
    </div>
  
    <hr class="opacity-10 mb-5">

    @if ($orders->count())
        <div class="row g-4">
            @foreach ($orders as $order)
                <div class="col-12 col-md-6 col-lg-4" wire:key="order-{{ $order->id }}">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                        
                        {{-- แถบสีสถานะด้านบน --}}
                        <div style="height: 6px;" class="{{
                            ($order->status == 'กำลังจัดส่ง') ? 'bg-info' :
                            (($order->status == 'รอเจ้าหน้าที่รับคำสั่งซื้อ') ? 'bg-warning' :
                            (($order->status == 'เสร็จสิ้น') ? 'bg-success' :
                            (($order->status == 'ยกเลิก') ? 'bg-danger' : 'bg-secondary')))
                        }}"></div>

                        <div class="card-body p-4">
                            {{-- หัวการ์ด: เลขที่สั่งซื้อและ Badge --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-uppercase text-muted small mb-1">Order Code</h6>
                                    <h5 class="fw-bold text-dark mb-0">#{{ $order->order_code }}</h5>
                                </div>
                                <span class="badge {{
                                    ($order->status == 'กำลังจัดส่ง') ? 'bg-info text-dark' :
                                    (($order->status == 'รอเจ้าหน้าที่รับคำสั่งซื้อ') ? 'bg-warning text-dark' :
                                    (($order->status == 'เสร็จสิ้น') ? 'bg-success' :
                                    (($order->status == 'ยกเลิก') ? 'bg-danger' : 'bg-secondary')))
                                }} px-3 py-2 rounded-pill">
                                    {{ $order->status }}
                                </span>
                            </div>

                            {{-- รายละเอียดลูกค้า/เวลา --}}
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted"><i class="bi bi-clock me-1"></i> เวลาสั่งซื้อ:</span>
                                    <span class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted"><i class="bi bi-wallet2 me-1"></i> ชำระโดย:</span>
                                    <span class="fw-bold text-primary">{{ $order->payment_method }}</span>
                                </div>
                                @if ($order->user && $order->user->room_number)
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted"><i class="bi bi-door-closed me-1"></i> เลขห้อง:</span>
                                    <span class="fw-bold ">{{ $order->user->room_number }}</span>
                                </div>
                                @endif
                            </div>

                            {{-- รายการสินค้า --}}
                            <h6 class="fw-bold small mb-2 text-uppercase text-secondary">รายการสินค้า</h6>
                            <div class="overflow-auto mb-3" style="max-height: 150px;">
                                <ul class="list-group list-group-flush">
                                    @foreach ($order->orderItems as $item)
                                        <li class="list-group-item px-0 py-2 bg-transparent border-light d-flex justify-content-between align-items-center">
                                            <div class="small">
                                                @if ($item->product)
                                                    <div class="fw-bold">{{ $item->product->name }}</div>
                                                    <div class="text-muted small">x{{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}</div>
                                                @else
                                                    <span class="text-danger small">[สินค้าถูกลบ]</span>
                                                @endif
                                            </div>
                                            <span class="fw-bold small">฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <hr class="my-3 border-light">

                            {{-- ยอดรวม --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">ยอดรวมสุทธิ</span>
                                <h4 class="fw-bold text-success mb-0">฿{{ number_format($order->total_amount, 2) }}</h4>
                            </div>

                            {{-- หลักฐานการโอน / เหตุผลการยกเลิก --}}
                            @if ($order->proof_image)
                                <div class="mt-2 text-center">
                                    <p class="small text-muted mb-1 text-start">หลักฐานการชำระเงิน:</p>
                                    <img src="{{ asset('storage/' . $order->proof_image) }}" 
                                         class="img-fluid rounded-3 shadow-sm border cursor-pointer" 
                                         style="max-height: 300px; width: 100%; object-fit: cover;">
                                </div>
                            @endif

                            @if ($order->cancel_reason)
                                <div class="alert alert-danger py-2 px-3 mt-2 mb-0 rounded-3">
                                    <small class="fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> เหตุผลที่ยกเลิก:</small>
                                    <div class="small">{{ $order->cancel_reason }}</div>
                                </div>
                            @endif
                        </div>

   
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- หน้าจอเมื่อไม่มีข้อมูล --}}
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-inbox text-light-emphasis" style="font-size: 5rem;"></i>
            </div>
            <h4 class="text-muted">ไม่พบคำสั่งซื้อ</h4>
            <p class="text-secondary">ในขณะนี้ยังไม่มีรายการในสถานะ "{{ $statusFilter == 'all' ? 'ทั้งหมด' : $statusFilter }}"</p>
            @if($statusFilter != 'all')
                <button wire:click="$set('statusFilter', 'all')" class="btn btn-primary rounded-pill px-4">ดูออเดอร์ทั้งหมด</button>
            @endif
        </div>
    @endif

    {{-- เสียงแจ้งเตือน --}}
    <audio id="orderSound" src="{{ asset('sounds/notify.mp3') }}"></audio>
</div>