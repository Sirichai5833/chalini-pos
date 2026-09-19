<style>
    .pos-topbar {
        min-height: 68px;
        padding: 0.55rem 0;
        background: linear-gradient(135deg, #050505 0%, #151515 70%, #292000 100%);
        border-bottom: 1px solid rgba(255, 193, 7, 0.35);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45);
        z-index: 1060;
    }

    .pos-topbar .navbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .pos-topbar .topbar-left,
    .pos-topbar .topbar-right {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 0;
    }

    .pos-topbar .shop-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        max-width: 260px;
        margin: 0;
        color: #ffffff;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .pos-topbar .shop-brand:hover {
        color: #ffc107;
    }

    .pos-topbar .shop-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffc107, #ffe082);
        color: #111111;
        box-shadow: 0 6px 18px rgba(255, 193, 7, 0.25);
    }

    .pos-topbar .shop-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pos-topbar .topbar-action {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.45rem 0.75rem;
        border: 1px solid #454545;
        border-radius: 11px;
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .pos-topbar .topbar-action:hover,
    .pos-topbar .topbar-action:focus {
        border-color: #ffc107;
        background: #ffc107;
        color: #111111;
        box-shadow: 0 6px 18px rgba(255, 193, 7, 0.2);
        transform: translateY(-1px);
    }

    .pos-topbar .menu-button {
        width: 42px;
        padding-left: 0;
        padding-right: 0;
    }

    .pos-topbar .profile-button {
        max-width: 260px;
        padding: 0.3rem 0.7rem 0.3rem 0.35rem;
    }

    .pos-topbar .profile-avatar {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid rgba(255, 193, 7, 0.85);
        border-radius: 50%;
        background: #ffc107;
        color: #111111;
        font-weight: 900;
    }

    .pos-topbar .profile-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .pos-topbar .profile-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        line-height: 1.15;
    }

    .pos-topbar .profile-name {
        max-width: 160px;
        overflow: hidden;
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pos-topbar .profile-role {
        margin-top: 3px;
        color: #bdbdbd;
        font-size: 0.7rem;
    }

    .pos-topbar .profile-button:hover .profile-name,
    .pos-topbar .profile-button:hover .profile-role {
        color: #111111;
    }

    .pos-topbar .logout-button {
        border-color: rgba(220, 53, 69, 0.75);
        background: rgba(220, 53, 69, 0.12);
        color: #ff8d98;
    }

    .pos-topbar .logout-button:hover,
    .pos-topbar .logout-button:focus {
        border-color: #dc3545;
        background: #dc3545;
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(220, 53, 69, 0.25);
    }

    @media (max-width: 991.98px) {
        .pos-topbar .shop-brand {
            max-width: 170px;
        }

        .pos-topbar .profile-text {
            display: none;
        }

        .pos-topbar .profile-button {
            width: 42px;
            padding: 0.2rem;
            border-radius: 50%;
        }

        .pos-topbar .profile-avatar {
            width: 34px;
            height: 34px;
        }
    }

    @media (max-width: 767.98px) {
        .pos-topbar {
            min-height: 62px;
        }

        .pos-topbar .navbar-container {
            gap: 0.4rem;
        }

        .pos-topbar .topbar-left,
        .pos-topbar .topbar-right {
            gap: 0.35rem;
        }

        .pos-topbar .shop-brand {
            max-width: none;
        }

        .pos-topbar .shop-name {
            display: none;
        }

        .pos-topbar .theme-text,
        .pos-topbar .logout-text {
            display: none;
        }

        .pos-topbar .topbar-action {
            width: 39px;
            min-height: 39px;
            padding: 0;
        }

        .pos-topbar .shop-icon {
            width: 39px;
            height: 39px;
        }
    }

    @media (max-width: 390px) {
        .pos-topbar .container-fluid {
            padding-left: 0.55rem;
            padding-right: 0.55rem;
        }

        .pos-topbar .topbar-action,
        .pos-topbar .shop-icon {
            width: 36px;
            min-height: 36px;
            height: 36px;
        }
    }
</style>

<nav class="navbar navbar-dark fixed-top pos-topbar">
    <div class="container-fluid px-3 px-lg-4 navbar-container">

        {{-- ส่วนด้านซ้าย --}}
        <div class="topbar-left">

            <button
                type="button"
                class="btn topbar-action menu-button"
                id="sidebarToggle"
                title="เปิดหรือปิดเมนู"
                aria-label="เปิดหรือปิดเมนูด้านข้าง"
            >
                <i class="bi bi-list fs-5"></i>
            </button>

            <a href="{{ url('/') }}" class="navbar-brand shop-brand">
                <span class="shop-icon">
                    <i class="bi bi-shop"></i>
                </span>

                <span class="shop-name">
                    {{ $shopName ?? 'POS System' }}
                </span>
            </a>

            <button
                type="button"
                class="btn topbar-action"
                id="themeToggle"
                title="เปลี่ยนธีม"
                aria-label="เปลี่ยนธีม"
            >
                <i class="bi bi-moon-fill"></i>
                <span class="theme-text d-none d-lg-inline">
                    เปลี่ยนธีม
                </span>
            </button>

        </div>

        {{-- ส่วนด้านขวา --}}
        <div class="topbar-right">
            @auth
                @php
                    $user = Auth::user();

                    $profileUrl = $user->role === 'member'
                        ? route('online.edit', ['member' => $user->id])
                        : route('staff.edit', ['member' => $user->id]);

                    $roleLabels = [
                        'admin' => 'ผู้ดูแลระบบ',
                        'owner' => 'เจ้าของร้าน',
                        'manager' => 'ผู้จัดการ',
                        'staff' => 'พนักงาน',
                        'member' => 'สมาชิก',
                    ];

                    $roleLabel = $roleLabels[$user->role] ?? $user->role;
                    $firstLetter = mb_strtoupper(
                        mb_substr($user->name ?: 'U', 0, 1)
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | รูปโปรไฟล์ผู้ใช้
                    |--------------------------------------------------------------------------
                    | รองรับชื่อคอลัมน์ที่นิยมใช้หลายแบบ และรองรับ path ที่ถูกบันทึก
                    | ทั้ง profiles/user.jpg, storage/profiles/user.jpg,
                    | public/storage/profiles/user.jpg และ URL ภายนอก
                    */
                    $profileImage = $user->profile_image
                        ?? $user->profile_photo
                        ?? $user->profile_photo_path
                        ?? $user->avatar
                        ?? $user->image
                        ?? $user->photo
                        ?? null;

                    $profileImageUrl = null;

                    if (filled($profileImage)) {
                        $profileImage = str_replace('\\', '/', trim($profileImage));

                        if (
                            str_starts_with($profileImage, 'http://') ||
                            str_starts_with($profileImage, 'https://') ||
                            str_starts_with($profileImage, 'data:')
                        ) {
                            $profileImageUrl = $profileImage;
                        } else {
                            $profileImage = ltrim($profileImage, '/');

                            $profileImage = preg_replace(
                                '#^(public/storage/|storage/app/public/|public/|storage/)#',
                                '',
                                $profileImage
                            );

                            if (
                                filled($profileImage) &&
                                \Illuminate\Support\Facades\Storage::disk('public')
                                    ->exists($profileImage)
                            ) {
                                $profileImageUrl = asset(
                                    'storage/' . $profileImage
                                );
                            }
                        }
                    }
                @endphp

                <a
                    href="{{ $profileUrl }}"
                    class="btn topbar-action profile-button"
                    title="ข้อมูลส่วนตัว"
                >
                    <span class="profile-avatar">
                        @if ($profileImageUrl)
                            <img
                                src="{{ $profileImageUrl }}"
                                alt="รูปโปรไฟล์ของ {{ $user->name }}"
                                width="36"
                                height="36"
                                loading="eager"
                                onerror="this.remove(); this.parentElement.textContent='{{ $firstLetter }}';"
                            >
                        @else
                            {{ $firstLetter }}
                        @endif
                    </span>

                    <span class="profile-text">
                        <span class="profile-name">
                            {{ $user->name }}
                        </span>

                        <small class="profile-role">
                            {{ $roleLabel }}
                        </small>
                    </span>
                </a>

                <button
                    type="button"
                    class="btn topbar-action logout-button"
                    onclick="confirmLogout()"
                    title="ออกจากระบบ"
                >
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="logout-text">ออกจากระบบ</span>
                </button>

                <form
                    id="logout-form"
                    action="{{ route('logout') }}"
                    method="POST"
                    class="d-none"
                >
                    @csrf
                </form>
            @endauth
        </div>

    </div>
</nav>

<script>
    function confirmLogout() {
        const logoutForm = document.getElementById('logout-form');

        if (!logoutForm) {
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการออกจากระบบ',
                text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ออกจากระบบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                background: '#171717',
                color: '#ffffff',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#ffc107',
                customClass: {
                    cancelButton: 'text-dark'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });

            return;
        }

        if (window.confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
            logoutForm.submit();
        }
    }
</script>
