@extends('layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">📋 ประวัติการเคลื่อนย้ายสินค้า</h2>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('product.movement-history') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาสินค้า / บาร์โค้ด" value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="from" class="form-control" value="{{ $from }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" class="form-control" value="{{ $to }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>วันที่/เวลา</th>
                <th>สินค้า</th>
                <th>ประเภท</th>
                <th>จำนวน</th>
                <th>คลัง/ตำแหน่ง</th>
                <th>หมายเหตุ (เหตุผล)</th>
            </tr>
        </thead>
        <tbody>
    @foreach ($movements as $m)
        <tr>
            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <strong>{{ $m->product->name ?? 'N/A' }}</strong><br>
                <small class="text-muted">{{ $m->product->barcode ?? '' }}</small>
            </td>
            <td>
                @if(str_contains($m->note, '[ยกเลิกการขาย]'))
                    <span class="badge bg-danger">🔄 ยกเลิกการขาย</span>
                @elseif(str_contains($m->note, '[ย้ายคลัง]'))
                    <span class="badge bg-info text-dark">📦 ย้ายคลังสินค้า</span>
                @else
                    <span class="badge bg-secondary">อื่น ๆ</span>
                @endif
            </td>
            <td class="{{ $m->type == 'in' ? 'text-success' : 'text-danger' }}">
                <strong>{{ $m->type == 'in' ? '+' : '-' }} {{ number_format($m->quantity) }}</strong> {{ $m->unit }}
            </td>
            <td>
                <span class="badge outline-secondary text-uppercase" style="border: 1px solid #ccc; color: #666;">
                    {{ $m->location }}
                </span>
            </td>
            <td>{{ str_replace(['[ยกเลิกการขาย]', '[ย้ายคลัง]'], '', $m->note) }}</td>
        </tr>
    @endforeach
</tbody>
    </table>
    {{ $movements->links() }}
</div>
@endsection