@extends('layouts.layout')

@section('content')
    <div class="container">
        <h2>ประวัติการเปลี่ยนแปลงสินค้าทั้งหมด</h2>

        <form method="GET" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">ตั้งแต่วันที่</label>
                    <input type="date" id="date_from" name="date_from" class="form-control"
                        value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">ถึงวันที่</label>
                    <input type="date" id="date_to" name="date_to" class="form-control"
                        value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="event" class="form-label">ประเภทการกระทำ</label>
                    <select name="event" id="event" class="form-select">
                        <option value="">ทั้งหมด</option>
                        {{-- <option value="stock_added" {{ request('event') == 'stock_added' ? 'selected' : '' }}>เพิ่มสต็อก</option> --}}
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>เพิ่ม</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>แก้ไข</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>ลบ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">ค้นหา</button>
                    <a href="{{ route('product.products.allHistory') }}" class="btn btn-secondary">รีเซ็ต</a>
                </div>
            </div>
        </form>


        <table class="table">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>วันที่</th>
                    <th>ประเภท</th>
                    <th>โดย</th>
                    <th>รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                    <tr>
                        <td>{{ $activity->subject?->name ?? '-' }}</td>
                        <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if ($activity->event == 'created')
                                <span class="badge bg-success">เพิ่ม</span>
                            @elseif($activity->event == 'updated')
                                <span class="badge bg-warning text-dark">แก้ไข</span>
                            @elseif($activity->event == 'deleted')
                                <span class="badge bg-danger">ลบ</span>
                            @elseif($activity->event == 'stock_added')
                                <span class="badge bg-info text-dark">เพิ่มสต็อก</span>                           
                            @else
                                {{ $activity->event }}
                            @endif
                        </td>
                        <td>{{ $activity->causer?->name ?? 'System' }}</td>
                        <td>
    <ul class="list-unstyled mb-0">
        @foreach ($activity->properties['attributes'] ?? [] as $key => $value)
            @php
                $old = $activity->properties['old'][$key] ?? null;
                $isChanged = $old !== $value;
            @endphp
            <li>
    <strong>{{ ucfirst($key) }}</strong>:
    @if ($key === 'image' && $value)
        <br>
        @php
            $oldImg = $activity->properties['old']['image'] ?? null;
        @endphp
        @if ($oldImg && $oldImg !== $value)
            <div class="mb-1">
                <span class="text-muted text-decoration-line-through">เก่า</span><br>
                <img src="{{ asset('storage/' . $oldImg) }}" style="max-width: 100px; max-height: 80px;">
            </div>
        @endif
        <div>
            <span class="text-success">ใหม่</span><br>
            <img src="{{ asset('storage/' . $value) }}" style="max-width: 100px; max-height: 80px;">
        </div>
    @else
        @php $old = $activity->properties['old'][$key] ?? null; @endphp
        @if ($old !== $value)
            <span class="text-muted text-decoration-line-through">{{ $old ?? '-' }}</span>
            <i class="bi bi-arrow-right mx-1"></i>
            <span class="text-success">{{ $value }}</span>
        @else
            <span>{{ $value }}</span>
        @endif
    @endif
</li>

        @endforeach
    </ul>
</td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $activities->links() }}
    </div>
@endsection
