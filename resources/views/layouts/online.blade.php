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
        --primary-color: #ffd43b;
        --accent-color: #ffd43b;
        --secondary-color: #e0b400;
        --bg-color: #090909;
        --surface-color: #141414;
        --soft-color: #202020;
        --text-color: #f7f7f7;
        --muted-color: #a7a7a7;
        --border-color: rgba(255, 212, 59, .22);
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        min-height: 100vh;
        padding-top: 78px;
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--bg-color);
        color: var(--text-color);
        font-family: 'Sarabun', sans-serif;
    }

    .navbar {
        min-height: 68px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(9, 9, 9, .94) !important;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .38) !important;
        backdrop-filter: blur(14px);
    }

    .navbar .container {
        gap: .75rem;
    }

    .navbar-brand {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: var(--primary-color) !important;
        font-size: 1.15rem;
        font-weight: 900 !important;
        letter-spacing: .01em;
        transition: .2s ease;
    }

    .navbar-brand:hover {
        color: #ffea82 !important;
        transform: translateY(-1px);
    }

    .navbar-brand::before {
        content: "";
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--primary-color);
        box-shadow: 0 0 14px rgba(255, 212, 59, .75);
    }

    .navbar-toggler {
        border: 1px solid var(--border-color);
        background: rgba(255, 212, 59, .08);
        box-shadow: none !important;
    }

    .navbar-toggler-icon {
        filter: invert(88%) sepia(69%) saturate(1097%) hue-rotate(342deg) brightness(104%);
    }

    .nav-link,
    .navbar .btn-link {
        position: relative;
        padding: .55rem .75rem !important;
        border-radius: 10px;
        color: #e9e9e9 !important;
        font-size: .88rem;
        font-weight: 700;
        text-decoration: none !important;
        transition: .2s ease;
    }

    .nav-link:hover,
    .nav-link:focus,
    .navbar .btn-link:hover {
        background: rgba(255, 212, 59, .10);
        color: var(--primary-color) !important;
        transform: translateY(-1px);
    }

    .nav-link i {
        color: var(--primary-color);
    }

    .cart-total-items {
        min-width: 21px;
        border: 1px solid #111;
        background: var(--primary-color) !important;
        color: #111 !important;
        font-weight: 900;
    }

    .btn-primary {
        border-color: var(--primary-color);
        border-radius: 12px;
        background: linear-gradient(135deg, #ffe66b, var(--primary-color));
        color: #111;
        font-weight: 850;
        box-shadow: 0 8px 20px rgba(255, 212, 59, .14);
        transition: .2s ease;
    }

    .btn-primary:hover,
    .btn-primary:focus {
        border-color: #ffea82;
        background: linear-gradient(135deg, #fff08f, #ffc800);
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 212, 59, .22);
    }

    .mobile-fixed-bottom-bar {
        position: fixed;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1055;
        height: 66px;
        padding: .45rem;
        border-top: 1px solid var(--border-color);
        background: rgba(12, 12, 12, .96);
        box-shadow: 0 -10px 28px rgba(0, 0, 0, .42);
        backdrop-filter: blur(14px);
    }

    .mobile-fixed-bottom-bar a {
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        color: #a9a9a9;
        font-size: .72rem;
        text-decoration: none;
        transition: .2s ease;
    }

    .mobile-fixed-bottom-bar a:hover,
    .mobile-fixed-bottom-bar a:focus {
        background: rgba(255, 212, 59, .09);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .mobile-fixed-bottom-bar i {
        margin-bottom: 3px;
        color: var(--primary-color);
        font-size: 1.25rem;
        line-height: 1;
    }

    .mobile-fixed-bottom-bar small {
        margin-top: 1px;
        line-height: 1.1;
        font-weight: 700;
    }

    .mobile-fixed-bottom-bar .badge {
        top: 2px !important;
        right: 14px !important;
        min-width: 20px;
        border: 1px solid #111;
        background: var(--primary-color) !important;
        color: #111 !important;
        font-size: .6rem;
        font-weight: 900;
    }

    body > .container.py-4 {
        max-width: 1320px;
    }

    .swal2-popup {
        border: 1px solid var(--border-color) !important;
        border-radius: 18px !important;
        background: #171717 !important;
        color: var(--text-color) !important;
    }

    .swal2-title,
    .swal2-html-container {
        color: var(--text-color) !important;
    }

    .swal2-confirm {
        border: none !important;
        background: var(--primary-color) !important;
        color: #111 !important;
        font-weight: 850 !important;
    }

    .swal2-cancel {
        background: #333 !important;
        color: #fff !important;
    }

    @media (max-width: 767.98px) {
        body {
            padding-top: 70px;
            padding-bottom: 78px;
        }

        .navbar {
            min-height: 62px;
        }

        .navbar-collapse {
            margin-top: .75rem;
            padding: .85rem;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            background: #111;
            box-shadow: 0 14px 30px rgba(0, 0, 0, .35);
        }

        .navbar-nav {
            align-items: stretch !important;
        }

        .navbar-nav .nav-item,
        .navbar-nav .nav-link,
        .navbar-nav form,
        .navbar-nav button {
            width: 100%;
        }

        body > .container.py-4 {
            padding-right: .8rem;
            padding-left: .8rem;
        }
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

     {{ $shopSettings->shop_name ?? 'Chalini' }}

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

                        <i class="fa-solid fa-house me-1"></i> หน้าแรก

                    </a>

                </li>


                {{-- Track Order --}}
                <li class="nav-item">

                    <a class="nav-link"
                        href="{{ route('online.track') }}">

                        <i class="fa-solid fa-box me-1"></i> ติดตามคำสั่งซื้อ

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

                                <i class="fa-solid fa-right-from-bracket me-1"></i> ออกจากระบบ

                            </button>

                        </form>

                    </li>

                @else

                    {{-- Login --}}
                    <li class="nav-item">

                        <a class="nav-link"
                            href="{{ route('login') }}">

                            <i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ

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

                                <i class="fa-solid fa-right-from-bracket me-1"></i> ออกจากระบบ

                            </button>

                        </form>

                    @else

                        <a href="{{ route('login') }}"
                            class="btn btn-link nav-link p-0">

                            <i class="fa-solid fa-right-to-bracket me-1"></i> เข้าสู่ระบบ

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

                    position: 'top-end',
                    background: '#171717',
                    color: '#f7f7f7',
                    iconColor: '#ffd43b'

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