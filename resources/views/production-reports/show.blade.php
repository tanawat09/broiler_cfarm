<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">รายงานสรุปผลการเลี้ยงประจำรุ่น</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('flocks.production-report.pdf', $flock) }}" target="_blank" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-700">PDF</a>
                <a href="{{ route('flocks.production-report.excel', $flock) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">Excel</a>
                <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">กลับหน้ารุ่น</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1800px] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('status') }}</div>
            @endif

            <div class="mb-5 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 md:grid-cols-[220px_280px_1fr] md:items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            @foreach ($reportSelectorFlocks as $option)
                                <option value="{{ $option['value'] }}" data-farm-id="{{ $option['farmId'] }}" data-url="{{ $option['url'] }}" @selected($option['selected'])>{{ $option['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-sm text-slate-500">ไก่จับใช้ค่าไก่เข้าจริงหลังหักตายขนส่ง และ PI ใช้สูตรที่กำหนด</div>
                </div>
            </div>

            <style>
                .production-table { width: 100%; min-width: 1680px; border-collapse: collapse; font-size: 12px; }
                .production-table th, .production-table td { border: 1px solid #cbd5e1; padding: 6px 7px; vertical-align: middle; }
                .production-table th { background: #eaf1dc; color: #0f172a; font-weight: 700; text-align: center; white-space: nowrap; }
                .production-table .total-row td { background: #eaf1dc; font-weight: 800; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .nowrap { white-space: nowrap; }
            </style>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-4 py-3 text-center">
                    <h2 class="text-base font-bold text-slate-900">{{ $title }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @include('production-reports._table')
                </div>
            </div>

            <div class="mt-5 grid gap-5 xl:grid-cols-[420px_1fr]">
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-3 text-sm font-bold text-slate-900">เปรียบเทียบกับมาตรฐาน</h3>
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="border px-3 py-2 text-left">รายการ</th>
                                <th class="border px-3 py-2 text-right">จริง</th>
                                <th class="border px-3 py-2 text-right">STD</th>
                                <th class="border px-3 py-2 text-right">%STD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($standards as $standard)
                                <tr>
                                    <td class="border px-3 py-2">{{ $standard['label'] }}</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($standard['actual'], in_array($standard['label'], ['PI']) ? 0 : 2) }}</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($standard['std'], in_array($standard['label'], ['PI', 'อายุ']) ? 0 : 2) }}</td>
                                    <td class="border px-3 py-2 text-right">{{ number_format($standard['percent'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">สรุปรายรับ/ต้นทุน</h3>
                        <button form="adjustment-form" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-700">บันทึกค่ากรอกเอง</button>
                    </div>
                    <form id="adjustment-form" method="POST" action="{{ route('flocks.production-report.adjustments', $flock) }}">
                        @csrf
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="border px-3 py-2 text-left">รายการ</th>
                                    <th class="border px-3 py-2 text-right">บาท</th>
                                    <th class="border px-3 py-2 text-right">บาท/ตัว</th>
                                    <th class="border px-3 py-2 text-left">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($financeRows as $financeRow)
                                    @php
                                        $amountClass = (float) $financeRow['amount'] < 0 ? 'font-bold text-red-600' : '';
                                        $perBirdClass = (float) $financeRow['per_bird'] < 0 ? 'font-bold text-red-600' : '';
                                    @endphp
                                    <tr @class(['font-bold bg-slate-50' => ($financeRow['type'] ?? '') === 'total'])>
                                        <td class="border px-3 py-2">{{ $financeRow['label'] }}</td>
                                        <td class="border px-3 py-2 text-right {{ $amountClass }}">
                                            @if (($financeRow['type'] ?? '') === 'manual' && isset($financeRow['key']))
                                                <input type="number" step="0.01" name="adjustments[{{ $financeRow['key'] }}][amount]" value="{{ $financeRow['amount'] }}" class="w-32 rounded-md border-gray-300 text-right text-sm {{ $amountClass }}">
                                            @else
                                                {{ number_format($financeRow['amount'], 2) }}
                                            @endif
                                        </td>
                                        <td class="border px-3 py-2 text-right {{ $perBirdClass }}">{{ number_format($financeRow['per_bird'], 2) }}</td>
                                        <td class="border px-3 py-2">
                                            @if (($financeRow['type'] ?? '') === 'manual' && isset($financeRow['key']))
                                                <input type="text" name="adjustments[{{ $financeRow['key'] }}][note]" value="{{ $financeRow['note'] }}" class="w-full rounded-md border-gray-300 text-sm" placeholder="หมายเหตุ">
                                            @else
                                                {{ $financeRow['note'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <x-input-label for="note" value="Note รวมของรายงาน" />
                            <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('note', $adjustment?->note) }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const farmSelector = document.getElementById('farm_selector');
        const flockSelector = document.getElementById('flock_selector');
        function syncFlocks() {
            const farmId = farmSelector.value;
            [...flockSelector.options].forEach(option => option.hidden = option.dataset.farmId !== farmId);
            if (flockSelector.selectedOptions[0]?.hidden) {
                const firstVisible = [...flockSelector.options].find(option => !option.hidden);
                if (firstVisible) firstVisible.selected = true;
            }
        }
        farmSelector?.addEventListener('change', syncFlocks);
        flockSelector?.addEventListener('change', () => {
            const url = flockSelector.selectedOptions[0]?.dataset.url;
            if (url) window.location.href = url;
        });
        syncFlocks();
    </script>
</x-app-layout>
