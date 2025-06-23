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
            --accent-color: #FF5B00;
            --secondary-color: #E60012;
            --bg-color: #fff;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            padding-top: 70px;
            background-color: #fff9f4;
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

        .btn-outline-dark {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .btn-outline-dark:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
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

        .mobile-fixed-bottom-bar a {
            text-decoration: none;
            color: var(--primary-color);
            font-size: 0.8rem;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        .mobile-fixed-bottom-bar a {
            flex: 1;
            /* ให้แต่ละลิงก์กินพื้นที่เท่าๆ กัน */
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
</head>

<body>

    {{-- 🔝 Navbar --}}
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('online.index') }}">🛍️ Chalini</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="collapse navbar-collapse" id="mainNavbar">

                    {{-- ✅ เมนูสำหรับ Desktop --}}
                    <ul class="navbar-nav ms-auto align-items-center gap-2 d-none d-md-flex">
                        <li class="nav-item"><span class="nav-link">{{ Auth::user()->name }}
                                ({{ Auth::user()->role }})</span></li>
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
                            <a class="nav-link" href="{{ route('online.edit', ['member' => Auth::user()->id]) }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i> จัดการข้อมูล
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link p-0"
                                    style="display: inline; cursor: pointer;">
                                    ออกจากระบบ
                                </button>
                            </form>

                        </li>
                    </ul>

                    {{-- ✅ เมนูเฉพาะมือถือ --}}
                    <ul class="navbar-nav ms-auto align-items-center gap-2 d-flex d-md-none">
                        <li class="nav-item">
                            <span class="nav-link">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
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
    <div class="mobile-fixed-bottom-bar d-block d-md-none">
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
            </a>
            <a href="{{ route('online.edit', ['member' => Auth::user()->id]) }}" class="text-center">
                <i class="fas fa-user"></i>
                <small>บัญชี</small>
            </a>
        </div>
    </div>


    {{-- 🔻 Main Content --}}
    <div class="container py-4">
        @yield('content')
        @yield('scripts')
    </div>

    {{-- 🧩 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const productId = form.querySelector('input[name="product_id"]').value;
                const productUnitId = form.querySelector('input[name="product_unit_id"]').value;
                const quantity = form.querySelector('input[name="quantity"]').value;

                fetch('{{ route('online.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            product_unit_id: productUnitId,
                            quantity: quantity
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            updateCartBadge(data.total_quantity);
                            Swal.fire({
                                icon: 'success',
                                title: 'เพิ่มสินค้าสำเร็จ!',
                                showConfirmButton: false,
                                timer: 1200,
                                toast: true,
                                position: 'top-end'
                            });
                        } else {
                            Swal.fire('ผิดพลาด', 'เพิ่มสินค้าไม่สำเร็จ', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเพิ่มสินค้าได้', 'error');
                    });
            });
        });

        function updateCartBadge(newTotal) {
            document.querySelectorAll('.cart-total-items').forEach(badge => {
                badge.textContent = newTotal;
                badge.classList.remove('bg-secondary', 'bg-danger');
                badge.classList.add(newTotal > 0 ? 'bg-danger' : 'bg-secondary');
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
