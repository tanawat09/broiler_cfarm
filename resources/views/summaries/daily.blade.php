<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">ใบหน้าเล้า</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $flock->flock_code }} | {{ $flock->farm->farm_name }}</p>
            </div>
            <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                กลับรายละเอียดรุ่น
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full px-3 sm:px-4 lg:px-6">
            <div class="mb-4 rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('flocks.summary', $flock) }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[220px_260px_180px_1fr_1fr_auto_auto] xl:items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($flockOptions->where('farm_id', $flock->farm_id) as $option)
                                <option
                                    value="{{ $option->id }}"
                                    data-farm-id="{{ $option->farm_id }}"
                                    data-summary-url="{{ route('flocks.summary', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="house_id" value="เลือกเล้า" />
                        <select id="house_id" name="house_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($availableStarts as $start)
                                <option value="{{ $start->house_id }}" @selected((int) $selectedHouseId === (int) $start->house_id)>
                                    เล้า {{ $start->house->house_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="start_date" value="วันที่เริ่มต้น" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="$startDate" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="วันที่สิ้นสุด" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="$endDate" />
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        แสดงรายงาน
                    </button>
                    <a href="{{ route('flocks.summary', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        ล้าง
                    </a>
                </form>
            </div>

            @forelse ($houseDailyTables as $table)
                @php
                    $placement = $table['placement'];
                    $totalFeedIn = $table['rows']->sum('feed_in');
                    $totalFeedUsed = $table['rows']->sum('feed_used');
                    $totalWater = $table['rows']->sum('water_used');
                    $lastRow = $table['rows']->last();
                @endphp

                <section class="overflow-hidden rounded-md border border-gray-300 bg-white shadow-sm">
                    <div class="border-b-2 border-emerald-700 bg-white text-center text-sm font-bold text-gray-900">
                        บันทึกการเลี้ยงไก่เนื้อ
                    </div>

                    <div class="grid border-b border-gray-300 text-xs md:grid-cols-[80px_1fr_1fr_1fr_120px]">
                        <div class="border-r border-gray-300 bg-gray-50 px-2 py-2 font-semibold">ฟาร์ม</div>
                        <div class="border-r border-gray-300 bg-fuchsia-500 px-2 py-2 text-center font-bold text-white">{{ $flock->farm->farm_name }}</div>
                        <div class="border-r border-gray-300 px-2 py-2">
                            <span class="font-semibold">ที่อยู่</span>
                            {{ $flock->farm->address ?: '-' }}
                        </div>
                        <div class="border-r border-gray-300 px-2 py-2">
                            <span class="font-semibold">รหัสเล้า-รุ่น</span>
                            {{ $placement?->batch_no ?: $flock->flock_code }}
                        </div>
                        <div class="bg-gray-50 px-2 py-2 text-center font-bold">โรงเรือน {{ $table['house']->house_no }}</div>
                    </div>

                    <div class="grid border-b border-gray-300 text-xs md:grid-cols-6">
                        <div class="border-r border-gray-300 px-2 py-1">
                            <span class="font-semibold">จำนวนไก่เข้า</span>
                            {{ number_format($table['initial_birds']) }} ตัว
                        </div>
                        <div class="border-r border-gray-300 px-2 py-1">
                            <span class="font-semibold">วันที่ลง</span>
                            {{ thai_date($table['start_date']) }}
                        </div>
                        <div class="border-r border-gray-300 px-2 py-1">
                            <span class="font-semibold">เพศ</span>
                            {{ $placement?->sex ?: '-' }}
                        </div>
                        <div class="border-r border-gray-300 px-2 py-1">
                            <span class="font-semibold">แหล่งลูกไก่</span>
                            {{ $placement?->chick_source ?: '-' }}
                        </div>
                        <div class="border-r border-gray-300 px-2 py-1">
                            <span class="font-semibold">เกรด</span>
                            {{ $placement?->chick_grade ?: '-' }}
                        </div>
                        <div class="px-2 py-1">
                            <span class="font-semibold">ไก่คงเหลือล่าสุด</span>
                            {{ $lastRow ? number_format($lastRow['remaining_birds']) : number_format($table['initial_birds']) }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[1900px] border-collapse text-xs">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">สัปดาห์</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">อายุ</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">ว/ด/ป</th>
                                    <th colspan="3" class="border border-gray-300 px-1 py-1 text-center">เบอร์อาหาร / เลขที่ผลิต</th>
                                    <th colspan="2" class="border border-gray-300 px-1 py-1 text-center">อาหาร</th>
                                    <th colspan="3" class="border border-gray-300 bg-green-100 px-1 py-1 text-center">อุณหภูมิ / ความชื้น</th>
                                    <th colspan="2" class="border border-gray-300 px-1 py-1 text-center">ไก่ตาย</th>
                                    <th colspan="2" class="border border-gray-300 px-1 py-1 text-center">ไก่คัดทิ้ง</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">สูญเสียรวม</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">น้ำหนัก (กก.)</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">อัตราตาย %</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">ยา/วัคซีน</th>
                                    <th rowspan="2" class="border border-gray-300 px-1 py-1 text-center">น้ำใช้</th>
                                    <th rowspan="2" class="border border-gray-300 bg-yellow-100 px-1 py-1 text-center">ไก่คงเหลือ</th>
                                </tr>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-1 py-1 text-center">เบอร์</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เลขที่ผลิต</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เข้า</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">สะสมเข้า</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">ใช้</th>
                                    <th class="border border-gray-300 bg-green-100 px-1 py-1 text-center">ต่ำสุด</th>
                                    <th class="border border-gray-300 bg-green-100 px-1 py-1 text-center">สูงสุด</th>
                                    <th class="border border-gray-300 bg-green-100 px-1 py-1 text-center">ชื้น %</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เช้า</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เย็น</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เช้า</th>
                                    <th class="border border-gray-300 px-1 py-1 text-center">เย็น</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningFeedIn = 0;
                                    $currentWeek = null;
                                @endphp
                                @forelse ($table['rows'] as $row)
                                    @php
                                        $runningFeedIn += $row['feed_in'];
                                        $nextRow = $table['rows']->get($loop->index + 1);
                                        $isWeekEnd = ! $nextRow || $nextRow['week_no'] !== $row['week_no'];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                         @php
                                             $dayOfWeek = (($row['age_day'] - 1) % 7) + 1;
                                         @endphp
                                         @if ($dayOfWeek === 5)
                                             <td class="border border-gray-300 px-1 py-1 text-center font-semibold bg-yellow-100/50">
                                                 {{ $row['week_no'] }}
                                             </td>
                                         @elseif ($dayOfWeek === 6)
                                             <td class="border border-gray-300 bg-yellow-350 px-1 py-1 text-center font-bold text-slate-800">
                                                 FCR
                                             </td>
                                         @elseif ($dayOfWeek === 7)
                                             <td class="border border-gray-300 px-1 py-1 text-center font-bold bg-yellow-50 text-slate-800">
                                                 {{ $row['fcr'] !== null ? number_format($row['fcr'], 2) : '-' }}
                                             </td>
                                         @else
                                             <td class="border border-gray-300 px-1 py-1 text-center"></td>
                                         @endif
                                         <td class="border border-gray-300 px-1 py-1 text-center">{{ number_format($row['age_day']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-center">{{ thai_date($row['date']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-center">{{ $row['feed_code'] ?: '-' }}</td>
                                         <td class="border border-gray-300 px-1 py-1">{{ $row['feed_lots'] ?: '-' }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ $row['feed_in'] > 0 ? number_format($row['feed_in'], 2) : '-' }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($runningFeedIn, 2) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['feed_used'], 2) }}</td>
                                         <td class="border border-gray-300 bg-green-50 px-1 py-1 text-right">{{ $row['temp_min'] === null ? '-' : number_format((float) $row['temp_min'], 0) }}</td>
                                         <td class="border border-gray-300 bg-green-50 px-1 py-1 text-right">{{ $row['temp_max'] === null ? '-' : number_format((float) $row['temp_max'], 0) }}</td>
                                         <td class="border border-gray-300 bg-green-50 px-1 py-1 text-right">{{ $row['humidity'] === null ? '-' : number_format((float) $row['humidity'], 0) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['dead_morning']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['dead_evening']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['cull_morning']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['cull_evening']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right font-semibold">{{ number_format($row['loss_total']) }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right font-semibold text-indigo-700">{{ $row['avg_weight'] !== null ? number_format($row['avg_weight'], 3) : '-' }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['mortality_rate'], 2) }}</td>
                                         <td class="border border-gray-300 px-1 py-1">{{ $row['medicine_note'] ?: '-' }}</td>
                                         <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($row['water_used']) }}</td>
                                         <td class="border border-gray-300 bg-yellow-50 px-1 py-1 text-right font-semibold">{{ number_format($row['remaining_birds']) }}</td>
                                     </tr>
                                     @php $currentWeek = $row['week_no']; @endphp
 
                                     @if ($isWeekEnd)
                                         @php $summary = $table['weekly_summaries']->get($row['week_no']); @endphp
                                         <tr class="bg-yellow-200 font-semibold border-t border-slate-300">
                                             <td class="border border-gray-300 px-1 py-1 text-center">{{ $row['week_no'] }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-center">รวม</td>
                                             <td colspan="3" class="border border-gray-300 px-1 py-1">รวมสัปดาห์ที่ {{ $row['week_no'] }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ $summary['feed_in'] > 0 ? number_format($summary['feed_in'], 2) : '-' }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($runningFeedIn, 2) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($summary['feed_used'], 2) }}</td>
                                             <td colspan="3" class="border border-gray-300 px-1 py-1"></td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-red-700">{{ number_format($summary['dead_morning']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-red-700">{{ number_format($summary['dead_evening']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-amber-700">{{ number_format($summary['cull_morning']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-amber-700">{{ number_format($summary['cull_evening']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right font-bold text-red-850">{{ number_format($summary['loss_total']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1"></td>
                                             <td colspan="2" class="border border-gray-300 px-1 py-1"></td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-blue-700">{{ number_format($summary['water_used']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right font-bold">{{ $summary['remaining_birds'] === null ? '-' : number_format($summary['remaining_birds']) }}</td>
                                         </tr>
                                         <tr class="bg-yellow-100 font-semibold border-b border-slate-300">
                                             <td class="border border-gray-300 px-1 py-1 text-center">{{ $row['week_no'] }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-center">สะสม</td>
                                             <td colspan="3" class="border border-gray-300 px-1 py-1">สะสมทั้งหมดถึงสัปดาห์ที่ {{ $row['week_no'] }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ $summary['cum_feed_in'] > 0 ? number_format($summary['cum_feed_in'], 2) : '-' }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($summary['cum_feed_in'], 2) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right">{{ number_format($summary['cum_feed_used'], 2) }}</td>
                                             <td colspan="3" class="border border-gray-300 px-1 py-1"></td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-red-700">{{ number_format($summary['cum_dead_morning']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-red-700">{{ number_format($summary['cum_dead_evening']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-amber-700">{{ number_format($summary['cum_cull_morning']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-amber-700">{{ number_format($summary['cum_cull_evening']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right font-bold text-red-850">{{ number_format($summary['cum_loss_total']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1"></td>
                                             <td colspan="2" class="border border-gray-300 px-1 py-1"></td>
                                             <td class="border border-gray-300 px-1 py-1 text-right text-blue-700">{{ number_format($summary['cum_water_used']) }}</td>
                                             <td class="border border-gray-300 px-1 py-1 text-right font-bold">{{ $summary['remaining_birds'] === null ? '-' : number_format($summary['remaining_birds']) }}</td>
                                         </tr>
                                     @endif
                                @empty
                                    <tr>
                                        <td colspan="21" class="border border-gray-300 px-4 py-8 text-center text-gray-500">ยังไม่มีข้อมูลของเล้านี้ในช่วงวันที่ที่เลือก</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-yellow-300 font-bold">
                                    <td colspan="5" class="border border-gray-300 px-2 py-2 text-right">รวมทั้งหมด</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ number_format($totalFeedIn, 2) }}</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ number_format($totalFeedIn, 2) }}</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ number_format($totalFeedUsed, 2) }}</td>
                                    <td colspan="7" class="border border-gray-300 px-1 py-2"></td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ $lastRow ? number_format($lastRow['cumulative_loss']) : '0' }}</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right font-semibold text-indigo-700">{{ $lastRow && $lastRow['latest_avg_weight'] !== null ? number_format($lastRow['latest_avg_weight'], 3) : '-' }}</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ $lastRow ? number_format($lastRow['mortality_rate'], 2) : '0.00' }}</td>
                                    <td class="border border-gray-300 px-1 py-2">รวม</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ number_format($totalWater) }}</td>
                                    <td class="border border-gray-300 px-1 py-2 text-right">{{ $lastRow ? number_format($lastRow['remaining_birds']) : number_format($table['initial_birds']) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            @empty
                <div class="rounded-md border border-gray-200 bg-white p-8 text-center text-gray-500 shadow-sm">
                    ยังไม่มีข้อมูลเล้าสำหรับรุ่นนี้
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            document.getElementById('house_id')?.addEventListener('change', function () {
                this.form.submit();
            });

            if (!farmSelector || !flockSelector) {
                return;
            }

            const flockOptions = @json($summarySelectorFlocks);

            const showOptionsForFarm = (farmId) => {
                let firstVisible = null;

                flockSelector.innerHTML = '';

                flockOptions
                    .filter((option) => option.farmId === farmId)
                    .forEach((optionData) => {
                        const option = new Option(optionData.text.trim(), optionData.value);
                        option.dataset.farmId = optionData.farmId;
                        option.dataset.summaryUrl = optionData.summaryUrl;
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

                flockOptions.forEach((option) => {
                    option.selected = option.farmId === farmId && firstVisible !== null && option.value === firstVisible.value;
                });

                return firstVisible;
            };

            const redirectToSummary = (option) => {
                if (!option?.dataset.summaryUrl) {
                    return;
                }

                const url = new URL(option.dataset.summaryUrl, window.location.origin);

                if (startDateInput?.value) {
                    url.searchParams.set('start_date', startDateInput.value);
                }

                if (endDateInput?.value) {
                    url.searchParams.set('end_date', endDateInput.value);
                }

                window.location.href = url.toString();
            };

            showOptionsForFarm(farmSelector.value);

            farmSelector.addEventListener('change', () => {
                redirectToSummary(showOptionsForFarm(farmSelector.value));
            });

            flockSelector.addEventListener('change', () => {
                redirectToSummary(flockSelector.selectedOptions[0]);
            });
        });
    </script>
</x-app-layout>
