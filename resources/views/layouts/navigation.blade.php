@php
    $navItems = [
        [
            'label' => 'Dashboard',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
            'icon' => 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z',
        ],
        [
            'label' => 'ฟาร์ม',
            'href' => url('/farms'),
            'active' => request()->is('farms*'),
            'icon' => 'M4 21V9l8-6 8 6v12h-5v-7H9v7H4Zm5-9h6V9H9v3Z',
        ],
        [
            'label' => 'รุ่นการเลี้ยง',
            'href' => url('/flocks'),
            'active' => request()->routeIs('flocks.index') || request()->routeIs('flocks.create') || request()->routeIs('flocks.edit') || request()->routeIs('flocks.show'),
            'icon' => 'M7 20a4 4 0 0 1-4-4V7h7v9a4 4 0 0 1-3 3.87V20Zm10 0v-.13A4 4 0 0 1 14 16V7h7v9a4 4 0 0 1-4 4ZM8 5a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm8 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z',
        ],
        [
            'label' => 'แหล่งลูกไก่',
            'href' => route('chick-sources.index'),
            'active' => request()->routeIs('chick-sources.*'),
            'icon' => 'M4 20V6a2 2 0 0 1 2-2h4l2-2 2 2h4a2 2 0 0 1 2 2v14H4Zm4-3h8v-2H8v2Zm0-4h8v-2H8v2Zm0-4h5V7H8v2Z',
        ],
        [
            'label' => 'อุณหภูมิ/ความชื้น/สูญเสียรายวัน',
            'href' => route('daily-records.shortcut'),
            'active' => request()->is('daily-records') || request()->is('flocks/*/daily-records*'),
            'icon' => 'M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm8 1.5V8h4.5M8 12h8M8 16h8M8 20h5',
        ],
        [
            'label' => 'นำเข้าข้อมูล (.txt)',
            'href' => route('daily-records.import-page'),
            'active' => request()->routeIs('daily-records.import-page'),
            'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        ],
        [
            'label' => 'รับอาหาร',
            'href' => route('feed-receipts.index'),
            'active' => request()->routeIs('feed-receipts.*'),
            'icon' => 'M4 6h16v4H4V6Zm1 6h14l-1 8H6l-1-8Zm4-9h6l1 2H8l1-2Z',
        ],
        [
            'label' => 'จับไก่ขาย',
            'href' => route('sale-records.shortcut'),
            'active' => request()->is('sale-records') || request()->routeIs('flocks.sale-records.*'),
            'icon' => 'M4 6h16v4H4V6Zm2 6h12v8H6v-8Zm3-9h6l2 2H7l2-2Zm1 12h4v2h-4v-2Z',
        ],
        [
            'label' => 'ราคาขาย',
            'href' => route('flock-close.shortcut'),
            'label' => 'ปิดรุ่นการเลี้ยง',
            'active' => request()->is('flock-close') || request()->routeIs('flocks.close.*'),
            'icon' => 'M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm3 5h8V7H8v2Zm0 4h8v-2H8v2Zm0 4h5v-2H8v2Z',
        ],
        [
            'label' => 'เธฃเธฒเธเธฒเธเธฒเธข',
            'href' => route('sale-price-masters.index'),
            'label' => 'ราคาขาย',
            'active' => request()->routeIs('sale-price-masters.*'),
            'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 15h-2v-1.2a4.2 4.2 0 0 1-2.4-1l1-1.7c.8.6 1.6.9 2.5.9.8 0 1.3-.3 1.3-.9 0-.5-.3-.8-1.7-1.2-1.8-.5-2.8-1.2-2.8-2.8 0-1.4.9-2.4 2.1-2.7V5h2v1.3c.8.2 1.5.5 2.1 1l-.9 1.7c-.7-.5-1.4-.7-2.1-.7-.7 0-1.1.3-1.1.8s.3.7 1.8 1.1c1.8.5 2.7 1.3 2.7 2.8 0 1.4-.9 2.4-2.5 2.8V17Z',
        ],
        [
            'label' => 'มิเตอร์น้ำรายวัน',
            'href' => route('water-meters.shortcut'),
            'active' => request()->is('water-meters') || request()->routeIs('flocks.water-meters.*'),
            'icon' => 'M12 22a7 7 0 0 1-7-7c0-4.5 7-13 7-13s7 8.5 7 13a7 7 0 0 1-7 7Zm0-4a3 3 0 0 0 3-3h-2a1 1 0 0 1-1 1v2Z',
        ],
        [
            'label' => 'ลงน้ำหนักไก่',
            'href' => route('weight-records.shortcut'),
            'active' => request()->is('weight-records') || request()->routeIs('flocks.weight-records.*'),
            'icon' => 'M12 3v18m-6-6 6 6 6-6M5 12h14M3 6h18',
        ],
        [
            'label' => 'ใบหน้าเล้า',
            'href' => route('summary.shortcut'),
            'active' => request()->is('summary') || request()->is('flocks/*/summary'),
            'icon' => 'M4 19V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14H4Zm4-3h2V8H8v8Zm4 0h2v-5h-2v5Zm4 0h2V6h-2v10Z',
        ],
    ];

    if (auth()->user()->isSuperAdmin()) {
        $navItems[] = [
            'label' => 'ผู้ใช้',
            'href' => route('users.index'),
            'active' => request()->routeIs('users.*'),
            'icon' => 'M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm-4 6c-4.2 0-7 2.1-7 4v1h14v-1c0-1.9-2.8-4-7-4Zm7-12a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm0 8c-1 0-1.9.2-2.7.5 1.2.8 2 1.8 2.4 2.9H23v-.8c0-1.4-1.8-2.6-4-2.6Z',
        ];
        $navItems[] = [
            'label' => 'ราคาลูกไก่',
            'href' => route('chick-price-masters.index'),
            'active' => request()->routeIs('chick-price-masters.*'),
            'icon' => 'M12 3c2 0 4 1.5 4 4v1h2a3 3 0 0 1 3 3v7H3v-7a3 3 0 0 1 3-3h2V7c0-2.5 2-4 4-4Zm-2 5h4V7a2 2 0 0 0-4 0v1Zm-4 5h3v2H6v-2Zm5 0h3v2h-3v-2Zm5 0h2v2h-2v-2Z',
        ];
        $navItems[] = [
            'label' => 'เกณฑ์การกินอาหาร/วัน',
            'href' => route('feed-intake-masters.index'),
            'active' => request()->routeIs('feed-intake-masters.*'),
            'icon' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
        ];
    }
@endphp

<div>
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-md bg-emerald-700 text-sm font-semibold text-white">ไก่</span>
            <span class="text-base font-semibold text-slate-900">ระบบเลี้ยงไก่เนื้อ</span>
        </a>

        <button type="button" @click="sidebarOpen = true" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50">
            <span class="sr-only">เปิดเมนู</span>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-emerald-900/20 bg-slate-950 text-white shadow-2xl transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : (sidebarCollapsed ? '-translate-x-full lg:-translate-x-full' : '-translate-x-full lg:translate-x-0')"
    >
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-md bg-emerald-500 text-sm font-semibold text-slate-950 shadow-sm">ไก่</span>
                <span>
                    <span class="block text-base font-semibold">ระบบเลี้ยงไก่เนื้อ</span>
                    <span class="block text-xs text-emerald-100/70">Broiler Farm MVP</span>
                </span>
            </a>

            <div class="flex items-center gap-1">
                <!-- Collapse button for desktop -->
                <button type="button" @click="sidebarCollapsed = true; localStorage.setItem('sidebar-collapsed', 'true')" class="hidden lg:flex rounded-md p-1.5 text-slate-400 hover:bg-white/10 hover:text-white transition-all" title="ซ่อนเมนู">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <!-- Close button for mobile -->
                <button type="button" @click="sidebarOpen = false" class="rounded-md p-2 text-slate-300 hover:bg-white/10 lg:hidden">
                    <span class="sr-only">ปิดเมนู</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5">
            <div class="mb-4 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">เมนูหลัก</div>
            <nav class="space-y-1">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        class="group flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition {{ $item['active'] ? 'bg-emerald-500 text-slate-950 shadow-sm shadow-emerald-950/20' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="h-5 w-5 shrink-0 {{ $item['active'] ? 'text-slate-950' : 'text-emerald-200/80 group-hover:text-emerald-200' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="{{ $item['icon'] }}" />
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="border-t border-white/10 p-4">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm text-slate-200 hover:bg-white/10">
                <span class="grid h-9 w-9 place-items-center rounded-md bg-white/10 text-sm font-semibold text-white">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate font-medium">{{ Auth::user()->name }}</span>
                    <span class="block truncate text-xs text-slate-400">{{ Auth::user()->email }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center rounded-md border border-white/10 px-3 py-2 text-sm font-medium text-slate-200 hover:bg-white/10 hover:text-white">
                    ออกจากระบบ
                </button>
            </form>
        </div>
    </aside>
</div>
