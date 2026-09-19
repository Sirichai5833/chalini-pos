<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - {{ $shopSettings->shop_name ?: 'ชาลินี' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


    @livewireStyles
    <style>
        :root {
            --pos-black: #070707;
            --pos-black-soft: #111111;
            --pos-panel: #181818;
            --pos-yellow: #ffc107;
            --pos-yellow-soft: #ffd95a;
            --pos-border: #343434;
            --pos-text: #f7f7f7;
            --pos-muted: #aaa;
            --sidebar-width: 290px;
            --topbar-height: 55px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: var(--topbar-height);
            margin: 0;
            background: radial-gradient(circle at top right, rgba(255, 193, 7, .08), transparent 28%), var(--pos-black);
            color: var(--pos-text);
            overflow-x: hidden;
        }

        main {
            flex: 1;
            display: flex;
            min-width: 0;
        }

        #sidebar {
            position: sticky;
            top: var(--topbar-height);
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            height: calc(100vh - var(--topbar-height));
            overflow-x: hidden;
            overflow-y: auto;
            white-space: nowrap;
            background: linear-gradient(180deg, #111 0%, #080808 100%);
            color: var(--pos-text);
            border-right: 1px solid rgba(255, 193, 7, .25);
            box-shadow: 10px 0 32px rgba(0, 0, 0, .32);
            transition: width .28s ease, min-width .28s ease, transform .28s ease, padding .28s ease;
            z-index: 1040;
            scrollbar-width: thin;
            scrollbar-color: var(--pos-yellow) #151515;
        }

        #sidebar::-webkit-scrollbar {
            width: 7px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #151515;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: var(--pos-yellow);
            border-radius: 20px;
        }

        #sidebar>h5 {
            position: relative;
            margin: 0 0 1.2rem;
            padding: .9rem 1rem .9rem 3.4rem;
            border: 1px solid rgba(255, 193, 7, .3);
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(255, 193, 7, .18), rgba(255, 193, 7, .03));
            color: var(--pos-yellow);
            font-weight: 800;
            letter-spacing: .4px;
        }

        #sidebar>h5::before {
            content: "\F64D";
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-family: "bootstrap-icons";
            font-size: 1.35rem;
        }

        #sidebar .nav-item {
            width: 100%;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-height: 46px;
            margin-bottom: .28rem;
            padding: .7rem .9rem;
            color: #ddd !important;
            border: 1px solid transparent;
            border-radius: 12px;
            text-decoration: none;
            font-size: .93rem;
            transition: *background* .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }

        #sidebar .nav-link span:first-child {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
        }

        #sidebar .nav-link i {
            color: var(--pos-yellow) !important;
            min-width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        #sidebar .nav-link:hover {
            color: #111 !important;
            background: var(--pos-yellow);
            border-color: var(--pos-yellow);
            transform: translateX(3px);
        }

        #sidebar .nav-link:hover i,
        #sidebar .nav-link.active i {
            color: #111 !important;
        }

        #sidebar .nav-link.active {
            color: #111 !important;
            background: linear-gradient(135deg, var(--pos-yellow), var(--pos-yellow-soft));
            border-color: var(--pos-yellow);
            box-shadow: 0 7px 18px rgba(255, 193, 7, .16);
            font-weight: 700;
        }

        #sidebar .collapse {
            margin: .15rem 0 .55rem .75rem;
            padding-left: .65rem !important;
            border-left: 1px dashed rgba(255, 193, 7, .38);
        }

        #sidebar .collapse .nav-link {
            min-height: 39px;
            padding: .55rem .75rem;
            font-size: .86rem;
            border-radius: 10px;
        }

        #sidebar .badge {
            margin-left: auto;
            border: 1px solid rgba(255, 255, 255, .15);
        }

        #sidebar.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-right: 0 !important;
        }

        #sidebar.collapsed>* {
            opacity: 0;
            pointer-events: none;
        }

        .content-wrapper {
            flex: 1;
            min-width: 0;
            padding: 1.5rem !important;
            background: radial-gradient(circle at 92% 4%, rgba(255, 193, 7, .08), transparent 23%), #f4f4f4;
            color: #1b1b1b;
            transition: *background* .28s ease, color .28s ease;
        }

        
        /* Dark theme: เปลี่ยนเฉพาะพื้นหลังของพื้นที่เนื้อหา */
        .content-wrapper.dark-mode {
            background: radial-gradient(circle at 92% 4%, rgba(255, 193, 7, .10), transparent 24%), #0d0d0d;
            color: #1b1b1b;
        }

.content-wrapper .btn-primary,
        .content-wrapper .btn-success,
        .content-wrapper .btn-warning {
            background: var(--pos-yellow);
            border-color: var(--pos-yellow);
            color: #111;
            font-weight: 700;
        }

        .content-wrapper .btn-primary:hover,
        .content-wrapper .btn-success:hover,
        .content-wrapper .btn-warning:hover {
            background: var(--pos-yellow-soft);
            border-color: var(--pos-yellow-soft);
            color: #000;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: var(--topbar-height) 0 0 0;
            display: none;
            background: rgba(0, 0, 0, .58);
            backdrop-filter: blur(2px);
            z-index: 1035;
        }

        footer {
            background: #080808 !important;
            border-top: 1px solid rgba(255, 193, 7, .24);
            min-height: 8px;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                left: 0;
                transform: translateX(-102%);
            }

            #sidebar.mobile-open {
                transform: translateX(0);
            }

            #sidebar.collapsed {
                width: var(--sidebar-width) !important;
                min-width: var(--sidebar-width) !important;
                padding: 1rem !important;
                transform: translateX(-102%);
            }

            #sidebar.collapsed.mobile-open {
                transform: translateX(0);
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .content-wrapper {
                width: 100%;
                padding: 1rem !important;
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --sidebar-width: min(88vw, 310px);
            }

            .content-wrapper {
                padding: .8rem !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @include('layouts.partials.topbar')

    <main>
        <aside id="sidebar" class="p-3">
            @auth
                @php
                    $role = auth()->user()->role;

                    $isAdmin = $role === 'admin';
                    $isOwner = $role === 'owner';
                    $isManager = $role === 'manager';
                    $isStaff = $role === 'staff';

                    $canSell = in_array($role, ['admin', 'owner', 'manager', 'staff'], true);

                    $canManageProducts = in_array($role, ['admin', 'owner', 'manager'], true);

                    $canViewStockAlerts = in_array($role, ['admin', 'owner', 'manager', 'staff'], true);

                    $canViewReports = in_array($role, ['admin', 'owner', 'manager'], true);

                    $canManageOnlineOrders = in_array($role, ['admin', 'owner', 'manager', 'staff'], true);

                    $canManageStaff = in_array($role, ['admin', 'owner', 'manager'], true);

                    $canManageMembers = in_array($role, ['admin', 'owner', 'manager', 'staff'], true);

                    $canViewMemberAudits = in_array($role, ['admin', 'owner', 'manager'], true);

                    $canManageShopSettings = in_array($role, ['admin', 'owner'], true);

                    $canViewManagerDashboard = in_array($role, ['admin', 'owner', 'manager'], true);
                @endphp

                <h5 class="mb-4">POS System</h5>

                <ul class="nav flex-column">
                    @if ($canSell)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->is('sale*') || request()->routeIs('staff.credit.sales.*', 'staff.sales.history') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseSale" role="button" aria-expanded="false"
                                aria-controls="collapseSale">
                                <span>
                                    <i class="bi bi-cart text-white"></i>
                                    ขายสินค้า
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseSale">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('sale') ? 'active' : '' }} text-white"
                                            href="{{ url('sale') }}">
                                            <i class="bi bi-cash-stack text-white"></i>
                                            หน้าขายสินค้า
                                        </a>
                                    </li>

                                    {{-- <li class="nav-item mb-1">
                                        <a
                                            class="nav-link {{ request()->routeIs('staff.credit.sales.*') ? 'active' : '' }} text-white"
                                            href="{{ route('staff.credit.sales.index') }}"
                                        >
                                            <i class="bi bi-journal-text text-white"></i>
                                            รายการเงินเชื่อ
                                        </a>
                                    </li> --}}

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('staff.sales.history') ? 'active' : '' }} text-white"
                                            href="{{ route('staff.sales.history') }}">
                                            <i class="bi bi-clock-history text-white"></i>
                                            ประวัติการขาย
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canManageProducts)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('product.product.*', 'product.products.*', 'product.stock*', 'categories.*', 'suppliers.*', 'staff.stock-check.*') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseProducts" role="button" aria-expanded="false"
                                aria-controls="collapseProducts">
                                <span>
                                    <i class="bi bi-box-seam text-white"></i>
                                    จัดการสินค้า
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseProducts">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('product.product.index') ? 'active' : '' }} text-white"
                                            href="{{ route('product.product.index') }}">
                                            <i class="bi bi-list-ul text-white"></i>
                                            รายการสินค้า/เพิ่มสินค้าใหม่
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('product.products.add-stock-form') ? 'active' : '' }} text-white"
                                            href="{{ route('product.products.add-stock-form') }}">
                                            <i class="bi bi-box-arrow-in-down text-white"></i>
                                            รับสินค้าเข้าสต็อก
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('product.stock.to.store') ? 'active' : '' }} text-white"
                                            href="{{ route('product.stock.to.store') }}">
                                            <i class="bi bi-box-arrow-up-right text-white"></i>
                                            ย้ายสินค้าไปหน้าร้าน
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }} text-white"
                                            href="{{ route('categories.index') }}">
                                            <i class="bi bi-folder text-white"></i>
                                            จัดการประเภทสินค้า
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }} text-white"
                                            href="{{ route('suppliers.index') }}">
                                            <i class="bi bi-truck-front-fill text-white"></i>
                                            จัดการผู้จำหน่าย
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('staff.stock-check.*') ? 'active' : '' }} text-white"
                                            href="{{ route('staff.stock.check.index') }}">
                                            <i class="bi bi-clipboard-check text-white"></i>
                                            ตรวจนับสต็อก
                                        </a>
                                    </li>

                                    @if ($isAdmin || $isOwner)
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('product.barcodes.index') ? 'active' : '' }} text-white"
                                                href="{{ route('product.barcodes.index') }}">
                                                <i class="bi bi-upc-scan"></i>
                                                Barcode
                                            </a>
                                        </li>

                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('product.products.allHistory') ? 'active' : '' }} text-white"
                                                href="{{ route('product.products.allHistory') }}">
                                                <i class="bi bi-clock-history text-white"></i>
                                                ประวัติการแก้ไขสินค้า
                                            </a>
                                        </li>
                                    @endif

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('product.stock-in-history') ? 'active' : '' }} text-white"
                                            href="{{ route('product.stock-in-history') }}">
                                            <i class="bi bi-journal-text text-white"></i>
                                            ประวัติการนำสินค้าเข้า
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('product.movement-history') ? 'active' : '' }} text-white"
                                            href="{{ route('product.movement-history') }}">
                                            <i class="bi bi-arrow-left-right text-white"></i>
                                            ประวัติเคลื่อนย้ายสินค้า
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canViewStockAlerts)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('notification*') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#Notification" role="button" aria-expanded="false"
                                aria-controls="Notification">
                                <span>
                                    <i class="bi bi-bell text-white"></i>
                                    การแจ้งเตือน

                                    @php
                                        $totalNoti = ($lowStockCount ?? 0) + ($expireCount ?? 0);
                                    @endphp

                                    @if ($totalNoti > 0)
                                        <span class="badge bg-danger ms-2">
                                            {{ $totalNoti }}
                                        </span>
                                    @endif
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="Notification">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('notification.OutStock') ? 'active' : '' }} text-white"
                                            href="{{ route('notification.OutStock') }}">
                                            <i class="bi bi-exclamation-triangle text-white"></i>
                                            สินค้าใกล้หมด

                                            @if (($lowStockCount ?? 0) > 0)
                                                <span class="badge bg-danger ms-2">
                                                    {{ $lowStockCount }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('notification.expire') ? 'active' : '' }} text-white"
                                            href="{{ route('notification.expire') }}">
                                            <i class="bi bi-calendar-x text-white"></i>
                                            สินค้าใกล้หมดอายุ

                                            @if (($expireCount ?? 0) > 0)
                                                <span class="badge bg-warning text-dark ms-2">
                                                    {{ $expireCount }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canViewReports)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('reports*') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseReports" role="button" aria-expanded="false"
                                aria-controls="collapseReports">
                                <span>
                                    <i class="bi bi-bar-chart text-white"></i>
                                    รายงานยอดขาย
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseReports">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }} text-white"
                                            href="{{ route('reports.daily') }}">
                                            <i class="bi bi-calendar-day text-white"></i>
                                            รายงานยอดขาย
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canManageOnlineOrders)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('orders.*') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseOnline" role="button" aria-expanded="false"
                                aria-controls="collapseOnline">
                                <span class="d-flex align-items-center gap-1">
                                    <i class="bi bi-bag-check text-white"></i>
                                    คำสั่งซื้อออนไลน์
                                    @livewire('pending-order-count-badge')
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseOnline">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('orders.list') ? 'active' : '' }} text-white"
                                            href="{{ route('orders.list') }}">
                                            <i class="bi bi-list-ul text-white"></i>
                                            ออเดอร์ทั้งหมด
                                            @livewire('pending-order-count-badge')
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('orders.my') ? 'active' : '' }} text-white"
                                            href="{{ route('orders.my') }}">
                                            <i class="bi bi-person-check text-white"></i>
                                            ออเดอร์ที่รับมา
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('orders.history') ? 'active' : '' }} text-white"
                                            href="{{ route('orders.history') }}">
                                            <i class="bi bi-clock-history text-white"></i>
                                            ประวัติคำสั่งซื้อ
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canManageStaff)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('staff.create', 'staff.index') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseStaff" role="button" aria-expanded="false"
                                aria-controls="collapseStaff">
                                <span>
                                    <i class="bi bi-people text-white"></i>
                                    จัดการพนักงาน
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseStaff">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('staff.create') ? 'active' : '' }} text-white"
                                            href="{{ route('staff.create') }}">
                                            <i class="bi bi-person-plus text-white"></i>
                                            เพิ่มพนักงานใหม่
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }} text-white"
                                            href="{{ route('staff.index') }}">
                                            <i class="bi bi-people text-white"></i>
                                            รายชื่อพนักงาน
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canManageMembers)
                        <li class="nav-item mb-2">
                            <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('members.*', 'staff.audits') ? 'active' : '' }} text-white"
                                data-bs-toggle="collapse" href="#collapseCustomer" role="button" aria-expanded="false"
                                aria-controls="collapseCustomer">
                                <span>
                                    <i class="bi bi-person-vcard text-white"></i>
                                    จัดการสมาชิก
                                </span>

                                <i class="bi bi-caret-down-fill text-white"></i>
                            </a>

                            <div class="collapse ps-3" id="collapseCustomer">
                                <ul class="nav flex-column">
                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('members.create') ? 'active' : '' }} text-white"
                                            href="{{ route('members.create') }}">
                                            <i class="bi bi-person-plus text-white"></i>
                                            เพิ่มสมาชิก
                                        </a>
                                    </li>

                                    <li class="nav-item mb-1">
                                        <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }} text-white"
                                            href="{{ route('members.index') }}">
                                            <i class="bi bi-people text-white"></i>
                                            รายชื่อสมาชิก
                                        </a>
                                    </li>

                                    @if ($canViewMemberAudits)
                                        <li class="nav-item mb-1">
                                            <a class="nav-link {{ request()->routeIs('staff.audits') ? 'active' : '' }} text-white"
                                                href="{{ route('staff.audits') }}">
                                                <i class="bi bi-clock-history text-white"></i>
                                                ประวัติการแก้ไขข้อมูลสมาชิก
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if ($canManageShopSettings)
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('staff.settings') ? 'active' : '' }} text-white"
                                href="{{ route('staff.settings') }}">
                                <i class="bi bi-gear-fill text-white"></i>
                                ตั้งค่าร้าน
                            </a>
                        </li>

                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('admin.online-users.*') ? 'active' : '' }} text-white"
                                href="{{ route('admin.online-users.index') }}">
                                <i class="bi bi-people-fill text-white"></i>
                                ผู้ใช้งานออนไลน์
                            </a>
                        </li>
                    @endif

                    @if ($canViewManagerDashboard)
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} text-white"
                                href="{{ route('manager.dashboard') }}">
                                <i class="bi bi-bar-chart-fill text-white"></i>
                                สรุปยอดขายแต่ละกะ
                            </a>
                        </li>
                    @endif

                    @php
                        $hasOpenShift = \App\Models\Shift::query()
                            ->where('user_id', auth()->id())
                            ->where('status', 'open')
                            ->exists();
                    @endphp

                    <li class="nav-item mb-1">
                        @if ($hasOpenShift)
                            <a class="nav-link {{ request()->routeIs('shift.close') ? 'active' : '' }} text-white"
                                href="{{ route('shift.close') }}">
                                <i class="bi bi-door-closed-fill text-white"></i>
                                ปิดกะ
                            </a>
                        @else
                            <a class="nav-link text-white-50 opacity-50 pe-none" href="#" tabindex="-1"
                                aria-disabled="true" title="กรุณาเปิดกะก่อนปิดกะ">
                                <i class="bi bi-door-closed-fill"></i>
                                ปิดกะ

                                <small class="badge bg-secondary ms-auto" style="font-size: .65rem;">
                                    ยังไม่เปิดกะ
                                </small>
                            </a>
                        @endif
                    </li>
                </ul>
            @endauth


        </aside>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="content-wrapper w-full px-4" id="mainContentWrapper">
            @yield('flash-messages')
            @yield('validation-errors')
            @yield('content')
        </div>
        @livewireScripts
    </main>
    <footer class="bg-dark text-white text-center py-2">
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const themeToggleBtn = document.getElementById('themeToggle');
            const mainContentWrapper = document.getElementById('mainContentWrapper');
            const mobileBreakpoint = 992;

            const isMobile = () => window.innerWidth < mobileBreakpoint;

            function closeMobileSidebar() {
                sidebar?.classList.remove('mobile-open');
                backdrop?.classList.remove('show');
                document.body.classList.remove('overflow-hidden');
            }

            function toggleSidebar() {
                if (!sidebar) return;

                if (isMobile()) {
                    sidebar.classList.toggle('mobile-open');
                    const opened = sidebar.classList.contains('mobile-open');
                    backdrop?.classList.toggle('show', opened);
                    document.body.classList.toggle('overflow-hidden', opened);
                    return;
                }

                sidebar.classList.toggle('collapsed');
                localStorage.setItem(
                    'sidebarCollapsed',
                    sidebar.classList.contains('collapsed') ? '1' : '0'
                );
            }

            if (sidebar && !isMobile() && localStorage.getItem('sidebarCollapsed') === '1') {
                sidebar.classList.add('collapsed');
            }

            toggleBtn?.addEventListener('click', toggleSidebar);
            backdrop?.addEventListener('click', closeMobileSidebar);

            sidebar?.querySelectorAll('a.nav-link[href]:not([href="#"])').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (isMobile() && !link.hasAttribute('data-bs-toggle')) {
                        closeMobileSidebar();
                    }
                });
            });

            window.addEventListener('resize', function() {
                if (!isMobile()) closeMobileSidebar();
            });

            const savedTheme = localStorage.getItem('contentTheme') || 'dark';
            applyTheme(savedTheme);

            themeToggleBtn?.addEventListener('click', function() {
                const nextTheme = mainContentWrapper?.classList.contains('dark-mode') ? 'light' : 'dark';
                localStorage.setItem('contentTheme', nextTheme);
                applyTheme(nextTheme);
            });

            function applyTheme(theme) {
                if (!mainContentWrapper) return;
                mainContentWrapper.classList.toggle('dark-mode', theme === 'dark');
                updateThemeToggleButton(theme);
            }

            function updateThemeToggleButton(theme) {
                if (!themeToggleBtn) return;

                const icon = themeToggleBtn.querySelector('i');
                const text = themeToggleBtn.querySelector('span');

                icon?.classList.toggle('bi-sun-fill', theme === 'dark');
                icon?.classList.toggle('bi-moon-fill', theme !== 'dark');

                if (text) {
                    text.textContent = theme === 'dark' ?
                        'เปลี่ยนธีม (สว่าง)' :
                        'เปลี่ยนธีม (มืด)';
                }
            }

            document.querySelectorAll('#sidebar .collapse').forEach(function(collapseElement) {
                if (collapseElement.querySelector('.nav-link.active')) {
                    bootstrap.Collapse.getOrCreateInstance(collapseElement, {
                        toggle: false
                    }).show();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>