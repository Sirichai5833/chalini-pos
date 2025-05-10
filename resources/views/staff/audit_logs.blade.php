@extends('layouts.layout')

@section('content')
<div class="container">
    <h3 class="mb-4">📜 ประวัติการแก้ไขของ</h3>

    {{-- 🧭 ฟอร์มเลือกวันที่ แบบหรูหรา --}}
    <form method="GET" action="{{ route('staff.audits') }}" class="row g-3 align-items-end mb-4 p-3 rounded border shadow-sm bg-light">
        <div class="col-md-4">
            <label for="date" class="form-label fw-bold">📅 เลือกวันที่:</label>
            <input type="date" name="date" id="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">
                🔍 ค้นหา
            </button>
        </div>
    </form>

    @if ($audits->isEmpty())
        <div class="alert alert-warning shadow-sm">ไม่มีประวัติการแก้ไขในวันที่เลือก</div>
    @else
        <div class="table-responsive shadow-sm">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>📌 เหตุการณ์</th>
                        <th>📉 ค่าก่อนหน้า</th>
                        <th>📈 ค่าใหม่</th>
                        <th>👤 โดย</th>
                        <th>🕒 เมื่อ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($audits as $audit)
                        <tr>
                            <td class="text-center">{{ ucfirst($audit->event) }}</td>
                            <td>
                                @foreach ($audit->old_values as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($audit->new_values as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                @endforeach
                            </td>
                            <td class="text-center">{{ $audit->user?->name ?? 'ไม่ทราบ' }}</td>
                            <td class="text-center">{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
            ⬅️ กลับหน้าหลัก
        </a>
    </div>
</div>
@endsection
