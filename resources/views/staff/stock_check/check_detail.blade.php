@extends('layouts.layout')

@section('content')
    <div class="container py-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-1">รายละเอียดการตรวจนับสต็อก</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('staff.stock.check.report') }}"
                                class="text-decoration-none">รายงาน</a></li>
                        <li class="breadcrumb-item active" aria-current="page">รอบที่ {{ $check->cycle }}</li>
                    </ol>
                </nav>
            </div>

            <a href="{{ route('staff.stock.check.report') }}" class="btn btn-outline-secondary px-4">
                <span class="me-1">⬅️</span> กลับหน้ารายงาน
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light rounded-3">
                <div class="row g-3">
                    <div class="col-md-4 border-end-md">
                        <small class="text-muted text-uppercase fw-bold">วันที่ตรวจสอบ</small>
                        <div class="fs-5 fw-bold text-dark">{{ \Carbon\Carbon::parse($check->check_date)->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="col-md-4 border-end-md">
                        <small class="text-muted text-uppercase fw-bold"> รอบการนับ</small>
                        <div class="fs-5 fw-bold text-primary">{{ $check->cycle }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold">ผู้ตรวจสอบ</small>
                        <div class="fs-5 fw-bold text-dark">{{ $check->user->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($check->remark)
            <div class="alert alert-warning d-flex align-items-center shadow-sm" role="alert">
                <span class="fs-4 me-2"></span>
                <div>
                    <strong>หมายเหตุ:</strong> {{ $check->remark }}
                </div>
            </div>
        @endif

        <div class="card shadow border-0 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-secondary">รายการสินค้าที่ตรวจนับ</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 35%;">สินค้า</th>
                            <th class="text-center py-3">หน่วย</th>
                            <th class="text-center py-3">ในระบบ</th>
                            <th class="text-center py-3">นับจริง</th>
                            <th class="text-center py-3">ส่วนต่าง</th>
                            <th class="text-center py-3">สถานะ</th>
                            <th class="text-center py-3">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($check->items as $item)
                            @php
                                $rowClass = '';
                                $diffClass = 'text-muted';
                                $statusBadge = '';

                                if ($item->diff_qty == 0) {
                                    $statusBadge =
                                        '<span class="badge bg-success bg-opacity-10 text-success border border-success px-3">✅ ตรง</span>';
                                } elseif ($item->diff_qty > 0) {
                                    $diffClass = 'text-primary fw-bold';
                                    $statusBadge =
                                        '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3">🔼 เกิน</span>';
                                    $rowClass = 'table-primary table-opacity-10'; // ไฮไลท์แถวบางๆ
                                } else {
                                    $diffClass = 'text-danger fw-bold';
                                    $statusBadge =
                                        '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3">🔽 ขาด</span>';
                                    $rowClass = 'table-danger table-opacity-10'; // ไฮไลท์แถวบางๆ
                                }
                            @endphp

                            <tr class="{{ $item->diff_qty != 0 ? '' : '' }}">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $item->product->name ?? '-' }}</div>
                                    {{-- <small class="text-muted">Code: {{ $item->product->code ?? '' }}</small> --}}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ $item->unit->unit_name ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="fs-6">{{ number_format($item->system_qty) }}</span>
                                </td>

                                <td class="text-center bg-light">
                                    <span class="fs-6 fw-bold text-dark">{{ number_format($item->real_qty) }}</span>
                                </td>

                                <td class="text-center">
                                    <span class="fs-6 {{ $diffClass }}">
                                        {{ $item->diff_qty > 0 ? '+' : '' }}{{ number_format($item->diff_qty) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {!! $statusBadge !!}
                                </td>
                                <td class="text-center">
                                    @if ($item->note)
                                        <span class="text-muted small">
                                             {{ $item->note }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3 text-end text-muted small">
                รายการทั้งหมด: <strong>{{ count($check->items) }}</strong> รายการ
            </div>
        </div>
    </div>

    <style>
        /* CSS เพิ่มเติมเพื่อให้เส้นขอบใน Card info สวยงามบน Desktop */
        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid #dee2e6;
            }
        }
    </style>
@endsection
