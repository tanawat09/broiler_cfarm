<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between py-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">จัดการปิดรุ่นการเลี้ยงและบันทึกจับขาย</h1>
                </div>
                <p class="mt-1.5 text-sm text-slate-500 font-medium">
                    ฟาร์ม: <span class="text-slate-800 font-semibold">{{ $flock->farm->farm_name }}</span> / 
                    รุ่นการเลี้ยง: <span class="text-slate-800 font-semibold">{{ $flock->flock_code }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if ($flock->status === 'closed')
                    <span class="inline-flex items-center rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 border border-green-200">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        ปิดรุ่นแล้ว
                    </span>
                    @if(auth()->user()->isSuperAdmin())
                        <form method="POST" action="{{ route('flocks.close.unlock', $flock) }}" onsubmit="return confirm('ยืนยันปลดล็อกรุ่นการเลี้ยงนี้เพื่อแก้ไขข้อมูลหรือไม่?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors">
                                ปลดล็อกรุ่นการเลี้ยง
                            </button>
                        </form>
                    @endif
                @else
                    <form method="POST" action="{{ route('flocks.close.store', $flock) }}" onsubmit="return confirm('ยืนยันปิดรุ่นการเลี้ยงนี้หรือไม่?')">
                        @csrf
                        <button
                            type="submit"
                            @disabled($summary['unbalanced_houses'] > 0)
                            class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold shadow-sm transition-all {{ $summary['unbalanced_houses'] > 0 ? 'cursor-not-allowed bg-slate-200 text-slate-400 border border-slate-350' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                        >
                            ปิดรุ่นการเลี้ยง
                        </button>
                    </form>
                @endif

                <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับรายละเอียดรุ่น
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full max-w-[98%] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @error('close')
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-r from-slate-50 to-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-end justify-between">
                    <div class="grid gap-4 sm:grid-cols-2 md:w-[600px]">
                        <div>
                            <x-input-label for="farm_selector" value="เลือกฟาร์ม" class="text-xs font-semibold uppercase tracking-wider text-slate-500" />
                            <select id="farm_selector" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="flock_selector" value="เลือกรุ่นการเลี้ยง" class="text-xs font-semibold uppercase tracking-wider text-slate-500" />
                            <select id="flock_selector" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                @foreach ($flockOptions as $option)
                                    <option
                                        value="{{ $option->id }}"
                                        data-farm-id="{{ $option->farm_id }}"
                                        data-close-url="{{ route('flocks.close.show', $option) }}"
                                        @selected((int) $option->id === (int) $flock->id)
                                    >
                                        {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('flocks.sale-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                            ประวัติจับไก่ขาย
                        </a>
                    </div>
                </div>
            </div>

            <!-- Summary Grid 1 -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Card 1: Status -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-slate-50 transition-all group-hover:scale-150 animate-pulse"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">สถานะรุ่นการเลี้ยง</p>
                            <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $flock->status === 'closed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $flock->status === 'closed' ? 'bg-green-500' : 'bg-amber-500 animate-pulse' }}"></span>
                                {{ $flock->status === 'closed' ? 'ปิดรุ่นเรียบร้อยแล้ว' : 'กำลังเลี้ยง / จับขาย' }}
                            </span>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-2.5 text-slate-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Initial Birds -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-emerald-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">จำนวนไก่เข้าเริ่มต้น</p>
                            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($summary['initial_birds']) }} <span class="text-sm font-normal text-slate-500">ตัว</span></p>
                        </div>
                        <div class="rounded-lg bg-emerald-50 p-2.5 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Accumulated Losses -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-rose-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">สูญเสียสะสม (ตาย/คัดทิ้ง)</p>
                            <p class="mt-1 text-2xl font-bold text-rose-600">{{ number_format($summary['loss_total']) }} <span class="text-sm font-normal text-slate-500">ตัว</span></p>
                            <p class="text-[10px] text-slate-500 mt-1">ตาย {{ number_format($summary['dead_total']) }} | คัดทิ้ง {{ number_format($summary['cull_total']) }}</p>
                        </div>
                        <div class="rounded-lg bg-rose-50 p-2.5 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Sold -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-sky-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">จับไก่ขายสะสม</p>
                            <p class="mt-1 text-2xl font-bold text-sky-600">{{ number_format($summary['birds_sold']) }} <span class="text-sm font-normal text-slate-500">ตัว</span></p>
                            <p class="text-[10px] text-slate-500 mt-1">ทั้งหมด {{ number_format($summary['sale_trips']) }} เที่ยวจับ</p>
                        </div>
                        <div class="rounded-lg bg-sky-50 p-2.5 text-sky-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Grid 2 -->
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Card 5: Balance Diff -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full {{ $summary['balance_diff'] === 0 ? 'bg-emerald-50' : 'bg-red-50' }} transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">ยอดคงเหลือรอเคลียร์</p>
                            <p class="mt-1 text-2xl font-bold {{ $summary['balance_diff'] === 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($summary['balance_diff']) }} <span class="text-sm font-normal text-slate-500">ตัว</span>
                            </p>
                        </div>
                        <div class="rounded-lg {{ $summary['balance_diff'] === 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} p-2.5">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Unbalanced Houses -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full {{ $summary['unbalanced_houses'] === 0 ? 'bg-emerald-50' : 'bg-amber-50' }} transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">เล้าที่ยอดไม่ตรง</p>
                            <p class="mt-1 text-2xl font-bold {{ $summary['unbalanced_houses'] === 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ number_format($summary['unbalanced_houses']) }} <span class="text-sm font-normal text-slate-500">เล้า</span>
                            </p>
                        </div>
                        <div class="rounded-lg {{ $summary['unbalanced_houses'] === 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} p-2.5">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 7: Total Weight -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-indigo-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">น้ำหนักจับขายรวม</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-900">{{ number_format($summary['total_weight'], 2) }} <span class="text-sm font-normal text-slate-500">กก.</span></p>
                            <p class="text-[10px] text-slate-500 mt-1">เฉลี่ย {{ $summary['avg_sale_weight'] === null ? '-' : number_format($summary['avg_sale_weight'], 3) }} กก./ตัว</p>
                        </div>
                        <div class="rounded-lg bg-indigo-50 p-2.5 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 8: Total Amount -->
                <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-amber-50 transition-all group-hover:scale-150"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">รายได้จากการจับขายรวม</p>
                            <p class="mt-1 text-2xl font-bold text-amber-700">{{ number_format($summary['total_amount'], 2) }} <span class="text-sm font-normal text-slate-500">บาท</span></p>
                        </div>
                        <div class="rounded-lg bg-amber-50 p-2.5 text-amber-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex gap-3 rounded-xl border border-blue-100 bg-blue-50/50 p-4 text-sm text-blue-900 shadow-sm">
                <svg class="h-5 w-5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-semibold text-blue-950">ข้อมูลเงื่อนไขปิดรุ่น:</span> จำนวนไก่เข้าเริ่มต้นของเล้า จะต้องเท่ากับ (ยอดตายสะสม + ยอดคัดทิ้งสะสม + ยอดจับขายรวมทั้งหมด) จึงจะถือว่ายอดไก่ตรงและระบบจะอนุญาตให้ทำการปิดรุ่นได้
                </div>
            </div>

            @if ($flock->status === 'active' && $summary['unbalanced_houses'] > 0)
                <div class="mb-6 overflow-hidden rounded-xl border-l-4 border-red-500 bg-red-50/40 p-5 shadow-sm animate-fade-in">
                    <div class="flex items-start gap-3">
                        <div class="rounded-lg bg-red-100 p-2 text-red-600 shrink-0">
                            <svg class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="w-full">
                            <h3 class="text-base font-bold text-red-950">ยังไม่สามารถทำการปิดรุ่นได้</h3>
                            <p class="mt-1 text-sm text-red-800">เนื่องจากพบยอดจำนวนไก่สะสมไม่ตรงกันในเล้าดังต่อไปนี้:</p>
                            <div class="mt-3 overflow-x-auto">
                                <table class="w-full text-left text-sm text-red-900 border-t border-red-100">
                                    <thead>
                                        <tr class="text-xs uppercase tracking-wider text-red-700">
                                            <th class="py-2 font-semibold">หมายเลขเล้า</th>
                                            <th class="py-2 text-right font-semibold">ยอดต่างสะสม</th>
                                            <th class="py-2 pl-6 font-semibold">สาเหตุ/คำอธิบาย</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-red-100/50">
                                        @foreach ($rows as $row)
                                            @if (!$row['is_balanced'])
                                                <tr>
                                                    <td class="py-2.5 font-bold">เล้า {{ $row['house_no'] }}</td>
                                                    <td class="py-2.5 text-right font-mono font-bold text-red-700">
                                                        {{ $row['balance_diff'] > 0 ? '+'.number_format($row['balance_diff']) : number_format($row['balance_diff']) }} ตัว
                                                    </td>
                                                    <td class="py-2.5 pl-6 text-xs text-red-800">
                                                        @if ($row['balance_diff'] > 0)
                                                            <span class="inline-flex items-center rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-medium text-amber-800 mr-1.5">ยอดขาด</span>
                                                            ไก่จับขายยังขาดไปอีก {{ number_format($row['balance_diff']) }} ตัว เมื่อเทียบกับไก่คงเหลือ
                                                        @else
                                                            <span class="inline-flex items-center rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-medium text-red-800 mr-1.5">ยอดเกิน</span>
                                                            ไก่ถูกจับขายเกินมา {{ number_format(abs($row['balance_diff'])) }} ตัว เกินยอดไก่ที่มีอยู่จริง
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-4 text-xs text-red-600/80 font-medium">
                                * คำแนะนำ: กรุณาแก้ไขบันทึกยอดจับไก่ขาย หรือปรับยอดรายงานตาย/คัดทิ้งประจำวันในระบบของเล้าด้านบนให้เรียบร้อยจนกระทั่งผลต่างเป็น 0 ตัว
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($flock->status === 'closed' || $summary['unbalanced_houses'] === 0)
                <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h2 class="text-base font-bold text-slate-900">ตารางสรุปรายละเอียดปิดรุ่นการเลี้ยงรายเล้า</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">เล้า</th>
                                    <th class="px-4 py-3 text-right font-semibold">ไก่เข้าเริ่มต้น</th>
                                    <th class="px-4 py-3 text-right font-semibold">ตายสะสม</th>
                                    <th class="px-4 py-3 text-right font-semibold">คัดทิ้งสะสม</th>
                                    <th class="px-4 py-3 text-right font-semibold">สูญเสียรวม</th>
                                    <th class="px-4 py-3 text-right font-semibold">จับขายแล้ว</th>
                                    <th class="px-4 py-3 text-right font-semibold">ผลต่างสะสม</th>
                                    <th class="px-4 py-3 text-right font-semibold">จำนวนเที่ยวจับ</th>
                                    <th class="px-4 py-3 text-right font-semibold">น้ำหนักรวม (กก.)</th>
                                    <th class="px-4 py-3 text-right font-semibold">น้ำหนักเฉลี่ย (กก./ตัว)</th>
                                    <th class="px-4 py-3 text-right font-semibold">ยอดเงินรวม (บาท)</th>
                                    <th class="px-4 py-3 text-center font-semibold">สถานะยอดไก่</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($rows as $row)
                                    <tr class="hover:bg-slate-50/50 transition-colors {{ $row['is_balanced'] ? '' : ($row['balance_diff'] > 0 ? 'bg-red-50/20' : 'bg-amber-50/20') }}">
                                        <td class="whitespace-nowrap px-4 py-3 font-bold text-slate-900">เล้า {{ $row['house_no'] }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format($row['initial_birds']) }} ตัว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-red-600">{{ number_format($row['dead_total']) }} ตัว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-amber-600">{{ number_format($row['cull_total']) }} ตัว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600 font-semibold">{{ number_format($row['loss_total']) }} ตัว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sky-600 font-semibold">{{ number_format($row['birds_sold']) }} ตัว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-bold {{ $row['is_balanced'] ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $row['balance_diff'] > 0 ? '+'.number_format($row['balance_diff']) : number_format($row['balance_diff']) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format($row['sale_trips']) }} เที่ยว</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700 font-semibold">{{ number_format($row['total_weight'], 2) }} กก.</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">{{ $row['avg_sale_weight'] === null ? '-' : number_format($row['avg_sale_weight'], 3) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-900 font-bold">{{ number_format($row['total_amount'], 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-center">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $row['is_balanced'] ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                                {{ $row['is_balanced'] ? 'ตรง' : ($row['balance_diff'] > 0 ? 'ยังขาด' : 'เกิน') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="mb-6 flex gap-3 rounded-xl border border-amber-100 bg-amber-50/50 p-4 text-sm text-amber-900 shadow-sm">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-semibold text-amber-950">หมายเหตุ:</span> ตารางสรุปรายละเอียดการปิดรุ่นการเลี้ยงจะแสดงขึ้นหลังจากที่ท่านทำการบันทึกจับไก่ขายเสร็จสิ้นครบทุกเล้าแล้วเท่านั้น (ปัจจุบันยังมีบางเล้าที่ยังจับขายไม่ครบ)
                    </div>
                </div>
            @endif

            @if ($flock->status === 'active' && $summary['unbalanced_houses'] > 0)
                <div class="mt-8 mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l8.97-6.36a1.5 1.5 0 0 1 1.76 0l8.97 6.36M12.5 18h.008v.008H12.5V18Zm0-3h.008v.008H12.5V15Zm0-3h.008v.008H12.5V12Zm0-3h.008v.008H12.5V9Zm-3 9h.008v.008H9.5V18Zm0-3h.008v.008H9.5V15Zm0-3h.008v.008H9.5V12Zm0-3h.008v.008H9.5V9Z" />
                            </svg>
                            ตารางบันทึกจับขายรายเล้าประจำรุ่น
                        </h2>
                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">ระบุวันที่จับและคลิกปุ่ม "ยืนยัน" เพื่อดึงข้อมูลและบันทึกทันที</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">เล้า</th>
                                    <th class="px-4 py-3 font-semibold">วันที่จับ</th>
                                    <th class="px-4 py-3 text-right font-semibold">อายุจับ</th>
                                    <th class="px-4 py-3 text-right font-semibold">ไก่เริ่มต้น</th>
                                    <th class="px-4 py-3 text-right font-semibold">สูญเสียสะสม</th>
                                    <th class="px-4 py-3 text-right font-semibold">จับขายแล้ว</th>
                                    <th class="px-4 py-3 text-right font-semibold">คงเหลือพร้อมจับ</th>
                                    <th class="px-4 py-3 text-right font-semibold">นน. เฉลี่ยล่าสุด</th>
                                    <th class="px-4 py-3 text-right font-semibold">นน. รวมคาดการณ์</th>
                                    <th class="px-4 py-3 text-center font-semibold">การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($availableStarts as $start)
                                    @php
                                        $context = $houseSaleContext->get($start->house_id);
                                        $remaining = $context['remaining_birds'] ?? $start->initial_birds;
                                        $loss = $context['loss_total'] ?? 0;
                                        $sold = $context['birds_sold'] ?? 0;
                                        $avgWeight = $context['latest_avg_weight'] ?? null;
                                        $estWeight = $context['estimated_total_weight'] ?? 0;
                                        $rowCatchDate = $context['catch_date'] ?? $saleDate;
                                        $placementDateStr = $context['placement_date'] ?? null;
                                        $catchAge = null;
                                        if ($placementDateStr && $rowCatchDate) {
                                            try {
                                                $pDate = \Carbon\CarbonImmutable::parse($placementDateStr);
                                                $cDate = \Carbon\CarbonImmutable::parse($rowCatchDate);
                                                $catchAge = $pDate->diffInDays($cDate);
                                            } catch (\Exception $e) {}
                                        }
                                        $houseSaleRecord = $saleRecords->firstWhere('house_id', $start->house_id);
                                    @endphp
                                    <tr id="row_house_{{ $start->house_id }}" class="house-row hover:bg-slate-50 transition-colors border-l-4 border-transparent">
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">
                                            @if ($start->house->house_name && $start->house->house_name !== 'เล้า '.$start->house->house_no && $start->house->house_name !== $start->house->house_no)
                                                เล้า {{ $start->house->house_no }} - {{ $start->house->house_name }}
                                            @else
                                                เล้า {{ $start->house->house_no }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <input type="date" 
                                                   id="date_house_{{ $start->house_id }}" 
                                                   value="{{ $rowCatchDate }}" 
                                                   data-house-id="{{ $start->house_id }}"
                                                   data-placement-date="{{ $placementDateStr }}"
                                                   class="house-date-input w-36 rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 px-2 text-slate-700" />
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-700">
                                            <span id="age_house_{{ $start->house_id }}">
                                                {{ $catchAge !== null ? $catchAge.' วัน' : '-' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                            {{ number_format($start->initial_birds) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-red-600">
                                            {{ number_format($loss) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">
                                            {{ number_format($sold) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $remaining > 0 ? 'text-emerald-700' : ($remaining < 0 ? 'text-red-600' : 'text-slate-400') }}">
                                            {{ number_format($remaining) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                            {{ $avgWeight ? number_format($avgWeight, 3).' กก.' : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">
                                            {{ $estWeight ? number_format($estWeight, 2).' กก.' : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-center">
                                            @if ($remaining == 0)
                                                <div class="inline-flex items-center gap-2 justify-center">
                                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                                        จับไก่เรียบร้อย (ตรง)
                                                    </span>
                                                    @if ($flock->status === 'active' && $houseSaleRecord)
                                                        <button type="button" 
                                                                onclick="editRecord(this)" 
                                                                data-id="{{ $houseSaleRecord->id }}"
                                                                data-date="{{ $houseSaleRecord->sale_date->toDateString() }}"
                                                                data-house-id="{{ $houseSaleRecord->house_id }}"
                                                                data-birds-sold="{{ $houseSaleRecord->birds_sold }}"
                                                                data-total-weight="{{ (float) $houseSaleRecord->total_weight }}"
                                                                data-price-per-kg="{{ (float) $houseSaleRecord->price_per_kg }}"
                                                                data-note="{{ $houseSaleRecord->note }}"
                                                                class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-955 hover:underline transition-colors">
                                                            แก้ไข
                                                        </button>
                                                    @endif
                                                </div>
                                            @elseif ($remaining < 0)
                                                <div class="inline-flex items-center gap-2 justify-center">
                                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                                                        จับเกิน (เกิน {{ number_format(abs($remaining)) }} ตัว)
                                                    </span>
                                                    @if ($flock->status === 'active' && $houseSaleRecord)
                                                        <button type="button" 
                                                                onclick="editRecord(this)" 
                                                                data-id="{{ $houseSaleRecord->id }}"
                                                                data-date="{{ $houseSaleRecord->sale_date->toDateString() }}"
                                                                data-house-id="{{ $houseSaleRecord->house_id }}"
                                                                data-birds-sold="{{ $houseSaleRecord->birds_sold }}"
                                                                data-total-weight="{{ (float) $houseSaleRecord->total_weight }}"
                                                                data-price-per-kg="{{ (float) $houseSaleRecord->price_per_kg }}"
                                                                data-note="{{ $houseSaleRecord->note }}"
                                                                class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-955 hover:underline transition-colors">
                                                            แก้ไข
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-3 justify-center">
                                                    @if ($flock->status === 'active')
                                                        <button type="button" 
                                                                onclick="selectHouse({{ $start->house_id }}, {{ $remaining }}, {{ $avgWeight ?? 'null' }}, {{ $estWeight }})"
                                                                class="confirm-house-btn inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                                                            ยืนยัน
                                                        </button>
                                                        @if ($houseSaleRecord)
                                                            <button type="button" 
                                                                    onclick="editRecord(this)" 
                                                                    data-id="{{ $houseSaleRecord->id }}"
                                                                    data-date="{{ $houseSaleRecord->sale_date->toDateString() }}"
                                                                    data-house-id="{{ $houseSaleRecord->house_id }}"
                                                                    data-birds-sold="{{ $houseSaleRecord->birds_sold }}"
                                                                    data-total-weight="{{ (float) $houseSaleRecord->total_weight }}"
                                                                    data-price-per-kg="{{ (float) $houseSaleRecord->price_per_kg }}"
                                                                    data-note="{{ $houseSaleRecord->note }}"
                                                                    class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-955 hover:underline transition-colors">
                                                                แก้ไข
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                                            ยังจับไม่ครบ
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($flock->status === 'active')
                <div id="sale_form_panel" class="mb-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300">
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            บันทึกข้อมูลจับขาย
                        </h2>
                    </div>
                    <form method="POST" action="{{ route('flocks.sale-records.store', $flock) }}">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        <input type="hidden" name="redirect_to" value="close">
                        @include('sale-records._form')
                    </form>
                </div>
            @endif

            <!-- ประวัติบันทึกการจับไก่ขาย -->
            @if ($saleRecords->isNotEmpty())
                <div class="mt-8 mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-width="2" />
                        </svg>
                        <h2 class="text-base font-bold text-slate-900">ประวัติบันทึกการจับไก่ขายล่าสุดประจำรุ่น</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">วันที่จับ</th>
                                    <th class="px-4 py-3 font-semibold">เล้า</th>
                                    <th class="px-4 py-3 text-right font-semibold">จำนวนไก่จับขาย</th>
                                    <th class="px-4 py-3 text-right font-semibold">น้ำหนักรวม (กก.)</th>
                                    <th class="px-4 py-3 text-right font-semibold">เฉลี่ย (กก./ตัว)</th>
                                    <th class="px-4 py-3 text-right font-semibold">ราคาต่อ กก. (บาท)</th>
                                    <th class="px-4 py-3 text-right font-semibold">ยอดเงินรวม (บาท)</th>
                                    @if ($flock->status === 'active')
                                        <th class="px-4 py-3 text-center font-semibold">การกระทำ</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($saleRecords as $record)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="whitespace-nowrap px-4 py-3 text-slate-900">
                                            {{ thai_date($record->sale_date) }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">
                                            @if ($record->house->house_name && $record->house->house_name !== 'เล้า '.$record->house->house_no && $record->house->house_name !== $record->house->house_no)
                                                เล้า {{ $record->house->house_no }} - {{ $record->house->house_name }}
                                            @else
                                                เล้า {{ $record->house->house_no }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">
                                            {{ number_format($record->birds_sold) }} ตัว
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">
                                            {{ number_format($record->total_weight, 2) }} กก.
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">
                                            {{ number_format($record->avg_weight, 3) }} กก.
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">
                                            {{ number_format($record->price_per_kg, 2) }} บาท
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">
                                            {{ number_format($record->total_amount, 2) }} บาท
                                        </td>
                                        @if ($flock->status === 'active')
                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                <div class="inline-flex items-center gap-3 justify-center">
                                                    <form method="POST" action="{{ route('flocks.sale-records.destroy', [$flock, $record]) }}" onsubmit="return confirm('ยืนยันลบบันทึกจับไก่ขายรายการนี้หรือไม่?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="redirect_to" value="close">
                                                        <button type="submit" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-900 transition-colors">
                                                            ลบ
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                @if ($flock->status === 'closed')
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
                            รุ่นการเลี้ยงนี้ปิดแล้ว 
                            @if(auth()->user()->isSuperAdmin())
                                (ผู้ดูแลระบบสามารถปลดล็อกเพื่อแก้ไขข้อมูลได้)
                            @else
                                (กรุณาติดต่อผู้ดูแลระบบ (Admin) เพื่อปลดล็อกหากต้องการแก้ไขข้อมูล)
                            @endif
                        </span>
                        @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('flocks.close.unlock', $flock) }}" onsubmit="return confirm('ยืนยันปลดล็อกรุ่นการเลี้ยงนี้เพื่อแก้ไขข้อมูลหรือไม่?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors">
                                    ปลดล็อกรุ่นการเลี้ยง
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('flocks.close.store', $flock) }}" onsubmit="return confirm('ยืนยันปิดรุ่นการเลี้ยงนี้หรือไม่?')">
                        @csrf
                        <button
                            type="submit"
                            @disabled($summary['unbalanced_houses'] > 0)
                            class="inline-flex items-center justify-center rounded-md px-5 py-2 text-sm font-medium shadow-sm {{ $summary['unbalanced_houses'] > 0 ? 'cursor-not-allowed bg-slate-200 text-slate-500' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                        >
                            ปิดรุ่น
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');

            if (farmSelector && flockSelector) {
                const flockOptions = @json($closeSelectorFlocks);

                const showOptionsForFarm = (farmId) => {
                    let firstVisible = null;

                    flockSelector.innerHTML = '';

                    flockOptions
                        .filter((option) => option.farmId === farmId)
                        .forEach((optionData) => {
                            const option = new Option(optionData.text.trim(), optionData.value);
                            option.dataset.farmId = optionData.farmId;
                            option.dataset.closeUrl = optionData.closeUrl;
                            option.selected = optionData.selected;
                            flockSelector.add(option);

                            if (option.selected || firstVisible === null) {
                                firstVisible = option;
                            }
                        });

                    if (firstVisible) {
                        flockSelector.value = firstVisible.value;
                    } else {
                        const option = new Option('ไม่มีรุ่นในฟาร์มนี้', '');
                        option.disabled = true;
                        option.selected = true;
                        flockSelector.add(option);
                    }

                    return firstVisible;
                };

                const redirectToClosePage = (option) => {
                    if (option?.dataset.closeUrl) {
                        window.location.href = option.dataset.closeUrl;
                    }
                };

                showOptionsForFarm(farmSelector.value);

                farmSelector.addEventListener('change', () => {
                    redirectToClosePage(showOptionsForFarm(farmSelector.value));
                });

                flockSelector.addEventListener('change', () => {
                    redirectToClosePage(flockSelector.selectedOptions[0]);
                });
            }

            // Dynamic catch age calculation
            document.querySelectorAll('.house-date-input').forEach(input => {
                input.addEventListener('change', function() {
                    const houseId = this.dataset.houseId;
                    const placementDateStr = this.dataset.placementDate;
                    const ageSpan = document.getElementById('age_house_' + houseId);
                    
                    if (!ageSpan) return;
                    
                    if (this.value && placementDateStr) {
                        const catchDate = new Date(this.value);
                        const placementDate = new Date(placementDateStr);
                        
                        // Set hours to 12 to normalize and avoid DST shift issues
                        catchDate.setHours(12, 0, 0, 0);
                        placementDate.setHours(12, 0, 0, 0);
                        
                        const diffTime = catchDate.getTime() - placementDate.getTime();
                        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                        
                        ageSpan.textContent = diffDays >= 0 ? diffDays + ' วัน' : '0 วัน';
                    } else {
                        ageSpan.textContent = '-';
                    }
                });
            });
        });

        window.selectHouse = (houseId, remaining, avgWeight, estWeight) => {
            const houseInput = document.getElementById('house_id');
            const birdsInput = document.getElementById('birds_sold');
            const weightInput = document.getElementById('total_weight');
            const priceInput = document.getElementById('price_per_kg');
            const dateInput = document.getElementById('date_house_' + houseId);
            const mainDateInput = document.getElementById('sale_date');
            const form = document.querySelector('#sale_form_panel form');
            
            if (remaining <= 0) {
                alert('ไม่สามารถบันทึกได้ เนื่องจากไม่มีไก่เหลืออยู่ในเล้านี้แล้ว');
                return;
            }
            
            if (priceInput && !priceInput.value) {
                alert('กรุณากรอกราคาขายต่อ กก. ก่อนทำการยืนยัน');
                priceInput.focus();
                return;
            }
            
            if (houseInput) {
                houseInput.value = houseId;
                
                if (birdsInput) {
                    birdsInput.value = remaining;
                }
                
                if (dateInput && mainDateInput) {
                    mainDateInput.value = dateInput.value;
                }
                
                if (estWeight <= 0) {
                    if (weightInput) {
                        weightInput.value = '';
                        weightInput.focus();
                    }
                    // Trigger change event so the form updates
                    houseInput.dispatchEvent(new Event('change'));
                    alert('ไม่สามารถบันทึกอัตโนมัติได้เนื่องจากยังไม่มีข้อมูลน้ำหนักเฉลี่ยล่าสุด กรุณากรอกน้ำหนักรวมด้วยตนเองในฟอร์มด้านล่าง');
                    
                    const formPanel = document.getElementById('sale_form_panel');
                    if (formPanel) {
                        formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                }
                
                if (weightInput) {
                    weightInput.value = Number(estWeight).toFixed(2);
                }
                
                // Trigger change event to sync the context panel and trigger calculations
                houseInput.dispatchEvent(new Event('change'));
                
                const houseRow = document.getElementById('row_house_' + houseId);
                const houseName = houseRow ? houseRow.querySelector('td').textContent.trim() : 'เล้า';
                const selectedDate = dateInput ? dateInput.value : '';
                
                // Format date to local standard just for nicer visual display in confirmation
                let formattedDate = selectedDate;
                try {
                    const dateParts = selectedDate.split('-');
                    if (dateParts.length === 3) {
                        formattedDate = `${dateParts[2]}/${dateParts[1]}/${Number(dateParts[0]) + 543}`;
                    }
                } catch (e) {}
                
                // Confirm with the user before saving
                if (confirm(`ยืนยันการบันทึกข้อมูลจับไก่ขายสำหรับ ${houseName}?\n- วันที่จับ: ${formattedDate}\n- จำนวนไก่: ${new Intl.NumberFormat('th-TH').format(remaining)} ตัว\n- น้ำหนักรวมคาดการณ์: ${new Intl.NumberFormat('th-TH').format(estWeight)} กก.`)) {
                    if (form) {
                        form.submit();
                    }
                }
            }
        };

        window.editRecord = (btn) => {
            const recordId = btn.dataset.id;
            const date = btn.dataset.date;
            const houseId = btn.dataset.houseId;
            const birds = btn.dataset.birdsSold;
            const weight = btn.dataset.totalWeight;
            const price = btn.dataset.pricePerKg;
            const note = btn.dataset.note;

            // 1. Change form title/header
            const header = document.querySelector('#sale_form_panel h2') || document.querySelector('#sale_form_panel .text-base');
            if (header) {
                header.innerHTML = `
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    แก้ไขข้อมูลจับไก่ขาย
                `;
            }

            // 2. Update form action and method
            const form = document.querySelector('#sale_form_panel form');
            if (form) {
                let updateUrl = "{{ route('flocks.sale-records.update', [$flock, 'RECORD_ID']) }}";
                form.action = updateUrl.replace('RECORD_ID', recordId);
                
                let methodInput = document.getElementById('form_method');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.id = 'form_method';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
            }

            // 3. Populate inputs
            document.getElementById('sale_date').value = date;
            
            const houseInput = document.getElementById('house_id');
            houseInput.value = houseId;
            
            document.getElementById('birds_sold').value = birds;
            document.getElementById('total_weight').value = weight;
            document.getElementById('price_per_kg').value = price;
            document.getElementById('note').value = note || '';

            // 4. Show a reset button next to Save button to cancel editing
            let cancelEditBtn = document.getElementById('cancel_edit_btn');
            if (!cancelEditBtn) {
                cancelEditBtn = document.createElement('button');
                cancelEditBtn.type = 'button';
                cancelEditBtn.id = 'cancel_edit_btn';
                cancelEditBtn.className = 'inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors';
                cancelEditBtn.textContent = 'ยกเลิกการแก้ไข';
                cancelEditBtn.onclick = resetFormToCreate;
                
                const btnContainer = document.querySelector('#sale_form_panel form .mt-6');
                if (btnContainer) {
                    const saveBtn = btnContainer.querySelector('button[type="submit"]') || btnContainer.lastElementChild;
                    btnContainer.insertBefore(cancelEditBtn, saveBtn);
                }
            }

            // 5. Hide the default cancel link
            const defaultCancelLink = document.querySelector('#sale_form_panel form .mt-6 a');
            if (defaultCancelLink) {
                defaultCancelLink.style.display = 'none';
            }

            // 6. Trigger changes to fill previews
            houseInput.dispatchEvent(new Event('change'));

            // 7. Scroll form into view and add golden highlights
            const formPanel = document.getElementById('sale_form_panel');
            if (formPanel) {
                formPanel.classList.add('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
                formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        };

        window.resetFormToCreate = () => {
            // 1. Reset title
            const header = document.querySelector('#sale_form_panel h2') || document.querySelector('#sale_form_panel .text-base');
            if (header) {
                header.innerHTML = `
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    บันทึกข้อมูลจับขาย
                `;
            }

            // 2. Reset form action and method
            const form = document.querySelector('#sale_form_panel form');
            if (form) {
                form.action = "{{ route('flocks.sale-records.store', $flock) }}";
                const methodInput = document.getElementById('form_method');
                if (methodInput) {
                    methodInput.value = 'POST';
                }
            }

            // 3. Clear inputs
            document.getElementById('sale_date').value = "{{ $saleDate }}";
            
            const houseInput = document.getElementById('house_id');
            houseInput.value = '';
            
            document.getElementById('birds_sold').value = '';
            document.getElementById('total_weight').value = '';
            document.getElementById('price_per_kg').value = "{{ $saleRecord->price_per_kg }}";
            document.getElementById('note').value = '';

            // 4. Remove cancel edit button
            const cancelEditBtn = document.getElementById('cancel_edit_btn');
            if (cancelEditBtn) {
                cancelEditBtn.remove();
            }

            // 5. Restore default cancel link
            const defaultCancelLink = document.querySelector('#sale_form_panel form .mt-6 a');
            if (defaultCancelLink) {
                defaultCancelLink.style.display = 'inline-flex';
            }

            // 6. Trigger change and remove highlights
            houseInput.dispatchEvent(new Event('change'));
            const formPanel = document.getElementById('sale_form_panel');
            if (formPanel) {
                formPanel.classList.remove('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
            }
        };
    </script>
</x-app-layout>
