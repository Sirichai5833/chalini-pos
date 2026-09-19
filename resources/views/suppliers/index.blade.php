@extends('layouts.layout')

@section('title', 'ซัพพลายเออร์')
@section('page-title', 'ซัพพลายเออร์')

@section('content')
<style>
    .supplier-page {
        --supplier-yellow: #ffd43b;
        --supplier-yellow-soft: #ffe56b;
        --supplier-black: #090909;
        --supplier-panel: #151515;
        --supplier-card: #1b1b1b;
        --supplier-border: rgba(255, 212, 59, .22);
        --supplier-text: #f7f7f7;
        --supplier-muted: #a8a8a8;
        color: var(--supplier-text);
    }

    .supplier-page .supplier-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding: 1.4rem;
        border: 1px solid var(--supplier-border);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at 90% 15%,
                rgba(255, 212, 59, .18),
                transparent 25%
            ),
            linear-gradient(
                135deg,
                #090909 0%,
                #181818 65%,
                #2a2200 100%
            );
        box-shadow: 0 18px 44px rgba(0, 0, 0, .34);
    }

    .supplier-page .supplier-hero::after {
        content: "";
        position: absolute;
        right: -70px;
        bottom: -110px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255, 212, 59, .08);
    }

    .supplier-page .supplier-hero-content {
        position: relative;
        z-index: 1;
    }

    .supplier-page .supplier-kicker {
        margin-bottom: .35rem;
        color: var(--supplier-yellow);
        font-size: .72rem;
        font-weight: 900;
        letter-spacing: .14em;
    }

    .supplier-page .supplier-title {
        margin: 0;
        color: #fff;
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 950;
    }

    .supplier-page .supplier-subtitle {
        margin: .4rem 0 0;
        color: #ccc;
        font-size: .88rem;
    }

    .supplier-page .btn-add-supplier {
        position: relative;
        z-index: 1;
        min-height: 46px;
        padding: .7rem 1rem;
        border: 1px solid var(--supplier-yellow);
        border-radius: 13px;
        background: linear-gradient(
            135deg,
            var(--supplier-yellow-soft),
            var(--supplier-yellow)
        );
        color: #111;
        font-weight: 900;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(255, 212, 59, .18);
        transition: .2s ease;
    }

    .supplier-page .btn-add-supplier:hover {
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(255, 212, 59, .24);
    }

    .supplier-page .supplier-card {
        overflow: hidden;
        border: 1px solid var(--supplier-border);
        border-radius: 22px;
        background: linear-gradient(180deg, #191919, #101010);
        box-shadow: 0 18px 42px rgba(0, 0, 0, .32);
    }

    .supplier-page .supplier-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid var(--supplier-border);
        background: rgba(255, 212, 59, .05);
    }

    .supplier-page .supplier-card-title {
        margin: 0;
        color: #fff;
        font-size: 1rem;
        font-weight: 900;
    }

    .supplier-page .supplier-count {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .42rem .68rem;
        border: 1px solid rgba(255, 212, 59, .28);
        border-radius: 999px;
        background: rgba(255, 212, 59, .08);
        color: var(--supplier-yellow);
        font-size: .74rem;
        font-weight: 850;
    }

    .supplier-page .supplier-table {
        width: 100%;
        margin: 0;
        color: var(--supplier-text);
        border-collapse: collapse;
    }

    .supplier-page .supplier-table thead th {
        padding: .9rem 1rem;
        border-bottom: 1px solid #323232;
        background: #0d0d0d;
        color: var(--supplier-yellow);
        font-size: .73rem;
        font-weight: 900;
        text-align: left;
        white-space: nowrap;
    }

    .supplier-page .supplier-table tbody td {
        padding: .95rem 1rem;
        border-top: 1px solid #2d2d2d;
        color: #eee;
        vertical-align: middle;
    }

    .supplier-page .supplier-table tbody tr {
        transition: .2s ease;
    }

    .supplier-page .supplier-table tbody tr:hover {
        background: rgba(255, 212, 59, .045);
    }

    .supplier-page .supplier-id {
        color: var(--supplier-muted);
        font-weight: 800;
    }

    .supplier-page .supplier-name {
        color: #fff;
        font-weight: 900;
    }

    .supplier-page .supplier-code {
        margin-top: .18rem;
        color: var(--supplier-muted);
        font-size: .76rem;
    }

    .supplier-page .supplier-products {
        max-width: 420px;
        margin-top: .35rem;
        color: #d8d8d8;
        font-size: .78rem;
        line-height: 1.45;
    }

    .supplier-page .supplier-products i {
        color: var(--supplier-yellow);
    }

    .supplier-page .supplier-status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .4rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
    }

    .supplier-page .supplier-status-active {
        border: 1px solid rgba(34, 197, 94, .35);
        background: rgba(34, 197, 94, .1);
        color: #86efac;
    }

    .supplier-page .supplier-status-inactive {
        border: 1px solid #444;
        background: #202020;
        color: #bbb;
    }

    .supplier-page .supplier-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .45rem;
    }

    .supplier-page .btn-action {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .45rem .7rem;
        border-radius: 10px;
        font-size: .75rem;
        font-weight: 850;
        text-decoration: none;
        transition: .2s ease;
    }

    .supplier-page .btn-edit {
        border: 1px solid rgba(255, 212, 59, .34);
        background: rgba(255, 212, 59, .08);
        color: var(--supplier-yellow);
    }

    .supplier-page .btn-edit:hover {
        background: var(--supplier-yellow);
        color: #111;
    }

    .supplier-page .btn-view {
        border: 1px solid rgba(59, 130, 246, .34);
        background: rgba(59, 130, 246, .08);
        color: #93c5fd;
    }

    .supplier-page .btn-view:hover {
        background: #3b82f6;
        color: #fff;
    }

    .supplier-page .empty-state {
        padding: 3.3rem 1rem;
        text-align: center;
        color: var(--supplier-muted);
    }

    .supplier-page .empty-icon {
        width: 76px;
        height: 76px;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        border: 1px dashed var(--supplier-border);
        border-radius: 24px;
        background: rgba(255, 212, 59, .06);
        color: var(--supplier-yellow);
        font-size: 2rem;
    }

    @media (max-width: 767.98px) {
        .supplier-page .supplier-hero {
            align-items: stretch;
            flex-direction: column;
        }

        .supplier-page .btn-add-supplier {
            width: 100%;
            text-align: center;
        }

        .supplier-page .supplier-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .supplier-page .btn-action {
            justify-content: center;
        }
    }
</style>

<div class="container-fluid py-4 supplier-page">
    <section class="supplier-hero">
        <div class="supplier-hero-content">
            <div class="supplier-kicker">
                SUPPLIER MANAGEMENT
            </div>

            <h2 class="supplier-title">
                <i class="bi bi-truck-front-fill me-2 text-warning"></i>
                ซัพพลายเออร์
            </h2>

            <p class="supplier-subtitle">
                ตรวจสอบและจัดการข้อมูลผู้จำหน่ายทั้งหมดในระบบ
            </p>
        </div>

        <a
            href="{{ route('suppliers.create') }}"
            class="btn-add-supplier"
        >
            <i class="bi bi-plus-circle-fill me-1"></i>
            เพิ่มข้อมูล
        </a>
    </section>

    <section class="supplier-card">
        <div class="supplier-card-header">
            <h3 class="supplier-card-title">
                <i class="bi bi-list-ul me-2 text-warning"></i>
                รายการซัพพลายเออร์
            </h3>

            <span class="supplier-count">
                <i class="bi bi-building"></i>
                {{ number_format(
                    method_exists($suppliers, 'total')
                        ? $suppliers->total()
                        : collect($suppliers)->count()
                ) }}
                รายการ
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="supplier-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อผู้จำหน่าย</th>
                        <th>สินค้าที่จำหน่าย</th>
                        <th>สถานะ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($suppliers as $item)
                        @php
                            $statusValue = strtolower(
                                (string) ($item->status
                                    ?? (
                                        isset($item->is_active)
                                            ? ($item->is_active
                                                ? 'active'
                                                : 'inactive')
                                            : '-'
                                    ))
                            );

                            $isActive = in_array(
                                $statusValue,
                                [
                                    'active',
                                    '1',
                                    'เปิดใช้งาน',
                                    'ใช้งาน',
                                ],
                                true
                            );
                        @endphp

                        <tr>
                            <td>
                                <span class="supplier-id">
                                    #{{ $item->id }}
                                </span>
                            </td>

                            <td>
                                <div class="supplier-name">
                                    {{ $item->name
                                        ?? $item->code
                                        ?? '-' }}
                                </div>

                                @if (!empty($item->code))
                                    <div class="supplier-code">
                                        รหัส: {{ $item->code }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if (!empty($item->supplied_products))
                                    <div class="supplier-products">
                                        <i class="bi bi-box-seam-fill me-1"></i>
                                        {{ \Illuminate\Support\Str::limit(
                                            $item->supplied_products,
                                            120
                                        ) }}
                                    </div>
                                @else
                                    <span class="text-secondary">
                                        ไม่ได้ระบุ
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($statusValue === '-')
                                    <span class="supplier-status supplier-status-inactive">
                                        ไม่ระบุ
                                    </span>
                                @elseif ($isActive)
                                    <span class="supplier-status supplier-status-active">
                                        <i class="bi bi-check-circle-fill"></i>
                                        เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="supplier-status supplier-status-inactive">
                                        <i class="bi bi-dash-circle-fill"></i>
                                        ปิดใช้งาน
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="supplier-actions">
                                    @if (Route::has('suppliers.show'))
                                        <a
                                            href="{{ route(
                                                'suppliers.show',
                                                $item
                                            ) }}"
                                            class="btn-action btn-view"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                            ดู
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route(
                                            'suppliers.edit',
                                            $item
                                        ) }}"
                                        class="btn-action btn-edit"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        แก้ไข
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>

                                    <h4 class="text-white fw-bold">
                                        ยังไม่มีข้อมูลซัพพลายเออร์
                                    </h4>

                                    <p class="mb-0">
                                        เริ่มต้นโดยเพิ่มข้อมูลผู้จำหน่ายรายแรก
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (
            isset($suppliers) &&
            method_exists($suppliers, 'hasPages') &&
            $suppliers->hasPages()
        )
            <div class="p-3 border-top border-secondary">
                {{ $suppliers->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection