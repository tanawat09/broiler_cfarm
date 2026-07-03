<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">ไก่เข้าเชือด นน.หน้าโรงงาน</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($flock->status !== 'closed' && $slaughterRecords->total() > 0)
                    <form method="POST" action="{{ route('flocks.slaughter-records.destroy-all', $flock) }}" onsubmit="return confirm('⚠️ ยืนยันลบข้อมูลไก่เข้าเชือดทั้งหมดของรุ่นนี้หรือไม่?\n\nจำนวน {{ $slaughterRecords->total() }} รายการจะถูกลบถาวร')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="group inline-flex items-center gap-2 rounded-xl border-2 border-red-300 bg-white px-4 py-2 text-sm font-bold text-red-600 shadow-sm hover:bg-red-50 hover:border-red-400 hover:text-red-700 transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            ลบข้อมูลรุ่นนี้ทั้งหมด
                        </button>
                    </form>
                @endif
                <a href="{{ route('flocks.slaughter-records.upload', $flock) }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    นำเข้าไฟล์ Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1600px] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filter form -->
            <form method="GET" action="{{ route('flocks.slaughter-records.index', $flock) }}" class="mb-5 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
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
                                    data-slaughter-url="{{ route('flocks.slaughter-records.index', $option) }}"
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
                        <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-350 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            ล้าง
                        </a>
                    </div>
                </div>
            </form>

            <!-- Summary Widgets -->
            <div class="mb-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-800">ไก่เข้าเชือดสะสม</p>
                    <p class="mt-2 text-2xl font-extrabold text-blue-950">{{ number_format($summary['total_birds']) }}</p>
                    <p class="mt-1 text-xs text-blue-700">ตัว</p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-800">น้ำหนักเข้าจริงรวม</p>
                    <p class="mt-2 text-2xl font-extrabold text-emerald-950">{{ number_format($summary['total_weight'], 2) }}</p>
                    <p class="mt-1 text-xs text-emerald-700">กิโลกรัม</p>
                </div>
                <div class="rounded-xl border border-red-100 bg-red-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-red-800">ไก่ตายขนส่งรวม</p>
                    <p class="mt-2 text-2xl font-extrabold text-red-950">{{ number_format($summary['total_doa']) }}</p>
                    <p class="mt-1 text-xs text-red-700">ตัว</p>
                </div>
                <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-800">ไก่เข้าจริงรวม</p>
                    <p class="mt-2 text-2xl font-extrabold text-indigo-950">{{ number_format($summary['total_net']) }}</p>
                    <p class="mt-1 text-xs text-indigo-700">ตัว</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-800">ไก่ตกราวรวม</p>
                    <p class="mt-2 text-2xl font-extrabold text-amber-950">{{ number_format($summary['total_condemned']) }}</p>
                    <p class="mt-1 text-xs text-amber-700">ตัว</p>
                </div>
                <div class="rounded-xl border border-purple-100 bg-purple-50/50 p-4 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-800">ไก่มีปัญหารวม</p>
                    <p class="mt-2 text-2xl font-extrabold text-purple-950">{{ number_format($summary['total_problem']) }}</p>
                    <p class="mt-1 text-xs text-purple-700">ตัว</p>
                </div>
            </div>

            <!-- Main Table -->
            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[1580px] divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-center font-medium text-slate-600">คันที่/ลำดับ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่เข้าเชือด</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เล้า</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ชื่อเล้าใน Excel</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ไก่เข้าเชือด (ตัว)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">น้ำหนักเข้าจริง (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ไก่ตายขนส่ง (ตัว)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ไก่เข้าจริง (ตัว)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ไก่ตกราว (ตัว)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">% ตกราว</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ไก่มีปัญหา (ตัว)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">% มีปัญหา</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">น้ำหนักไก่ตาย (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($slaughterRecords as $record)
                                <tr class="hover:bg-slate-50/40">
                                    <td class="whitespace-nowrap px-4 py-3 text-center font-mono text-slate-700">คันที่ {{ $record->sequence ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ thai_date($record->slaughter_date) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900">เล้า {{ $record->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-500 font-mono">{{ $record->raw_house_name ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono">{{ number_format($record->slaughter_birds) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono font-semibold text-slate-800">{{ number_format($record->actual_weight, 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-red-650">{{ number_format($record->doa_birds) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-emerald-700">{{ number_format($record->net_birds) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-amber-700">{{ number_format($record->condemned_birds) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-amber-600">{{ number_format($record->condemned_percent, 2) }}%</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-purple-700">{{ number_format($record->problem_birds) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-purple-650">{{ number_format($record->problem_percent, 2) }}%</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-slate-600">{{ number_format($record->dead_weight, 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            @if ($flock->status !== 'closed')
                                                <form method="POST" action="{{ route('flocks.slaughter-records.destroy', [$flock, $record]) }}" onsubmit="return confirm('ยืนยันลบรายการข้อมูลไก่เข้าเชือดนี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-750 hover:text-red-950 font-bold">ลบ</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">ปิดรุ่นแล้ว</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-4 py-8 text-center text-slate-500">ยังไม่มีประวัติการนำเข้าข้อมูลไก่เข้าเชือด นน.หน้าโรงงาน</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($slaughterRecords->hasPages())
                    <div class="border-t border-slate-200 px-4 py-3">
                        {{ $slaughterRecords->links() }}
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

            const flockOptions = [
                @foreach ($flockOptions as $option)
                    {
                        value: "{{ $option->id }}",
                        text: "{{ $option->flock_code }}@if ($option->farm) - {{ $option->farm->farm_name }}@endif",
                        farmId: "{{ $option->farm_id }}",
                        slaughterUrl: "{{ route('flocks.slaughter-records.index', $option) }}",
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
                        option.dataset.slaughterUrl = optionData.slaughterUrl;
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

            const redirectToSlaughterRecords = (option) => {
                if (!option || !option.dataset.slaughterUrl) {
                    return;
                }

                const url = new URL(option.dataset.slaughterUrl, window.location.origin);

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
                redirectToSlaughterRecords(showOptionsForFarm(farmSelector.value));
            });

            flockSelector.addEventListener('change', () => {
                const selectedOption = flockSelector.options[flockSelector.selectedIndex];
                redirectToSlaughterRecords(selectedOption);
            });
        });
    </script>
</x-app-layout>
