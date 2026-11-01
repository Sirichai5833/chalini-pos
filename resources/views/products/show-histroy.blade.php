
@extends('layouts.layout')

@section('content')
@php
    $fieldTranslations = [
        'name'          => 'ชื่อสินค้า/หน่วย',
        'sku'           => 'รหัสสินค้า (SKU)',
        'barcode'       => 'บาร์โค้ด',
        'price'         => 'ราคาขาย',
        'wholesale'     => 'ราคาขายส่ง',
        'cost'          => 'ราคาทุน',
        'cost_price'    => 'ราคาทุน',
        'stock'         => 'จำนวนสต็อก',
        'quantity'      => 'จำนวนสินค้า',
        'minimum_stock' => 'สต็อกขั้นต่ำ',
        'min_stock'     => 'สต็อกขั้นต่ำ',
        'unit_name'     => 'ชื่อหน่วย',
        'unit_quantity' => 'จำนวนต่อหน่วย',
        'description'   => 'รายละเอียดสินค้า',
        'category_id'   => 'หมวดหมู่สินค้า',
        'brand_id'      => 'แบรนด์สินค้า',
        'status'        => 'สถานะสินค้า',
        'is_active'     => 'การเปิดใช้งาน',
        'image'         => 'รูปภาพสินค้า',
    ];

    $sensitiveFields = ['cost', 'cost_price', 'supplier_id', 'margin'];

    $canViewSensitive = auth()->check() && (
        in_array(auth()->user()->role ?? '', ['admin', 'manager']) ||
        (method_exists(auth()->user(), 'hasRole') &&
            auth()->user()->hasRole(['admin', 'manager'])) ||
        (method_exists(auth()->user(), 'can') &&
            auth()->user()->can('view-sensitive-logs'))
    );

    /*
    |--------------------------------------------------------------------------
    | กรอง Log ที่ไม่มีรายละเอียดการเปลี่ยนแปลง
    |--------------------------------------------------------------------------
    | ป้องกันแถวว่างจากการ update ที่ไม่มีฟิลด์เปลี่ยนจริง
    */
    $visibleActivities = $activities->getCollection()->filter(
        function ($activity) {
            if (!in_array($activity->event, ['created', 'updated'])) {
                return false;
            }

            $attributes = $activity->properties['attributes'] ?? [];

            // ไม่แสดง Log ที่ไม่มี attributes
            if (empty($attributes) || !is_array($attributes)) {
                return false;
            }

            // ไม่แสดง Log ที่มีแค่ updated_at
            $meaningfulAttributes = collect($attributes)
                ->except(['updated_at']);

            return $meaningfulAttributes->isNotEmpty();
        }
    );
@endphp

<div class="container-fluid py-4 product-history-page">

    <section class="history-hero">
        <div class="history-hero-content">
            <h3 class="history-title">
                <i class="bi bi-clock-history me-2"></i>
                ประวัติการเปลี่ยนแปลงสินค้า
            </h3>
        </div>
    </section>

    {{-- ค้นหา --}}
    <div class="card history-card mb-3">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">

                    <div class="col-md-3">
                        <label for="date_from" class="form-label text-muted text-xs fw-bold mb-1">
                            ตั้งแต่วันที่
                        </label>
                        <input
                            type="date"
                            id="date_from"
                            name="date_from"
                            class="form-control form-control-sm"
                            value="{{ request('date_from') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label text-muted text-xs fw-bold mb-1">
                            ถึงวันที่
                        </label>
                        <input
                            type="date"
                            id="date_to"
                            name="date_to"
                            class="form-control form-control-sm"
                            value="{{ request('date_to') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="event" class="form-label text-muted text-xs fw-bold mb-1">
                            ประเภทการกระทำ
                        </label>
                        <select name="event" id="event" class="form-select form-select-sm">
                            <option value="">ทั้งหมด</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>
                                เพิ่มข้อมูล (Created)
                            </option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>
                                แก้ไข (Updated)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-search-history w-100">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>

                            <a href="{{ route('product.products.allHistory') }}"
                               class="btn btn-reset-history w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ต
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ตารางประวัติ --}}
    <div class="card history-card">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table history-table table-hover table-compact align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 py-2 text-secondary text-uppercase text-xs fw-bold" style="width:20%">
                                สินค้า / หน่วยสินค้า
                            </th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width:15%">
                                วันที่ & เวลา
                            </th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width:10%">
                                ประเภท
                            </th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width:15%">
                                ทำรายการโดย
                            </th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width:40%">
                                รายละเอียดการเปลี่ยนแปลง
                            </th>
                        </tr>
                    </thead>

                    <tbody class="border-top-0">

                        @forelse ($visibleActivities as $activity)

                            @php
                                $properties = $activity->properties ?? collect();

                                $attributes = $properties['attributes'] ?? [];
                                $oldValues = $properties['old'] ?? [];

                                /*
                                |--------------------------------------------------------------------------
                                | ชื่อสินค้าและหน่วยสินค้า
                                |--------------------------------------------------------------------------
                                | ProductUnit ใช้ tapActivity เปลี่ยน subject เป็น Product
                                | จึงต้องอ่านชื่อหน่วยจาก properties แทน subject->product()
                                */
                                $displayName = $activity->subject?->name
                                    ?? 'ไม่พบข้อมูลสินค้า';

                                $unitName = $properties['unit_name'] ?? null;
                                $unitId = $properties['unit_id'] ?? null;

                                if ($unitName) {
                                    $displaySubDetail = "หน่วย: {$unitName}";

                                    if ($unitId) {
                                        $displaySubDetail .= " (ID: {$unitId})";
                                    }
                                } else {
                                    $displaySubDetail = "ID: {$activity->subject_id}";
                                }

                                $hasVisibleAttributes = false;
                            @endphp

                            <tr>

                                {{-- สินค้า / หน่วย --}}
                                <td class="ps-3">
                                    <span
                                        class="subject-name d-block text-truncate text-sm"
                                        style="max-width:200px;"
                                        title="{{ $displayName }}"
                                    >
                                        {{ $displayName }}
                                    </span>

                                    <span class="subject-subtitle text-xs d-block">
                                        {{ $displaySubDetail }}
                                    </span>
                                </td>

                                {{-- วันที่ --}}
                                <td>
                                    <div class="d-flex flex-column lh-sm">
                                        <span class="date-main text-sm">
                                            {{ $activity->created_at->format('d/m/Y') }}
                                        </span>

                                        <span class="date-time text-xs">
                                            {{ $activity->created_at->format('H:i:s') }} น.
                                        </span>
                                    </div>
                                </td>

                                {{-- ประเภท --}}
                                <td>
                                    @if ($activity->event === 'created')
                                        <span class="badge-mini event-created">
                                            <i class="bi bi-plus-lg me-1"></i> เพิ่ม
                                        </span>
                                    @elseif ($activity->event === 'updated')
                                        <span class="badge-mini event-updated">
                                            <i class="bi bi-pencil me-1"></i> แก้ไข
                                        </span>
                                    @endif
                                </td>

                                {{-- ผู้ทำรายการ --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2">
                                            <i class="bi bi-person text-secondary text-xs"></i>
                                        </div>

                                        <span class="text-white fw-medium text-sm">
                                            {{ $activity->causer?->name ?? 'System' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- รายละเอียด --}}
                                <td class="py-2">
                                    <div class="change-box">
                                        <ul class="list-unstyled mb-0">

                                            @foreach ($attributes as $key => $value)

                                                @php
                                                    if ($key === 'updated_at') {
                                                        continue;
                                                    }

                                                    if (
                                                        in_array($key, $sensitiveFields)
                                                        && !$canViewSensitive
                                                    ) {
                                                        continue;
                                                    }

                                                    $old = $oldValues[$key] ?? null;

                                                    $hasVisibleAttributes = true;

                                                    $displayOld = $old;
                                                    $displayNew = $value;

                                                    if (in_array($key, ['is_active', 'status', 'active'])) {
                                                        if (!is_null($old)) {
                                                            $displayOld = (
                                                                $old == 1 || $old === true
                                                            ) ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                                                        }

                                                        $displayNew = (
                                                            $value == 1 || $value === true
                                                        ) ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                                                    }

                                                    // จัดรูปแบบราคา
                                                    if (
                                                        in_array($key, ['price', 'wholesale', 'cost_price', 'cost'])
                                                        && is_numeric($value)
                                                    ) {
                                                        $displayNew = number_format((float) $value, 2);

                                                        if (is_numeric($old)) {
                                                            $displayOld = number_format((float) $old, 2);
                                                        }
                                                    }

                                                    // แปลงข้อมูล Array / Object
                                                    if (is_array($displayOld) || is_object($displayOld)) {
                                                        $displayOld = json_encode($displayOld, JSON_UNESCAPED_UNICODE);
                                                    }

                                                    if (is_array($displayNew) || is_object($displayNew)) {
                                                        $displayNew = json_encode($displayNew, JSON_UNESCAPED_UNICODE);
                                                    }
                                                @endphp

                                                <li class="mb-1 pb-1 border-bottom border-light-subtle text-xs">
                                                    <div class="d-flex align-items-start gap-2">

                                                        <strong
                                                            class="change-field text-xs"
                                                            style="min-width:100px;max-width:100px;display:inline-block;"
                                                        >
                                                            {{ $fieldTranslations[$key] ?? str_replace('_', ' ', ucfirst($key)) }}:
                                                        </strong>

                                                        @if ($key === 'image' && $value)

                                                            <div class="d-flex align-items-center gap-2">

                                                                @if ($old && $old !== $value)
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="text-muted text-xs">เดิม</span>

                                                                        <img
                                                                            src="{{ asset('storage/' . $old) }}"
                                                                            class="img-thumbnail p-0"
                                                                            style="height:40px;width:auto;object-fit:cover;"
                                                                            alt="รูปภาพเดิม"
                                                                        >
                                                                    </div>

                                                                    <i class="bi bi-arrow-right text-muted text-xs"></i>
                                                                @endif

                                                                <div class="d-flex align-items-center gap-1">
                                                                    <span class="text-muted text-xs">ใหม่</span>

                                                                    <img
                                                                        src="{{ asset('storage/' . $value) }}"
                                                                        class="img-thumbnail p-0"
                                                                        style="height:40px;width:auto;object-fit:cover;"
                                                                        alt="รูปภาพใหม่"
                                                                    >
                                                                </div>

                                                            </div>

                                                        @elseif ($key === 'barcode' && $value)

                                                            <div class="d-flex align-items-center gap-2">

                                                                @if ($old && $old !== $value)
                                                                    <div class="d-flex flex-column align-items-center bg-white p-1 border rounded">
                                                                        <span class="text-danger fw-bold text-xs mb-1">
                                                                            {{ $old }}
                                                                        </span>

                                                                        <img
                                                                            src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($old) }}&code=Code128"
                                                                            style="height:25px;max-width:100px;object-fit:contain;"
                                                                            alt="บาร์โค้ดเดิม"
                                                                        >
                                                                    </div>

                                                                    <i class="bi bi-arrow-right text-muted text-xs"></i>
                                                                @endif

                                                                <div class="d-flex flex-column align-items-center bg-white p-1 border rounded">
                                                                    <span class="text-success fw-bold text-xs mb-1">
                                                                        {{ $value }}
                                                                    </span>

                                                                    <img
                                                                        src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($value) }}&code=Code128"
                                                                        style="height:25px;max-width:100px;object-fit:contain;"
                                                                        alt="บาร์โค้ดใหม่"
                                                                    >
                                                                </div>

                                                            </div>

                                                        @else

                                                            <div class="d-flex flex-wrap align-items-center gap-1.5 text-break">

                                                                @if ($old !== $value && !is_null($old))
                                                                    <span class="old-value text-decoration-line-through px-2 py-1 text-xs">
                                                                        {{ $displayOld }}
                                                                    </span>

                                                                    <i class="bi bi-arrow-right text-muted text-xs"></i>
                                                                @endif

                                                                <span class="new-value fw-bold px-2 py-1 text-xs">
                                                                    {{ $displayNew ?? 'ไม่มีข้อมูล' }}
                                                                </span>

                                                            </div>

                                                        @endif

                                                    </div>
                                                </li>

                                            @endforeach

                                            @if (!$hasVisibleAttributes)
                                                <li class="text-muted text-xs fst-italic">
                                                    ไม่มีรายละเอียดการเปลี่ยนแปลงที่คุณสามารถเข้าถึงได้
                                                </li>
                                            @endif

                                        </ul>
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center empty-history">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle p-3 mb-2">
                                            <i class="bi bi-inbox fs-3 text-muted"></i>
                                        </div>

                                        <h6 class="text-muted fw-bold mb-1">
                                            ไม่พบประวัติการเปลี่ยนแปลง
                                        </h6>

                                        <p class="text-secondary text-xs mb-0">
                                            ลองเปลี่ยนเงื่อนไขวันที่ หรือตัวกรองการค้นหา
                                        </p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>

            @if ($activities->hasPages())
                <div class="card-footer border-top border-secondary-subtle bg-transparent py-3">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>

</div>

<style>
    :root {
        --history-yellow: #ffd43b;
        --history-black: #090909;
        --history-panel: #151515;
        --history-soft: #1f1f1f;
        --history-border: rgba(255, 212, 59, .22);
        --history-text: #f7f7f7;
        --history-muted: #aaa;
        --history-success: #22c55e;
        --history-warning: #f59e0b;
        --history-danger: #ef4444;
        --history-info: #38bdf8;
    }

    body {
        background:
            radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
            var(--history-black);
        color: var(--history-text);
    }

    .product-history-page {
        max-width: 1500px;
        color: var(--history-text);
    }

    .history-hero {
        position: relative;
        overflow: hidden;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        border: 1px solid var(--history-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
            linear-gradient(135deg, #080808 0%, #171717 58%, #2b2300 100%);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .35);
    }

    .history-hero::after {
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

    .history-hero-content {
        position: relative;
        z-index: 1;
    }

    .history-title {
        margin: 0;
        color: var(--history-yellow);
        font-weight: 950;
    }

    .history-subtitle {
        margin: .4rem 0 0;
        color: #d0d0d0;
        font-size: .9rem;
    }

    .history-card {
        border: 1px solid #2d2d2d !important;
        border-radius: 20px !important;
        background: linear-gradient(180deg, #191919, #111) !important;
        color: var(--history-text);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .32) !important;
    }

    .history-card:hover {
        border-color: rgba(255, 212, 59, .48) !important;
    }

    .form-label {
        color: #ddd !important;
        font-weight: 800 !important;
    }

    .form-control,
    .form-select {
        min-height: 44px;
        border: 1px solid #3a3a3a;
        border-radius: 12px;
        background: #131313;
        color: #fff !important;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--history-yellow);
        background: #171717;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }

    .form-select option {
        background: #171717;
        color: #fff;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .btn-search-history {
        min-height: 44px;
        border: 1px solid var(--history-yellow);
        border-radius: 12px;
        background: linear-gradient(135deg, #ffe66a, var(--history-yellow));
        color: #111;
        font-weight: 900;
    }

    .btn-search-history:hover {
        background: linear-gradient(135deg, #fff08c, #ffc800);
        color: #000;
    }

    .btn-reset-history {
        min-height: 44px;
        border: 1px solid #3b3b3b;
        border-radius: 12px;
        background: #171717;
        color: #ddd;
        font-weight: 800;
    }

    .btn-reset-history:hover {
        border-color: var(--history-yellow);
        background: var(--history-yellow);
        color: #111;
    }

    .history-table {
        --bs-table-bg: transparent;
        --bs-table-color: var(--history-text);
        --bs-table-border-color: #303030;
        --bs-table-hover-bg: rgba(255, 212, 59, .05);
        --bs-table-hover-color: #fff;
        margin-bottom: 0;
    }

    .history-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #101010 !important;
        color: var(--history-yellow) !important;
        border-color: #303030;
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .3px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .history-table td {
        color: #e8e8e8;
        border-color: #303030;
        vertical-align: middle;
    }

    .subject-name {
        color: #fff !important;
        font-weight: 850;
    }

    .subject-subtitle {
        color: var(--history-muted) !important;
    }

    .date-main {
        color: #fff !important;
        font-weight: 800;
    }

    .date-time {
        color: var(--history-muted) !important;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        display: grid;
        place-items: center;
        border: 1px solid var(--history-border);
        border-radius: 50%;
        background: rgba(255, 212, 59, .08) !important;
        color: var(--history-yellow);
    }

    .change-box {
        padding: .75rem;
        border: 1px solid #303030 !important;
        border-radius: 14px !important;
        background: #111 !important;
    }

    .change-field {
        color: #bbb !important;
        font-weight: 800;
    }

    .old-value {
        border: 1px solid rgba(239, 68, 68, .25);
        border-radius: 8px;
        background: rgba(239, 68, 68, .10);
        color: #ffaaaa;
    }

    .new-value {
        border: 1px solid rgba(34, 197, 94, .25);
        border-radius: 8px;
        background: rgba(34, 197, 94, .10);
        color: #86efac;
    }

    .badge-mini {
        display: inline-flex;
        align-items: center;
        padding: .3rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
    }

    .event-created {
        border: 1px solid rgba(34, 197, 94, .35);
        background: rgba(34, 197, 94, .12);
        color: #86efac;
    }

    .event-updated {
        border: 1px solid rgba(245, 158, 11, .35);
        background: rgba(245, 158, 11, .12);
        color: #fcd34d;
    }

    .event-deleted {
        border: 1px solid rgba(239, 68, 68, .35);
        background: rgba(239, 68, 68, .12);
        color: #ff9f9f;
    }

    .empty-history {
        padding: 3.5rem 1rem !important;
        color: var(--history-muted) !important;
    }

    .pagination {
        --bs-pagination-color: var(--history-yellow);
        --bs-pagination-bg: #171717;
        --bs-pagination-border-color: #333;
        --bs-pagination-hover-color: #111;
        --bs-pagination-hover-bg: var(--history-yellow);
        --bs-pagination-hover-border-color: var(--history-yellow);
        --bs-pagination-active-color: #111;
        --bs-pagination-active-bg: var(--history-yellow);
        --bs-pagination-active-border-color: var(--history-yellow);
        --bs-pagination-disabled-color: #666;
        --bs-pagination-disabled-bg: #111;
        --bs-pagination-disabled-border-color: #2a2a2a;
    }

    .text-xs {
        font-size: .75rem !important;
    }

    .text-sm {
        font-size: .86rem !important;
    }

    .table-compact td,
    .table-compact th {
        padding: .65rem .6rem !important;
    }

    .last\:mb-0:last-child {
        margin-bottom: 0 !important;
    }

    .last\:border-0:last-child {
        border-bottom: 0 !important;
    }

    .gap-1\.5 {
        gap: .375rem !important;
    }

    @media (max-width: 767.98px) {
        .history-hero {
            padding: 1.2rem;
            border-radius: 19px;
        }

        .history-card {
            border-radius: 17px !important;
        }
    }
</style>
@endsection
