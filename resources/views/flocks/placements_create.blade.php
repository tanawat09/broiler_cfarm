<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 py-2">
            <span class="inline-flex items-center justify-center rounded-lg bg-emerald-500/10 p-2 text-emerald-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">เปิดรุ่นการเลี้ยงใหม่</h1>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mt-0.5">
                    ขั้นตอนแรก: ระบุรหัสรุ่นและฟาร์มเพื่อเริ่มป้อนข้อมูลลูกไก่
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-md">
                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">ระบุข้อมูลรุ่นเลี้ยงเพื่อเริ่มบันทึก</h2>
                    <p class="text-xs text-slate-500 mt-1">หลังจากบันทึกแล้ว ระบบจะเปิดหน้าสเปรดชีตให้ลงข้อมูลลูกไก่รายเล้าทันที</p>
                </div>

                @if ($farms->isEmpty())
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs font-semibold text-amber-800">
                        ยังไม่มีข้อมูลฟาร์มในระบบ กรุณาเพิ่มฟาร์มก่อนเปิดรุ่นการเลี้ยง
                    </div>
                @else
                    <form method="POST" action="{{ route('placements.create-flock') }}" class="space-y-5">
                        @csrf

                        <!-- Farm Select -->
                        <div>
                            <x-input-label for="farm_id" value="เลือกฟาร์ม" class="font-bold text-slate-700 text-xs" />
                            <select id="farm_id" name="farm_id" class="mt-1.5 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                @foreach ($farms as $farm)
                                    <option value="{{ $farm->id }}">{{ $farm->farm_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-1" :messages="$errors->get('farm_id')" />
                        </div>

                        <!-- Flock Code -->
                        <div>
                            <x-input-label for="flock_code" value="รหัสรุ่นการเลี้ยง" class="font-bold text-slate-700 text-xs" />
                            <x-text-input id="flock_code" name="flock_code" type="text" class="mt-1.5 block w-full text-xs" placeholder="ตัวอย่าง: รุ่น 11, BGBR-01" required />
                            <x-input-error class="mt-1" :messages="$errors->get('flock_code')" />
                        </div>

                        <!-- Start Date -->
                        <div>
                            <x-input-label for="start_date" value="วันที่เริ่มเลี้ยง" class="font-bold text-slate-700 text-xs" />
                            <input id="start_date" name="start_date" type="date" value="{{ now()->toDateString() }}" class="mt-1.5 block w-full rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required />
                            <x-input-error class="mt-1" :messages="$errors->get('start_date')" />
                        </div>

                        <!-- Chicken Type -->
                        <div>
                            <x-input-label for="chicken_type" value="ประเภทไก่" class="font-bold text-slate-700 text-xs" />
                            <x-text-input id="chicken_type" name="chicken_type" type="text" class="mt-1.5 block w-full text-xs" value="ไก่เนื้อ" required />
                            <x-input-error class="mt-1" :messages="$errors->get('chicken_type')" />
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <a href="{{ url('/flocks') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                                ยกเลิก
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                                สร้างรุ่นและเริ่มลงไก่
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
