<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Chalini</title>

    <!-- Bootstrap 5.3.5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">




    @livewireStyles
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 55px;
            /* ปรับตามความสูงของ navbar */
        }

        main {
            flex: 1;
            display: flex;
        }

        aside {
            width: 250px;
            background-color: #212529;
            color: white;
            min-height: 100vh;
        }

        aside a {
            color: white;
            text-decoration: none;
        }

        aside a:hover,
        aside a.active {
            background-color: #495057;
            border-radius: 5px;
            padding-left: 8px;
        }

        .content-wrapper {
            flex: 1;
            padding: 20px;
            display: block;
        }


        /* Sidebar ปกติ */
        #sidebar {
            width: max-content;
            /* ปรับตามขนาดจริงของคุณ */
            transition: all 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        /* เมื่อหุบ */
        #sidebar.collapsed {
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        /* ซ่อนเนื้อหาด้านในแบบนุ่มนวล */
        #sidebar.collapsed>* {
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <livewire:styles />
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">

        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="{{ url('/') }}" class="navbar-brand fw-bold me-2 mb-0">
                    <i class="bi bi-shop"></i> ชาลินี
                </a>
                <button class="btn btn-outline-light" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="d-flex">
                <a href="{{ route('staff.edit', ['member' => Auth::user()->id]) }}" class="btn btn-outline-light me-2">
                    <i class="bi bi-person"></i>
                    {{ Auth::user()->name }} ({{ Auth::user()->role }})
                </a>

                <a href="{{ route('logout') }}" class="btn btn-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

    </nav>

    <!-- Sidebar + Content -->
    <main>

        <aside id="sidebar" class="p-3">
            <h5 class="mb-4">POS System</h5>
            <ul class="nav flex-column">

                <!-- ขายสินค้า (Dropdown) -->
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('sale*') ? 'active' : '' }} text-white"
                        data-bs-toggle="collapse" href="#collapseSale" role="button" aria-expanded="false"
                        aria-controls="collapseSale">
                        <span><i class="bi bi-cart text-white"></i> ขายสินค้า</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseSale">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('sale') ? 'active' : '' }} text-white"
                                    href="{{ url('sale') }}">
                                    <i class="bi bi-cash-stack text-white"></i> หน้าขายสินค้า
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('staff.sales.history') ? 'active' : '' }} text-white"
                                    href="{{ route('staff.sales.history') }}">
                                    <i class="bi bi-clock-history text-white"></i> ประวัติการขาย
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- จัดการสินค้า (Dropdown) -->
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('product.product.*') ? 'active' : '' }} text-white"
                        data-bs-toggle="collapse" href="#collapseProducts" role="button" aria-expanded="false"
                        aria-controls="collapseProducts">
                        <span><i class="bi bi-box-seam text-white"></i> จัดการสินค้า</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseProducts">
                        <ul class="nav flex-column">
                            <!-- ข้อมูลสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.product.index') ? 'active' : '' }} text-white"
                                    href="{{ route('product.product.index') }}">
                                    <i class="bi bi-list-ul text-white"></i> รายการสินค้าหน้าร้าน
                                </a>
                            </li>
                            {{-- <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.product.create') ? 'active' : '' }} text-white"
                                    href="{{ route('product.product.create') }}">
                                    <i class="bi bi-plus-circle text-white"></i> เพิ่มสินค้าใหม่หน้าร้าน
                                </a>
                            </li> --}}

                            <!-- สต็อกสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.products.add-stock-form') ? 'active' : '' }} text-white"
                                    href="{{ route('product.products.add-stock-form') }}">
                                    <i class="bi bi-list-ul text-white"></i> เพิ่มสินค้าเข้าหน้าบ้าน/คลังสินค้า
                                </a>
                            </li>

                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.indexstock') ? 'active' : '' }} text-white"
                                    href="{{ route('product.indexstock') }}">
                                    <i class="bi bi-box text-white"></i> รายการสินค้าในสต็อก
                                </a>
                            </li>

                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.stock.to.store') ? 'active' : '' }} text-white"
                                    href="{{ route('product.stock.to.store') }}">
                                    <i class="bi bi-box-arrow-up-right text-white"></i> ย้ายสินค้าไปหน้าร้าน
                                </a>
                            </li>

                            <!-- หมวดหมู่และหน่วยสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }} text-white"
                                    href="{{ route('categories.index') }}">
                                    <i class="bi bi-folder text-white"></i> จัดการหมวดหมู่
                                </a>
                            </li>
                            {{-- <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/unit') ? 'active' : '' }} text-white" href="{{ route('units.index')}}">
                                    <i class="bi bi-box text-white"></i> หน่วยสินค้า
                                </a>
                            </li> --}}

                            <!-- รหัสสินค้า -->
                            @unless (auth()->user()->role !== 'admin')
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('product.barcodes.index') ? 'active' : '' }} text-white"
                                        href="{{ route('product.barcodes.index') }}">
                                       <i class="bi-upc-scan"></i>  Barcode
                                    </a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('product.products.allHistory') ? 'active' : '' }} text-white"
                                        href="{{ route('product.products.allHistory') }}">
                                        <i class="bi bi-clock-history text-white"></i> ประวัติการแก้ไขสินค้า
                                    </a>
                                </li>
                            @endunless
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('product.stock-in-history') ? 'active' : '' }} text-white"
                                    href="{{ route('product.stock-in-history') }}">
                                    <i class="bi bi-journal-text text-white"></i> ประวัติการเพิ่มสินค้า
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('notification*') ? 'active' : '' }} text-white"
                        data-bs-toggle="collapse" href="#Notification" role="button" aria-expanded="false"
                        aria-controls="Notification">

                        <span>
                            <i class="bi bi-box-seam text-white"></i> การแจ้งเตือน
                            @php
                                $totalNoti = ($lowStockCount ?? 0) + ($expireCount ?? 0);
                            @endphp
                            @if ($totalNoti > 0)
                                <span class="badge bg-danger ms-2">{{ $totalNoti }}</span>
                            @endif
                        </span>

                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>

                    <div class="collapse ps-3" id="Notification">
                        <ul class="nav flex-column">
                            <!-- ข้อมูลสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('notification.OutStock') ? 'active' : '' }} text-white"
                                    href="{{ route('notification.OutStock') }}">
                                    <i class="bi bi-list-ul text-white"></i> สินค้าใกล้หมด
                                    @if (isset($lowStockCount) && $lowStockCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $lowStockCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('notification.expire') ? 'active' : '' }} text-white"
                                    href="{{ route('notification.expire') }}">
                                    <i class="bi bi-plus-circle text-white"></i> สินค้าใกล้หมดอายุ
                                    @if (isset($expireCount) && $expireCount > 0)
                                        <span class="badge bg-warning text-dark ms-2">{{ $expireCount }}</span>
                                    @endif
                                </a>
                            </li>



                        </ul>
                    </div>
                </li>



                <!-- จัดการยอดขาย (Dropdown เดิม) -->
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('reports*') ? 'active' : '' }} text-white"
                        data-bs-toggle="collapse" href="#collapseReports" role="button" aria-expanded="false"
                        aria-controls="collapseReports">
                        <span><i class="bi bi-bar-chart text-white"></i> จัดการยอดขาย</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseReports">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }} text-white"
                                    href="{{ route('reports.daily') }}">
                                    <i class="bi bi-calendar-day text-white"></i> รายงานยอดขาย
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center text-white"
                        data-bs-toggle="collapse" href="#collapseOnline" role="button" aria-expanded="false"
                        aria-controls="collapseOnline">
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-box-seam text-white"></i> คำสั่งซื้อออนไลน์
                            @livewire('pending-order-count-badge')
                        </span>

                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseOnline">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('orders.list') ? 'active' : '' }} text-white"
                                    href="{{ route('orders.list') }}">
                                    <i class="bi bi-list-ul text-white"></i> ออเดอร์
                                    @livewire('pending-order-count-badge')
                                </a>
                            </li>

                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }} text-white"
                                    href="{{ route('orders.history') }}">
                                    <i class="bi bi-plus-circle text-white"></i> ประวัติการขาย
                                </a>
                            </li>

                            <!-- สถานะการขาย -->
                            {{-- <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/status') ? 'active' : '' }} text-white"
                                    href="{{ url('manage_products/status') }}">
                                    <i class="bi bi-check-circle text-white"></i> เปิด/ปิดขายออนไลน์
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </li>

                <!-- จัดการพนักงาน (Dropdown เดิม) -->
                @if (Auth::user()->role !== 'staff')
                    <li class="nav-item mb-2">
                        <a class="nav-link d-flex justify-content-between align-items-center {{ request()->Is('staff/create','staff') ? 'active' : '' }} text-white"
                            data-bs-toggle="collapse" href="#collapseStaff" role="button" aria-expanded="false"
                            aria-controls="collapseStaff">
                            <span><i class="bi bi-people text-white"></i> จัดการพนักงาน</span>
                            <i class="bi bi-caret-down-fill text-white"></i>
                        </a>
                        <div class="collapse ps-3" id="collapseStaff">
                            <ul class="nav flex-column">
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('staff.create') ? 'active' : '' }} text-white"
                                        href="{{ route('staff.create') }}">
                                        <i class="bi bi-person-plus text-white"></i> เพิ่มพนักงานใหม่
                                    </a>
                                </li>
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }} text-white"
                                        href="{{ route('staff.index') }}">
                                        <i class="bi bi-people text-white"></i> รายชื่อพนักงาน
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                {{-- จััดการสมาชิก --}}
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('manage_staff*','staff/audits') ? 'active' : '' }} text-white"
                        data-bs-toggle="collapse" href="#collapsecustomer" role="button" aria-expanded="false"
                        aria-controls="collapsecustomer">
                        <span><i class="bi bi-people text-white"></i> จัดการสมาชิก</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapsecustomer">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('members.create') ? 'active' : '' }} text-white"
                                    href="{{ route('members.create') }}"> <i
                                        class="bi bi-person-plus text-white"></i> เพิ่มสมาชิก</a>

                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }} text-white"
                                    href="{{ route('members.index') }}">
                                    <i class="bi bi-people text-white"></i> รายชื่อสมาชิก
                                </a>
                            </li>
                            @if (Auth::user()->role !== 'staff')
                                <li class="nav-item mb-1">
                                    <a class="nav-link {{ request()->routeIs('staff.audits') ? 'active' : '' }} text-white"
                                        href="{{ route('staff.audits') }}">
                                        <i class="bi bi-people text-white"></i> ประวัติการแก้ไขข้อมูลสมาชิก
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

            </ul>





        </aside>

        <div class="content-wrapper w-full px-4">
            @yield('content')

        </div>
        @livewireScripts
    </main>
    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-2">
        Copyright &copy; 2025 ชาลินี
    </footer>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js">
        < /> <
        script src = "https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js" >
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });
        });
    </script>

    @stack('scripts')
    @livewireScripts
</body>

</html>
