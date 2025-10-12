@extends('layouts.layout')

@section('content')
<div class="container py-4"> {{-- Use Bootstrap's container for responsive width and py-4 for vertical padding --}}
    <h2 class="mb-4 text-primary fw-bold text-center">
        <i class="bi bi-box-seam-fill me-2"></i> รายงานสินค้าคงคลังใกล้หมด <span class="text-muted small">(แยกหน่วยนับ)</span>
    </h2>

    <div class="card shadow-lg rounded-3 border-0"> {{-- Card component with strong shadow, rounded corners, and no default border --}}
        <div class="card-body p-4"> {{-- More padding inside the card body --}}
            <h3 class="card-title mb-4 pb-2 border-bottom text-dark fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i> สินค้าน้อยกว่า 30 ชิ้น
            </h3>

            @forelse($lowStockProducts as $item)
                <div class="d-flex justify-content-between align-items-center border border-danger rounded-3 px-3 py-2 mb-3 bg-light-danger hover-shadow-sm transition-ease-in-out"> {{-- Danger border for low stock, light background, hover effect --}}
                    <div>
                        <div class="fs-6 fw-semibold text-dark mb-1">{{ $item->product_name }}</div>
                        <div class="text-muted small">หน่วยนับ: <span class="fw-bold text-secondary">{{ $item->unit_name }}</span></div>
                    </div>
                    <div class="text-end small">
                        <div>คลังสินค้า: <span class="text-danger fw-bolder fs-5">{{ $item->warehouse_stock }}</span></div>
                        <div class="mt-1">หน้าร้าน: <span class="text-danger fw-bolder fs-5">{{ $item->store_stock }}</span></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
                    <p class="text-success fw-semibold mb-0">✅ สินค้าทั้งหมดมีจำนวนเพียงพอ</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Custom CSS for hover effect, if not already in your main CSS --}}
<style>
    .bg-light-danger {
        background-color: #fcebeb; /* A very light red */
    }
    .hover-shadow-sm:hover {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; /* Small shadow on hover */
    }
    .transition-ease-in-out {
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection