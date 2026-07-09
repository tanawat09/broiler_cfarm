@csrf

<input type="hidden" name="start_date" value="{{ old('start_date', optional($flock->start_date)->format('Y-m-d') ?: now()->toDateString()) }}">

<div class="grid gap-4 lg:grid-cols-3">
    @isset($farms)
        <div>
            <x-input-label for="farm_id" value="ฟาร์ม" />
            <select
                id="farm_id"
                name="farm_id"
                onchange="window.location='{{ route('flocks.create') }}?farm_id='+this.value"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                @foreach ($farms as $farm)
                    <option value="{{ $farm->id }}" @selected((int) old('farm_id', $selectedFarm?->id) === $farm->id)>
                        {{ $farm->farm_name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('farm_id')" />
        </div>
    @else
        <div>
            <x-input-label value="ฟาร์ม" />
            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                {{ $flock->farm->farm_name }}
            </div>
        </div>
    @endisset

    <div>
        <x-input-label for="flock_code" value="รหัสรุ่น" />
        <x-text-input id="flock_code" name="flock_code" type="text" class="mt-1 block w-full" :value="old('flock_code', $flock->flock_code)" required />
        <x-input-error class="mt-2" :messages="$errors->get('flock_code')" />
    </div>

    <div>
        <x-input-label for="chicken_type" value="ประเภทไก่" />
        <x-text-input id="chicken_type" name="chicken_type" type="text" class="mt-1 block w-full" :value="old('chicken_type', $flock->chicken_type ?: 'ไก่เนื้อ')" required />
        <x-input-error class="mt-2" :messages="$errors->get('chicken_type')" />
    </div>

    <x-input-error class="lg:col-span-3" :messages="$errors->get('start_date')" />
</div>

<div class="mt-8">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">รายละเอียดลงไก่รายเล้า</h2>
        <div class="text-sm text-gray-500">ช่องไก่เข้าใช้เป็นจำนวนไก่เริ่มต้นของเล้านั้น</div>
    </div>

    <div class="mb-3 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        ถ้าเล้ามีข้อมูลลูกไก่มากกว่า 1 แถว หน้านี้จะแสดงเป็นยอดรวมรายเล้าเท่านั้น และจะล็อกช่องรายละเอียดไว้
        หากต้องการแก้ข้อมูลรายแถว ให้ไปที่
        <a href="{{ route('flocks.placements.index', $flock) }}" class="font-semibold underline">ข้อมูลลูกไก่</a>
    </div>

    <div class="overflow-hidden rounded-md border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-[2050px] divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">เล้า</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">วันที่ลง</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">วันที่จับ</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">อายุจับ</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">ไก่เข้า</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">M</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">FM</th>
                        <th colspan="2" class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">เกรด M</th>
                        <th colspan="2" class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">เกรด FM</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">แหล่งลูกไก่</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">เกรดลูกไก่</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">รหัส</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">รุ่น</th>
                        <th rowspan="2" class="border-r border-gray-200 px-3 py-3 text-center font-medium text-gray-600">เพศ</th>
                        <th rowspan="2" class="px-3 py-3 text-center font-medium text-gray-600">พันธุ์</th>
                    </tr>
                    <tr>
                        <th class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">A</th>
                        <th class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">B</th>
                        <th class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">A</th>
                        <th class="border-r border-gray-200 px-3 py-2 text-center font-medium text-gray-600">B</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($houses as $house)
                        @php
                            $placement = $placementValues->get($house->id);
                            $isAggregatePlacement = (bool) data_get($placement, '_is_aggregate', false);
                            $aggregateInputAttributes = $isAggregatePlacement ? 'readonly' : '';
                            $aggregateSelectAttributes = $isAggregatePlacement ? 'disabled' : '';
                            $aggregateCellClass = $isAggregatePlacement ? ' bg-amber-50/50' : '';
                            $value = fn ($field, $default = '') => old('placements.'.$house->id.'.'.$field, data_get($placement, $field, $default));
                            $dateValue = function ($field) use ($house, $placement) {
                                $oldValue = old('placements.'.$house->id.'.'.$field);
                                $storedValue = data_get($placement, $field);

                                if ($oldValue !== null) {
                                    return $oldValue;
                                }

                                return $storedValue ? \Carbon\CarbonImmutable::parse($storedValue)->format('Y-m-d') : '';
                            };
                        @endphp
                        <tr class="placement-row hover:bg-gray-50 {{ $isAggregatePlacement ? 'bg-amber-50/20' : '' }}">
                            <td class="whitespace-nowrap border-r border-gray-100 px-3 py-2 text-center font-medium text-gray-900">
                                {{ $house->house_no }}
                                @if ($isAggregatePlacement)
                                    <div class="mt-1 text-[10px] font-semibold text-amber-700">ยอดรวม</div>
                                @endif
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <input data-placement-date type="date" name="placements[{{ $house->id }}][placement_date]" value="{{ $dateValue('placement_date') }}" {!! $aggregateInputAttributes !!} class="w-32 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                                <x-input-error class="mt-1 text-left" :messages="$errors->get('placements.'.$house->id.'.placement_date')" />
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <input data-catch-date type="date" name="placements[{{ $house->id }}][catch_date]" value="{{ $dateValue('catch_date') }}" {!! $aggregateInputAttributes !!} class="w-32 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                                <x-input-error class="mt-1 text-left" :messages="$errors->get('placements.'.$house->id.'.catch_date')" />
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2 text-right">
                                <input data-catch-age type="number" min="0" step="1" name="placements[{{ $house->id }}][catch_age]" value="{{ $value('catch_age') }}" class="w-16 rounded-md border-gray-300 bg-emerald-50 text-right text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" readonly>
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2 text-right">
                                <input type="hidden" name="house_initial_birds[{{ $house->id }}]" value="{{ old('placements.'.$house->id.'.chicks_in', data_get($placement, 'chicks_in', $startValues->get($house->id, ''))) }}">
                                <input type="number" min="0" step="1" name="placements[{{ $house->id }}][chicks_in]" value="{{ old('placements.'.$house->id.'.chicks_in', data_get($placement, 'chicks_in', $startValues->get($house->id, ''))) }}" {!! $aggregateInputAttributes !!} class="w-24 rounded-md border-gray-300 text-right text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                                <x-input-error class="mt-1 text-left" :messages="$errors->get('placements.'.$house->id.'.chicks_in')" />
                            </td>
                            @foreach (['male_count', 'female_count', 'male_grade_a_count', 'male_grade_b_count', 'female_grade_a_count', 'female_grade_b_count'] as $field)
                                <td class="border-r border-gray-100 px-2 py-2 text-right">
                                    <input data-grade-field="{{ $field }}" type="number" min="0" step="1" name="placements[{{ $house->id }}][{{ $field }}]" value="{{ $value($field) }}" {!! $aggregateInputAttributes !!} class="w-20 rounded-md border-gray-300 text-right text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                                    <x-input-error class="mt-1 text-left" :messages="$errors->get('placements.'.$house->id.'.'.$field)" />
                                </td>
                            @endforeach
                            <td class="border-r border-gray-100 px-2 py-2">
                                <select name="placements[{{ $house->id }}][chick_source]" {!! $aggregateSelectAttributes !!} class="w-32 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                                    <option value="">-</option>
                                    @foreach ($chickSources as $source)
                                        <option value="{{ $source->name }}" @selected($value('chick_source') === $source->name)>{{ $source->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <input data-chick-grade type="text" name="placements[{{ $house->id }}][chick_grade]" value="{{ $value('chick_grade') }}" class="w-20 rounded-md border-gray-300 bg-emerald-50 text-center text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" readonly>
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <textarea name="placements[{{ $house->id }}][chick_code]" rows="1" {!! $aggregateInputAttributes !!} class="w-80 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">{{ $value('chick_code') }}</textarea>
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <input type="text" name="placements[{{ $house->id }}][batch_no]" value="{{ $value('batch_no') }}" {!! $aggregateInputAttributes !!} class="w-28 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                            </td>
                            <td class="border-r border-gray-100 px-2 py-2">
                                <input data-sex type="text" name="placements[{{ $house->id }}][sex]" value="{{ $value('sex') }}" class="w-20 rounded-md border-gray-300 bg-emerald-50 text-center text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" readonly>
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="placements[{{ $house->id }}][breed]" value="{{ $value('breed') }}" {!! $aggregateInputAttributes !!} class="w-24 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500{{ $aggregateCellClass }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="px-4 py-8 text-center text-gray-500">
                                ยังไม่มีเล้าที่เปิดใช้งานสำหรับฟาร์มนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-input-error class="mt-2" :messages="$errors->get('house_initial_birds')" />
    <x-input-error class="mt-2" :messages="$errors->get('placements')" />
</div>

<script>
    document.querySelectorAll('.placement-row').forEach((row) => {
        const placementDate = row.querySelector('[data-placement-date]');
        const catchDate = row.querySelector('[data-catch-date]');
        const catchAge = row.querySelector('[data-catch-age]');
        const chickGrade = row.querySelector('[data-chick-grade]');
        const sex = row.querySelector('[data-sex]');
        const gradeInputs = row.querySelectorAll('[data-grade-field]');

        const numberValue = (field) => Number(row.querySelector(`[data-grade-field="${field}"]`)?.value || 0);

        const refreshDates = () => {
            if (!placementDate?.value || !catchDate?.value) {
                if (catchAge) {
                    catchAge.value = '';
                }
                return;
            }

            const start = new Date(`${placementDate.value}T00:00:00`);
            const end = new Date(`${catchDate.value}T00:00:00`);
            const diffDays = Math.round((end - start) / 86400000);

            catchAge.value = Number.isFinite(diffDays) && diffDays >= 0 ? diffDays : '';
        };

        const refreshGradeAndSex = () => {
            const maleTotal = numberValue('male_count');
            const femaleTotal = numberValue('female_count');
            const maleA = numberValue('male_grade_a_count');
            const maleB = numberValue('male_grade_b_count');
            const femaleA = numberValue('female_grade_a_count');
            const femaleB = numberValue('female_grade_b_count');
            const gradeA = maleA + femaleA;
            const gradeB = maleB + femaleB;
            const male = maleTotal + maleA + maleB;
            const female = femaleTotal + femaleA + femaleB;

            chickGrade.value = gradeA > 0 && gradeB > 0 ? 'A,B' : (gradeA > 0 ? 'A' : (gradeB > 0 ? 'B' : ''));
            sex.value = male > 0 && female > 0 ? 'คละ' : (male > 0 ? 'ผู้' : (female > 0 ? 'เมีย' : ''));
        };

        placementDate?.addEventListener('change', refreshDates);
        catchDate?.addEventListener('change', refreshDates);
        gradeInputs.forEach((input) => input.addEventListener('input', refreshGradeAndSex));

        refreshDates();
        refreshGradeAndSex();
    });
</script>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('flocks.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ยกเลิก
    </a>
    <x-primary-button :disabled="$houses->isEmpty()">
        บันทึก
    </x-primary-button>
</div>
