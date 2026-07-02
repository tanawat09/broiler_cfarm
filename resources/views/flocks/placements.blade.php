<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between py-2">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center rounded-lg bg-emerald-500/10 p-2 text-emerald-600">
                        <!-- Chicken SVG Icon -->
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2 0 4 1.5 4 4v1h2a3 3 0 0 1 3 3v7H3v-7a3 3 0 0 1 3-3h2V7c0-2.5 2-4 4-4Zm-2 5h4V7a2 2 0 0 0-4 0v1Zm-4 5h3v2H6v-2Zm5 0h3v2h-3v-2Zm5 0h2v2h-2v-2Z" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">ข้อมูลลูกไก่</h1>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mt-0.5">
                            จัดการรายละเอียดการลงลูกไก่รายเล้า | {{ $flock->flock_code }} ({{ $flock->farm->farm_name }})
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
        <div class="mx-auto w-full max-w-[99%] px-2 sm:px-4 lg:px-6">
            
            <!-- Selector Header -->
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[240px_280px_1fr] items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" class="font-semibold text-slate-700 text-xs" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" class="font-semibold text-slate-700 text-xs" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($flockOptions as $option)
                                <option
                                    value="{{ $option->id }}"
                                    data-farm-id="{{ $option->farm_id }}"
                                    data-url="{{ route('flocks.placements.index', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-4 self-center justify-end">
                        <span class="text-xs text-slate-500 font-semibold">
                            วันที่เริ่มรุ่นการเลี้ยง: {{ $flock->start_date->format('d/m/Y') }}
                        </span>
                        <a href="{{ route('placements.create-flock-form') }}" class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-300 transform hover:-translate-y-0.5 ring-2 ring-emerald-400/30">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            เปิดรุ่นใหม่
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400"></span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('flocks.placements.store', $flock) }}" id="placements-form">
                @csrf
                
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">ตารางจัดการข้อมูลลูกไก่</h2>
                            <p class="text-xs text-slate-500 mt-0.5">ระบุรายละเอียดลูกไก่รายเล้า หากมีหลายสายพันธุ์หรือกล่องหลายรหัสในเล้าเดียวกัน สามารถกดเพิ่มแถวได้</p>
                        </div>
                        <div>
                            <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                บันทึกข้อมูลทั้งหมด
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse min-w-[1200px]">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-650 font-bold uppercase">
                                    <th class="py-3 px-3 text-center border-r border-slate-150 w-[7%] sticky left-0 z-10 bg-slate-50/90 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">เล้า</th>
                                    <th class="py-3 px-3 text-center border-r border-slate-100 w-[11%]">วันลง</th>
                                    <th class="py-3 px-3 text-center border-r border-slate-100 w-[7%]">พันธุ์</th>
                                    <th class="py-3 px-3 text-left border-r border-slate-100 w-[13%]">โรงฟัก</th>
                                    <th class="py-3 px-3 text-left border-r border-slate-100 w-[14%]">รหัส (Box Code)</th>
                                    <th class="py-3 px-3 text-right border-r border-slate-100 w-[8%] bg-emerald-50/30">ตัวผู้ (ตัว)</th>
                                    <th class="py-3 px-3 text-right border-r border-slate-100 w-[8%] bg-rose-50/30">เมีย (ตัว)</th>
                                    <th class="py-3 px-3 text-center border-r border-slate-100 w-[6%]">เกรด</th>
                                    <th class="py-3 px-3 text-center border-r border-slate-100 w-[7%]">เพศ</th>
                                    <th class="py-3 px-3 text-right border-r border-slate-100 w-[8%] bg-emerald-50/20 text-emerald-950 font-bold">รวม (ตัว)</th>
                                    <th class="py-3 px-3 text-left border-r border-slate-100 w-[11%]">หมายเหตุ</th>
                                    <th class="py-3 px-3 text-center w-[5%]">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-150">
                                @foreach ($flock->flockHouseStarts->sortBy('house.house_no') as $start)
                                    @php 
                                        $houseId = $start->house_id;
                                        $housePlacements = $placementsByHouse->get($houseId) ?: collect();
                                        
                                        $maleSum = $housePlacements->sum('male_count');
                                        $femaleSum = $housePlacements->sum('female_count');
                                        $houseSex = '-';
                                        if ($maleSum > 0 && $femaleSum > 0) $houseSex = 'คละ';
                                        elseif ($maleSum > 0) $houseSex = 'ผู้';
                                        elseif ($femaleSum > 0) $houseSex = 'เมีย';
                                    @endphp
                                    
                                    <!-- House Placement Section -->
                                    <tr class="bg-slate-50/40 font-bold border-t-2 border-slate-200">
                                        <td colspan="12" class="py-2.5 px-3 text-slate-800 text-sm">
                                            เล้า {{ $start->house->house_no }} 
                                            <span class="text-xs font-normal text-slate-500 ml-2">
                                                (จำนวนไก่เข้าเริ่มต้นเดิม: {{ number_format($start->initial_birds) }} ตัว)
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Table Body Container for Dynamic Rows -->
                                    <tbody id="house-rows-{{ $houseId }}" data-house-id="{{ $houseId }}" class="divide-y divide-slate-100">
                                        @foreach ($housePlacements as $index => $placement)
                                            <tr class="placement-row hover:bg-slate-50/50 transition-colors" data-house-id="{{ $houseId }}">
                                                <!-- เล้า -->
                                                <td class="whitespace-nowrap py-2.5 px-3 text-center font-bold text-slate-900 border-r border-slate-150 bg-white sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                                    เล้า {{ $start->house->house_no }}
                                                </td>
                                                <!-- วันลง -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <input type="date" name="placements[{{ $houseId }}][{{ $index }}][placement_date]" value="{{ optional($placement->placement_date)->format('Y-m-d') ?: $flock->start_date->format('Y-m-d') }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                </td>
                                                <!-- พันธุ์ -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <input type="text" name="placements[{{ $houseId }}][{{ $index }}][breed]" value="{{ $placement->breed ?: 'AA' }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center">
                                                </td>
                                                <!-- โรงฟัก -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <select name="placements[{{ $houseId }}][{{ $index }}][chick_source]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="">-</option>
                                                        @foreach ($chickSources as $source)
                                                            <option value="{{ $source->name }}" @selected($placement->chick_source === $source->name)>{{ $source->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <!-- รหัส -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <input type="text" name="placements[{{ $houseId }}][{{ $index }}][chick_code]" value="{{ $placement->chick_code }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 font-mono">
                                                </td>
                                                <!-- ตัวผู้ -->
                                                <td class="py-2.5 px-2 border-r border-slate-100 bg-emerald-50/10">
                                                    <input type="number" name="placements[{{ $houseId }}][{{ $index }}][male_count]" value="{{ $placement->male_count }}" class="qty-trigger w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-bold text-slate-800">
                                                </td>
                                                <!-- เมีย -->
                                                <td class="py-2.5 px-2 border-r border-slate-100 bg-rose-50/10">
                                                    <input type="number" name="placements[{{ $houseId }}][{{ $index }}][female_count]" value="{{ $placement->female_count }}" class="qty-trigger w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-bold text-slate-800">
                                                </td>
                                                <!-- เกรด -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <select name="placements[{{ $houseId }}][{{ $index }}][chick_grade]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center">
                                                        <option value="A" @selected($placement->chick_grade === 'A')>A</option>
                                                        <option value="B" @selected($placement->chick_grade === 'B')>B</option>
                                                        <option value="A,B" @selected($placement->chick_grade === 'A,B')>A,B</option>
                                                    </select>
                                                </td>
                                                <!-- เพศ -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <select name="placements[{{ $houseId }}][{{ $index }}][sex]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="ผู้" @selected($placement->sex === 'ผู้')>ผู้</option>
                                                        <option value="เมีย" @selected($placement->sex === 'เมีย')>เมีย</option>
                                                        <option value="คละ" @selected($placement->sex === 'คละ' || !$placement->sex)>คละ</option>
                                                    </select>
                                                </td>
                                                <!-- รวม -->
                                                <td class="py-2.5 px-2 border-r border-slate-100 bg-emerald-50/5 text-right font-extrabold text-slate-800 text-xs">
                                                    <span class="row-total">{{ number_format($placement->male_count + $placement->female_count) }}</span>
                                                </td>
                                                <!-- หมายเหตุ -->
                                                <td class="py-2.5 px-2 border-r border-slate-100">
                                                    <input type="text" name="placements[{{ $houseId }}][{{ $index }}][remarks]" value="{{ $placement->remarks }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                </td>
                                                <!-- ปุ่มลบ -->
                                                <td class="py-2.5 px-2 text-center">
                                                    <button type="button" class="btn-remove-row text-xs font-semibold text-rose-600 hover:text-rose-800 focus:outline-none">ลบ</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <!-- House Action & Summary Row -->
                                    <tbody class="divide-y divide-slate-100 bg-yellow-50/40">
                                        <!-- Action Row -->
                                        <tr>
                                            <td colspan="12" class="py-2.5 px-3 border-r border-slate-100">
                                                <button type="button" class="btn-add-row inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-900" data-house-id="{{ $houseId }}">
                                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    เพิ่มรายการลูกไก่ลง เล้า {{ $start->house->house_no }}
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Yellow Summary Row -->
                                        <tr class="bg-yellow-100 font-bold border-b border-slate-200" id="house-summary-{{ $houseId }}">
                                            <td class="py-2 px-3 text-center border-r border-slate-150 sticky left-0 z-10 bg-yellow-100 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">รวม</td>
                                            <td class="border-r border-slate-100" colspan="4"></td>
                                            <td class="py-2 px-2 text-right border-r border-slate-100 font-extrabold text-slate-900" id="house-summary-male-{{ $houseId }}">0</td>
                                            <td class="py-2 px-2 text-right border-r border-slate-100 font-extrabold text-slate-900" id="house-summary-female-{{ $houseId }}">0</td>
                                            <td class="border-r border-slate-100"></td>
                                            <td class="py-2 px-2 text-center border-r border-slate-100 font-extrabold text-slate-800 text-xs" id="house-summary-sex-{{ $houseId }}">{{ $houseSex }}</td>
                                            <td class="py-2 px-2 text-right border-r border-slate-100 font-extrabold text-emerald-950" id="house-summary-total-{{ $houseId }}">0</td>
                                            <td class="border-r border-slate-100"></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Controls -->
                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        ยกเลิก
                    </a>
                    <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        บันทึกข้อมูลทั้งหมด
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template for Dynamic Rows -->
    <template id="row-template">
        <tr class="placement-row hover:bg-slate-50/50 transition-colors" data-house-id="{house_id}">
            <!-- เล้า -->
            <td class="whitespace-nowrap py-2.5 px-3 text-center font-bold text-slate-900 border-r border-slate-150 bg-white sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                เล้า {house_no}
            </td>
            <!-- วันลง -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <input type="date" name="placements[{house_id}][{index}][placement_date]" value="{{ $flock->start_date->format('Y-m-d') }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </td>
            <!-- พันธุ์ -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <input type="text" name="placements[{house_id}][{index}][breed]" value="AA" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center">
            </td>
            <!-- โรงฟัก -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <select name="placements[{house_id}][{index}][chick_source]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-</option>
                    @foreach ($chickSources as $source)
                        <option value="{{ $source->name }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </td>
            <!-- รหัส -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <input type="text" name="placements[{house_id}][{index}][chick_code]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 font-mono">
            </td>
            <!-- ตัวผู้ -->
            <td class="py-2.5 px-2 border-r border-slate-100 bg-emerald-50/10">
                <input type="number" name="placements[{house_id}][{index}][male_count]" value="0" class="qty-trigger w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-bold text-slate-800">
            </td>
            <!-- เมีย -->
            <td class="py-2.5 px-2 border-r border-slate-100 bg-rose-50/10">
                <input type="number" name="placements[{house_id}][{index}][female_count]" value="0" class="qty-trigger w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-bold text-slate-800">
            </td>
            <!-- เกรด -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <select name="placements[{house_id}][{index}][chick_grade]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center">
                    <option value="A" selected>A</option>
                    <option value="B">B</option>
                    <option value="A,B">A,B</option>
                </select>
            </td>
            <!-- เพศ -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <select name="placements[{house_id}][{index}][sex]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="ผู้">ผู้</option>
                    <option value="เมีย">เมีย</option>
                    <option value="คละ" selected>คละ</option>
                </select>
            </td>
            <!-- รวม -->
            <td class="py-2.5 px-2 border-r border-slate-100 bg-emerald-50/5 text-right font-extrabold text-slate-800 text-xs">
                <span class="row-total">0</span>
            </td>
            <!-- หมายเหตุ -->
            <td class="py-2.5 px-2 border-r border-slate-100">
                <input type="text" name="placements[{house_id}][{index}][remarks]" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </td>
            <!-- ปุ่มลบ -->
            <td class="py-2.5 px-2 text-center">
                <button type="button" class="btn-remove-row text-xs font-semibold text-rose-600 hover:text-rose-800 focus:outline-none">ลบ</button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');
            
            // Redirect logic
            if (farmSelector && flockSelector) {
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

                const showOptionsForFarm = (farmId) => {
                    let firstVisible = null;
                    flockSelector.innerHTML = '';

                    flockOptions
                        .filter((option) => option.farmId === farmId)
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
                    if (option?.dataset.url) {
                        window.location.href = option.dataset.url;
                    }
                };

                // Initial filtering on load
                showOptionsForFarm(farmSelector.value);

                farmSelector.addEventListener('change', () => {
                    redirectToReport(showOptionsForFarm(farmSelector.value));
                });

                flockSelector.addEventListener('change', () => {
                    redirectToReport(flockSelector.selectedOptions[0]);
                });
            }

            // Placements Editor JS
            const rowTemplate = document.getElementById('row-template').innerHTML;
            const houseRowIndices = {};

            // Initialize indices for each house based on existing rows
            document.querySelectorAll('[id^="house-rows-"]').forEach(tbody => {
                const houseId = tbody.dataset.houseId;
                const rowCount = tbody.querySelectorAll('.placement-row').length;
                houseRowIndices[houseId] = rowCount;
            });

            // Recalculate summary totals for a house
            const recalculateHouse = (houseId) => {
                const tbody = document.getElementById(`house-rows-${houseId}`);
                if (!tbody) return;

                let totalMale = 0;
                let totalFemale = 0;
                let grandTotal = 0;

                tbody.querySelectorAll('.placement-row').forEach(row => {
                    const maleInput = row.querySelector('input[name*="[male_count]"]');
                    const femaleInput = row.querySelector('input[name*="[female_count]"]');
                    
                    const maleVal = parseInt(maleInput?.value || 0, 10);
                    const femaleVal = parseInt(femaleInput?.value || 0, 10);
                    const rowSum = maleVal + femaleVal;

                    // Update row total
                    const rowTotalSpan = row.querySelector('.row-total');
                    if (rowTotalSpan) {
                        rowTotalSpan.textContent = new Intl.NumberFormat().format(rowSum);
                    }
                    
                    totalMale += maleVal;
                    totalFemale += femaleVal;
                    grandTotal += rowSum;
                });

                const maleSumEl = document.getElementById(`house-summary-male-${houseId}`);
                const femaleSumEl = document.getElementById(`house-summary-female-${houseId}`);
                const totalSumEl = document.getElementById(`house-summary-total-${houseId}`);
                const sexSumEl = document.getElementById(`house-summary-sex-${houseId}`);

                if (maleSumEl) maleSumEl.textContent = new Intl.NumberFormat().format(totalMale);
                if (femaleSumEl) femaleSumEl.textContent = new Intl.NumberFormat().format(totalFemale);
                if (totalSumEl) totalSumEl.textContent = new Intl.NumberFormat().format(grandTotal);

                if (sexSumEl) {
                    if (totalMale > 0 && totalFemale > 0) {
                        sexSumEl.textContent = 'คละ';
                    } else if (totalMale > 0) {
                        sexSumEl.textContent = 'ผู้';
                    } else if (totalFemale > 0) {
                        sexSumEl.textContent = 'เมีย';
                    } else {
                        sexSumEl.textContent = '-';
                    }
                }
            };

            // Recalculate all houses
            const recalculateAll = () => {
                Object.keys(houseRowIndices).forEach(houseId => {
                    recalculateHouse(houseId);
                });
            };

            // Add row logic
            document.querySelectorAll('.btn-add-row').forEach(btn => {
                btn.addEventListener('click', () => {
                    const houseId = btn.dataset.houseId;
                    const tbody = document.getElementById(`house-rows-${houseId}`);
                    
                    // Extract house number from button text or list
                    const houseNo = btn.textContent.replace('เพิ่มรายการลูกไก่ลง เล้า ', '').trim();
                    const index = houseRowIndices[houseId];

                    // Replace tokens in template
                    let rowHtml = rowTemplate
                        .replaceAll('{house_id}', houseId)
                        .replaceAll('{house_no}', houseNo)
                        .replaceAll('{index}', index);

                    tbody.insertAdjacentHTML('beforeend', rowHtml);
                    houseRowIndices[houseId]++;

                    recalculateHouse(houseId);
                });
            });

            // Handle remove row & live changes
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-remove-row')) {
                    const row = e.target.closest('.placement-row');
                    const houseId = row.dataset.houseId;
                    row.remove();
                    recalculateHouse(houseId);
                }
            });

            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('qty-trigger')) {
                    const row = e.target.closest('.placement-row');
                    const houseId = row.dataset.houseId;

                    // Auto-detect and set sex selection based on inputs
                    const maleInput = row.querySelector('input[name*="[male_count]"]');
                    const femaleInput = row.querySelector('input[name*="[female_count]"]');
                    const sexSelect = row.querySelector('select[name*="[sex]"]');
                    
                    if (maleInput && femaleInput && sexSelect) {
                        const maleVal = parseInt(maleInput.value || 0, 10);
                        const femaleVal = parseInt(femaleInput.value || 0, 10);
                        
                        if (maleVal > 0 && femaleVal > 0) {
                            sexSelect.value = 'คละ';
                        } else if (maleVal > 0) {
                            sexSelect.value = 'ผู้';
                        } else if (femaleVal > 0) {
                            sexSelect.value = 'เมีย';
                        }
                    }

                    recalculateHouse(houseId);
                }
            });

            // Initial calculation
            recalculateAll();
        });
    </script>
</x-app-layout>
