<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">เพิ่มบันทึกรับอาหาร</h1>
                    <p class="mt-0.5 text-sm text-slate-500">เลือกรุ่นการเลี้ยง กรอกข้อมูลเบอร์อาหาร และระบุปริมาณที่ลงในแต่ละเล้า</p>
                </div>
            </div>
            <a href="{{ route('feed-receipts.index') }}"
               class="group inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm ring-1 ring-transparent transition-all duration-200 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800 hover:shadow hover:ring-slate-200">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1500px] px-3 sm:px-4 lg:px-6">

            {{-- ─── Validation Errors ─── --}}
            @if ($errors->any())
                <div class="mb-6 animate-[fadeIn_0.3s_ease-out] rounded-2xl border border-red-200/80 bg-gradient-to-r from-red-50 to-rose-50 p-4 text-sm text-red-800 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100">
                            <svg class="h-4.5 w-4.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-red-900">เกิดข้อผิดพลาดในการบันทึกข้อมูล</p>
                            <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════════════════ --}}
            {{-- SECTION 1 — ข้อมูลอ้างอิงหลัก (Primary Reference Selection)       --}}
            {{-- ═══════════════════════════════════════════════════════════════════ --}}
            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                {{-- Card header --}}
                <div class="relative overflow-hidden border-b border-slate-100 px-6 py-4">
                    {{-- Background decoration --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-50/70 via-white to-teal-50/40"></div>
                    <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-emerald-100/30 blur-2xl"></div>
                    <div class="absolute -left-4 -bottom-4 h-20 w-20 rounded-full bg-teal-100/20 blur-xl"></div>

                    <div class="relative flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md shadow-emerald-500/20">
                            <svg class="h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">ระบุข้อมูลอ้างอิง</h2>
                            <p class="text-xs text-slate-500">เลือกฟาร์ม รุ่นการเลี้ยง และวันที่รับอาหาร</p>
                        </div>
                    </div>
                </div>

                {{-- Card body: filters --}}
                <form id="receipt-filter-form" method="GET" action="{{ route('feed-receipts.create') }}" class="p-6">
                    <div class="grid gap-x-5 gap-y-4 md:grid-cols-2 xl:grid-cols-[1fr_1.2fr_200px_auto] xl:items-end">
                        {{-- ฟาร์ม --}}
                        <div>
                            <label for="farm_filter" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">ฟาร์ม</label>
                            <select id="farm_filter" name="farm_id"
                                    class="block w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 pl-4 pr-10 text-sm font-medium text-slate-700 shadow-sm transition-all duration-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-400/30">
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected($selectedFarm?->id === $farm->id)>{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- รุ่นการเลี้ยง --}}
                        <div>
                            <label for="flock_filter" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">รุ่นการเลี้ยง</label>
                            <select id="flock_filter" name="flock_id"
                                    class="block w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 pl-4 pr-10 text-sm font-medium text-slate-700 shadow-sm transition-all duration-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-400/30">
                                <option value="">เลือกรุ่น active ล่าสุดของฟาร์ม</option>
                                @foreach ($flockOptions as $flockOption)
                                    <option value="{{ $flockOption->id }}"
                                            data-farm-id="{{ $flockOption->farm_id }}"
                                            @selected((int) request('flock_id') === (int) $flockOption->id || (!request('flock_id') && $activeFlock && (int) $activeFlock->id === (int) $flockOption->id))
                                    >
                                        {{ $flockOption->flock_code }} @if ($flockOption->farm) — {{ $flockOption->farm->farm_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- วันที่ --}}
                        <div>
                            <label for="receipt_date_filter" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">วันที่รับ</label>
                            <x-text-input id="receipt_date_filter" name="receipt_date" type="date"
                                          class="block w-full rounded-xl !border-slate-200 !bg-slate-50/50 !py-2.5 !shadow-sm transition-all duration-200 focus:!border-emerald-400 focus:!bg-white focus:!ring-2 focus:!ring-emerald-400/30"
                                          :value="$receiptDate" />
                        </div>

                        {{-- ปุ่มโหลดข้อมูล --}}
                        <button type="submit"
                                class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition-all duration-200 hover:-translate-y-0.5 hover:from-emerald-500 hover:to-teal-500 hover:shadow-xl hover:shadow-emerald-600/30 active:translate-y-0">
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            โหลดข้อมูลใหม่
                        </button>
                    </div>

                    {{-- Date Explanation Banner --}}
                    @if ($dateExplanation)
                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-sky-100 bg-gradient-to-r from-sky-50/80 to-indigo-50/40 p-3.5 text-xs leading-relaxed text-sky-800 shadow-sm">
                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>
                            <span>{{ $dateExplanation }}</span>
                        </div>
                    @endif
                </form>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════ --}}
            {{-- SECTION 2 — ฟอร์มหลัก (Main Form) - 2 Column Layout               --}}
            {{-- ═══════════════════════════════════════════════════════════════════ --}}
            <form method="POST" action="{{ route('feed-receipts.store') }}" class="grid gap-6 lg:grid-cols-[1fr_340px] xl:grid-cols-[1fr_380px] lg:items-start">
                @csrf
                <input type="hidden" name="farm_id" value="{{ $selectedFarm?->id }}">
                <input type="hidden" name="flock_id" value="{{ $activeFlock?->id }}">
                <input type="hidden" name="receipt_date" value="{{ old('receipt_date', $receiptDate) }}">

                {{-- ── LEFT COLUMN ── --}}
                <div class="flex flex-col gap-6">

                    {{-- Card: รายละเอียดรับอาหาร --}}
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50/80 to-white px-6 py-4">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                    </svg>
                                </div>
                                ข้อมูลรายละเอียดรับอาหาร
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="feed_code" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">เบอร์อาหาร</label>
                                    <select id="feed_code" name="feed_code"
                                            class="block w-full rounded-xl border-slate-200 bg-slate-50/50 py-2.5 pl-4 pr-10 text-sm font-medium text-slate-700 shadow-sm transition-all duration-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-400/30">
                                        @foreach (['203', '204', '205'] as $feedCode)
                                            <option value="{{ $feedCode }}" @selected(old('feed_code') === $feedCode)>{{ $feedCode }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('feed_code')" />
                                </div>
                                <div>
                                    <label for="production_lot" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">เลขที่ผลิต (LOT NO.)</label>
                                    <x-text-input id="production_lot" name="production_lot" type="text"
                                                  class="block w-full rounded-xl !border-slate-200 !bg-slate-50/50 !py-2.5 !shadow-sm transition-all duration-200 focus:!border-emerald-400 focus:!bg-white focus:!ring-2 focus:!ring-emerald-400/30"
                                                  :value="old('production_lot')" placeholder="เช่น 260709-01" />
                                    <x-input-error class="mt-2" :messages="$errors->get('production_lot')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card: ตารางปริมาณอาหารลงแต่ละเล้า --}}
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50/80 to-white px-6 py-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2.5">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                            </svg>
                                        </div>
                                        ปริมาณอาหารที่ลงแต่ละเล้า
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500 pl-8">กรอกปริมาณอาหารลงเล้าในหน่วย <strong>กิโลกรัม</strong> — ข้ามเล้าที่ไม่ได้รับในเที่ยวนี้</p>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('items')" />
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm" id="feed-table">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/60">
                                        <th class="w-10 py-3 pl-6 pr-2 text-left font-bold text-slate-400 text-[10px] uppercase tracking-widest">#</th>
                                        <th class="py-3 px-3 text-left font-bold text-slate-400 text-[10px] uppercase tracking-widest">เล้า</th>
                                        <th class="w-28 py-3 px-3 text-center font-bold text-slate-400 text-[10px] uppercase tracking-widest">อายุไก่</th>
                                        <th class="w-56 py-3 pl-3 pr-6 text-right font-bold text-slate-400 text-[10px] uppercase tracking-widest">ปริมาณอาหารรับ (กก.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100/80" id="feed-tbody">
                                    @forelse ($houses as $index => $house)
                                        @php $field = 'items.'.$house->id.'.quantity_kg'; @endphp
                                        <tr class="group relative transition-all duration-200 hover:bg-emerald-50/30" data-house-row>
                                            {{-- Row number --}}
                                            <td class="py-3.5 pl-6 pr-2 text-xs font-bold text-slate-300 group-hover:text-emerald-400 transition-colors">
                                                {{ $index + 1 }}
                                            </td>
                                            {{-- House name --}}
                                            <td class="whitespace-nowrap py-3.5 px-3">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-400 transition-all duration-200 group-hover:bg-emerald-100 group-hover:text-emerald-600 group-hover:shadow-sm">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12M21 8.25H15" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-semibold text-slate-700 group-hover:text-slate-900 transition-colors">เล้า {{ $house->house_no }}</span>
                                                </div>
                                            </td>
                                            {{-- Chicken age badge --}}
                                            <td class="whitespace-nowrap py-3.5 px-3 text-center">
                                                @if ($ageByHouse->get($house->id) !== null)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 transition-all duration-200 group-hover:bg-emerald-100 group-hover:text-emerald-700">
                                                        <svg class="h-3 w-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        {{ number_format($ageByHouse->get($house->id)) }} วัน
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-3 py-1 text-xs font-medium text-slate-400">—</span>
                                                @endif
                                            </td>
                                            {{-- Quantity input --}}
                                            <td class="py-3.5 pl-3 pr-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <input data-feed-quantity
                                                           type="number"
                                                           step="0.01"
                                                           min="0"
                                                           name="items[{{ $house->id }}][quantity_kg]"
                                                           value="{{ old($field) }}"
                                                           class="w-44 rounded-xl border-slate-200 bg-slate-50/50 py-2 text-right text-sm font-semibold text-slate-700 shadow-sm placeholder-slate-300 transition-all duration-200 focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-400/30 focus:shadow-md"
                                                           placeholder="0.00">
                                                    <span class="text-[11px] font-medium text-slate-400 w-6">กก.</span>
                                                </div>
                                                <x-input-error class="mt-1 text-right" :messages="$errors->get($field)" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-16 text-center">
                                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                                                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12M21 8.25H15" />
                                                    </svg>
                                                </div>
                                                <p class="mt-3 text-sm font-medium text-slate-500">ยังไม่มีเล้าเปิดใช้งานในฟาร์มนี้</p>
                                                <p class="mt-1 text-xs text-slate-400">กรุณาตรวจสอบสถานะเล้าในฟาร์มที่เลือก</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT COLUMN — Sticky Summary ── --}}
                <div class="lg:sticky lg:top-6 flex flex-col gap-5">

                    {{-- Summary Card --}}
                    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-shadow duration-300 hover:shadow-md">
                        {{-- Header --}}
                        <div class="relative overflow-hidden border-b border-slate-100 px-5 py-4">
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 to-slate-50/50"></div>
                            <h3 class="relative text-sm font-bold text-slate-800 flex items-center gap-2.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-violet-100 text-violet-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                </div>
                                สรุปข้อมูลการลงรับ
                            </h3>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 space-y-5">
                            {{-- Detail rows --}}
                            <div class="space-y-0 divide-y divide-slate-100">
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-xs font-medium text-slate-500 flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12M21 8.25H15" /></svg>
                                        ฟาร์ม
                                    </span>
                                    <span class="text-sm font-bold text-slate-800">{{ $selectedFarm?->farm_name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-xs font-medium text-slate-500 flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                        รุ่นการเลี้ยง
                                    </span>
                                    @if ($activeFlock)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-amber-50 to-orange-50 px-2.5 py-1 text-xs font-bold text-amber-800 border border-amber-200/80 shadow-sm">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ $activeFlock->flock_code }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 border border-red-200/80">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            ไม่มีรุ่นกำลังเลี้ยง
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-xs font-medium text-slate-500 flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                        วันที่รับ
                                    </span>
                                    <span class="text-sm font-bold text-slate-800">{{ thai_date($receiptDate) }}</span>
                                </div>
                            </div>

                            {{-- Live calculation widgets --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-br from-slate-50 to-slate-100/50 p-4 text-center transition-all duration-300" id="widget-houses">
                                    <div class="absolute inset-0 opacity-0 bg-gradient-to-br from-emerald-50 to-teal-50 transition-opacity duration-500" id="widget-houses-glow"></div>
                                    <span class="relative text-[10px] font-bold uppercase tracking-widest text-slate-400 block">เล้าที่กรอก</span>
                                    <div class="relative mt-2 flex items-baseline justify-center gap-1">
                                        <span id="filled-house-count" class="text-2xl font-black text-slate-800 tabular-nums transition-all duration-300">0</span>
                                        <span class="text-sm font-medium text-slate-400">/ {{ count($houses) }}</span>
                                    </div>
                                </div>
                                <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50/60 p-4 text-center" id="widget-weight">
                                    <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-200/20 blur-xl"></div>
                                    <span class="relative text-[10px] font-bold uppercase tracking-widest text-emerald-600 block">น้ำหนักรวม</span>
                                    <div class="relative mt-2 flex items-baseline justify-center gap-1">
                                        <span id="feed-total-kg" class="text-2xl font-black text-emerald-800 tabular-nums transition-all duration-300">0.00</span>
                                        <span class="text-xs font-bold text-emerald-500">กก.</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress indicator --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ความคืบหน้า</span>
                                    <span id="progress-pct" class="text-[10px] font-bold text-emerald-600 tabular-nums">0%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                    <div id="progress-bar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500 ease-out shadow-sm" style="width: 0%"></div>
                                </div>
                            </div>

                            {{-- Submit button --}}
                            <button type="submit" @disabled($houses->isEmpty())
                                    class="group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-emerald-600/30 active:translate-y-0 disabled:opacity-50 disabled:shadow-none disabled:translate-y-0 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                <span class="relative inline-flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    บันทึกรับอาหาร
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ JavaScript ═══ --}}
    <script>
        // ── Filter auto-submit ──
        const filterForm = document.getElementById('receipt-filter-form');
        const farmFilter = document.getElementById('farm_filter');
        const flockFilter = document.getElementById('flock_filter');
        const dateFilter = document.getElementById('receipt_date_filter');

        function syncFlockOptions() {
            const farmId = farmFilter?.value;
            if (!flockFilter) return;
            [...flockFilter.options].forEach((opt) => {
                if (!opt.value) { opt.hidden = false; return; }
                opt.hidden = opt.dataset.farmId !== farmId;
            });
            if (flockFilter.selectedOptions[0]?.hidden) {
                const first = [...flockFilter.options].find((o) => !o.hidden);
                if (first) first.selected = true;
            }
        }

        farmFilter?.addEventListener('change', () => { syncFlockOptions(); filterForm?.submit(); });
        flockFilter?.addEventListener('change', () => filterForm?.submit());
        dateFilter?.addEventListener('change', () => filterForm?.submit());
        syncFlockOptions();

        // ── Real-time calculations ──
        const qtyInputs = document.querySelectorAll('[data-feed-quantity]');
        const elTotal   = document.getElementById('feed-total-kg');
        const elCount   = document.getElementById('filled-house-count');
        const elPct     = document.getElementById('progress-pct');
        const elBar     = document.getElementById('progress-bar');
        const elGlow    = document.getElementById('widget-houses-glow');
        const totalHouses = {{ count($houses) }};

        const refresh = () => {
            let total = 0, filled = 0;

            qtyInputs.forEach((input) => {
                const val = Number(input.value || 0);
                const row = input.closest('tr');

                if (val > 0) {
                    total += val;
                    filled++;
                    row?.classList.add('!bg-emerald-50/40');
                    input.classList.add('!border-emerald-400', '!bg-emerald-50/30', '!text-emerald-800');
                } else {
                    row?.classList.remove('!bg-emerald-50/40');
                    input.classList.remove('!border-emerald-400', '!bg-emerald-50/30', '!text-emerald-800');
                }
            });

            elTotal.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            elCount.textContent = filled;

            const pct = totalHouses > 0 ? Math.round((filled / totalHouses) * 100) : 0;
            elPct.textContent = pct + '%';
            elBar.style.width = pct + '%';

            // Glow effect when houses are being filled
            if (elGlow) {
                elGlow.style.opacity = filled > 0 ? '1' : '0';
            }
        };

        qtyInputs.forEach((input) => input.addEventListener('input', refresh));
        refresh();
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-app-layout>
