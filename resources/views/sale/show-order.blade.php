@extends('layouts.layout')

@section('content')
    <style>
        :root {
            --myorder-yellow: #ffd43b;
            --myorder-yellow-dark: #caa400;
            --myorder-black: #090909;
            --myorder-surface: #121212;
            --myorder-card: #181818;
            --myorder-soft: #222;
            --myorder-border: rgba(255, 212, 59, .22);
            --myorder-text: #f7f7f7;
            --myorder-muted: #aaa;
            --myorder-success: #22c55e;
            --myorder-danger: #ef4444;
            --myorder-info: #38bdf8;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(255, 212, 59, .08), transparent 28%),
                var(--myorder-black);
            color: var(--myorder-text);
        }

        .my-orders-page {
            max-width: 1180px;
        }

        .my-orders-hero {
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--myorder-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at 88% 18%, rgba(255, 212, 59, .20), transparent 24%),
                linear-gradient(135deg, #080808 0%, #171717 58%, #2b2300 100%);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .35);
        }

        .my-orders-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -120px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 212, 59, .1);
            pointer-events: none;
        }

        .my-orders-hero-content {
            position: relative;
            z-index: 1;
        }

        .my-orders-title {
            margin: 0;
            color: var(--myorder-yellow);
            font-weight: 950;
        }

        .my-orders-subtitle {
            margin: .4rem 0 0;
            color: #d0d0d0;
            font-size: 1.12rem;
        }

        .filter-panel {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding: .85rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--myorder-border);
            border-radius: 18px;
            background: var(--myorder-surface);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .24);
        }

        .filter-btn {
            min-height: 40px;
            padding: .5rem .85rem;
            border: 1px solid #3a3a3a;
            border-radius: 999px;
            background: #171717;
            color: #d6d6d6;
            font-weight: 800;
            transition: .2s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            border-color: var(--myorder-yellow);
            background: linear-gradient(135deg, #ffe66a, var(--myorder-yellow));
            color: #111;
            transform: translateY(-1px);
            box-shadow: 0 7px 18px rgba(255, 212, 59, .16);
        }

        .filter-btn[data-status="late"] {
            border-color: rgba(239, 68, 68, .45);
            color: #ff9f9f;
        }

        .filter-btn[data-status="late"]:hover,
        .filter-btn[data-status="late"].active {
            border-color: var(--myorder-danger);
            background: var(--myorder-danger);
            color: #fff;
        }

        .order-card {
            overflow: hidden;
            border: 1px solid #2d2d2d !important;
            border-radius: 22px !important;
            background: linear-gradient(180deg, #1a1a1a, #111);
            color: var(--myorder-text);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .34) !important;
            transition: .2s ease;
        }

        .order-card:hover {
            border-color: rgba(255, 212, 59, .56) !important;
            transform: translateY(-3px);
            box-shadow: 0 24px 48px rgba(0, 0, 0, .46) !important;
        }

        .order-card.is-late {
            border-color: rgba(239, 68, 68, .58) !important;
        }

        .order-card-header {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--myorder-border);
            background:
                linear-gradient(135deg, rgba(255, 212, 59, .10), rgba(255, 212, 59, .03)),
                #111;
        }

        .order-code {
            color: #fff;
            font-weight: 900;
        }

        .order-date {
            color: var(--myorder-muted);
            font-size: .92rem;
        }

        .late-badge {
            border: 1px solid rgba(239, 68, 68, .38);
            background: rgba(239, 68, 68, .14);
            color: #ff9f9f;
        }

        .late-alert {
            border: 1px solid rgba(239, 68, 68, .32);
            border-radius: 16px;
            background: rgba(239, 68, 68, .10);
            color: #ffb1b1;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }

        .info-box {
            padding: .55rem .65rem;
            border: 1px solid #2d2d2d;
            border-radius: 10px;
            background: #121212;
        }

        .info-label {
            display: block;
            margin-bottom: .1rem;
            color: var(--myorder-muted);
            font-size: .64rem;
        }

        .info-value {
            color: #fff;
            font-size: .98rem;
            font-weight: 750;
            word-break: break-word;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: .4rem .7rem;
            border-radius: 999px;
            background: rgba(56, 189, 248, .13);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, .34);
            font-size: .95rem;
            font-weight: 850;
        }

        .product-list {
            overflow: hidden;
            border: 1px solid #2c2c2c;
            border-radius: 16px;
        }

        .product-list .list-group-item {
            border-color: #292929;
            background: #131313;
            color: var(--myorder-text);
        }

        .product-name {
            color: #fff;
            font-weight: 850;
        }

        .product-meta {
            color: var(--myorder-muted);
            font-size: .76rem;
        }

        .gift-text {
            color: #86efac;
            font-size: .76rem;
        }

        .line-total {
            color: var(--myorder-yellow);
            font-weight: 900;
            white-space: nowrap;
        }

        .grand-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            margin: 1rem 0;
            border: 1px solid var(--myorder-border);
            border-radius: 16px;
            background: rgba(255, 212, 59, .07);
        }

        .order-total-breakdown {
            display: grid;
            gap: .45rem;
            width: 100%;
        }

        .order-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .order-total-row.total-final {
            padding-top: .7rem;
            margin-top: .2rem;
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .grand-total-label {
            color: var(--myorder-muted);
            font-size: .95rem;
        }

        .grand-total-amount {
            color: var(--myorder-yellow);
            font-size: 1.4rem;
            font-weight: 950;
        }

        .status-panel {
            padding: 1rem;
            border: 1px solid #2d2d2d;
            border-radius: 18px;
            background: #111;
        }

        .form-label {
            color: #ddd;
            font-weight: 800;
        }

        .form-select,
        .form-control {
            min-height: 46px;
            border: 1px solid #383838;
            border-radius: 13px;
            background: #151515;
            color: #fff;
            box-shadow: none;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--myorder-yellow);
            background: #171717;
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(255, 212, 59, .12);
        }

        .form-select option {
            background: #171717;
            color: #fff;
        }

        .update-order-btn {
            min-height: 46px;
            border: 1px solid var(--myorder-yellow);
            border-radius: 13px;
            background: linear-gradient(135deg, #ffe66a, var(--myorder-yellow));
            color: #111;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(255, 212, 59, .14);
            transition: .2s ease;
        }

        .update-order-btn:hover:not(:disabled) {
            border-color: #fff08c;
            background: linear-gradient(135deg, #fff08c, #ffc800);
            color: #000;
            transform: translateY(-1px);
        }

        .empty-orders {
            padding: 3.5rem 1.25rem;
            border: 1px dashed var(--myorder-border);
            border-radius: 22px;
            background: rgba(18, 18, 18, .72);
            text-align: center;
        }

        .empty-orders-icon {
            width: 82px;
            height: 82px;
            display: grid;
            place-items: center;
            margin: 0 auto 1rem;
            border-radius: 24px;
            background: rgba(255, 212, 59, .08);
            color: var(--myorder-yellow);
            font-size: 2.2rem;
        }


        /* ===== ลำดับความสำคัญของข้อมูล ===== */
        .priority-customer {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(105px, .65fr);
            gap: .55rem;
            margin-bottom: .65rem;
        }

        .priority-box {
            padding: .55rem .7rem;
            border: 1px solid rgba(255, 212, 59, .22);
            border-radius: 10px;
            background: rgba(255, 212, 59, .035);
        }

        .priority-label {
            display: block;
            margin-bottom: .12rem;
            color: var(--myorder-muted);
            font-size: .62rem;
            font-weight: 800;
        }

        .priority-name {
            color: #fff;
            font-size: 1.12rem;
            font-weight: 900;
            line-height: 1.15;
        }

        .priority-room {
            color: var(--myorder-yellow);
            font-size: 1.15rem;
            font-weight: 950;
            line-height: 1.05;
        }

        .products-section {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 2px solid rgba(255, 212, 59, .38);
            border-radius: 17px;
            background:
                linear-gradient(180deg, rgba(255, 212, 59, .055), rgba(255, 212, 59, .018)),
                #101010;
        }

        .products-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
            margin-bottom: .8rem;
        }

        .products-section-title {
            margin: 0;
            color: var(--myorder-yellow);
            font-size: 1.12rem;
            font-weight: 950;
        }

        .products-total-qty {
            padding: .34rem .65rem;
            border-radius: 999px;
            background: rgba(255, 212, 59, .12);
            color: var(--myorder-yellow);
            font-size: .95rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .product-list .list-group-item {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: center !important;
            gap: .8rem !important;
            padding: .85rem .9rem !important;
        }

        .product-name {
            font-size: .96rem;
            font-weight: 950;
        }

        .product-qty {
            min-width: 58px;
            padding: .38rem .55rem;
            border: 1px solid #3a3a3a;
            border-radius: 9px;
            background: #0d0d0d;
            color: #fff;
            font-size: .8rem;
            font-weight: 950;
            text-align: center;
            white-space: nowrap;
        }

        .line-total {
            min-width: 95px;
            font-size: 1.12rem;
            text-align: right;
        }


        /* ===== เส้นแสดงขั้นตอนออเดอร์ ===== */
        .order-progress {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
            margin: 0 0 1rem;
            padding: .9rem .45rem .75rem;
            border: 1px solid #303030;
            border-radius: 14px;
            background: #101010;
        }

        .order-progress::before {
            content: "";
            position: absolute;
            top: 25px;
            left: 16.666%;
            right: 16.666%;
            height: 4px;
            border-radius: 999px;
            background: #343434;
        }

        .progress-fill {
            position: absolute;
            top: 25px;
            left: 16.666%;
            height: 4px;
            border-radius: 999px;
            background: var(--myorder-yellow);
            transition: width .25s ease;
        }

        .progress-step {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .progress-dot {
            width: 26px;
            height: 26px;
            display: grid;
            place-items: center;
            margin: 0 auto .42rem;
            border: 3px solid #404040;
            border-radius: 50%;
            background: #171717;
            color: #777;
            font-size: .67rem;
            font-weight: 950;
        }

        .progress-step.done .progress-dot,
        .progress-step.current .progress-dot {
            border-color: var(--myorder-yellow);
            background: var(--myorder-yellow);
            color: #111;
        }

        .progress-step.current .progress-dot {
            box-shadow: 0 0 0 5px rgba(255, 212, 59, .12);
        }

        .progress-step-label {
            color: #777;
            font-size: .7rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .progress-step.done .progress-step-label,
        .progress-step.current .progress-step-label {
            color: #fff;
        }

        .progress-step.current .progress-step-label {
            color: var(--myorder-yellow);
            font-weight: 950;
        }

        .progress-cancelled {
            margin-bottom: 1rem;
            padding: .85rem 1rem;
            border: 1px solid rgba(239, 68, 68, .45);
            border-radius: 14px;
            background: rgba(239, 68, 68, .08);
            color: #ff9f9f;
            font-weight: 900;
            text-align: center;
        }

        .direct-action-note {
            margin: -.15rem 0 .8rem;
            color: var(--myorder-muted);
            font-size: .82rem;
        }

        .upload-proof,
        .cancel-reason {
            margin-top: .7rem;
        }

        .hidden-file-input {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        /* ===== เปลี่ยนสถานะด้วยปุ่ม ===== */
        .status-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .75rem;
            margin-bottom: .85rem;
        }

        .status-action-btn {
            min-height: 52px;
            border-radius: 13px;
            font-size: .92rem;
            font-weight: 950;
            transition: .18s ease;
        }

        .status-action-btn.next {
            border: 1px solid var(--myorder-yellow);
            background: var(--myorder-yellow);
            color: #111;
        }

        .status-action-btn.next:hover {
            background: #ffe469;
            border-color: #ffe469;
            color: #000;
        }

        .status-action-btn.cancel {
            min-width: 120px;
            border: 1px solid rgba(239, 68, 68, .58);
            background: rgba(239, 68, 68, .08);
            color: #ff9f9f;
        }

        .status-action-btn.cancel:hover {
            background: var(--myorder-danger);
            border-color: var(--myorder-danger);
            color: #fff;
        }

        .status-action-btn.selected {
            outline: 3px solid rgba(255, 255, 255, .14);
            outline-offset: 2px;
        }

        .current-status-big {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            padding: .6rem .75rem;
            margin-bottom: .65rem;
            border: 1px solid rgba(56, 189, 248, .28);
            border-radius: 10px;
            background: rgba(56, 189, 248, .06);
        }

        .current-status-big .status-text {
            color: #7dd3fc;
            font-size: .98rem;
            font-weight: 900;
        }

        .selected-status-note {
            min-height: 24px;
            margin-bottom: .7rem;
            color: var(--myorder-muted);
            font-size: .76rem;
        }


        /* ===== Boarding-pass / ticket style ===== */
        .tickets-wrap {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            align-items: start;
        }

        .order-ticket {
            position: relative;
            overflow: visible;
            margin-bottom: 0 !important;
            border: 1px solid rgba(255, 212, 59, .30) !important;
            border-radius: 16px !important;
            background: #121212 !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .28) !important;
        }

        .order-ticket::before,
        .order-ticket::after {
            content: "";
            position: absolute;
            top: 116px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--myorder-black);
            z-index: 3;
        }

        .order-ticket::before {
            left: -10px;
        }

        .order-ticket::after {
            right: -10px;
        }

        .ticket-top {
            padding: .9rem 1rem .85rem;
            border-bottom: 1px dashed rgba(255, 212, 59, .35);
            background:
                linear-gradient(135deg, rgba(255, 212, 59, .10), transparent 55%),
                #0f0f0f;
            border-radius: 16px 16px 0 0;
        }

        .ticket-code-row {
            display: flex;
            justify-content: space-between;
            gap: .8rem;
            align-items: flex-start;
        }

        .ticket-code-label {
            color: var(--myorder-muted);
            font-size: .82rem;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .ticket-code {
            margin-top: .15rem;
            color: var(--myorder-yellow);
            font-size: 1.4rem;
            font-weight: 950;
            line-height: 1.1;
            word-break: break-word;
        }

        .ticket-date {
            color: var(--myorder-muted);
            font-size: .92rem;
            text-align: right;
            white-space: nowrap;
        }

        .ticket-passenger {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .7rem;
            align-items: end;
            margin-top: .65rem;
        }

        .ticket-name-label,
        .ticket-room-label {
            color: var(--myorder-muted);
            font-size: .82rem;
            font-weight: 850;
        }

        .ticket-name {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 950;
            line-height: 1.08;
        }

        .ticket-room {
            color: var(--myorder-yellow);
            font-size: 2.2rem;
            font-weight: 950;
            line-height: 1;
            text-align: right;
        }

        .ticket-body {
            padding: .8rem .9rem .9rem;
        }

        .ticket-mini-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .45rem;
            margin-bottom: .65rem;
        }

        .ticket-mini-box {
            padding: .45rem .55rem;
            border: 1px solid #292929;
            border-radius: 8px;
            background: #0e0e0e;
        }

        .ticket-mini-label {
            color: var(--myorder-muted);
            font-size: .78rem;
            font-weight: 800;
        }

        .ticket-mini-value {
            margin-top: .08rem;
            color: #fff;
            font-size: 1.12rem;
            font-weight: 850;
            word-break: break-word;
        }

        .ticket-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .6rem;
            padding: .45rem .55rem;
            margin-bottom: .65rem;
            border: 1px solid rgba(56, 189, 248, .26);
            border-radius: 8px;
            background: rgba(56, 189, 248, .055);
        }

        .ticket-status-label {
            color: var(--myorder-muted);
            font-size: .7rem;
            font-weight: 800;
        }

        .ticket-status-value {
            color: #7dd3fc;
            font-size: 1.12rem;
            font-weight: 950;
        }

        .ticket-products {
            margin-bottom: .7rem;
            border: 1px solid rgba(255, 212, 59, .32);
            border-radius: 10px;
            background: rgba(255, 212, 59, .035);
        }

        .ticket-products-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .5rem .6rem;
            border-bottom: 1px dashed rgba(255, 212, 59, .23);
        }

        .ticket-products-title {
            color: var(--myorder-yellow);
            font-size: 1.18rem;
            font-weight: 950;
        }

        .ticket-products-count {
            color: var(--myorder-yellow);
            font-size: .95rem;
            font-weight: 900;
        }

        .ticket-product-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            gap: .7rem;
            align-items: center;
            padding: .68rem .72rem;
            border-bottom: 1px solid #262626;
        }

        .ticket-product-row:last-child {
            border-bottom: 0;
        }

        .ticket-product-name {
            color: #fff;
            font-size: 1.18rem;
            font-weight: 950;
            line-height: 1.18;
        }

        .ticket-product-meta {
            margin-top: .12rem;
            color: var(--myorder-muted);
            font-size: .98rem;
        }

        .ticket-product-qty {
            min-width: 58px;
            padding: .38rem .5rem;
            border-radius: 8px;
            background: #0b0b0b;
            color: #fff;
            font-size: 1.12rem;
            font-weight: 950;
            text-align: center;
        }

        .ticket-product-total {
            min-width: 92px;
            color: var(--myorder-yellow);
            font-size: 1.08rem;
            font-weight: 950;
            text-align: right;
        }

        .ticket-total {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: .45rem .8rem;
            padding: .7rem .75rem;
            margin-bottom: .65rem;
            border: 1px solid #2b2b2b;
            border-radius: 9px;
            background: #0d0d0d;
            font-size: .98rem;
        }

        .ticket-total .label {
            color: var(--myorder-muted);
        }

        .ticket-total .value {
            color: #fff;
            font-weight: 850;
            text-align: right;
        }

        .ticket-total .final-label {
            padding-top: .35rem;
            border-top: 1px dashed #333;
            color: #fff;
            font-weight: 900;
        }

        .ticket-total .final-value {
            padding-top: .35rem;
            border-top: 1px dashed #333;
            color: var(--myorder-yellow);
            font-size: 1.35rem;
            font-weight: 950;
            text-align: right;
        }

        .order-ticket .order-progress {
            margin-bottom: .65rem;
            padding: .65rem .3rem .55rem;
            border-radius: 9px;
        }

        .order-ticket .order-progress::before,
        .order-ticket .progress-fill {
            top: 20px;
            height: 3px;
        }

        .order-ticket .progress-dot {
            width: 22px;
            height: 22px;
            margin-bottom: .32rem;
            border-width: 2px;
            font-size: .58rem;
        }

        .order-ticket .progress-step-label {
            font-size: .82rem;
            font-weight: 850;
        }

        .order-ticket .status-panel {
            padding: .65rem;
            border-radius: 10px;
        }

        .order-ticket .direct-action-note {
            margin-bottom: .5rem;
            font-size: .95rem;
        }

        .order-ticket .status-actions {
            gap: .5rem;
            margin-bottom: .5rem;
        }

        .order-ticket .status-action-btn {
            min-height: 46px;
            border-radius: 9px;
            font-size: 1.12rem;
        }

        .order-ticket .status-action-btn.cancel {
            min-width: 90px;
        }

        .ticket-late {
            margin-bottom: .65rem;
            padding: .55rem .7rem;
            border: 1px solid rgba(239, 68, 68, .32);
            border-radius: 8px;
            background: rgba(239, 68, 68, .07);
            color: #ffaaaa;
            font-size: .92rem;
            font-weight: 800;
        }

        @media (max-width: 991.98px) {
            .tickets-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .priority-customer {
                grid-template-columns: 1fr;
            }

            .status-actions {
                grid-template-columns: 1fr;
            }

            .status-action-btn.cancel {
                width: 100%;
            }

            .product-list .list-group-item {
                grid-template-columns: minmax(0, 1fr) auto !important;
            }

            .line-total {
                grid-column: 1 / -1;
                min-width: 0;
                text-align: left;
            }
        }

        @media (max-width: 767.98px) {
            .my-orders-hero {
                padding: 1.2rem;
                border-radius: 19px;
            }

            .order-card-header {
                align-items: flex-start !important;
                flex-direction: column;
                gap: .5rem;
            }

            .order-info-grid {
                grid-template-columns: 1fr;
            }

            .grand-total {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* ===== Larger text override ===== */
        .ticket-name {
            font-size: 1.8rem;
        }

        .ticket-room {
            font-size: 2.2rem;
        }

        .ticket-products-title {
            font-size: 1.18rem;
        }

        .ticket-product-name {
            font-size: 1.18rem;
        }

        .ticket-product-qty {
            font-size: 1rem;
            min-width: 64px;
        }

        .ticket-product-total {
            font-size: 1.08rem;
            min-width: 100px;
        }

        .ticket-mini-value {
            font-size: 1rem;
        }

        .ticket-status-value {
            font-size: 1.12rem;
        }

        .ticket-total {
            font-size: .98rem;
        }

        .ticket-total .final-value {
            font-size: 1.35rem;
        }

        .order-ticket .status-action-btn {
            font-size: 1rem;
            min-height: 50px;
        }

        .order-ticket .progress-step-label {
            font-size: .82rem;
        }

    </style>

    <div class="container py-4 my-orders-page">
        <section class="my-orders-hero">
            <div class="my-orders-hero-content">
                <h3 class="my-orders-title">
                    <i class="bi bi-person-check-fill me-2"></i>
                    ออเดอร์ที่ฉันรับไว้ทั้งหมด
                </h3>
                <p class="my-orders-subtitle">
                    ติดตามงานที่รับไว้ อัปเดตสถานะ และจัดการคำสั่งซื้อได้จากหน้านี้
                </p>
            </div>
        </section>

        <div class="filter-panel">

            <button type="button" class="filter-btn active" data-status="all">
                <i class="bi bi-grid-fill me-1"></i>
                ทั้งหมด
            </button>
            <button type="button" class="filter-btn" data-status="กำลังจัดสินค้า">
                <i class="bi bi-box-seam me-1"></i>
                กำลังจัดสินค้า
            </button>
            
             <button type="button" class="filter-btn" data-status="กำลังจัดส่ง">
                <i class="bi bi-truck me-1"></i>
                กำลังจัดส่ง
            </button>
            <button type="button" class="filter-btn" data-status="late">
                <i class="bi bi-alarm-fill me-1"></i>
                ล่าช้าเกิน 15 นาที
            </button>


           

        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-4 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger rounded-4 shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="tickets-wrap">
        @forelse ($orders as $order)
            @php
                $isLate =
                    now()->greaterThan($order->updated_at->copy()->addMinutes(15)) &&
                    !in_array($order->status, ['เสร็จสิ้น', 'ยกเลิก']);

                $shippingFee = max(
                    0,
                    (float) ($order->shipping_fee ?? 0)
                );

                $itemsSubtotal = $order->orderItems->sum(function ($item) {
                    return (float) $item->price
                        * (int) $item->quantity;
                });

                $orderGrandTotal = (float) $order->total_amount;

                if ($orderGrandTotal <= 0) {
                    $orderGrandTotal = $itemsSubtotal + $shippingFee;
                }

                $totalQty = $order->orderItems->sum(
                    fn ($item) => (int) $item->quantity
                );
            @endphp

            <div class="card order-card order-ticket {{ $isLate ? 'is-late' : '' }}"
                data-status="{{ $order->status }}"
                data-late="{{ $isLate ? '1' : '0' }}">

                <div class="ticket-top">
                    <div class="ticket-code-row">
                        <div>
                            <div class="ticket-code-label">ORDER / BOARDING PASS</div>
                            <div class="ticket-code">
                                <i class="bi bi-ticket-perforated-fill me-1"></i>
                                #{{ $order->order_code }}
                            </div>
                        </div>

                        <div class="ticket-date">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="ticket-passenger">
                        <div>
                            <div class="ticket-name-label">ลูกค้า / PASSENGER</div>
                            <div class="ticket-name">
                                {{ $order->user->name ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="ticket-room-label">ห้อง / ROOM</div>
                            <div class="ticket-room">
                                {{ $order->user->room_number ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ticket-body">
                    @if ($isLate)
                        <div class="ticket-late">
                            <i class="bi bi-alarm-fill me-1"></i>
                            เกิน 15 นาที — กรุณาดำเนินการโดยเร็ว
                        </div>
                    @endif

                    <div class="ticket-mini-info">
                        <div class="ticket-mini-box">
                            <div class="ticket-mini-label">เบอร์โทร</div>
                            <div class="ticket-mini-value">
                                {{ $order->tracking_number ?? '-' }}
                            </div>
                        </div>

                        <div class="ticket-mini-box">
                            <div class="ticket-mini-label">ชำระเงิน</div>
                            <div class="ticket-mini-value">
                                {{ $order->payment_method }}
                            </div>
                        </div>
                    </div>



                    <section class="ticket-products">
                        <div class="ticket-products-head">
                            <div class="ticket-products-title">
                                <i class="bi bi-cart-check-fill me-1"></i>
                                รายการสินค้า
                            </div>

                            <div class="ticket-products-count">
                                {{ number_format($totalQty) }} ชิ้น
                            </div>
                        </div>

                        @foreach ($order->orderItems as $item)
                            <div class="ticket-product-row">
                                <div>
                                    <div class="ticket-product-name">
                                        {{ $item->product->name ?? '[ลบแล้ว]' }}
                                    </div>

                                    <div class="ticket-product-meta">
                                        {{ $item->productUnit->unit_name ?? 'หน่วย' }}
                                        · ฿{{ number_format($item->price, 2) }}/หน่วย
                                    </div>

                                    @if (!empty($item->product->description))
                                        <div class="gift-text mt-1">
                                            <i class="bi bi-gift-fill me-1"></i>
                                            ของแถม: {{ $item->product->description }}
                                        </div>
                                    @endif
                                </div>

                                <div class="ticket-product-qty">
                                    × {{ number_format((int) $item->quantity) }}
                                </div>

                                <div class="ticket-product-total">
                                    ฿{{ number_format($item->price * $item->quantity, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <div class="ticket-total">
                        <span class="label">ยอดรวมสินค้า</span>
                        <span class="value">฿{{ number_format($itemsSubtotal, 2) }}</span>

                        <span class="label">ค่าจัดส่ง</span>
                        <span class="value">
                            @if ($shippingFee > 0)
                                ฿{{ number_format($shippingFee, 2) }}
                            @else
                                ฟรี
                            @endif
                        </span>

                        <span class="final-label">รวมทั้งหมด</span>
                        <span class="final-value">
                            ฿{{ number_format($orderGrandTotal, 2) }}
                        </span>
                    </div>

                    @php
                        /*
                        |--------------------------------------------------------------------------
                        | ลำดับสถานะ
                        |--------------------------------------------------------------------------
                        | อนุญาตให้เปลี่ยนไปข้างหน้าได้ครั้งละ 1 ขั้นเท่านั้น
                        */
                        $statusFlow = [
                            'รอเจ้าหน้าที่รับคำสั่งซื้อ' => 'กำลังจัดสินค้า',
                            'กำลังจัดสินค้า' => 'กำลังจัดส่ง',
                            'กำลังจัดส่ง' => 'เสร็จสิ้น',
                        ];

                        $nextStatus = $statusFlow[$order->status] ?? null;
                        $isFinalStatus = in_array($order->status, ['เสร็จสิ้น', 'ยกเลิก'], true);

                        $progressSteps = [
                            'กำลังจัดสินค้า',
                            'กำลังจัดส่ง',
                            'เสร็จสิ้น',
                        ];

                        $progressIndex = match ($order->status) {
                            'รอเจ้าหน้าที่รับคำสั่งซื้อ' => -1,
                            'กำลังจัดสินค้า' => 0,
                            'กำลังจัดส่ง' => 1,
                            'เสร็จสิ้น' => 2,
                            default => -1,
                        };

                        $progressWidth = match ($progressIndex) {
                            0 => '0%',
                            1 => '33.334%',
                            2 => '66.668%',
                            default => '0%',
                        };
                    @endphp

                    @if ($order->status === 'ยกเลิก')
                        <div class="progress-cancelled">
                            <i class="bi bi-x-circle-fill me-1"></i>
                            ออเดอร์นี้ถูกยกเลิกแล้ว
                        </div>
                    @else
                        <div class="order-progress">
                            <div
                                class="progress-fill"
                                style="width: {{ $progressWidth }};"
                            ></div>

                            @foreach ($progressSteps as $index => $step)
                                <div
                                    class="progress-step
                                        {{ $index < $progressIndex ? 'done' : '' }}
                                        {{ $index === $progressIndex ? 'current' : '' }}"
                                >
                                    <div class="progress-dot">
                                        @if ($index < $progressIndex)
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>

                                    <div class="progress-step-label">
                                        {{ $step }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('orders.updateStatus', $order->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="status-form status-panel">
                        @csrf
                        @method('PATCH')

                        @if ($isFinalStatus)
                            <div class="text-muted small">
                                <i class="bi bi-lock-fill me-1"></i>
                                ออเดอร์นี้อยู่ในสถานะสุดท้ายแล้ว
                            </div>
                        @else
                            <input
                                type="hidden"
                                name="status"
                                class="status-input"
                                value=""
                            >

                            <div class="direct-action-note">
                                กดปุ่มเพื่อเปลี่ยนสถานะได้ทันที
                            </div>

                            <div class="status-actions">
                                @if ($nextStatus)
                                    <button
                                        type="button"
                                        class="btn status-action-btn next direct-status-btn"
                                        data-status-value="{{ $nextStatus }}"
                                    >
                                        <i class="bi bi-arrow-right-circle-fill me-1"></i>
                                        {{ $nextStatus }}
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    class="btn status-action-btn cancel direct-status-btn"
                                    data-status-value="ยกเลิก"
                                >
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                    ยกเลิก
                                </button>
                            </div>

                            <div class="upload-proof" style="display: none;">
                                <label class="form-label">
                                    <i class="bi bi-camera-fill me-1"></i>
                                    เลือกรูปหลักฐานเพื่อจบออเดอร์
                                </label>

                                <input
                                    type="file"
                                    name="proof_image"
                                    accept="image/*"
                                    class="form-control proof-input"
                                >
                            </div>

                            <div class="cancel-reason" style="display: none;">
                                <label class="form-label">
                                    <i class="bi bi-chat-left-text-fill me-1"></i>
                                    เหตุผลการยกเลิก
                                </label>

                                <textarea
                                    name="cancel_reason"
                                    rows="3"
                                    class="form-control cancel-textarea"
                                    placeholder="พิมพ์เหตุผล แล้วกดปุ่มยกเลิกอีกครั้ง"
                                ></textarea>

                                <button
                                    type="button"
                                    class="btn btn-danger w-100 mt-2 confirm-cancel-btn"
                                >
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                    ยกเลิกออเดอร์
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-orders">
                <div class="empty-orders-icon">
                    <i class="bi bi-inbox-fill"></i>
                </div>

                <h4 class="text-white fw-bold">ยังไม่มีออเดอร์ที่รับไว้</h4>
                <p class="text-muted mb-0">
                    เมื่อคุณรับออเดอร์ รายการจะแสดงในหน้านี้
                </p>
            </div>
        @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.status-form').forEach(form => {
                const statusInput = form.querySelector('.status-input');
                const proofWrap = form.querySelector('.upload-proof');
                const proofInput = form.querySelector('.proof-input');
                const cancelWrap = form.querySelector('.cancel-reason');
                const cancelTextarea = form.querySelector('.cancel-textarea');
                const cancelConfirm = form.querySelector('.confirm-cancel-btn');

                const submitForm = (status) => {
                    if (!statusInput) return;

                    statusInput.value = status;

                    form.querySelectorAll('button').forEach(button => {
                        button.disabled = true;
                    });

                    form.submit();
                };

                form.querySelectorAll('.direct-status-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const status = this.dataset.statusValue || '';

                        if (status === 'เสร็จสิ้น') {
                            if (cancelWrap) cancelWrap.style.display = 'none';
                            if (proofWrap) proofWrap.style.display = 'block';

                            if (proofInput && proofInput.files.length === 0) {
                                proofInput.click();
                                return;
                            }

                            submitForm(status);
                            return;
                        }

                        if (status === 'ยกเลิก') {
                            if (proofWrap) proofWrap.style.display = 'none';
                            if (cancelWrap) cancelWrap.style.display = 'block';

                            cancelTextarea?.focus();
                            return;
                        }

                        submitForm(status);
                    });
                });

                proofInput?.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        submitForm('เสร็จสิ้น');
                    }
                });

                cancelConfirm?.addEventListener('click', function () {
                    const reason = cancelTextarea?.value.trim() || '';

                    if (reason === '') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'กรุณากรอกเหตุผล',
                                text: 'ระบุเหตุผลก่อนยกเลิกออเดอร์',
                                confirmButtonColor: '#ffd43b'
                            });
                        } else {
                            alert('กรุณากรอกเหตุผลก่อนยกเลิกออเดอร์');
                        }
                        return;
                    }

                    submitForm('ยกเลิก');
                });
            });

            document.querySelectorAll('.filter-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const status = this.dataset.status;

                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');

                    document.querySelectorAll('.order-card').forEach(card => {
                        const cardStatus = card.dataset.status;
                        const isLate = card.dataset.late === '1';

                        if (status === 'all') {
                            card.style.display = '';
                        } else if (status === 'late') {
                            card.style.display = isLate ? '' : 'none';
                        } else {
                            card.style.display =
                                cardStatus === status ? '' : 'none';
                        }
                    });
                });
            });
        });
    </script>
@endsection