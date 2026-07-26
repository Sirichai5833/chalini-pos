@extends('layouts.layout')

@section('validation-errors')
    @include('layouts.partials.validation-errors')
@endsection

@section('content')

<div class="container">

<div class="card shadow">

<div class="card-header bg-danger text-white">

<h4>ปิดกะการขาย</h4>

</div>

<div class="card-body">

<form action="{{ route('shift.update') }}" method="POST">

@csrf

<div class="mb-3">

<label>ผู้ขาย</label>

<input
class="form-control"
value="{{ auth()->user()->name }}"
readonly>

</div>

<div class="mb-3">

<label>กะ</label>

<input
class="form-control"
value="{{ $shift->shiftType->name }}"
readonly>

</div>

<div class="mb-3">

<label>เงินทอนเริ่มต้น</label>

<input
class="form-control"
value="{{ number_format($shift->opening_cash,2) }}"
readonly>

</div>

<div class="mb-3">

<label>ยอดขายเงินสด</label>

<input
class="form-control"
value="{{ number_format($cashSales,2) }}"
readonly>

</div>

<div class="mb-3">

<label>QR</label>

<input
class="form-control"
value="{{ number_format($qrSales,2) }}"
readonly>

</div>

<div class="mb-3">

<label>เครดิต</label>

<input
class="form-control"
value="{{ number_format($creditSales,2) }}"
readonly>

</div>

<div class="mb-3">

<label>เงินสดที่ควรมี</label>

<input
class="form-control"
value="{{ number_format($expectedCash,2) }}"
readonly>

</div>

<div class="mb-3">

<label>เงินสดที่นับจริง</label>

<input
type="number"
step="0.01"
name="closing_cash"
class="form-control"
required>

</div>

<div class="mb-3">

<label>หมายเหตุ</label>

<textarea
name="note"
class="form-control"></textarea>

</div>

<button class="btn btn-danger">

ปิดกะ

</button>

</form>

</div>

</div>

</div>

@endsection
