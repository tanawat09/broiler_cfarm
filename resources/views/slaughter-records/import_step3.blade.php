<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">ตรวจสอบและจับคู่เล้า (ขั้นตอนที่ 3/3)</h1>
                <p class="mt-1 text-sm text-slate-600">กรุณาตรวจสอบความถูกต้องและเลือกจับคู่เล้าก่อนบันทึกเข้าระบบ</p>
            </div>
            <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span class="font-bold">กรุณาตรวจสอบข้อผิดพลาด:</span>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('flocks.slaughter-records.handle-import', $flock) }}">
                @csrf
                <input type="hidden" name="upload_token" value="{{ $uploadToken }}">

                <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-500 font-semibold block">วันที่เข้าเชือด</span>
                        <span class="text-sm font-bold text-slate-800">ใช้วันที่จับจากบันทึกจับไก่ตามคันที่/เล้า</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-500 font-semibold block">จำนวนรายการที่พบ</span>
                        <span class="text-lg font-bold text-emerald-700">{{ count($parsedRows) }} แถว</span>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse min-w-[1660px]">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="w-24 px-4 py-3 font-semibold text-slate-600 text-center">คันที่/ลำดับ</th>
                                    <th class="w-32 px-4 py-3 font-semibold text-slate-600 text-center">วันที่จับไก่</th>
                                    <th class="w-36 px-4 py-3 font-semibold text-slate-600">ชื่อเล้าใน Excel</th>
                                    <th class="w-44 px-4 py-3 font-semibold text-slate-600">จับคู่เล้าในระบบ</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ไก่เข้าเชือด (ตัว)</th>
                                    <th class="w-32 px-4 py-3 font-semibold text-slate-600 text-right">น้ำหนักจริง (กก.)</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ตายขนส่ง (ตัว)</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ไก่เข้าจริง (ตัว)</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ไก่ตกราว (ตัว)</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">% ตกราว</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">ไก่มีปัญหา (ตัว)</th>
                                    <th class="w-28 px-4 py-3 font-semibold text-slate-600 text-right">% มีปัญหา</th>
                                    <th class="w-32 px-4 py-3 font-semibold text-slate-600 text-right">น้ำหนักไก่ตาย (กก.)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($parsedRows as $index => $row)
                                    <tr class="hover:bg-slate-50/40">
                                        <td class="px-4 py-2 text-center">
                                            <input type="number" min="1" name="records[{{ $index }}][sequence]" value="{{ $row['sequence'] }}" class="w-20 rounded-md border-gray-300 bg-slate-50 text-xs text-center py-1 font-mono font-semibold" readonly required>
                                        </td>

                                        <td class="px-4 py-2 text-center whitespace-nowrap">
                                            @if (!empty($row['catch_date']))
                                                <span class="font-mono font-semibold text-emerald-700">{{ thai_date($row['catch_date']) }}</span>
                                            @else
                                                <span class="font-semibold text-red-600">ไม่พบวันที่</span>
                                            @endif
                                        </td>

                                        <!-- Raw House Name -->
                                        <td class="px-4 py-2 text-slate-900 font-semibold font-mono">
                                            {{ $row['raw_house_name'] }}
                                            <input type="hidden" name="records[{{ $index }}][raw_house_name]" value="{{ $row['raw_house_name'] }}">
                                        </td>

                                        <!-- House Dropdown mapping -->
                                        <td class="px-4 py-2">
                                            <select name="records[{{ $index }}][house_id]" class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 font-semibold" required>
                                                <option value="">-- กรุณาจับคู่เล้า --</option>
                                                @foreach ($availableHouses as $house)
                                                    <option value="{{ $house->id }}" @selected($row['matched_house_id'] == $house->id)>
                                                        เล้า {{ $house->house_no }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <!-- Slaughter Birds -->
                                        <td class="px-4 py-2">
                                            <input type="number" min="0" name="records[{{ $index }}][slaughter_birds]" value="{{ $row['slaughter_birds'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono" required>
                                        </td>

                                        <!-- Actual Weight -->
                                        <td class="px-4 py-2">
                                            <input type="number" step="0.01" min="0" name="records[{{ $index }}][actual_weight]" value="{{ $row['actual_weight'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono font-semibold" required>
                                        </td>

                                        <!-- DOA Birds -->
                                        <td class="px-4 py-2">
                                            <input type="number" min="0" name="records[{{ $index }}][doa_birds]" value="{{ $row['doa_birds'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono text-red-650" required>
                                        </td>

                                        <!-- Net Birds -->
                                        <td class="px-4 py-2">
                                            <input type="number" min="0" name="records[{{ $index }}][net_birds]" value="{{ $row['net_birds'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono text-emerald-700" required>
                                        </td>

                                        <!-- Condemned Birds -->
                                        <td class="px-4 py-2">
                                            <input type="number" min="0" name="records[{{ $index }}][condemned_birds]" value="{{ $row['condemned_birds'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono text-amber-700" required>
                                        </td>

                                        <!-- Condemned Percent -->
                                        <td class="px-4 py-2">
                                            <input type="number" step="0.01" min="0" name="records[{{ $index }}][condemned_percent]" value="{{ $row['condemned_percent'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono" required>
                                        </td>

                                        <!-- Problem Birds -->
                                        <td class="px-4 py-2">
                                            <input type="number" min="0" name="records[{{ $index }}][problem_birds]" value="{{ $row['problem_birds'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono text-purple-700" required>
                                        </td>

                                        <!-- Problem Percent -->
                                        <td class="px-4 py-2">
                                            <input type="number" step="0.01" min="0" name="records[{{ $index }}][problem_percent]" value="{{ $row['problem_percent'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono" required>
                                        </td>

                                        <!-- Dead Weight -->
                                        <td class="px-4 py-2">
                                            <input type="number" step="0.01" min="0" name="records[{{ $index }}][dead_weight]" value="{{ $row['dead_weight'] }}" class="w-full rounded-md border-gray-300 text-xs text-right py-1 font-mono text-slate-650" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        ยกเลิกการนำเข้า
                    </a>
                    <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="h-4.5 w-4.5 text-white/80 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ยืนยันการบันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
