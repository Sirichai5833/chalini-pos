<!-- resources/views/units/create.blade.php -->

@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>เพิ่มหน่วยนับสินค้า</h2>

    <form action="{{ route('units.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_id">เลือกสินค้า:</label>
            <select name="product_id" id="product_id" class="form-control" required>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="unit_name">ชื่อหน่วยนับ:</label>
            <input type="text" class="form-control" id="unit_name" name="unit_name" required>
        </div>

        <div class="form-group">
            <label for="unit_quantity">จำนวนหน่วย:</label>
            <input type="number" class="form-control" id="unit_quantity" name="unit_quantity" required>
        </div>

        <div class="form-group">
            <label for="barcode">บาร์โค้ด:</label>
            <input type="text" class="form-control" id="barcode" name="barcode" required>
        </div>

        <div class="form-group">
            <label for="price">ราคา:</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="cost_price">ราคาทุน:</label>
            <input type="number" class="form-control" id="cost_price" name="cost_price" step="0.01">
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>

@endsection
