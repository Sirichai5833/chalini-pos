@extends('layouts.online')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    :root {
        --primary-orange: #ff5722;
        --soft-orange: #fff4f1;
        --dark-orange: #e64a19;
    }

    body {
        background-color: #f8f9fa;
    }

    .cart-container {
        max-width: 900px;
    }

    /* สไตล์การ์ดสินค้า */
    .product-card {
        background: white;
        border: none;
        border-radius: 20px;
        transition: transform 0.2s ease, shadow 0.2s ease;
    }

    .product-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }

    .product-img-container {
        position: relative;
        width: 100px;
        height: 100px;
    }

    .cart-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 15px;
    }

    /* ปุ่มปรับจำนวนแบบใหม่ */
    .qty-control {
        background: var(--soft-orange);
        border-radius: 50px;
        padding: 5px;
        display: inline-flex;
        align-items: center;
    }

    .delete-mode {
        color: #dc3545 !important;
    }

    .delete-mode:hover {
        background: #dc3545 !important;
        color: white !important;
    }

    .btn-qty {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: white;
        color: var(--primary-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-qty:hover:not(:disabled) {
        background: var(--primary-orange);
        color: white;
    }

    .btn-qty:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #e0e0e0;
        color: #888;
        box-shadow: none;
    }

    .qty-input {
        width: 45px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* ซ่อนลูกศร input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .btn-orange {
        background: var(--primary-orange);
        color: white;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-orange:hover {
        background: var(--dark-orange);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 87, 34, 0.3);
    }

    .summary-card {
        background: white;
        border-radius: 20px;
        border: 2px solid var(--soft-orange);
    }

    /* ปรับแต่งสำหรับ Mobile */
    @media (max-width: 576px) {
        .product-img-container {
            width: 80px;
            height: 80px;
        }
        .product-card {
            padding: 15px !important;
        }
    }
</style>

<div class="container py-5 cart-container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0">
            <span class="text-orange"><i class="bi bi-bag-heart-fill me-2"></i>ตะกร้าของฉัน</span>
        </h2>
        <span class="badge bg-white text-dark border rounded-pill px-3 py-2 shadow-sm" id="cart-item-types">
            {{ count($cart) }} รายการ
        </span>
    </div>

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
                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="cart-img"
                                     onerror="this.src='https://placehold.co/100x100?text=Error'">
                            @else
                                <img src="https://placehold.co/100x100?text=No+Image" class="cart-img">
                            @endif
                        </div>
                    </div>

                    {{-- รายละเอียด --}}
                    <div class="col">
                        <h6 class="fw-bold mb-1 text-dark text-truncate" style="max-width: 200px;">{{ $item['name'] }}</h6>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-tag-fill me-1"></i> {{ $item['unit_name'] ?? 'ไม่มีระบุหน่วย' }}
                        </p>

                        {{-- แสดงคงเหลือในสต็อก --}}
                        @if($stock !== null)
                            <small class="{{ $isOverStock ? 'text-danger fw-bold' : 'text-muted' }} d-block" style="font-size: 0.8rem;" id="stock-info-{{ $id }}">
                                <i class="bi {{ $isOverStock ? 'bi-exclamation-triangle-fill text-danger' : 'bi-box-seam' }} me-1"></i>
                                คงเหลือ: {{ $stock }} ชิ้น 
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
            <div class="text-center py-5">
                <i class="bi bi-cart-x text-light-emphasis" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">ตะกร้าว่างเปล่า</h4>
                <a href="{{ url('/') }}" class="btn btn-orange mt-3 px-5 rounded-pill">ไปเลือกซื้อสินค้า</a>
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

        <div class="card summary-card shadow-sm p-4" id="summary-section">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted mb-1">ยอดรวมสุทธิ</p>
                    <h2 class="fw-bold text-orange mb-0" id="cart-total">
                        ฿{{ number_format($total, 2) }}
                    </h2>
                </div>
                <div class="col-md-6 text-md-end">
                    @if (empty($outOfStockItems) && $total > 0)
                        <a href="{{ route('online.checkout') }}" id="checkout-btn" class="btn btn-orange btn-lg w-100 w-md-auto shadow">
                            <i class="bi bi-wallet2 me-2"></i> ชำระเงินตอนนี้
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg w-100 w-md-auto" disabled>
                            {{ !empty($outOfStockItems) ? 'สต็อกสินค้าไม่พอ' : 'ตะกร้าว่างเปล่า' }}
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

        const cartTotal = document.getElementById('cart-total');
        if (cartTotal) {
            cartTotal.innerHTML = `฿${newCartTotal.toFixed(2)}`;
        }

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

        const newQuantity = action === 'increase' ? currentQuantity + 1 : currentQuantity - 1;
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

                const cartTotal = document.getElementById('cart-total');
                if (cartTotal) cartTotal.innerHTML = `฿${data.total}`;

                const maxStock = quantityInput.dataset.stock ? parseInt(quantityInput.dataset.stock) : null;
                updateButtonsState(id, data.quantity, maxStock);
            }

            if (data.total_items !== undefined) {
                document.querySelectorAll('.cart-total-items').forEach(element => {
                    element.textContent = data.total_items;
                });
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

        Swal.fire({
            title: 'ลบสินค้านี้?',
            text: 'สินค้าจะถูกลบออกจากตะกร้า',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff5722',
            confirmButtonText: 'ลบสินค้า',
            cancelButtonText: 'ยกเลิก'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            fetch(`/online/cart/remove/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(function (data) {
                if (!data.success) return;

                const card = document.getElementById(`cart-card-${id}`);
                if (card) card.remove();

                delete updateQueues[id];

                document.querySelectorAll('.cart-total-items').forEach(element => {
                    element.textContent = data.total_items;
                });

                const cartTotal = document.getElementById('cart-total');
                if (cartTotal) {
                    cartTotal.innerHTML = `฿${data.total}`;
                }

                const remainingCards = document.querySelectorAll('.product-card');
                if (remainingCards.length === 0) {
                    location.reload();
                } else {
                    const itemTypesBadge = document.getElementById('cart-item-types');
                    if (itemTypesBadge) {
                        itemTypesBadge.textContent = `${remainingCards.length} รายการ`;
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'ลบสินค้าแล้ว',
                    showConfirmButton: false,
                    timer: 1000
                });
            })
            .catch(function (error) {
                console.error(error);
            });
        });
    });
</script>
@endsection