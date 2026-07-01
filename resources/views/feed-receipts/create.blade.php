<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">เพิ่มบันทึกรับอาหาร</h1>
                <p class="mt-1 text-sm text-gray-600">กรอกข้อมูล 1 เที่ยว และระบุปริมาณอาหารลงเล้าแยกตามเล้า</p>
            </div>
            <a href="{{ route('feed-receipts.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    กรุณาตรวจสอบข้อมูลอีกครั้ง
                </div>
            @endif

            <div class="mb-6 rounded-md border border-gray-200 bg-white p-5 shadow-sm">
                <form id="receipt-filter-form" method="GET" action="{{ route('feed-receipts.create') }}" class="grid gap-4 lg:grid-cols-[1.3fr_0.7fr_auto] lg:items-end">
                    <div>
                        <x-input-label for="farm_filter" value="เลือกฟาร์มสำหรับแสดงเล้า" />
                        <select id="farm_filter" name="farm_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected($selectedFarm?->id === $farm->id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="receipt_date_filter" value="วันที่รับอาหาร" />
                        <x-text-input id="receipt_date_filter" name="receipt_date" type="date" class="mt-1 block w-full" :value="$receiptDate" />
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        แสดงข้อมูล
                    </button>
                </form>
            </div>

            <form method="POST" action="{{ route('feed-receipts.store') }}">
                @csrf
                <input type="hidden" name="farm_id" value="{{ $selectedFarm?->id }}">
                <input type="hidden" name="receipt_date" value="{{ old('receipt_date', $receiptDate) }}">

                <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 grid gap-4 md:grid-cols-4">
                        <div class="rounded-md border border-emerald-100 bg-emerald-50 px-4 py-3">
                            <p class="text-xs font-medium text-gray-500">ฟาร์ม</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $selectedFarm?->farm_name ?? '-' }}</p>
                        </div>
                        <div class="rounded-md border border-sky-100 bg-sky-50 px-4 py-3">
                            <p class="text-xs font-medium text-gray-500">วันที่รับ</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ thai_date($receiptDate) }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('receipt_date')" />
                        </div>
                        <div class="rounded-md border border-amber-100 bg-amber-50 px-4 py-3">
                            <p class="text-xs font-medium text-gray-500">รุ่นอ้างอิงสำหรับคำนวณอายุ</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activeFlock?->flock_code ?? '-' }}</p>
                        </div>
                        <div class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                            <p class="text-xs font-medium text-gray-500">สรุปที่กรอก</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                <span id="filled-house-count">0</span> เล้า /
                                <span id="feed-total-kg">0.00</span> กก.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-[220px_1fr]">
                        <div>
                            <x-input-label for="feed_code" value="เบอร์อาหาร" />
                            <select id="feed_code" name="feed_code" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach (['203', '204', '205'] as $feedCode)
                                    <option value="{{ $feedCode }}" @selected(old('feed_code') === $feedCode)>{{ $feedCode }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('feed_code')" />
                        </div>
                        <div>
                            <x-input-label for="production_lot" value="เลขที่ผลิต" />
                            <x-text-input id="production_lot" name="production_lot" type="text" class="mt-1 block w-full" :value="old('production_lot')" />
                            <x-input-error class="mt-2" :messages="$errors->get('production_lot')" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">ปริมาณอาหารที่ลงแต่ละเล้า</h2>
                            <p class="mt-1 text-sm text-gray-500">กรอกเฉพาะเล้าที่ได้รับอาหารในเที่ยวนี้</p>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('items')" />
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-white">
                                <tr>
                                    <th class="w-28 px-5 py-3 text-left font-medium text-gray-600">เล้า</th>
                                    <th class="w-32 px-5 py-3 text-center font-medium text-gray-600">อายุไก่</th>
                                    <th class="px-5 py-3 text-right font-medium text-gray-600">ปริมาณอาหารรับ (กก.)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($houses as $house)
                                    @php $field = 'items.'.$house->id.'.quantity_kg'; @endphp
                                    <tr class="hover:bg-emerald-50/40">
                                        <td class="whitespace-nowrap px-5 py-3 font-semibold text-gray-900">เล้า {{ $house->house_no }}</td>
                                        <td class="whitespace-nowrap px-5 py-3 text-center">
                                            <span class="inline-flex min-w-16 justify-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                                {{ $ageByHouse->get($house->id) === null ? '-' : number_format($ageByHouse->get($house->id)).' วัน' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <input data-feed-quantity type="number" step="0.01" min="0" name="items[{{ $house->id }}][quantity_kg]" value="{{ old($field) }}" class="w-48 rounded-md border-gray-300 text-right text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            <x-input-error class="mt-1 text-left" :messages="$errors->get($field)" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">ยังไม่มีเล้าที่ใช้งานในฟาร์มนี้</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button :disabled="$houses->isEmpty()">บันทึกรับอาหาร</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('farm_filter')?.addEventListener('change', function () {
            document.getElementById('receipt-filter-form')?.submit();
        });

        document.getElementById('receipt_date_filter')?.addEventListener('change', function () {
            document.getElementById('receipt-filter-form')?.submit();
        });

        const quantityInputs = document.querySelectorAll('[data-feed-quantity]');
        const totalKg = document.getElementById('feed-total-kg');
        const filledHouseCount = document.getElementById('filled-house-count');

        const refreshFeedSummary = () => {
            let total = 0;
            let count = 0;

            quantityInputs.forEach((input) => {
                const value = Number(input.value || 0);

                if (value > 0) {
                    total += value;
                    count += 1;
                }
            });

            totalKg.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            filledHouseCount.textContent = count.toLocaleString();
        };

        quantityInputs.forEach((input) => input.addEventListener('input', refreshFeedSummary));
        refreshFeedSummary();
    </script>
</x-app-layout>
