@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-clock-history text-primary me-2"></i>ประวัติการเปลี่ยนแปลงสินค้า
            </h2>
            <p class="text-muted small mb-0">ตรวจสอบรายการ Activity Log ย้อนหลังทั้งหมด</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label text-muted small fw-bold">📅 ตั้งแต่วันที่</label>
                        <input type="date" id="date_from" name="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label text-muted small fw-bold">📅 ถึงวันที่</label>
                        <input type="date" id="date_to" name="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="event" class="form-label text-muted small fw-bold">⚡ ประเภทการกระทำ</label>
                        <select name="event" id="event" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>✨ เพิ่มข้อมูล (Created)</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>📝 แก้ไข (Updated)</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>🗑️ ลบ (Deleted)</option>
                            </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100 fw-bold">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                            <a href="{{ route('product.products.allHistory') }}" class="btn btn-light border w-100 text-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> รีเซ็ต
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase small fw-bold" style="width: 15%">สินค้า</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" style="width: 15%">วันที่ & เวลา</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" style="width: 10%">ประเภท</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" style="width: 15%">ทำรายการโดย</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" style="width: 45%">รายละเอียดการเปลี่ยนแปลง</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($activities as $activity)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark d-block text-truncate" style="max-width: 200px;">
                                        {{ $activity->subject?->name ?? 'ไม่พบข้อมูลสินค้า' }}
                                    </span>
                                    <small class="text-muted">ID: {{ $activity->subject_id }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $activity->created_at->format('d/m/Y') }}</span>
                                        <span class="text-muted small">{{ $activity->created_at->format('H:i:s') }} น.</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($activity->event == 'created')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                            <i class="bi bi-plus-lg me-1"></i> เพิ่ม
                                        </span>
                                    @elseif($activity->event == 'updated')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">
                                            <i class="bi bi-pencil me-1"></i> แก้ไข
                                        </span>
                                    @elseif($activity->event == 'deleted')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill">
                                            <i class="bi bi-trash me-1"></i> ลบ
                                        </span>
                                    @elseif($activity->event == 'stock_added')
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill">
                                            <i class="bi bi-box-seam me-1"></i> สต็อก
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $activity->event }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                        <span class="text-dark fw-medium">{{ $activity->causer?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="bg-light rounded p-3 border">
                                        <ul class="list-unstyled mb-0">
                                            @forelse ($activity->properties['attributes'] ?? [] as $key => $value)
                                                @php
                                                    $old = $activity->properties['old'][$key] ?? null;
                                                    // กรองฟิลด์ที่ไม่จำเป็น (เช่น updated_at)
                                                    if ($key === 'updated_at') continue;
                                                @endphp
                                                
                                                <li class="mb-2 last:mb-0 pb-2 border-bottom border-light last:border-0">
                                                    <strong class="text-secondary text-uppercase small d-block mb-1">
                                                        {{ str_replace('_', ' ', ucfirst($key)) }}
                                                    </strong>

                                                    @if ($key === 'image' && $value)
                                                        {{-- กรณีเป็นรูปภาพ --}}
                                                        <div class="d-flex align-items-center gap-3 mt-2">
                                                            @if ($old && $old !== $value)
                                                                <div class="text-center">
                                                                    <div class="badge bg-danger mb-1">Old</div><br>
                                                                    <img src="{{ asset('storage/' . $old) }}" class="img-thumbnail" style="height: 60px; width: auto;">
                                                                </div>
                                                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                                                            @endif
                                                            <div class="text-center">
                                                                <div class="badge bg-success mb-1">New</div><br>
                                                                <img src="{{ asset('storage/' . $value) }}" class="img-thumbnail" style="height: 60px; width: auto;">
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- กรณีเป็นข้อความปกติ --}}
                                                        <div class="d-flex flex-wrap align-items-center gap-2 text-break">
                                                            @if ($old !== $value && !is_null($old))
                                                                <span class="text-danger text-decoration-line-through bg-danger bg-opacity-10 px-2 rounded">
                                                                    {{ $old }}
                                                                </span>
                                                                <i class="bi bi-arrow-right text-muted small"></i>
                                                            @endif
                                                            <span class="text-success fw-bold bg-success bg-opacity-10 px-2 rounded">
                                                                {{ $value }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="text-muted small fst-italic">ไม่มีข้อมูลรายละเอียดการเปลี่ยนแปลง</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted fw-bold">ไม่พบประวัติการเปลี่ยนแปลง</h5>
                                        <p class="text-secondary small">ลองเปลี่ยนเงื่อนไขวันที่ หรือตัวกรองการค้นหา</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($activities->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* CSS เพิ่มเติมเล็กน้อย */
    .table-hover tbody tr:hover {
        background-color: #f9fafb;
    }
    .last\:mb-0:last-child {
        margin-bottom: 0 !important;
    }
    .last\:border-0:last-child {
        border-bottom: 0 !important;
    }
</style>
@endsection