@extends('layouts.layout')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-gray-50 rounded-xl shadow-md space-y-6">
    <h2 class="text-3xl font-semibold text-gray-800 flex items-center gap-2">
        📦 รายการสินค้า (พร้อมสต็อก)
    </h2>

    <form method="GET" class="flex items-center space-x-3">
        <label for="category_id" class="text-sm font-medium text-gray-700">หมวดหมู่:</label>
        <select name="category_id" onchange="this.form.submit()" class="border border-gray-300 p-2 rounded-md shadow-sm focus:ring focus:ring-blue-200">
            <option value="">-- ทุกหมวดหมู่ --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="overflow-x-auto rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-700 border border-gray-200">
            <thead class="bg-blue-100 text-blue-800 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 border-b">สินค้า</th>
                    <th class="px-4 py-3 border-b">หมวดหมู่</th>
                    <th class="px-4 py-3 text-center border-b">ในคลัง</th>
                    <th class="px-4 py-3 text-center border-b">หน้าร้าน</th>
                    <th class="px-4 py-3 text-center border-b">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">{{ $product->name }}</td>
                    <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full text-xs font-medium">
                            {{ $product->stock->warehouse_stock ?? 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-medium">
                            {{ $product->stock->store_stock ?? 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if ($product->is_active)
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                ใช้งาน
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-gray-200 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">
                                <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                ไม่ใช้งาน
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
