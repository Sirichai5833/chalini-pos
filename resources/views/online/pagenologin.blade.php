<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chalini | Online Shop</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    {{-- Google Font (optional) --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            padding-top: 70px;
            background-color: #fff9f4;
        }

        .navbar {
            background-color: #ff6f3c !important;
        }

        .navbar-brand,
        .nav-link,
        .btn-link {
            color: #fff !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-outline-dark {
            border-color: #ff6f3c;
            color: #ff6f3c;
        }

        .btn-outline-dark:hover {
            background-color: #ff6f3c;
            color: white;
        }

        .btn-primary {
            background-color: #ff6f3c;
            border-color: #ff6f3c;
        }

        .btn-primary:hover {
            background-color: #ff5722;
            border-color: #ff5722;
        }

        .mobile-fixed-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #fff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1055;
            padding: 0.5rem;
            border-top: 1px solid #eee;
        }

        .mobile-fixed-bottom-bar .btn {
            font-size: 0.95rem;
        }



        .nav-link:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    {{-- 🔝 Navbar --}}
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('online.index') }}">🛍️ Chalini</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link">👋 {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.index') }}">หน้าแรก</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.track') }}">📦ติดตามคำสั่งซื้อ</a>
                        </li>
                        <li class="nav-item d-none d-lg-inline">
                            <a class="nav-link" href="{{ route('online.cart') }}">🛒ตะกร้า</a>
                        </li>
                        <li class="nav-item d-none d-lg-inline">
                            <a class="nav-link" href="{{ route('online.checkout') }}">💳ชำระเงิน</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.edit', ['member' => Auth::user()->id]) }}">
                                <i class="fa-solid fa-pen-to-square" style="color: #74C0FC;"></i>จัดการข้อมูล
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">🚪ออกจากระบบ</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- ✅ Mobile Bottom Bar --}}
    <div class="mobile-fixed-bottom-bar d-block d-md-none">
        <div class="d-flex justify-content-around align-items-center h-100">
            <a href="{{ route('online.cart') }}" class="btn btn-outline-dark w-50 mx-1">ตะกร้า</a>
            <a href="{{ route('online.checkout') }}" class="btn btn-primary w-50 mx-1">ชำระเงิน</a>
        </div>
    </div>



<div class="container py-4">
    <h2 class="mb-4 text-center">🛒 ร้านค้าออนไลน์</h2>

    {{-- 🗂 หมวดหมู่สินค้า --}}
    <div class="mb-4">
        <strong>หมวดหมู่:</strong>
        <a href="{{ route('online.pagenologin') }}" class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-secondary' }}">ทั้งหมด</a>
        @foreach($categories as $category)
            <a href="{{ route('online.pagenologin', ['category' => $category->id]) }}"
               class="btn btn-sm {{ request('category') == $category->id ? 'btn-secondary' : 'btn-outline-secondary' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    {{-- 🔍 ค้นหาและกรองสินค้า --}}
    <form method="GET" action="{{ route('online.pagenologin') }}" class="row mb-4 g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="">เรียงตาม</option>
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ใหม่สุด</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>ราคาต่ำไปสูง</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>ราคาสูงไปต่ำ</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">ค้นหา</button>
        </div>
    </form>

    {{-- 📦 แสดงรายการสินค้า --}}
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <a href="javascript:void(0)">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white text-center py-5" style="height: 200px;">ไม่มีรูป</div>
                        @endif
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted mb-1">
                            ราคา: <strong>{{ number_format($product->price, 2) }}</strong> บาท
                        </p>
                        
                        @if($product->stock <= 0)
                            <span class="text-danger mb-2">❌ สินค้าหมด</span>
                        @elseif($product->stock <= 5)
                            <span class="text-warning mb-2">⚠️ เหลือ {{ $product->stock }} ชิ้น</span>
                        @endif

                        {{-- 🔒 ปุ่มล็อกไว้สำหรับคนยังไม่ login --}}
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-auto">
                            🔒 เข้าสู่ระบบเพื่อสั่งซื้อ
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted">ไม่พบสินค้า</p>
            </div>
        @endforelse
    </div>

    {{-- 🔁 Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
