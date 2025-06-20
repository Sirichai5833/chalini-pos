@extends('layouts.layout')

@section('content')
<div class="max-w-5xl mx-auto mt-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">📦 รายงานสินค้าคงคลังใกล้หมด</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-medium text-gray-700 mb-4">รายการสินค้าที่ต้องตรวจสอบ</h3>
        <table class="table-auto w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 border-b">ชื่อสินค้า</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 border-b">คงเหลือในคลัง</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 border-b">คงเหลือหน้าร้าน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-3 text-gray-800">{{ $product->name }}</td>
                        <td class="border px-4 py-3 text-center text-red-600 font-semibold">{{ $product->warehouse_stock }}</td>
                        <td class="border px-4 py-3 text-center text-red-600 font-semibold">{{ $product->store_stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-green-600 font-medium">✅ สินค้าทั้งหมดมีจำนวนเพียงพอ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
