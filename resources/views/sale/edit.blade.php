@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2>📝 แก้ไขการขาย #{{ $sale->id }}</h2>

    <form action="{{ route('staff.sales.update', $sale->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="sale_date" class="form-label">วันที่ขาย</label>
                <input type="datetime-local" name="sale_date" id="sale_date" class="form-control"
                       value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="col-md-4">
                <label for="sale_type" class="form-label">ประเภทการขาย</label>
                <select name="sale_type" id="sale_type" class="form-select" required>
                    <option value="retail" {{ $sale->sale_type == 'retail' ? 'selected' : '' }}>ขายปลีก</option>
                    <option value="wholesale" {{ $sale->sale_type == 'wholesale' ? 'selected' : '' }}>ขายส่ง</option>
                </select>
            </div>
        </div>

        <h5>สินค้า</h5>
        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>หน่วย</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $index => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $index }}][product_id]" class="form-select" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="items[{{ $index }}][product_unit_id]" class="form-select" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $item->product_unit_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][price]" class="form-control" step="0.01" value="{{ $item->price }}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">ลบ</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary mb-3" onclick="addRow()">➕ เพิ่มสินค้า</button>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">💾 บันทึกการแก้ไข</button>
            <a href="{{ route('staff.sales.show', $sale->id) }}" class="btn btn-secondary">ย้อนกลับ</a>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count($sale->items) }};

    function addRow() {
        const row = `
            <tr>
                <td>
                    <select name="items[\${itemIndex}][product_id]" class="form-select" required>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="items[\${itemIndex}][product_unit_id]" class="form-select" required>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[\${itemIndex}][quantity]" class="form-control" required>
                </td>
                <td>
                    <input type="number" name="items[\${itemIndex}][price]" class="form-control" step="0.01" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">ลบ</button>
                </td>
            </tr>`;

        document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', row);
        itemIndex++;
    }

    function removeRow(button) {
        button.closest('tr').remove();
    }
</script>
@endsection
