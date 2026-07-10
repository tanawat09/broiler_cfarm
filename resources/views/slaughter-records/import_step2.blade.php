<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">ตั้งค่าการดึงข้อมูล Excel (ขั้นตอนที่ 2/3)</h1>
                <p class="mt-1 text-sm text-slate-600">เลือก Sheet และคอลัมน์ที่ต้องการดึงข้อมูล</p>
            </div>
            <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
                <form method="POST" action="{{ route('flocks.slaughter-records.handle-config', $flock) }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="upload_token" value="{{ $uploadToken }}">

                    <!-- Sheet selection -->
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <x-input-label for="sheet_name" value="เลือก Sheet ข้อมูล" class="font-bold text-slate-750 text-xs" />
                            <select id="sheet_name" name="sheet_name" class="mt-1.5 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                @foreach ($sheetNames as $sheet)
                                    <option value="{{ $sheet }}" @selected($loop->first)>{{ $sheet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="start_row" value="แถวเริ่มต้นอ่านข้อมูล" class="font-bold text-slate-750 text-xs" />
                            <x-text-input id="start_row" name="start_row" type="number" min="1" class="mt-1.5 block w-full text-right" value="2" required />
                            <p class="text-xs text-slate-500 mt-1">ระบบจะข้ามแถวหัวข้อ และเริ่มอ่านข้อมูลตั้งแต่แถวนี้ลงไป</p>
                        </div>
                    </div>

                    <!-- Column mappings -->
                    <div class="border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-bold text-slate-900 mb-4">กำหนดตำแหน่งคอลัมน์ใน Excel (ระบุตัวอักษร A-Z)</h3>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <!-- House -->
                            <div>
                                <x-input-label for="col_house" value="คอลัมน์ เล้า (เช่น I)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_house" name="col_house" type="text" class="mt-1 block w-full uppercase" value="I" required maxlength="3" />
                            </div>
                            
                            <!-- Slaughter Birds -->
                            <div>
                                <x-input-label for="col_birds" value="คอลัมน์ จำนวนตัวไก่เข้าเชือด (เช่น K)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_birds" name="col_birds" type="text" class="mt-1 block w-full uppercase" value="K" required maxlength="3" />
                            </div>

                            <!-- Actual Weight -->
                            <div>
                                <x-input-label for="col_weight" value="คอลัมน์ น้ำหนักเข้าจริง (เช่น R)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_weight" name="col_weight" type="text" class="mt-1 block w-full uppercase" value="R" required maxlength="3" />
                            </div>

                            <!-- DOA Birds -->
                            <div>
                                <x-input-label for="col_doa" value="คอลัมน์ ไก่ตายขนส่ง (เช่น AA)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_doa" name="col_doa" type="text" class="mt-1 block w-full uppercase" value="AA" required maxlength="3" />
                            </div>

                            <!-- Net Birds -->
                            <div>
                                <x-input-label for="col_net" value="คอลัมน์ ไก่เข้าจริง (เช่น AB)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_net" name="col_net" type="text" class="mt-1 block w-full uppercase" value="AB" required maxlength="3" />
                            </div>

                            <!-- Condemned Birds -->
                            <div>
                                <x-input-label for="col_condemned" value="คอลัมน์ จำนวนตัวไก่ตกราว (เช่น AF)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_condemned" name="col_condemned" type="text" class="mt-1 block w-full uppercase" value="AF" required maxlength="3" />
                            </div>

                            <!-- Condemned Percent -->
                            <div>
                                <x-input-label for="col_condemned_percent" value="คอลัมน์ % ไก่ตกราว (เช่น AG)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_condemned_percent" name="col_condemned_percent" type="text" class="mt-1 block w-full uppercase" value="AG" required maxlength="3" />
                            </div>

                            <!-- Problem Birds -->
                            <div>
                                <x-input-label for="col_problem" value="คอลัมน์ จำนวนตัวไก่มีปัญหา (เช่น AH)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_problem" name="col_problem" type="text" class="mt-1 block w-full uppercase" value="AH" required maxlength="3" />
                            </div>

                            <!-- Problem Percent -->
                            <div>
                                <x-input-label for="col_problem_percent" value="คอลัมน์ % ไก่มีปัญหา (เช่น AI)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_problem_percent" name="col_problem_percent" type="text" class="mt-1 block w-full uppercase" value="AI" required maxlength="3" />
                            </div>

                            <!-- Dead Weight -->
                            <div>
                                <x-input-label for="col_dead_weight" value="คอลัมน์ น้ำหนักไก่ตาย (เช่น AJ)" class="font-semibold text-slate-700 text-xs" />
                                <x-text-input id="col_dead_weight" name="col_dead_weight" type="text" class="mt-1 block w-full uppercase" value="AJ" required maxlength="3" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                        <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                            ยกเลิก
                        </a>
                        <x-primary-button>
                            ขั้นตอนถัดไป (ตรวจสอบข้อมูล)
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
