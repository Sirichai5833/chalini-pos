@extends('layouts.layout')

@section('content')

@if (session('error'))
    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
@endif


<div class="container mt-4">
    <h2>จัดการประเภทสินค้า</h2>

    <!-- แสดงข้อความแจ้งเตือน -->
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <!-- ฟอร์มเพิ่มประเภทใหม่ -->
    <form action="{{ route('categories.store') }}" method="POST" class="mt-4 mb-5">
        @csrf
        <div class="form-group">
            <label for="name">ชื่อประเภท</label>
            <input type="text" name="name" id="name" class="form-control" required>
            @error('name')
    <div class="text-danger">{{ $message }}</div>
@enderror
        </div>
        <button type="submit" class="btn btn-primary mt-2">เพิ่มประเภท</button>
    </form>

    <!-- ตารางแสดงประเภท -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>ชื่อประเภท</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <!-- ปุ่มแก้ไข -->


                        <!-- ฟอร์มลบ -->
                        <form action="{{ route('categories.delete', $category->id) }}" method="POST"
      class="d-inline-block"
      onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทนี้?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" 
        {{ $category->products()->count() > 0 ? 'disabled' : '' }}>
        ลบ
    </button>
</form>

                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">ไม่มีประเภท</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
