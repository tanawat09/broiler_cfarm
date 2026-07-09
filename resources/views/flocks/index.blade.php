<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">รุ่นการเลี้ยง</h1>
                <p class="mt-1 text-sm text-gray-600">ดูรายละเอียดสำคัญของแต่ละรุ่น พร้อมทางลัดไปบันทึกและรายงาน</p>
            </div>
            <a href="{{ route('flocks.create') }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                เปิดรุ่นใหม่
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8 max-w-none">
            @if (session('status'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <div class="space-y-6">
                @forelse ($flocks as $flock)
                    @php
                        $starts = $flock->flockHouseStarts;
                        $firstStartDate = $starts->pluck('start_date')->filter()->min();
                        $lastStartDate = $starts->pluck('start_date')->filter()->max();
                        $birdsSold = (int) ($flock->birds_sold_total ?? 0);
                        $saleWeight = (float) ($flock->sale_weight_total ?? 0);
                        $saleAmount = (float) ($flock->sale_amount_total ?? 0);
                        $statusLabel = $flock->status === 'active' ? 'กำลังเลี้ยง' : ($flock->status === 'closed' ? 'ปิดรุ่นแล้ว' : $flock->status);
                        $statusClass = $flock->status === 'closed'
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10'
                            : 'bg-amber-50 text-amber-700 ring-amber-600/10';
                    @endphp

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                        <!-- Card Header -->
                        <div class="flex flex-col gap-4 border-b border-slate-100 bg-slate-50/30 p-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('flocks.show', $flock) }}" class="text-lg font-bold text-slate-900 hover:text-emerald-650 transition-colors">
                                        รุ่น {{ $flock->flock_code }}
                                    </a>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-slate-500">
                                    ฟาร์ม: {{ $flock->farm->farm_name }} <span class="mx-1.5 text-slate-300">•</span> ชนิดไก่: {{ $flock->chicken_type }}
                                </p>
                            </div>

                            <!-- Shortcuts Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-250 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all duration-150">
                                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    รายละเอียด
                                </a>
                                <a href="{{ route('flocks.edit', $flock) }}" class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50/30 px-3.5 py-2 text-xs font-bold text-emerald-800 shadow-sm hover:bg-emerald-50 transition-all duration-150">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.089a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    แก้ไข
                                </a>
                                <a href="{{ route('flocks.daily-records.index', $flock) }}" class="inline-flex items-center gap-1 rounded-xl border border-sky-200 bg-sky-50/30 px-3.5 py-2 text-xs font-bold text-sky-800 shadow-sm hover:bg-sky-50 transition-all duration-150">
                                    <svg class="h-3.5 w-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801-12c.065.21.1.433.1.664a3 3 0 0 1-6 0c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5M16.5 9h.008v.008H16.5V9Z" />
                                    </svg>
                                    บันทึกประจำวัน
                                </a>
                                <a href="{{ route('flocks.summary', $flock) }}" class="inline-flex items-center gap-1 rounded-xl border border-indigo-200 bg-indigo-50/30 px-3.5 py-2 text-xs font-bold text-indigo-800 shadow-sm hover:bg-indigo-50 transition-all duration-150">
                                    <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    ใบหน้าเล้า
                                </a>
                                <a href="{{ route('flocks.close.show', $flock) }}" class="inline-flex items-center gap-1 rounded-xl border border-amber-300 bg-amber-50 px-3.5 py-2 text-xs font-bold text-amber-850 shadow-sm hover:bg-amber-100 transition-all duration-150">
                                    <svg class="h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    ปิดรุ่น
                                </a>
                                @if(auth()->user()->isSuperAdmin())
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-flock-deletion-{{ $flock->id }}')" class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-bold text-red-750 shadow-sm hover:bg-red-100 transition-all duration-150">
                                        <svg class="h-3.5 w-3.5 text-red-650" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        ลบ
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Card Metrics Section -->
                        <div class="grid gap-0 divide-y divide-slate-100 md:grid-cols-2 md:divide-y-0 md:divide-x xl:grid-cols-4">
                            <!-- Col 1 -->
                            <div class="p-5 flex items-start gap-4">
                                <div class="rounded-xl bg-slate-100 p-2.5 text-slate-600 shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">วันที่เลี้ยง</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">เริ่มรุ่น: <span class="text-slate-900 font-bold">{{ thai_date($flock->start_date) }}</span></p>
                                    <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                        ลงเล้า: {{ $firstStartDate ? thai_date($firstStartDate) : '-' }}
                                        @if ($lastStartDate && $lastStartDate !== $firstStartDate)
                                            ถึง {{ thai_date($lastStartDate) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <!-- Col 2 -->
                            <div class="p-5 flex items-start gap-4">
                                <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600 shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">ไก่เริ่มต้น</p>
                                    <p class="mt-1 text-2xl font-black text-slate-900 font-mono">{{ number_format($flock->initial_birds) }} <span class="text-xs font-semibold text-slate-500 font-sans">ตัว</span></p>
                                    <p class="mt-0.5 text-xs text-slate-500 font-semibold font-mono">{{ number_format((int) $flock->flock_house_starts_count) }} <span class="font-medium font-sans text-slate-450">เล้า</span></p>
                                </div>
                            </div>
                            <!-- Col 3 -->
                            <div class="p-5 flex items-start gap-4">
                                <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600 shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.129-1.125v-3.75c0-.621-.508-1.125-1.13-1.125H16.5a9 9 0 0 0-9 9M16.5 13.5v-2.25H6v2.25m13.5-3v-2.25a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v2.25m13.5-3h-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">จับขายรวม</p>
                                    <p class="mt-1 text-2xl font-black text-slate-900 font-mono">{{ number_format($birdsSold) }} <span class="text-xs font-semibold text-slate-500 font-sans">ตัว</span></p>
                                    <p class="mt-0.5 text-xs text-slate-500 font-semibold font-mono">น้ำหนัก {{ number_format($saleWeight, 2) }} <span class="font-medium font-sans text-slate-450">กก.</span></p>
                                </div>
                            </div>
                            <!-- Col 4 -->
                            <div class="p-5 flex items-start gap-4">
                                <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 shrink-0">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h.007m-.007 3h.007m-.007 3h.007m-2.243 3h2.243m0 0a3 3 0 0 0 3 3h1.5a3 3 0 0 0 3-3m-7.5 0V3m7.5 12V3m0 12a3 3 0 0 0 3 3h1.5a3 3 0 0 0 3-3m-6 0v-2.25m6 2.25V3m-6 0h6.75M9.07 21.32a3.75 3.75 0 0 0 5.86 0M3.75 6.75h.007M3.75 12h.007" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">ยอดขายรวม</p>
                                    <p class="mt-1 text-2xl font-black text-emerald-700 font-mono">฿{{ number_format($saleAmount, 2) }}</p>
                                    <p class="mt-0.5 text-xs text-emerald-600/80 font-medium">บาท (สุทธิ)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Info Bar -->
                        <div class="border-t border-slate-100 bg-slate-50 px-5 py-3">
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-semibold text-slate-500">
                                <span class="flex items-center gap-1">
                                    <span class="text-slate-400 font-medium">รหัสรุ่น:</span> 
                                    <span class="text-slate-700">{{ $flock->flock_code }}</span>
                                </span>
                                <span class="text-slate-300">|</span>
                                <span class="flex items-center gap-1">
                                    <span class="text-slate-400 font-medium">ฟาร์ม:</span> 
                                    <span class="text-slate-700">{{ $flock->farm->farm_name }}</span>
                                </span>
                                <span class="text-slate-300">|</span>
                                <span class="flex items-center gap-1">
                                    <span class="text-slate-400 font-medium">สถานะ:</span> 
                                    <span class="text-slate-700">{{ $statusLabel }}</span>
                                </span>
                                @if ($flock->note)
                                    <span class="text-slate-300">|</span>
                                    <span class="flex items-center gap-1">
                                        <span class="text-slate-400 font-medium">หมายเหตุ:</span> 
                                        <span class="text-slate-700">{{ $flock->note }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </section>

                    <!-- Superadmin delete modal -->
                    @if(auth()->user()->isSuperAdmin())
                        <x-modal name="confirm-flock-deletion-{{ $flock->id }}" :show="$errors->{'flockDeletion_' . $flock->id}->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('flocks.destroy', $flock) }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-bold text-slate-900">
                                    คุณแน่ใจหรือไม่ว่าต้องการลบรุ่นการเลี้ยงนี้?
                                </h2>

                                <p class="mt-2 text-sm text-slate-650">
                                    เมื่อลบรุ่นการเลี้ยงนี้แล้ว ข้อมูลทั้งหมดที่เกี่ยวข้องรวมถึงบันทึกประจำวัน การจับไก่ และการนำเข้าเชือดจะถูกลบอย่างถาวร โปรดยืนยันรหัสผ่านของคุณเพื่อดำเนินการต่อ
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="password_{{ $flock->id }}" value="รหัสผ่าน" class="sr-only" />

                                    <x-text-input
                                        id="password_{{ $flock->id }}"
                                        name="password"
                                        type="password"
                                        class="mt-1 block w-3/4 rounded-xl"
                                        placeholder="ป้อนรหัสผ่านเพื่อยืนยัน"
                                    />

                                    <x-input-error :messages="$errors->{'flockDeletion_' . $flock->id}->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl">
                                        ยกเลิก
                                    </x-secondary-button>

                                    <x-danger-button class="rounded-xl">
                                        ยืนยันการลบ
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @endif
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500 shadow-sm">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <p class="mt-4 text-sm font-bold text-slate-900">ยังไม่มีข้อมูลรุ่นการเลี้ยง</p>
                        <p class="mt-1 text-xs text-slate-500">กรุณากดปุ่มเปิดรุ่นใหม่ด้านบนเพื่อสร้างรุ่นการเลี้ยงแรก</p>
                    </div>
                @endforelse
            </div>

            @if ($flocks->hasPages())
                <div class="mt-5 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    {{ $flocks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
