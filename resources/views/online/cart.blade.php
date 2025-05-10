@extends('layouts.online')

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

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .cart-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
</style>
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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cart as $id => $item)
                <tr>
                    <td class="d-flex align-items-center">
                        <img src="{{ asset('images/' . $item['image']) }}" alt="{{ $item['name'] }}" class="cart-img me-3">
                        <div>
                            <strong>{{ $item['name'] }}</strong><br>
                            @if(isset($item['unit_name']))
                                {{ $item['unit_name'] }}
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="text-center">{{ number_format($item['price'], 2) }} บาท</td>
                    <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center">
                            <form action="{{ route('online.updateQuantity', $id) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-orange border-orange" name="action" value="decrease">-</button>
                                <input type="number" name="quantity" class="form-control text-center border-orange" value="{{ $item['quantity'] }}" min="1" max="999" style="width: 80px;" readonly>
                                <button class="btn btn-outline-orange border-orange" name="action" value="increase">+</button>
                            </form>
                        </div>
                    </td>
                    <td class="text-center fw-bold text-orange">
                        {{ number_format($item['price'] * $item['quantity'], 2) }} บาท
                    </td>
                    <td class="text-center">
                        <form action="#" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm rounded-circle" title="ลบสินค้า">
                                <i class="bi bi-trash"></i>ลบสินค้า
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
            <a href="{{ route('online.checkout') }}" class="btn btn-orange px-4 py-2">
                <i class="bi bi-wallet2 me-1"></i> ไปชำระเงิน
            </a>
        </div>
    </div>
</div>
@endsection
