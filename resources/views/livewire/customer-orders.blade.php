<div wire:poll.60s="pollOrders" class="orders-component">
<style>
.orders-component{--y:#ffd43b;--yd:#d9ad00;--bg:#0a0a0a;--surface:#141414;--card:#1a1a1a;--soft:#222;--text:#f7f7f7;--muted:#aaa;--border:rgba(255,212,59,.22);color:var(--text)}
.orders-component .oc-head{text-align:center;margin-bottom:2rem}.orders-component .oc-icon{width:62px;height:62px;display:grid;place-items:center;margin:0 auto .8rem;border:1px solid var(--border);border-radius:20px;background:rgba(255,212,59,.08);color:var(--y);font-size:1.6rem}
.orders-component .oc-title{margin:0;color:var(--y);font-weight:900}.orders-component .oc-sub{margin:.4rem 0 0;color:var(--muted);font-size:.85rem}
.orders-component .filter-card{max-width:520px;margin:0 auto 2rem;padding:1rem;border:1px solid var(--border);border-radius:18px;background:linear-gradient(180deg,#181818,#111);box-shadow:0 14px 30px rgba(0,0,0,.3)}
.orders-component .filter-label{display:block;margin-bottom:.5rem;color:var(--y);font-size:.72rem;font-weight:850;text-transform:uppercase;letter-spacing:.04em}
.orders-component .filter-select{min-height:48px;border:1px solid #363636;border-radius:13px;background:#111;color:#fff;font-weight:750}.orders-component .filter-select:focus{border-color:var(--y);background:#151515;color:#fff;box-shadow:0 0 0 .2rem rgba(255,212,59,.12)}
.orders-component .oc-divider{border-color:rgba(255,212,59,.14);opacity:1;margin-bottom:2rem}
.orders-component .order-card{overflow:hidden;border:1px solid #2d2d2d!important;border-radius:20px!important;background:linear-gradient(180deg,#1b1b1b,#121212)!important;color:#fff;box-shadow:0 14px 30px rgba(0,0,0,.34)!important;transition:.2s}
.orders-component .order-card:hover{border-color:rgba(255,212,59,.55)!important;transform:translateY(-4px);box-shadow:0 22px 44px rgba(0,0,0,.46)!important}
.orders-component .status-line{height:7px}.orders-component .waiting{background:#ffc107;color:#111}.orders-component .preparing{background:#6c757d;color:#fff}.orders-component .shipping{background:#0dcaf0;color:#06252c}.orders-component .completed{background:#198754;color:#fff}.orders-component .cancelled{background:#dc3545;color:#fff}
.orders-component .order-code-label,.orders-component .section-label{color:var(--muted);font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em}.orders-component .order-code{margin:0;color:#fff;font-size:1.05rem;font-weight:900}
.orders-component .status-badge{padding:.48rem .72rem;border-radius:999px;font-size:.68rem;font-weight:850;text-align:center;line-height:1.25}
.orders-component .meta-box{padding:.85rem;border:1px solid #2b2b2b;border-radius:14px;background:#101010}.orders-component .meta-row{display:flex;justify-content:space-between;gap:.8rem;font-size:.78rem}.orders-component .meta-row+.meta-row{margin-top:.55rem}.orders-component .meta-label{color:var(--muted)}.orders-component .meta-value{color:#fff;font-weight:800;text-align:right}.orders-component .payment{color:var(--y)}
.orders-component .items-scroll{max-height:150px;scrollbar-width:thin;scrollbar-color:var(--y) #222}.orders-component .items-scroll::-webkit-scrollbar{width:6px}.orders-component .items-scroll::-webkit-scroll-track{background:#222}.orders-component .items-scroll::-webkit-scrollbar-thumb{background:var(--y);border-radius:10px}
.orders-component .item-row{border-color:#2b2b2b!important;background:transparent!important;color:#fff!important}.orders-component .item-name{font-weight:800}.orders-component .item-unit{color:var(--muted)}.orders-component .item-price,.orders-component .total{color:var(--y);font-weight:900}
.orders-component .total-breakdown{display:grid;gap:.45rem;margin-bottom:1rem;padding:.9rem;border:1px solid var(--border);border-radius:14px;background:rgba(255,212,59,.06)}
.orders-component .total-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;font-size:.82rem}
.orders-component .total-row .label{color:var(--muted)}
.orders-component .total-row.final{padding-top:.6rem;margin-top:.15rem;border-top:1px solid rgba(255,255,255,.12);font-size:.95rem;font-weight:900}
.orders-component .proof-image{width:100%;max-height:300px;border:1px solid #363636!important;border-radius:14px!important;object-fit:cover;transition:.2s}.orders-component .proof-image:hover{border-color:var(--y)!important;transform:scale(1.01)}
.orders-component .cancel-box{border:1px solid rgba(239,68,68,.3);border-radius:13px;background:rgba(239,68,68,.1);color:#ffb4b4}
.orders-component .empty-state{padding:3.5rem 1rem;border:1px dashed var(--border);border-radius:22px;background:rgba(18,18,18,.72);text-align:center}.orders-component .empty-icon{width:78px;height:78px;display:grid;place-items:center;margin:0 auto 1rem;border-radius:24px;background:rgba(255,212,59,.08);color:var(--y);font-size:2rem}
.orders-component .btn-yellow{border:1px solid var(--y);border-radius:999px;background:linear-gradient(135deg,#ffe56b,var(--y));color:#111;font-weight:850}.orders-component .pagination{--bs-pagination-color:var(--y);--bs-pagination-bg:#171717;--bs-pagination-border-color:#333;--bs-pagination-hover-color:#111;--bs-pagination-hover-bg:var(--y);--bs-pagination-hover-border-color:var(--y);--bs-pagination-active-color:#111;--bs-pagination-active-bg:var(--y);--bs-pagination-active-border-color:var(--y);--bs-pagination-disabled-bg:#111;--bs-pagination-disabled-border-color:#2a2a2a}
@media(max-width:575.98px){.orders-component .order-card .card-body{padding:1rem!important}.orders-component .status-badge{max-width:130px;font-size:.62rem}}
</style>

@php
$statusClass = fn($status) => match($status) {
    'รอเจ้าหน้าที่รับคำสั่งซื้อ' => 'waiting',
    'กำลังจัดสินค้า' => 'preparing',
    'กำลังจัดส่ง' => 'shipping',
    'เสร็จสิ้น' => 'completed',
    'ยกเลิก' => 'cancelled',
    default => 'preparing',
};
@endphp

<div class="oc-head">
    
    <h2 class="oc-title">ระบบจัดการคำสั่งซื้อ</h2>
    <p class="oc-sub">ติดตามสถานะและรายละเอียดคำสั่งซื้อของคุณ</p>
</div>

<div class="filter-card">
    <label class="filter-label" for="statusFilter"><i class="bi bi-funnel-fill me-1"></i>กรองตามสถานะ</label>
    <select id="statusFilter" class="form-select filter-select" wire:model.live="statusFilter">
        <option value="all">ทั้งหมด (ทุกสถานะ)</option>
        <option value="รอเจ้าหน้าที่รับคำสั่งซื้อ">รอเจ้าหน้าที่รับคำสั่งซื้อ</option>
        <option value="กำลังจัดสินค้า">กำลังจัดสินค้า</option>
        <option value="กำลังจัดส่ง">กำลังจัดส่ง</option>
        <option value="เสร็จสิ้น">เสร็จสิ้น</option>
        <option value="ยกเลิก">ยกเลิก</option>
    </select>
</div>

<hr class="oc-divider">

@if($orders->count())
<div class="row g-4">
@foreach($orders as $order)
@php
    $class = $statusClass($order->status);

    $shippingFee = max(
        0,
        (float) ($order->shipping_fee ?? 0)
    );

    $itemsSubtotal = $order->orderItems->sum(function ($item) {
        return (float) $item->price
            * (int) $item->quantity;
    });

    $orderGrandTotal = (float) $order->total_amount;

    if ($orderGrandTotal <= 0) {
        $orderGrandTotal = $itemsSubtotal + $shippingFee;
    }
@endphp
<div class="col-12 col-md-6 col-lg-4" wire:key="order-{{ $order->id }}">
    <article class="card order-card h-100">
        <div class="status-line {{ $class }}"></div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <div class="order-code-label mb-1">Order Code</div>
                    <h3 class="order-code">#{{ $order->order_code }}</h3>
                </div>
                <span class="status-badge {{ $class }}">{{ $order->status }}</span>
            </div>

            <div class="meta-box mb-3">
                <div class="meta-row"><span class="meta-label"><i class="bi bi-clock me-1"></i>เวลาสั่งซื้อ</span><span class="meta-value">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                <div class="meta-row"><span class="meta-label"><i class="bi bi-wallet2 me-1"></i>ชำระโดย</span><span class="meta-value payment">{{ $order->payment_method }}</span></div>
                @if($order->user && $order->user->room_number)
                <div class="meta-row"><span class="meta-label"><i class="bi bi-door-closed me-1"></i>เลขห้อง</span><span class="meta-value">{{ $order->user->room_number }}</span></div>
                @endif
            </div>

            <div class="section-label mb-2">รายการสินค้า</div>
            <div class="overflow-auto items-scroll mb-3">
                <ul class="list-group list-group-flush">
                @foreach($order->orderItems as $item)
                    <li class="list-group-item item-row px-0 py-2 d-flex justify-content-between align-items-center gap-2">
                        <div class="small">
                            @if($item->product)
                                <div class="item-name">{{ $item->product->name }}</div>
                                <div class="item-unit">x{{ $item->quantity }} {{ $item->productUnit->unit_name ?? '' }}</div>
                            @else
                                <span class="text-danger">[สินค้าถูกลบ]</span>
                            @endif
                        </div>
                        <span class="item-price small text-nowrap">฿{{ number_format($item->price * $item->quantity, 2) }}</span>
                    </li>
                @endforeach
                </ul>
            </div>

            <hr class="border-secondary opacity-25">

            <div class="total-breakdown">
                <div class="total-row">
                    <span class="label">ยอดรวมสินค้า</span>
                    <strong>
                        ฿{{ number_format($itemsSubtotal, 2) }}
                    </strong>
                </div>

                <div class="total-row">
                    <span class="label">
                        <i class="bi bi-truck me-1"></i>
                        ค่าจัดส่ง
                    </span>

                    <strong>
                        @if ($shippingFee > 0)
                            ฿{{ number_format($shippingFee, 2) }}
                        @else
                            ฟรี
                        @endif
                    </strong>
                </div>

                <div class="total-row final">
                    <span>ยอดรวมสุทธิ</span>

                    <h4 class="total mb-0">
                        ฿{{ number_format($orderGrandTotal, 2) }}
                    </h4>
                </div>
            </div>

            @if($order->proof_image)
            <div class="mt-2">
                <p class="small text-secondary mb-2"><i class="bi bi-image me-1"></i>หลักฐานการชำระเงิน</p>
                <img src="{{ asset('storage/'.$order->proof_image) }}" alt="หลักฐานการชำระเงิน {{ $order->order_code }}" class="img-fluid proof-image" loading="lazy" decoding="async">
            </div>
            @endif

            @if($order->cancel_reason)
            <div class="cancel-box py-2 px-3 mt-3">
                <small class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>เหตุผลที่ยกเลิก</small>
                <div class="small mt-1">{{ $order->cancel_reason }}</div>
            </div>
            @endif
        </div>
    </article>
</div>
@endforeach
</div>

<div class="mt-4 d-flex justify-content-center">{{ $orders->links() }}</div>
@else
<div class="empty-state">
    <div class="empty-icon"><i class="bi bi-inbox"></i></div>
    <h4 class="fw-bold text-white">ไม่พบคำสั่งซื้อ</h4>
    <p class="text-secondary">ในขณะนี้ยังไม่มีรายการในสถานะ “{{ $statusFilter == 'all' ? 'ทั้งหมด' : $statusFilter }}”</p>
    @if($statusFilter != 'all')
    <button wire:click="$set('statusFilter','all')" class="btn btn-yellow px-4"><i class="bi bi-arrow-counterclockwise me-1"></i>ดูออเดอร์ทั้งหมด</button>
    @endif
</div>
@endif

<audio id="orderSound" src="{{ asset('sounds/notify.mp3') }}" preload="none"></audio>
</div>