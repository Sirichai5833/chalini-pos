@if (session('success'))
    <x-sweet-alert
        icon="success"
        :title="$successTitle ?? 'สำเร็จ!'"
        :text="session('success')"
        :confirm-button-text="$confirmButtonText ?? 'ตกลง'"
    />
@endif

@if (session('error'))
    <x-sweet-alert
        icon="error"
        :title="$errorTitle ?? 'ผิดพลาด'"
        :text="session('error')"
        :confirm-button-text="$confirmButtonText ?? 'ตกลง'"
    />
@endif
