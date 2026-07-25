@extends('layouts.layout')

@section('content')

<div class="container">


<h3 class="mb-4">
    📜 ประวัติการแก้ไขข้อมูลสมาชิก
</h3>


{{-- ค้นหาตามวันที่ --}}
<form
    method="GET"
    action="{{ route('staff.audits') }}"
    class="row g-3 align-items-end mb-4 p-3 rounded border shadow-sm bg-light">


    <div class="col-md-4">

        <label
            for="date"
            class="form-label fw-bold">

            📅 เลือกวันที่

        </label>


        <input
            type="date"
            name="date"
            id="date"
            value="{{ request('date') }}"
            class="form-control">

    </div>


    <div class="col-md-auto">

        <button
            type="submit"
            class="btn btn-primary">

            🔍 ค้นหา

        </button>

    </div>


    <div class="col-md-auto">

        <a
            href="{{ route('staff.audits') }}"
            class="btn btn-outline-secondary">

            รีเซ็ต

        </a>

    </div>


</form>


@if ($audits->isEmpty())


    <div class="alert alert-warning shadow-sm">

        ไม่พบประวัติการแก้ไขข้อมูลสมาชิก

    </div>


@else


    @php

        $labels = [

            'name' => 'ชื่อสมาชิก',

            'email' => 'อีเมล',

            'room_number' => 'หมายเลขห้อง',

            'role' => 'ประเภทสมาชิก',

        ];

    @endphp


    <div class="table-responsive shadow-sm">

        <table
            class="table table-bordered table-hover align-middle">


            <thead
                class="table-dark text-center">

                <tr>

                    <th width="10%">
                        เหตุการณ์
                    </th>

                    <th width="20%">
                        สมาชิก
                    </th>

                    <th width="35%">
                        รายละเอียดการเปลี่ยนแปลง
                    </th>

                    <th width="15%">
                        ผู้ดำเนินการ
                    </th>

                    <th width="20%">
                        วันที่ / เวลา
                    </th>

                </tr>

            </thead>


            <tbody>


            @foreach ($audits as $audit)


                <tr>


                    {{-- เหตุการณ์ --}}
                    <td class="text-center">


                        @switch($audit->event)


                            @case('created')

                                <span
                                    class="badge bg-success">

                                    เพิ่มสมาชิก

                                </span>

                                @break


                            @case('updated')

                                <span
                                    class="badge bg-warning text-dark">

                                    แก้ไขข้อมูล

                                </span>

                                @break


                            @case('deleted')

                                <span
                                    class="badge bg-danger">

                                    ลบสมาชิก

                                </span>

                                @break


                            @default

                                {{ ucfirst($audit->event) }}


                        @endswitch


                    </td>


                    {{-- ชื่อสมาชิก --}}
                    <td>


                        {{ $audit->new_values['name']
                            ?? $audit->old_values['name']
                            ?? '-' }}


                    </td>


                    {{-- รายละเอียด --}}
                    <td>


                        @if ($audit->event === 'created')


                            <span class="text-success">

                                เพิ่มสมาชิกใหม่เข้าสู่ระบบ

                            </span>


                        @elseif ($audit->event === 'deleted')


                            <span class="text-danger">

                                ลบสมาชิกออกจากระบบ

                            </span>


                        @else


                            @php

                                $hasChanges = false;

                            @endphp


                            @foreach ($labels as $field => $label)


                                @php

                                    $old =
                                        $audit->old_values[$field]
                                        ?? null;

                                    $new =
                                        $audit->new_values[$field]
                                        ?? null;

                                @endphp


                                @if ($old != $new)


                                    @php

                                        $hasChanges = true;

                                    @endphp


                                    <div class="mb-2">


                                        <strong>

                                            {{ $label }}

                                        </strong>


                                        <br>


                                        <span
                                            class="text-danger">

                                            {{ $old ?: '-' }}

                                        </span>


                                        <i
                                            class="bi bi-arrow-right mx-1">

                                        </i>


                                        <span
                                            class="text-success">

                                            {{ $new ?: '-' }}

                                        </span>


                                    </div>


                                @endif


                            @endforeach


                            @if (!$hasChanges)

                                <span class="text-muted">

                                    ไม่มีรายละเอียดการเปลี่ยนแปลง

                                </span>

                            @endif


                        @endif


                    </td>


                    {{-- ผู้ดำเนินการ --}}
                    <td class="text-center">


                        {{ $audit->user?->name ?? 'ไม่ทราบ' }}


                    </td>


                    {{-- วันที่ --}}
                    <td class="text-center">


                        {{ $audit->created_at->format('d/m/Y H:i') }}


                    </td>


                </tr>


            @endforeach


            </tbody>


        </table>

    </div>


@endif


<div class="mt-4">


    <a
        href="{{ route('staff.index') }}"
        class="btn btn-outline-secondary">

        ⬅️ กลับ

    </a>

</div>


</div>

@endsection
