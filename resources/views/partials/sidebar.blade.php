<nav class="space-y-4 text-sm font-medium text-slate-300">

    {{-- เมนูหลัก (Dashboard) --}}
    <div class="space-y-1">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                  {{ request()->routeIs('dashboard')
                      ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                      : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
        </a>
    </div>

    {{-- Admin: สินค้าและคลัง --}}
    @if(auth()->user()?->role === 'admin')
        @php
            $isActive = request()->routeIs(['products.*', 'stocks.*', 'stock-movements.*', 'stock-counts.*', 'expiring-products.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>สินค้าและคลัง</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('products.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📦</span>
                    <span>สินค้า</span>
                </a>

                <a href="{{ route('stocks.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('stocks.index', 'stocks.show', 'stocks.adjust') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🏬</span>
                    <span>สต็อกสินค้า</span>
                </a>

                <a href="{{ route('stock-movements.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('stock-movements.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🔄</span>
                    <span>ประวัติสต็อก</span>
                </a>

                <a href="{{ route('stock-counts.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('stock-counts.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🧮</span>
                    <span>ตรวจนับสต็อก</span>
                </a>

                <a href="{{ route('expiring-products.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('expiring-products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>⏳</span>
                    <span>สินค้าใกล้หมดอายุ</span>
                </a>
            </div>
        </div>

        {{-- Admin: จัดซื้อ --}}
        @php
            $isActive = request()->routeIs(['suppliers.*', 'purchase-orders.*', 'goods-receipts.*', 'purchase-returns.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>จัดซื้อ</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('suppliers.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🏢</span>
                    <span>ซัพพลายเออร์</span>
                </a>

                <a href="{{ route('purchase-orders.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('purchase-orders.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📝</span>
                    <span>ใบสั่งซื้อ</span>
                </a>

                <a href="{{ route('goods-receipts.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('goods-receipts.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📥</span>
                    <span>รับสินค้าเข้า</span>
                </a>

                <a href="{{ route('purchase-returns.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('purchase-returns.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>↩️</span>
                    <span>คืนสินค้าให้ผู้ขาย</span>
                </a>
            </div>
        </div>
    @endif

    {{-- Admin และ Staff: การขาย --}}
    @if(in_array(auth()->user()?->role, ['admin', 'staff']))
        @php
            $isActive = request()->routeIs(['pos.*', 'sales.*', 'sale-returns.*', 'online.orders.admin.*', 'payments.*', 'refunds.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>การขาย</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('pos.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🛒</span>
                    <span>หน้าขาย POS</span>
                </a>

                <a href="{{ route('sales.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🧾</span>
                    <span>ประวัติการขาย</span>
                </a>

                <a href="{{ route('sale-returns.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('sale-returns.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>↪️</span>
                    <span>รับคืนสินค้า</span>
                </a>

                <a href="{{ route('online.orders.admin.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.orders.admin.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🌐</span>
                    <span>ออเดอร์ออนไลน์</span>
                </a>

                <a href="{{ route('payments.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>💳</span>
                    <span>การชำระเงิน</span>
                </a>

                <a href="{{ route('refunds.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('refunds.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>💸</span>
                    <span>คืนเงิน</span>
                </a>
            </div>
        </div>
    @endif

    {{-- Admin: ลูกค้าและการตลาด --}}
    @if(auth()->user()?->role === 'admin')
        @php
            $isActive = request()->routeIs(['members.*', 'apartment-rooms.*', 'loyalty.*', 'promotions.*', 'delivery-fee-rules.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>ลูกค้าและการตลาด</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('members.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('members.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>👥</span>
                    <span>สมาชิก</span>
                </a>

                <a href="{{ route('apartment-rooms.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('apartment-rooms.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🏠</span>
                    <span>ห้องพัก</span>
                </a>

                <a href="{{ route('loyalty.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('loyalty.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>⭐</span>
                    <span>แต้มสะสม</span>
                </a>

                <a href="{{ route('promotions.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('promotions.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🏷️</span>
                    <span>โปรโมชั่น</span>
                </a>

                <a href="{{ route('delivery-fee-rules.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('delivery-fee-rules.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🚚</span>
                    <span>ค่าจัดส่ง</span>
                </a>
            </div>
        </div>

        {{-- Admin: รายงาน --}}
        @php
            $isActive = request()->routeIs(['reports.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>รายงาน</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('reports.sales') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('reports.sales') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📈</span>
                    <span>รายงานยอดขาย</span>
                </a>

                <a href="{{ route('reports.products') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('reports.products') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📊</span>
                    <span>รายงานสินค้า</span>
                </a>

                <a href="{{ route('reports.stocks') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('reports.stocks') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📚</span>
                    <span>รายงานสต็อก</span>
                </a>

                <a href="{{ route('reports.profits') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('reports.profits') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>💰</span>
                    <span>รายงานกำไร</span>
                </a>

                <a href="{{ route('reports.purchases') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('reports.purchases') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📋</span>
                    <span>รายงานจัดซื้อ</span>
                </a>
            </div>
        </div>

        {{-- Admin: ระบบ --}}
        @php
            $isActive = request()->routeIs(['users.*', 'audit-logs.*', 'settings.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>ระบบ</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🔐</span>
                    <span>ผู้ใช้งานและสิทธิ์</span>
                </a>

                <a href="{{ route('audit-logs.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('audit-logs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🕘</span>
                    <span>บันทึกการใช้งาน</span>
                </a>

                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>⚙️</span>
                    <span>ตั้งค่าระบบ</span>
                </a>
            </div>
        </div>
    @endif

    {{-- Member: ร้านค้าออนไลน์ --}}
    @if(auth()->user()?->role === 'member')
        @php
            $isActive = request()->routeIs(['online.products.*', 'online.cart.*', 'online.checkout.*', 'online.orders.*', 'online.profile.*', 'online.loyalty.*']);
        @endphp
        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }}" class="space-y-1">
            <button @click="open = !open" type="button"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                <span>ร้านค้าออนไลน์</span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="space-y-1 pl-2">
                <a href="{{ route('online.products.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🛍️</span>
                    <span>เลือกสินค้า</span>
                </a>

                <a href="{{ route('online.cart.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.cart.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>🛒</span>
                    <span>ตะกร้าสินค้า</span>
                </a>

                <a href="{{ route('online.checkout.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.checkout.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>💳</span>
                    <span>ชำระเงิน</span>
                </a>

                <a href="{{ route('online.orders.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.orders.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>📦</span>
                    <span>คำสั่งซื้อของฉัน</span>
                </a>

                <a href="{{ route('online.profile.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.profile.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>👤</span>
                    <span>ข้อมูลสมาชิก</span>
                </a>

                <a href="{{ route('online.loyalty.index') }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition
                          {{ request()->routeIs('online.loyalty.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <span>⭐</span>
                    <span>แต้มสะสมของฉัน</span>
                </a>
            </div>
        </div>
    @endif

</nav>