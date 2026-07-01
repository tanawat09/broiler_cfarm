<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">นำเข้าข้อมูลประจำวัน (.txt)</h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('daily-records.import-submit') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Select Farm -->
                    <div>
                        <x-input-label for="farm_selector" value="เลือกฟาร์ม" class="font-bold text-slate-700" />
                        <select id="farm_selector" name="farm_id" required class="mt-2 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- กรุณาเลือกฟาร์ม --</option>
                            @foreach ($farms as $farm)
                                <option value="{{ $farm->id }}">{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select Flock -->
                    <div>
                        <x-input-label for="flock_selector" value="เลือกรุ่นการเลี้ยง" class="font-bold text-slate-700" />
                        <select id="flock_selector" name="flock_id" required class="mt-2 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- กรุณาเลือกรุ่น --</option>
                            @foreach ($flocks as $flockOption)
                                <option 
                                    value="{{ $flockOption->id }}" 
                                    data-farm-id="{{ $flockOption->farm_id }}"
                                    class="flock-option"
                                >
                                    {{ $flockOption->flock_code }} ({{ $flockOption->status === 'active' ? 'กำลังเลี้ยง' : 'ปิดรุ่นแล้ว' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Choose File -->
                    <div>
                        <x-input-label for="import_file" value="เลือกไฟล์ข้อมูล (.txt)" class="font-bold text-slate-700" />
                        <input id="import_file" name="file" type="file" accept=".txt,.csv" required class="mt-2 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                        <p class="mt-1 text-xs text-slate-500">รองรับเฉพาะไฟล์บันทึกประจำวันของเล้าสกุล .txt เท่านั้น</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition duration-150">
                            ยกเลิก
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition duration-150">
                            ตกลงนำเข้าข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const farmSelector = document.getElementById('farm_selector');
            const flockSelector = document.getElementById('flock_selector');

            if (!farmSelector || !flockSelector) return;

            // Cache all flock options
            const flockOptions = Array.from(flockSelector.options).map(option => ({
                value: option.value,
                text: option.textContent.trim(),
                farmId: option.dataset.farmId
            })).filter(opt => opt.value !== "");

            function filterFlocks(farmId) {
                // Clear dropdown except first option
                flockSelector.innerHTML = '<option value="">-- กรุณาเลือกรุ่น --</option>';
                
                if (!farmId) return;

                // Add matching options
                flockOptions.forEach(opt => {
                    if (opt.farmId === farmId) {
                        const newOpt = new Option(opt.text, opt.value);
                        flockSelector.add(newOpt);
                    }
                });
            }

            filterFlocks(farmSelector.value);

            farmSelector.addEventListener('change', () => {
                filterFlocks(farmSelector.value);
            });
        });
    </script>
</x-app-layout>
