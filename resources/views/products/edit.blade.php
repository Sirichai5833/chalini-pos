@extends('layouts.layout')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-4">
    <h2>แก้ไขสินค้า</h2>
    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')  <!-- ระบุว่าเป็น PUT method -->

        <!-- ชื่อสินค้า -->
        <div class="form-group">
            <label for="name">ชื่อสินค้า</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <!-- บาร์โค้ด -->
        <div class="form-group">
            <label for="barcode">บาร์โค้ด</label>
            <input type="text" name="barcode" id="barcode" class="form-control" value="{{ $product->barcode }}" required>
        </div>

        <!-- SKU -->
        <div class="form-group">
            <label for="sku">รหัส SKU</label>
            <input type="text" name="sku" id="sku" class="form-control" value="{{ $product->sku }}" required>
        </div>

        <!-- หน่วยนับ -->
        <div class="form-group">
            <label for="unit">หน่วยนับ</label>
            <input type="text" name="unit" id="unit" class="form-control" value="{{ $product->unit }}" required>
        </div>

        <!-- ราคาทุน -->
        <div class="form-group">
            <label for="cost_price">ราคาทุน</label>
            <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" value="{{ $product->cost_price }}" required>
        </div>

        <!-- ราคาขาย -->
        <div class="form-group">
            <label for="selling_price">ราคาขาย</label>
            <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ $product->selling_price }}" required>
        </div>

        <!-- ราคาพิเศษ -->
        <div class="form-group">
            <label for="promotion_price">ราคาพิเศษ</label>
            <input type="number" step="0.01" name="promotion_price" id="promotion_price" class="form-control" value="{{ $product->promotion_price }}">
        </div>

        <!-- ของแถม -->
        <div class="form-check mt-3">
            <input type="checkbox" name="has_gift" id="has_gift" class="form-check-input" {{ $product->has_gift ? 'checked' : '' }}>
            <label for="has_gift" class="form-check-label">มีของแถม</label>
        </div>
        <div class="form-group">
            <label for="gift_name">ชื่อของแถม</label>
            <input type="text" name="gift_name" id="gift_name" class="form-control" value="{{ $product->gift_name }}">
        </div>

        <!-- คลังสินค้า -->
        <div class="form-group">
            <label for="stock">จำนวนคงเหลือ</label>
            <input type="number" name="stock" id="stock" class="form-control" value="{{ $product->stock }}" required>
        </div>

        <div class="form-check">
            <input type="hidden" name="track_stock" value="0">
            <input type="checkbox" name="track_stock" id="track_stock" class="form-check-input" value="1" {{ $product->track_stock ? 'checked' : '' }}>
            <label for="track_stock" class="form-check-label">ติดตาม Stock</label>
        </div>

        <!-- สถานะ -->
        <div class="form-check">
            <input type="checkbox" name="is_online" id="is_online" class="form-check-input" {{ $product->is_online ? 'checked' : '' }}>
            <label for="is_online" class="form-check-label">ขายออนไลน์ได้</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $product->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">เปิดขายอยู่</label>
        </div>

        <!-- รูปภาพ -->
        <div class="form-group mt-3">
            <label for="image">รูปภาพสินค้า</label>
            <input type="file" name="image" id="image" class="form-control">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        <!-- คำอธิบาย -->
        <div class="form-group">
            <label for="description">คำอธิบาย</label>
            <textarea name="description" id="description" rows="3" class="form-control" required>{{ $product->description }}</textarea>
        </div>

        <!-- QR Code -->
        <div class="form-group">
            <label for="qr_code">QR Code</label>
            <input type="text" name="qr_code" id="qr_code" class="form-control" value="{{ $product->qr_code }}" required>
        </div>

        <!-- หมวดหมู่ -->
        <div class="form-group">
            <label for="category_id">หมวดหมู่</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3">อัปเดตสินค้า</button>
    </form>    
</div>
@endsection
