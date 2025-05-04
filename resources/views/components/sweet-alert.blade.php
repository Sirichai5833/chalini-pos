<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: '{{ $icon }}',
        title: '{{ $title }}',
        text: '{{ $text }}',
        confirmButtonText: '{{ $confirmButtonText }}'
    });
</script>
