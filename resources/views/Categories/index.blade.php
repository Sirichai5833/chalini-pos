@extends('layouts.layout')

@section('content')
    <style>
        :root {
            --cat-yellow: #ffd43b;
            --cat-black: #090909;
            --cat-surface: #121212;
            --cat-card: #181818;
            --cat-border: rgba(255, 212, 59, .22);
            --cat-text: #f7f7f7;
            --cat-muted: #aaa;
            --cat-danger: #ef4444;
            --cat-success: #22c55e;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--cat-black);
            color: var(--cat-text);
        }

        .category-page {
            max-width: 1100px;
        }

        .category-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            margin-bottom: 1.4rem;
            border: 1px solid var(--cat-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #080808 0%, #171717 58%, #2b2300 100%);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .35);
        }

        .category-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -120px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .1);
            pointer-events: none;
        }

        .category-hero-content {
            position: relative;
            z-index: 1;
        }

        .category-title {
            margin: 0;
            color: var(--cat-yellow);
            font-weight: 950;
        }

        .category-subtitle {
            margin: .4rem 0 0;
            color: #d0d0d0;
            font-size: .9rem;
        }

        .category-card {
            border: 1px solid #2d2d2d;
            border-radius: 22px;
            background: linear-gradient(180deg, #191919, #111);
            color: var(--cat-text);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .32);
        }

        .section-title {
            color: var(--cat-yellow);
            font-weight: 900;
        }

        .form-label {
            color: #ddd;
            font-weight: 800;
        }

        .form-control {
            min-height: 46px;
            border: 1px solid #3a3a3a;
            border-radius: 13px;
            background: #141414;
            color: #fff;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--cat-yellow);
            background: #171717;
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
        }

        .form-control::placeholder {
            color: #777;
        }

        .btn-add-category {
            min-height: 46px;
            border: 1px solid var(--cat-yellow);
            border-radius: 13px;
            background: linear-gradient(135deg, #ffe66a, var(--cat-yellow));
            color: #111;
            font-weight: 900;
        }

        .btn-add-category:hover {
            background: linear-gradient(135deg, #fff08c, #ffc800);
            color: #000;
        }

        .category-table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--cat-text);
            --bs-table-border-color: #303030;
            --bs-table-hover-bg: rgba(255, 212, 59, .05);
            --bs-table-hover-color: #fff;
            margin-bottom: 0;
        }

        .category-table th {
            background: #101010 !important;
            color: var(--cat-yellow);
            border-color: #303030;
            font-size: .76rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .category-table td {
            border-color: #303030;
            color: #eee;
            vertical-align: middle;
        }

        .category-name {
            color: #fff;
            font-weight: 850;
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            padding: .35rem .65rem;
            border: 1px solid var(--cat-border);
            border-radius: 999px;
            background: rgba(255, 212, 59, .08);
            color: var(--cat-yellow);
            font-size: .75rem;
            font-weight: 850;
        }

        .btn-delete-category {
            border-radius: 11px;
            font-weight: 850;
        }

        .btn-delete-category:disabled {
            cursor: not-allowed;
            opacity: .45;
        }

        .empty-category {
            padding: 3rem 1rem !important;
            color: var(--cat-muted) !important;
        }

        .alert {
            border-radius: 15px;
        }

        @media (max-width: 767.98px) {
            .category-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .category-card {
                border-radius: 18px;
            }
        }
    </style>

    <div class="container py-4 category-page">
        <section class="category-hero">
            <div class="category-hero-content">
                <h2 class="category-title">
                    <i class="bi bi-tags-fill me-2"></i>
                    จัดการประเภทสินค้า
                </h2>

                <p class="category-subtitle">
                    เพิ่ม ตรวจสอบ และจัดการหมวดหมู่สินค้าในระบบ
                </p>
            </div>
        </section>

        @if (session('error'))
            <div class="alert alert-danger shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="card category-card mb-4">
            <div class="card-body p-4">
                <h4 class="section-title mb-4">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    เพิ่มประเภทสินค้าใหม่
                </h4>

                <form action="{{ route('categories.store') }}"
                      method="POST"
                      id="categoryCreateForm"
                      class="row g-3 align-items-end">
                    @csrf

                    <div class="col-md-9">
                        <label for="name" class="form-label">
                            ชื่อประเภทสินค้า
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="เช่น เครื่องดื่ม ขนม ของใช้"
                               required
                               autofocus>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-add-category w-100">
                            <i class="bi bi-plus-lg me-1"></i>
                            เพิ่มประเภท
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card category-card">
            <div class="card-body p-0">
                <div class="p-4 pb-3 d-flex justify-content-between align-items-center gap-3">
                    <h4 class="section-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        รายการประเภทสินค้า
                    </h4>

                    <span class="count-badge">
                        {{ $categories->count() }} ประเภท
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table category-table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 90px;">ลำดับ</th>
                                <th>ชื่อประเภท</th>
                                <th style="width: 170px;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($categories as $category)
                                @php
                                    $productCount = $category->products()->count();
                                @endphp

                                <tr>
                                    <td>
                                        <span class="count-badge">
                                            #{{ $loop->iteration }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="category-name">
                                            {{ $category->name }}
                                        </div>

                                        <div class="text-white-50 small ">
                                            มีสินค้า {{ $productCount }} รายการ
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <form action="{{ route('categories.delete', $category->id) }}"
                                              method="POST"
                                              class="d-inline-block delete-category-form"
                                              data-category-name="{{ $category->name }}"
                                              data-product-count="{{ $productCount }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm btn-delete-category"
                                                    {{ $productCount > 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-trash-fill me-1"></i>
                                                ลบ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center empty-category">
                                        <i class="bi bi-inbox-fill fs-2 d-block mb-2"></i>
                                        ยังไม่มีประเภทสินค้า
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createForm = document.getElementById('categoryCreateForm');

            createForm?.addEventListener('submit', function (event) {
                const button = this.querySelector('button[type="submit"]');

                if (this.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

                this.dataset.submitting = '1';
                button.disabled = true;
                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    กำลังบันทึก...
                `;
            });

            document.querySelectorAll('.delete-category-form').forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const categoryName = this.dataset.categoryName;

                    Swal.fire({
                        icon: 'warning',
                        title: 'ยืนยันการลบประเภทสินค้า',
                        text: `ต้องการลบประเภท "${categoryName}" หรือไม่`,
                        showCancelButton: true,
                        confirmButtonText: 'ลบประเภท',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection