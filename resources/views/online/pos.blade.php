@extends('layouts.online')

@section('content')
    @if (session('success'))
        <x-sweet-alert icon="success" title="สำเร็จ!" text="{{ session('success') }}" confirm-button-text="ตกลง" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="ผิดพลาด" text="{{ session('error') }}" confirm-button-text="ตกลง" />
    @endif

    <style>
        :root {
            --primary-color: #f5c400;
            --primary-dark: #c99f00;
            --accent-color: #ffd43b;
            --accent-hover: #ffbf00;
            --danger-color: #ef4444;
            --dark-bg: #090909;
            --dark-surface: #121212;
            --dark-card: #181818;
            --dark-soft: #222222;
            --text-light: #f8f8f8;
            --text-muted: #a9a9a9;
            --border-color: rgba(255, 212, 59, .22);
            --yellow-shadow: 0 16px 38px rgba(245, 196, 0, .10);
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(245, 196, 0, .07), transparent 28%),
                var(--dark-bg);
            color: var(--text-light);
            padding-top: 70px;
        }

        .navbar {
            background: rgba(8, 8, 8, .96) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .35);
            backdrop-filter: blur(12px);
        }

        .navbar-brand,
        .nav-link,
        .btn-link {
            color: var(--text-light) !important;
        }

        .navbar-brand:hover,
        .nav-link:hover,
        .nav-link.active {
            color: var(--accent-color) !important;
        }

        .container-fluid.py-5.mb-3 {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color) !important;
            border-bottom: 3px solid var(--primary-color) !important;
            border-radius: 0 0 32px 32px !important;
            background:
                radial-gradient(circle at 86% 20%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #080808 0%, #171717 58%, #2b2300 100%) !important;
            box-shadow: var(--yellow-shadow);
        }

        .container-fluid.py-5.mb-3::after {
            content: "";
            position: absolute;
            right: -80px;
            bottom: -115px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(245, 196, 0, .13);
            pointer-events: none;
        }

        .container-fluid.py-5.mb-3 h2 {
            position: relative;
            z-index: 1;
            color: var(--accent-color) !important;
            text-shadow: 0 4px 18px rgba(0, 0, 0, .65);
        }

        .container-fluid.py-5.mb-3 p {
            position: relative;
            z-index: 1;
            color: #dedede !important;
        }

        #category-menu {
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            background: var(--dark-surface);
            box-shadow: var(--yellow-shadow);
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) var(--dark-soft);
        }

        #category-menu::-webkit-scrollbar {
            height: 6px;
        }

        #category-menu::-webkit-scrollbar-track {
            background: var(--dark-soft);
            border-radius: 20px;
        }

        #category-menu::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 20px;
        }

        .category-link {
            flex: none !important;
            padding: .55rem .9rem !important;
            border: 1px solid #444 !important;
            border-radius: 999px !important;
            background: #1a1a1a !important;
            color: #d7d7d7 !important;
            font-weight: 750 !important;
            scroll-snap-align: start;
            text-decoration: none;
            transition: .18s ease;
        }

        .category-link.btn-orange,
        .category-link:hover {
            border-color: var(--primary-color) !important;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color)) !important;
            color: #111 !important;
            transform: translateY(-2px);
            box-shadow: 0 7px 18px rgba(245, 196, 0, .20);
        }

        form[action*="online"] .form-control {
            min-height: 48px;
            border: 1px solid #383838;
            border-radius: 14px;
            background: #141414;
            color: var(--text-light);
            box-shadow: none;
        }

        form[action*="online"] .form-control::placeholder {
            color: #777;
        }

        form[action*="online"] .form-control:focus {
            border-color: var(--primary-color);
            background: #171717;
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(245, 196, 0, .13);
        }

        .btn-orange,
        .btn-primary {
            border-color: var(--primary-color) !important;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color)) !important;
            color: #111 !important;
            font-weight: 850;
            box-shadow: 0 7px 18px rgba(245, 196, 0, .15);
        }

        .btn-orange:hover,
        .btn-primary:hover {
            border-color: var(--accent-hover) !important;
            background: linear-gradient(135deg, #ffe46b, var(--accent-hover)) !important;
            color: #000 !important;
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border-color: #454545 !important;
            background: #171717 !important;
            color: #ddd !important;
        }

        .btn-outline-secondary:hover {
            border-color: var(--primary-color) !important;
            background: var(--primary-color) !important;
            color: #111 !important;
        }

        #product-list .row {
            row-gap: 1rem !important;
        }

        #product-list .card {
            overflow: hidden;
            border: 1px solid #292929 !important;
            border-radius: 20px !important;
            background: linear-gradient(180deg, #1a1a1a, #131313);
            color: var(--text-light);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .34) !important;
            transition: transform .2s, box-shadow .2s, border-color .2s;
            contain: content;
        }

        #product-list .card:hover {
            border-color: rgba(255, 212, 59, .62) !important;
            transform: translateY(-5px);
            box-shadow: 0 22px 48px rgba(0, 0, 0, .5), 0 0 22px rgba(245, 196, 0, .08) !important;
        }

        .product-image-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1 / .76;
            border-bottom: 1px solid #292929;
            background: linear-gradient(135deg, #202020, #101010);
        }

        .product-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s, filter .3s;
        }

        .card:hover .product-image-wrap img {
            transform: scale(1.035);
            filter: brightness(1.04);
        }

        .product-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: #666;
            font-size: 2rem;
        }

        .card .card-body {
            padding: .95rem;
        }

        .card .card-title {
            display: -webkit-box;
            overflow: hidden;
            min-height: 2.7em;
            margin: 0;
            color: #fff;
            font-size: .98rem;
            font-weight: 850;
            line-height: 1.35;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .card .price,
        .text-orange {
            color: var(--accent-color) !important;
            font-weight: 900;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .card-out-of-stock {
            opacity: .58;
            filter: grayscale(.35);
        }

        .badge-low-stock,
        .badge-out-of-stock {
            z-index: 2;
            border-radius: 999px !important;
            padding: .38rem .6rem !important;
            font-size: .68rem !important;
            font-weight: 850 !important;
            box-shadow: 0 5px 14px rgba(0, 0, 0, .38);
        }

        .badge-out-of-stock {
            background: var(--danger-color);
            color: #fff;
        }

        .badge-low-stock {
            background: var(--accent-color);
            color: #111;
        }

        .add-to-cart {
            cursor: pointer;
        }

        .add-to-cart-form .btn {
            min-height: 43px;
            border-radius: 12px !important;
            font-weight: 850;
        }

        .add-to-cart-form .btn-secondary {
            border-color: #3b3b3b;
            background: #292929;
            color: #999;
        }

        .pagination {
            --bs-pagination-color: var(--accent-color);
            --bs-pagination-bg: #171717;
            --bs-pagination-border-color: #333;
            --bs-pagination-hover-color: #111;
            --bs-pagination-hover-bg: var(--accent-color);
            --bs-pagination-hover-border-color: var(--accent-color);
            --bs-pagination-active-color: #111;
            --bs-pagination-active-bg: var(--primary-color);
            --bs-pagination-active-border-color: var(--primary-color);
            --bs-pagination-disabled-color: #666;
            --bs-pagination-disabled-bg: #111;
            --bs-pagination-disabled-border-color: #2a2a2a;
            font-size: 1rem;
        }

        .pagination svg {
            width: 1em;
            height: 1em;
        }

        .pagination .page-link {
            padding: .5rem .75rem;
            font-size: 1rem;
            box-shadow: none;
        }

        .mobile-fixed-bottom-bar {
            background: #101010;
            border-top: 1px solid var(--border-color);
            box-shadow: 0 -8px 22px rgba(0, 0, 0, .35);
        }

        .mobile-fixed-bottom-bar a {
            color: #c5c5c5;
        }

        .mobile-fixed-bottom-bar a.active,
        .mobile-fixed-bottom-bar a:hover {
            color: var(--accent-color);
        }

        .bg-warning.bg-opacity-25 {
            border: 1px solid rgba(245, 196, 0, .28);
            background: #211d08 !important;
            color: #ffe46b;
        }

        .bg-warning.bg-opacity-25 .text-danger {
            color: #ffe46b !important;
        }

        /* Product Image Gallery */
        .product-image-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1 / .76;
            border-bottom: 1px solid #292929;
            background: linear-gradient(135deg, #202020, #101010);
        }

        .product-image-wrap .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity .2s ease;
        }

        .product-image-wrap .gallery-image[hidden] {
            display: none !important;
        }

       .gallery-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 35px;
    height: 50px;
    border: none;
    background: rgba(0, 0, 0, .45);
    color: #ffd43b;
    font-size: 25px;
    font-weight: bold;
    cursor: pointer;
}

.gallery-control:hover {
    background: rgba(0, 0, 0, .8);
    color: white;
}

.gallery-prev { left: 5px; }
.gallery-next { right: 5px; }

        .gallery-counter {
            position: absolute;
            right: 10px;
            bottom: 10px;
            z-index: 3;
            padding: 3px 10px;
            border-radius: 20px;
            background: rgba(0, 0, 0, .75);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .container-fluid.py-5.mb-3 {
                border-radius: 0 0 22px 22px !important;
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            #product-list .row {
                --bs-gutter-x: .7rem;
            }

            .card .card-body {
                padding: .75rem;
            }

            .card .card-title {
                font-size: .88rem;
            }

            .card .price {
                font-size: 1.05rem !important;
            }
        }
    </style>


    @php
        $onlineOpenTime = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'open_time')->value('value');

        $onlineCloseTime = \Illuminate\Support\Facades\DB::table('settings')
            ->where('key', 'close_time')
            ->value('value');

        try {
            $onlineOpenTime = \Carbon\Carbon::parse($onlineOpenTime ?: '09:00')->format('H:i');
        } catch (\Throwable $e) {
            $onlineOpenTime = '09:00';
        }

        try {
            $onlineCloseTime = \Carbon\Carbon::parse($onlineCloseTime ?: '21:00')->format('H:i');
        } catch (\Throwable $e) {
            $onlineCloseTime = '21:00';
        }
    @endphp



    {{-- 🏪 Header ร้าน --}}

    <div class="container-fluid py-5 mb-3">


        <div class="container text-center">
            <h2 class="fw-bold text-success">
                <i class="bi bi-shop-window me-2"></i>
                ร้าน {{ $shopSettings->shop_name ?? 'pos' }}
            </h2>

            <i class="bi bi-clock-fill me-1"></i>
            เปิดให้บริการ
            {{ $onlineOpenTime }}–{{ $onlineCloseTime }} น.
            </p>
        </div>

        {{-- 🔔 System Alert --}}
        @if (!empty($systemAlert))
            <div class="container-fluid bg-warning bg-opacity-25 py-2 mb-3  border-5">
                <div class="container">

                    @if (Auth::check() && Auth::user()->role === 'member' && Auth::user()->room_number === '601')
                        <form method="POST" action="{{ route('online.settings.updateAlert') }}">
                            @csrf

                            <div class="mb-2">
                                <textarea name="system_alert" class="form-control" rows="2">{{ $systemAlert }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-danger btn-sm">
                                บันทึกข้อความแจ้งเตือน
                            </button>
                        </form>
                    @else
                        <div class="text-danger fw-semibold">
                            🔔 {{ $systemAlert }}
                        </div>
                    @endif

                </div>
            </div>
        @endif


    </div>

    {{-- 🏷️ หมวดหมู่ --}}

    <div id="category-menu" class="mb-4 overflow-auto d-flex gap-2 pb-2" style="scroll-snap-type: x mandatory;">


        <a href="{{ route('online.index') }}"
            class="category-link btn btn-sm
    {{ request('category') ? 'btn-outline-secondary' : 'btn-orange' }}
    flex-shrink-0">

            ทั้งหมด

        </a>

        @foreach ($categories as $category)
            <a href="{{ route('online.index', ['category' => $category->id]) }}"
                class="category-link btn btn-sm
        {{ request('category') == $category->id ? 'btn-orange' : 'btn-outline-secondary' }}
        flex-shrink-0">

                {{ $category->name }}

            </a>
        @endforeach


    </div>

    {{-- 🔍 ค้นหาสินค้า --}}

    <form method="GET" action="{{ route('online.index') }}" class="row mb-4 g-2">


        <div class="col-md-4">

            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..."
                value="{{ request('search') }}">

        </div>

        <div class="col-md-2">

            <button type="submit" class="btn btn-orange w-100">

                ค้นหา

            </button>

        </div>


    </form>

    {{-- 📦 รายการสินค้า --}}

    <div id="product-list">


        <div class="row g-4">

            @forelse($products as $product)
                @php

                    $totalStock = (int) ($product->getAttribute('total_stock') ?? 0);

                    $unit = $product->productUnits->first();

                    $isActive = (bool) ($product->is_active ?? true);

                    /*
            | สินค้าที่ปิดการขายจะไม่ถูกสร้างเป็น Card ในหน้านี้
            */
                    if (!$isActive) {
                        continue;
                    }
                @endphp


                <div class="col-6 col-md-4 col-lg-3">

                    <div
                        class="card h-100 shadow-sm
                {{ $totalStock <= 0 ? 'card-out-of-stock' : '' }}
                position-relative">


                        {{-- หมด --}}
                        @if ($totalStock <= 0)
                            <span
                                class="badge badge-out-of-stock
                        position-absolute top-0 start-0 m-2">

                                ❌ หมด

                            </span>
                        @endif


                        {{-- รูปสินค้า: โหลดเฉพาะรูปแรกเพื่อลดจำนวน request และทำให้หน้าเร็วขึ้น --}}
                        {{-- รูปสินค้า Gallery --}}
                        @php
                            $galleryImages = $product->images
                                ->map(function ($image) {
                                    if (!filled($image->image_path)) {
                                        return null;
                                    }

                                    $imagePath = str_replace('\\', '/', $image->image_path);
                                    $imagePath = ltrim($imagePath, '/');

                                    $imagePath = preg_replace(
                                        '#^(public/storage/|storage/app/public/|public/|storage/)#',
                                        '',
                                        $imagePath,
                                    );

                                    if (!Storage::disk('public')->exists($imagePath)) {
                                        return null;
                                    }

                                    return asset('storage/' . $imagePath);
                                })
                                ->filter()
                                ->values();

                            $galleryCount = $galleryImages->count();
                            $galleryId = 'product-gallery-' . $product->id;
                        @endphp

                        <div class="product-image-wrap" id="{{ $galleryId }}">

                            @if ($galleryCount > 0)
                                @foreach ($galleryImages as $index => $imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }} รูปที่ {{ $index + 1 }}"
                                        class="gallery-image" width="480" height="365"
                                        loading="{{ $index === 0 ? 'lazy' : 'lazy' }}" decoding="async"
                                        @if ($index !== 0) hidden @endif>
                                @endforeach

                                {{-- ปุ่มเปลี่ยนรูป แสดงเมื่อมีมากกว่า 1 รูป --}}
                                @if ($galleryCount > 1)
                                    <button type="button"
    class="gallery-control gallery-prev"
    data-gallery-action="prev"
    data-gallery-id="{{ $galleryId }}"
    aria-label="รูปก่อนหน้า">
    &#10094;
</button>

                                    <button type="button"
    class="gallery-control gallery-next"
    data-gallery-action="next"
    data-gallery-id="{{ $galleryId }}"
    aria-label="รูปถัดไป">
    &#10095;
</button>

                                    <span class="gallery-counter" data-gallery-counter>
                                        1 / {{ $galleryCount }}
                                    </span>
                                @endif
                            @else
                                <div class="product-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif

                        </div>

                        {{-- รายละเอียด --}}
                        <div class="card-body d-flex flex-column">

                            <h5 class="card-title">

                                {{ $product->name }}

                            </h5>


                            @if ($unit)
                                <p class="card-text mb-1">

                                    <span class="price fs-5 text-orange">

                                        {{ number_format($unit->price, 2) }}

                                        บาท

                                    </span>

                                    <span class="text-muted">

                                        / {{ $unit->unit_name }}

                                    </span>

                                </p>
                            @endif


                            {{-- Add To Cart --}}
                            <form action="{{ route('online.add') }}" method="POST" class="mt-auto add-to-cart-form">

                                @csrf


                                <input type="hidden" name="product_id" value="{{ $product->id }}">


                                @if ($unit)
                                    <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">


                                    <input type="hidden" name="price" value="{{ $unit->price }}">
                                @endif


                                <input type="hidden" name="quantity" value="1">


                                @if ($totalStock <= 0)
                                    <button type="button" class="btn btn-secondary w-100" disabled>

                                        สินค้าหมด

                                    </button>
                                @else
                                    <button type="submit" class="btn btn-primary w-100">

                                        เพิ่มลงตะกร้า

                                    </button>
                                @endif

                            </form>

                        </div>

                    </div>

                </div>


            @empty

                <div class="col-12 text-center">

                    <p>
                        ไม่พบสินค้าในตอนนี้
                    </p>

                </div>
            @endforelse

        </div>


        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">

            {{ $products->withQueryString()->links() }}

        </div>


    </div>

    {{-- ⚡ AJAX เปลี่ยนหมวดหมู่ / Pagination / ค้นหา --}}
    @push('scripts')
        <script>
            (() => {
                /*
                |--------------------------------------------------------------------------
                | เพิ่มสินค้าลงตะกร้าแบบ AJAX
                | ป้องกันการผูก Event ซ้ำและป้องกันการส่ง Request ซ้ำ
                |--------------------------------------------------------------------------
                */
                if (window.onlineCartSubmitBound) {
                    return;
                }

                window.onlineCartSubmitBound = true;

                document.addEventListener('submit', async function(event) {
                    const form = event.target.closest('.add-to-cart-form');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();

                    // ป้องกันการส่งฟอร์มเดิมซ้ำขณะที่ Request แรกยังไม่เสร็จ
                    if (form.dataset.submitting === 'true') {
                        return;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');

                    if (!submitBtn || submitBtn.disabled) {
                        return;
                    }

                    form.dataset.submitting = 'true';

                    const originalText = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
            กำลังเพิ่ม...
        `;

                    try {
                        const csrfToken = form.querySelector('input[name="_token"]')?.value;

                        if (!csrfToken) {
                            throw new Error('ไม่พบ CSRF token');
                        }

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(
                                data.message || 'ไม่สามารถเพิ่มสินค้าลงตะกร้าได้'
                            );
                        }

                        if (data.success) {
                            const cartBadge = document.querySelector('#cart-count');

                            if (cartBadge && data.total_quantity !== undefined) {
                                cartBadge.textContent = data.total_quantity;
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว!',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                });
                            } else {
                                alert('เพิ่มลงตะกร้าเรียบร้อย!');
                            }
                        }
                    } catch (error) {
                        console.error('Add to cart error:', error);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: error.message || 'ไม่สามารถเพิ่มสินค้าได้ กรุณาลองใหม่อีกครั้ง'
                            });
                        } else {
                            alert(error.message || 'ไม่สามารถเพิ่มสินค้าได้ กรุณาลองใหม่อีกครั้ง');
                        }
                    } finally {
                        form.dataset.submitting = 'false';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            })();

            document.addEventListener('DOMContentLoaded', function() {

                let loading = false;


                function fetchAndUpdate(url) {

                    if (!url || loading) return;

                    loading = true;

                    document.body.style.cursor = 'wait';


                    fetch(url, {

                            headers: {

                                'X-Requested-With': 'XMLHttpRequest',

                                'Accept': 'text/html'

                            }

                        })

                        .then(response => {

                            if (!response.ok) {

                                throw new Error('Network Error');

                            }

                            return response.text();

                        })

                        .then(html => {

                            const parser =
                                new DOMParser();

                            const doc =
                                parser.parseFromString(
                                    html,
                                    'text/html'
                                );


                            const newProducts =
                                doc.querySelector('#product-list');


                            const currentProducts =
                                document.querySelector('#product-list');


                            if (newProducts && currentProducts) {

                                currentProducts.innerHTML =
                                    newProducts.innerHTML;

                            }


                            const newCategories =
                                doc.querySelector('#category-menu');


                            const currentCategories =
                                document.querySelector('#category-menu');


                            if (newCategories && currentCategories) {

                                currentCategories.innerHTML =
                                    newCategories.innerHTML;

                            }


                            window.history.pushState({},
                                '',
                                url
                            );


                            window.scrollTo({

                                top: 0,

                                behavior: 'smooth'

                            });

                        })

                        .catch(error => {

                            console.error(error);

                            window.location.href = url;

                        })

                        .finally(() => {

                            loading = false;

                            document.body.style.cursor =
                                'default';

                        });

                }


                // หมวดหมู่ + Pagination
                document.addEventListener('click', function(event) {

                    const link =
                        event.target.closest(
                            '.category-link, #product-list .pagination a'
                        );


                    if (!link) return;


                    event.preventDefault();


                    fetchAndUpdate(
                        link.href
                    );

                });


                // Search
                document.addEventListener('submit', function(event) {

                    const form =
                        event.target.closest(
                            'form[action="{{ route('online.index') }}"]'
                        );


                    if (!form) return;


                    event.preventDefault();


                    const params =
                        new URLSearchParams(
                            new FormData(form)
                        );


                    const url =
                        form.action +
                        '?' +
                        params.toString();


                    fetchAndUpdate(url);

                });

            });
            // เปลี่ยนรูปสินค้าใน Gallery
            document.addEventListener('click', function(event) {

                const button = event.target.closest('[data-gallery-action]');

                if (!button) return;

                const galleryId = button.dataset.galleryId;
                const action = button.dataset.galleryAction;

                const gallery = document.getElementById(galleryId);

                if (!gallery) return;

                const images = Array.from(
                    gallery.querySelectorAll('.gallery-image')
                );

                if (images.length <= 1) return;

                let currentIndex = images.findIndex(
                    image => !image.hidden
                );

                if (currentIndex < 0) currentIndex = 0;

                if (action === 'next') {
                    currentIndex = (currentIndex + 1) % images.length;
                } else {
                    currentIndex = (currentIndex - 1 + images.length) % images.length;
                }

                images.forEach((image, index) => {
                    image.hidden = index !== currentIndex;
                });

                const counter = gallery.querySelector('[data-gallery-counter]');

                if (counter) {
                    counter.textContent = `${currentIndex + 1} / ${images.length}`;
                }

            });
        </script>
    @endpush
@endsection
