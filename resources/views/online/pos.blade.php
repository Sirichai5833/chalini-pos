@extends('layouts.online')

@section('content')
    @if (session('success'))
        <x-sweet-alert icon="success" title="สำเร็จ!" text="{{ session('success') }}" confirm-button-text="ตกลง" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="ผิดพลาด" text="{{ session('error') }}" confirm-button-text="ตกลง" />
    @endif

   <style>
    :root {
        --primary-color: #007849; /* เขียวเซเว่น */
        --accent-color: #FF7F00;  /* ส้มเซเว่น */
        --danger-color: #ED1C24; /* แดงเซเว่น */
        --light-bg: #fff9f4;
        --text-dark: #212529;
    }

    body {
        font-family: 'Sarabun', sans-serif;
        background-color: var(--light-bg);
        padding-top: 70px;
    }

    .navbar {
        background-color: var(--primary-color) !important;
    }

    .navbar-brand,
    .nav-link,
    .btn-link {
        color: #fff !important;
    }

    .btn-orange {
        background-color: var(--accent-color);
        color: #fff;
        border: none;
    }

    .btn-orange:hover {
        background-color: #e76b00;
        color: #fff;
    }

    .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
    }

    .btn-outline-secondary:hover {
    background-color: var(--accent-color);
    color: white;
}
.overflow-auto a {
    scroll-snap-align: start;
}


    .btn-primary:hover {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .category-btn.active {
        background-color: var(--accent-color) !important;
        color: white;
    }

    .card .price {
        color: var(--accent-color);
        font-weight: bold;
    }

    .add-to-cart {
    cursor: pointer;
}


    .card-out-of-stock {
        opacity: 0.6;
    }

    .badge-low-stock {
        background-color: #ffc107;
        color: black;
        font-size: 0.75rem;
        border-radius: 5px;
        padding: 2px 6px;
    }

    .badge-out-of-stock {
        background-color: var(--danger-color);
        color: white;
        font-size: 0.75rem;
        border-radius: 5px;
        padding: 2px 6px;
    }

    .pagination svg {
        width: 1em;
        height: 1em;
    }

    .pagination {
        font-size: 1rem;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
    }

    .text-orange {
        color: var(--accent-color);
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


    <div class="container-fluid py-5 mb-3" style="background: linear-gradient(135deg, #fef9f4 0%, #ffe3d2 100%); border-bottom: 4px solid #FF5B00;">
    <div class="container text-center">
        <h2 class="fw-bold text-success">
            <i class="bi bi-shop-window me-2"></i> ร้าน Chalini Online
        </h2>
        <p class="text-muted">เลือกซื้อสินค้าที่ต้องการ จัดส่งถึงที่ไม่มีค่าบริการ (ตั้งแต่ 8.00 ถึง 21.00)</p>
    </div>

    @if(!empty($systemAlert))
    <div class="container-fluid bg-warning bg-opacity-25 py-2 mb-3 border-start border-5 border-danger">
        <div class="container">
            @if(Auth::user()->role === 'member' && Auth::user()->room_number === '601')
                {{-- ให้ member 601 แก้ไขได้ --}}
                <form method="POST" action="{{ route('online.settings.updateAlert') }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="system_alert" class="form-control" rows="2">{{ $systemAlert }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm">บันทึกข้อความแจ้งเตือน</button>
                </form>
            @else
                {{-- คนอื่นเห็นข้อความเฉย ๆ --}}
                <div class="text-danger fw-semibold">
                    🔔 {{ $systemAlert }}
                </div>
            @endif
        </div>
    </div>
    @endif

</div>





       <div class="mb-4 overflow-auto d-flex gap-2 pb-2" style="scroll-snap-type: x mandatory;">
    <a href="{{ route('online.index') }}"
        class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-orange' }} flex-shrink-0">ทั้งหมด</a>
    @foreach ($categories as $category)
        <a href="{{ route('online.index', ['category' => $category->id]) }}"
            class="btn btn-sm {{ request('category') == $category->id ? 'btn-orange' : 'btn-outline-secondary' }} flex-shrink-0">
            {{ $category->name }}
        </a>
    @endforeach
</div>


        <form method="GET" action="{{ route('online.index') }}" class="row mb-4 g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-orange w-100">ค้นหา</button>
            </div>
        </form>

        <div class="row g-4">
            @forelse($products as $product)
                @php
                    $stock = $product->stock;
                    $totalStock = $stock ? $stock->store_stock + $stock->warehouse_stock : 0;
                @endphp

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm {{ $totalStock <= 0 ? 'card-out-of-stock' : '' }} position-relative">

                        @if ($stock && !$stock->track_stock)
                            <span class="badge bg-secondary position-absolute top-0 start-0 m-2">ไม่ติดตามสต๊อก</span>
                        @elseif($totalStock <= 0)
                            <span class="badge badge-out-of-stock">❌ หมด</span>
                        @elseif($totalStock <= 5)
                            <span class="badge badge-low-stock">⚠️ เหลือ {{ $totalStock }}</span>
                        @endif

                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                style="height: 200px;">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            
                            @php
    $unit = $product->productUnits->first();
@endphp


                            @if ($unit)
                                <p class="card-text mb-1">
                                   
<span class="price fs-5 text-orange">{{ number_format($unit->price, 2) }} บาท</span>
<span class="text-muted fs-6"> / {{ $unit->unit_name }}</span>
                                </p>
                               

                            @endif

                            <form action="{{ route('online.add') }}" method="POST" class="mt-auto add-to-cart-form"
                                data-product-id="{{ $product->id }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="product_unit_id" value="{{ $unit->id}}">
                                <input type="hidden" name="price" value="{{ $unit->price }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="button"
        class="btn btn-primary add-to-cart"
        data-id="{{ $product->id }}"
        data-quantity="1">
    เพิ่มลงตะกร้า
</button>

                            </form>

                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p>ไม่พบสินค้าในตอนนี้</p>
                </div>
            @endforelse

        </div>

        {{-- แสดง pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>

    {{-- สคริปต์ AJAX --}}
    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                $(document).on('submit', '.add-to-cart-form', function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var button = form.find('button[type="submit"]');
                    var formData = form.serialize();

                    button.prop('disabled', true);

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#cart-total-items').text(response.totalItems);

                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด',
                                text: 'เกิดข้อผิดพลาดในการเพิ่มสินค้า'
                            });
                        },
                        complete: function() {
                            button.prop('disabled', false);
                        }
                    });
                });
            });
        </script>
    @endsection


@endsection
