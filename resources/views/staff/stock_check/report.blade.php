@extends('layouts.layout')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h2 class="h5 fw-bold mb-4">รายงานตรวจนับสต็อก</h2>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>รอบ</th>
                            <th>วันที่</th>
                            <th>ผู้ตรวจ</th>
                            <th class="text-center">ดู</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checks as $check)
                        <tr>
                            <td>{{ $check->cycle }}</td>
                            <td>{{ $check->check_date }}</td>
                            <td>{{ $check->user->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('staff.stock.check.detail',$check) }}"
                                   class="btn btn-sm btn-link">
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('staff.stock.check.index') }}"
           class="btn btn-outline-secondary btn-sm">
            ← กลับ
        </a>
    </div>
</div>
@endsection
