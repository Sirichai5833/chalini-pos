@extends('layouts.layout')

@section('title', 'เพิ่มซัพพลายเออร์')
@section('page-title', 'เพิ่มซัพพลายเออร์')

@section('content')
<style>
    .supplier-create-page {
        --sp-yellow: #ffd43b;
        --sp-yellow-soft: #ffe56b;
        --sp-black: #090909;
        --sp-panel: #151515;
        --sp-border: rgba(255, 212, 59, .22);
        --sp-text: #f7f7f7;
        --sp-muted: #aaa;
        color: var(--sp-text);
    }

    .supplier-create-page .page-shell {
        max-width: 980px;
        margin: 0 auto;
    }

    .supplier-create-page .hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1.25rem;
        padding: 1.4rem;
        border: 1px solid var(--sp-border);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 90% 18%,
                rgba(255, 212, 59, .18),
                transparent 24%
            ),
            linear-gradient(135deg, #090909, #181818 65%, #2a2200);
        box-shadow: 0 18px 44px rgba(0, 0, 0, .34);
    }

    .supplier-create-page .hero-title {
        margin: 0;
        color: var(--sp-yellow);
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 950;
    }

    .supplier-create-page .hero-subtitle {
        margin: .4rem 0 0;
        color: #ccc;
        font-size: .88rem;
    }

    .supplier-create-page .form-card {
        padding: 1.35rem;
        border: 1px solid var(--sp-border);
        border-radius: 22px;
        background: linear-gradient(180deg, #191919, #101010);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .32);
    }

    .supplier-create-page .section-title {
        margin-bottom: 1.1rem;
        padding-bottom: .8rem;
        border-bottom: 1px solid var(--sp-border);
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .supplier-create-page .form-label {
        margin-bottom: .45rem;
        color: #ddd;
        font-size: .82rem;
        font-weight: 850;
    }

    .supplier-create-page .form-control {
        min-height: 48px;
        border: 1px solid #3b3b3b;
        border-radius: 13px;
        background: #101010;
        color: #fff;
        box-shadow: none;
    }

    .supplier-create-page textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .supplier-create-page .form-control:focus {
        border-color: var(--sp-yellow);
        background: #121212;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .supplier-create-page .form-check-input {
        border-color: #555;
        background-color: #111;
    }

    .supplier-create-page .form-check-input:checked {
        border-color: var(--sp-yellow);
        background-color: var(--sp-yellow);
    }

    .supplier-create-page .form-check-label {
        color: #ddd;
        font-weight: 750;
    }

    .supplier-create-page .btn-save {
        min-height: 46px;
        border: 1px solid var(--sp-yellow);
        border-radius: 12px;
        background: linear-gradient(
            135deg,
            var(--sp-yellow-soft),
            var(--sp-yellow)
        );
        color: #111;
        font-weight: 900;
    }

    .supplier-create-page .btn-cancel {
        min-height: 46px;
        border: 1px solid #444;
        border-radius: 12px;
        background: #1b1b1b;
        color: #ddd;
        font-weight: 850;
    }

    @media (max-width: 575.98px) {
        .supplier-create-page .hero,
        .supplier-create-page .form-card {
            border-radius: 18px;
        }

        .supplier-create-page .form-actions {
            display: grid !important;
            grid-template-columns: 1fr;
        }

        .supplier-create-page .form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-4 supplier-create-page">
    <div class="page-shell">
        <section class="hero">
            <h2 class="hero-title">
                <i class="bi bi-truck-front-fill me-2"></i>
                เพิ่มซัพพลายเออร์
            </h2>

            <p class="hero-subtitle">
                บันทึกข้อมูลผู้จำหน่ายสำหรับใช้อ้างอิงในเอกสารรับสินค้า
            </p>
        </section>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>กรุณาตรวจสอบข้อมูล</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('suppliers.store') }}"
            class="form-card"
        >
            @csrf

            <h3 class="section-title">
                <i class="bi bi-building-fill me-2 text-warning"></i>
                ข้อมูลผู้จำหน่าย
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        ชื่อผู้จำหน่าย
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror"
                        maxlength="255"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="contact_name" class="form-label">
                        ชื่อผู้ติดต่อ
                    </label>

                    <input
                        type="text"
                        name="contact_name"
                        id="contact_name"
                        value="{{ old('contact_name') }}"
                        class="form-control @error('contact_name') is-invalid @enderror"
                        maxlength="255"
                        placeholder="เช่น คุณสมชาย"
                    >

                    @error('contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">
                        เบอร์โทร
                    </label>

                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror"
                        maxlength="20"
                        placeholder="เช่น 081-234-5678"
                    >

                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">
                        อีเมล
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        maxlength="255"
                        placeholder="supplier@example.com"
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="tax_id" class="form-label">
                        เลขประจำตัวผู้เสียภาษี
                    </label>

                    <input
                        type="text"
                        name="tax_id"
                        id="tax_id"
                        value="{{ old('tax_id') }}"
                        class="form-control @error('tax_id') is-invalid @enderror"
                        maxlength="30"
                        placeholder="เลขประจำตัวผู้เสียภาษี 13 หลัก"
                    >

                    @error('tax_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="supplied_products" class="form-label">
                        สินค้าที่จำหน่าย
                    </label>

                    <textarea
                        name="supplied_products"
                        id="supplied_products"
                        class="form-control @error('supplied_products') is-invalid @enderror"
                        maxlength="2000"
                        placeholder="เช่น น้ำดื่ม, ขนมขบเคี้ยว, เครื่องดื่มชูกำลัง"
                    >{{ old('supplied_products') }}</textarea>

                    @error('supplied_products')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text text-secondary mt-1">
                        ระบุเป็นข้อความสั้น ๆ ว่าผู้จำหน่ายรายนี้จำหน่ายสินค้าอะไร
                    </div>
                </div>

                <div class="col-12">
                    <label for="address" class="form-label">
                        ที่อยู่
                    </label>

                    <textarea
                        name="address"
                        id="address"
                        class="form-control @error('address') is-invalid @enderror"
                        maxlength="2000"
                        placeholder="ที่อยู่สำหรับติดต่อหรือออกเอกสาร"
                    >{{ old('address') }}</textarea>

                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            @checked(old('is_active', true))
                        >

                        <label
                            class="form-check-label"
                            for="is_active"
                        >
                            เปิดใช้งานผู้จำหน่าย
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex justify-content-end gap-2 mt-4">
                <a
                    href="{{ route('suppliers.index') }}"
                    class="btn btn-cancel px-4"
                >
                    <i class="bi bi-x-circle me-1"></i>
                    ยกเลิก
                </a>

                <button
                    type="submit"
                    class="btn btn-save px-4"
                >
                    <i class="bi bi-check-circle-fill me-1"></i>
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
@endsection