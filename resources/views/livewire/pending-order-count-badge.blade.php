<div wire:poll.5s="pollCount">
    @if ($count > 0)
        <span class="badge bg-danger ms-2">{{ $count }}</span>
    @endif

    <audio id="orderSound" src="{{ asset('sounds/notify.mp3') }}" preload="auto"></audio>
</div>

@push('scripts')
<script>
    window.addEventListener('new-order', function () {
        const sound = document.getElementById('orderSound');
        if (sound) {
            sound.play().catch(() => {});
            
            Swal.fire({
                icon: 'info',
                title: 'มีคำสั่งซื้อใหม่',
                text: 'คุณมีคำสั่งซื้อใหม่เข้ามา โปรดตรวจสอบ',
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
            });
        }
    });
</script>
@endpush
