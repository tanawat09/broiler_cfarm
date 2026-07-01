<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">จับไก่ขาย</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <a href="{{ route('flocks.sale-records.create', $flock) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                เพิ่มบันทึกจับไก่ขาย
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1500px] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('flocks.sale-records.index', $flock) }}" class="mb-5 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[220px_260px_170px_170px_170px_auto] xl:items-end">
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
                                    data-sale-url="{{ route('flocks.sale-records.index', $option) }}"
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

                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        ค้นหา
                    </button>
                </div>
            </form>

            <div class="mb-5 grid gap-4 md:grid-cols-3">
                <div class="rounded-md border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-sm text-slate-600">จำนวนไก่จับขาย</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($summary['birds_sold']) }}</p>
                </div>
                <div class="rounded-md border border-sky-100 bg-sky-50 p-4">
                    <p class="text-sm text-slate-600">น้ำหนักรวม (กก.)</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($summary['total_weight'], 2) }}</p>
                </div>
                <div class="rounded-md border border-amber-100 bg-amber-50 p-4">
                    <p class="text-sm text-slate-600">ยอดเงินรวม</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($summary['total_amount'], 2) }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-[1100px] divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่จับขาย</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เล้า</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จำนวนไก่</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">น้ำหนักรวม</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">น้ำหนักเฉลี่ย</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ราคา/กก.</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ยอดเงิน</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หมายเหตุ</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($saleRecords as $record)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ thai_date($record->sale_date) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">เล้า {{ $record->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format($record->birds_sold) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format((float) $record->total_weight, 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format((float) $record->avg_weight, 3) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-700">{{ number_format((float) $record->price_per_kg, 2) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $record->total_amount, 2) }}</td>
                                    <td class="min-w-48 px-4 py-3 text-slate-700">{{ $record->note ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('flocks.sale-records.edit', [$flock, $record]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">แก้ไข</a>
                                            <form method="POST" action="{{ route('flocks.sale-records.destroy', [$flock, $record]) }}" onsubmit="return confirm('ยืนยันลบบันทึกจับไก่ขายนี้หรือไม่?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">ลบ</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-500">ยังไม่มีบันทึกจับไก่ขาย</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $saleRecords->links() }}
                </div>
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

            const flockOptions = @json($saleSelectorFlocks);

            const showOptionsForFarm = (farmId) => {
                let firstVisible = null;
                flockSelector.innerHTML = '';

                flockOptions
                    .filter((option) => option.farmId === farmId)
                    .forEach((optionData) => {
                        const option = new Option(optionData.text.trim(), optionData.value);
                        option.dataset.farmId = optionData.farmId;
                        option.dataset.saleUrl = optionData.saleUrl;
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

            const redirectToSaleRecords = (option) => {
                if (!option?.dataset.saleUrl) {
                    return;
                }

                const url = new URL(option.dataset.saleUrl, window.location.origin);

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
                redirectToSaleRecords(showOptionsForFarm(farmSelector.value));
            });

            flockSelector.addEventListener('change', () => {
                redirectToSaleRecords(flockSelector.selectedOptions[0]);
            });
        });
    </script>
</x-app-layout>
