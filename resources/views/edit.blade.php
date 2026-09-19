@php
    $editing = isset($supplier);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">
            ชื่อผู้จำหน่าย <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $supplier->name ?? '') }}"
            maxlength="255"
            required
        >

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">ชื่อผู้ติดต่อ</label>

        <input
            type="text"
            name="contact_name"
            class="form-control @error('contact_name') is-invalid @enderror"
            value="{{ old('contact_name', $supplier->contact_name ?? '') }}"
            maxlength="255"
        >

        @error('contact_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">เบอร์โทร</label>

        <input
            type="text"
            name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $supplier->phone ?? '') }}"
            maxlength="20"
        >

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">อีเมล</label>

        <input
            type="email"
            name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $supplier->email ?? '') }}"
            maxlength="255"
        >

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            เลขประจำตัวผู้เสียภาษี
        </label>

        <input
            type="text"
            name="tax_id"
            class="form-control @error('tax_id') is-invalid @enderror"
            value="{{ old('tax_id', $supplier->tax_id ?? '') }}"
            maxlength="30"
        >

        @error('tax_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ที่อยู่</label>

        <textarea
            name="address"
            rows="4"
            class="form-control @error('address') is-invalid @enderror"
            maxlength="2000"
        >{{ old('address', $supplier->address ?? '') }}</textarea>

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
                @checked(
                    old(
                        'is_active',
                        $supplier->is_active ?? true
                    )
                )
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

<div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
    <a
        href="{{ route('suppliers.index') }}"
        class="btn btn-outline-secondary px-4"
    >
        ยกเลิก
    </a>

    <button
        type="submit"
        class="btn btn-warning px-4 fw-bold"
    >
        <i class="bi bi-check-circle-fill me-1"></i>
        {{ $editing ? 'บันทึกการแก้ไข' : 'เพิ่มผู้จำหน่าย' }}
    </button>
</div>
