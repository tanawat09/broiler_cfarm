<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-950">บันทึกคิวจับไก่</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('flocks.catch-records.payment-report-pdf', $flock) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-rose-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v15A2.25 2.25 0 0 0 6.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25V14.25Z" />
                    </svg>
                    PDF สรุปจ่ายเงิน
                </a>
                @if ($flock->status !== 'closed')
                    <a href="{{ route('flocks.catch-records.create', $flock) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        เพิ่มคิวจับไก่
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-100/70 py-6">
        <div class="mx-auto w-full max-w-none px-3 sm:px-5 lg:px-8">
            @if (session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="mb-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase text-slate-500">คิวทั้งหมด</div>
                    <div class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($summary['total_trips']) }}</div>
                    <div class="mt-1 text-sm text-slate-500">เที่ยวรถจับไก่</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase text-slate-500">ไก่จับรวม</div>
                    <div class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($summary['total_birds']) }}</div>
                    <div class="mt-1 text-sm text-slate-500">ตัว</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase text-slate-500">กล่องรวม</div>
                    <div class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($summary['total_boxes']) }}</div>
                    <div class="mt-1 text-sm text-slate-500">กล่องขนส่ง</div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-xs font-semibold uppercase text-slate-500">ค่าจับรวม</div>
                    <div class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($summary['total_fee'], 2) }}</div>
                    <div class="mt-1 text-sm text-slate-500">บาท</div>
                </div>
            </section>

            <form method="GET" action="{{ route('flocks.catch-records.index', $flock) }}" class="mb-5 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-3 lg:grid-cols-[220px_280px_170px_170px_170px_auto] lg:items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farmOption)
                                <option value="{{ $farmOption->id }}" @selected((int) $farmOption->id === (int) $flock->farm_id)>{{ $farmOption->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($flockOptions as $option)
                                <option
                                    value="{{ $option->id }}"
                                    data-farm-id="{{ $option->farm_id }}"
                                    data-catch-url="{{ route('flocks.catch-records.index', $option) }}"
                                    @selected((int) $option->id === (int) $flock->id)
                                >
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="start_date" value="วันที่เริ่มต้น" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full rounded-lg" :value="$startDate" />
                    </div>

                    <div>
                        <x-input-label for="end_date" value="วันที่สิ้นสุด" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full rounded-lg" :value="$endDate" />
                    </div>

                    <div>
                        <x-input-label for="house_id" value="เล้า" />
                        <select id="house_id" name="house_id" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">ทุกเล้า</option>
                            @foreach ($availableStarts as $start)
                                <option value="{{ $start->house_id }}" @selected((int) $houseId === (int) $start->house_id)>เล้า {{ $start->house->house_no }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-w-24 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">ค้นหา</button>
                        <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex min-w-20 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">ล้าง</a>
                    </div>
                </div>
            </form>

            <section class="mb-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 bg-white px-4 py-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-950">สรุปจ่ายเงินทีมจับไก่</h2>
                        <p class="mt-1 text-sm text-slate-500">ใส่ค่าน้ำมันและค่ารถยกเพื่อใช้คำนวณยอดจ่ายสุทธิ หัก ณ ที่จ่าย 3%</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4">
                        <div class="rounded-md bg-slate-50 px-3 py-2">
                            <div class="text-xs text-slate-500">ค่าน้ำมัน</div>
                            <div class="font-bold text-slate-950">{{ number_format($teamPaymentSummary['total_fuel_cost'], 2) }}</div>
                        </div>
                        <div class="rounded-md bg-slate-50 px-3 py-2">
                            <div class="text-xs text-slate-500">ค่ารถยก</div>
                            <div class="font-bold text-slate-950">{{ number_format($teamPaymentSummary['total_forklift_cost'], 2) }}</div>
                        </div>
                        <div class="rounded-md bg-red-50 px-3 py-2">
                            <div class="text-xs text-red-700">หัก 3%</div>
                            <div class="font-bold text-red-700">{{ number_format($teamPaymentSummary['withholding_amount'], 2) }}</div>
                        </div>
                        <div class="rounded-md bg-emerald-50 px-3 py-2">
                            <div class="text-xs text-emerald-700">สุทธิจ่าย</div>
                            <div class="font-bold text-emerald-700">{{ number_format($teamPaymentSummary['net_payment'], 2) }}</div>
                        </div>
                    </div>
                </div>

                @if ($teamPaymentRows->isNotEmpty())
                    <form method="POST" action="{{ route('flocks.catch-records.team-costs.update', $flock) }}">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[1180px] text-sm">
                                <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                                    <tr class="border-b border-slate-200">
                                        <th class="px-4 py-3 text-left">ทีมจับ</th>
                                        <th class="px-4 py-3 text-right">เที่ยว</th>
                                        <th class="px-4 py-3 text-right">ตัว</th>
                                        <th class="px-4 py-3 text-right">กล่อง</th>
                                        <th class="px-4 py-3 text-right">ค่าจับ</th>
                                        <th class="px-4 py-3 text-right">ค่าน้ำมัน</th>
                                        <th class="px-4 py-3 text-right">ค่ารถยก</th>
                                        <th class="px-4 py-3 text-right">รวมจ่าย</th>
                                        <th class="px-4 py-3 text-right">หัก 3%</th>
                                        <th class="px-4 py-3 text-right">สุทธิจ่าย</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($teamPaymentRows as $index => $row)
                                        <tr class="hover:bg-slate-50">
                                            <td class="whitespace-nowrap px-4 py-3 font-bold text-slate-950">
                                                {{ $row['catching_team'] }}
                                                <input type="hidden" name="costs[{{ $index }}][catching_team]" value="{{ $row['catching_team'] }}">
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono">{{ number_format($row['total_trips']) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono">{{ number_format($row['total_birds']) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono">{{ number_format($row['total_boxes']) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-semibold">{{ number_format($row['total_fee'], 2) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                                <input type="number" name="costs[{{ $index }}][fuel_cost]" step="0.01" min="0" value="{{ old('costs.'.$index.'.fuel_cost', $row['fuel_cost']) }}" class="w-32 rounded-md border-gray-300 py-1.5 text-right text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" @disabled($flock->status === 'closed')>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                                <input type="number" name="costs[{{ $index }}][forklift_cost]" step="0.01" min="0" value="{{ old('costs.'.$index.'.forklift_cost', $row['forklift_cost']) }}" class="w-32 rounded-md border-gray-300 py-1.5 text-right text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" @disabled($flock->status === 'closed')>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-semibold">{{ number_format($row['payment_total'], 2) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-semibold text-red-600">{{ number_format($row['withholding_amount'], 2) }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-bold text-emerald-700">{{ number_format($row['net_payment'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($flock->status !== 'closed')
                            <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-4 py-3">
                                <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">บันทึกรายละเอียดทีมจับไก่</button>
                            </div>
                        @endif
                    </form>
                @else
                    <div class="px-4 py-8 text-center text-sm text-slate-500">ยังไม่มีข้อมูลทีมจับไก่ในรุ่นนี้</div>
                @endif
            </section>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 bg-white px-4 py-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-950">รายละเอียดเที่ยวรถจับไก่</h2>
                        <p class="mt-1 text-sm text-slate-500">เรียงตามคันที่/ลำดับจากการคีย์ข้อมูลต่อกันทั้งรุ่น</p>
                    </div>
                    <div class="text-sm font-semibold text-slate-600">พบ {{ number_format($catchRecords->total()) }} รายการ</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1320px] text-sm">
                        <thead class="bg-slate-900 text-xs uppercase text-white">
                            <tr>
                                <th class="w-24 px-3 py-3 text-center">คันที่</th>
                                <th class="w-32 px-3 py-3 text-left">วันที่จับไก่</th>
                                <th class="w-24 px-3 py-3 text-center">เล้า</th>
                                <th class="w-36 px-3 py-3 text-left">ทะเบียน</th>
                                <th class="w-32 px-3 py-3 text-right">จำนวนตัว</th>
                                <th class="w-28 px-3 py-3 text-right">กล่อง</th>
                                <th class="w-32 px-3 py-3 text-left">ชนิดรถ</th>
                                <th class="w-36 px-3 py-3 text-left">ทีมจับ</th>
                                <th class="w-32 px-3 py-3 text-right">ค่าจับ</th>
                                <th class="min-w-56 px-3 py-3 text-left">หมายเหตุ</th>
                                <th class="w-36 px-3 py-3 text-right">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($catchRecords as $record)
                                <tr class="hover:bg-emerald-50/40">
                                    <td class="whitespace-nowrap px-3 py-3 text-center">
                                        <span class="inline-flex min-w-14 justify-center rounded-md bg-slate-100 px-2 py-1 font-mono text-sm font-bold text-slate-900">{{ $record->sequence }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 font-medium text-slate-800">{{ thai_date($record->catch_date) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center font-bold text-slate-900">เล้า {{ $record->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 font-mono font-semibold text-slate-900">{{ $record->license_plate }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right font-mono font-bold text-slate-950">{{ number_format($record->birds_count) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right font-mono text-slate-700">{{ number_format($record->boxes_count) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $record->vehicle_type ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-800">{{ $record->catching_team ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right font-mono font-bold text-emerald-700">{{ number_format($record->catching_fee, 2) }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $record->note ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right">
                                        @if ($flock->status !== 'closed')
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('flocks.catch-records.edit', [$flock, $record]) }}" class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50">แก้ไข</a>
                                                <form method="POST" action="{{ route('flocks.catch-records.destroy', [$flock, $record]) }}" onsubmit="return confirm('ยืนยันลบรายการคิวจับไก่นี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100">ลบ</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">ปิดรุ่นแล้ว</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-10 text-center text-sm text-slate-500">ยังไม่มีข้อมูลคิวจับไก่</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($catchRecords->hasPages())
                    <div class="border-t border-slate-200 bg-white px-4 py-3">
                        {{ $catchRecords->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (!farmSelector || !flockSelector) {
                return;
            }

            const flockOptions = [
                @foreach ($flockOptions as $option)
                    {
                        value: "{{ $option->id }}",
                        text: "{{ $option->flock_code }}@if ($option->farm) - {{ $option->farm->farm_name }}@endif",
                        farmId: "{{ $option->farm_id }}",
                        catchUrl: "{{ route('flocks.catch-records.index', $option) }}",
                        selected: {{ (int) $option->id === (int) $flock->id ? 'true' : 'false' }}
                    },
                @endforeach
            ];

            const showOptionsForFarm = (farmId) => {
                let firstVisible = null;
                flockSelector.innerHTML = '';

                flockOptions
                    .filter((option) => option.farmId === farmId)
                    .forEach((optionData) => {
                        const option = new Option(optionData.text.trim(), optionData.value);
                        option.dataset.farmId = optionData.farmId;
                        option.dataset.catchUrl = optionData.catchUrl;
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

            const redirectToCatchRecords = (option) => {
                if (!option || !option.dataset.catchUrl) {
                    return;
                }

                const url = new URL(option.dataset.catchUrl, window.location.origin);

                if (startDateInput && startDateInput.value) {
                    url.searchParams.set('start_date', startDateInput.value);
                }

                if (endDateInput && endDateInput.value) {
                    url.searchParams.set('end_date', endDateInput.value);
                }

                window.location.href = url.toString();
            };

            showOptionsForFarm(farmSelector.value);

            farmSelector.addEventListener('change', () => {
                redirectToCatchRecords(showOptionsForFarm(farmSelector.value));
            });

            flockSelector.addEventListener('change', () => {
                redirectToCatchRecords(flockSelector.options[flockSelector.selectedIndex]);
            });
        });
    </script>
</x-app-layout>
