@extends('layouts.layout')

@section('content')
<style>
    .supplier-form-page {
        max-width: 900px;
        color: #f7f7f7;
    }

    .supplier-form-card {
        padding: 1.4rem;
        border: 1px solid rgba(255, 212, 59, .22);
        border-radius: 22px;
        background: linear-gradient(180deg, #191919, #101010);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .32);
    }

    .supplier-form-page .form-control {
        border-color: #3a3a3a;
        background: #101010;
        color: #fff;
    }

    .supplier-form-page .form-control:focus {
        border-color: #ffd43b;
        background: #111;
        color: #fff;
        box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
    }
</style>

<div class="container py-4 supplier-form-page">
    <div class="supplier-form-card">
        <h2 class="text-warning fw-bold mb-4">
            <i class="bi bi-pencil-square me-2"></i>
            แก้ไขผู้จำหน่าย
        </h2>

        <form
            method="POST"
            action="{{ route('suppliers.update', $supplier) }}"
        >
            @csrf
            @method('PUT')
            @include('suppliers._form')
        </form>
    </div>
</div>
@endsection
