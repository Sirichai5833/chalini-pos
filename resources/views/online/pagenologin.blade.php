<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chalini | Online Shop</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        :root {
            --primary-color: #007849;
            /* เขียวเซเว่น */
            --accent-color: #FF5B00;
            /* ส้มเข้ม (ใช้แทน FF7F00) */
            --secondary-color: #E60012;
            /* แดง */
            --danger-color: #ED1C24;
            --light-bg: #fff9f4;
            --bg-color: #fff;
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

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
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

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-dark {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .btn-outline-dark:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-outline-secondary:hover {
            background-color: var(--accent-color);
            color: white;
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

        .overflow-auto a {
            scroll-snap-align: start;
        }

        .mobile-fixed-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: white;
            box-shadow: 0 -1px 6px rgba(0, 0, 0, 0.1);
            z-index: 1055;
            padding: 0.5rem;
            border-top: 1px solid #eee;
        }

        .mobile-fixed-bottom-bar a {
            flex: 1;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .mobile-fixed-bottom-bar i {
            font-size: 1.5rem;
            line-height: 1;
        }

        .mobile-fixed-bottom-bar small {
            line-height: 1.1;
            margin-top: 2px;
        }

        .mobile-fixed-bottom-bar .badge {
            font-size: 0.6rem;
            top: 5px !important;
            right: 15px !important;
        }

        .mobile-fixed-bottom-bar a.active,
        .mobile-fixed-bottom-bar a:hover {
            color: var(--accent-color);
        }

        .nav-link:hover {
            text-decoration: underline;
        }
    </style>


</head>

<body class="container py-4">

    {{-- 🔝 Navbar --}}
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm ">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('online.index') }}">🛍️ Chalini</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="collapse navbar-collapse" id="mainNavbar">

                    {{-- ✅ เมนูสำหรับ Desktop --}}
                    <ul class="navbar-nav ms-auto align-items-center gap-2 d-none d-md-flex ">
                        @auth
                            <li class="nav-item"><span class="nav-link">{{ Auth::user()->name }}
                                    ({{ Auth::user()->role }})</span></li>
                        @endauth

                        <li class="nav-item"><a class="nav-link" href="{{ route('online.index') }}">หน้าแรก</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('online.track') }}">ติดตามคำสั่งซื้อ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1" href="{{ route('online.cart') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span>ตะกร้า</span>
                                <span class="cart-total-items badge bg-danger rounded-pill px-2 py-1">
                                    {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('online.edit', ['member' => Auth::user()->id]) }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> จัดการข้อมูล
                                </a>
                            </li>
                        @endauth

                        </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link p-0"
                                    style="display: inline; cursor: pointer;">
                                    เข้าสู่ระบบ
                                </button>
                            </form>

                        </li>
                    </ul>

                    {{-- ✅ เมนูเฉพาะมือถือ --}}
                    <ul class="navbar-nav ms-auto align-items-center gap-2 d-flex d-md-none">
                        <li class="nav-item">
                            @auth
                                <span class="nav-link">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                            @endauth

                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link p-0">ออกจากระบบ</button>
                            </form>
                        </li>
                    </ul>

                </div>

            </div>
    </nav>

    {{-- ✅ Mobile Bottom Bar --}}
    <div class="mobile-fixed-bottom-bar d-block d-md-none ">
        <div class="d-flex justify-content-around align-items-center h-100">
            <a href="{{ route('online.index') }}" class="text-center">
                <i class="fa-solid fa-home fa-lg"></i>
                <small>หน้าแรก</small>
            </a>
            <a href="{{ route('online.cart') }}" class="text-center position-relative">
                <i class="fas fa-shopping-cart"></i>
                <small>ตะกร้า</small>
                <span class="cart-total-items position-absolute badge rounded-pill bg-danger">
                    {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                </span>
            </a>
            <a href="{{ route('online.track') }}" class="text-center">
                <i class="fas fa-box"></i>
                <small>คำสั่งซื้อ</small>
                @auth
                    <a href="{{ route('online.edit', ['member' => Auth::user()->id]) }}" class="text-center">
                        <i class="fas fa-user"></i>
                        <small>บัญชี</small>
                    </a>
                @endauth
        </div>
    </div>

    <div class="container-fluid py-5 mb-3 mt-5"
        style="background: linear-gradient(135deg, #fef9f4 0%, #ffe3d2 100%); border-bottom: 4px solid #FF5B00;">
        <div class="container text-center ">
            <h2 class="fw-bold text-success ">
                <i class="bi bi-shop-window me-2"></i> ร้าน Chalini Online
            </h2>
            <p class="text-muted">เลือกซื้อสินค้าที่ต้องการ จัดส่งถึงที่ไม่มีค่าบริการเฉพาะลูกค้าตึกชาลินี 2 เท่านั้น
                (ตั้งแต่ 8.00 ถึง 21.00) </p>
        </div>


    </div>





    <div class="mb-4 overflow-auto d-flex gap-2 pb-2" style="scroll-snap-type: x mandatory;">
        <a href="{{ route('online.index') }}"
            class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-orange' }} flex-shrink-0">ทั้งหมด</a>
        @foreach ($categories as $category)
            <a href="{{ route('online.index', ['category' => $category->id]) }}"
                class="btn btn-sm {{ request('category') == $category->id ? 'btn-orange' : 'btn-outline-secondary' }} flex-shrink-0">
                {{ $category->name }}
            </a>
        @endforeach
    </div>


    <form method="GET" action="{{ route('online.index') }}" class="row mb-4 g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..."
                value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-orange w-100">ค้นหา</button>
        </div>
    </form>

    <div class="row g-4">
        @forelse($products as $product)
            @php
                $stock = $product->stock;
                $totalStock = $stock ? $stock->store_stock + $stock->warehouse_stock : 0;
            @endphp

            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm {{ $totalStock <= 0 ? 'card-out-of-stock' : '' }} position-relative">

                    @if ($stock && !$stock->track_stock)
                        <span class="badge bg-secondary position-absolute top-0 start-0 m-2">ไม่ติดตามสต๊อก</span>
                    @elseif($totalStock <= 0)
                        <span class="badge badge-out-of-stock">❌ หมด</span>
                    @elseif($totalStock <= 5)
                        <span class="badge badge-low-stock">⚠️ เหลือ {{ $totalStock }}</span>
                    @endif

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                            alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                            style="height: 200px;">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>

                        @php
                            $unit = $product->productUnits->first();
                        @endphp


                        @if ($unit)
                            <p class="card-text mb-1">

                                <span class="price fs-5 text-orange">{{ number_format($unit->price, 2) }} บาท</span>
                                <span class="text-muted fs-6"> / {{ $unit->unit_name }}</span>
                            </p>
                        @endif

                        <form action="{{ route('online.add') }}" method="POST" class="mt-auto add-to-cart-form"
                            data-product-id="{{ $product->id }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_unit_id" value="{{ $unit->id }}">
                            <input type="hidden" name="price" value="{{ $unit->price }}">
                            <input type="hidden" name="quantity" value="1">

                            @if (Auth::check())
                                {{-- ถ้าล็อกอินแล้ว แสดงปุ่มเพิ่มลงตะกร้า --}}
                                <button type="submit" class="btn btn-primary add-to-cart"
                                    data-id="{{ $product->id }}" data-quantity="1">
                                    เพิ่มลงตะกร้า
                                </button>
                            @else
                                {{-- ถ้ายังไม่ล็อกอิน แสดงปุ่มกดแล้วพาไปล็อกอิน --}}
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100"
                                    onclick="return confirm('กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ')">
                                    🔒 เข้าสู่ระบบเพื่อสั่งซื้อ
                                </a>
                            @endif
                        </form>


                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p>ไม่พบสินค้าในตอนนี้</p>
            </div>
        @endforelse

    </div>

    {{-- แสดง pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
    </div>

    {{-- สคริปต์ AJAX --}}
