@extends('layouts.online')

@section('content')
    @if (session('success'))
        <x-sweet-alert icon="success" title="สำเร็จ!" text="{{ session('success') }}" confirm-button-text="ตกลง" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="ผิดพลาด" text="{{ session('error') }}" confirm-button-text="ตกลง" />
    @endif

    <style>
        .btn-orange {
            background-color: #f85a1c;
            color: #fff;
            border: none;
        }

        .btn-orange:hover {
            background-color: #e14c12;
        }

        .category-btn.active {
            background-color: #f85a1c !important;
            color: white;
        }

        .card .price {
            color: #f85a1c;
            font-weight: bold;
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
            background-color: #dc3545;
            font-size: 0.75rem;
            border-radius: 5px;
            padding: 2px 6px;
        }

        /* ปรับขนาด icon ของ pagination ให้ปกติ */
        .pagination svg {
            width: 1em;
            height: 1em;
        }

        /* ปรับขนาด font ของ pagination ถ้ามีปุ่มเลข */
        .pagination {
            font-size: 1rem;
        }

        /* ป้องกัน style อื่นมากระทบ pagination */
        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
        }
    </style>

    <div class="container py-4">
        <h2 class="mb-4 text-center text-orange"><i class="bi bi-shop"></i> ร้านค้าออนไลน์</h2>

        <div class="mb-4">
            <strong>หมวดหมู่:</strong>
            <a href="{{ route('online.index') }}"
                class="btn btn-sm {{ request('category') ? 'btn-outline-secondary' : 'btn-orange' }}">ทั้งหมด</a>
            @foreach ($categories as $category)
                <a href="{{ route('online.index', ['category' => $category->id]) }}"
                    class="btn btn-sm {{ request('category') == $category->id ? 'btn-orange' : 'btn-outline-secondary' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('online.index') }}" class="row mb-4 g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า..."
                    value="{{ request('search') }}">
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
                            <span class="badge badge-out-of-stock position-absolute top-0 start-0 m-2">❌ หมด</span>
                        @elseif($totalStock <= 5)
                            <span class="badge badge-low-stock position-absolute top-0 start-0 m-2">⚠️ เหลือ
                                {{ $totalStock }}</span>
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
                                    ราคา ({{ $unit->unit_name }}): <span
                                        class="price">{{ number_format($unit->price, 2) }}</span> บาท
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
