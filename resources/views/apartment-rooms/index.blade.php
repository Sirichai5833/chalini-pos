@extends('layouts.app')

@section('title', 'ห้องพัก')
@section('page-title', 'ห้องพัก')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b flex justify-between items-center">
        <h3 class="font-semibold">ห้องพัก</h3>
        <a href="{{ route('apartment-rooms.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">เพิ่มข้อมูล</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr><th class="text-left p-3">ID</th><th class="text-left p-3">ชื่อ/เลขที่</th><th class="text-left p-3">สถานะ</th><th class="text-left p-3">จัดการ</th></tr>
            </thead>
            <tbody>
                @forelse($items ?? [] as $item)
                    <tr class="border-t">
                        <td class="p-3">{{ $item->id }}</td>
                        <td class="p-3">{{ $item->name ?? $item->code ?? '-' }}</td>
                        <td class="p-3">{{ $item->status ?? '-' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('apartment-rooms.show', $item) }}" class="text-blue-600">ดู</a>
                            <a href="{{ route('apartment-rooms.edit', $item) }}" class="text-amber-600 ml-3">แก้ไข</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-gray-500">ยังไม่มีข้อมูล</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
