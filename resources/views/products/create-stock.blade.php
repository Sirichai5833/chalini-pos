@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2>บันทึกสินค้าเข้าสต็อก</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('staff.inventory_items.store') }}" method="POST">
        @csrf

        <!-- เลือกสินค้า (จาก product_variant) -->
        <div class="form-group">
            <label for="product_variant_id">สินค้า</label>
            <select name="product_variant_id" id="product_variant_id" class="form-control" required>
                <option value="">-- เลือกสินค้า --</option>
                @foreach($inventoryItems as $item)
                    <option value="{{ $item->id }}">{{ $item->product_variant_id }}</option>
                @endforeach
            </select>
        </div>

        <!-- เลือกคลังเก็บ -->
        <div class="form-group">
            <label for="location_id">คลังสินค้า</label>
            <select name="location_id" id="location_id" class="form-control" required>
                <option value="">-- เลือกคลัง --</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- รหัสล็อต -->
        <div class="form-group">
            <label for="batch_number">รหัสล็อต (ถ้ามี)</label>
            <input type="text" name="batch_number" id="batch_number" class="form-control">
        </div>

        <!-- วันหมดอายุ -->
        <div class="form-group">
            <label for="expiry_date">วันหมดอายุ (ถ้ามี)</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control">
        </div>

        <!-- จำนวน -->
        <div class="form-group">
            <label for="quantity">จำนวน</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>

        <!-- จำนวนหน่วยย่อย (unit_quantity) -->
<div class="form-group">
    <label for="unit_quantity">จำนวนหน่วยย่อย</label>
    <input type="number" name="unit_quantity" id="unit_quantity" class="form-control" required min="1" value="1">
</div>

        <!-- ราคาทุน -->
        <div class="form-group">
            <label for="cost_price">ราคาทุน</label>
            <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" required>
        </div>

        <!-- ราคาขาย -->
        <div class="form-group">
            <label for="sale_price">ราคาขาย</label>
            <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">บันทึกเข้าสต็อก</button>
    </form>
</div>
@endsection
