<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between py-2">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center rounded-lg bg-emerald-500/10 p-2 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $flock->flock_code }}</h1>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mt-0.5">รายละเอียดรุ่นการเลี้ยง</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('flocks.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับรายการ
                </a>
                <a href="{{ route('flocks.daily-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    อุณหภูมิ/ความชื้น/สูญเสีย
                </a>
                <a href="{{ route('flocks.placements.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2 0 4 1.5 4 4v1h2a3 3 0 0 1 3 3v7H3v-7a3 3 0 0 1 3-3h2V7c0-2.5 2-4 4-4Zm-2 5h4V7a2 2 0 0 0-4 0v1Zm-4 5h3v2H6v-2Zm5 0h3v2h-3v-2Zm5 0h2v2h-2v-2Z" />
                    </svg>
                    ข้อมูลลูกไก่
                </a>
                <a href="{{ route('flocks.summary', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    ใบหน้าเล้า
                </a>
                <a href="{{ route('flocks.sale-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    จับไก่ขาย
                </a>
                <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    บันทึกคิวจับไก่
                </a>
                <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    นน.หน้าโรงงาน
                </a>
                <a href="{{ route('flocks.close.show', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-amber-300 bg-amber-50 px-3.5 py-2 text-sm font-semibold text-amber-800 shadow-sm hover:bg-amber-100 transition-colors">
                    <svg class="mr-1.5 h-4 w-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    ปิดรุ่น
                </a>
                <a href="{{ route('flocks.edit', $flock) }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    แก้ไข
                </a>
                @if(auth()->user()->isSuperAdmin())
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-flock-deletion')" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-red-50 px-3.5 py-2 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-100 transition-colors">
                        <svg class="mr-1.5 h-4 w-4 text-red-650" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        ลบ
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full max-w-[98%] px-2 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <!-- 1. Key Metrics Cards Grid -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- KPI 1: Initial Birds -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-blue-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">จำนวนไก่เริ่มต้นรวม</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($flock->initial_birds) }} <span class="text-sm font-medium text-slate-500">ตัว</span></h3>
                            <p class="mt-1 text-xs text-slate-500">สำหรับทั้งรุ่นการเลี้ยง</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- KPI 2: Start Date -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-amber-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">วันที่เริ่มเลี้ยง</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $flock->start_date->format('d/m/Y') }}</h3>
                            <p class="mt-1 text-xs text-slate-500">วันลงเลี้ยงวันแรก</p>
                        </div>
                        <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Chicken Type -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-sky-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">ประเภทไก่</p>
                            <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $flock->chicken_type }}</h3>
                            <p class="mt-1 text-xs text-slate-500">สายพันธุ์ที่เลี้ยง</p>
                        </div>
                        <div class="rounded-xl bg-sky-50 p-3 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V3a1 1 0 00-1-1h-2a1 1 0 00-1 1v15a1 1 0 001 1h2a1 1 0 001-1z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Status -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                    @php
                        $isActive = $flock->status === 'active';
                    @endphp
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full {{ $isActive ? 'bg-emerald-50' : 'bg-slate-100' }} transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">สถานะรุ่น</p>
                            <h3 class="mt-2 text-2xl font-bold {{ $isActive ? 'text-emerald-600' : 'text-slate-600' }}">
                                {{ $isActive ? 'กำลังเลี้ยง' : 'ปิดรุ่นเรียบร้อยแล้ว' }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">สถานะปัจจุบัน</p>
                        </div>
                        <div class="rounded-xl {{ $isActive ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-600' }} p-3">
                            @if ($isActive)
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Detailed Metadata Card and Placements Table -->
            <div class="grid gap-6 lg:grid-cols-12 items-start">
                
                <!-- Left Sidebar Details -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4 border-b border-slate-100 pb-3">
                            <h2 class="text-sm font-bold text-slate-900">ข้อมูลทั่วไปรุ่นเลี้ยง</h2>
                        </div>
                        <dl class="space-y-4 text-xs">
                            <div>
                                <dt class="font-semibold text-slate-400 uppercase tracking-wider">ฟาร์มที่สังกัด</dt>
                                <dd class="mt-1 text-sm font-bold text-slate-800">{{ $flock->farm->farm_name }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400 uppercase tracking-wider">รหัสรุ่นการเลี้ยง</dt>
                                <dd class="mt-1 text-sm font-bold text-slate-800">{{ $flock->flock_code }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-400 uppercase tracking-wider">หมายเหตุ / บันทึกเพิ่มเติม</dt>
                                <dd class="mt-1 text-sm font-medium text-slate-600 bg-slate-50 rounded-lg p-2.5 whitespace-pre-line border border-slate-100">{{ $flock->note ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Right Placements Table -->
                <div class="lg:col-span-9">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">รายละเอียดการลงไก่รายเล้า</h2>
                                <p class="text-xs text-slate-500 mt-0.5">ตารางบันทึกจำนวนไก่ เพศ และราคาค่าไก่แยกตามเล้าเลี้ยง</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                รวม {{ $flock->flockHouseStarts->count() }} เล้า
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse min-w-[1800px]">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50/30 text-slate-500 font-bold uppercase tracking-wider">
                                        <th class="py-3 px-3 text-center border-r border-slate-100">เล้า</th>
                                        <th class="py-3 px-3 text-center">วันที่ลงไก่</th>
                                        <th class="py-3 px-3 text-center">วันที่จับขาย</th>
                                        <th class="py-3 px-3 text-right">อายุจับ (วัน)</th>
                                        <th class="py-3 px-3 text-right bg-emerald-50/20 text-emerald-950 font-bold">จำนวนไก่เข้า (ตัว)</th>
                                        <th class="py-3 px-3 text-right">เพศผู้ (M)</th>
                                        <th class="py-3 px-3 text-right">เพศเมีย (FM)</th>
                                        <th class="py-3 px-3 text-right">ผู้เกรด A</th>
                                        <th class="py-3 px-3 text-right">ผู้เกรด B</th>
                                        <th class="py-3 px-3 text-right">เมียเกรด A</th>
                                        <th class="py-3 px-3 text-right">เมียเกรด B</th>
                                        <th class="py-3 px-3 text-right bg-indigo-50/20 text-indigo-950 font-bold">จำนวนเงิน (บาท)</th>
                                        <th class="py-3 px-3 text-left">แหล่งลูกไก่</th>
                                        <th class="py-3 px-3 text-left">เกรดลูกไก่</th>
                                        <th class="py-3 px-3 text-left">รหัสสายพันธุ์</th>
                                        <th class="py-3 px-3 text-left">Batch No.</th>
                                        <th class="py-3 px-3 text-left">เพศ</th>
                                        <th class="py-3 px-3 text-left">พันธุ์</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                                    @php $placementsByHouse = $flock->flockHousePlacements->groupBy('house_id'); @endphp
                                    @foreach ($flock->flockHouseStarts->sortBy('house.house_no') as $start)
                                        @php
                                            $housePlacements = $placementsByHouse->get($start->house_id) ?: collect();
                                            $firstPlacement = $housePlacements->sortBy('placement_date')->first();
                                            $lastPlacement = $housePlacements->whereNotNull('catch_date')->sortByDesc('catch_date')->first();

                                            $placementDate = $firstPlacement?->placement_date;
                                            $catchDate = $lastPlacement?->catch_date;
                                            $catchAge = $housePlacements->whereNotNull('catch_age')->max('catch_age');
                                            $maleCount = $housePlacements->sum('male_count');
                                            $femaleCount = $housePlacements->sum('female_count');
                                            $maleGradeA = $housePlacements->sum('male_grade_a_count');
                                            $maleGradeB = $housePlacements->sum('male_grade_b_count');
                                            $femaleGradeA = $housePlacements->sum('female_grade_a_count');
                                            $femaleGradeB = $housePlacements->sum('female_grade_b_count');
                                            $amount = $housePlacements->sum('amount');
                                            
                                            $chickSource = $housePlacements->pluck('chick_source')->filter()->unique()->implode(', ');
                                            $chickGrade = $housePlacements->pluck('chick_grade')->filter()->unique()->implode(', ');
                                            $chickCode = $housePlacements->pluck('chick_code')->filter()->unique()->implode(', ');
                                            $batchNo = $housePlacements->pluck('batch_no')->filter()->unique()->implode(', ');
                                            $maleSum = $housePlacements->sum('male_count');
                                            $femaleSum = $housePlacements->sum('female_count');
                                            if ($maleSum > 0 && $femaleSum > 0) {
                                                $sex = 'คละ';
                                            } elseif ($maleSum > 0) {
                                                $sex = 'ผู้';
                                            } elseif ($femaleSum > 0) {
                                                $sex = 'เมีย';
                                            } else {
                                                $sex = $housePlacements->pluck('sex')->filter()->unique()->implode(', ');
                                            }
                                            $breed = $housePlacements->pluck('breed')->filter()->unique()->implode(', ');
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="whitespace-nowrap py-3 px-3 text-center font-bold text-slate-900 border-r border-slate-100 bg-slate-50/10">เล้า {{ $start->house->house_no }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-center font-medium">{{ $placementDate ? thai_date($placementDate) : thai_date($start->start_date ?: $flock->start_date) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-center text-slate-500">{{ $catchDate ? thai_date($catchDate) : '-' }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right font-medium text-slate-800">{{ $catchAge === null ? '-' : number_format($catchAge) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right font-bold text-emerald-600 bg-emerald-50/10">{{ number_format($start->initial_birds) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right">{{ number_format($maleCount) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right">{{ number_format($femaleCount) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right text-slate-500">{{ number_format($maleGradeA) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right text-slate-500">{{ number_format($maleGradeB) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right text-slate-500">{{ number_format($femaleGradeA) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right text-slate-500">{{ number_format($femaleGradeB) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-right font-bold text-indigo-600 bg-indigo-50/10">{{ number_format((float) $amount, 2) }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-slate-600 font-medium">{{ $chickSource ?: '-' }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-slate-600">{{ $chickGrade ?: '-' }}</td>
                                            <td class="py-3 px-3 text-slate-500 font-mono">{{ $chickCode ?: '-' }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-slate-500">{{ $batchNo ?: '-' }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-slate-500">{{ $sex ?: '-' }}</td>
                                            <td class="whitespace-nowrap py-3 px-3 text-slate-500">{{ $breed ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Modals -->
    <x-modal name="confirm-flock-deletion" :show="$errors->{'flockDeletion_' . $flock->id}->isNotEmpty()" focusable>
        <form method="post" action="{{ route('flocks.destroy', $flock) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900">
                คุณแน่ใจหรือไม่ว่าต้องการลบรุ่นการเลี้ยงนี้?
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                เมื่อลบรุ่นการเลี้ยงนี้แล้ว ข้อมูลทั้งหมดที่เกี่ยวข้องจะถูกลบอย่างถาวร โปรดยืนยันรหัสผ่านของคุณเพื่อดำเนินการต่อ
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="รหัสผ่าน" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 text-xs"
                    placeholder="รหัสผ่านผู้ใช้"
                />

                <x-input-error :messages="$errors->{'flockDeletion_' . $flock->id}->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')" class="text-xs">
                    ยกเลิก
                </x-secondary-button>

                <x-danger-button class="text-xs">
                    ยืนยันการลบ
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
