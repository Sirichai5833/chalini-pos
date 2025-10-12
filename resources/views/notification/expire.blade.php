@extends('layouts.layout')

@section('content')
<div class="container py-4"> {{-- Use Bootstrap's container for responsive width and py-4 for vertical padding --}}
    <h2 class="mb-4 text-primary fw-bold text-center">
        <i class="bi bi-hourglass-split me-2"></i> สินค้าใกล้หมดอายุ
    </h2>

    <div class="card shadow-lg rounded-3 border-0"> {{-- Card component with strong shadow, rounded corners, and no default border --}}
        <div class="card-body p-4"> {{-- More padding inside the card body --}}
            <div class="table-responsive"> {{-- Ensures table is scrollable on small screens --}}
                <table class="table table-striped table-hover text-center align-middle mb-0"> {{-- Striped, hover, and aligned middle --}}
                    <thead class="bg-primary text-white"> {{-- Primary background for header --}}
                        <tr>
                            <th scope="col" class="py-3">ชื่อสินค้า</th>
                            <th scope="col" class="py-3">รหัสล็อต</th>
                            <th scope="col" class="py-3">จำนวน</th>
                            <th scope="col" class="py-3">หน่วยนับ</th>
                            <th scope="col" class="py-3">วันหมดอายุ</th>
                            <th scope="col" class="py-3">เหลืออีก (วัน)</th>
                            <th scope="col" class="py-3">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nearExpiryProducts as $item)
                            @php
                                $expiryDate = \Carbon\Carbon::parse($item->expiry_date)->startOfDay(); // Remove time for accurate day comparison
                                $today = now()->startOfDay(); // Remove time
                                $diffDays = $today->diffInDays($expiryDate, false); // Expiry date - today

                                $rowClass = '';
                                if ($diffDays < 0) {
                                    $rowClass = 'table-danger fw-bold'; // Expired, make row bold
                                } elseif ($diffDays <= 7) {
                                    $rowClass = 'table-danger'; // Expiring within 7 days
                                } elseif ($diffDays <= 14) {
                                    $rowClass = 'table-warning'; // Expiring within 14 days
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="text-start">{{ $item->name }}</td> {{-- Align product name to start --}}
                                <td>{{ $item->batch_code ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit_name }}</td>
                                <td>{{ $expiryDate->format('d/m/Y') }}</td>
                                <td>
                                    @if ($diffDays < 0)
                                        <span class="badge bg-danger">หมดอายุแล้ว <i class="bi bi-x-circle-fill"></i></span>
                                    @elseif ($diffDays == 0)
                                        <span class="badge bg-warning text-dark">หมดอายุวันนี้ <i class="bi bi-exclamation-circle-fill"></i></span>
                                    @else
                                        <span class="badge bg-info text-dark">เหลืออีก {{ $diffDays }} วัน</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('notification.expiry.acknowledge', $item->id) }}" method="POST" onsubmit="return confirm('ยืนยันว่าจัดการแล้ว?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success shadow-sm">
                                            <i class="bi bi-check-circle-fill me-1"></i> จัดการแล้ว
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
                                    <p class="text-success fw-semibold mb-0">✅ ไม่มีสินค้าใกล้หมดอายุ</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection