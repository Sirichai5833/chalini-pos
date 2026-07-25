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
            --primary-color: #007849;
            /* เขียวเซเว่น */
            --accent-color: #FF7F00;
            /* ส้มเซเว่น */
            --danger-color: #ED1C24;
            /* แดงเซเว่น */
            --light-bg: #fff9f4;
            --text-dark: #212529;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--light-bg);
            padding-top: 70px;
        }

        .navbar {
            background-color: var(--primary-color) !important;
        }

        .navbar-brand,
        .nav-link,
        .btn-link {
            color: #fff !important;
        }

        .btn-orange {
            background-color: var(--accent-color);
            color: #fff;
            border: none;
        }

        .btn-orange:hover {
            background-color: #e76b00;
            color: #fff;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }

        .btn-outline-secondary:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .overflow-auto a {
            scroll-snap-align: start;
        }

        .btn-primary:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .category-btn.active {
            background-color: var(--accent-color) !important;
            color: white;
        }

        .card .price {
            color: var(--accent-color);
            font-weight: bold;
        }

        .add-to-cart {
            cursor: pointer;
        }

        .card-out-of-stock {
            opacity: 0.6;
        }

        .badge-low-stock {
            background-color: #ffc107;
            color: black;
            font-size: 0.75rem;
            border-radius: 5px;
            padding: 2px 6px;
        }

        .badge-out-of-stock {
            background-color: var(--danger-color);
            color: white;
            font-size: 0.75rem;
            border-radius: 5px;
            padding: 2px 6px;
        }

        .pagination svg {
            width: 1em;
            height: 1em;
        }

        .pagination {
            font-size: 1rem;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
        }

        .text-orange {
            color: var(--accent-color);
        }

        .mobile-fixed-bottom-bar {
            background-color: white;
            border-top: 1px solid #eee;
            box-shadow: 0 -1px 6px rgba(0, 0, 0, 0.1);
        }

        .mobile-fixed-bottom-bar a {
            color: var(--primary-color);
        }

        .mobile-fixed-bottom-bar a.active,
        .mobile-fixed-bottom-bar a:hover {
            color: var(--accent-color);
        }
    </style>

   {{-- 🏪 Header ร้าน --}}

<div class="container-fluid py-5 mb-3"
    style="background: linear-gradient(135deg, #fef9f4 0%, #ffe3d2 100%); border-bottom: 4px solid #FF5B00;">


<div class="container text-center">
    <h2 class="fw-bold text-success">
        <i class="bi bi-shop-window me-2"></i>
        ร้าน {{ $shopSettings->shop_name ?? 'pos' }}
    </h2>

    <p class="text-muted">
        เลือกซื้อสินค้าที่ต้องการ จัดส่งถึงที่ไม่มีค่าบริการ
        (ตั้งแต่ 8.00 ถึง 21.00)
    </p>
</div>

{{-- 🔔 System Alert --}}
@if (!empty($systemAlert))
    <div class="container-fluid bg-warning bg-opacity-25 py-2 mb-3 border-start border-5 border-danger">
        <div class="container">

            @if (
                Auth::check() &&
                Auth::user()->role === 'member' &&
                Auth::user()->room_number === '601'
            )

                <form method="POST" action="{{ route('online.settings.updateAlert') }}">
                    @csrf

                    <div class="mb-2">
                        <textarea
                            name="system_alert"
                            class="form-control"
                            rows="2">{{ $systemAlert }}</textarea>
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

<div id="category-menu"
    class="mb-4 overflow-auto d-flex gap-2 pb-2"
    style="scroll-snap-type: x mandatory;">


<a href="{{ route('online.index') }}"
    class="category-link btn btn-sm
    {{ request('category') ? 'btn-outline-secondary' : 'btn-orange' }}
    flex-shrink-0">

    ทั้งหมด

</a>

@foreach ($categories as $category)

    <a href="{{ route('online.index', ['category' => $category->id]) }}"
        class="category-link btn btn-sm
        {{ request('category') == $category->id
            ? 'btn-orange'
            : 'btn-outline-secondary' }}
        flex-shrink-0">

        {{ $category->name }}

    </a>

@endforeach


</div>

{{-- 🔍 ค้นหาสินค้า --}}

<form method="GET"
    action="{{ route('online.index') }}"
    class="row mb-4 g-2">


<div class="col-md-4">

    <input
        type="text"
        name="search"
        class="form-control"
        placeholder="ค้นหาสินค้า..."
        value="{{ request('search') }}">

</div>

<div class="col-md-2">

    <button type="submit"
        class="btn btn-orange w-100">

        ค้นหา

    </button>

</div>


</form>

{{-- 📦 รายการสินค้า --}}

<div id="product-list">


<div class="row g-4">

    @forelse($products as $product)

        @php

            $totalStock =
                (int) ($product->getAttribute('total_stock') ?? 0);

            $unit =
                $product->productUnits->first();

            $isActive =
                $product->is_active ?? true;

        @endphp


        <div class="col-6 col-md-4 col-lg-3">

            <div class="card h-100 shadow-sm
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


                {{-- รูปสินค้า --}}
                @if ($product->images->isNotEmpty())

                    <div
                        id="carouselProduct{{ $product->id }}"
                        class="carousel slide mb-3">

                        <div class="carousel-inner">

                            @foreach ($product->images as $key => $image)

                                <div
                                    class="carousel-item
                                    {{ $key === 0 ? 'active' : '' }}">

                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        class="d-block w-100"
                                        loading="lazy"
                                        style="height:200px; object-fit:cover;">

                                </div>

                            @endforeach

                        </div>


                        @if ($product->images->count() > 1)

                            <button
                                class="carousel-control-prev"
                                type="button"
                                data-bs-target="#carouselProduct{{ $product->id }}"
                                data-bs-slide="prev">

                                <span class="carousel-control-prev-icon"></span>

                            </button>

                            <button
                                class="carousel-control-next"
                                type="button"
                                data-bs-target="#carouselProduct{{ $product->id }}"
                                data-bs-slide="next">

                                <span class="carousel-control-next-icon"></span>

                            </button>

                        @endif

                    </div>

                @endif


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
                    <form
                        action="{{ route('online.add') }}"
                        method="POST"
                        class="mt-auto add-to-cart-form">

                        @csrf


                        <input
                            type="hidden"
                            name="product_id"
                            value="{{ $product->id }}">


                        @if ($unit)

                            <input
                                type="hidden"
                                name="product_unit_id"
                                value="{{ $unit->id }}">


                            <input
                                type="hidden"
                                name="price"
                                value="{{ $unit->price }}">

                        @endif


                        <input
                            type="hidden"
                            name="quantity"
                            value="1">


                        @if (!$isActive || $totalStock <= 0)

                            <button
                                type="button"
                                class="btn btn-secondary w-100"
                                disabled>

                                สินค้าหมด / ปิดการขาย

                            </button>

                        @else

                            <button
                                type="submit"
                                class="btn btn-primary w-100">

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

    {{ $products->links() }}

</div>


</div>

{{-- ⚡ AJAX เปลี่ยนหมวดหมู่ / Pagination / ค้นหา --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    // 🛒 ดักจับการกดปุ่ม "เพิ่มลงตะกร้า" ไม่ให้รีโหลดหน้า
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('.add-to-cart-form');
        if (!form) return;

        event.preventDefault(); // ป้องกันไม่ให้หน้าเว็บ รีโหลด/กระพริบ

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        const originalText = submitBtn.innerHTML;

        // เปลี่ยนสถานะปุ่มชั่วคราว เพื่อให้ผู้ใช้รู้ว่ากำลังทำงาน
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> กำลังเพิ่ม...`;

        // ส่งข้อมูลแบบ AJAX ไปหา public function add() ใน Controller
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(response => {
            if (!response.ok) throw new Error('เกิดข้อผิดพลาดจากเซิร์ฟเวอร์');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // แจ้งเตือนแบบ SweetAlert (ถ้ามีใช้งาน)
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

                // อัปเดตตัวเลขบนไอคอนตะกร้า (ถ้าหน้าเว็บมี element ID เช่น #cart-count)
                const cartBadge = document.querySelector('#cart-count');
                if (cartBadge && data.total_quantity !== undefined) {
                    cartBadge.innerText = data.total_quantity;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('ไม่สามารถเพิ่มสินค้าได้ กรุณาลองใหม่อีกครั้ง');
        })
        .finally(() => {
            // คืนค่าปุ่มกลับเป็นปกติ
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

});

document.addEventListener('DOMContentLoaded', function () {

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


            window.history.pushState(
                {},
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
    document.addEventListener('click', function (event) {

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
    document.addEventListener('submit', function (event) {

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

</script>

@endpush
@endsection
