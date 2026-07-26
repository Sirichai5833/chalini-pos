<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <a href="{{ url('/') }}" class="navbar-brand fw-bold me-2 mb-0">
                <i class="bi bi-shop"></i> {{ $shopName }}
            </a>
            <button class="btn btn-outline-light me-2" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <button class="btn btn-outline-light" id="themeToggle">
                <i class="bi bi-moon-fill"></i> <span class="d-none d-md-inline">เปลี่ยนธีม</span>
            </button>
        </div>

        <div class="d-flex">
            @php
                $profileUrl = Auth::user()->role === 'member'
                    ? route('online.edit', ['member' => Auth::user()->id])
                    : route('staff.edit', ['member' => Auth::user()->id]);
            @endphp
            <a href="{{ $profileUrl }}" class="btn btn-outline-light me-2">
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
