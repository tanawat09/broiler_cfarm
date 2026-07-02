<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between py-2">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">แดชบอร์ดบริหารฟาร์ม (Poultry Farm Management)</h1>
                <p class="mt-1 text-sm text-slate-500">
                    @if(isset($flock))
                        ฟาร์ม: <span class="font-semibold text-slate-700">{{ $flock->farm->farm_name }}</span> | รุ่นการเลี้ยง: <span class="font-semibold text-slate-700">{{ $flock->flock_code }}</span>
                    @else
                        ภาพรวมข้อมูลและประสิทธิภาพการเลี้ยงไก่เนื้อ
                    @endif
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('daily-records.shortcut') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition-all duration-200">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                    </svg>
                    บันทึกประจำวัน
                </a>
                <a href="{{ route('weight-records.shortcut') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all duration-200">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-6-6 6 6 6-6M5 12h14" />
                    </svg>
                    ลงน้ำหนักไก่
                </a>
                <a href="{{ route('water-meters.shortcut') }}" class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-700 transition-all duration-200">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a7 7 0 0 1-7-7c0-4.5 7-13 7-13s7 8.5 7 13a7 7 0 0 1-7 7Z" />
                    </svg>
                    มิเตอร์น้ำรายวัน
                </a>
                <a href="{{ route('summary.shortcut') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all duration-200">
                    <svg class="h-4 w-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14" />
                    </svg>
                    ใบหน้าเล้า
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[98%] px-3 sm:px-4 lg:px-6">
            @if(isset($error))
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm">
                    เกิดข้อผิดพลาดในการโหลดข้อมูล: {{ $error }}
                </div>
            @endif

            @unless ($databaseReady)
                <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 shadow-sm">
                    ยังเชื่อมต่อฐานข้อมูลไม่ได้ กรุณาตรวจสอบการตั้งค่า Database
                </div>
            @endunless

            @if(isset($flock))
                <!-- Selector Panel -->
                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <x-input-label for="db_farm_selector" value="เลือกฟาร์ม" class="text-xs font-semibold uppercase tracking-wider text-slate-500" />
                            <select id="db_farm_selector" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="db_flock_selector" value="เลือกรุ่นการเลี้ยง" class="text-xs font-semibold uppercase tracking-wider text-slate-500" />
                            <select id="db_flock_selector" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                @foreach ($flockOptions as $option)
                                    <option
                                        value="{{ $option->id }}"
                                        data-farm-id="{{ $option->farm_id }}"
                                        @selected((int) $option->id === (int) $flock->id)
                                    >
                                        {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="db_house_selector" value="เลือกเล้า" class="text-xs font-semibold uppercase tracking-wider text-slate-500" />
                            <select id="db_house_selector" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50">
                                <option value="0" @selected((int)$selectedHouseId === 0)>เล้าทั้งหมด (All Houses)</option>
                                @foreach ($availableHouses as $house)
                                    <option value="{{ $house->id }}" @selected((int)$selectedHouseId === (int)$house->id)>
                                        เล้า {{ $house->house_no }} @if($house->house_name) - {{ $house->house_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            @if(!isset($flock))
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">ยังไม่มีรุ่นการเลี้ยงที่เปิดใช้งาน</h3>
                    <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">กรุณาลงทะเบียนฟาร์มและเปิดรุ่นการเลี้ยงใหม่ในระบบ เพื่อเริ่มบันทึกข้อมูลและเข้าสู่การวิเคราะห์ผ่านแดชบอร์ด</p>
                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('flocks.create') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                            เปิดรุ่นการเลี้ยงใหม่
                        </a>
                    </div>
                </div>
            @else
                <!-- 1. KPI Summary Cards -->
                <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- KPI 1: Liveability / Survival Rate -->
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-emerald-50 transition-all group-hover:scale-150"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">อัตราการเลี้ยงรอดสะสม (Liveability)</p>
                                <h3 class="mt-2 text-2xl font-bold text-emerald-600">
                                    {{ number_format($kpis['survivalRate'], 2) }}%
                                    <span class="text-xs font-medium text-slate-500">({{ number_format($kpis['remainingBirds']) }} ตัว)</span>
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">จากไก่เข้าเริ่มต้น {{ number_format($flock->initial_birds) }} ตัว</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 2: Mortality & Losses -->
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-red-50 transition-all group-hover:scale-150"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">อัตราการสูญเสียสะสม (Mortality)</p>
                                <h3 class="mt-2 text-2xl font-bold text-red-600">
                                    {{ number_format($kpis['mortalityRate'], 2) }}%
                                    <span class="text-xs font-medium text-slate-500">({{ number_format($kpis['mortality']) }} ตัว)</span>
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">เป้าหมายมาตรฐานควรต่ำกว่า 4.0%</p>
                            </div>
                            <div class="rounded-xl bg-red-50 p-3 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 3: Total Feed Consumed -->
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-amber-50 transition-all group-hover:scale-150"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">อาหารที่ใช้สะสม (Feed Consumed)</p>
                                <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($kpis['totalFeedConsumed'], 2) }} <span class="text-sm font-medium text-slate-500">กก.</span></h3>
                                <p class="mt-1 text-xs text-slate-500">คิดเป็นประมาณ {{ number_format($kpis['totalFeedConsumed'] / 50, 0) }} กระสอบ (50 กก.)</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- KPI 4: FCR -->
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-emerald-50 transition-all group-hover:scale-150"></div>
                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">ดัชนีแลกเนื้อสะสม (Average FCR)</p>
                                <h3 class="mt-2 text-2xl font-bold text-emerald-600">{{ $kpis['fcr'] !== null ? number_format($kpis['fcr'], 2) : '-' }}</h3>
                                <p class="mt-1 text-xs text-slate-500">เป้าหมายมาตรฐาน FCR ณ สัปดาห์ปัจจุบัน</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V3a1 1 0 00-1-1h-2a1 1 0 00-1 1v15a1 1 0 001 1h2a1 1 0 001-1z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Main Analytics & Graph Charts -->
                <div class="mb-6 grid gap-6 lg:grid-cols-3">
                    <!-- Graph Section (Span 2) -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Chart 1: Feed vs Water -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">การกินอาหารรายวันและสะสม vs ปริมาณน้ำกิน</h3>
                                <span class="rounded bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">แสดงผลทับซ้อน (Dual Axis)</span>
                            </div>
                            <div class="h-80 w-full">
                                <canvas id="feedWaterChart"></canvas>
                            </div>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Chart 2: Environment Temp/Humidity -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">อุณหภูมิห้องเลี้ยง (°C) & ความชื้น (%)</h3>
                                </div>
                                <div class="h-64 w-full">
                                    <canvas id="envChart"></canvas>
                                </div>
                            </div>

                            <!-- Chart 3: Mortality splitting (Morning/Evening) -->
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">จำนวนไก่ตายและไก่คัดรายวัน (ตัว)</h3>
                                </div>
                                <div class="h-64 w-full">
                                    <canvas id="mortalityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 3: Efficiency & Summary Insights Widget -->
                    <div class="space-y-6">
                        <!-- Density widget -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-100 pb-3 mb-4">ความหนาแน่นของการเลี้ยง (Density)</h3>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-slate-900">{{ number_format($birdsPerSqm, 2) }} <span class="text-xs font-normal text-slate-500">ตัว / ตารางเมตร</span></p>
                                    <p class="mt-1 text-xs text-slate-500">พื้นที่รวมประมาณ {{ number_format($totalArea) }} ตร.ม.</p>
                                    <p class="mt-1.5 text-[10px] font-medium text-slate-400">สูตร: จำนวนไก่ลงเริ่มต้น / พื้นที่การเลี้ยง</p>
                                </div>
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $birdsPerSqm > 13 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ $birdsPerSqm > 13 ? 'ค่อนข้างหนาแน่น' : 'ได้มาตรฐาน' }}
                                </span>
                            </div>
                            <!-- progress bar -->
                            <div class="mt-4">
                                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-indigo-600 transition-all duration-300" style="width: {{ min(100, ($birdsPerSqm / 15) * 100) }}%"></div>
                                </div>
                                <div class="mt-1 flex justify-between text-[10px] text-slate-400">
                                    <span>เบาบาง (0-8)</span>
                                    <span>มาตรฐาน (10-12)</span>
                                    <span>หนาแน่น (13+)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Standard Comparison Table -->
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">เปรียบเทียบกับมาตรฐาน ( Ross 308 )</h3>
                                <span class="text-xs font-semibold text-slate-500">คำนวณตามอายุจับจริงเฉลี่ย</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse min-w-[650px]">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                            <th class="py-2 pr-2 text-left">ตัวชี้วัด</th>
                                            <th class="py-2 px-2 text-right">จริง (Actual)</th>
                                            <th class="py-2 px-2 text-right">STD {{ $comparisonTable['age']['std_prev'] }} วัน</th>
                                            <th class="py-2 px-2 text-right">STD {{ $comparisonTable['age']['std_next'] }} วัน</th>
                                            <th class="py-2 px-2 text-right">Delta ที่ใช้</th>
                                            <th class="py-2 px-2 text-right">Fraction</th>
                                            <th class="py-2 px-2 text-right bg-emerald-50/50 text-emerald-950 font-bold border-x border-emerald-100/50">STD (คุม)</th>
                                            <th class="py-2 pl-2 text-right bg-indigo-50/50 text-indigo-950 font-bold">%STD</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach(['age', 'liveability', 'feed', 'weight', 'fcr', 'pi'] as $key)
                                            @php 
                                                $row = $comparisonTable[$key];
                                            @endphp
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="py-2.5 pr-2 font-semibold text-slate-800">{{ $row['label'] }}</td>
                                                <td class="py-2.5 px-2 text-right font-bold text-slate-900 bg-slate-50/30">{{ $row['actual'] }}</td>
                                                <td class="py-2.5 px-2 text-right text-slate-500">{{ $row['std_prev'] }}</td>
                                                <td class="py-2.5 px-2 text-right text-slate-500">{{ $row['std_next'] }}</td>
                                                <td class="py-2.5 px-2 text-right text-slate-500 font-medium">{{ $row['delta'] }}</td>
                                                <td class="py-2.5 px-2 text-right text-slate-500 font-medium">{{ $row['fraction'] }}</td>
                                                <td class="py-2.5 px-2 text-right font-bold text-emerald-700 bg-emerald-50/20 border-x border-emerald-100/30">{{ $row['std_interpolated'] }}</td>
                                                <td class="py-2.5 pl-2 text-right bg-indigo-50/10">
                                                    @if($row['pct'] === '-')
                                                        <span class="text-slate-400 font-semibold">-</span>
                                                    @else
                                                        @php 
                                                            $val = (float) str_replace('%', '', $row['pct']);
                                                            $isGood = $val >= 100;
                                                        @endphp
                                                        <span class="inline-flex items-center rounded-md px-1.5 py-0.5 font-bold text-[10px] border {{ $isGood ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                                            {{ $row['pct'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Log Table for Current Week -->
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">ข้อมูลรายงานบันทึกประจำเป็นสัปดาห์ปัจจุบัน (Daily Log Week)</h3>
                        <span class="text-xs font-medium text-slate-500">แสดงผลย้อนหลัง 7 วัน</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 table-fixed min-w-[700px]">
                            <colgroup>
                                <col class="w-[120px]">
                                <col class="w-[80px]">
                                <col class="w-[100px]">
                                <col class="w-[120px]">
                                <col class="w-[120px]">
                                <col class="w-[130px]">
                                <col class="w-[160px]">
                            </colgroup>
                            <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-2.5 font-semibold">วันที่</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">อายุ (วัน)</th>
                                    <th class="px-4 py-2.5 font-semibold">เบอร์อาหาร</th>
                                    <th class="px-4 py-2.5 text-right font-semibold">อาหารที่ใช้กิน (กก.)</th>
                                    <th class="px-4 py-2.5 text-center font-semibold">อุณหภูมิเฉลี่ย</th>
                                    <th class="px-4 py-2.5 text-right font-semibold">ไก่คงเหลือ (ตัว)</th>
                                    <th class="px-4 py-2.5 font-semibold">วัคซีน / ยารักษา</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($dailyLogs as $log)
                                    @php
                                        // Detect if row indicates a vaccine/medication day
                                        $hasMed = $log['medicine_note'] !== null;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors {{ $hasMed ? 'bg-indigo-50/20' : '' }}">
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $log['thai_date'] }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-slate-700">{{ $log['age_day'] }} วัน</td>
                                        <td class="px-4 py-3 font-semibold text-slate-600">{{ $log['feed_code'] }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ number_format($log['feed_used'], 2) }}</td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ $log['temp_range'] }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format($log['remaining_birds']) }}</td>
                                        <td class="px-4 py-3">
                                            @if($hasMed)
                                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700 border border-indigo-200">
                                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z" />
                                                    </svg>
                                                    {{ $log['medicine_note'] }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">ยังไม่มีข้อมูลสำหรับสัปดาห์นี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Chart.js and Custom Script -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const chartData = @json($chartData);
                        
                        const labels = chartData.map(d => 'Day ' + d.age_day);
                        const feedDaily = chartData.map(d => d.feed_used);
                        const feedCum = chartData.map(d => d.cum_feed);
                        const waterDaily = chartData.map(d => d.water_used);
                        
                        const tempMin = chartData.map(d => d.temp_min);
                        const tempMax = chartData.map(d => d.temp_max);
                        const humidity = chartData.map(d => d.humidity);

                        const deadTotal = chartData.map(d => d.dead_morning + d.dead_evening);
                        const cullTotal = chartData.map(d => d.cull_morning + d.cull_evening);

                        // 1. Feed & Water Dual Axis Line Chart
                        const ctxFeed = document.getElementById('feedWaterChart').getContext('2d');
                        new Chart(ctxFeed, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'อาหารกินรายวัน (กก.)',
                                        data: feedDaily,
                                        borderColor: '#fbbf24',
                                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                                        borderWidth: 2,
                                        yAxisID: 'yDaily',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'อาหารสะสมรวม (กก.)',
                                        data: feedCum,
                                        borderColor: '#4f46e5',
                                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                                        borderWidth: 2,
                                        fill: true,
                                        yAxisID: 'yCum',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'น้ำกินรายวัน (ลิตร)',
                                        data: waterDaily,
                                        borderColor: '#0ea5e9',
                                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                                        borderWidth: 2,
                                        yAxisID: 'yDaily',
                                        tension: 0.3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { boxWidth: 12, font: { size: 10 } }
                                    }
                                },
                                scales: {
                                    x: { grid: { display: false } },
                                    yDaily: {
                                        type: 'linear',
                                        position: 'left',
                                        title: { display: true, text: 'รายวัน (อาหาร/น้ำ)', font: { size: 10 } }
                                    },
                                    yCum: {
                                        type: 'linear',
                                        position: 'right',
                                        title: { display: true, text: 'สะสมรวม (อาหาร)', font: { size: 10 } },
                                        grid: { drawOnChartArea: false }
                                    }
                                }
                            }
                        });

                        // 2. Temperature & Humidity Line Chart
                        const ctxEnv = document.getElementById('envChart').getContext('2d');
                        new Chart(ctxEnv, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'อุณหภูมิต่ำสุด (°C)',
                                        data: tempMin,
                                        borderColor: '#38bdf8',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        pointRadius: 0
                                    },
                                    {
                                        label: 'อุณหภูมิสูงสุด (°C)',
                                        data: tempMax,
                                        borderColor: '#f43f5e',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        pointRadius: 0
                                    },
                                    {
                                        label: 'ความชื้น (%)',
                                        data: humidity,
                                        borderColor: '#10b981',
                                        borderWidth: 2,
                                        tension: 0.3,
                                        pointRadius: 0
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { boxWidth: 10, font: { size: 9 } }
                                    }
                                },
                                scales: {
                                    x: { grid: { display: false } }
                                }
                            }
                        });

                        // 3. Mortality Bar Chart
                        const ctxMort = document.getElementById('mortalityChart').getContext('2d');
                        new Chart(ctxMort, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'ไก่ตาย',
                                        data: deadTotal,
                                        backgroundColor: '#ef4444',
                                    },
                                    {
                                        label: 'ไก่คัด',
                                        data: cullTotal,
                                        backgroundColor: '#f59e0b',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { boxWidth: 10, font: { size: 9 } }
                                    }
                                },
                                scales: {
                                    x: { stacked: true, grid: { display: false } },
                                    y: { stacked: true }
                                }
                            }
                        });
                        // 4. Selector Redirection logic
                        const farmSelector = document.getElementById('db_farm_selector');
                        const flockSelector = document.getElementById('db_flock_selector');
                        const houseSelector = document.getElementById('db_house_selector');

                        if (farmSelector && flockSelector && houseSelector) {
                            const flockOptions = Array.from(flockSelector.options).map((option) => ({
                                value: option.value,
                                text: option.textContent,
                                farmId: option.dataset.farmId,
                                selected: option.selected,
                            }));

                            const showOptionsForFarm = (farmId) => {
                                let firstVisible = null;
                                flockSelector.innerHTML = '';
                                flockOptions
                                    .filter((option) => option.farmId === farmId)
                                    .forEach((optionData) => {
                                        const option = new Option(optionData.text.trim(), optionData.value);
                                        option.dataset.farmId = optionData.farmId;
                                        option.selected = optionData.selected;
                                        flockSelector.add(option);
                                        if (option.selected || firstVisible === null) firstVisible = option;
                                    });

                                if (firstVisible) {
                                    flockSelector.value = firstVisible.value;
                                } else {
                                    const option = new Option('ไม่มีรุ่นในฟาร์มนี้', '');
                                    option.disabled = true;
                                    option.selected = true;
                                    flockSelector.add(option);
                                }

                                flockOptions.forEach((option) => {
                                    if (option.farmId === farmId) {
                                        option.selected = firstVisible !== null && option.value === firstVisible.value;
                                    } else {
                                        option.selected = false;
                                    }
                                });

                                return firstVisible;
                            };

                            showOptionsForFarm(farmSelector.value);

                            const redirectToSelection = (flockVal, houseVal) => {
                                const url = new URL('/dashboard', window.location.origin);
                                if (flockVal) {
                                    url.searchParams.set('flock_id', flockVal);
                                }
                                if (houseVal !== undefined) {
                                    url.searchParams.set('house_id', houseVal);
                                }
                                window.location.href = url.toString();
                            };

                            farmSelector.addEventListener('change', () => {
                                const firstFlock = showOptionsForFarm(farmSelector.value);
                                redirectToSelection(firstFlock ? firstFlock.value : '', '0');
                            });

                            flockSelector.addEventListener('change', () => {
                                redirectToSelection(flockSelector.value, '0');
                            });

                            houseSelector.addEventListener('change', () => {
                                redirectToSelection(flockSelector.value, houseSelector.value);
                            });
                        }
                    });
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
