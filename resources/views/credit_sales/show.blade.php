@extends('layouts.layout')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                💳 รายละเอียดบิลเงินเชื่อ
            </h2>

            <div class="text-muted">
                เลขที่บิล #{{ $sale->id }}
            </div>

        </div>

        <div class="d-flex gap-2">

            <a
                href="{{ route('staff.credit.sales.index') }}"
                class="btn btn-secondary"
            >
                ← กลับ
            </a>

            <button
                class="btn btn-dark"
                onclick="window.print()"
            >
                🖨 พิมพ์บิล
            </button>

        </div>

    </div>

    <div class="row">

        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- Customer --}}
            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">
                        👤 ข้อมูลลูกค้า
                    </h5>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <div class="text-muted small">
                                ชื่อลูกค้า
                            </div>

                            <div class="fw-bold fs-5">
                                {{ $sale->customer->name ?? '-' }}
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="text-muted small">
                                เบอร์โทร
                            </div>

                            <div class="fw-bold fs-5">
                                {{ $sale->customer->phone ?? '-' }}
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="text-muted small">
                                วันที่ขาย
                            </div>

                            <div class="fw-bold">
                                {{ optional($sale->created_at)->format('d/m/Y H:i') }}
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">

                            <div class="text-muted small">
                                วันครบกำหนด
                            </div>

                            <div class="fw-bold">

                                @if($sale->due_date)

                                    {{ \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') }}

                                @else

                                    -

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Product List --}}
            <div class="card shadow border-0">

                <div class="card-header bg-dark text-white">

                    <h5 class="mb-0">
                        🛒 รายการสินค้า
                    </h5>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle mb-0">

                            <thead class="table-light text-center">

                                <tr>

                                    <th width="60">
                                        #
                                    </th>

                                    <th>
                                        สินค้า
                                    </th>

                                    <th width="120">
                                        จำนวน
                                    </th>

                                    <th width="150">
                                        ราคาต่อหน่วย
                                    </th>

                                    <th width="150">
                                        รวม
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($sale->items as $index => $item)

                                <tr>

                                    <td class="text-center">
                                        {{ $index + 1 }}
                                    </td>

                                    <td>

                                        <div class="fw-bold">
                                            {{ $item->product->name ?? '-' }}
                                        </div>

                                        @if($item->unit)

                                        <small class="text-muted">
                                            หน่วย:
                                            {{ $item->unit->unit_name }}
                                        </small>

                                        @endif

                                    </td>

                                    <td class="text-center fw-bold">

                                        {{ $item->qty }}

                                    </td>

                                    <td class="text-end">

                                        {{ number_format($item->price,2) }}

                                    </td>

                                    <td class="text-end fw-bold text-success">

                                        {{ number_format(
                                            $item->qty * $item->price,
                                            2
                                        ) }}

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="5" class="text-center py-4 text-muted">

                                        ไม่มีรายการสินค้า

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">

            {{-- Summary --}}
            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">
                        💰 สรุปยอดชำระ
                    </h5>

                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            ยอดรวมทั้งหมด
                        </span>

                        <span class="fw-bold fs-5">

                            {{ number_format($sale->total_amount,2) }}

                        </span>

                    </div>

                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            ชำระแล้ว
                        </span>

                        <span class="fw-bold text-success fs-5">

                            {{ number_format($sale->paid_amount,2) }}

                        </span>

                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">

                        <span class="fw-bold">
                            คงเหลือ
                        </span>

                        <span class="fw-bolder text-danger fs-4">

                            {{ number_format($sale->remaining_amount,2) }}

                        </span>

                    </div>

                </div>

            </div>

           

        </div>

    </div>

</div>

@endsection