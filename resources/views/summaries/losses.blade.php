<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between py-2">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center rounded-lg bg-emerald-500/10 p-2 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V3a1 1 0 00-1-1h-2a1 1 0 00-1 1v15a1 1 0 001 1h2a1 1 0 001-1z" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">รายงานความสูญเสีย</h1>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mt-0.5">
                            {{ $flock->flock_code }} | {{ $flock->farm->farm_name }}
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="mr-1.5 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                กลับรายละเอียดรุ่น
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[98%] px-2 sm:px-4 lg:px-6">
            
            <!-- Tab Switcher -->
            <div class="mb-6 border-b border-slate-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'summary']) }}" class="border-b-2 py-4 px-1 text-sm font-bold transition {{ $tab === 'summary' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-750' }}">
                        <span class="flex items-center gap-2">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            สูญเสียรวม (Weekly & Cumulative)
                        </span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'daily']) }}" class="border-b-2 py-4 px-1 text-sm font-bold transition {{ $tab === 'daily' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-750' }}">
                        <span class="flex items-center gap-2">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            ประวัติการสูญเสียรายวัน
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Tab 1: Summary Table -->
            @if ($tab === 'summary')
                <!-- Filters for Summary -->
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <form method="GET" action="{{ route('flocks.losses', $flock) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[240px_280px_200px_1fr_auto] items-end">
                        <input type="hidden" name="tab" value="summary">
                        <div>
                            <x-input-label for="farm_selector_summary" value="เลือกฟาร์ม" class="font-semibold text-slate-700 text-xs" />
                            <select id="farm_selector_summary" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="flock_selector_summary" value="เลือกรุ่น" class="font-semibold text-slate-700 text-xs" />
                            <select id="flock_selector_summary" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($flockOptions as $option)
                                    <option
                                        value="{{ $option->id }}"
                                        data-farm-id="{{ $option->farm_id }}"
                                        data-url="{{ route('flocks.losses', $option) }}"
                                        @selected((int) $option->id === (int) $flock->id)
                                    >
                                        {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="selected_date" value="วันที่ต้องการสรุปยอด" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="selected_date" name="selected_date" type="date" class="mt-1 block w-full text-xs" :value="$selectedDateStr" required />
                        </div>
                        <div></div>
                        <div class="flex gap-2">
                            <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                ดึงข้อมูลสรุป
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Summary Loss Report Table -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">สรุปความสูญเสียสะสมรวม ณ วันที่ {{ thai_date($selectedDateStr) }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5">แบ่งเปอร์เซ็นต์สูญเสียตามสัปดาห์ (1-7 วัน, 14 วัน, 21 วัน...) และสรุปยอดคงเหลือการกินอาหาร</p>
                        </div>
                        <div class="text-xs font-bold text-slate-500 bg-slate-100 rounded-lg px-3 py-1.5">
                            วันที่เริ่มรุ่น: {{ $flock->start_date->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse min-w-[2800px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-650 font-bold">
                                    <!-- 1. ข้อมูลลงไก่ -->
                                    <th class="py-3.5 px-3 text-center border-r border-slate-100 bg-slate-100/50 w-16 sticky left-0 z-10">เล้า</th>
                                    <th class="py-3.5 px-3 text-center border-r border-slate-100">วันที่ลงไก่</th>
                                    <th class="py-3.5 px-3 text-center border-r border-slate-100">วันที่จับไก่</th>
                                    <th class="py-3.5 px-3 text-right border-r border-slate-100">อายุจับ (วัน)</th>
                                    <th class="py-3.5 px-3 text-right bg-emerald-50/20 text-emerald-950 font-bold border-r border-slate-150">จำนวนไก่เข้า (ตัว)</th>
                                    <th class="py-3.5 px-3 text-right text-slate-600">เพศผู้ (M)</th>
                                    <th class="py-3.5 px-3 text-right text-slate-600 border-r border-slate-100">เพศเมีย (FM)</th>
                                    <th class="py-3.5 px-3 text-right text-slate-400">ผู้เกรด A</th>
                                    <th class="py-3.5 px-3 text-right text-slate-400 border-r border-slate-100">ผู้เกรด B</th>
                                    <th class="py-3.5 px-3 text-right text-slate-400">เมียเกรด A</th>
                                    <th class="py-3.5 px-3 text-right text-slate-400 border-r border-slate-100">เมียเกรด B</th>
                                    <th class="py-3.5 px-3 text-right bg-indigo-50/20 text-indigo-950 font-bold border-r border-slate-150">จำนวนเงิน (บาท)</th>
                                    
                                    <!-- 2. ข้อมูลความสูญเสีย -->
                                    <th class="py-3.5 px-3 text-right bg-amber-50/20 text-slate-800">สูญเสีย 7 วัน</th>
                                    <th class="py-3.5 px-3 text-right bg-amber-50/20 text-slate-800 border-r border-slate-150 font-extrabold">%สูญเสีย 7 วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700">%14วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700">%21วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700">%28วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700">%35วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700">%42วัน</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700 border-r border-slate-150">%49วัน</th>
                                    <th class="py-3.5 px-3 text-right bg-emerald-50/10 text-slate-800 font-extrabold">สูญเสียสะสม</th>
                                    <th class="py-3.5 px-3 text-right bg-emerald-50/10 text-emerald-700 border-r border-slate-150 font-extrabold">%สูญเสีย</th>
                                    <th class="py-3.5 px-3 text-center text-slate-700 border-r border-slate-150 w-20">อายุ (วัน)</th>
                                    <th class="py-3.5 px-3 text-center bg-yellow-50 text-slate-900 font-bold border-r border-slate-150 w-28">{{ Carbon\Carbon::parse($selectedDateStr)->format('j-n-y') }}</th>
                                    
                                    <!-- 3. อาหาร -->
                                    <th class="py-3.5 px-3 text-right text-slate-700">อาหารลงเล้า (กก.)</th>
                                    <th class="py-3.5 px-3 text-right text-slate-700 border-r border-slate-150">อาหารรับ (กก.)</th>
                                    
                                    <!-- 4. รายละเอียดลูกไก่ -->
                                    <th class="py-3.5 px-3 text-left">แหล่งลูกไก่</th>
                                    <th class="py-3.5 px-3 text-left">เกรดลูกไก่</th>
                                    <th class="py-3.5 px-3 text-left">รหัสสายพันธุ์</th>
                                    <th class="py-3.5 px-3 text-left">Batch No.</th>
                                    <th class="py-3.5 px-3 text-left">เพศ</th>
                                    <th class="py-3.5 px-3 text-left">พันธุ์</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-750 bg-white">
                                @foreach ($summaryRows as $row)
                                    @php
                                        $start = $flock->flockHouseStarts->where('house_id', $row['house']->id)->first();
                                        
                                        $housePlacements = $flock->flockHousePlacements->where('house_id', $row['house']->id);
                                        $firstPlacement = $housePlacements->sortBy('placement_date')->first();
                                        $lastPlacement = $housePlacements->whereNotNull('catch_date')->sortByDesc('catch_date')->first();

                                        $placementDate = $row['placement_date'] ?? $firstPlacement?->placement_date;
                                        $catchDate = $row['catch_date'] ?? $lastPlacement?->catch_date;
                                        $catchAge = $row['catch_age'] ?? $housePlacements->whereNotNull('catch_age')->max('catch_age');
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

                                        // color coding conditions matching Excel
                                        // %สูญเสีย 7 วัน > 1.0% -> red
                                        $loss7Rate = $row['initial_birds'] > 0 ? ($row['loss_7'] / $row['initial_birds']) * 100 : 0;
                                        $loss7Color = $loss7Rate > 1.0 ? 'text-red-650 font-bold' : '';

                                        // Total loss rate
                                        $lossCumulativeRate = $row['initial_birds'] > 0 ? ($row['loss_cumulative'] / $row['initial_birds']) * 100 : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <!-- เล้า -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-center font-bold text-slate-900 border-r border-slate-100 bg-slate-100/20 sticky left-0 z-10 bg-white shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                            {{ $row['house']->house_no }}
                                        </td>
                                        <!-- วันที่ลงไก่ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-center font-medium border-r border-slate-100">
                                            {{ $placementDate ? thai_date($placementDate) : thai_date($start?->start_date ?: $flock->start_date) }}
                                        </td>
                                        <!-- วันที่จับไก่ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-center text-slate-500 border-r border-slate-100">
                                            {{ $catchDate ? thai_date($catchDate) : '-' }}
                                        </td>
                                        <!-- อายุจับ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-medium text-slate-800 border-r border-slate-100">
                                            {{ $catchAge === null ? '-' : number_format($catchAge) }}
                                        </td>
                                        <!-- ไก่เข้า -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-bold text-emerald-600 bg-emerald-50/10 border-r border-slate-150">
                                            {{ number_format($row['initial_birds']) }}
                                        </td>
                                        <!-- เพศและจำนวนเงิน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">{{ number_format($maleCount) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right border-r border-slate-100">{{ number_format($femaleCount) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right text-slate-500">{{ number_format($maleGradeA) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right text-slate-500 border-r border-slate-100">{{ number_format($maleGradeB) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right text-slate-500">{{ number_format($femaleGradeA) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right text-slate-500 border-r border-slate-100">{{ number_format($femaleGradeB) }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-bold text-indigo-650 bg-indigo-50/10 border-r border-slate-150">
                                            {{ number_format((float) $amount, 2) }}
                                        </td>

                                        <!-- สูญเสีย 7 วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-medium">
                                            {{ number_format($row['loss_7']) }}
                                        </td>
                                        <!-- %สูญเสีย 7 วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right border-r border-slate-150 font-bold {{ $loss7Color }}">
                                            {{ number_format($loss7Rate, 2) }}
                                        </td>
                                        <!-- %14วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">
                                            {{ ($row['loss_14'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_14'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- %21วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">
                                            {{ ($row['loss_21'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_21'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- %28วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">
                                            {{ ($row['loss_28'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_28'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- %35วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">
                                            {{ ($row['loss_35'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_35'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- %42วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right">
                                            {{ ($row['loss_42'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_42'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- %49วัน -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right border-r border-slate-150">
                                            {{ ($row['loss_49'] > 0 && $row['initial_birds'] > 0) ? number_format(($row['loss_49'] / $row['initial_birds']) * 100, 2) : '-' }}
                                        </td>
                                        <!-- สูญเสียสะสม -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-medium">
                                            {{ number_format($row['loss_cumulative']) }}
                                        </td>
                                        <!-- %สูญเสีย -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right border-r border-slate-150 font-bold bg-sky-50/40 text-sky-850">
                                            {{ number_format($lossCumulativeRate, 2) }}
                                        </td>
                                        <!-- อายุ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-center border-r border-slate-150 font-medium text-slate-800">
                                            {{ $row['age'] > 0 ? $row['age'] : '-' }}
                                        </td>
                                        <!-- Remaining Birds -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-center bg-yellow-50/20 font-bold border-r border-slate-150 text-slate-900">
                                            {{ number_format($row['remaining']) }}
                                        </td>
                                        <!-- อาหารลงเล้า -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-medium text-slate-750">
                                            {{ number_format($row['feed_used']) }}
                                        </td>
                                        <!-- อาหารรับ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-right font-medium text-slate-750 border-r border-slate-150">
                                            {{ number_format($row['feed_received']) }}
                                        </td>

                                        <!-- รายละเอียดลูกไก่ -->
                                        <td class="whitespace-nowrap py-3.5 px-3 text-slate-600 font-medium">{{ $chickSource ?: '-' }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-slate-600">{{ $chickGrade ?: '-' }}</td>
                                        <td class="py-3.5 px-3 text-slate-500 font-mono">{{ $chickCode ?: '-' }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-slate-500">{{ $batchNo ?: '-' }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-slate-500">{{ $sex ?: '-' }}</td>
                                        <td class="whitespace-nowrap py-3.5 px-3 text-slate-500">{{ $breed ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-slate-350 bg-slate-50 font-extrabold text-slate-900">
                                @php
                                    $totInitial = $summaryTotals['initial_birds'];
                                    
                                    $totLoss7Rate = $totInitial > 0 ? ($summaryTotals['loss_7'] / $totInitial) * 100 : 0;
                                    $totLoss14Rate = $totInitial > 0 ? ($summaryTotals['loss_14'] / $totInitial) * 100 : 0;
                                    $totLoss21Rate = $totInitial > 0 ? ($summaryTotals['loss_21'] / $totInitial) * 100 : 0;
                                    $totLoss28Rate = $totInitial > 0 ? ($summaryTotals['loss_28'] / $totInitial) * 100 : 0;
                                    $totLoss35Rate = $totInitial > 0 ? ($summaryTotals['loss_35'] / $totInitial) * 100 : 0;
                                    $totLoss42Rate = $totInitial > 0 ? ($summaryTotals['loss_42'] / $totInitial) * 100 : 0;
                                    $totLoss49Rate = $totInitial > 0 ? ($summaryTotals['loss_49'] / $totInitial) * 100 : 0;
                                    $totLossCumulativeRate = $totInitial > 0 ? ($summaryTotals['loss_cumulative'] / $totInitial) * 100 : 0;

                                    $avgAge = count($summaryRows) > 0 ? array_sum(array_column($summaryRows, 'age')) / count($summaryRows) : 0;

                                    // Placement totals
                                    $placements = $flock->flockHousePlacements;
                                    $totMale = $placements->sum('male_count');
                                    $totFemale = $placements->sum('female_count');
                                    $totMaleA = $placements->sum('male_grade_a_count');
                                    $totMaleB = $placements->sum('male_grade_b_count');
                                    $totFemaleA = $placements->sum('female_grade_a_count');
                                    $totFemaleB = $placements->sum('female_grade_b_count');
                                    $totAmount = $placements->sum('amount');
                                    $avgCatchAge = $placements->whereNotNull('catch_age')->avg('catch_age');
                                @endphp
                                <tr>
                                    <td class="py-3.5 px-3 text-center border-r border-slate-100 bg-slate-100/70 font-black sticky left-0 z-10 bg-slate-50/90 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                        รวม
                                    </td>
                                    <td class="border-r border-slate-100"></td>
                                    <td class="border-r border-slate-100"></td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-100">
                                        {{ $avgCatchAge ? number_format($avgCatchAge, 1) : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150 bg-emerald-50/20">
                                        {{ number_format($totInitial) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">{{ number_format($totMale) }}</td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-100">{{ number_format($totFemale) }}</td>
                                    <td class="py-3.5 px-3 text-right text-slate-500">{{ number_format($totMaleA) }}</td>
                                    <td class="py-3.5 px-3 text-right text-slate-500 border-r border-slate-100">{{ number_format($totMaleB) }}</td>
                                    <td class="py-3.5 px-3 text-right text-slate-500">{{ number_format($totFemaleA) }}</td>
                                    <td class="py-3.5 px-3 text-right text-slate-500 border-r border-slate-100">{{ number_format($totFemaleB) }}</td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150 bg-indigo-50/20">
                                        {{ number_format($totAmount, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($summaryTotals['loss_7']) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150">
                                        {{ number_format($totLoss7Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($totLoss14Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($totLoss21Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($totLoss28Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($totLoss35Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($totLoss42Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150">
                                        {{ number_format($totLoss49Rate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right text-emerald-850">
                                        {{ number_format($summaryTotals['loss_cumulative']) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150 text-emerald-800 bg-emerald-50">
                                        {{ number_format($totLossCumulativeRate, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center border-r border-slate-150 text-slate-800">
                                        {{ number_format($avgAge, 2) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center bg-yellow-100/50 text-slate-900 border-r border-slate-150">
                                        {{ number_format($summaryTotals['remaining']) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        {{ number_format($summaryTotals['feed_used']) }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right border-r border-slate-150">
                                        {{ number_format($summaryTotals['feed_received']) }}
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            @else
                <!-- Tab 2: Daily History Matrix -->
                <!-- Filters for Daily -->
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <form method="GET" action="{{ route('flocks.losses', $flock) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[220px_260px_180px_180px_1fr_auto] items-end">
                        <input type="hidden" name="tab" value="daily">
                        <div>
                            <x-input-label for="farm_selector_daily" value="เลือกฟาร์ม" class="font-semibold text-slate-700 text-xs" />
                            <select id="farm_selector_daily" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="flock_selector_daily" value="เลือกรุ่น" class="font-semibold text-slate-700 text-xs" />
                            <select id="flock_selector_daily" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($flockOptions as $option)
                                    <option
                                        value="{{ $option->id }}"
                                        data-farm-id="{{ $option->farm_id }}"
                                        data-url="{{ route('flocks.losses', $option) }}"
                                        @selected((int) $option->id === (int) $flock->id)
                                    >
                                        {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="start_date" value="วันที่เริ่มต้น" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full text-xs" :value="$startDate" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="วันที่สิ้นสุด" class="font-semibold text-slate-700 text-xs" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full text-xs" :value="$endDate" />
                        </div>
                        <div>
                            <x-input-label value="ประเภทความสูญเสีย" class="font-semibold text-slate-700 text-xs" />
                            <div class="mt-1 inline-flex rounded-lg shadow-sm" role="group">
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'total']) }}" class="px-3.5 py-2 text-xs font-bold border border-slate-300 rounded-l-lg {{ $selectedType === 'total' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                                    ตาย + คัด
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'dead']) }}" class="px-3.5 py-2 text-xs font-bold border-t border-b border-slate-300 {{ $selectedType === 'dead' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                                    ตายอย่างเดียว
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'cull']) }}" class="px-3.5 py-2 text-xs font-bold border border-slate-300 rounded-r-lg {{ $selectedType === 'cull' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 hover:bg-slate-50' }}">
                                    คัดอย่างเดียว
                                </a>
                            </div>
                            <input type="hidden" name="type" value="{{ $selectedType }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                ค้นหา
                            </button>
                            <a href="{{ route('flocks.losses', $flock) }}?tab=daily" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-750 shadow-sm hover:bg-slate-50">
                                ล้าง
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Report Matrix Card -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">
                                ตารางสรุปความสูญเสียประจำวันแยกตามเล้า (หน่วย: ตัว) 
                                <span class="text-xs font-normal text-slate-500 ml-2">
                                    (ประเภท: {{ $selectedType === 'dead' ? 'ตายอย่างเดียว' : ($selectedType === 'cull' ? 'คัดอย่างเดียว' : 'ตายรวมคัด') }})
                                </span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">แสดงประวัติจำนวนการตายหรือคัดรายเล้า เรียงลำดับจากวันที่ล่าสุด</p>
                        </div>
                        <div class="text-sm font-bold text-emerald-600">
                            สูญเสียรวมทั้งหมด: {{ number_format($grandTotal) }} ตัว
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/30 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-3 text-center sticky left-0 bg-slate-100 z-10 min-w-[140px] border-r border-slate-100">วันที่บันทึก</th>
                                    @foreach ($houses as $start)
                                        <th class="py-3 px-2 text-center min-w-[95px]">
                                            เล้า {{ $start->house->house_no }}
                                            <div class="text-[10px] font-normal text-slate-400">
                                                (เริ่ม: {{ number_format($start->initial_birds) }})
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="py-3 px-3 text-right bg-emerald-50 text-emerald-950 font-bold min-w-[110px]">ยอดรวม/วัน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($matrix as $row)
                                    <tr class="hover:bg-slate-50 divide-x divide-slate-100">
                                        <td class="py-3 px-3 text-center font-semibold text-slate-900 sticky left-0 bg-white group-hover:bg-slate-50 whitespace-nowrap border-r border-slate-100 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                            {{ thai_date($row['date']) }}
                                        </td>
                                        @foreach ($houses as $start)
                                            @php 
                                                $cell = $row['houses'][$start->house_id];
                                                $val = $cell['value'];
                                                $age = $cell['age'];
                                                
                                                $bgColor = '';
                                                if ($cell['has_record']) {
                                                    $totalLoss = (int)$cell['total_loss'] ?? 0;
                                                    $initialBirds = (int)$cell['initial_birds'] ?? 0;
                                                    $lossRate = $initialBirds > 0 ? ($totalLoss / $initialBirds) * 100 : 0;
                                                    
                                                    if ($lossRate > 0.31) {
                                                        $bgColor = 'bg-red-50 text-red-700 font-bold';
                                                    } elseif ($lossRate > 0.15) {
                                                        $bgColor = 'bg-amber-50 text-amber-800';
                                                    }
                                                }
                                            @endphp
                                            <td class="py-3 px-2 text-center {{ $bgColor }}">
                                                @if ($cell['has_record'])
                                                    <div class="font-bold text-slate-800">{{ number_format($val) }}</div>
                                                    <div class="text-[10px] text-slate-400">วันที่ {{ $age }}</div>
                                                @else
                                                    <span class="text-slate-300 font-light">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="py-3 px-3 text-right bg-emerald-50/20 text-emerald-950 font-bold whitespace-nowrap">
                                            {{ number_format($row['total']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $houses->count() + 2 }}" class="px-4 py-12 text-center text-slate-500 text-sm">
                                            ไม่พบข้อมูลการบันทึกประจำวันของรุ่นนี้ในช่วงเวลาที่เลือก
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($matrix)
                                <tfoot class="bg-slate-50 border-t-2 border-slate-205 font-bold text-slate-900">
                                    <tr class="divide-x divide-slate-100">
                                        <td class="py-3 px-3 text-center sticky left-0 bg-slate-100 font-bold border-r border-slate-100">รวมทั้งหมด</td>
                                        @foreach ($houses as $start)
                                            <td class="py-3 px-2 text-center bg-slate-100/30 font-bold text-slate-850">
                                                {{ number_format($totalsByHouse[$start->house_id]) }}
                                                <div class="text-[10px] font-bold text-emerald-700 mt-0.5">
                                                    ({{ ($start->initial_birds > 0) ? number_format(($totalsByHouse[$start->house_id] / $start->initial_birds) * 100, 2) . '%' : '-' }})
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="py-3 px-3 text-right bg-emerald-100/50 text-emerald-900 font-black">
                                            {{ number_format($grandTotal) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const setupSelectorPair = (farmId, flockId) => {
                const farmSelector = document.getElementById(farmId);
                const flockSelector = document.getElementById(flockId);
                if (!farmSelector || !flockSelector) return;

                const flockOptions = [];
                flockSelector.querySelectorAll('option').forEach(opt => {
                    flockOptions.push({
                        value: opt.value,
                        text: opt.text,
                        farmId: opt.dataset.farmId,
                        url: opt.dataset.url,
                        selected: opt.selected
                    });
                });

                const showOptionsForFarm = (farmIdValue) => {
                    let firstVisible = null;
                    flockSelector.innerHTML = '';

                    flockOptions
                        .filter((option) => option.farmId === farmIdValue)
                        .forEach((optionData) => {
                            const option = new Option(optionData.text, optionData.value);
                            option.dataset.farmId = optionData.farmId;
                            option.dataset.url = optionData.url;
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

                const redirectToReport = (option) => {
                    if (!option?.dataset.url) {
                        return;
                    }

                    const url = new URL(option.dataset.url, window.location.origin);
                    url.searchParams.set('tab', '{{ $tab }}');

                    if ('{{ $tab }}' === 'summary') {
                        const selectedDateInput = document.getElementById('selected_date');
                        if (selectedDateInput?.value) {
                            url.searchParams.set('selected_date', selectedDateInput.value);
                        }
                    } else {
                        const startDateInput = document.getElementById('start_date');
                        const endDateInput = document.getElementById('end_date');
                        url.searchParams.set('type', '{{ $selectedType }}');
                        if (startDateInput?.value) {
                            url.searchParams.set('start_date', startDateInput.value);
                        }
                        if (endDateInput?.value) {
                            url.searchParams.set('end_date', endDateInput.value);
                        }
                    }

                    window.location.href = url.toString();
                };

                // Initial filtering on load
                showOptionsForFarm(farmSelector.value);

                farmSelector.addEventListener('change', () => {
                    redirectToReport(showOptionsForFarm(farmSelector.value));
                });

                flockSelector.addEventListener('change', () => {
                    redirectToReport(flockSelector.selectedOptions[0]);
                });
            };

            setupSelectorPair('farm_selector_summary', 'flock_selector_summary');
            setupSelectorPair('farm_selector_daily', 'flock_selector_daily');
        });
    </script>
</x-app-layout>
