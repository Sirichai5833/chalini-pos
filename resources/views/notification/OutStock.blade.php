@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-primary fw-bold text-center">
         แจ้งเตือนสินค้าใกล้หมด 
    </h2>

    <div class="card shadow-lg rounded-3 border-0">
        <div class="card-body p-4">
            <h3 class="card-title mb-4 pb-2 border-bottom text-dark fw-bold">
                สินค้าน้อยกว่าหรือเท่ากับจำนวนแจ้งเตือนขั้นต่ำ
            </h3>

            @forelse($lowStockProducts as $item)
                <div class="d-flex justify-content-between align-items-center border border-danger rounded-3 px-3 py-3 mb-3 bg-light-danger hover-shadow-sm transition-ease-in-out">
                    
                    <div>
                        <div class="fs-5 fw-bold text-dark mb-1">
                            {{ $item->product_name }}
                        </div>
                        <div class="text-muted small">
                            สต็อกขั้นต่ำ: 
                            <span class="fw-bold text-secondary">
                                {{ $item->min_stock }} ชิ้น
                            </span>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="text-danger fw-bolder fs-4">
                            {{ $item->total_stock }} ชิ้น
                        </div>
                        <div class="small text-muted">
                            (คงเหลือจริง)
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
                    <p class="text-success fw-semibold mb-0">สินค้าทั้งหมดมีจำนวนเพียงพอ</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .bg-light-danger {
        background-color: #fcebeb;
    }
    .hover-shadow-sm:hover {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }
    .transition-ease-in-out {
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection