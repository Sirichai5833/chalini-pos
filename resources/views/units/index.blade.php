@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>ดูหน่วยสินค้าตามสินค้า</h2>
        <a href="{{ route('units.create') }}" class="btn btn-success">+ เพิ่มหน่วยสินค้า</a>
    </div>

    {{-- เลือกสินค้า --}}
    <form method="GET" action="{{ route('units.byProduct') }}" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="product_id" class="form-control" required>
                    <option value="">-- เลือกสินค้า --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </div>
    </form>

    @if(isset($units) && count($units) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>หน่วยนับ</th>
                    <th>จำนวนหน่วย</th>
                    <th>ราคาขาย</th>
                    <th>ราคาทุน</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $unit)
                    <tr>
                        <td>{{ $unit->id }}</td>
                        <td>{{ $unit->unit_name }}</td>
                        <td>{{ $unit->unit_quantity }}</td>
                        <td>{{ number_format($unit->price, 2) }}</td>
                        <td>{{ number_format($unit->cost_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(request('product_id'))
        <div class="alert alert-warning">ไม่พบหน่วยของสินค้าที่เลือก</div>
    @endif
</div>
@endsection
