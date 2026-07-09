<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between py-2">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center rounded-lg bg-sky-500/10 p-2 text-sky-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6v5l4 7a4 4 0 0 1-3.5 6h-7A4 4 0 0 1 5 15l4-7V3Zm1 9h4m-5 4h6" />
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">บันทึกการให้ยา</h1>
                        <p class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            {{ $flock->flock_code }} | {{ $flock->farm->farm_name }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('medicine-masters.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    มาสเตอร์ยา
                </a>
                <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    กลับรายละเอียดรุ่น
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[99%] px-2 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">กรุณาตรวจสอบข้อมูลการให้ยาอีกครั้ง</div>
            @endif

            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-[240px_280px_1fr] lg:items-end">
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" class="text-xs font-semibold text-slate-700" />
                        <select id="farm_selector" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}" @selected((int) $farm->id === (int) $flock->farm_id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่น" class="text-xs font-semibold text-slate-700" />
                        <select id="flock_selector" class="mt-1 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            @foreach ($flockOptions as $option)
                                <option value="{{ $option->id }}" data-farm-id="{{ $option->farm_id }}" data-url="{{ route('flocks.medicine-records.index', $option) }}" @selected((int) $option->id === (int) $flock->id)>
                                    {{ $option->flock_code }} @if ($option->farm) - {{ $option->farm->farm_name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 lg:text-right">
                        <div>วันที่เริ่มรุ่น: {{ thai_date($flock->start_date) }}</div>
                        <div class="mt-1 text-sky-700">
                            วันที่ให้ยาล่าสุดของรุ่นนี้:
                            <span class="font-extrabold">{{ $latestMedicineDate ? thai_date($latestMedicineDate) : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('flocks.medicine-records.store', $flock) }}" id="medicine-records-form">
                @csrf

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">ตารางบันทึกการให้ยารายเล้า</h2>
                            <p class="mt-0.5 text-xs text-slate-500">เพิ่มได้หลายรายการต่อเล้า รองรับยา วัคซีน และอาหารเสริม</p>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-sky-700">
                            บันทึกข้อมูลทั้งหมด
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1180px] border-collapse text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-slate-700">
                                    <th class="sticky left-0 z-10 w-24 border-r border-slate-200 bg-slate-50 px-3 py-3 text-center">เล้า</th>
                                    <th class="w-36 border-r border-slate-100 px-3 py-3 text-center">วันที่ให้ยา</th>
                                    <th class="w-20 border-r border-slate-100 px-3 py-3 text-right">อายุ</th>
                                    <th class="w-64 border-r border-slate-100 px-3 py-3">ยาให้</th>
                                    <th class="w-36 border-r border-slate-100 px-3 py-3 text-right">ปริมาณ</th>
                                    <th class="w-36 border-r border-slate-100 px-3 py-3 text-right">อัตรา/1,000 ตัว</th>
                                    <th class="w-28 border-r border-slate-100 px-3 py-3">หน่วย</th>
                                    <th class="border-r border-slate-100 px-3 py-3">หมายเหตุ</th>
                                    <th class="w-20 px-3 py-3 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($flock->flockHouseStarts->sortBy('house.house_no') as $start)
                                    @php
                                        $houseId = $start->house_id;
                                        $houseRecords = $recordsByHouse->get($houseId) ?: collect();
                                    @endphp
                                    <tr class="bg-slate-50/70">
                                        <td colspan="9" class="px-3 py-2 text-sm font-bold text-slate-800">
                                            เล้า {{ $start->house->house_no }}
                                            <span class="ml-2 text-xs font-normal text-slate-500">จำนวน {{ number_format($houseRecords->count()) }} รายการ</span>
                                        </td>
                                    </tr>
                                    <tbody id="house-rows-{{ $houseId }}" data-house-id="{{ $houseId }}" data-start-date="{{ optional($start->start_date ?: $flock->start_date)->format('Y-m-d') }}" data-house-no="{{ $start->house->house_no }}">
                                        @forelse ($houseRecords as $index => $record)
                                            @include('medicine-records._row', ['houseId' => $houseId, 'index' => $index, 'record' => $record, 'start' => $start, 'medicines' => $medicines, 'flock' => $flock])
                                        @empty
                                            @include('medicine-records._row', ['houseId' => $houseId, 'index' => 0, 'record' => null, 'start' => $start, 'medicines' => $medicines, 'flock' => $flock])
                                        @endforelse
                                    </tbody>
                                    <tr class="bg-sky-50/50">
                                        <td colspan="9" class="px-3 py-2">
                                            <button type="button" class="btn-add-row inline-flex items-center rounded-md px-2 py-1 text-xs font-bold text-sky-700 hover:bg-sky-100" data-house-id="{{ $houseId }}">
                                                + เพิ่มรายการยา เล้า {{ $start->house->house_no }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-sky-700">
                        บันทึกข้อมูลทั้งหมด
                    </button>
                </div>
            </form>
        </div>
    </div>

    <template id="medicine-row-template">
        @include('medicine-records._row', ['houseId' => '{house_id}', 'index' => '{index}', 'record' => null, 'start' => null, 'medicines' => $medicines, 'flock' => $flock, 'isTemplate' => true])
    </template>

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

        const medicineUnits = @json($medicines->mapWithKeys(fn ($medicine) => [$medicine->id => $medicine->default_unit])->all());

        function parseDate(value) {
            if (!value) return null;
            const [year, month, day] = value.split('-').map(Number);
            return new Date(year, month - 1, day);
        }

        function refreshRow(row) {
            const tbody = row.closest('tbody[data-start-date]');
            const recordDate = parseDate(row.querySelector('[data-record-date]')?.value);
            const startDate = parseDate(tbody?.dataset.startDate);
            const ageInput = row.querySelector('[data-age-day]');
            if (recordDate && startDate && ageInput) {
                const diff = Math.floor((recordDate - startDate) / 86400000) + 1;
                ageInput.value = Number.isFinite(diff) && diff > 0 ? diff : '';
            }

            const medicineSelect = row.querySelector('[data-medicine-select]');
            const unitInput = row.querySelector('[data-unit]');
            if (medicineSelect && unitInput && !unitInput.value) {
                unitInput.value = medicineUnits[medicineSelect.value] || '';
            }
        }

        document.addEventListener('change', (event) => {
            if (event.target.matches('[data-record-date], [data-medicine-select]')) {
                refreshRow(event.target.closest('tr'));
            }
        });

        document.addEventListener('click', (event) => {
            const addButton = event.target.closest('.btn-add-row');
            if (addButton) {
                const houseId = addButton.dataset.houseId;
                const tbody = document.getElementById(`house-rows-${houseId}`);
                const index = tbody.querySelectorAll('tr.medicine-row').length;
                const html = document.getElementById('medicine-row-template').innerHTML
                    .replaceAll('{house_id}', houseId)
                    .replaceAll('{index}', index);
                tbody.insertAdjacentHTML('beforeend', html);
                const row = tbody.lastElementChild;
                const houseNoCell = row.querySelector('[data-house-label]');
                if (houseNoCell) houseNoCell.textContent = `เล้า ${tbody.dataset.houseNo}`;
                refreshRow(row);
                return;
            }

            const removeButton = event.target.closest('.btn-remove-row');
            if (removeButton) {
                const row = removeButton.closest('tr');
                const tbody = row.closest('tbody');
                if (tbody.querySelectorAll('tr.medicine-row').length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('input, textarea').forEach(input => input.value = '');
                    row.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                }
            }
        });

        document.querySelectorAll('tr.medicine-row').forEach(refreshRow);
    </script>
</x-app-layout>
