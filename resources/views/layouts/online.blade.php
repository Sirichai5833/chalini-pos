<!DOCTYPE html>

<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


<title>Chalini | Online Shop</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Bootstrap --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

{{-- FontAwesome --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

{{-- Google Font --}}
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #007849;
        --accent-color: #FF5B00;
        --secondary-color: #E60012;
        --bg-color: #fff9f4;
    }

    body {
        font-family: 'Sarabun', sans-serif;
        padding-top: 70px;
        background-color: var(--bg-color);
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

    .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
        color: #fff;
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
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
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

    .mobile-fixed-bottom-bar a:hover {
        color: var(--accent-color);
    }

    .mobile-fixed-bottom-bar i {
        font-size: 1.4rem;
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

    .nav-link:hover {
        text-decoration: underline;
    }
</style>


</head>

<body>


{{-- =========================
    NAVBAR
========================== --}}

<nav class="navbar navbar-expand-lg fixed-top shadow-sm">

    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand fw-bold"
   href="{{ route('online.index') }}">

    🛍️ {{ $shopSettings->shop_name ?? 'Chalini' }}

</a>


        {{-- Mobile Toggle --}}
        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar">

            <span class="navbar-toggler-icon"></span>

        </button>


        <div class="collapse navbar-collapse"
            id="mainNavbar">


            {{-- =========================
                DESKTOP MENU
            ========================== --}}

            <ul class="navbar-nav ms-auto align-items-center gap-2 d-none d-md-flex">


                {{-- User --}}
                <li class="nav-item">

                    @auth

                        <span class="nav-link">

                            {{ Auth::user()->name }}

                            ({{ Auth::user()->role }})

                        </span>

                    @else

                        <span class="nav-link">

                            ผู้เยี่ยมชม

                        </span>

                    @endauth

                </li>


                {{-- Home --}}
                <li class="nav-item">

                    <a class="nav-link"
                        href="{{ route('online.index') }}">

                        หน้าแรก

                    </a>

                </li>


                {{-- Track Order --}}
                <li class="nav-item">

                    <a class="nav-link"
                        href="{{ route('online.track') }}">

                        ติดตามคำสั่งซื้อ

                    </a>

                </li>


                {{-- Cart --}}
                <li class="nav-item">

                    <a class="nav-link d-flex align-items-center gap-1"
                        href="{{ route('online.cart') }}">

                        <i class="fas fa-shopping-cart"></i>

                        <span>ตะกร้า</span>

                        <span class="cart-total-items badge bg-danger rounded-pill px-2 py-1">

                            {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}

                        </span>

                    </a>

                </li>


                {{-- Login User Menu --}}
                @auth

                    {{-- Account --}}
                    <li class="nav-item">

                        <a class="nav-link"
                            href="{{ route('online.edit', ['member' => Auth::id()]) }}">

                            <i class="fa-solid fa-pen-to-square me-1"></i>

                            จัดการข้อมูล

                        </a>

                    </li>


                    {{-- Logout --}}
                    <li class="nav-item">

                        <form method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                class="nav-link btn btn-link p-0">

                                ออกจากระบบ

                            </button>

                        </form>

                    </li>

                @else

                    {{-- Login --}}
                    <li class="nav-item">

                        <a class="nav-link"
                            href="{{ route('login') }}">

                            เข้าสู่ระบบ

                        </a>

                    </li>

                @endauth

            </ul>


            {{-- =========================
                MOBILE MENU
            ========================== --}}

            <ul class="navbar-nav ms-auto align-items-center gap-2 d-flex d-md-none">


                {{-- User --}}
                <li class="nav-item">

                    @auth

                        <span class="nav-link">

                            {{ Auth::user()->name }}

                            ({{ Auth::user()->role }})

                        </span>

                    @else

                        <span class="nav-link">

                            ผู้เยี่ยมชม

                        </span>

                    @endauth

                </li>


                {{-- Login / Logout --}}
                <li class="nav-item">

                    @auth

                        <form method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                class="btn btn-link nav-link p-0">

                                ออกจากระบบ

                            </button>

                        </form>

                    @else

                        <a href="{{ route('login') }}"
                            class="btn btn-link nav-link p-0">

                            เข้าสู่ระบบ

                        </a>

                    @endauth

                </li>

            </ul>

        </div>

    </div>

</nav>


{{-- =========================
    MOBILE BOTTOM BAR
========================== --}}

<div class="mobile-fixed-bottom-bar d-block d-md-none">

    <div class="d-flex justify-content-around align-items-center h-100">


        {{-- Home --}}
        <a href="{{ route('online.index') }}">

            <i class="fa-solid fa-home"></i>

            <small>หน้าแรก</small>

        </a>


        {{-- Cart --}}
        <a href="{{ route('online.cart') }}"
            class="position-relative">

            <i class="fas fa-shopping-cart"></i>

            <small>ตะกร้า</small>

            <span class="cart-total-items position-absolute badge rounded-pill bg-danger">

                {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}

            </span>

        </a>


        {{-- Track Order --}}
        <a href="{{ route('online.track') }}">

            <i class="fas fa-box"></i>

            <small>คำสั่งซื้อ</small>

        </a>


        {{-- Account --}}
        @auth

            <a href="{{ route('online.edit', ['member' => Auth::id()]) }}">

                <i class="fas fa-user"></i>

                <small>บัญชี</small>

            </a>

        @else

            <a href="{{ route('login') }}">

                <i class="fas fa-sign-in-alt"></i>

                <small>เข้าสู่ระบบ</small>

            </a>

        @endauth

    </div>

</div>


{{-- =========================
    MAIN CONTENT
========================== --}}

<div class="container py-4">

    @yield('content')

    @yield('scripts')

</div>


{{-- =========================
    JAVASCRIPT
========================== --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>

    /*
    |--------------------------------------------------------------------------
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    document.addEventListener('submit', function (event) {

        const form = event.target.closest('.add-to-cart-form');

        if (!form) {
            return;
        }

        event.preventDefault();


        const button = form.querySelector('button[type="submit"]');

        if (button) {
            button.disabled = true;
        }


        const formData = new FormData(form);


        fetch(form.action, {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),

                'Accept': 'application/json'

            },

            body: formData

        })

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'HTTP Error: ' + response.status
                );

            }

            return response.json();

        })

        .then(data => {

            if (data.success) {


                updateCartBadge(
                    data.total_quantity
                );


                Swal.fire({

                    icon: 'success',

                    title: 'เพิ่มสินค้าสำเร็จ!',

                    showConfirmButton: false,

                    timer: 1200,

                    toast: true,

                    position: 'top-end'

                });

            } else {

                Swal.fire(

                    'ผิดพลาด',

                    data.message || 'เพิ่มสินค้าไม่สำเร็จ',

                    'error'

                );

            }

        })

        .catch(error => {

            console.error(error);


            Swal.fire(

                'เกิดข้อผิดพลาด',

                'ไม่สามารถเพิ่มสินค้าได้',

                'error'

            );

        })

        .finally(() => {

            if (button) {

                button.disabled = false;

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE CART BADGE
    |--------------------------------------------------------------------------
    */

    function updateCartBadge(total) {

        document
            .querySelectorAll('.cart-total-items')
            .forEach(badge => {

                badge.textContent = total;

                badge.classList.remove(
                    'bg-secondary',
                    'bg-danger'
                );

                badge.classList.add(

                    Number(total) > 0

                        ? 'bg-danger'

                        : 'bg-secondary'

                );

            });

    }

</script>


@stack('scripts')


</body>

</html>
