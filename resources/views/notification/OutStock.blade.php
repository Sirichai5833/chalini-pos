@extends('layouts.layout')

@section('content')
<style>
    .low-stock-page{--y:#ffd43b;--bg:#090909;--surface:#141414;--border:rgba(255,212,59,.22);--text:#f7f7f7;--muted:#aaa;--danger:#ef4444;--success:#22c55e;color:var(--text)}
    .low-stock-page .page-shell{max-width:1100px;margin:0 auto}
    .low-stock-page .hero{position:relative;overflow:hidden;margin-bottom:1.35rem;padding:1.45rem;border:1px solid var(--border);border-radius:24px;background:radial-gradient(circle at 88% 18%,rgba(255,212,59,.18),transparent 24%),linear-gradient(135deg,#080808 0%,#171717 60%,#2a2200 100%);box-shadow:0 18px 45px rgba(0,0,0,.34)}
    .low-stock-page .hero::after{content:"";position:absolute;right:-70px;bottom:-110px;width:240px;height:240px;border-radius:50%;background:rgba(255,212,59,.08)}
    .low-stock-page .hero-content{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:1rem}
    .low-stock-page .hero-icon{width:70px;height:70px;display:grid;place-items:center;flex:none;border:1px solid var(--border);border-radius:22px;background:rgba(255,212,59,.1);color:var(--y);font-size:1.8rem}
    .low-stock-page .hero-title{margin:0;color:var(--y);font-weight:950}
    .low-stock-page .hero-subtitle{margin:.4rem 0 0;color:#d0d0d0;font-size:.9rem}
    .low-stock-page .summary-card{border:1px solid var(--border);border-radius:22px;background:linear-gradient(180deg,#191919,#111);box-shadow:0 16px 38px rgba(0,0,0,.32)}
    .low-stock-page .summary-card-header{padding:1rem 1.2rem;border-bottom:1px solid var(--border);background:rgba(255,212,59,.05)}
    .low-stock-page .section-title{margin:0;color:#fff;font-size:1rem;font-weight:900}
    .low-stock-page .section-description{margin:.35rem 0 0;color:var(--muted);font-size:.8rem}
    .low-stock-page .summary-card-body{padding:1.25rem}
    .low-stock-page .stock-item{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:.9rem;padding:1rem;border:1px solid rgba(239,68,68,.34);border-radius:17px;background:linear-gradient(135deg,rgba(239,68,68,.11),rgba(239,68,68,.04)),#141414;transition:.2s ease}
    .low-stock-page .stock-item:last-child{margin-bottom:0}
    .low-stock-page .stock-item::before{content:"";position:absolute;top:0;left:0;width:5px;height:100%;background:var(--danger)}
    .low-stock-page .stock-item:hover{transform:translateY(-2px);border-color:rgba(239,68,68,.6);box-shadow:0 14px 28px rgba(0,0,0,.3)}
    .low-stock-page .product-wrap{min-width:0;display:flex;align-items:center;gap:.9rem}
    .low-stock-page .product-icon{width:48px;height:48px;display:grid;place-items:center;flex:none;border-radius:14px;background:rgba(239,68,68,.14);color:#ff8a8a;font-size:1.15rem}
    .low-stock-page .product-name{margin-bottom:.2rem;color:#fff;font-size:1rem;font-weight:900;word-break:break-word}
    .low-stock-page .product-meta{color:var(--muted);font-size:.78rem}
    .low-stock-page .stock-value{flex:none;text-align:right}
    .low-stock-page .stock-number{display:block;color:#ff7b7b;font-size:1.45rem;font-weight:950;line-height:1}
    .low-stock-page .stock-caption{margin-top:.3rem;color:var(--muted);font-size:.72rem}
    .low-stock-page .empty-state{padding:3.2rem 1rem;border:1px dashed rgba(34,197,94,.35);border-radius:20px;background:rgba(34,197,94,.06);text-align:center}
    .low-stock-page .empty-icon{width:78px;height:78px;display:grid;place-items:center;margin:0 auto 1rem;border-radius:24px;background:rgba(34,197,94,.12);color:#7ee2aa;font-size:2rem}
    .low-stock-page .empty-title{margin-bottom:.35rem;color:#fff;font-weight:900}
    .low-stock-page .empty-text{margin:0;color:var(--muted)}
    @media(max-width:575.98px){.low-stock-page .hero{padding:1.15rem;border-radius:19px}.low-stock-page .hero-content{align-items:flex-start}.low-stock-page .hero-icon{width:56px;height:56px;border-radius:17px;font-size:1.4rem}.low-stock-page .stock-item{align-items:flex-start;flex-direction:column}.low-stock-page .stock-value{width:100%;padding-top:.8rem;border-top:1px solid rgba(255,255,255,.08);text-align:left}}
</style>

<div class="low-stock-page py-4">
    <div class="container-fluid">
        <div class="page-shell">
            <section class="hero">
                <div class="hero-content">
                    <div>
                        <h2 class="hero-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>แจ้งเตือนสินค้าใกล้หมด</h2>
                        <p class="hero-subtitle">ตรวจสอบสินค้าที่มีจำนวนคงเหลือต่ำกว่าหรือเท่ากับจุดแจ้งเตือน</p>
                    </div>
                    <div class="hero-icon"><i class="bi bi-box-seam-fill"></i></div>
                </div>
            </section>

            <section class="summary-card">
                <div class="summary-card-header">
                    <h3 class="section-title"><i class="bi bi-clipboard-data-fill text-warning me-2"></i>รายการที่ต้องตรวจสอบ</h3>
                    <p class="section-description">สินค้าที่มียอดคงเหลือน้อยกว่าหรือเท่ากับสต็อกขั้นต่ำ</p>
                </div>

                <div class="summary-card-body">
                    @forelse($lowStockProducts as $item)
                        <article class="stock-item">
                            <div class="product-wrap">
                                <div class="product-icon"><i class="bi bi-box2-fill"></i></div>
                                <div>
                                    <div class="product-name">{{ $item->product_name }}</div>
                                    <div class="product-meta">สต็อกขั้นต่ำ: <strong class="text-light">{{ $item->min_stock }} ชิ้น</strong></div>
                                </div>
                            </div>
                            <div class="stock-value">
                                <span class="stock-number">{{ $item->total_stock }} ชิ้น</span>
                                <div class="stock-caption">คงเหลือจริง</div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-check-circle-fill"></i></div>
                            <h4 class="empty-title">สินค้าทั้งหมดมีจำนวนเพียงพอ</h4>
                            <p class="empty-text">ขณะนี้ยังไม่มีสินค้าที่ต่ำกว่าจุดแจ้งเตือน</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection