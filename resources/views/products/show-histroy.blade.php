@extends('layouts.layout')

@section('content')
@php
    // 1. ตัวแปลภาษาไทยสำหรับชื่อฟิลด์ใน Database
    $fieldTranslations = [
        'name'          => 'ชื่อสินค้า/หน่วย',
        'sku'           => 'รหัสสินค้า (SKU)',
        'barcode'       => 'บาร์โค้ด',
        'price'         => 'ราคาขาย',
        'cost'          => 'ราคาทุน',
        'cost_price'    => 'ราคาทุน',
        'stock'         => 'จำนวนสต็อก',
        'quantity'      => 'จำนวนสินค้า',
        'minimum_stock' => 'สต็อกขั้นต่ำ',
        'description'   => 'รายละเอียดสินค้า',
        'category_id'   => 'หมวดหมู่สินค้า',
        'brand_id'      => 'แบรนด์สินค้า',
        'status'        => 'สถานะสินค้า',
        'is_active'     => 'การเปิดใช้งาน',
        'image'         => 'รูปภาพสินค้า',
    ];

    // 2. ฟิลด์ข้อมูลที่อ่อนไหว (Sensitive Fields) สำหรับควบคุมสิทธิ์การมองเห็น
    $sensitiveFields = ['cost', 'cost_price', 'supplier_id', 'margin'];
@endphp

<div class="container-fluid py-3" style="max-width: 1400px;">

    <!-- ส่วนหัวหน้าจอ -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-dark mb-0 fs-4">
                <i class="bi bi-clock-history text-primary me-2 fs-5"></i>ประวัติการเปลี่ยนแปลงสินค้า
            </h3>
            <p class="text-muted text-xs mb-0">ตรวจสอบรายการ Activity Log ย้อนหลังทั้งหมด</p>
        </div>
    </div>

    <!-- ส่วนค้นหา -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label text-muted text-xs fw-bold mb-1">ตั้งแต่วันที่</label>
                        <input type="date" id="date_from" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label text-muted text-xs fw-bold mb-1">ถึงวันที่</label>
                        <input type="date" id="date_to" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="event" class="form-label text-muted text-xs fw-bold mb-1">ประเภทการกระทำ</label>
                        <select name="event" id="event" class="form-select form-select-sm">
                            <option value="">ทั้งหมด</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>เพิ่มข้อมูล (Created)</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>แก้ไข (Updated)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm w-100 fw-bold">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                            <a href="{{ route('product.products.allHistory') }}" class="btn btn-light border btn-sm w-100 text-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ต
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแบบ Compact -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-compact align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 py-2 text-secondary text-uppercase text-xs fw-bold" style="width: 20%">สินค้า / หน่วยสินค้า</th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width: 15%">วันที่ & เวลา</th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width: 10%">ประเภท</th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width: 15%">ทำรายการโดย</th>
                            <th class="py-2 text-secondary text-uppercase text-xs fw-bold" style="width: 40%">รายละเอียดการเปลี่ยนแปลง</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($activities as $activity)
                            @php
                                // กำหนดการแสดงชื่อสินค้า (กรณีเป็น Log ของ ProductUnit ให้วิ่งไปหา Product หลัก)
                                $displayName = 'ไม่พบข้อมูลสินค้า';
                                $displaySubDetail = 'ID: ' . $activity->subject_id;

                                if ($activity->subject) {
                                    // ตรวจสอบว่าเป็น Model ProductUnit หรือไม่ (เช็คชื่อคลาสหรือเช็คว่ามีความสัมพันธ์ product หรือไม่)
                                    if (str_contains(get_class($activity->subject), 'ProductUnit') || method_exists($activity->subject, 'product')) {
                                        $productName = $activity->subject->product?->name ?? 'ไม่พบข้อมูลสินค้าหลัก';
                                        $unitName = $activity->subject->name ?? $activity->subject->unit_name ?? '';
                                        $displayName = $productName;
                                        $displaySubDetail = "หน่วย: " . ($unitName ?: 'ทั่วไป') . " (ID: {$activity->subject_id})";
                                    } else {
                                        // กรณีเป็นสินค้าหลักโดยตรง
                                        $displayName = $activity->subject->name ?? 'ไม่พบข้อมูลสินค้า';
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold text-dark d-block text-truncate text-sm" style="max-width: 200px;" title="{{ $displayName }}">
                                        {{ $displayName }}
                                    </span>
                                    <span class="text-muted text-xs d-block">{{ $displaySubDetail }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column lh-sm">
                                        <span class="fw-semibold text-dark text-sm">{{ $activity->created_at->format('d/m/Y') }}</span>
                                        <span class="text-muted text-xs">{{ $activity->created_at->format('H:i:s') }} น.</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($activity->event == 'created')
                                        <span class="badge-mini bg-success-light text-success border border-success">
                                            <i class="bi bi-plus-lg me-0.5"></i> เพิ่ม
                                        </span>
                                    @elseif($activity->event == 'updated')
                                        <span class="badge-mini bg-warning-light text-warning border border-warning">
                                            <i class="bi bi-pencil me-0.5"></i> แก้ไข
                                        </span>
                                    @elseif($activity->event == 'deleted')
                                        <span class="badge-mini bg-danger-light text-danger border border-danger">
                                            <i class="bi bi-trash me-0.5"></i> ลบ
                                        </span>
                                    @elseif($activity->event == 'stock_added')
                                        <span class="badge-mini bg-info-light text-info border border-info">
                                            <i class="bi bi-box-seam me-0.5"></i> สต็อก
                                        </span>
                                    @else
                                        <span class="badge-mini bg-secondary text-white">{{ $activity->event }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-person text-secondary text-xs"></i>
                                        </div>
                                        <span class="text-dark fw-medium text-sm">{{ $activity->causer?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <div class="bg-light rounded p-2 border border-light-subtle">
                                        <ul class="list-unstyled mb-0">
                                            @php $hasVisibleAttributes = false; @endphp
                                            
                                            @forelse ($activity->properties['attributes'] ?? [] as $key => $value)
                                                @php
                                                    $old = $activity->properties['old'][$key] ?? null;
                                                    if ($key === 'updated_at') continue;

                                                    // ตรวจสอบสิทธิ์การดูข้อมูลลับ
                                                    $canViewSensitive = auth()->check() && (
                                                        in_array(auth()->user()->role ?? '', ['admin', 'manager']) || 
                                                        (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole(['admin', 'manager'])) ||
                                                        (method_exists(auth()->user(), 'can') && auth()->user()->can('view-sensitive-logs'))
                                                    );

                                                    if (in_array($key, $sensitiveFields) && !$canViewSensitive) {
                                                        continue;
                                                    }

                                                    $hasVisibleAttributes = true;

                                                    // แปลงค่า Boolean
                                                    $displayOld = $old;
                                                    $displayNew = $value;

                                                    if (in_array($key, ['is_active', 'status', 'active'])) {
                                                        if (!is_null($old)) {
                                                            $displayOld = ($old == 1 || $old === true) ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                                                        }
                                                        $displayNew = ($value == 1 || $value === true) ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                                                    }
                                                @endphp
                                                
                                                <li class="mb-1 last:mb-0 pb-1 border-bottom border-light-subtle last:border-0 text-xs">
                                                    <div class="d-flex align-items-start gap-2">
                                                        <strong class="text-secondary text-uppercase text-xs" style="min-width: 100px; max-width: 100px; display: inline-block;">
                                                            {{ $fieldTranslations[$key] ?? str_replace('_', ' ', ucfirst($key)) }}:
                                                        </strong>

                                                        @if ($key === 'image' && $value)
                                                            <!-- การแสดงผลรูปภาพ -->
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if ($old && $old !== $value)
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="text-muted text-xs">เดิม</span>
                                                                        <img src="{{ asset('storage/' . $old) }}" class="img-thumbnail p-0" style="height: 40px; width: auto; object-fit: cover;">
                                                                    </div>
                                                                    <i class="bi bi-arrow-right text-muted text-xs mx-1"></i>
                                                                @endif
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <span class="text-muted text-xs">ใหม่</span>
                                                                    <img src="{{ asset('storage/' . $value) }}" class="img-thumbnail p-0" style="height: 40px; width: auto; object-fit: cover;">
                                                                </div>
                                                            </div>

                                                        @elseif ($key === 'barcode' && $value)
                                                            <!-- การแสดงผลแท่งบาร์โค้ดจริงที่สแกนได้ -->
                                                            <div class="d-flex align-items-center gap-2">
                                                                @if ($old && $old !== $value)
                                                                    <div class="d-flex flex-column align-items-center bg-white p-1 border rounded" style="min-width: 110px;">
                                                                        <span class="text-danger text-decoration-line-through fw-bold text-xs mb-1" style="font-size: 0.65rem !important;">{{ $old }}</span>
                                                                        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($old) }}&code=Code128" style="height: 25px; max-width: 100px; object-fit: contain;">
                                                                    </div>
                                                                    <i class="bi bi-arrow-right text-muted text-xs mx-1"></i>
                                                                @endif
                                                                <div class="d-flex flex-column align-items-center bg-white p-1 border rounded" style="min-width: 110px;">
                                                                    <span class="text-success fw-bold text-xs mb-1" style="font-size: 0.65rem !important;">{{ $value }}</span>
                                                                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($value) }}&code=Code128" style="height: 25px; max-width: 100px; object-fit: contain;">
                                                                </div>
                                                            </div>

                                                        @else
                                                            <!-- ตัวหนังสือแบบกระชับทั่วไป -->
                                                            <div class="d-flex flex-wrap align-items-center gap-1.5 text-break">
                                                                @if ($old !== $value && !is_null($old))
                                                                    <span class="text-danger text-decoration-line-through bg-danger-light px-1.5 py-0.5 rounded text-xs">
                                                                        {{ $displayOld }}
                                                                    </span>
                                                                    <i class="bi bi-arrow-right text-muted text-xs" style="font-size: 0.7rem;"></i>
                                                                @endif
                                                                <span class="text-success fw-bold bg-success-light px-1.5 py-0.5 rounded text-xs">
                                                                    {{ $displayNew }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="text-muted text-xs fst-italic">ไม่มีข้อมูลรายละเอียดการเปลี่ยนแปลง</li>
                                            @endforelse

                                            @if(!$hasVisibleAttributes)
                                                <li class="text-muted text-xs fst-italic">ไม่มีรายละเอียดการเปลี่ยนแปลงที่คุณสามารถเข้าถึงได้</li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle p-3 mb-2">
                                            <i class="bi bi-inbox fs-3 text-muted"></i>
                                        </div>
                                        <h6 class="text-muted fw-bold mb-1">ไม่พบประวัติการเปลี่ยนแปลง</h6>
                                        <p class="text-secondary text-xs mb-0">ลองเปลี่ยนเงื่อนไขวันที่ หรือตัวกรองการค้นหา</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($activities->hasPages())
                <div class="card-footer bg-white py-2">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .text-xs {
        font-size: 0.75rem !important;
    }
    .text-sm {
        font-size: 0.85rem !important;
    }
    
    .table-compact td, 
    .table-compact th {
        padding: 0.5rem 0.5rem !important;
    }

    .badge-mini {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.5rem;
        font-size: 0.72rem;
        font-weight: 600;
        border-radius: 50rem;
    }

    .bg-success-light { background-color: rgba(25, 135, 84, 0.08) !important; }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.08) !important; }
    .bg-danger-light  { background-color: rgba(220, 53, 69, 0.08) !important; }
    .bg-info-light    { background-color: rgba(13, 202, 240, 0.08) !important; }

    .table-hover tbody tr:hover {
        background-color: #f9fafb;
    }
    .last\:mb-0:last-child {
        margin-bottom: 0 !important;
    }
    .last\:border-0:last-child {
        border-bottom: 0 !important;
    }
    .gap-1\.5 {
        gap: 0.375rem !important;
    }
</style>
@endsection