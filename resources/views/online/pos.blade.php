@extends('layouts.online')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/">Chalini POS</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item">
                        <span class="nav-link text-white">👋 {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                    </li>

                    @if (Auth::user()->role === 'owner')
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/admin">⚙️ จัดการหลังร้าน</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white">🚪 ออกจากระบบ</button>
                        </form>
                    </li>
                @else
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h2 class="mb-4 text-center">🛒 ร้านค้าออนไลน์</h2>

    {{-- 🔍 ค้นหาและกรองสินค้า --}}
    <form method="GET" action="#" class="row mb-4 g-2">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="">เรียงตาม</option>
                <option value="newest">ใหม่สุด</option>
                <option value="price_low">ราคาต่ำไปสูง</option>
                <option value="price_high">ราคาสูงไปต่ำ</option>
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
                    <a href="javascript:void(0)"> {{-- ป้องกัน error จาก route ที่ยังไม่มี --}}
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

                        {{-- ปุ่มเพิ่มลงตะกร้า --}}
                        <form action="#" method="POST" class="mt-auto"> {{-- ยังไม่ใช้ route จริง --}}
                            @csrf
                            <button type="submit" class="btn btn-primary w-100" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                เพิ่มลงตะกร้า
                            </button>
                        </form>
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
@endsection
