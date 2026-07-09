@php
    $isTemplate = $isTemplate ?? false;
    $startDate = $start ? optional($start->start_date ?: $flock->start_date)->format('Y-m-d') : $flock->start_date->format('Y-m-d');
    $recordDate = $record?->record_date?->format('Y-m-d') ?: now()->toDateString();
    $ageDay = $record?->age_day;
    if (!$ageDay && $recordDate && $startDate) {
        $ageDay = max(1, \Carbon\CarbonImmutable::parse($startDate)->diffInDays(\Carbon\CarbonImmutable::parse($recordDate)) + 1);
    }
@endphp

<tr class="medicine-row hover:bg-slate-50/70">
    <td data-house-label class="sticky left-0 z-10 whitespace-nowrap border-r border-slate-200 bg-white px-3 py-2 text-center font-bold text-slate-900">
        {{ $start?->house?->house_no ? 'เล้า '.$start->house->house_no : 'เล้า {house_no}' }}
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input data-record-date type="date" name="records[{{ $houseId }}][{{ $index }}][record_date]" value="{{ $isTemplate ? now()->toDateString() : $recordDate }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500">
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input data-age-day type="number" name="records[{{ $houseId }}][{{ $index }}][age_day]" value="{{ $isTemplate ? '' : $ageDay }}" class="w-full rounded-md border-slate-200 bg-slate-50 text-right text-xs font-semibold text-slate-700 shadow-sm" readonly>
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <div class="flex gap-2">
            <select data-medicine-select name="records[{{ $houseId }}][{{ $index }}][medicine_master_id]" class="w-40 rounded-md border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">เลือกยา</option>
                @foreach ($medicines as $medicine)
                    <option value="{{ $medicine->id }}" @selected((int) ($record?->medicine_master_id ?? 0) === (int) $medicine->id)>{{ $medicine->name }}</option>
                @endforeach
            </select>
            <input type="text" name="records[{{ $houseId }}][{{ $index }}][medicine_name]" value="{{ $record?->medicine_name }}" class="min-w-36 flex-1 rounded-md border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="หรือพิมพ์ชื่อยา">
        </div>
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input type="text" name="records[{{ $houseId }}][{{ $index }}][quantity]" value="{{ $record?->quantity }}" class="w-full rounded-md border-slate-300 text-right text-xs font-semibold shadow-sm focus:border-sky-500 focus:ring-sky-500">
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input type="number" min="0" step="0.01" name="records[{{ $houseId }}][{{ $index }}][dose_per_1000_birds]" value="{{ $record?->dose_per_1000_birds }}" class="w-full rounded-md border-slate-300 text-right text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="0.00">
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input data-unit type="text" name="records[{{ $houseId }}][{{ $index }}][unit]" value="{{ $record?->unit }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="กรัม">
    </td>
    <td class="border-r border-slate-100 px-2 py-2">
        <input type="text" name="records[{{ $houseId }}][{{ $index }}][note]" value="{{ $record?->note }}" class="w-full rounded-md border-slate-300 text-xs shadow-sm focus:border-sky-500 focus:ring-sky-500">
    </td>
    <td class="px-2 py-2 text-center">
        <button type="button" class="btn-remove-row text-xs font-bold text-red-600 hover:text-red-800">ลบ</button>
    </td>
</tr>
