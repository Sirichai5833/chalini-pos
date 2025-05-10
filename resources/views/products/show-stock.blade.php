@extends('layouts.layout')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded-xl shadow space-y-4">
    <h2 class="text-2xl font-bold mb-4">รายการสินค้า (พร้อมสต็อก)</h2>

    <form method="GET" class="mb-4">
        <select name="category_id" onchange="this.form.submit()" class="border p-2 rounded">
            <option value="">-- ทุกหมวดหมู่ --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </form>

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">ชื่อสินค้า</th>
                <th class="p-2 border">หมวดหมู่</th>
                <th class="p-2 border">ราคาขาย</th>
                <th class="p-2 border">ในคลัง</th>
                <th class="p-2 border">หน้าร้าน</th>
                <th class="p-2 border">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="text-center">
                <td class="p-2 border">{{ $product->name }}</td>
                <td class="p-2 border">{{ $product->category->name ?? '-' }}</td>
                <td class="p-2 border">{{ number_format($product->defaultUnit->price ?? 0, 2) }}</td>
                <td class="p-2 border">{{ $product->stock->warehouse_stock ?? 0 }}</td>
                <td class="p-2 border">{{ $product->stock->store_stock ?? 0 }}</td>
                <td class="p-2 border">
                    <span class="px-2 py-1 text-sm rounded text-white {{ $product->is_active ? 'bg-green-600' : 'bg-gray-500' }}">
                        {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
