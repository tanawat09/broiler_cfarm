<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">เพิ่มบันทึกจับไก่ขาย</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        @php
            $saleDate = old('sale_date', optional($saleRecord->sale_date)->toDateString() ?: now()->toDateString());
        @endphp
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-5 grid gap-4 md:grid-cols-4">
                <div class="rounded-md border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-sm font-medium text-emerald-800">ฟาร์ม</p>
                    <p class="mt-1 text-xl font-semibold text-emerald-950">{{ $flock->farm->farm_name }}</p>
                </div>
                <div class="rounded-md border border-sky-100 bg-sky-50 p-4">
                    <p class="text-sm font-medium text-sky-800">รุ่นการเลี้ยง</p>
                    <p class="mt-1 text-xl font-semibold text-sky-950">{{ $flock->flock_code }}</p>
                </div>
                <div class="rounded-md border border-amber-100 bg-amber-50 p-4">
                    <p class="text-sm font-medium text-amber-800">ไก่เริ่มต้นรวม</p>
                    <p class="mt-1 text-xl font-semibold text-amber-950">{{ number_format($flock->initial_birds) }}</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-4">
                    <p class="text-sm font-medium text-slate-600">จำนวนเล้า</p>
                    <p class="mt-1 text-xl font-semibold text-slate-950">{{ number_format($availableStarts->count()) }}</p>
                </div>
            </div>

            <!-- รายชื่อเล้าทั้งหมดและปุ่มยืนยัน -->
            <div class="mb-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l8.97-6.36a1.5 1.5 0 0 1 1.76 0l8.97 6.36M12.5 18h.008v.008H12.5V18Zm0-3h.008v.008H12.5V15Zm0-3h.008v.008H12.5V12Zm0-3h.008v.008H12.5V9Zm-3 9h.008v.008H9.5V18Zm0-3h.008v.008H9.5V15Zm0-3h.008v.008H9.5V12Zm0-3h.008v.008H9.5V9Z" />
                        </svg>
                        เล้าทั้งหมดในรุ่นการเลี้ยง
                    </h2>
                    <span class="rounded bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">ระบุวันที่จับและคลิกปุ่ม "ยืนยัน" เพื่อดึงข้อมูลและบันทึกทันที</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">เล้า</th>
                                <th class="px-4 py-3 font-medium">วันที่จับ</th>
                                <th class="px-4 py-3 text-right font-medium">อายุจับ</th>
                                <th class="px-4 py-3 text-right font-medium">ไก่เริ่มต้น</th>
                                <th class="px-4 py-3 text-right font-medium">สูญเสียสะสม</th>
                                <th class="px-4 py-3 text-right font-medium">จับขายแล้ว</th>
                                <th class="px-4 py-3 text-right font-medium">คงเหลือพร้อมจับ</th>
                                <th class="px-4 py-3 text-right font-medium">นน. เฉลี่ยล่าสุด</th>
                                <th class="px-4 py-3 text-right font-medium">นน. รวมคาดการณ์</th>
                                <th class="px-4 py-3 text-center font-medium">การกระทำ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($availableStarts as $start)
                                @php
                                    $context = $houseSaleContext->get($start->house_id);
                                    $remaining = $context['remaining_birds'] ?? $start->initial_birds;
                                    $loss = $context['loss_total'] ?? 0;
                                    $sold = $context['birds_sold'] ?? 0;
                                    $avgWeight = $context['latest_avg_weight'] ?? null;
                                    $estWeight = $context['estimated_total_weight'] ?? 0;
                                    $rowCatchDate = $context['catch_date'] ?? $saleDate;
                                    $placementDateStr = $context['placement_date'] ?? null;
                                    $catchAge = null;
                                    if ($placementDateStr && $rowCatchDate) {
                                        try {
                                            $pDate = \Carbon\CarbonImmutable::parse($placementDateStr);
                                            $cDate = \Carbon\CarbonImmutable::parse($rowCatchDate);
                                            $catchAge = $pDate->diffInDays($cDate);
                                        } catch (\Exception $e) {}
                                    }
                                @endphp
                                <tr id="row_house_{{ $start->house_id }}" class="house-row hover:bg-slate-50 transition-colors border-l-4 border-transparent">
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">
                                        @if ($start->house->house_name && $start->house->house_name !== 'เล้า '.$start->house->house_no && $start->house->house_name !== $start->house->house_no)
                                            เล้า {{ $start->house->house_no }} - {{ $start->house->house_name }}
                                        @else
                                            เล้า {{ $start->house->house_no }}
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <input type="date" 
                                               id="date_house_{{ $start->house_id }}" 
                                               value="{{ $rowCatchDate }}" 
                                               data-house-id="{{ $start->house_id }}"
                                               data-placement-date="{{ $placementDateStr }}"
                                               class="house-date-input w-36 rounded-md border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 py-1 px-2 text-slate-700" />
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-slate-700">
                                        <span id="age_house_{{ $start->house_id }}">
                                            {{ $catchAge !== null ? $catchAge.' วัน' : '-' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        {{ number_format($start->initial_birds) }} ตัว
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-red-600">
                                        {{ number_format($loss) }} ตัว
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">
                                        {{ number_format($sold) }} ตัว
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $remaining > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                        {{ number_format($remaining) }} ตัว
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        {{ $avgWeight ? number_format($avgWeight, 3).' กก.' : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-slate-600">
                                        {{ $estWeight ? number_format($estWeight, 2).' กก.' : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">
                                        <button type="button" 
                                                onclick="selectHouse({{ $start->house_id }}, {{ $remaining }}, {{ $avgWeight ?? 'null' }}, {{ $estWeight }})"
                                                class="confirm-house-btn inline-flex items-center justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                                            ยืนยัน
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="sale_form_panel" class="rounded-md border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300">
                <form method="POST" action="{{ route('flocks.sale-records.store', $flock) }}">
                    @include('sale-records._form')
                </form>
            </div>

            <script>
                window.selectHouse = (houseId, remaining, avgWeight, estWeight) => {
                    const houseInput = document.getElementById('house_id');
                    const birdsInput = document.getElementById('birds_sold');
                    const weightInput = document.getElementById('total_weight');
                    const priceInput = document.getElementById('price_per_kg');
                    const dateInput = document.getElementById('date_house_' + houseId);
                    const mainDateInput = document.getElementById('sale_date');
                    const form = document.querySelector('#sale_form_panel form');
                    
                    if (remaining <= 0) {
                        alert('ไม่สามารถบันทึกได้ เนื่องจากไม่มีไก่เหลืออยู่ในเล้านี้แล้ว');
                        return;
                    }
                    
                    if (priceInput && !priceInput.value) {
                        alert('กรุณากรอกราคาขายต่อ กก. ก่อนทำการยืนยัน');
                        priceInput.focus();
                        return;
                    }
                    
                    if (houseInput) {
                        houseInput.value = houseId;
                        
                        if (birdsInput) {
                            birdsInput.value = remaining;
                        }
                        
                        if (dateInput && mainDateInput) {
                            mainDateInput.value = dateInput.value;
                        }
                        
                        if (estWeight <= 0) {
                            if (weightInput) {
                                weightInput.value = '';
                                weightInput.focus();
                            }
                            // Trigger change event so the form updates
                            houseInput.dispatchEvent(new Event('change'));
                            alert('ไม่สามารถบันทึกอัตโนมัติได้เนื่องจากยังไม่มีข้อมูลน้ำหนักเฉลี่ยล่าสุด กรุณากรอกน้ำหนักรวมด้วยตนเองในฟอร์มด้านล่าง');
                            
                            const formPanel = document.getElementById('sale_form_panel');
                            if (formPanel) {
                                formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            }
                            return;
                        }
                        
                        if (weightInput) {
                            weightInput.value = Number(estWeight).toFixed(2);
                        }
                        
                        // Trigger change event to sync the context panel and trigger calculations
                        houseInput.dispatchEvent(new Event('change'));
                        
                        const houseRow = document.getElementById('row_house_' + houseId);
                        const houseName = houseRow ? houseRow.querySelector('td').textContent.trim() : 'เล้า';
                        const selectedDate = dateInput ? dateInput.value : '';
                        
                        // Format date to local standard just for nicer visual display in confirmation
                        let formattedDate = selectedDate;
                        try {
                            const dateParts = selectedDate.split('-');
                            if (dateParts.length === 3) {
                                formattedDate = `${dateParts[2]}/${dateParts[1]}/${Number(dateParts[0]) + 543}`;
                            }
                        } catch (e) {}
                        
                        // Confirm with the user before saving
                        if (confirm(`ยืนยันการบันทึกข้อมูลจับไก่ขายสำหรับ ${houseName}?\n- วันที่จับ: ${formattedDate}\n- จำนวนไก่: ${new Intl.NumberFormat('th-TH').format(remaining)} ตัว\n- น้ำหนักรวมคาดการณ์: ${new Intl.NumberFormat('th-TH').format(estWeight)} กก.`)) {
                            if (form) {
                                form.submit();
                            }
                        }
                    }
                };

                const parseLocalDate = (value) => {
                    if (!value) {
                        return null;
                    }

                    const parts = String(value).slice(0, 10).split('-').map(Number);
                    if (parts.length !== 3 || parts.some(Number.isNaN)) {
                        return null;
                    }

                    return new Date(parts[0], parts[1] - 1, parts[2], 12, 0, 0, 0);
                };

                const calculateCatchAge = (placementDateStr, catchDateStr) => {
                    const placementDate = parseLocalDate(placementDateStr);
                    const catchDate = parseLocalDate(catchDateStr);

                    if (!placementDate || !catchDate) {
                        return null;
                    }

                    const diffTime = catchDate.getTime() - placementDate.getTime();

                    return Math.round(diffTime / (1000 * 60 * 60 * 24));
                };

                // Dynamic catch age calculation
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.house-date-input').forEach(input => {
                        input.addEventListener('change', function() {
                            const houseId = this.dataset.houseId;
                            const placementDateStr = this.dataset.placementDate;
                            const ageSpan = document.getElementById('age_house_' + houseId);
                            
                            if (!ageSpan) return;
                            
                            const diffDays = calculateCatchAge(placementDateStr, this.value);
                            if (diffDays !== null) {
                                ageSpan.textContent = diffDays >= 0 ? diffDays + ' วัน' : '0 วัน';
                            } else {
                                ageSpan.textContent = '-';
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
</x-app-layout>
