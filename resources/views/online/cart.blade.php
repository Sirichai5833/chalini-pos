@extends('layouts.online')
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@section('content')
    <style>
        .btn-orange {
            background-color: #ff5722;
            color: white;
        }

        .btn-orange:hover {
            background-color: #e64a19;
            color: white;
        }

        .text-orange {
            color: #ff5722;
        }

        .border-orange {
            border-color: #ff5722 !important;
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .cart-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        /* 👇 เพิ่มส่วนนี้เข้าไปเพื่อรองรับมือถือ */
        @media (max-width: 576px) {
            .cart-img {
                width: 50px;
                height: 50px;
            }

            td.d-flex.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 5px;
            }

            .input-group input {
                width: 60px !important;
            }

            .btn.btn-outline-orange {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .table th,
            .table td {
                font-size: 13px;
                padding: 0.4rem 0.3rem;
            }

            .btn-orange {
                width: 100%;
                text-align: center;
            }

            .text-end {
                width: 100%;
            }

            .btn-sm.rounded-circle {
                width: 32px;
                height: 32px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-orange {
                background-color: #ff5722;
                color: white;
                border: none;
            }

            .btn-orange:hover {
                background-color: #e64a19;
                color: white;
            }

            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
        }
    </style>
    @if(session('error'))
    <div class="alert alert-danger my-3">
        {{ session('error') }}
    </div>
@endif

    <div class="container py-4">
        <h2 class="mb-4 text-orange"><i class="bi bi-cart-fill me-2"></i>ตะกร้าสินค้า</h2>

        <div class="table-responsive">
            <table class="table align-middle shadow-sm">
                <thead class="text-center" style="background-color: #ffece4;">
                    <tr>
                        <th>สินค้า</th>
                        <th>ราคา</th>
                        <th style="width: 160px;">จำนวน</th>
                        <th>รวม</th>
                        <th>ลบสินค้า</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cart as $id => $item)
                        <tr>
                            <td class="d-flex align-items-center">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="100"
                                    height="100" class="me-2 rounded">
                                <div>
                                    <strong>{{ $item['name'] }}</strong><br>
                                    @if (isset($item['unit_name']))
                                        {{ $item['unit_name'] }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">{{ number_format($item['price'], 2) }} บาท</td>
                            <td class="text-center">
                                <div class="input-group input-group-sm justify-content-center" style="max-width: 140px;">
                                    <form action="{{ route('online.updateQuantity', $id) }}" method="POST"
                                        class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            class="btn btn-sm btn-orange rounded-circle d-flex justify-content-center align-items-center"
                                            name="action" value="decrease" style="width: 36px; height: 36px;">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>

                                        <input type="number" name="quantity" class="form-control text-center border-orange"
                                            value="{{ $item['quantity'] }}" min="1" max="999" readonly
                                            style="width: 60px;">

                                        <button
                                            class="btn btn-sm btn-orange rounded-circle d-flex justify-content-center align-items-center"
                                            name="action" value="increase" style="width: 36px; height: 36px;">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </form>

                                </div>
        </div>
        </td>
        <td class="text-center fw-bold text-orange">
            {{ number_format($item['price'] * $item['quantity'], 2) }} บาท
        </td>
        <td class="text-center align-middle">
            <form action="{{ route('online.remove', $id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 36px; height: 36px;"
                    title="ลบสินค้า">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>

        </td>

        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted">ไม่มีสินค้าในตะกร้า</td>
        </tr>
        @endforelse
        </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <div class="text-end">
            <h5 class="mb-3">ยอดรวมทั้งหมด: <strong class="text-orange">{{ number_format($total, 2) }}</strong> บาท</h5>
            <a href="{{ route('online.checkout.form', ['total' => $total]) }}" class="btn btn-orange px-4 py-2">
                <i class="bi bi-wallet2 me-1"></i> ไปชำระเงิน
            </a>
        </div>
    </div>
    </div>
@endsection
