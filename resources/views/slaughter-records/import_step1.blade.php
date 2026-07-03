<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">นำเข้าข้อมูลไก่เข้าเชือด นน.หน้าโรงงาน (ขั้นตอนที่ 1/3)</h1>
                <p class="mt-1 text-sm text-slate-600">อัปโหลดไฟล์ Excel เพื่อนำเข้าข้อมูลโรงงาน</p>
            </div>
            <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
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

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('flocks.slaughter-records.handle-upload', $flock) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label value="ฟาร์ม / รุ่นการเลี้ยง" class="font-bold text-slate-750 text-xs" />
                        <div class="mt-1.5 p-3.5 bg-slate-50 rounded-lg border border-slate-150 text-sm">
                            <span class="font-bold text-slate-800">{{ $flock->farm->farm_name }}</span> / รุ่น {{ $flock->flock_code }}
                        </div>
                    </div>

                    <div>
                        <x-input-label for="slaughter_date" value="วันที่เข้าเชือด" class="font-bold text-slate-750 text-xs" />
                        <x-text-input id="slaughter_date" name="slaughter_date" type="date" class="mt-1.5 block w-full" :value="old('slaughter_date', now()->toDateString())" required />
                    </div>

                    <div>
                        <x-input-label for="file" value="เลือกไฟล์ Excel (.xlsx, .xls)" class="font-bold text-slate-750 text-xs" />
                        <div class="mt-1.5 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-lg hover:border-emerald-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-slate-650 justify-center">
                                    <label for="file" class="relative cursor-pointer rounded-md font-bold text-emerald-600 hover:text-emerald-700 focus-within:outline-none">
                                        <span>คลิกเพื่อเลือกไฟล์</span>
                                        <input id="file" name="file" type="file" accept=".xlsx,.xls" class="sr-only" required>
                                    </label>
                                </div>
                                <p class="text-xs text-slate-500">รองรับเฉพาะ Excel .xlsx หรือ .xls</p>
                            </div>
                        </div>
                        <p id="file-name" class="mt-2 text-xs font-bold text-emerald-700 text-center"></p>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                            ยกเลิก
                        </a>
                        <x-primary-button>
                            ขั้นตอนถัดไป
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            document.getElementById('file-name').textContent = fileName ? 'เลือกไฟล์แล้ว: ' + fileName : '';
        });
    </script>
</x-app-layout>
