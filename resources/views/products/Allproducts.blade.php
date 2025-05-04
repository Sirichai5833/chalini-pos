@extends('layouts.layout')

@section('content')

@if (session('success'))
<x-sweet-alert 
    icon="success" 
    title="Oh Yeah!" 
    text="{{ session('success') }}" 
    confirm-button-text="Ok"
/>
@endif

@if (session('error'))
<x-sweet-alert 
    icon="error" 
    title="Oops..." 
    text="{{ session('error') }}" 
    confirm-button-text="Ok"
/>
@endif

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Products</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ เพิ่มสินค้าใหม่</a>
    </div>

    <!-- ฟอร์มเลือกประเภทสินค้า -->
    <form method="GET" action="{{ route('products.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label for="category" class="form-label">เลือกประเภทสินค้า</label>
                <select name="category_id" id="category" class="form-select">
                    <option value="">เลือกทั้งหมด</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">กรอง</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>ชื่อสินค้า</th>
                <th>บาร์โค้ด</th>
                <th>รหัสสินค้า (SKU)</th>
                <th>ราคาต้นทุน</th>
                <th>ราคาโปรโมชั่น</th>
                <th>สถานะสินค้า</th>
                <th>จำนวนคงเหลือ</th>
                <th>รูปภาพ</th> <!-- คอลัมน์รูปภาพ -->
                <th>ตัวเลือก</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ number_format($product->cost_price, 2) }} บาท</td>
                    <td>{{ number_format($product->promotion_price, 2) }} บาท</td>
                    <td>{{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <!-- แสดงรูปภาพสินค้า -->
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50">
                        @else
                            <span>ไม่มีรูปภาพ</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('คุณแน่ใจหรือไม่ว่าจะลบ?')" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">ไม่มีข้อมูลสินค้า</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
