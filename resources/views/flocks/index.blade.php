<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">รุ่นการเลี้ยง</h1>
                <p class="mt-1 text-sm text-gray-600">ดูรายละเอียดสำคัญของแต่ละรุ่น พร้อมทางลัดไปบันทึกและรายงาน</p>
            </div>
            <a href="{{ route('flocks.create') }}" class="inline-flex items-center justify-center rounded-md border-2 border-emerald-500 bg-white px-4 py-2 text-sm font-semibold text-emerald-600 shadow-sm transition-all duration-200 hover:bg-emerald-50 hover:text-emerald-700 hover:shadow-md hover:scale-105 active:scale-95">
                + เปิดรุ่นใหม่
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full max-w-[1500px] px-3 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="space-y-4">
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
                            ? 'bg-green-50 text-green-700 ring-green-200'
                            : 'bg-amber-50 text-amber-700 ring-amber-200';
                    @endphp

                    <section class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-4 border-b border-slate-200 p-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('flocks.show', $flock) }}" class="text-lg font-semibold text-slate-950 hover:text-emerald-700">
                                        {{ $flock->flock_code }}
                                    </a>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium ring-1 {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $flock->farm->farm_name }} / {{ $flock->chicken_type }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('flocks.show', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    รายละเอียด
                                </a>
                                <a href="{{ route('flocks.edit', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    แก้ไข
                                </a>
                                <a href="{{ route('flocks.daily-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    บันทึกประจำวัน
                                </a>
                                <a href="{{ route('flocks.summary', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    ใบหน้าเล้า
                                </a>
                                <a href="{{ route('flocks.close.show', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-100">
                                    ปิดรุ่น
                                </a>
                                @if(auth()->user()->isSuperAdmin())
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-flock-deletion-{{ $flock->id }}')" class="inline-flex items-center justify-center rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                                        ลบ
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-0 md:grid-cols-2 xl:grid-cols-4">
                            <div class="border-b border-slate-100 p-4 xl:border-b-0 xl:border-r">
                                <p class="text-xs font-medium uppercase text-slate-500">วันที่เลี้ยง</p>
                                <p class="mt-1 text-sm text-slate-700">เริ่มรุ่น: {{ thai_date($flock->start_date) }}</p>
                                <p class="mt-1 text-sm text-slate-700">
                                    ลงเล้า: {{ $firstStartDate ? thai_date($firstStartDate) : '-' }}
                                    @if ($lastStartDate && $lastStartDate !== $firstStartDate)
                                        ถึง {{ thai_date($lastStartDate) }}
                                    @endif
                                </p>
                            </div>
                            <div class="border-b border-slate-100 p-4 xl:border-b-0 xl:border-r">
                                <p class="text-xs font-medium uppercase text-slate-500">ไก่เริ่มต้น</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-950">{{ number_format($flock->initial_birds) }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ number_format((int) $flock->flock_house_starts_count) }} เล้า</p>
                            </div>
                            <div class="border-b border-slate-100 p-4 xl:border-b-0 xl:border-r">
                                <p class="text-xs font-medium uppercase text-slate-500">จับขายรวม</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-950">{{ number_format($birdsSold) }}</p>
                                <p class="mt-1 text-sm text-slate-600">น้ำหนัก {{ number_format($saleWeight, 2) }} กก.</p>
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-medium uppercase text-slate-500">ยอดขายรวม</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-950">{{ number_format($saleAmount, 2) }}</p>
                                <p class="mt-1 text-sm text-slate-600">บาท</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">
                                <span>รหัสรุ่น: <span class="font-medium text-slate-900">{{ $flock->flock_code }}</span></span>
                                <span>ฟาร์ม: <span class="font-medium text-slate-900">{{ $flock->farm->farm_name }}</span></span>
                                <span>สถานะ: <span class="font-medium text-slate-900">{{ $statusLabel }}</span></span>
                                @if ($flock->note)
                                    <span>หมายเหตุ: <span class="font-medium text-slate-900">{{ $flock->note }}</span></span>
                                @endif
                            </div>
                        </div>
                    </section>

                    @if(auth()->user()->isSuperAdmin())
                        <x-modal name="confirm-flock-deletion-{{ $flock->id }}" :show="$errors->{'flockDeletion_' . $flock->id}->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('flocks.destroy', $flock) }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900">
                                    คุณแน่ใจหรือไม่ว่าต้องการลบรุ่นการเลี้ยงนี้?
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    เมื่อลบรุ่นการเลี้ยงนี้แล้ว ข้อมูลทั้งหมดที่เกี่ยวข้องจะถูกลบอย่างถาวร โปรดยืนยันรหัสผ่านของคุณเพื่อดำเนินการต่อ
                                </p>

                                <div class="mt-6">
                                    <x-input-label for="password_{{ $flock->id }}" value="รหัสผ่าน" class="sr-only" />

                                    <x-text-input
                                        id="password_{{ $flock->id }}"
                                        name="password"
                                        type="password"
                                        class="mt-1 block w-3/4"
                                        placeholder="รหัสผ่าน"
                                    />

                                    <x-input-error :messages="$errors->{'flockDeletion_' . $flock->id}->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        ยกเลิก
                                    </x-secondary-button>

                                    <x-danger-button class="ms-3">
                                        ยืนยันการลบ
                                    </x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @endif
                @empty
                    <div class="rounded-md border border-slate-200 bg-white p-8 text-center text-slate-500 shadow-sm">
                        ยังไม่มีข้อมูลรุ่นการเลี้ยง
                    </div>
                @endforelse
            </div>

            @if ($flocks->hasPages())
                <div class="mt-5 rounded-md border border-slate-200 bg-white px-4 py-3 shadow-sm">
                    {{ $flocks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
