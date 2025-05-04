<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Chalini</title>

    <!-- Bootstrap 5.3.5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        aside a:hover, aside a.active {
            background-color: #495057;
            border-radius: 5px;
            padding-left: 8px;
        }
        .content-wrapper {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a href="{{ url('/') }}" class="navbar-brand fw-bold">
                <i class="bi bi-shop"></i> ชาลินี
            </a>
            <div class="d-flex">
                <a href="{{ route('logout') }}" class="btn btn-outline-light me-2">
                    <i class="bi bi-person"></i> 
                    {{ Auth::user()->name }} ({{ Auth::user()->role }})
                </a>
                <a href="{{ route('logout') }}" 
                class="btn btn-danger" 
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
        <aside class="p-3">
            <h5 class="mb-4">POS System</h5>
            <ul class="nav flex-column">

                <!-- ขายสินค้า (Dropdown) -->
                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('sale*') ? 'active' : '' }} text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapseSale" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapseSale"
                    >
                        <span><i class="bi bi-cart text-white"></i> ขายสินค้า</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseSale">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('sale') ? 'active' : '' }} text-white" href="{{ url('sale') }}">
                                    <i class="bi bi-cash-stack text-white"></i> หน้าขายสินค้า
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('sale/history') ? 'active' : '' }} text-white" href="{{ url('sale/history') }}">
                                    <i class="bi bi-clock-history text-white"></i> ประวัติการขาย
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            
                <!-- จัดการสินค้า (Dropdown) -->
                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('manage_products*') ? 'active' : '' }} text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapseProducts" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapseProducts"
                    >
                        <span><i class="bi bi-box-seam text-white"></i> จัดการสินค้าหน้าร้าน</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseProducts">
                        <ul class="nav flex-column">
                            <!-- ข้อมูลสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products') ? 'active' : '' }} text-white" href="{{ url('manage_products') }}">
                                    <i class="bi bi-list-ul text-white"></i> รายการสินค้าหน้าร้าน
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/create') ? 'active' : '' }} text-white" href="{{ url('manage_products/create') }}">
                                    <i class="bi bi-plus-circle text-white"></i> เพิ่มสินค้าใหม่หน้าร้าน
                                </a>
                            </li>
                
                            <!-- สต็อกสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/stock') ? 'active' : '' }} text-white" href="{{ url('manage_products/stock') }}">
                                    <i class="bi bi-box text-white"></i> สต็อกสินค้า
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/stock/adjust') ? 'active' : '' }} text-white" href="{{ url('manage_products/stock/adjust') }}">
                                    <i class="bi bi-arrow-up-down text-white"></i> ปรับยอดสต็อก
                                </a>
                            </li>
                
                            <!-- ราคาและโปรโมชั่น -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/pricing') ? 'active' : '' }} text-white" href="{{ url('manage_products/pricing') }}">
                                    <i class="bi bi-tag text-white"></i> ตั้งราคาขาย/โปรโมชั่น
                                </a>
                            </li>
                            
                            <!-- หมวดหมู่และหน่วยสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/categories') ? 'active' : '' }} text-white" href="{{ url('manage_products/categories') }}">
                                    <i class="bi bi-folder text-white"></i> จัดการหมวดหมู่
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/unit') ? 'active' : '' }} text-white" href="{{ url('manage_products/unit') }}">
                                    <i class="bi bi-box text-white"></i> หน่วยสินค้า
                                </a>
                            </li>
                
                            <!-- รหัสสินค้า -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/barcode') ? 'active' : '' }} text-white" href="{{ url('manage_products/barcode') }}">
                                    <i class="bi bi-barcode text-white"></i> สร้าง Barcode/QR Code
                                </a>
                            </li>
                
                            <!-- สถานะการขาย -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/status') ? 'active' : '' }} text-white" href="{{ url('manage_products/status') }}">
                                    <i class="bi bi-check-circle text-white"></i> เปิด/ปิดขายออนไลน์
                                </a>
                            </li>
                            
                            <!-- รูปภาพและคำอธิบาย -->
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('manage_products/image') ? 'active' : '' }} text-white" href="{{ url('manage_products/image') }}">
                                    <i class="bi bi-image text-white"></i> จัดการรูปสินค้า
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                
            
                <!-- จัดการยอดขาย (Dropdown เดิม) -->
                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('reports*') ? 'active' : '' }} text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapseReports" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapseReports"
                    >
                        <span><i class="bi bi-bar-chart text-white"></i> จัดการยอดขาย</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseReports">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('reports/daily') ? 'active' : '' }} text-white" href="{{ url('reports/daily') }}">
                                    <i class="bi bi-calendar-day text-white"></i> รายงานรายวัน
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('reports/monthly') ? 'active' : '' }} text-white" href="{{ url('reports/monthly') }}">
                                    <i class="bi bi-calendar-month text-white"></i> รายงานรายเดือน
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('reports/product') ? 'active' : '' }} text-white" href="{{ url('reports/product') }}">
                                    <i class="bi bi-box text-white"></i> รายงานตามสินค้า
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center  text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapseOnline" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapseProducts"
                    >
                        <span><i class="bi bi-box-seam text-white"></i> คำสั่งซื้อออนไลน์</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseOnline">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link  text-white" href="{{ url('manage_products') }}">
                                    <i class="bi bi-list-ul text-white"></i> ออเดอร์
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link  text-white" href="{{ url('manage_products/create') }}">
                                    <i class="bi bi-plus-circle text-white"></i> ประวัติการขาย
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            
                <!-- จัดการพนักงาน (Dropdown เดิม) -->
                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('manage_staff*') ? 'active' : '' }} text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapseStaff" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapseStaff"
                    >
                        <span><i class="bi bi-people text-white"></i> จัดการพนักงาน</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapseStaff">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('staff.create') ? 'active' : '' }} text-white" href="{{ route('staff.create') }}">
                                    <i class="bi bi-person-plus text-white"></i> เพิ่มพนักงานใหม่
                                </a>                               
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('staff.index') ? 'active' : '' }} text-white" href="{{ route('staff.index') }}">
                                    <i class="bi bi-people text-white"></i> รายชื่อพนักงาน
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
                
                {{-- จััดการสมาชิก --}}
                <li class="nav-item mb-2">
                    <a 
                        class="nav-link d-flex justify-content-between align-items-center {{ request()->is('manage_staff*') ? 'active' : '' }} text-white" 
                        data-bs-toggle="collapse" 
                        href="#collapsecustomer" 
                        role="button" 
                        aria-expanded="false" 
                        aria-controls="collapsecustomer"
                    >
                        <span><i class="bi bi-people text-white"></i> จัดการสมาชิก</span>
                        <i class="bi bi-caret-down-fill text-white"></i>
                    </a>
                    <div class="collapse ps-3" id="collapsecustomer">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->is('members.create') ? 'active' : '' }} text-white" href="{{ route('members.create') }}"> <i class="bi bi-person-plus text-white"></i> เพิ่มสมาชิก</a>
                               
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }} text-white" href="{{ route('members.index') }}">
                                    <i class="bi bi-people text-white"></i> รายชื่อสมาชิก
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
            
            </ul>
            
             
            
            
            
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-2">
        Copyright &copy; 2025 ชาลินี
    </footer>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>
