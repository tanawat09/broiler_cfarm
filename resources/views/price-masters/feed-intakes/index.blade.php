<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between py-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">STD CFARM Target Guide ( Ross 308 )</h1>
                </div>
                <p class="mt-1.5 text-sm text-slate-500 font-medium">
                    ตารางเปรียบเทียบมาตรฐานการเจริญเติบโต การกินอาหาร อัตรา FCR อัตราตาย และดัชนีประสิทธิภาพรายวัน (อายุ 0-56 วัน)
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    กลับหน้าหลัก
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full max-w-[98%] px-2 sm:px-4">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50/50 p-4 text-sm text-red-800 shadow-sm backdrop-blur">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <svg class="h-5 w-5 text-red-650" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        พบข้อผิดพลาดในการบันทึกข้อมูล:
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-12 items-start">
                
                <!-- คอลัมน์ซ้าย: ฟอร์มจัดการเกณฑ์มาตรฐาน (Sticky) -->
                @if (auth()->user()->isSuperAdmin())
                    <div class="xl:col-span-3 xl:sticky xl:top-24">
                        <div id="feed_intake_form_panel" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300">
                            <div class="mb-4 border-b border-slate-100 pb-3">
                                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>บันทึกมาตรฐาน (Target Guide)</span>
                                </h2>
                                <p class="text-xs text-slate-500 mt-1">กรอกข้อมูลมาตรฐาน Ross 308 รายวัน</p>
                            </div>

                            <form method="POST" action="{{ route('feed-intake-masters.store') }}" class="space-y-4 text-xs">
                                @csrf
                                <input type="hidden" id="form_method" name="_method" value="POST">

                                <div>
                                    <x-input-label for="age" value="อายุสะสม (วัน)" class="font-semibold text-slate-700 text-xs" />
                                    <x-text-input id="age" name="age" type="number" min="0" max="100" class="block w-full mt-1 text-right font-semibold text-xs" :value="old('age')" required />
                                </div>

                                <!-- Daily Feed Intake -->
                                <div class="bg-teal-50/50 rounded-xl p-3 border border-teal-100 space-y-2">
                                    <div class="font-bold text-teal-800 uppercase tracking-wider">Daily Feed Intake (กรัม)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="feed_ah" value="AH" class="text-teal-700" />
                                            <x-text-input id="feed_ah" name="feed_ah" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('feed_ah')" required />
                                        </div>
                                        <div>
                                            <x-input-label for="feed_male" value="Male" class="text-teal-700" />
                                            <x-text-input id="feed_male" name="feed_male" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('feed_male')" required />
                                        </div>
                                        <div>
                                            <x-input-label for="feed_female" value="Female" class="text-teal-700" />
                                            <x-text-input id="feed_female" name="feed_female" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('feed_female')" required />
                                        </div>
                                    </div>
                                </div>

                                <!-- Cumulative Feed Intake -->
                                <div class="bg-purple-50/50 rounded-xl p-3 border border-purple-100 space-y-2">
                                    <div class="font-bold text-purple-800 uppercase tracking-wider">Acc. Feed Intake / Feed Intake (กรัม)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="cum_feed_ah" value="AH" class="text-purple-700" />
                                            <x-text-input id="cum_feed_ah" name="cum_feed_ah" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('cum_feed_ah')" />
                                        </div>
                                        <div>
                                            <x-input-label for="cum_feed_male" value="Male" class="text-purple-700" />
                                            <x-text-input id="cum_feed_male" name="cum_feed_male" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('cum_feed_male')" />
                                        </div>
                                        <div>
                                            <x-input-label for="cum_feed_female" value="Female" class="text-purple-700" />
                                            <x-text-input id="cum_feed_female" name="cum_feed_female" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('cum_feed_female')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Body Weight -->
                                <div class="bg-emerald-50/50 rounded-xl p-3 border border-emerald-100 space-y-2">
                                    <div class="font-bold text-emerald-800 uppercase tracking-wider">Body Weight (กรัม)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="weight_ah" value="AH" class="text-emerald-700" />
                                            <x-text-input id="weight_ah" name="weight_ah" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('weight_ah')" />
                                        </div>
                                        <div>
                                            <x-input-label for="weight_male" value="Male" class="text-emerald-700" />
                                            <x-text-input id="weight_male" name="weight_male" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('weight_male')" />
                                        </div>
                                        <div>
                                            <x-input-label for="weight_female" value="Female" class="text-emerald-700" />
                                            <x-text-input id="weight_female" name="weight_female" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('weight_female')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Acc Mortality -->
                                <div class="bg-rose-50/50 rounded-xl p-3 border border-rose-100 space-y-2">
                                    <div class="font-bold text-rose-800 uppercase tracking-wider">Acc. Mortality (%)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="mortality_ah" value="AH" class="text-rose-700" />
                                            <x-text-input id="mortality_ah" name="mortality_ah" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('mortality_ah')" />
                                        </div>
                                        <div>
                                            <x-input-label for="mortality_male" value="Male" class="text-rose-700" />
                                            <x-text-input id="mortality_male" name="mortality_male" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('mortality_male')" />
                                        </div>
                                        <div>
                                            <x-input-label for="mortality_female" value="Female" class="text-rose-700" />
                                            <x-text-input id="mortality_female" name="mortality_female" type="number" min="0" step="0.01" class="block w-full text-right mt-0.5 text-xs" :value="old('mortality_female')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- FCR -->
                                <div class="bg-amber-50/50 rounded-xl p-3 border border-amber-100 space-y-2">
                                    <div class="font-bold text-amber-800 uppercase tracking-wider">Feed Conversion Rate (FCR)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="fcr_ah" value="AH" class="text-amber-700" />
                                            <x-text-input id="fcr_ah" name="fcr_ah" type="number" min="0" step="0.001" class="block w-full text-right mt-0.5 text-xs" :value="old('fcr_ah')" />
                                        </div>
                                        <div>
                                            <x-input-label for="fcr_male" value="Male" class="text-amber-700" />
                                            <x-text-input id="fcr_male" name="fcr_male" type="number" min="0" step="0.001" class="block w-full text-right mt-0.5 text-xs" :value="old('fcr_male')" />
                                        </div>
                                        <div>
                                            <x-input-label for="fcr_female" value="Female" class="text-amber-700" />
                                            <x-text-input id="fcr_female" name="fcr_female" type="number" min="0" step="0.001" class="block w-full text-right mt-0.5 text-xs" :value="old('fcr_female')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Performance Index -->
                                <div class="bg-indigo-50/50 rounded-xl p-3 border border-indigo-100 space-y-2">
                                    <div class="font-bold text-indigo-800 uppercase tracking-wider">Performance Index</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div>
                                            <x-input-label for="pi_ah" value="AH" class="text-indigo-700" />
                                            <x-text-input id="pi_ah" name="pi_ah" type="number" min="0" class="block w-full text-right mt-0.5 text-xs" :value="old('pi_ah')" />
                                        </div>
                                        <div>
                                            <x-input-label for="pi_male" value="Male" class="text-indigo-700" />
                                            <x-text-input id="pi_male" name="pi_male" type="number" min="0" class="block w-full text-right mt-0.5 text-xs" :value="old('pi_male')" />
                                        </div>
                                        <div>
                                            <x-input-label for="pi_female" value="Female" class="text-indigo-700" />
                                            <x-text-input id="pi_female" name="pi_female" type="number" min="0" class="block w-full text-right mt-0.5 text-xs" :value="old('pi_female')" />
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 flex flex-col gap-2">
                                    <button type="submit" id="submit_btn" class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 hover:shadow transition-all duration-150">
                                        บันทึกเกณฑ์มาตรฐาน
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- คอลัมน์ขวา: ตารางฐานข้อมูลการกินอาหาร (Unified Scrollable Table) -->
                <div class="{{ auth()->user()->isSuperAdmin() ? 'xl:col-span-9' : 'xl:col-span-12' }}">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        
                        <!-- เครื่องมือค้นหาและฟิลเตอร์ด่วน (Premium Filters Panel) -->
                        <div class="border-b border-slate-100 bg-slate-50/50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">ตารางอ้างอิงมาตรฐาน (STD CFARM Target Guide)</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">ค้นหาข้อมูลตามช่วงอายุ หรือกรองด่วนตามสัปดาห์</p>
                                </div>
                                <div class="relative w-full sm:w-48">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="doc_search" onkeyup="filterBySearch()" placeholder="ค้นหาอายุ..." class="block w-full rounded-lg border-slate-300 pl-9 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>

                            <!-- แท็บสัปดาห์ (Quick Javascript Filters) -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" onclick="filterByWeek('all', this)" class="week-filter-btn rounded-lg bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition-all">
                                    ทั้งหมด
                                </button>
                                <button type="button" onclick="filterByWeek('w1-2', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 1 - 2 (วัน 0-14)
                                </button>
                                <button type="button" onclick="filterByWeek('w3-4', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 3 - 4 (วัน 15-28)
                                </button>
                                <button type="button" onclick="filterByWeek('w5-6', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 5 - 6 (วัน 29-42)
                                </button>
                                <button type="button" onclick="filterByWeek('w7-8', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 7 - 8 (วัน 43-56)
                                </button>
                            </div>
                        </div>

                        <!-- ตารางข้อมูลหลัก (Scrollable body with Sticky Headers) -->
                        <div class="max-h-[700px] overflow-x-auto overflow-y-auto" style="scrollbar-width: thin;">
                            <table class="min-w-[1800px] text-left text-xs text-slate-700 border-collapse">
                                <thead class="sticky top-0 bg-slate-50 text-slate-600 shadow-sm z-10 border-b border-slate-200">
                                    <tr class="divide-x divide-slate-200">
                                        <th rowspan="2" class="px-3 py-3 font-bold text-center border-r border-slate-200 sticky left-0 bg-slate-50 z-20 min-w-[70px]">อายุ (วัน)</th>
                                        <th rowspan="2" class="px-3 py-3 font-bold text-center border-r border-slate-200 min-w-[80px]">สัปดาห์</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-sky-50 text-sky-900">Feed Intake (grams)</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-emerald-50 text-emerald-900">Body Weight (grams)</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-rose-50 text-rose-900">Acc. Mortality (%)</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-amber-50 text-amber-900">FCR</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-indigo-50 text-indigo-900">Performance Index</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-purple-50 text-purple-900">Acc. Feed Intake (grams)</th>
                                        <th colspan="3" class="px-3 py-2 font-bold text-center bg-teal-50 text-teal-900">Daily Feed Intake</th>
                                        @if (auth()->user()->isSuperAdmin())
                                            <th rowspan="2" class="px-3 py-3 text-center font-bold min-w-[120px]">การกระทำ</th>
                                        @endif
                                    </tr>
                                    <tr class="divide-x divide-slate-200 border-b border-slate-250">
                                        <!-- Feed Intake -->
                                        <th class="px-2 py-2 text-right font-semibold bg-sky-50/20 text-sky-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-sky-50/20 text-sky-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-sky-50/20 text-sky-950">Female</th>
                                        <!-- Body Weight -->
                                        <th class="px-2 py-2 text-right font-semibold bg-emerald-50/20 text-emerald-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-emerald-50/20 text-emerald-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-emerald-50/20 text-emerald-950">Female</th>
                                        <!-- Acc Mortality -->
                                        <th class="px-2 py-2 text-right font-semibold bg-rose-50/20 text-rose-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-rose-50/20 text-rose-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-rose-50/20 text-rose-950">Female</th>
                                        <!-- FCR -->
                                        <th class="px-2 py-2 text-right font-semibold bg-amber-50/20 text-amber-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-amber-50/20 text-amber-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-amber-50/20 text-amber-950">Female</th>
                                        <!-- PI -->
                                        <th class="px-2 py-2 text-right font-semibold bg-indigo-50/20 text-indigo-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-indigo-50/20 text-indigo-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-indigo-50/20 text-indigo-950">Female</th>
                                        <!-- Acc Feed Intake -->
                                        <th class="px-2 py-2 text-right font-semibold bg-purple-50/20 text-purple-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-purple-50/20 text-purple-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-purple-50/20 text-purple-950">Female</th>
                                        <!-- Daily Feed Intake -->
                                        <th class="px-2 py-2 text-right font-semibold bg-teal-50/20 text-teal-950">AH</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-teal-50/20 text-teal-950">Male</th>
                                        <th class="px-2 py-2 text-right font-semibold bg-teal-50/20 text-teal-950">Female</th>
                                    </tr>
                                </thead>
                                <tbody id="feed_intake_tbody" class="divide-y divide-slate-100 bg-white">
                                    @forelse ($records as $row)
                                        @php
                                            $week = floor($row->age / 7) + 1;
                                            $weekGroup = $week <= 2 ? 'w1-2' : ($week <= 4 ? 'w3-4' : ($week <= 6 ? 'w5-6' : 'w7-8'));
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition-colors duration-150 feed-row divide-x divide-slate-100" data-age="{{ $row->age }}" data-week-group="{{ $weekGroup }}">
                                            <td class="whitespace-nowrap px-3 py-2 font-bold text-slate-900 border-l-2 border-transparent hover:border-emerald-500 text-center sticky left-0 bg-white group-hover:bg-slate-50 z-10">
                                                {{ $row->age }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-2 text-center">
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-600">
                                                    {{ $week }} สัปดาห์
                                                </span>
                                            </td>
                                            
                                            <!-- Feed Intake (Acc. Feed) -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-sky-950 bg-sky-50/5">
                                                {{ $row->cum_feed_ah !== null ? number_format($row->cum_feed_ah) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-sky-950 bg-sky-50/5">
                                                {{ $row->cum_feed_male !== null ? number_format($row->cum_feed_male) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-sky-950 bg-sky-50/5">
                                                {{ $row->cum_feed_female !== null ? number_format($row->cum_feed_female) : '-' }}
                                            </td>

                                            <!-- Body Weight -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-emerald-950 bg-emerald-50/5">
                                                {{ $row->weight_ah !== null ? number_format($row->weight_ah) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-emerald-950 bg-emerald-50/5">
                                                {{ $row->weight_male !== null ? number_format($row->weight_male) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-emerald-950 bg-emerald-50/5">
                                                {{ $row->weight_female !== null ? number_format($row->weight_female) : '-' }}
                                            </td>

                                            <!-- Acc Mortality -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-rose-950 bg-rose-50/5">
                                                {{ $row->mortality_ah !== null ? number_format($row->mortality_ah, 2) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-rose-950 bg-rose-50/5">
                                                {{ $row->mortality_male !== null ? number_format($row->mortality_male, 2) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-rose-950 bg-rose-50/5">
                                                {{ $row->mortality_female !== null ? number_format($row->mortality_female, 2) : '-' }}
                                            </td>

                                            <!-- FCR -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-amber-950 bg-amber-50/5">
                                                {{ $row->fcr_ah !== null ? number_format($row->fcr_ah, 2) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-amber-950 bg-amber-50/5">
                                                {{ $row->fcr_male !== null ? number_format($row->fcr_male, 2) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-amber-950 bg-amber-50/5">
                                                {{ $row->fcr_female !== null ? number_format($row->fcr_female, 2) : '-' }}
                                            </td>

                                            <!-- PI -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-indigo-950 bg-indigo-50/5">
                                                {{ $row->pi_ah !== null ? number_format($row->pi_ah) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-indigo-950 bg-indigo-50/5">
                                                {{ $row->pi_male !== null ? number_format($row->pi_male) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-indigo-950 bg-indigo-50/5">
                                                {{ $row->pi_female !== null ? number_format($row->pi_female) : '-' }}
                                            </td>

                                            <!-- Acc Feed Intake (duplicate of cumulative feed intake) -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-purple-950 bg-purple-50/5">
                                                {{ $row->cum_feed_ah !== null ? number_format($row->cum_feed_ah) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-purple-950 bg-purple-50/5">
                                                {{ $row->cum_feed_male !== null ? number_format($row->cum_feed_male) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-purple-950 bg-purple-50/5">
                                                {{ $row->cum_feed_female !== null ? number_format($row->cum_feed_female) : '-' }}
                                            </td>

                                            <!-- Daily Feed Intake -->
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-teal-950 bg-teal-50/5">
                                                {{ $row->feed_ah !== null ? number_format($row->feed_ah) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-teal-950 bg-teal-50/5">
                                                {{ $row->feed_male !== null ? number_format($row->feed_male) : '-' }}
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-2 text-right text-teal-950 bg-teal-50/5">
                                                {{ $row->feed_female !== null ? number_format($row->feed_female) : '-' }}
                                            </td>

                                            @if (auth()->user()->isSuperAdmin())
                                                <td class="whitespace-nowrap px-3 py-2 text-center">
                                                    <div class="inline-flex items-center gap-2 justify-center">
                                                        <button type="button"
                                                                onclick="editRecord(this)"
                                                                data-id="{{ $row->id }}"
                                                                data-age="{{ $row->age }}"
                                                                data-feed_ah="{{ (float) $row->feed_ah }}"
                                                                data-feed_male="{{ (float) $row->feed_male }}"
                                                                data-feed_female="{{ (float) $row->feed_female }}"
                                                                data-cum_feed_ah="{{ (float) $row->cum_feed_ah }}"
                                                                data-cum_feed_male="{{ (float) $row->cum_feed_male }}"
                                                                data-cum_feed_female="{{ (float) $row->cum_feed_female }}"
                                                                data-weight_ah="{{ (float) $row->weight_ah }}"
                                                                data-weight_male="{{ (float) $row->weight_male }}"
                                                                data-weight_female="{{ (float) $row->weight_female }}"
                                                                data-mortality_ah="{{ (float) $row->mortality_ah }}"
                                                                data-mortality_male="{{ (float) $row->mortality_male }}"
                                                                data-mortality_female="{{ (float) $row->mortality_female }}"
                                                                data-fcr_ah="{{ (float) $row->fcr_ah }}"
                                                                data-fcr_male="{{ (float) $row->fcr_male }}"
                                                                data-fcr_female="{{ (float) $row->fcr_female }}"
                                                                data-pi_ah="{{ (int) $row->pi_ah }}"
                                                                data-pi_male="{{ (int) $row->pi_male }}"
                                                                data-pi_female="{{ (int) $row->pi_female }}"
                                                                class="inline-flex items-center text-xs font-bold text-emerald-650 hover:text-emerald-950 transition-colors">
                                                            แก้ไข
                                                        </button>
                                                        <form method="POST" action="{{ route('feed-intake-masters.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบเกณฑ์มาตรฐานของช่วงอายุ {{ $row->age }} วันนี้หรือไม่?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center text-xs font-bold text-red-650 hover:text-red-950 transition-colors">
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 24 : 23 }}" class="px-5 py-12 text-center text-slate-400">
                                                ยังไม่มีข้อมูลเกณฑ์มาตรฐานการกินอาหาร
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <script>
            let currentFilter = 'all';

            window.editRecord = (btn) => {
                const dataset = btn.dataset;

                // 1. Change header title
                const header = document.querySelector('#feed_intake_form_panel h2');
                if (header) {
                    header.innerHTML = `
                        <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>แก้ไขเกณฑ์มาตรฐาน (อายุ ${dataset.age} วัน)</span>
                    `;
                }

                // 2. Populate inputs
                document.getElementById('age').value = dataset.age;
                document.getElementById('feed_ah').value = dataset.feed_ah;
                document.getElementById('feed_male').value = dataset.feed_male;
                document.getElementById('feed_female').value = dataset.feed_female;

                document.getElementById('cum_feed_ah').value = dataset.cum_feed_ah;
                document.getElementById('cum_feed_male').value = dataset.cum_feed_male;
                document.getElementById('cum_feed_female').value = dataset.cum_feed_female;

                document.getElementById('weight_ah').value = dataset.weight_ah;
                document.getElementById('weight_male').value = dataset.weight_male;
                document.getElementById('weight_female').value = dataset.weight_female;

                document.getElementById('mortality_ah').value = dataset.mortality_ah;
                document.getElementById('mortality_male').value = dataset.mortality_male;
                document.getElementById('mortality_female').value = dataset.mortality_female;

                document.getElementById('fcr_ah').value = dataset.fcr_ah;
                document.getElementById('fcr_male').value = dataset.fcr_male;
                document.getElementById('fcr_female').value = dataset.fcr_female;

                document.getElementById('pi_ah').value = dataset.pi_ah;
                document.getElementById('pi_male').value = dataset.pi_male;
                document.getElementById('pi_female').value = dataset.pi_female;

                // 3. Highlight form
                const formPanel = document.getElementById('feed_intake_form_panel');
                if (formPanel) {
                    formPanel.classList.add('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
                    formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                // 4. Add reset button next to save button
                let cancelBtn = document.getElementById('cancel_edit_btn');
                if (!cancelBtn) {
                    cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.id = 'cancel_edit_btn';
                    cancelBtn.className = 'w-full mt-2 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors text-xs';
                    cancelBtn.textContent = 'ยกเลิกการแก้ไข';
                    cancelBtn.onclick = resetFormToCreate;
                    
                    const form = document.querySelector('#feed_intake_form_panel form');
                    if (form) {
                        form.appendChild(cancelBtn);
                    }
                }
            };

            window.resetFormToCreate = () => {
                // 1. Reset title
                const header = document.querySelector('#feed_intake_form_panel h2');
                if (header) {
                    header.innerHTML = `
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>บันทึกเกณฑ์การกินอาหารรายวัน</span>
                    `;
                }

                // 2. Clear inputs
                document.getElementById('age').value = '';
                document.getElementById('feed_ah').value = '';
                document.getElementById('feed_male').value = '';
                document.getElementById('feed_female').value = '';

                document.getElementById('cum_feed_ah').value = '';
                document.getElementById('cum_feed_male').value = '';
                document.getElementById('cum_feed_female').value = '';

                document.getElementById('weight_ah').value = '';
                document.getElementById('weight_male').value = '';
                document.getElementById('weight_female').value = '';

                document.getElementById('mortality_ah').value = '';
                document.getElementById('mortality_male').value = '';
                document.getElementById('mortality_female').value = '';

                document.getElementById('fcr_ah').value = '';
                document.getElementById('fcr_male').value = '';
                document.getElementById('fcr_female').value = '';

                document.getElementById('pi_ah').value = '';
                document.getElementById('pi_male').value = '';
                document.getElementById('pi_female').value = '';

                // 3. Clear highlight
                const formPanel = document.getElementById('feed_intake_form_panel');
                if (formPanel) {
                    formPanel.classList.remove('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
                }

                // 4. Remove cancel edit button
                const cancelBtn = document.getElementById('cancel_edit_btn');
                if (cancelBtn) {
                    cancelBtn.remove();
                }
            };

            // Instant Client-side Filters
            window.filterByWeek = (weekGroup, btn) => {
                currentFilter = weekGroup;
                
                // Style active button
                document.querySelectorAll('.week-filter-btn').forEach(b => {
                    b.className = 'week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all';
                });
                btn.className = 'week-filter-btn rounded-lg bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition-all';

                applyFilters();
            };

            window.filterBySearch = () => {
                applyFilters();
            };

            function applyFilters() {
                const searchVal = document.getElementById('doc_search').value.toLowerCase().trim();
                const rows = document.querySelectorAll('.feed-row');

                rows.forEach(row => {
                    const rowAge = row.dataset.age;
                    const rowWeekGroup = row.dataset.weekGroup;

                    const matchesWeek = (currentFilter === 'all' || rowWeekGroup === currentFilter);
                    const matchesSearch = (!searchVal || rowAge.includes(searchVal));

                    if (matchesWeek && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
