@extends('layouts.layout')

@push('styles')
<style>
[data-product-list]{
    --bg:#080808;--panel:#151515;--panel2:#1e1e1e;--yellow:#ffc107;
    --yellow2:#ffe27a;--text:#f6f6f6;--muted:#9c9c9c;
    --border:rgba(255,255,255,.09);--danger:#ff7180;--success:#59d99d;
    color:var(--text)
}
[data-product-list] .inventory-shell{max-width:1550px;margin:auto}
[data-product-list] .inventory-hero{
    position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;
    gap:1rem;padding:1.4rem 1.5rem;border:1px solid rgba(255,193,7,.32);
    border-radius:24px;background:radial-gradient(circle at 88% 12%,rgba(255,193,7,.21),transparent 25%),
    linear-gradient(135deg,#202020,#090909 72%);box-shadow:0 22px 55px rgba(0,0,0,.34)
}
[data-product-list] .inventory-hero:after{
    content:"";position:absolute;right:-90px;bottom:-140px;width:290px;height:290px;
    border-radius:50%;background:rgba(255,193,7,.06)
}
[data-product-list] .hero-copy,[data-product-list] .hero-actions{position:relative;z-index:2}
[data-product-list] .hero-copy{display:flex;align-items:center;gap:1rem}
[data-product-list] .hero-mark{
    width:62px;height:62px;display:grid;place-items:center;flex:none;border-radius:19px;
    background:linear-gradient(135deg,var(--yellow),var(--yellow2));color:#111;
    font-size:1.55rem;box-shadow:0 12px 28px rgba(255,193,7,.24)
}
[data-product-list] .hero-kicker{color:var(--yellow);font-size:.7rem;font-weight:900;letter-spacing:.14em;text-transform:uppercase}
[data-product-list] .hero-title{margin:.12rem 0 0;color:#fff;font-size:clamp(1.35rem,2.3vw,2rem);font-weight:900}
[data-product-list] .hero-subtitle{margin:.35rem 0 0;color:var(--muted);font-size:.9rem}
[data-product-list] .btn-inv-primary,[data-product-list] .btn-inv-outline{
    min-height:44px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;
    padding:.65rem 1rem;border-radius:12px;font-weight:850;text-decoration:none;transition:.2s
}
[data-product-list] .btn-inv-primary{border:1px solid var(--yellow);background:linear-gradient(135deg,var(--yellow),var(--yellow2));color:#111}
[data-product-list] .btn-inv-primary:hover{color:#000;transform:translateY(-2px);box-shadow:0 9px 23px rgba(255,193,7,.22)}
[data-product-list] .btn-inv-outline{border:1px solid #494949;background:rgba(255,255,255,.025);color:#ddd}
[data-product-list] .btn-inv-outline:hover{border-color:var(--yellow);background:rgba(255,193,7,.07);color:var(--yellow)}
[data-product-list] .metrics-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.85rem;margin:1rem 0}
[data-product-list] .metric-card{
    display:flex;align-items:center;gap:.85rem;padding:1rem;border:1px solid var(--border);
    border-radius:17px;background:linear-gradient(145deg,var(--panel),#101010);
    box-shadow:0 13px 34px rgba(0,0,0,.24)
}
[data-product-list] .metric-icon{
    width:45px;height:45px;display:grid;place-items:center;flex:none;border-radius:14px;
    background:rgba(255,193,7,.1);color:var(--yellow);font-size:1.1rem
}
[data-product-list] .metric-label{color:var(--muted);font-size:.73rem;font-weight:750}
[data-product-list] .metric-value{margin-top:.15rem;color:#fff;font-size:1.32rem;font-weight:900;line-height:1}
[data-product-list] .control-panel{
    padding:1rem;border:1px solid var(--border);border-radius:19px;background:var(--panel);
    box-shadow:0 15px 38px rgba(0,0,0,.28)
}
[data-product-list] .control-heading{display:flex;align-items:center;gap:.5rem;margin-bottom:.85rem;color:var(--yellow);font-size:.85rem;font-weight:850}
[data-product-list] .form-label{color:#e4e4e4;font-size:.8rem;font-weight:800}
[data-product-list] .form-control,[data-product-list] .form-select,[data-product-list] .input-group-text{
    min-height:46px;border-color:#414141!important;background:#0d0d0d!important;color:#fff!important;box-shadow:none!important
}
[data-product-list] .form-control::placeholder{color:#696969}
[data-product-list] .form-control:focus,[data-product-list] .form-select:focus{border-color:var(--yellow)!important;box-shadow:0 0 0 .2rem rgba(255,193,7,.12)!important}
[data-product-list] .form-select option{background:#111;color:#fff}
[data-product-list] textarea.form-control{min-height:110px}
[data-product-list] .result-caption{display:flex;justify-content:space-between;gap:.75rem;margin:1rem 0 .65rem;color:var(--muted);font-size:.8rem}
[data-product-list] .inventory-panel{
    overflow:hidden;border:1px solid var(--border);border-radius:20px;background:var(--panel);
    box-shadow:0 18px 46px rgba(0,0,0,.31)
}
[data-product-list] .inventory-header,[data-product-list] .inventory-row{
    display:grid;grid-template-columns:minmax(310px,1.6fr) minmax(170px,.82fr) minmax(145px,.65fr) minmax(190px,.82fr) 105px;
    align-items:center;gap:1rem
}
[data-product-list] .inventory-header{
    padding:.78rem 1rem;border-bottom:1px solid rgba(255,193,7,.28);
    background:linear-gradient(135deg,#212121,#121212);color:var(--yellow);
    font-size:.72rem;font-weight:850;letter-spacing:.06em;text-transform:uppercase
}
[data-product-list] .inventory-row{padding:1rem;border-bottom:1px solid var(--border);transition:.18s}
[data-product-list] .inventory-row:last-child{border-bottom:0}
[data-product-list] .inventory-row:hover{background:#202020}
[data-product-list] .product-main{min-width:0;display:flex;align-items:center;gap:.9rem}
[data-product-list] .product-thumb{
    position:relative;width:76px;height:76px;overflow:hidden;flex:none;border:1px solid var(--border);
    border-radius:15px;background:#0b0b0b
}
[data-product-list] .product-thumb img{width:100%;height:100%;object-fit:cover}
[data-product-list] .product-thumb-empty{
    width:100%;height:100%;display:grid;place-items:center;
    background:radial-gradient(circle,rgba(255,193,7,.13),transparent 65%),#0b0b0b;
    color:var(--yellow);font-size:1.55rem
}
[data-product-list] .image-count{
    position:absolute;right:.3rem;bottom:.3rem;padding:.18rem .35rem;border-radius:999px;
    background:rgba(0,0,0,.72);color:#fff;font-size:.62rem;font-weight:800
}
[data-product-list] .product-identity{min-width:0}
[data-product-list] .product-title-line{display:flex;align-items:center;gap:.5rem;min-width:0}
[data-product-list] .product-title{overflow:hidden;margin:0;color:#fff;font-size:.98rem;font-weight:900;text-overflow:ellipsis;white-space:nowrap}
[data-product-list] .status-pill{display:inline-flex;align-items:center;gap:.28rem;flex:none;padding:.3rem .52rem;border-radius:999px;font-size:.64rem;font-weight:850}
[data-product-list] .status-on{border:1px solid rgba(89,217,157,.34);background:rgba(89,217,157,.09);color:var(--success)}
[data-product-list] .status-off{border:1px solid #474747;background:rgba(255,255,255,.035);color:#999}
[data-product-list] .product-subline{display:flex;flex-wrap:wrap;gap:.4rem .65rem;margin-top:.35rem;color:var(--muted);font-size:.72rem}
[data-product-list] .product-description{max-width:330px;overflow:hidden;margin-top:.35rem;color:#777;font-size:.71rem;text-overflow:ellipsis;white-space:nowrap}
[data-product-list] .unit-stack{display:grid;gap:.35rem}
[data-product-list] .unit-chip{
    display:flex;justify-content:space-between;gap:.45rem;padding:.42rem .55rem;
    border:1px solid var(--border);border-radius:10px;background:#101010;font-size:.7rem
}
[data-product-list] .unit-chip-name{overflow:hidden;color:#d9d9d9;font-weight:750;text-overflow:ellipsis;white-space:nowrap}
[data-product-list] .unit-chip-qty{color:var(--muted);white-space:nowrap}
[data-product-list] .more-units{color:var(--yellow);font-size:.68rem;font-weight:800}
[data-product-list] .price-main{color:var(--yellow);font-size:1rem;font-weight:900}
[data-product-list] .price-range,[data-product-list] .cost-line{margin-top:.25rem;color:var(--muted);font-size:.68rem}
[data-product-list] .stock-column{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.55rem}
[data-product-list] .stock-tile{padding:.62rem;border:1px solid var(--border);border-radius:12px;background:#101010}
[data-product-list] .stock-label{display:flex;align-items:center;gap:.3rem;color:var(--muted);font-size:.65rem;font-weight:750}
[data-product-list] .stock-number{margin-top:.25rem;color:#fff;font-size:.92rem;font-weight:900}
[data-product-list] .stock-number.low{color:var(--danger)}
[data-product-list] .stock-warning{display:inline-flex;align-items:center;gap:.3rem;margin-top:.45rem;color:var(--danger);font-size:.65rem;font-weight:800}
[data-product-list] .row-actions{display:flex;justify-content:flex-end}
[data-product-list] .edit-action{
    width:42px;height:42px;display:grid;place-items:center;border:1px solid rgba(255,193,7,.32);
    border-radius:12px;background:rgba(255,193,7,.07);color:var(--yellow);text-decoration:none;transition:.18s
}
[data-product-list] .edit-action:hover{background:var(--yellow);color:#111;transform:translateY(-2px)}
[data-product-list] .empty-state,[data-product-list] .client-empty{
    padding:3rem 1rem;border:1px dashed #484848;border-radius:20px;background:var(--panel);color:var(--muted);text-align:center
}
[data-product-list] .empty-icon{
    width:68px;height:68px;display:grid;place-items:center;margin:0 auto 1rem;border-radius:20px;
    background:rgba(255,193,7,.09);color:var(--yellow);font-size:1.65rem
}
[data-product-list] .client-empty{display:none;margin-top:1rem}
[data-product-list] .modal-content{
    overflow:hidden;border:1px solid rgba(255,193,7,.32)!important;border-radius:22px!important;
    background:linear-gradient(160deg,var(--panel),#101010)!important;color:var(--text);
    box-shadow:0 24px 70px rgba(0,0,0,.52)!important
}
[data-product-list] .modal-header{border-bottom:1px solid rgba(255,193,7,.32)!important;background:linear-gradient(135deg,#222,#0e0e0e)!important}
[data-product-list] .modal-title{color:var(--yellow)!important;font-weight:900}
[data-product-list] .modal-footer{border-top:1px solid var(--border)!important;background:#121212!important}
[data-product-list] .btn-close{filter:invert(1)}
[data-product-list] .border-end{border-color:var(--border)!important}
[data-product-list] .unit-group{
    border:1px solid #414141!important;border-radius:16px!important;background:var(--panel2)!important;
    color:var(--text)!important;transition:.2s
}
[data-product-list] .unit-group:hover{border-color:rgba(255,193,7,.5)!important}
[data-product-list] .unit-group.has-duplicate{border-color:rgba(220,53,69,.75)!important}
[data-product-list] .barcode-preview{padding:12px;border-radius:14px;background:#fff}
[data-product-list] .barcode-value{color:#111!important}
[data-product-list] .duplicate-feedback,[data-product-list] .checking-indicator{display:none;margin-top:.4rem;font-size:.77rem;font-weight:750}
[data-product-list] .duplicate-feedback.is-visible,[data-product-list] .checking-indicator.is-visible{display:block}
[data-product-list] .duplicate-feedback.is-error{color:#ff7f8a}
[data-product-list] .duplicate-feedback.is-success{color:#67dc9d}
[data-product-list] .checking-indicator{color:var(--muted)}
[data-product-list] .validation-summary{
    display:none;margin-bottom:1rem;padding:.85rem 1rem;border:1px solid rgba(220,53,69,.35);
    border-radius:13px;background:rgba(220,53,69,.09);color:#ff9aa3;font-size:.82rem
}
[data-product-list] .validation-summary.is-visible{display:block}
[data-product-list] .pagination{
    --bs-pagination-bg:#151515;--bs-pagination-border-color:#353535;--bs-pagination-color:var(--yellow);
    --bs-pagination-hover-bg:var(--yellow);--bs-pagination-hover-color:#111;
    --bs-pagination-active-bg:var(--yellow);--bs-pagination-active-border-color:var(--yellow);--bs-pagination-active-color:#111
}
@media(max-width:1399.98px){
    [data-product-list] .metrics-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
}

@media(max-width:1199.98px){
    [data-product-list] .inventory-header,[data-product-list] .inventory-row{
        grid-template-columns:minmax(280px,1.55fr) minmax(145px,.75fr) minmax(130px,.62fr) minmax(170px,.8fr) 80px;gap:.75rem
    }
}
@media(max-width:991.98px){
    [data-product-list] .metrics-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    [data-product-list] .inventory-header{display:none}
    [data-product-list] .inventory-panel{overflow:visible;border:0;background:transparent;box-shadow:none}
    [data-product-list] .inventory-row{
        grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:.85rem;border:1px solid var(--border);
        border-radius:18px;background:var(--panel);box-shadow:0 14px 34px rgba(0,0,0,.26)
    }
    [data-product-list] .product-main,[data-product-list] .row-actions{grid-column:1/-1}
    [data-product-list] .row-actions{justify-content:stretch}
    [data-product-list] .edit-action{width:100%}
}
@media(max-width:575.98px){
    [data-product-list] .metrics-grid{grid-template-columns:1fr}
}

@media(max-width:767.98px){
    [data-product-list] .inventory-hero{align-items:stretch;flex-direction:column;padding:1rem}
    [data-product-list] .hero-mark{width:52px;height:52px;border-radius:16px}
    [data-product-list] .hero-actions{display:grid!important;grid-template-columns:1fr}
    [data-product-list] .metrics-grid{gap:.65rem}
    [data-product-list] .metric-card{align-items:flex-start;flex-direction:column;gap:.55rem;padding:.85rem}
    [data-product-list] .metric-icon{width:38px;height:38px;border-radius:11px}
    [data-product-list] .inventory-row{grid-template-columns:1fr}
    [data-product-list] .product-main,[data-product-list] .row-actions{grid-column:auto}
    [data-product-list] .product-thumb{width:68px;height:68px}
    [data-product-list] .border-end{border-right:0!important}
    [data-product-list] .modal-footer{gap:.6rem}
    [data-product-list] .modal-footer .btn{width:100%}
}


/* =========================================================
   Modal เพิ่มสินค้า — เว้นพื้นที่ด้านบนสำหรับ Fixed Header
   ปรับค่า --product-modal-header-offset ได้ตามความสูง Header
   ========================================================= */
:root {
    --product-modal-header-offset: 110px;
}

/* ดันพื้นที่แสดง Modal ทั้งชุดลง ไม่ใช้ margin-top ที่ dialog */
#addProductModal.modal {
    padding-top: var(--product-modal-header-offset) !important;
    padding-right: 12px !important;
    padding-bottom: 20px !important;
    padding-left: 12px !important;
    overflow-y: auto !important;
    z-index: 1060 !important;
}

/* ล้าง margin ที่ Bootstrap อาจนำมาคำนวณกับ dialog-scrollable */
#addProductModal .modal-dialog {
    width: auto;
    margin: 0 auto !important;
    min-height: 0 !important;
}

/* จำกัดความสูงตามพื้นที่ที่เหลือใต้ Header */
#addProductModal .modal-dialog.modal-dialog-scrollable {
    height: calc(100vh - var(--product-modal-header-offset) - 20px) !important;
    max-height: calc(100vh - var(--product-modal-header-offset) - 20px) !important;
}

#addProductModal .modal-content {
    max-height: 100% !important;
}

/* ให้หัว Popup ค้างอยู่ด้านบนขณะเลื่อนเนื้อหา */
#addProductModal .modal-header {
    position: sticky;
    top: 0;
    z-index: 5;
    flex-shrink: 0;
}

/* ให้ฉากหลังอยู่ใต้ Modal แต่เหนือ Header ทั่วไป */
#addProductModal.modal,
#addCategoryModal.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1055 !important;
}

@media (max-width: 767.98px) {
    :root {
        --product-modal-header-offset: 85px;
    }

    #addProductModal.modal {
        padding-right: 8px !important;
        padding-bottom: 12px !important;
        padding-left: 8px !important;
    }

    #addProductModal .modal-dialog.modal-dialog-scrollable {
        height: calc(100vh - var(--product-modal-header-offset) - 12px) !important;
        max-height: calc(100vh - var(--product-modal-header-offset) - 12px) !important;
    }
}

</style>
@endpush

@section('flash-messages')
    @include('layouts.partials.flash-messages', [
        'successTitle' => 'Success!',
        'errorTitle' => 'Oops...',
        'confirmButtonText' => 'Ok',
    ])
@endsection

@section('content')

@php
    $productCollection = collect($products->items() ?? $products);

    $totalProductCount = method_exists($products, 'total')
        ? $products->total()
        : $productCollection->count();

    $activeProductCount = $productCollection->where('is_active', true)->count();
    $inactiveProductCount = $productCollection->where('is_active', false)->count();
    $categoryCount = collect($categories)->count();

    /*
    |--------------------------------------------------------------------------
    | มูลค่าต้นทุนสต็อกของสินค้าที่แสดงในหน้าปัจจุบัน
    |--------------------------------------------------------------------------
    | คำนวณจาก (สต็อกคลัง + สต็อกหน้าร้าน) × ราคาทุนของแต่ละหน่วยนับ
    */
    $totalInventoryCost = $productCollection->sum(function ($product) {
        return collect($product->productUnits ?? [])->sum(function ($unit) {
            $warehouseStock = (int) ($unit->stock?->warehouse_stock ?? 0);
            $storeStock = (int) ($unit->stock?->store_stock ?? 0);
            $costPrice = (float) ($unit->cost_price ?? 0);

            return ($warehouseStock + $storeStock) * $costPrice;
        });
    });
@endphp

<div
    class="container-fluid py-4"
    data-product-list
    data-barcode-check-url="{{ route('barcode.check') }}"
    data-product-name-check-url="{{ route('product.name.check') }}"
    data-show-category-modal="{{ $errors->any() ? 'true' : 'false' }}"
>
    <div class="inventory-shell">
        <section class="inventory-hero">
            <div class="hero-copy">
                <span class="hero-mark">
                    <i class="bi bi-box-seam-fill"></i>
                </span>

                <div>
                    <div class="hero-kicker">Inventory Control</div>
                    <h1 class="hero-title">จัดการสินค้า</h1>
                    <p class="hero-subtitle">
                        ตรวจสอบสินค้า ราคา หน่วยนับ และสต็อกคลังกับหน้าร้านได้อย่างรวดเร็ว
                    </p>
                </div>
            </div>

            <div class="hero-actions d-flex flex-wrap gap-2">
                <button
                    type="button"
                    class="btn btn-inv-outline"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal"
                >
                    <i class="bi bi-tags-fill"></i>
                    เพิ่มประเภท
                </button>

                <button
                    type="button"
                    class="btn btn-inv-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addProductModal"
                >
                    <i class="bi bi-plus-circle-fill"></i>
                    เพิ่มสินค้าใหม่
                </button>
            </div>
        </section>

        <section class="metrics-grid">
            <article class="metric-card">
                <span class="metric-icon"><i class="bi bi-boxes"></i></span>
                <div>
                    <div class="metric-label">สินค้าทั้งหมด</div>
                    <div class="metric-value">{{ number_format($totalProductCount) }}</div>
                </div>
            </article>

            <article class="metric-card">
                <span class="metric-icon"><i class="bi bi-check-circle-fill"></i></span>
                <div>
                    <div class="metric-label">สินค้าพร้อมขาย</div>
                    <div class="metric-value">{{ number_format($activeProductCount) }}</div>
                </div>
            </article>

            <article class="metric-card">
                <span class="metric-icon"><i class="bi bi-pause-circle-fill"></i></span>
                <div>
                    <div class="metric-label">ปิดการขาย</div>
                    <div class="metric-value">{{ number_format($inactiveProductCount) }}</div>
                </div>
            </article>

            <article class="metric-card">
                <span class="metric-icon"><i class="bi bi-tags-fill"></i></span>
                <div>
                    <div class="metric-label">ประเภทสินค้า</div>
                    <div class="metric-value">{{ number_format($categoryCount) }}</div>
                </div>
            </article>

            <article class="metric-card">
                <span class="metric-icon"><i class="bi bi-cash-stack"></i></span>
                <div>
                    <div class="metric-label">มูลค่าต้นทุนสต็อก</div>
                    <div class="metric-value">{{ number_format($totalInventoryCost, 2) }} ฿</div>
                </div>
            </article>
        </section>

        <section class="control-panel">
            <div class="control-heading">
                <i class="bi bi-funnel-fill"></i>
                ค้นหาและกรองรายการสินค้า
            </div>

            <form
                method="GET"
                action="{{ url()->current() }}"
                id="filterForm"
                class="row g-3 align-items-end"
            >
                <div class="col-lg-3 col-md-4">
                    <label for="category" class="form-label">ประเภทสินค้า</label>

                    <select
                        name="category_id"
                        id="category"
                        class="form-select rounded-3"
                        onchange="this.form.submit()"
                    >
                        <option value="">แสดงทุกประเภท</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-6 col-md-8">
                    <label for="searchProduct" class="form-label">ค้นหาสินค้า</label>

                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="bi bi-search text-warning"></i>
                        </span>

                        <input
                            type="search"
                            id="searchProduct"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control border-start-0"
                            placeholder="ค้นหาจากชื่อสินค้า หรือรหัสสินค้า"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="d-grid d-sm-flex gap-2">
                        <button type="submit" class="btn btn-inv-primary flex-fill">
                            <i class="bi bi-search"></i>
                            ค้นหา
                        </button>

                        @if (request()->filled('search') || request()->filled('category_id'))
                            <a
                                href="{{ url()->current() }}"
                                class="btn btn-inv-outline"
                                title="ล้างตัวกรอง"
                            >
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </section>

        <div class="result-caption">
            <span>
                <i class="bi bi-list-ul me-1 text-warning"></i>
                แสดง {{ number_format($productCollection->count()) }} รายการในหน้านี้
            </span>

            <span class="d-none d-md-inline">
                กดปุ่มดินสอเพื่อแก้ไขข้อมูลสินค้า
            </span>
        </div>

        @if ($products->count())
            <section class="inventory-panel">
                <div class="inventory-header">
                    <div>สินค้า</div>
                    <div>หน่วยนับ</div>
                    <div>ราคา</div>
                    <div>สต็อก</div>
                    <div class="text-end">จัดการ</div>
                </div>

                @foreach ($products as $product)
                    @php
                        $units = collect($product->productUnits ?? []);

                        $totalWarehousePieces = 0;
                        $totalStorePieces = 0;

                        foreach ($units as $unit) {
                            $unitQuantity = max((int) ($unit->unit_quantity ?? 1), 1);

                            $totalWarehousePieces +=
                                (int) ($unit->stock?->warehouse_stock ?? 0) * $unitQuantity;

                            $totalStorePieces +=
                                (int) ($unit->stock?->store_stock ?? 0) * $unitQuantity;
                        }

                        $retailPrices = $units
                            ->pluck('price')
                            ->filter(fn ($price) => $price !== null)
                            ->map(fn ($price) => (float) $price);

                        $costPrices = $units
                            ->pluck('cost_price')
                            ->filter(fn ($price) => $price !== null)
                            ->map(fn ($price) => (float) $price);

                        $minimumPrice = $retailPrices->min() ?? 0;
                        $maximumPrice = $retailPrices->max() ?? 0;
                        $minimumCost = $costPrices->min() ?? 0;

                        $minimumStock = (int) ($product->min_stock ?? 0);

                        // รวมสต็อกคลังและหน้าร้านเป็นจำนวนชิ้น ก่อนตรวจสอบระดับแจ้งเตือน
                        $totalStockPieces = $totalWarehousePieces + $totalStorePieces;
                        $isLowStock = $totalStockPieces <= $minimumStock;

                        $firstImage = $product->images->first();
                    @endphp

                    <article
                        class="product-item inventory-row"
                        data-name="{{ strtolower($product->name) }}"
                        data-id="{{ strtolower((string) $product->id) }}"
                    >
                        <div class="product-main">
                            <div class="product-thumb">
                                @if ($firstImage)
                                    <img
                                        src="{{ asset('storage/' . $firstImage->image_path) }}"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                    >

                                    @if ($product->images->count() > 1)
                                        <span class="image-count">
                                            <i class="bi bi-images"></i>
                                            {{ $product->images->count() }}
                                        </span>
                                    @endif
                                @else
                                    <div class="product-thumb-empty">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="product-identity">
                                <div class="product-title-line">
                                    <h2 class="product-title" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h2>

                                    <span class="status-pill {{ $product->is_active ? 'status-on' : 'status-off' }}">
                                        <i class="bi {{ $product->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"></i>
                                        {{ $product->is_active ? 'พร้อมขาย' : 'ปิดขาย' }}
                                    </span>
                                </div>

                                <div class="product-subline">
                                    <span><i class="bi bi-hash"></i> {{ $product->id }}</span>

                                    <span>
                                        <i class="bi bi-tag-fill"></i>
                                        {{ $product->category->name ?? 'ไม่มีประเภท' }}
                                    </span>

                                    <span>
                                        <i class="bi bi-bell-fill"></i>
                                        เตือนที่ {{ number_format($minimumStock) }} ชิ้น
                                    </span>
                                </div>

                                <div class="product-description" title="{{ $product->description }}">
                                    {{ $product->description ?: 'ไม่มีรายละเอียดเพิ่มเติม' }}
                                </div>
                            </div>
                        </div>

                        <div class="unit-stack">
                            @forelse ($units->take(3) as $unit)
                                <div class="unit-chip">
                                    <span class="unit-chip-name" title="{{ $unit->unit_name }}">
                                        {{ $unit->unit_name }}
                                    </span>

                                    <span class="unit-chip-qty">
                                        {{ number_format($unit->unit_quantity ?? 1) }} ชิ้น
                                    </span>
                                </div>
                            @empty
                                <span class="text-muted small">ยังไม่มีหน่วยนับ</span>
                            @endforelse

                            @if ($units->count() > 3)
                                <div class="more-units">
                                    + อีก {{ $units->count() - 3 }} หน่วย
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="price-main">
                                {{ number_format($minimumPrice, 2) }} ฿
                            </div>

                            @if ($minimumPrice !== $maximumPrice)
                                <div class="price-range">
                                    สูงสุด {{ number_format($maximumPrice, 2) }} ฿
                                </div>
                            @else
                                <div class="price-range">ราคาขาย</div>
                            @endif

                            <div class="cost-line">
                                ทุนเริ่มต้น {{ number_format($minimumCost, 2) }} ฿
                            </div>
                        </div>

                        <div>
                            <div class="stock-column">
                                <div class="stock-tile">
                                    <div class="stock-label">
                                        <i class="bi bi-box-seam-fill"></i>
                                        คลัง
                                    </div>

                                    <div class="stock-number">
                                        {{ number_format($totalWarehousePieces) }}
                                    </div>
                                </div>

                                <div class="stock-tile">
                                    <div class="stock-label">
                                        <i class="bi bi-shop-window"></i>
                                        หน้าร้าน
                                    </div>

                                    <div class="stock-number">
                                        {{ number_format($totalStorePieces) }}
                                    </div>
                                </div>
                            </div>

                            

                            @if ($isLowStock)
                                <div class="stock-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    สต็อกรวมใกล้หมด
                                </div>
                            @endif
                        </div>

                        <div class="row-actions">
                            <a
                                href="{{ route('product.product.edit', $product->id) }}"
                                class="edit-action"
                                title="แก้ไข {{ $product->name }}"
                                aria-label="แก้ไข {{ $product->name }}"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </section>

            <div id="clientSearchEmpty" class="client-empty">
                <span class="empty-icon"><i class="bi bi-search"></i></span>
                <h4 class="text-white fw-bold">ไม่พบสินค้าที่ค้นหา</h4>
                <p class="mb-0">ลองเปลี่ยนชื่อสินค้า หรือค้นหาด้วยรหัสสินค้า</p>
            </div>

            @if (method_exists($products, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <span class="empty-icon"><i class="bi bi-box-seam"></i></span>
                <h4 class="text-white fw-bold">ยังไม่มีสินค้าในรายการ</h4>
                <p class="mb-3">เพิ่มสินค้าใหม่ หรือเปลี่ยนตัวกรองเพื่อดูสินค้าอื่น</p>

                <button
                    type="button"
                    class="btn btn-inv-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addProductModal"
                >
                    <i class="bi bi-plus-circle-fill"></i>
                    เพิ่มสินค้าใหม่
                </button>
            </div>
        @endif
    </div> {{-- /.inventory-shell --}}

    {{-- Modal for Adding New Product --}}
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addProductModalLabel">
                        <i class="bi bi-box-seam-fill"></i> เพิ่มสินค้าใหม่พร้อมหน่วยนับ
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('product.product.storeWithUnit') }}" method="POST"
                        data-product-create-form
                        enctype="multipart/form-data">
                        @csrf

                        <div id="productValidationSummary" class="validation-summary"></div>

                        <div class="row g-4">
                            {{-- Product Information Section --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="mb-3 text-success"><i class="bi bi-info-circle me-2"></i>ข้อมูลสินค้าหลัก</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">ชื่อสินค้า <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="เช่น โค้ก 2 ลิตร"
                                        autocomplete="off"
                                        data-product-name-input
                                        required>

                                    <div class="checking-indicator" data-product-name-checking>
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        กำลังตรวจสอบชื่อสินค้า
                                    </div>

                                    <div class="duplicate-feedback" data-product-name-feedback></div>

                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-semibold">ประเภทสินค้า <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id"
                                        class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- เลือกประเภทสินค้า --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label>จำนวนแจ้งเตือนขั้นต่ำ</label>
                                    <input type="number" name="min_stock" class="form-control" min="0"
                                        value="{{ old('min_stock', 10) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="description"
                                        class="form-label fw-semibold">ของแถม/รายละเอียดเพิ่มเติม</label>
                                    <textarea name="description" id="description" class="form-control rounded-3" rows="3"
                                        placeholder="เช่น แถมแก้วน้ำ, รายละเอียดเฉพาะของสินค้า">{{ old('description') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">เลือกรูปภาพสินค้า (อัปโหลดได้หลายรูป)</label>
                                    <input type="file" class="form-control" name="images[]" id="images" multiple>
                                    <small class="text-muted">สามารถเลือกได้หลายไฟล์พร้อมกัน (กด Ctrl หรือ Shift)</small>

                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label fw-semibold">สถานะการขาย</label>
                                    <select name="is_active" id="is_active" class="form-select rounded-3">
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>เปิด
                                            (พร้อมขาย)</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>ปิด
                                            (ไม่พร้อมขาย)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Unit Information Section --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="mb-3 text-primary"><i class="bi bi-rulers me-2"></i>ข้อมูลหน่วยนับสินค้า</h6>
                                <div id="units-container">
                                    {{-- Initial Unit Input (Dynamically added/cloned) --}}
                                    <div class="unit-group border rounded-3 p-3 mb-3">
                                        <h6 class="unit-title text-secondary small mb-3">หน่วยนับหลัก <span class="fw-normal">(เช่น
                                                ชิ้น, ขวด)</span></h6>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">ชื่อหน่วยนับ <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="units[0][unit_name]"
                                                class="form-control rounded-3"
                                                placeholder="เช่น ชิ้น, ขวด, แพ็ค"
                                                data-unit-name-input
                                                autocomplete="off"
                                                required>
                                            <div class="duplicate-feedback" data-unit-name-feedback></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">
                                                จำนวนหน่วย
                                            </label>

                                            <input type="number" name="units[0][unit_quantity]"
                                                class="form-control rounded-3" value="1" readonly>

                                            <small class="text-success">
                                                หน่วยแรก
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">รหัสบาร์โค้ด</label>
                                            <div class="input-group">
                                                <input type="text" name="units[0][unit_barcode]"
                                                    class="form-control rounded-start-3"
                                                    placeholder="ระบุเองหรือกดสร้าง"
                                                    data-barcode-input
                                                    inputmode="numeric"
                                                    maxlength="13"
                                                    autocomplete="off"
                                                    required>
                                                <button type="button"
                                                    class="btn btn-inv-outline generate-barcode-btn rounded-end-3"
                                                    title="สร้างบาร์โค้ดสุ่ม">
                                                    <i class="bi bi-arrow-clockwise me-1"></i> สร้าง
                                                </button>
                                            </div>

                                            <div class="checking-indicator" data-barcode-checking>
                                                <span class="spinner-border spinner-border-sm me-1"></span>
                                                กำลังตรวจสอบบาร์โค้ด
                                            </div>

                                            <div class="duplicate-feedback" data-barcode-feedback></div>

                                            <div class="barcode-preview mt-2 text-center" style="display:none;">
                                                <canvas class="barcode-canvas"></canvas>
                                                <p class="barcode-value mt-1 fw-bold text-dark"></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาขายปลีก <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][price]" step="0.01" min="0"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาขายส่ง <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][wholesale]" step="0.01" min="0"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted">ราคาทุน <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="units[0][cost_price]" step="0.01" min="0"
                                                    class="form-control rounded-3" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-unit d-none rounded-pill">
                                            <i class="bi bi-trash me-1"></i> ลบหน่วยนับนี้
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-inv-outline px-3"
                                    id="add-unit">
                                    <i class="bi bi-plus me-1"></i> เพิ่มหน่วยนับอื่น
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer rounded-bottom-4 px-4 py-3 d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-inv-primary px-5">
                                <i class="bi bi-save2-fill me-2"></i> บันทึกสินค้า
                            </button>
                            <button type="button" class="btn btn-inv-outline px-4"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i> ยกเลิก
                            </button>
                        </div>
                    </form>
                </div>
            </div> {{-- /.modal-content --}}
        </div> {{-- /.modal-dialog --}}
    </div> {{-- /#addProductModal --}}

    {{-- Modal for Adding Category --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header rounded-top-4 px-4 py-3">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addCategoryModalLabel">
                        <i class="bi bi-tags-fill"></i> เพิ่มประเภทสินค้า
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="mb-3">
                            <label for="category_name" class="form-label fw-semibold">ชื่อประเภทสินค้า <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="category_name"
                                class="form-control rounded-3 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required placeholder="เช่น ขนม, เครื่องดื่ม">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer rounded-bottom-4 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-inv-outline px-4"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-inv-primary px-4">
                            <i class="bi bi-save2-fill me-1"></i> บันทึกประเภทสินค้า
                        </button>
                    </div>
                </form>
            </div> {{-- /.modal-content --}}
        </div> {{-- /.modal-dialog --}}
    </div> {{-- /#addCategoryModal --}}

</div> {{-- /[data-product-list] --}}

@endsection

@push('scripts')
@include('layouts.partials.jsbarcode')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.querySelector('[data-product-list]');
    const container = document.getElementById('units-container');
    const addButton = document.getElementById('add-unit');
    const form = document.querySelector('[data-product-create-form]');
    const searchInput = document.getElementById('searchProduct');
    const productNameInput = form?.querySelector('[data-product-name-input]');
    const validationSummary = document.getElementById('productValidationSummary');

    if (!root || !container || !addButton || !form) {
        return;
    }

    const productNameEndpoint = root.dataset.productNameCheckUrl;
    const barcodeEndpoint = root.dataset.barcodeCheckUrl;
    let nextIndex = container.querySelectorAll('.unit-group').length;
    let nameCheckTimer = null;
    const barcodeCheckTimers = new WeakMap();

    const normalizeText = value =>
        String(value ?? '')
            .trim()
            .replace(/\s+/g, ' ')
            .toLocaleLowerCase('th-TH');

    const setFeedback = (element, message = '', type = '') => {
        if (!element) return;

        element.textContent = message;
        element.classList.remove('is-visible', 'is-error', 'is-success');

        if (message) {
            element.classList.add('is-visible');
            element.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    };

    const setChecking = (element, visible) => {
        element?.classList.toggle('is-visible', visible);
    };

    const showValidationSummary = messages => {
        if (!validationSummary) return;

        if (!messages.length) {
            validationSummary.innerHTML = '';
            validationSummary.classList.remove('is-visible');
            return;
        }

        validationSummary.innerHTML = `
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                กรุณาตรวจสอบข้อมูล
            </div>
            <ul class="mb-0 ps-3">
                ${messages.map(message => `<li>${message}</li>`).join('')}
            </ul>
        `;

        validationSummary.classList.add('is-visible');
    };

    const requestDuplicateCheck = async (endpoint, parameter, value) => {
        if (!endpoint) {
            return false;
        }

        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set(parameter, value);

        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('ตรวจสอบข้อมูลไม่สำเร็จ');
        }

        const data = await response.json();

        return Boolean(
            data.exists ??
            data.is_exists ??
            data.duplicate ??
            data.taken ??
            false
        );
    };

    const checkProductName = async () => {
        if (!productNameInput) return true;

        const value = productNameInput.value.trim();
        const feedback = form.querySelector('[data-product-name-feedback]');
        const checking = form.querySelector('[data-product-name-checking]');

        productNameInput.classList.remove('is-invalid');
        setFeedback(feedback);

        if (!value) {
            return false;
        }

        setChecking(checking, true);

        try {
            const exists = await requestDuplicateCheck(
                productNameEndpoint,
                'name',
                value
            );

            if (exists) {
                productNameInput.classList.add('is-invalid');
                setFeedback(
                    feedback,
                    'มีชื่อสินค้านี้อยู่ในระบบแล้ว กรุณาใช้ชื่อที่แตกต่าง',
                    'error'
                );
                return false;
            }

            setFeedback(feedback, 'สามารถใช้ชื่อสินค้านี้ได้', 'success');
            return true;
        } catch (error) {
            console.error(error);
            setFeedback(
                feedback,
                'ไม่สามารถตรวจสอบชื่อสินค้าขณะนี้ได้',
                'error'
            );
            return false;
        } finally {
            setChecking(checking, false);
        }
    };

    productNameInput?.addEventListener('input', function () {
        clearTimeout(nameCheckTimer);

        const feedback = form.querySelector('[data-product-name-feedback]');
        productNameInput.classList.remove('is-invalid');
        setFeedback(feedback);

        if (!productNameInput.value.trim()) {
            return;
        }

        nameCheckTimer = setTimeout(checkProductName, 450);
    });

    const generateEAN13 = () => {
        let value = '';

        for (let index = 0; index < 12; index++) {
            value += Math.floor(Math.random() * 10);
        }

        let sum = 0;

        for (let index = 0; index < 12; index++) {
            sum += Number(value[index]) * (index % 2 === 0 ? 1 : 3);
        }

        return value + ((10 - (sum % 10)) % 10);
    };

    const checkBarcode = async input => {
        const group = input.closest('.unit-group');
        const feedback = group?.querySelector('[data-barcode-feedback]');
        const checking = group?.querySelector('[data-barcode-checking]');
        const barcode = input.value.trim();

        input.classList.remove('is-invalid');
        group?.classList.remove('has-duplicate');
        setFeedback(feedback);



        const sameFormInputs = Array.from(
            container.querySelectorAll('[data-barcode-input]')
        ).filter(other => other !== input && other.value.trim() === barcode);

        if (sameFormInputs.length) {
            input.classList.add('is-invalid');
            group?.classList.add('has-duplicate');
            setFeedback(feedback, 'บาร์โค้ดนี้ซ้ำกับหน่วยนับอื่นในฟอร์ม', 'error');
            return false;
        }

        setChecking(checking, true);

        try {
            const exists = await requestDuplicateCheck(
                barcodeEndpoint,
                'barcode',
                barcode
            );

            if (exists) {
                input.classList.add('is-invalid');
                group?.classList.add('has-duplicate');
                setFeedback(
                    feedback,
                    'บาร์โค้ดนี้ถูกใช้กับสินค้าอื่นแล้ว',
                    'error'
                );
                return false;
            }

            setFeedback(feedback, 'สามารถใช้บาร์โค้ดนี้ได้', 'success');
            return true;
        } catch (error) {
            console.error(error);
            setFeedback(
                feedback,
                'ไม่สามารถตรวจสอบบาร์โค้ดขณะนี้ได้',
                'error'
            );
            return false;
        } finally {
            setChecking(checking, false);
        }
    };

    const renderBarcode = (group, barcode) => {
        const preview = group?.querySelector('.barcode-preview');
        const canvas = group?.querySelector('.barcode-canvas');
        const value = group?.querySelector('.barcode-value');

        if (!preview || !canvas || typeof JsBarcode === 'undefined') {
            return;
        }

        try {
            JsBarcode(canvas, barcode, {
                format: 'EAN13',
                width: 2,
                height: 55,
                margin: 8,
                displayValue: true
            });

            if (value) {
                value.textContent = barcode;
            }

            preview.style.display = 'block';
        } catch (error) {
            preview.style.display = 'none';
        }
    };

    const updateIndex = (group, index) => {
        group.querySelectorAll('[name]').forEach(field => {
            field.name = field.name.replace(
                /units\[\d+\]/,
                `units[${index}]`
            );
        });
    };

    const prepareGroup = (group, index) => {
        updateIndex(group, index);

        group.querySelectorAll('input').forEach(input => {
            input.classList.remove('is-invalid');

            if (input.name.endsWith('[unit_quantity]')) {
                input.value = '';
                input.readOnly = false;
                input.min = '1';
                input.placeholder = 'เช่น 6, 12, 24';
            } else {
                input.value = '';
            }
        });

        group.classList.remove('has-duplicate');

        group.querySelectorAll(
            '.duplicate-feedback, .checking-indicator'
        ).forEach(element => {
            element.textContent = '';
            element.classList.remove(
                'is-visible',
                'is-error',
                'is-success'
            );
        });

        const title = group.querySelector('.unit-title');

        if (title) {
            title.innerHTML = `
                หน่วยนับที่ ${index + 1}
                <span class="fw-normal">
                    (ระบุจำนวนชิ้นต่อหน่วย)
                </span>
            `;
        }

        const help = group
            .querySelector('input[name$="[unit_quantity]"]')
            ?.closest('.mb-3')
            ?.querySelector('small');

        if (help) {
            help.className = 'text-warning';
            help.textContent =
                'ตัวอย่าง: 1 แพ็คมี 6 ชิ้น ให้กรอก 6';
        }

        group.querySelector('.remove-unit')?.classList.remove('d-none');

        const preview = group.querySelector('.barcode-preview');

        if (preview) {
            preview.style.display = 'none';
        }
    };

    const reindexGroups = () => {
        const groups = Array.from(
            container.querySelectorAll('.unit-group')
        );

        groups.forEach((group, index) => {
            updateIndex(group, index);

            const quantityInput =
                group.querySelector('input[name$="[unit_quantity]"]');

            const title = group.querySelector('.unit-title');
            const removeButton = group.querySelector('.remove-unit');

            if (index === 0) {
                if (title) {
                    title.innerHTML = `
                        หน่วยนับหลัก
                        <span class="fw-normal">(เช่น ชิ้น, ขวด)</span>
                    `;
                }

                if (quantityInput) {
                    quantityInput.value = '1';
                    quantityInput.readOnly = true;
                }

                removeButton?.classList.add('d-none');
            } else {
                if (quantityInput) {
                    quantityInput.readOnly = false;
                }

                removeButton?.classList.remove('d-none');
            }
        });

        nextIndex = groups.length;
    };

    addButton.addEventListener('click', function () {
        const template = container.querySelector('.unit-group');

        if (!template) return;

        const group = template.cloneNode(true);
        prepareGroup(group, nextIndex++);
        container.appendChild(group);

        group.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        group.querySelector('[data-unit-name-input]')?.focus();
    });

    container.addEventListener('click', async function (event) {
        const generateButton =
            event.target.closest('.generate-barcode-btn');

        if (generateButton) {
            const group = generateButton.closest('.unit-group');
            const input = group?.querySelector('[data-barcode-input]');

            if (!group || !input) return;

            const originalHtml = generateButton.innerHTML;
            generateButton.disabled = true;
            generateButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                กำลังสร้าง
            `;

            try {
                let barcode = '';

                for (let attempt = 0; attempt < 10; attempt++) {
                    const candidate = generateEAN13();

                    const duplicatedInForm = Array.from(
                        container.querySelectorAll('[data-barcode-input]')
                    ).some(other => other !== input &&
                        other.value.trim() === candidate);

                    if (duplicatedInForm) continue;

                    const exists = await requestDuplicateCheck(
                        barcodeEndpoint,
                        'barcode',
                        candidate
                    );

                    if (!exists) {
                        barcode = candidate;
                        break;
                    }
                }

                barcode ||= generateEAN13();
                input.value = barcode;
                renderBarcode(group, barcode);
                await checkBarcode(input);
            } finally {
                generateButton.disabled = false;
                generateButton.innerHTML = originalHtml;
            }

            return;
        }

        const removeButton = event.target.closest('.remove-unit');

        if (removeButton) {
            removeButton.closest('.unit-group')?.remove();
            reindexGroups();
        }
    });

    container.addEventListener('input', function (event) {
        const barcodeInput = event.target.closest('[data-barcode-input]');

        if (barcodeInput) {
            barcodeInput.value = barcodeInput.value
                .replace(/\D/g, '')
                .slice(0, 13);

            const group = barcodeInput.closest('.unit-group');
            const preview = group?.querySelector('.barcode-preview');

            if (/^\d{13}$/.test(barcodeInput.value)) {
                renderBarcode(group, barcodeInput.value);
            } else if (preview) {
                preview.style.display = 'none';
            }

            const previousTimer = barcodeCheckTimers.get(barcodeInput);
            clearTimeout(previousTimer);

            barcodeCheckTimers.set(
                barcodeInput,
                setTimeout(() => checkBarcode(barcodeInput), 450)
            );

            return;
        }

        const unitNameInput = event.target.closest('[data-unit-name-input]');

        if (unitNameInput) {
            unitNameInput.classList.remove('is-invalid');

            const group = unitNameInput.closest('.unit-group');
            const feedback =
                group?.querySelector('[data-unit-name-feedback]');

            setFeedback(feedback);

            const normalizedValue = normalizeText(unitNameInput.value);

            if (!normalizedValue) return;

            const duplicate = Array.from(
                container.querySelectorAll('[data-unit-name-input]')
            ).some(other =>
                other !== unitNameInput &&
                normalizeText(other.value) === normalizedValue
            );

            if (duplicate) {
                unitNameInput.classList.add('is-invalid');
                group?.classList.add('has-duplicate');
                setFeedback(
                    feedback,
                    'ชื่อหน่วยนับซ้ำกันภายในสินค้านี้',
                    'error'
                );
            } else {
                group?.classList.remove('has-duplicate');
                setFeedback(feedback, 'ชื่อหน่วยนับนี้ใช้ได้', 'success');
            }
        }
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        showValidationSummary([]);

        const errors = [];
        const groups = Array.from(
            container.querySelectorAll('.unit-group')
        );

        const normalizedUnitNames = new Set();
        const barcodes = new Set();

        const productNameValid = await checkProductName();

        if (!productNameValid) {
            errors.push('ชื่อสินค้าซ้ำหรือไม่สามารถตรวจสอบได้');
        }

        for (const [index, group] of groups.entries()) {
            const unitNumber = index + 1;
            const unitNameInput =
                group.querySelector('[data-unit-name-input]');

            const quantityInput =
                group.querySelector('input[name$="[unit_quantity]"]');

            const barcodeInput =
                group.querySelector('[data-barcode-input]');

            const priceInput =
                group.querySelector('input[name$="[price]"]');

            const wholesaleInput =
                group.querySelector('input[name$="[wholesale]"]');

            const costInput =
                group.querySelector('input[name$="[cost_price]"]');

            const requiredInputs = [
                unitNameInput,
                quantityInput,
                barcodeInput,
                priceInput,
                wholesaleInput,
                costInput
            ];

            requiredInputs.forEach(input => {
                input?.classList.remove('is-invalid');
            });

            if (requiredInputs.some(input => !input?.value.trim())) {
                requiredInputs
                    .filter(input => !input?.value.trim())
                    .forEach(input => input?.classList.add('is-invalid'));

                errors.push(`ข้อมูลหน่วยนับที่ ${unitNumber} ไม่ครบ`);
            }

            const normalizedUnitName =
                normalizeText(unitNameInput?.value);

            if (
                normalizedUnitName &&
                normalizedUnitNames.has(normalizedUnitName)
            ) {
                unitNameInput?.classList.add('is-invalid');
                errors.push(`ชื่อหน่วยนับที่ ${unitNumber} ซ้ำกัน`);
            } else if (normalizedUnitName) {
                normalizedUnitNames.add(normalizedUnitName);
            }

            const barcode = barcodeInput?.value.trim() ?? '';

            if (!/^\d{13}$/.test(barcode)) {
                barcodeInput?.classList.add('is-invalid');
                errors.push(`บาร์โค้ดหน่วยนับที่ ${unitNumber} ต้องมี 13 หลัก`);
            } else if (barcodes.has(barcode)) {
                barcodeInput?.classList.add('is-invalid');
                errors.push(`บาร์โค้ดหน่วยนับที่ ${unitNumber} ซ้ำในฟอร์ม`);
            } else {
                barcodes.add(barcode);

                const barcodeValid = await checkBarcode(barcodeInput);

                if (!barcodeValid) {
                    errors.push(`บาร์โค้ดหน่วยนับที่ ${unitNumber} ถูกใช้แล้ว`);
                }
            }

            const quantity = Number(quantityInput?.value ?? 0);
            const retail = Number(priceInput?.value ?? -1);
            const wholesale = Number(wholesaleInput?.value ?? -1);
            const cost = Number(costInput?.value ?? -1);

            if (quantity < 1) {
                quantityInput?.classList.add('is-invalid');
                errors.push(`จำนวนหน่วยที่ ${unitNumber} ต้องมากกว่า 0`);
            }

            if (retail < 0 || wholesale < 0 || cost < 0) {
                [priceInput, wholesaleInput, costInput].forEach(input => {
                    if (Number(input?.value ?? -1) < 0) {
                        input?.classList.add('is-invalid');
                    }
                });

                errors.push(`ราคาของหน่วยนับที่ ${unitNumber} ห้ามติดลบ`);
            }
        }

        if (errors.length) {
            const uniqueErrors = [...new Set(errors)];
            showValidationSummary(uniqueErrors);

            form.querySelector('.is-invalid')?.focus();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'ยังบันทึกสินค้าไม่ได้',
                    text: 'พบข้อมูลซ้ำหรือข้อมูลไม่ถูกต้อง กรุณาตรวจสอบช่องที่แจ้งเตือน',
                    background: '#171717',
                    color: '#fff',
                    confirmButtonColor: '#ffc107',
                    customClass: {
                        confirmButton: 'text-dark fw-bold'
                    }
                });
            }

            return;
        }

        const submitButton = form.querySelector('[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                กำลังบันทึกสินค้า
            `;
        }

        form.submit();
    });

    searchInput?.addEventListener('input', function () {
        const keyword = normalizeText(searchInput.value);
        let visibleCount = 0;

        document.querySelectorAll('.product-item').forEach(item => {
            const productName = normalizeText(item.dataset.name);
            const productId = normalizeText(item.dataset.id);

            const visible =
                !keyword ||
                productName.includes(keyword) ||
                productId.includes(keyword);

            item.style.display = visible ? '' : 'none';

            if (visible) {
                visibleCount++;
            }
        });

        const emptyState = document.getElementById('clientSearchEmpty');

        if (emptyState) {
            emptyState.style.display =
                keyword && visibleCount === 0 ? 'block' : 'none';
        }
    });

    if (root.dataset.showCategoryModal === 'true') {
        const modal = document.getElementById('addProductModal');

        if (modal && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    }
});
</script>
@endpush