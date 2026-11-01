@extends('layouts.online')



@section('content')



@php
    // เก็บค่าจัดส่งปกติไว้เสมอ เพื่อให้คำนวณใหม่ได้เมื่อยอดต่ำกว่าเกณฑ์
    $normalShippingFee = max(0, (float) ($shippingFee ?? 0));
    $freeShippingEnabled = filter_var($freeShippingEnabled ?? false, FILTER_VALIDATE_BOOLEAN);
    $freeShippingMinimum = max(0, (float) ($freeShippingMinimum ?? 200));

    $shippingFee = ($freeShippingEnabled && (float) $total >= $freeShippingMinimum)
        ? 0
        : $normalShippingFee;

    $grandTotal = round((float) $total + $shippingFee, 2);
    $remainingForFreeShipping = max(
    0,
    round($freeShippingMinimum - (float) $total, 2)
);
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



<style>

:root{--cart-yellow:#ffd43b;--cart-yellow-dark:#d8ad00;--cart-black:#090909;--cart-surface:#121212;--cart-card:#191919;--cart-soft:#222;--cart-border:rgba(255,212,59,.22);--cart-text:#f7f7f7;--cart-muted:#a7a7a7;--cart-danger:#ef4444}

body{background:radial-gradient(circle at top right,rgba(255,212,59,.08),transparent 28%),var(--cart-black);color:var(--cart-text)}

.cart-container{max-width:950px}.cart-header{position:relative;overflow:hidden;padding:1.4rem 1.5rem;margin-bottom:1.5rem;border:1px solid var(--cart-border);border-radius:22px;background:radial-gradient(circle at 88% 20%,rgba(255,212,59,.18),transparent 24%),linear-gradient(135deg,#090909 0%,#171717 60%,#2a2200 100%);box-shadow:0 18px 42px rgba(0,0,0,.35)}

.cart-header::after{content:"";position:absolute;right:-55px;bottom:-95px;width:220px;height:220px;border-radius:50%;background:rgba(255,212,59,.1)}.cart-header-inner{position:relative;z-index:1}.cart-title{margin:0;color:var(--cart-yellow);font-weight:900}.cart-subtitle{margin:.35rem 0 0;color:#c8c8c8;font-size:.86rem}

#cart-item-types{border:1px solid var(--cart-border)!important;background:rgba(255,212,59,.1)!important;color:var(--cart-yellow)!important;font-weight:800}

.product-card{overflow:hidden;border:1px solid #2c2c2c!important;border-radius:20px;background:linear-gradient(180deg,#1b1b1b,#121212);color:var(--cart-text);box-shadow:0 14px 30px rgba(0,0,0,.32)!important;transition:.2s}.product-card:hover{border-color:rgba(255,212,59,.55)!important;transform:translateY(-3px);box-shadow:0 22px 44px rgba(0,0,0,.45)!important}

.product-img-container{position:relative;width:100px;height:100px;overflow:hidden;border:1px solid #333;border-radius:16px;background:#111}.cart-img{width:100%;height:100%;object-fit:cover;transition:.25s}.product-card:hover .cart-img{transform:scale(1.04)}.product-name{color:#fff!important}.text-muted,.text-secondary{color:var(--cart-muted)!important}.text-orange{color:var(--cart-yellow)!important}

.qty-control{display:inline-flex;align-items:center;padding:5px;border:1px solid var(--cart-border);border-radius:50px;background:#111}.btn-qty{width:34px;height:34px;display:flex;align-items:center;justify-content:center;border:1px solid #333;border-radius:50%;background:#1d1d1d;color:var(--cart-yellow);box-shadow:0 3px 8px rgba(0,0,0,.28);transition:.2s}.btn-qty:hover:not(:disabled){border-color:var(--cart-yellow);background:var(--cart-yellow);color:#111;transform:translateY(-1px)}.btn-qty:disabled{opacity:.35;cursor:not-allowed;background:#292929;color:#777;box-shadow:none}.delete-mode{color:#ff6b6b!important}.delete-mode:hover{border-color:var(--cart-danger)!important;background:var(--cart-danger)!important;color:#fff!important}.qty-input{width:46px;border:none;background:transparent;color:#fff;text-align:center;font-size:1.05rem;font-weight:850;outline:none}input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}

.remove-item{color:#ff6b6b!important;transition:.2s}.remove-item:hover{color:#fff!important;transform:scale(1.08)}.btn-orange{border:1px solid var(--cart-yellow);border-radius:13px;background:linear-gradient(135deg,#ffe56b,var(--cart-yellow));color:#111;padding:12px 30px;font-weight:850;transition:.2s;box-shadow:0 8px 20px rgba(255,212,59,.15)}.btn-orange:hover{border-color:#ffea83;background:linear-gradient(135deg,#fff08f,#ffc800);color:#000;transform:translateY(-2px);box-shadow:0 12px 24px rgba(255,212,59,.22)}

.summary-card{border:1px solid var(--cart-border)!important;border-radius:22px;background:radial-gradient(circle at right center,rgba(255,212,59,.1),transparent 30%),linear-gradient(180deg,#181818,#111);color:var(--cart-text);box-shadow:0 18px 40px rgba(0,0,0,.34)!important}.summary-card #cart-total{color:var(--cart-yellow)!important;text-shadow:0 4px 18px rgba(255,212,59,.12)}.alert-danger{border:1px solid rgba(239,68,68,.3)!important;background:rgba(239,68,68,.11)!important;color:#ffb4b4!important}.alert-warning{border:1px solid rgba(255,212,59,.3)!important;background:#211c07!important;color:#ffe992!important}.alert-warning .text-dark,.alert-warning strong{color:var(--cart-yellow)!important}

.empty-cart{padding:3.5rem 1rem;border:1px dashed var(--cart-border);border-radius:22px;background:rgba(18,18,18,.72);text-align:center}.empty-cart-icon{width:82px;height:82px;display:grid;place-items:center;margin:0 auto 1rem;border-radius:24px;background:rgba(255,212,59,.08);color:var(--cart-yellow);font-size:2.2rem}

@media(max-width:576px){.cart-container{padding-top:1rem!important}.cart-header{padding:1.15rem;border-radius:18px}.product-img-container{width:82px;height:82px}.product-card{padding:14px!important}.qty-control{padding:4px}.btn-qty{width:32px;height:32px}}

</style>



<div class="container py-5 cart-container">

    <section class="cart-header">

        <div class="cart-header-inner d-flex align-items-center justify-content-between gap-3">

            <div>

                <h2 class="cart-title"><i class="bi bi-bag-heart-fill me-2"></i>ตะกร้าของฉัน</h2>

                <p class="cart-subtitle">ตรวจสอบสินค้าและจำนวนก่อนดำเนินการชำระเงิน</p>

            </div>

            <span class="badge rounded-pill px-3 py-2" id="cart-item-types">{{ count($cart) }} รายการ</span>

        </div>

    </section>



    @if(session('error'))

        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">

            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}

        </div>

    @endif



    {{-- รายการสินค้า --}}

    <div class="cart-items mb-4">

        @forelse ($cart as $id => $item)

            @php

                $stock = $item['stock'] ?? $item['available_stock'] ?? null;

                $isOverStock = $stock !== null && $item['quantity'] > $stock;

            @endphp

            <div class="card product-card shadow-sm mb-3 p-3 {{ $isOverStock ? 'border border-danger' : '' }}" id="cart-card-{{ $id }}">

                <div class="row align-items-center g-3">

                    {{-- รูปภาพ --}}

                    <div class="col-auto">

                        <div class="product-img-container shadow-sm">

                            @if(!empty($item['image']))

                                <img src="{{ asset('storage/' . $item['image']) }}"&#x20;

                                     alt="{{ $item['name'] }}"&#x20;

                                     class="cart-img"

                                     onerror="this.onerror=null;this.src='https://placehold.co/100x100?text=Error'">

                            @else

                                <img src="https://placehold.co/100x100?text=No+Image" class="cart-img">

                            @endif

                        </div>

                    </div>



                    {{-- รายละเอียด --}}

                    <div class="col">

                        <h6 class="fw-bold mb-1 product-name text-truncate" style="max-width: 200px;">{{ $item['name'] }}</h6>

                        <p class="text-muted small mb-1">

                            <i class="bi bi-tag-fill me-1"></i> {{ $item['unit_name'] ?? 'ไม่มีระบุหน่วย' }}

                        </p>



                        {{-- แสดงคงเหลือในสต็อก --}}

                        @if($stock !== null)

                            <small class="{{ $isOverStock ? 'text-danger fw-bold' : 'text-muted' }} d-block" style="font-size: 0.8rem;" id="stock-info-{{ $id }}">

                                <i class="bi {{ $isOverStock ? 'bi-exclamation-triangle-fill text-danger' : 'bi-box-seam' }} me-1"></i>

                                คงเหลือรวมทุกหน่วย: {{ $stock }} {{ $item['unit_name'] ?? 'หน่วย' }}&#x20;

                                @if($isOverStock)

                                    (เลือกเกินสต็อก)

                                @endif

                            </small>

                        @endif



                        {{-- ราคารวมบน Mobile --}}

                        <div class="d-block d-md-none mt-1">

                            <small class="text-muted d-block" style="font-size: 0.75rem;">ราคารวม</small>

                            <span class="fw-bold text-orange fs-5 item-total-{{ $id }}" data-price="{{ $item['price'] }}">

                                ฿{{ number_format($item['price'] * $item['quantity'], 2) }}

                            </span>

                        </div>

                    </div>



                    {{-- ตัวปรับจำนวน --}}

                    <div class="col-12 col-md-auto">

                        <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-3 gap-md-4">

                            <div class="qty-control">

                                {{-- ปุ่มลบ / ลดจำนวน --}}

                                <button type="button"

                                    class="btn-qty shadow-sm update-quantity {{ $item['quantity'] <= 1 ? 'delete-mode' : '' }}"

                                    data-id="{{ $id }}"

                                    data-action="decrease">

                                    <i class="bi {{ $item['quantity'] <= 1 ? 'bi-trash3-fill' : 'bi-dash' }}"></i>

                                </button>



                                {{-- จำนวนสินค้า + แนบค่า stock ใน data-stock --}}

                                <input type="number"

                                    class="qty-input"

                                    id="quantity-{{ $id }}"

                                    value="{{ $item['quantity'] }}"

                                    data-stock="{{ $stock ?? '' }}"

                                    readonly>



                                {{-- เพิ่มจำนวน --}}

                                <button type="button"

                                    class="btn-qty shadow-sm update-quantity"

                                    data-id="{{ $id }}"

                                    data-action="increase"

                                    {{ $stock !== null && $item['quantity'] >= $stock ? 'disabled' : '' }}>

                                    <i class="bi bi-plus"></i>

                                </button>

                            </div>



                            {{-- ราคารวมบน Desktop --}}

                            <div class="text-end d-none d-md-block" style="min-width: 100px;">

                                <small class="text-muted d-block">ราคารวม</small>

                                <span class="fw-bold text-orange fs-5 item-total-{{ $id }}" data-price="{{ $item['price'] }}">

                                    ฿{{ number_format($item['price'] * $item['quantity'], 2) }}

                                </span>

                            </div>



                            <button type="button"

                                class="btn btn-link text-danger p-2 remove-item"

                                data-id="{{ $id }}"

                                title="ลบสินค้า">

                                <i class="bi bi-trash3-fill fs-5"></i>

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-cart">

                <div class="empty-cart-icon"><i class="bi bi-cart-x"></i></div>

                <h4 class="text-white fw-bold">ตะกร้าว่างเปล่า</h4>

                <p class="text-muted mb-0">ยังไม่มีสินค้าอยู่ในตะกร้าของคุณ</p>

                <a href="{{ url('/online/pos') }}" class="btn btn-orange mt-3 px-5 rounded-pill"><i class="bi bi-shop me-1"></i> ไปเลือกซื้อสินค้า</a>

            </div>

        @endforelse

    </div>



    {{-- ส่วนสรุปยอด --}}

    @if(!empty($cart))

        @if (!empty($outOfStockItems))

            <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4">

                <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-exclamation-triangle-fill me-2"></i>สินค้าไม่เพียงพอ</h6>

                <ul class="mb-0 small">

                    @foreach ($outOfStockItems as $item)

                        <li><strong>{{ $item['name'] }}</strong>: เหลือเพียง {{ $item['available'] }} (ต้องการ {{ $item['requested'] }})</li>

                    @endforeach

                </ul>

            </div>

        @endif



        <div

    class="card summary-card shadow-sm p-4"

    id="summary-section"

    data-shipping-fee="{{ number_format($normalShippingFee, 2, '.', '') }}"

    data-free-shipping-enabled="{{ $freeShippingEnabled ? 1 : 0 }}"

    data-free-shipping-minimum="{{ $freeShippingMinimum }}"

>

            <div class="row align-items-center g-4">

                <div class="col-md-6">

                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted">ยอดรวมสินค้า</span>

                        <strong id="cart-subtotal">

                            ฿{{ number_format($total, 2) }}

                        </strong>

                    </div>



                    <div class="d-flex justify-content-between mb-2">
    <span class="text-muted">
        <i class="bi bi-truck me-1"></i>
        ค่าจัดส่ง
    </span>

    <strong id="shipping-fee">
        @if ($shippingFee > 0)
            ฿{{ number_format($shippingFee, 2) }}
        @else
            ฟรี
        @endif
    </strong>
</div>

{{-- แนะนำยอดซื้อเพื่อรับสิทธิ์ส่งฟรี --}}
@if ($freeShippingEnabled)
    <div id="free-shipping-promotion" class="mb-3">

        <div id="free-shipping-message"
             class="small fw-bold mb-2"
             style="color: var(--cart-yellow);">

            @if ($remainingForFreeShipping > 0)
                <i class="bi bi-truck me-1"></i>
                ซื้อเพิ่มอีก ฿{{ number_format($remainingForFreeShipping, 2) }}
                รับสิทธิ์จัดส่งฟรี!
            @else
                <i class="bi bi-check-circle-fill me-1"></i>
                ยินดีด้วย! คุณได้รับสิทธิ์จัดส่งฟรีแล้ว
            @endif

        </div>

        <div class="progress"
             style="height: 9px; background: #333; border-radius: 20px;">

            <div id="free-shipping-progress"
                 class="progress-bar"
                 role="progressbar"
                 style="
                    width: {{ $freeShippingMinimum > 0
                        ? min(100, ($total / $freeShippingMinimum) * 100)
                        : 100 }}%;
                    background: var(--cart-yellow);
                    border-radius: 20px;
                    transition: width .3s ease;
                 ">
            </div>

        </div>

        <small class="text-muted mt-1 d-block" id="free-shipping-target">
            ซื้อครบ ฿{{ number_format($freeShippingMinimum, 2) }}
            รับสิทธิ์จัดส่งฟรี
        </small>

    </div>
@endif



                    <hr class="border-secondary">



                    <div class="d-flex justify-content-between align-items-end">

                        <span class="fw-bold">ยอดชำระทั้งหมด</span>



                        <h2 class="fw-bold text-orange mb-0" id="cart-total">

                            ฿{{ number_format($grandTotal, 2) }}

                        </h2>

                    </div>

                </div>



                <div class="col-md-6 text-md-end">

                    @if (empty($outOfStockItems) && $total > 0)

                        <a

                            href="{{ route('online.checkout') }}"

                            id="checkout-btn"

                            class="btn btn-orange btn-lg w-100 w-md-auto shadow"

                        >

                            <i class="bi bi-wallet2 me-2"></i>

                            ชำระเงินตอนนี้

                        </a>

                    @else

                        <button

                            class="btn btn-secondary btn-lg w-100 w-md-auto"

                            disabled

                        >

                            {{ !empty($outOfStockItems)

                                ? 'สต็อกสินค้าไม่พอ'

                                : 'ตะกร้าว่างเปล่า' }}

                        </button>

                    @endif

                </div>

            </div>

        </div>

    @endif

</div>



<script>

    const updateQueues = {};

    let activeRequests = 0; // ตัวนับ Request ที่กำลังบันทึกหลังบ้าน



    // ดึง CSRF Token ปลอดภัยชัวร์

    function getCsrfToken() {

        const meta = document.querySelector('meta[name="csrf-token"]');

        return meta ? meta.getAttribute('content') : '{{ csrf_token() }}';

    }

    function calculateShipping(subtotal) {
        const summarySection = document.getElementById('summary-section');
        if (!summarySection) return 0;

        const normalFee = Math.max(0, Number(summarySection.dataset.shippingFee) || 0);
        const enabled = summarySection.dataset.freeShippingEnabled === '1';
        const minimum = Math.max(0, Number(summarySection.dataset.freeShippingMinimum) || 0);

        return enabled && subtotal >= minimum ? 0 : normalFee;
    }

    // อัปเดตยอดจากค่าที่ Backend ส่งมาได้ หรือคำนวณจากค่าตั้งต้นในหน้าเว็บ
    function updateCartSummary(
    subtotal,
    serverShippingFee = null,
    serverGrandTotal = null
) {
    const summarySection = document.getElementById('summary-section');

    const shippingFee = serverShippingFee !== null &&
        Number.isFinite(Number(serverShippingFee))
        ? Math.max(0, Number(serverShippingFee))
        : calculateShipping(subtotal);

    const grandTotal = serverGrandTotal !== null &&
        Number.isFinite(Number(serverGrandTotal))
        ? Number(serverGrandTotal)
        : subtotal + shippingFee;

    const cartSubtotal = document.getElementById('cart-subtotal');
    const shippingFeeElement = document.getElementById('shipping-fee');
    const cartTotal = document.getElementById('cart-total');

    if (cartSubtotal) {
        cartSubtotal.textContent = `฿${subtotal.toFixed(2)}`;
    }

    if (shippingFeeElement) {
        shippingFeeElement.textContent =
            shippingFee > 0
                ? `฿${shippingFee.toFixed(2)}`
                : 'ฟรี';
    }

    if (cartTotal) {
        cartTotal.textContent = `฿${grandTotal.toFixed(2)}`;
    }

    // อัปเดตข้อความแนะนำส่งฟรี
    if (summarySection) {
        const enabled =
            summarySection.dataset.freeShippingEnabled === '1';

        const minimum = Math.max(
            0,
            Number(summarySection.dataset.freeShippingMinimum) || 0
        );

        const message = document.getElementById('free-shipping-message');
        const progress = document.getElementById('free-shipping-progress');
        const target = document.getElementById('free-shipping-target');

        if (enabled && message && progress && target) {

            const remaining = Math.max(0, minimum - subtotal);

            const percentage = minimum > 0
                ? Math.min(100, (subtotal / minimum) * 100)
                : 100;

            progress.style.width = `${percentage}%`;

            if (remaining > 0) {
                message.innerHTML = `
                    <i class="bi bi-truck me-1"></i>
                    ซื้อเพิ่มอีก ฿${remaining.toFixed(2)}
                    รับสิทธิ์จัดส่งฟรี!
                `;

                target.textContent =
                    `ซื้อครบ ฿${minimum.toFixed(2)} รับสิทธิ์จัดส่งฟรี`;

            } else {
                message.innerHTML = `
                    <i class="bi bi-check-circle-fill me-1"></i>
                    ยินดีด้วย! คุณได้รับสิทธิ์จัดส่งฟรีแล้ว
                `;

                target.textContent = 'คุณได้รับสิทธิ์จัดส่งฟรีแล้ว';
            }
        }
    }

    return shippingFee;
}
    // ล็อคปุ่มชำระเงินชั่วคราว เพื่อป้องกันการกดไปหน้า Checkout ก่อน Session บันทึกเสร็จ

    function setCheckoutButtonState(isSaving) {

        const checkoutBtn = document.getElementById('checkout-btn');

        if (!checkoutBtn) return;



        if (isSaving) {

            checkoutBtn.classList.add('disabled');

            checkoutBtn.style.pointerEvents = 'none';

            checkoutBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> กำลังบันทึก...`;

        } else {

            checkoutBtn.classList.remove('disabled');

            checkoutBtn.style.pointerEvents = 'auto';

            checkoutBtn.innerHTML = `<i class="bi bi-wallet2 me-2"></i> ชำระเงินตอนนี้`;

        }

    }



    // ฟังก์ชันคำนวณและอัปเดต Navbar + สรุปยอดบนหน้าเว็บ

    function refreshCartUI() {

        let newCartTotal = 0;

        let totalQuantity = 0;



        document.querySelectorAll('.qty-input').forEach(function (input) {

            const itemId = input.id.replace('quantity-', '');

            const itemTotalEl = document.querySelector(`.item-total-${itemId}`);

            const qty = parseInt(input.value) || 0;



            if (itemTotalEl) {

                const itemPrice = parseFloat(itemTotalEl.dataset.price);

                newCartTotal += itemPrice * qty;

            }

            totalQuantity += qty;

        });
        updateCartSummary(newCartTotal);




        document.querySelectorAll('.cart-total-items').forEach(element => {

            element.textContent = totalQuantity;

        });

    }



    document.addEventListener('click', function (event) {

        const button = event.target.closest('.update-quantity');

        if (!button || button.disabled) return;



        const id = button.dataset.id;

        const action = button.dataset.action;

        const quantityInput = document.getElementById(`quantity-${id}`);

        const maxStockAttr = quantityInput.dataset.stock;

        const maxStock = maxStockAttr !== "" && maxStockAttr !== null ? parseInt(maxStockAttr) : null;



        if (!updateQueues[id]) {

            updateQueues[id] = {

                quantity: parseInt(quantityInput.value),

                processing: false,

                lastSavedQuantity: parseInt(quantityInput.value)

            };

        }



        const currentQuantity = updateQueues[id].quantity;



        // กดลดตอนมี 1 ชิ้น -> เรียกปุ่มลบสินค้า

        if (action === 'decrease' && currentQuantity === 1) {

            const removeButton = document.querySelector(`.remove-item[data-id="${id}"]`);

            if (removeButton) removeButton.click();

            return;

        }



        // เช็คการเพิ่มสินค้าเกินสต็อก

        if (action === 'increase') {

            if (maxStock !== null && !isNaN(maxStock) && currentQuantity >= maxStock) {

                showStockWarning(`สินค้าในสต็อกมีเหลือเพียง ${maxStock} ชิ้น`);

                return;

            }

        }



        let newQuantity;



        if (action === 'increase') {

            newQuantity = currentQuantity + 1;

        } else {

            /*

            |--------------------------------------------------------------------------

            | แก้กรณีจำนวนในตะกร้าเกินสต็อก

            |--------------------------------------------------------------------------

            | เดิมลดทีละ 1 แล้ว Backend ปฏิเสธ เพราะจำนวนใหม่ยังเกินสต็อก

            | จึงลดลงมาเท่ากับสต็อกทันที เพื่อให้บันทึกได้

            */

            if (

                maxStock !== null &&

                !isNaN(maxStock) &&

                currentQuantity > maxStock

            ) {

                if (maxStock <= 0) {

                    const removeButton = document.querySelector(

                        `.remove-item[data-id="${id}"]`

                    );



                    if (removeButton) {

                        removeButton.click();

                    }



                    return;

                }



                newQuantity = maxStock;

            } else {

                newQuantity = currentQuantity - 1;

            }

        }



        if (newQuantity < 1) return;



        // อัปเดตค่าใน Queue และ Input หน้าเว็บ

        updateQueues[id].quantity = newQuantity;

        quantityInput.value = newQuantity;



        // อัปเดตราคารวมของชิ้นนี้

        const itemTotals = document.querySelectorAll(`.item-total-${id}`);

        if (itemTotals.length > 0) {

            const price = parseFloat(itemTotals[0].dataset.price);

            const calculatedTotal = `฿${(price * newQuantity).toFixed(2)}`;

            itemTotals.forEach(el => el.innerHTML = calculatedTotal);

        }



        updateButtonsState(id, newQuantity, maxStock);

        refreshCartUI();



        // ส่งข้อมูลไปอัปเดตที่ Backend

        processUpdate(id);

    });



    // แสดงแจ้งเตือนเรื่องสต็อกสินค้า

    function showStockWarning(message) {

        if (typeof Swal !== 'undefined') {

            Swal.fire({

                icon: 'warning',

                title: 'สินค้าไม่เพียงพอ',

                text: message,

                toast: true,

                position: 'top-end',

                showConfirmButton: false,

                timer: 2500

            });

        } else {

            alert(message);

        }

    }



    // อัปเดตสถานะปุ่ม เพิ่ม/ลด ชนิด Real-time

    function updateButtonsState(id, quantity, maxStock) {

        // ปุ่มลด

        const decreaseButton = document.querySelector(`.update-quantity[data-id="${id}"][data-action="decrease"]`);

        if (decreaseButton) {

            const icon = decreaseButton.querySelector('i');

            if (quantity <= 1) {

                icon.className = 'bi bi-trash3-fill';

                decreaseButton.classList.add('delete-mode');

            } else {

                icon.className = 'bi bi-dash';

                decreaseButton.classList.remove('delete-mode');

            }

        }



        // ปุ่มเพิ่ม

        const increaseButton = document.querySelector(`.update-quantity[data-id="${id}"][data-action="increase"]`);

        if (increaseButton && maxStock !== null && !isNaN(maxStock)) {

            if (quantity >= maxStock) {

                increaseButton.disabled = true;

            } else {

                increaseButton.disabled = false;

            }

        }

    }



    async function processUpdate(id) {

        const queue = updateQueues[id];

        if (queue.processing) return;



        queue.processing = true;

        activeRequests++;

        setCheckoutButtonState(true);



        const targetQuantity = queue.quantity;



        try {

            const response = await fetch(`/online/cart/update-quantity/${id}`, {

                method: 'PUT',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': getCsrfToken(),

                    'Accept': 'application/json'

                },

                body: JSON.stringify({ quantity: targetQuantity })

            });



            if (!response.ok) {

                const errJson = await response.json().catch(() => ({}));

                throw new Error(errJson.message || 'สินค้าในสต็อกมีไม่เพียงพอ');

            }



            const data = await response.json();

            if (!data.success) {

                throw new Error(data.message || 'ไม่สามารถอัปเดตจำนวนสินค้าได้');

            }



            // บันทึกสำเร็จ

            queue.lastSavedQuantity = data.quantity;



            const quantityInput = document.getElementById(`quantity-${id}`);

            if (queue.quantity === targetQuantity && quantityInput) {

                quantityInput.value = data.quantity;



                document.querySelectorAll(`.item-total-${id}`).forEach(el => {

                    el.innerHTML = `฿${data.item_total}`;

                });



                const subtotal = parseFloat(String(data.total ?? 0).replace(/,/g, '')) || 0;
                updateCartSummary(
                    subtotal,
                    data.shipping_fee !== undefined ? data.shipping_fee : null,
                    data.grand_total !== undefined ? data.grand_total : null
                );



                const maxStock = quantityInput.dataset.stock ? parseInt(quantityInput.dataset.stock) : null;

                updateButtonsState(id, data.quantity, maxStock);

            }



            if (data.total_items !== undefined) {

                document.querySelectorAll('.cart-total-items').forEach(element => {

                    element.textContent = data.total_items;

                });

            }



            /*

            |--------------------------------------------------------------------------

            | รีเฟรชสถานะชำระเงินเมื่อแก้จำนวนสินค้าที่เคยเกินสต็อก

            |--------------------------------------------------------------------------

            | ปุ่มชำระเงินถูกสร้างจาก Blade ฝั่งเซิร์ฟเวอร์ ถ้าตอนเปิดหน้า

            | มีสินค้าเกินสต็อก ปุ่มจะเป็น disabled และไม่มี #checkout-btn

            | เมื่อแก้จำนวนสำเร็จจึงต้องโหลดหน้าใหม่เพื่อคำนวณสถานะอีกครั้ง

            */

            const hasStockWarning = document.querySelector('.alert-warning');

            const checkoutButton = document.getElementById('checkout-btn');



            if (hasStockWarning || !checkoutButton) {

                window.location.reload();

                return;

            }



        } catch (error) {

            console.error('Cart Update Error:', error);



            // เมื่อเกิดข้อผิดพลาด คืนค่าตัวเลขเดิมกลับหน้าจอ

            queue.quantity = queue.lastSavedQuantity;

            const quantityInput = document.getElementById(`quantity-${id}`);

            if (quantityInput) {

                quantityInput.value = queue.lastSavedQuantity;

                const maxStock = quantityInput.dataset.stock ? parseInt(quantityInput.dataset.stock) : null;

                updateButtonsState(id, queue.lastSavedQuantity, maxStock);

            }



            refreshCartUI();

            showStockWarning(error.message || 'ระบบไม่สามารถบันทึกข้อมูลได้');

        } finally {

            queue.processing = false;

            activeRequests--;



            if (activeRequests <= 0) {

                activeRequests = 0;

                setCheckoutButtonState(false);

            }



            // ถ้าผู้ใช้กดซ้ำระหว่างที่กำลังส่ง Request ให้ทำงานซ้ำจนกว่าจะตรงกับค่าล่าสุด

            if (queue.quantity !== targetQuantity) {

                processUpdate(id);

            }

        }

    }



    // ลบสินค้า

    document.addEventListener('click', function (event) {

        const button = event.target.closest('.remove-item');

        if (!button) return;



        const id = button.dataset.id;



        const confirmRemove = () => {

            fetch(`/online/cart/remove/${id}`, {

                method: 'DELETE',

                headers: {

                    'X-CSRF-TOKEN': getCsrfToken(),

                    'Accept': 'application/json'

                }

            })

            .then(async response => {

                const data = await response.json().catch(() => ({}));



                if (!response.ok) {

                    throw new Error(data.message || 'ไม่สามารถลบสินค้าได้');

                }



                return data;

            })

            .then(function (data) {

                if (!data.success) {

                    throw new Error(data.message || 'ไม่สามารถลบสินค้าได้');

                }



                const card = document.getElementById(`cart-card-${id}`);

                if (card) card.remove();

                const itemTypesBadge = document.getElementById('cart-item-types');
                if (itemTypesBadge) {
                    const remainingCards = document.querySelectorAll('.product-card[id^=\"cart-card-\"]').length;
                    itemTypesBadge.textContent = `${remainingCards} รายการ`;
                }

                delete updateQueues[id];



                document.querySelectorAll('.cart-total-items').forEach(element => {

                    element.textContent = data.total_items;

                });



                const subtotal = parseFloat(String(data.total ?? 0).replace(/,/g, '')) || 0;
                updateCartSummary(
                    subtotal,
                    data.shipping_fee !== undefined ? data.shipping_fee : null,
                    data.grand_total !== undefined ? data.grand_total : null
                );



                /*

                |--------------------------------------------------------------------------

                | โหลดหน้าใหม่หลังลบสินค้า

                |--------------------------------------------------------------------------

                | เพื่อให้ Laravel คำนวณ $outOfStockItems และสร้างปุ่มชำระเงินใหม่

                | ป้องกันกรณีลบสินค้าที่เกินสต็อกแล้วปุ่มยังคง disabled

                */

                if (typeof Swal !== 'undefined') {

                    Swal.fire({

                        icon: 'success',

                        title: 'ลบสินค้าแล้ว',

                        showConfirmButton: false,

                        timer: 700

                    }).then(() => {

                        window.location.reload();

                    });

                } else {

                    window.location.reload();

                }

            })

            .catch(function (error) {

                console.error('Remove cart item error:', error);



                if (typeof Swal !== 'undefined') {

                    Swal.fire({

                        icon: 'error',

                        title: 'ลบสินค้าไม่สำเร็จ',

                        text: error.message

                    });

                } else {

                    alert(error.message);

                }

            });

        };



        if (typeof Swal === 'undefined') {

            if (confirm('ต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?')) {

                confirmRemove();

            }



            return;

        }



        Swal.fire({

            title: 'ลบสินค้านี้?',

            text: 'สินค้าจะถูกลบออกจากตะกร้า',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#ffd43b',

            cancelButtonColor: '#333333',

            confirmButtonText: 'ลบสินค้า',

            cancelButtonText: 'ยกเลิก',

            background: '#171717',

            color: '#f7f7f7',

            iconColor: '#ffd43b'

        }).then(function (result) {

            if (!result.isConfirmed) return;

            confirmRemove();

        });

    });

</script>

@endsection
