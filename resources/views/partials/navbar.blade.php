<header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-6 py-3.5 flex justify-between items-center transition-all">
    <!-- Page Title Section -->
    <div class="flex items-center gap-3">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">
            @yield('page-title', 'POS V2')
        </h2>
    </div>

    <!-- User Profile & Action Section -->
    <div class="flex items-center gap-4">
        <!-- User Info Badge -->
        <div class="flex items-center gap-3">
            <!-- Avatar Circle (แสดงอักษรแรกของชื่อ) -->
            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-200 text-blue-600 font-semibold flex items-center justify-center text-sm shadow-sm">
                {{ strtoupper(mb_substr(auth()->user()->name ?? 'G', 0, 1)) }}
            </div>

            <div class="hidden sm:block text-left">
                <p class="text-sm font-semibold text-slate-700 leading-tight">
                    {{ auth()->user()->name ?? 'Guest' }}
                </p>
                @auth
                    <p class="text-xs text-emerald-600 font-medium flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        ออนไลน์
                    </p>
                @endauth
            </div>
        </div>

        @auth
            <!-- Divider Line -->
            <div class="h-5 w-px bg-slate-200"></div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 transition duration-150 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    <!-- Icon Logout -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>ออกจากระบบ</span>
                </button>
            </form>
        @endauth
    </div>
</header>