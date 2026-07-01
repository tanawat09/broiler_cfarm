@csrf

@php
    $selectedHouseId = old('house_id', $saleRecord->house_id);
    $saleDate = old('sale_date', optional($saleRecord->sale_date)->toDateString() ?: now()->toDateString());
    $houseSaleContext = $houseSaleContext ?? collect();
@endphp

<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
    <div>
        <x-input-label for="sale_date" value="วันที่จับขาย" />
        <x-text-input id="sale_date" name="sale_date" type="date" class="mt-1 block w-full" :value="$saleDate" required />
        <x-input-error class="mt-2" :messages="$errors->get('sale_date')" />
    </div>

    <div>
        <x-input-label for="house_id" value="เล้า" />
        <select id="house_id" name="house_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
            <option value="">เลือกเล้า</option>
            @foreach ($availableStarts as $start)
                @php $context = $houseSaleContext->get($start->house_id); @endphp
                <option
                    value="{{ $start->house_id }}"
                    data-remaining="{{ $context['remaining_birds'] ?? $start->initial_birds }}"
                    data-avg-weight="{{ $context['latest_avg_weight'] ?? '' }}"
                    data-estimated-weight="{{ $context['estimated_total_weight'] ?? 0 }}"
                    @selected((int) $selectedHouseId === (int) $start->house_id)
                >
                    @if ($start->house->house_name && $start->house->house_name !== 'เล้า '.$start->house->house_no && $start->house->house_name !== $start->house->house_no)
                        เล้า {{ $start->house->house_no }} - {{ $start->house->house_name }}
                    @else
                        เล้า {{ $start->house->house_no }}
                    @endif
                    - คงเหลือ {{ number_format((int) ($context['remaining_birds'] ?? $start->initial_birds)) }} ตัว
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('house_id')" />
    </div>

    <div>
        <x-input-label for="birds_sold" value="จำนวนไก่จับขาย" />
        <x-text-input id="birds_sold" name="birds_sold" type="number" min="1" class="sale-calc mt-1 block w-full text-right" :value="old('birds_sold', $saleRecord->birds_sold)" required />
        <x-input-error class="mt-2" :messages="$errors->get('birds_sold')" />
    </div>

    <div>
        <x-input-label for="total_weight" value="น้ำหนักรวม (กก.)" />
        <x-text-input id="total_weight" name="total_weight" type="number" min="0.01" step="0.01" class="sale-calc mt-1 block w-full text-right" :value="old('total_weight', $saleRecord->total_weight)" required />
        <x-input-error class="mt-2" :messages="$errors->get('total_weight')" />
    </div>

    <div>
        <x-input-label for="price_per_kg" value="ราคาขายต่อกก." />
        <x-text-input id="price_per_kg" name="price_per_kg" type="number" min="0" step="0.01" class="sale-calc mt-1 block w-full text-right" :value="old('price_per_kg', $saleRecord->price_per_kg)" required />
        <x-input-error class="mt-2" :messages="$errors->get('price_per_kg')" />
    </div>

    <div>
        <x-input-label for="avg_weight_preview" value="น้ำหนักเฉลี่ย (กก./ตัว)" />
        <input id="avg_weight_preview" type="text" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-emerald-50 text-right text-sm shadow-sm" value="{{ $saleRecord->avg_weight ? number_format((float) $saleRecord->avg_weight, 3) : '0.000' }}">
    </div>

    <div>
        <x-input-label for="total_amount_preview" value="ยอดเงินรวม" />
        <input id="total_amount_preview" type="text" readonly class="mt-1 block w-full rounded-md border-gray-200 bg-emerald-50 text-right text-sm shadow-sm" value="{{ $saleRecord->total_amount ? number_format((float) $saleRecord->total_amount, 2) : '0.00' }}">
    </div>

    <div class="md:col-span-2 xl:col-span-4">
        <x-input-label for="note" value="หมายเหตุ" />
        <textarea id="note" name="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('note', $saleRecord->note) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('note')" />
    </div>
</div>

<div id="house_context_panel" class="mt-5 hidden rounded-md border border-slate-200 bg-slate-50 p-4">
    <div class="grid gap-4 md:grid-cols-4">
        <div>
            <p class="text-xs font-medium text-slate-500">ไก่เริ่มต้น</p>
            <p id="ctx_initial" class="mt-1 text-lg font-semibold text-slate-900">-</p>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500">สูญเสียสะสม</p>
            <p id="ctx_loss" class="mt-1 text-lg font-semibold text-slate-900">-</p>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500">จับขายแล้ว</p>
            <p id="ctx_sold" class="mt-1 text-lg font-semibold text-slate-900">-</p>
        </div>
        <div>
            <p class="text-xs font-medium text-slate-500">คงเหลือพร้อมจับ</p>
            <p id="ctx_remaining" class="mt-1 text-lg font-semibold text-emerald-700">-</p>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ request()->routeIs('flocks.close.show') ? route('flocks.close.show', $flock) : route('flocks.sale-records.index', $flock) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ยกเลิก
    </a>
    <x-primary-button>บันทึก</x-primary-button>
</div>

<script>
    (() => {
        const houseContext = @json($houseSaleContext);
        const houseInput = document.getElementById('house_id');
        const birdsInput = document.getElementById('birds_sold');
        const weightInput = document.getElementById('total_weight');
        const priceInput = document.getElementById('price_per_kg');
        const avgPreview = document.getElementById('avg_weight_preview');
        const amountPreview = document.getElementById('total_amount_preview');
        const panel = document.getElementById('house_context_panel');
        const formatter = new Intl.NumberFormat('th-TH');

        const calculate = () => {
            const birds = Number(birdsInput.value || 0);
            const weight = Number(weightInput.value || 0);
            const price = Number(priceInput.value || 0);

            avgPreview.value = birds > 0 ? (weight / birds).toFixed(3) : '0.000';
            amountPreview.value = (weight * price).toFixed(2);
        };

        const fillHouseData = () => {
            const context = houseContext[houseInput.value];

            // Update row highlighting for house table (if it exists)
            document.querySelectorAll('.house-row').forEach(row => {
                row.classList.remove('bg-emerald-50/70', 'border-emerald-500');
                row.classList.add('border-transparent');
            });
            const selectedRow = document.getElementById('row_house_' + houseInput.value);
            if (selectedRow) {
                selectedRow.classList.remove('border-transparent');
                selectedRow.classList.add('bg-emerald-50/70', 'border-emerald-500');
            }

            if (!context) {
                panel.classList.add('hidden');
                calculate();
                return;
            }

            panel.classList.remove('hidden');
            document.getElementById('ctx_initial').textContent = formatter.format(context.initial_birds || 0);
            document.getElementById('ctx_loss').textContent = formatter.format(context.loss_total || 0);
            document.getElementById('ctx_sold').textContent = formatter.format(context.birds_sold || 0);
            document.getElementById('ctx_remaining').textContent = formatter.format(context.remaining_birds || 0);

            if (!birdsInput.value) {
                birdsInput.value = context.remaining_birds || '';
            }

            if (!weightInput.value && Number(context.estimated_total_weight || 0) > 0) {
                weightInput.value = Number(context.estimated_total_weight).toFixed(2);
            }

            calculate();
        };

        document.querySelectorAll('.sale-calc').forEach((input) => {
            input.addEventListener('input', calculate);
        });

        houseInput?.addEventListener('change', fillHouseData);
        fillHouseData();
    })();
</script>
