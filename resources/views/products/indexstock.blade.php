<!-- resources/views/products/index.blade.php -->
@extends('layouts.layout')

@section('content')

<div class="container mt-4">
    <h2>รายการสินค้าในสต็อก</h2>

    @if (session('success'))
        <x-sweet-alert 
            icon="success" 
            title="สำเร็จ!" 
            text="{{ session('success') }}" 
            confirm-button-text="Ok"
        />
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>รูปภาพ</th>
                <th>ชื่อสินค้า</th>
                <th>บาร์โค้ด</th>
                <th>ราคาขาย</th>
                <th>จำนวนคงเหลือ</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>
                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" width="100">
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ number_format($product->sale_price, 2) }} บาท</td>
                    <td> {{ $product->quantity }}</td>
                    <td>
                        @if ($product->is_active)
                            <span class="badge badge-success">เปิดขาย</span>
                        @else
                            <span class="badge badge-danger">ปิดการขาย</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบสินค้านี้หรือไม่?')">ลบ</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }} <!-- Pagination -->

</div>

@endsection
