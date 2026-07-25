@extends('layouts.online')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">📦 คำสั่งซื้อของคุณ</h2>
        
        {{-- ข้อความแจ้งเตือน --}}
        <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                🚨 <strong>แจ้งเตือน:</strong> เมื่อทำการสั่งซื้อแล้วจะไม่สามารถยกเลิกคำสั่งซื้อเองได้ ถ้าต้องการจะยกเลิกกรุณาติดต่อที่เบอร์ 084-386-0015
            </div>
        </div>

        @livewire('customer-orders')
    </div>
@endsection

@push('scripts')
<script>
    Livewire.on('order-status-changed', ({ id, status }) => {
        const sound = document.getElementById('orderSound');
        if (sound) sound.play().catch(() => {
            // Autoplay might be blocked, user might need to interact first.
            console.warn('Audio autoplay prevented. User interaction required.');
        });

        Swal.fire({
            icon: 'info',
            title: 'สถานะอัปเดต',
            text: `คำสั่งซื้อ #${id} ถูกเปลี่ยนเป็น ${status}`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
@endpush

<style>
    /* Custom CSS for the decorative stripe */
    .status-stripe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px; /* Height of the stripe */
        border-top-left-radius: 0.3rem; /* Match card border radius */
        border-top-right-radius: 0.3rem;
        z-index: 1; /* Ensure it's above card body but within card */
    }

    .card.position-relative {
        padding-top: 8px; /* Offset for the stripe */
    }

    /* Adjust padding for list items to remove default Bootstrap list-group-item padding */
    .list-group-flush .list-group-item {
        padding-left: 0;
        padding-right: 0;
    }

    /* Custom scrollbar for product items */
    .overflow-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
