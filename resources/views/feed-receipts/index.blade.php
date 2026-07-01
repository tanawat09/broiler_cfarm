<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">รายงานการรับอาหาร</h1>
                <p class="mt-1 text-sm text-slate-600">สรุปอาหารเข้าแยกตามวันที่ เบอร์อาหาร ฟาร์ม และเล้าที่ลงอาหาร</p>
            </div>
            <a href="{{ route('feed-receipts.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                เพิ่มบันทึกรับอาหาร
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

            <form method="GET" action="{{ route('feed-receipts.index') }}" class="mb-5 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[220px_220px_170px_170px_auto_auto] xl:items-end">
                    <div>
                        <x-input-label for="farm_id" value="ฟาร์ม" />
                        <select id="farm_id" name="farm_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">ทุกฟาร์ม</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $selectedFarmId === (int) $farm->id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_id" value="รุ่นการเลี้ยง" />
                        <select id="flock_id" name="flock_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">ทุกรุ่น</option>
                            @foreach ($flockOptions as $flockOption)
                                <option
                                    value="{{ $flockOption->id }}"
                                    data-farm-id="{{ $flockOption->farm_id }}"
                                    @selected((int) $selectedFlockId === (int) $flockOption->id)
                                >
                                    {{ $flockOption->flock_code }} @if ($flockOption->farm) - {{ $flockOption->farm->farm_name }} @endif
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
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        แสดงรายงาน
                    </button>
                    <a href="{{ route('feed-receipts.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        ล้างตัวกรอง
                    </a>
                </div>
            </form>

            <div class="mb-5 grid gap-4 md:grid-cols-3">
                <div class="rounded-md border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-sm font-medium text-emerald-800">อาหารรับเข้ารวม</p>
                    <p class="mt-2 text-2xl font-semibold text-emerald-950">{{ number_format((float) $overallSummary['total_quantity_kg'], 2) }}</p>
                    <p class="mt-1 text-xs text-emerald-700">กิโลกรัม</p>
                </div>
                <div class="rounded-md border border-sky-100 bg-sky-50 p-4">
                    <p class="text-sm font-medium text-sky-800">จำนวนเที่ยวรับอาหาร</p>
                    <p class="mt-2 text-2xl font-semibold text-sky-950">{{ number_format((int) $overallSummary['receipt_count']) }}</p>
                    <p class="mt-1 text-xs text-sky-700">รายการ</p>
                </div>
                <div class="rounded-md border border-amber-100 bg-amber-50 p-4">
                    <p class="text-sm font-medium text-amber-800">เล้าที่มีการลงอาหาร</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-950">{{ number_format((int) $overallSummary['house_count']) }}</p>
                    <p class="mt-1 text-xs text-amber-700">เล้า</p>
                </div>
            </div>

            <div class="mb-5 grid gap-4 lg:grid-cols-3">
                @foreach (['203', '204', '205'] as $feedCode)
                    @php $summary = $feedCodeTotals->get($feedCode); @endphp
                    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm text-slate-500">เบอร์อาหาร</p>
                                <p class="text-2xl font-semibold text-slate-900">{{ $feedCode }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                {{ number_format((int) ($summary->receipt_count ?? 0)) }} เที่ยว
                            </span>
                        </div>
                        <div class="mt-4 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xs text-slate-500">ปริมาณรวม</p>
                                <p class="text-xl font-semibold text-slate-900">{{ number_format((float) ($summary->total_quantity_kg ?? 0), 2) }}</p>
                            </div>
                            <p class="text-sm text-slate-500">กก.</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mb-5 overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">สรุปรวมรายวัน</h2>
                        <p class="text-sm text-slate-500">รวมปริมาณอาหารรับเข้าแยกตามเบอร์อาหาร</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่รับ</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">203 (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">204 (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">205 (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">รวมทั้งหมด (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จำนวนเที่ยว</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จำนวนเล้า</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($dailySummaries as $date => $rows)
                                @php
                                    $byFeed = $rows->keyBy('feed_code');
                                    $total = $rows->sum('total_quantity_kg');
                                    $receiptCount = $rows->sum('receipt_count');
                                    $houseCount = $rows->sum('house_count');
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ thai_date($date) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($byFeed->get('203')->total_quantity_kg ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($byFeed->get('204')->total_quantity_kg ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((float) ($byFeed->get('205')->total_quantity_kg ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $total, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((int) $receiptCount) }}</td>
                                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format((int) $houseCount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลรับอาหารตามเงื่อนไขที่เลือก</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">รายละเอียดการรับอาหาร</h2>
                        <p class="text-sm text-slate-500">แสดงแต่ละเที่ยว พร้อมเล้าที่ลงอาหารและปริมาณรวม</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[1200px] divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่รับ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ฟาร์ม</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">รุ่น</th>
                                <th class="px-4 py-3 text-center font-medium text-slate-600">เบอร์อาหาร</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เลขที่ผลิต</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ลงเล้า</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">รวม (กก.)</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($receipts as $receipt)
                                @php
                                    $housesText = $receipt->items
                                        ->sortBy('house.house_no')
                                        ->map(fn ($item) => 'เล้า '.$item->house->house_no.' '.number_format((float) $item->quantity_kg, 0).' กก.')
                                        ->implode(', ');
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-700">{{ thai_date($receipt->receipt_date) }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $receipt->farm->farm_name }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $receipt->matched_flock_code ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex min-w-14 justify-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                            {{ $receipt->feed_code }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $receipt->production_lot }}</td>
                                    <td class="max-w-[520px] px-4 py-3 text-slate-700">
                                        <div class="max-h-11 overflow-hidden leading-5">{{ $housesText ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $receipt->total_quantity_kg, 2) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('feed-receipts.show', $receipt) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-500">ยังไม่มีรายการรับอาหารตามเงื่อนไขที่เลือก</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $receipts->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_id');
            const flockSelector = document.getElementById('flock_id');

            if (!farmSelector || !flockSelector) {
                return;
            }

            const syncFlockOptions = () => {
                const farmId = farmSelector.value;
                let selectedStillVisible = false;

                Array.from(flockSelector.options).forEach((option) => {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    const visible = !farmId || option.dataset.farmId === farmId;
                    option.hidden = !visible;

                    if (visible && option.selected) {
                        selectedStillVisible = true;
                    }
                });

                if (!selectedStillVisible) {
                    flockSelector.value = '';
                }
            };

            syncFlockOptions();
            farmSelector.addEventListener('change', syncFlockOptions);
        });
    </script>
</x-app-layout>
