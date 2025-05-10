@extends('layouts.layout')

@section('content')

    @if (session('success'))
        <x-sweet-alert icon="success" title="Oh Yeah!" text="{{ session('success') }}" confirm-button-text="Ok" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="Oops..." text="{{ session('error') }}" confirm-button-text="Ok" />
    @endif

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Products</h2>

        </div>

        <!-- ฟอร์มเลือกประเภทสินค้า -->
        <form method="GET" action="#" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="category" class="form-label">เลือกประเภทสินค้า</label>
                    <select name="category_id" id="category" class="form-select">
                        <option value="">เลือกทั้งหมด</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">กรอง</button>
                </div>
            </div>
        </form>

        @if ($products->count())
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card shadow-sm border-0 h-100 rounded-4">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top rounded-top-4"
                                    alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex justify-content-center align-items-center rounded-top-4"
                                    style="height: 150px;">
                                    <small class="text-muted">ไม่มีรูปภาพ</small>
                                </div>
                            @endif
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-1">{{ Str::limit($product->name, 20) }}</h6>
                                <div class="mb-2">
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                        {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted"><strong>รหัส:</strong> {{ $product->sku }}</p>
                                {{-- <p class="mb-1 text-muted">
                                    <strong>ราคาขายปลีก:</strong> {{ number_format($product->defaultUnit->price ?? 0, 2) }}
                                    บาท
                                </p>
                                <p class="mb-1 text-muted">
                                    <strong>ราคาขายส่ง:</strong>
                                    {{ number_format($product->defaultUnit->wholesale ?? 0, 2) }} บาท
                                </p> --}}
                                <p class="mb-1 text-muted">
                                    <strong>จำนวนหน้าร้าน:</strong> {{ $product->stock->store_stock ?? 0 }}
                                </p>

                            </div>
                            <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                <a href="{{ route('product.product.edit', $product->id) }}"
                                    class="btn btn-outline-warning btn-sm">แก้ไข</a>
                                <form action="{{ route('product.product.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าจะลบ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">ลบ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info text-center">ไม่มีข้อมูลสินค้า</div>
        @endif
    </div>


@endsection
