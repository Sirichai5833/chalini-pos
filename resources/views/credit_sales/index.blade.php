@extends('layouts.layout')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid py-4" style="font-family: 'Kanit', sans-serif;">
    
   <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bolder text-dark mb-1">
            <span class="me-2"></span>ระบบบริหารลูกหนี้เงินเชื่อ
        </h2>
        <p class="text-muted small mb-0">
            ตรวจสอบ ตรวจทาน และติดตามยอดค้างชำระแยกตามรายบุคคล
        </p>
    </div>

    <button class="btn btn-primary rounded-3 shadow-sm fw-bold"
        data-bs-toggle="modal"
        data-bs-target="#addCustomerModal">
        <i class="bi bi-person-plus-fill me-1"></i>
        เพิ่มลูกค้า
    </button>
</div>

    @php
        // คำนวณภาพรวมยอดหนี้ของลูกค้าทั้งหมดในหน้าเพื่อแสดงบนการ์ดด้านบน
        $grandTotalAmount = 0;
        $grandRemaining = 0;
        $grandOverdueBills = 0;

        foreach($customers as $c) {
            $s = $c->creditSales;
            $grandTotalAmount += $s->sum('total_amount');
            $grandRemaining += $s->sum('remaining_amount');
            $grandOverdueBills += $s->filter(function($item) {
                return $item->status != 'paid' && $item->due_date && now()->gt($item->due_date);
            })->count();
        }
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white" style="border-left: 5px solid #0d6efd !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold">วงเงินเชื่อสะสมทั้งหมด</span>
                        <h3 class="fw-bolder text-primary mb-0 mt-1">{{ number_format($grandTotalAmount, 2) }} <span class="fs-6 fw-normal">บาท</span></h3>
                    </div>
                    <div class="text-primary bg-primary bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white" style="border-left: 5px solid #dc3545 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger small fw-bold">💰 ยอดค้างชำระรวมขณะนี้</span>
                        <h3 class="fw-bolder text-danger mb-0 mt-1">{{ number_format($grandRemaining, 2) }} <span class="fs-6 fw-normal">บาท</span></h3>
                    </div>
                    <div class="text-danger bg-danger bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-exclamation-octagon fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning-emphasis small fw-bold">⚠️ บิลที่เลยกำหนดชำระ</span>
                        <h3 class="fw-bolder text-warning-emphasis mb-0 mt-1">{{ $grandOverdueBills }} <span class="fs-6 fw-normal">บิล</span></h3>
                    </div>
                    <div class="text-warning bg-warning bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-secondary"></i> รายชื่อลูกหนี้
            </h5>
            <span class="badge bg-secondary rounded-pill">ทั้งหมด {{ $customers->count() }} รายชื่อ</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1100px;">
                    <thead class="bg-dark text-white text-center small tracking-wider" style="--bs-bg-opacity: 0.95;">
                        <tr>
                            <th rowspan="2" class="py-3 border-end border-light border-opacity-10" style="width: 60px;">ID</th>
                            <th rowspan="2" class="text-start py-3 border-end border-light border-opacity-10">ข้อมูลลูกค้า</th>
                            <th colspan="3" class="py-2 border-bottom border-light border-opacity-10 border-end border-light border-opacity-10#fdfdfe bg-opacity-10">สรุปยอดเงิน (บาท)</th>
                            <th colspan="3" class="py-2 border-bottom border-light border-opacity-10 border-end border-light border-opacity-10 #fdfdfe bg-opacity-20">สรุปจำนวนบิล</th>
                            <th rowspan="2" class="py-3 border-end border-light border-opacity-10" style="width: 120px;">เครดิตเครือข่าย</th>
                            <th rowspan="2" class="py-3 border-end border-light border-opacity-10">
    สถานะ
</th>

<th rowspan="2" class="py-3" style="width: 160px;">
    การจัดการ
</th>
                        </tr>
                        <tr class="small text-muted" style="font-size: 0.82rem;">
                            <th class="py-2 #fdfdfe bg-opacity-10  decoration-black">รวมสะสม</th>
                            <th class="py-2 #fdfdfe bg-opacity-10 decoration-black">ยอดค้าง</th>
                            <th class="py-2 #fdfdfe bg-opacity-10  decoration-black border-end border-light border-opacity-10">ชำระแล้ว</th>
                            <th class="py-2 #fdfdfe bg-opacity-20  decoration-black">บิลทั้งหมด</th>
                            <th class="py-2 #fdfdfe bg-opacity-20  decoration-black">คงค้าง</th>
                            <th class="py-2 #fdfdfe bg-opacity-20 decoration-black border-end border-light border-opacity-10">เลยกำหนด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        @php
                            $sales = $customer->creditSales;

                            $totalAmount = $sales->sum('total_amount');
                            $remaining = $sales->sum('remaining_amount');
                            $paid = $sales->sum('paid_amount');

                            $totalBills = $sales->count();
                            $unpaidBills = $sales->where('status','!=','paid')->count();

                            $overdueBills = $sales->filter(function($s){
                                return $s->status != 'paid'
                                    && $s->due_date
                                    && now()->gt($s->due_date);
                            })->count();

                            // คำนวณสถานะเกณฑ์เสี่ยง
                            if ($remaining == 0) {
                                $status = 'good';
                            } elseif ($overdueBills > 0) {
                                $status = 'danger';
                            } else {
                                $status = 'warning';
                            }
                        @endphp

                        <tr>
                            <td class="text-center text-muted small">#{{ $customer->id }}</td>
                            
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark fs-6">{{ $customer->name }}</span>
                                    <span class="text-muted small mt-0.5">
                                        <i class="bi bi-telephone me-1"></i>{{ $customer->phone ?? 'ไม่มีเบอร์โทรศัพท์' }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-end px-3 fw-semibold text-secondary">
                                {{ number_format($totalAmount, 2) }}
                            </td>

                            <td class="text-end px-3 fw-bold {{ $remaining > 0 ? 'text-danger bg-danger bg-opacity-10' : 'text-muted' }}">
                                {{ $remaining > 0 ? number_format($remaining, 2) : '0.00' }}
                            </td>

                            <td class="text-end px-3 fw-semibold text-success">
                                {{ number_format($paid, 2) }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2 py-1.5 rounded-pill">{{ $totalBills }}</span>
                            </td>

                            <td class="text-center">
                                @if($unpaidBills > 0)
                                    <span class="badge bg-light text-danger border border-danger border-opacity-25 px-2 py-1.5 rounded-pill">{{ $unpaidBills }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($overdueBills > 0)
                                    <span class="badge bg-danger px-2 py-1.5 rounded-pill animate-pulse">{{ $overdueBills }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($status == 'good')
                                    <span class="badge px-2.5 py-1.5 rounded-3 d-inline-flex align-items-center gap-1" style="background-color: #e6f4ea; color: #137333;">
                                        <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> ปกติ
                                    </span>
                                @elseif($status == 'warning')
                                    <span class="badge px-2.5 py-1.5 rounded-3 d-inline-flex align-items-center gap-1" style="background-color: #fef7e0; color: #b06000;">
                                        <span class="rounded-circle bg-warning" style="width: 6px; height: 6px;"></span> เฝ้าระวัง
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1.5 rounded-3 d-inline-flex align-items-center gap-1" style="background-color: #fce8e6; color: #c5221f;">
                                        <span class="rounded-circle bg-danger" style="width: 6px; height: 6px;"></span> เสี่ยงสูง
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">

    @if($customer->is_active)
        <span class="badge bg-success rounded-pill px-3 py-2">
            ใช้งาน
        </span>
    @else
        <span class="badge bg-danger rounded-pill px-3 py-2">
            ถูกระงับ
        </span>
    @endif

</td>

<td class="text-center">

    <div class="d-flex justify-content-center gap-2">

        <a href="{{ route('staff.credit.customer', $customer->id) }}"
           class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-semibold">
            <i class="bi bi-search"></i>
            ดูบิล
        </a>

        @if($customer->is_active)

            <button class="btn btn-sm btn-outline-danger rounded-3 fw-semibold"
                onclick="suspendCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                <i class="bi bi-person-x-fill"></i>
            </button>

        @else

            <button class="btn btn-sm btn-outline-success rounded-3 fw-semibold"
                onclick="restoreCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>

        @endif

    </div>

</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

function saveCustomer() {

    const name = document.getElementById('customerName').value;
    const phone = document.getElementById('customerPhone').value;

    if (!name) {

        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกชื่อลูกค้า'
        });

        return;
    }

    fetch('/members/customers', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },

        body: JSON.stringify({
            name: name,
            phone: phone
        })

    })

    .then(res => res.json())

    .then(data => {

        if (data.success) {

            Swal.fire({
                icon: 'success',
                title: 'เพิ่มลูกค้าสำเร็จ',
                text: 'ระบบได้บันทึกลูกค้าใหม่แล้ว'
            }).then(() => {
                location.reload();
            });

        } else {

            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: data.message || 'ไม่สามารถเพิ่มลูกค้าได้'
            });

        }

    });

}

function suspendCustomer(id, name) {

    Swal.fire({
        icon: 'warning',
        title: 'ระงับลูกค้า?',
        text: `ต้องการระงับ ${name} ใช่หรือไม่`,
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    })

    .then((result) => {

        if (result.isConfirmed) {

            fetch(`/customers/${id}/suspend`, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }

            })

            .then(res => res.json())

            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'ระงับลูกค้าสำเร็จ'
                    }).then(() => {
                        location.reload();
                    });

                }

            });

        }

    });

}

function restoreCustomer(id, name) {

    Swal.fire({
        icon: 'question',
        title: 'เปิดใช้งานลูกค้า?',
        text: `ต้องการเปิดใช้งาน ${name} ใช่หรือไม่`,
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    })

    .then((result) => {

        if (result.isConfirmed) {

            fetch(`/customers/${id}/restore`, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }

            })

            .then(res => res.json())

            .then(data => {

                if (data.success) {

                    Swal.fire({
                        icon: 'success',
                        title: 'เปิดใช้งานลูกค้าสำเร็จ'
                    }).then(() => {
                        location.reload();
                    });

                }

            });

        }

    });

}

</script>
<!-- Modal เพิ่มลูกค้า -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    เพิ่มลูกค้าใหม่
                </h5>

                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-bold">
                        ชื่อลูกค้า
                    </label>

                    <input type="text"
                        id="customerName"
                        class="form-control rounded-3"
                        placeholder="กรอกชื่อลูกค้า">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">
                        เบอร์โทรศัพท์
                    </label>

                    <input type="text"
                        id="customerPhone"
                        class="form-control rounded-3"
                        placeholder="กรอกเบอร์โทร">
                </div>

            </div>

            <div class="modal-footer border-0">
                <button class="btn btn-light border rounded-3"
                    data-bs-dismiss="modal">
                    ยกเลิก
                </button>

                <button class="btn btn-primary rounded-3 fw-bold"
                    onclick="saveCustomer()">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    บันทึก
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    /* เอฟเฟกต์เพื่อให้ปุ่มและแถวตารางขยับดูละมุนขึ้น */
    .table-hover tbody tr:hover {
        background-color: #fdfdfe !important;
    }
    .btn-outline-primary:hover {
        transform: translateY(-1px);
        box-on: 0 4px 6px #fdfdfe;
    }
    .shadow-xs {
        box-shadow: 0 1px 2px #fdfdfe;
    }
</style>
@endsection