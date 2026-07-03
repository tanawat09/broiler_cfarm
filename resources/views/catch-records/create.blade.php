<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">เพิ่มบันทึกการจับไก่ (คีย์ค่าจับ)</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
            <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1600px] px-3 sm:px-4 lg:px-6">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span class="font-bold">กรุณาตรวจสอบข้อมูล:</span>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('flocks.catch-records.store', $flock) }}" id="catch-records-form">
                @csrf

                <!-- Dynamic Rows table for trucks/trips -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-5 py-4">
                        <h2 class="text-sm font-bold text-slate-900">รายละเอียดเที่ยวรถจับไก่</h2>
                        <p class="text-xs text-slate-500 mt-0.5">ระบุรายละเอียดการจับไก่ของรถแต่ละคันเป็นรายแถว (สามารถจับหลายคันและสลับเล้า/วันที่ได้ตามจริง)</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse min-w-[1400px]" id="catch-rows-table">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="w-40 px-4 py-3 font-semibold text-slate-600">วันที่จับไก่</th>
                                    <th class="w-36 px-4 py-3 font-semibold text-slate-600">เลือกเล้า</th>
                                    <th class="w-20 px-4 py-3 font-semibold text-slate-600 text-center">ลำดับที่</th>
                                    <th class="w-32 px-4 py-3 font-semibold text-slate-600">ทะเบียนรถ</th>
                                    <th class="w-24 px-4 py-3 font-semibold text-slate-600 text-right">จำนวนตัว</th>
                                    <th class="w-24 px-4 py-3 font-semibold text-slate-600 text-right">จำนวนกล่อง</th>
                                    <th class="w-36 px-4 py-3 font-semibold text-slate-600">ชนิดรถ</th>
                                    <th class="w-44 px-4 py-3 font-semibold text-slate-600">ทีมจับไก่</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ค่าจับ (บาท)</th>
                                    <th class="px-4 py-3 font-semibold text-slate-600">หมายเหตุ / อื่นๆ</th>
                                    <th class="w-16 px-4 py-3 font-semibold text-slate-600 text-center">ลบ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="catch-records-tbody">
                                @if (old('items'))
                                    @foreach (old('items') as $index => $item)
                                        <tr class="catch-row hover:bg-slate-50/40">
                                            <td class="px-4 py-2.5">
                                                <input type="date" name="items[{{ $index }}][catch_date]" value="{{ $item['catch_date'] }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <select name="items[{{ $index }}][house_id]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 house-select" required>
                                                    <option value="">เลือกเล้า</option>
                                                    @foreach ($availableStarts as $start)
                                                        <option value="{{ $start->house_id }}" @selected($item['house_id'] == $start->house_id)>
                                                            เล้า {{ $start->house->house_no }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2.5 text-center font-mono">
                                                <input type="number" name="items[{{ $index }}][sequence]" value="{{ $item['sequence'] }}" class="w-14 rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center py-1 row-sequence" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="text" name="items[{{ $index }}][license_plate]" value="{{ $item['license_plate'] }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" placeholder="เช่น กข-1234" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" min="0" name="items[{{ $index }}][birds_count]" value="{{ $item['birds_count'] }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right py-1" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" min="0" name="items[{{ $index }}][boxes_count]" value="{{ $item['boxes_count'] }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right py-1" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <select name="items[{{ $index }}][vehicle_type]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 vehicle-select">
                                                    <option value="รถ 10 ล้อ" @selected(($item['vehicle_type'] ?? '') == 'รถ 10 ล้อ')>รถ 10 ล้อ</option>
                                                    <option value="รถ 6 ล้อ" @selected(($item['vehicle_type'] ?? '') == 'รถ 6 ล้อ')>รถ 6 ล้อ</option>
                                                    <option value="รถกระบะ" @selected(($item['vehicle_type'] ?? '') == 'รถกระบะ')>รถกระบะ</option>
                                                    <option value="อื่นๆ" @selected(($item['vehicle_type'] ?? '') == 'อื่นๆ')>อื่นๆ</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <select name="items[{{ $index }}][catching_team]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1">
                                                    <option value="">เลือกทีมจับ</option>
                                                    @foreach ($catchingTeams as $team)
                                                        <option value="{{ $team->name }}" @selected(($item['catching_team'] ?? '') == $team->name)>{{ $team->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2.5 text-right">
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][catching_fee]" value="{{ $item['catching_fee'] }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-semibold py-1 fee-input" placeholder="0.00" required>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="text" name="items[{{ $index }}][note]" value="{{ $item['note'] ?? '' }}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" placeholder="หมายเหตุ">
                                            </td>
                                            <td class="px-4 py-2.5 text-center">
                                                <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn">
                                                    <svg class="h-4.5 w-4.5 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-12 12m0-12l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Add catch row button placed at the bottom of the input table -->
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30 flex justify-center">
                        <button type="button" id="add-row-btn" class="group inline-flex items-center gap-1.5 rounded-xl border border-slate-350 bg-white px-4 py-2 text-xs font-bold text-slate-750 shadow-sm hover:bg-slate-50 transition-colors">
                            <svg class="h-4.5 w-4.5 text-slate-550 group-hover:text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            เพิ่มเที่ยวรถจับไก่
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        ยกเลิก
                    </a>
                    <x-primary-button>
                        บันทึกค่าจับไก่
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nextSequences = @json($nextSequences);
            const tbody = document.getElementById('catch-records-tbody');
            const addRowBtn = document.getElementById('add-row-btn');
            const defaultCatchDate = "{{ $catchDate }}";

            // Catching teams dropdown options list from controller
            const catchingTeams = @json($catchingTeams);

            let rowIndex = {{ old('items') ? count(old('items')) : 0 }};

            const getNextSequence = (houseId) => {
                if (!houseId) return 1;

                // Find number of rows currently in table selecting this house_id
                let currentRowsCount = 0;
                tbody.querySelectorAll('.catch-row').forEach((row) => {
                    const select = row.querySelector('.house-select');
                    if (select && select.value === houseId) {
                        currentRowsCount++;
                    }
                });

                const dbNextSeq = Number(nextSequences[houseId] || 1);
                return dbNextSeq + currentRowsCount;
            };

            const addRow = () => {
                const tr = document.createElement('tr');
                tr.className = 'catch-row hover:bg-slate-50/40';

                // Render catching teams options dynamically
                let teamOptions = '<option value="">เลือกทีมจับ</option>';
                catchingTeams.forEach(team => {
                    teamOptions += `<option value="${team.name}">${team.name}</option>`;
                });

                tr.innerHTML = `
                    <td class="px-4 py-2.5">
                        <input type="date" name="items[${rowIndex}][catch_date]" value="${defaultCatchDate}" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" required>
                    </td>
                    <td class="px-4 py-2.5">
                        <select name="items[${rowIndex}][house_id]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 house-select" required>
                            <option value="">เลือกเล้า</option>
                            @foreach ($availableStarts as $start)
                                <option value="{{ $start->house_id }}">
                                    เล้า {{ $start->house->house_no }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-2.5 text-center font-mono">
                        <input type="number" name="items[${rowIndex}][sequence]" value="1" class="w-14 rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-center py-1 row-sequence" required>
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="text" name="items[${rowIndex}][license_plate]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" placeholder="เช่น กข-1234" required>
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="number" min="0" name="items[${rowIndex}][birds_count]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right py-1" required value="0">
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="number" min="0" name="items[${rowIndex}][boxes_count]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right py-1" required value="486">
                    </td>
                    <td class="px-4 py-2.5">
                        <select name="items[${rowIndex}][vehicle_type]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 vehicle-select">
                            <option value="รถ 10 ล้อ" selected>รถ 10 ล้อ</option>
                            <option value="รถ 6 ล้อ">รถ 6 ล้อ</option>
                            <option value="รถกระบะ">รถกระบะ</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </td>
                    <td class="px-4 py-2.5">
                        <select name="items[${rowIndex}][catching_team]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1">
                            ${teamOptions}
                        </select>
                    </td>
                    <td class="px-4 py-2.5 text-right">
                        <input type="number" step="0.01" min="0" name="items[${rowIndex}][catching_fee]" value="1600" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-right font-semibold py-1 fee-input" placeholder="0.00" required>
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="text" name="items[${rowIndex}][note]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1" placeholder="หมายเหตุ">
                    </td>
                    <td class="px-4 py-2.5 text-center">
                        <button type="button" class="text-red-650 hover:text-red-900 remove-row-btn">
                            <svg class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);

                // Auto-fill catch fee based on vehicle type
                const vehicleSelect = tr.querySelector('.vehicle-select');
                const feeInput = tr.querySelector('.fee-input');
                vehicleSelect.addEventListener('change', () => {
                    if (vehicleSelect.value === 'รถ 10 ล้อ') {
                        feeInput.value = '1600';
                    } else if (vehicleSelect.value === 'รถ 6 ล้อ') {
                        feeInput.value = '1400';
                    }
                });

                // Recalculate sequence when house changes
                const houseSelect = tr.querySelector('.house-select');
                const seqInput = tr.querySelector('.row-sequence');
                houseSelect.addEventListener('change', () => {
                    seqInput.value = getNextSequence(houseSelect.value);
                });

                tr.querySelector('.remove-row-btn').addEventListener('click', () => {
                    tr.remove();
                });

                rowIndex++;
            };

            addRowBtn.addEventListener('click', addRow);

            if (tbody.querySelectorAll('.catch-row').length === 0) {
                addRow();
            }

            // Bind vehicle type change and house change events on any existing old input rows
            tbody.querySelectorAll('.catch-row').forEach((row) => {
                const vehicleSelect = row.querySelector('.vehicle-select');
                const feeInput = row.querySelector('.fee-input');
                if (vehicleSelect && feeInput) {
                    vehicleSelect.addEventListener('change', () => {
                        if (vehicleSelect.value === 'รถ 10 ล้อ') {
                            feeInput.value = '1600';
                        } else if (vehicleSelect.value === 'รถ 6 ล้อ') {
                            feeInput.value = '1400';
                        }
                    });
                }

                const houseSelect = row.querySelector('.house-select');
                const seqInput = row.querySelector('.row-sequence');
                if (houseSelect && seqInput) {
                    houseSelect.addEventListener('change', () => {
                        seqInput.value = getNextSequence(houseSelect.value);
                    });
                }

                const removeBtn = row.querySelector('.remove-row-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', () => {
                        row.remove();
                    });
                }
            });
        });
    </script>
</x-app-layout>
