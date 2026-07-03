<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">คิดค่าจับไก่</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <a href="{{ route('flocks.catch-records.create', $flock) }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                บันทึกค่าจับไก่
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1500px] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <form method="GET" action="{{ route('flocks.catch-records.index', $flock) }}" class="mb-5 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[220px_260px_170px_170px_170px_auto] xl:items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farmOption)
                                <option value="{{ $farmOption->id }}" @selected((int) $farmOption->id === (int) $flock->farm_id)>{{ $farmOption->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
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
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="$startDate" />
                    </div>

                    <div>
                        <x-input-label for="end_date" value="วันที่สิ้นสุด" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="$endDate" />
                    </div>

                    <div>
                        <x-input-label for="house_id" value="เล้า" />
                        <select id="house_id" name="house_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">ทุกเล้า</option>
                            @foreach ($availableStarts as $start)
                                <option value="{{ $start->house_id }}" @selected((int) $houseId === (int) $start->house_id)>เล้า {{ $start->house->house_no }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            ค้นหา
                        </button>
                        <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-350 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            ล้าง
                        </a>
                    </div>
                </div>
            </form>

            <div class="mb-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-5 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-800">ยอดรวมค่าจับไก่ทั้งหมด</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-950">฿{{ number_format($summary['total_fee'], 2) }}</p>
                    <p class="mt-1 text-xs text-emerald-700">บาท</p>
                </div>
                <div class="rounded-xl border border-sky-100 bg-sky-50/50 p-5 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sky-800">จำนวนรถ / เที่ยวที่จับไก่</p>
                    <p class="mt-2 text-3xl font-extrabold text-sky-950">{{ number_format($summary['total_trips']) }}</p>
                    <p class="mt-1 text-xs text-sky-700">เที่ยวคัน</p>
                </div>
                <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-5 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-800">จำนวนไก่ที่จับรวม</p>
                    <p class="mt-2 text-3xl font-extrabold text-indigo-950">{{ number_format($summary['total_birds']) }}</p>
                    <p class="mt-1 text-xs text-indigo-700">ตัว</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-5 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">จำนวนกล่องใส่ไก่รวม</p>
                    <p class="mt-2 text-3xl font-extrabold text-amber-950">{{ number_format($summary['total_boxes']) }}</p>
                    <p class="mt-1 text-xs text-amber-700">กล่อง</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[1200px] divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่จับ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เล้า</th>
                                <th class="px-4 py-3 text-center font-medium text-slate-600">คันที่/ลำดับ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ทะเบียน</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จำนวนตัว</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จำนวนกล่อง</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ชนิดรถ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ทีมจับ</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ค่าจับ (บาท)</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หมายเหตุ</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($catchRecords as $record)
                                <tr class="hover:bg-slate-50/40">
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ thai_date($record->catch_date) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900">เล้า {{ $record->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center text-slate-700 font-mono">คันที่ {{ $record->sequence }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700 font-medium">{{ $record->license_plate }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700 font-mono">{{ number_format($record->birds_count) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700 font-mono">{{ number_format($record->boxes_count) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $record->vehicle_type ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $record->catching_team ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-emerald-700">฿{{ number_format($record->catching_fee, 2) }}</td>
                                    <td class="min-w-48 px-4 py-3 text-slate-700">{{ $record->note ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            @if ($flock->status !== 'closed')
                                                <a href="{{ route('flocks.catch-records.edit', [$flock, $record]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">แก้ไข</a>
                                                <form method="POST" action="{{ route('flocks.catch-records.destroy', [$flock, $record]) }}" onsubmit="return confirm('ยืนยันลบรายการค่าจับไก่นี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-750 hover:text-red-950">ลบ</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">ปิดรุ่นแล้ว</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-8 text-center text-slate-500">ยังไม่มีประวัติการคีย์ข้อมูลคิดค่าจับไก่</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($catchRecords->hasPages())
                    <div class="border-t border-slate-200 px-4 py-3">
                        {{ $catchRecords->links() }}
                    </div>
                @endif
            </div>
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

            // Raw flocks options from PHP to allow dynamic JS filtering
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
                const selectedOption = flockSelector.options[flockSelector.selectedIndex];
                redirectToCatchRecords(selectedOption);
            });
        });
    </script>
</x-app-layout>
