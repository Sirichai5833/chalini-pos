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
                            <span class="nav-link"> {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.index') }}">หน้าแรก</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.track') }}">ติดตามคำสั่งซื้อ</a>
                        </li>
                        <li class="nav-item d-none d-lg-inline">
                            <a class="nav-link" href="{{ route('online.cart') }}">
                                ตะกร้า 
                                <span class="badge cart-badge {{ $totalItems > 0 ? 'bg-danger' : 'bg-secondary' }}">
                                    {{ $totalItems }}
                                </span>
                            </a>
                        </li>
                        
                        <li class="nav-item d-none d-lg-inline">
                            <a class="nav-link" href="{{ route('online.checkout') }}">ชำระเงิน</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('online.edit', ['member' => Auth::user()->id]) }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> จัดการข้อมูล
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">ออกจากระบบ</button>
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
            <a href="{{ route('online.cart') }}" class="btn position-relative w-50 mx-1" >
                <i class="bi bi-cart3 fs-4"></i>
                <span id="cart-total-items" class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                </span>ตะกร้า
            </a>
            <a href="{{ route('online.checkout') }}" class="btn btn-primary w-50 mx-1">ชำระเงิน</a>
        </div>
    </div>
    {{-- 🔻 Main Content --}}
    <div class="container py-4">
        @yield('content')
        @yield('scripts')
    </div>

    {{-- 🧩 JS --}}
    <script>
    function addToCart(productId, quantity) {
    // อัปเดต UI ทันทีเมื่อกดเพิ่มสินค้า
    const currentBadge = document.querySelector('.cart-badge');
    const mobileBadge = document.querySelector('#cart-total-items');
    const currentCount = parseInt(currentBadge.textContent) || 0;

    // อัปเดตจำนวนใน UI ก่อน
    const newCount = currentCount + quantity;
    if (currentBadge) {
        currentBadge.textContent = newCount;
        currentBadge.classList.remove('bg-secondary');
        currentBadge.classList.add(newCount > 0 ? 'bg-danger' : 'bg-secondary');
    }
    if (mobileBadge) {
        mobileBadge.textContent = newCount;
        mobileBadge.classList.remove('bg-secondary');
        mobileBadge.classList.add(newCount > 0 ? 'bg-danger' : 'bg-secondary');
    }

    // ส่งคำขอ AJAX เพื่อเพิ่มสินค้าในตะกร้าผ่านเซิร์ฟเวอร์
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('สินค้าเพิ่มแล้ว');
        } else {
            console.error('เกิดข้อผิดพลาดในการเพิ่มสินค้า');
        }
    })
    .catch(error => {
        console.error('เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์', error);
    });
}


function updateCartBadge(newTotal) {
    // อัปเดต badge ใน navbar
    const navbarBadge = document.querySelector('.cart-badge');
    if (navbarBadge) {
        navbarBadge.textContent = newTotal;
        navbarBadge.classList.remove('bg-secondary');
        navbarBadge.classList.add(newTotal > 0 ? 'bg-danger' : 'bg-secondary');
    }

    // อัปเดต badge ใน mobile bottom bar
    const mobileBadge = document.querySelector('#cart-total-items');
    if (mobileBadge) {
        mobileBadge.textContent = newTotal;
        mobileBadge.classList.remove('bg-secondary');
        mobileBadge.classList.add(newTotal > 0 ? 'bg-danger' : 'bg-secondary');
    }
}


    </script>
    
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
