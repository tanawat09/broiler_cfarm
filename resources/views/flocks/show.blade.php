<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 py-2 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1m4-4h1m-1 4h1" />
                    </svg>
                </span>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $flock->flock_code }}</h1>
                    <p class="mt-0.5 text-sm text-slate-500">รายละเอียดรุ่นการเลี้ยง</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('flocks.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับรายการ
                </a>
                <a href="{{ route('flocks.placements.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2 0 4 1.5 4 4v1h2a3 3 0 013 3v7H3v-7a3 3 0 013-3h2V7c0-2.5 2-4 4-4z" />
                    </svg>
                    ข้อมูลลูกไก่
                </a>
                <a href="{{ route('flocks.daily-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                    </svg>
                    บันทึกประจำวัน
                </a>
                <a href="{{ route('flocks.catch-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h3m-5 6h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    คิวจับไก่
                </a>
                <a href="{{ route('flocks.slaughter-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    นน.หน้าโรงงาน
                </a>
                <a href="{{ route('flocks.production-report.show', $flock) }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-100">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6M5 21h14a2 2 0 002-2V7.5L14.5 3H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    รายงานรุ่น
                </a>
                <a href="{{ route('flocks.edit', $flock) }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07 6 18l1.93-4.582 8.932-8.931z" />
                    </svg>
                    แก้ไข
                </a>
                @if(auth()->user()->isSuperAdmin())
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-flock-deletion')" class="inline-flex items-center justify-center rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">
                        ลบ
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-6">
        <div class="mx-auto w-full max-w-[98%] space-y-5 px-2 sm:px-4 lg:px-6">
            @if (session('status'))
                <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @php
                $placements = $flock->flockHousePlacements;
                $placementTotalBirds = (int) $placements->sum('chicks_in');
                $displayInitialBirds = $placementTotalBirds > 0 ? $placementTotalBirds : (int) $flock->initial_birds;
                $houseCount = $flock->flockHouseStarts->count();
                $totalAmount = (float) $placements->sum('amount');
                $maleCount = (int) $placements->sum('male_count');
                $femaleCount = (int) $placements->sum('female_count');
                $isActive = $flock->status === 'active';
            @endphp

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-white px-5 py-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">ข้อมูลทั่วไปรุ่นเลี้ยง</h2>
                            <p class="mt-0.5 text-sm text-slate-500">ข้อมูลหลักของรุ่นนี้อยู่ด้านบนเพื่อให้ตรวจสอบได้ทันที</p>
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-sm font-bold {{ $isActive ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-100 text-slate-700 ring-1 ring-slate-200' }}">
                            {{ $isActive ? 'กำลังเลี้ยง' : 'ปิดรุ่นแล้ว' }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-0 md:grid-cols-2 xl:grid-cols-4">
                    <div class="border-b border-slate-100 p-5 md:border-r xl:border-b-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">ฟาร์ม</p>
                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $flock->farm->farm_name }}</p>
                    </div>
                    <div class="border-b border-slate-100 p-5 xl:border-r xl:border-b-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">รุ่นการเลี้ยง</p>
                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $flock->flock_code }}</p>
                    </div>
                    <div class="border-b border-slate-100 p-5 md:border-r md:border-b-0 xl:border-r">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">วันที่เริ่มเลี้ยง</p>
                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $flock->start_date ? thai_date($flock->start_date) : '-' }}</p>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">ประเภทไก่</p>
                        <p class="mt-2 text-lg font-bold text-slate-900">{{ $flock->chicken_type ?: '-' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">หมายเหตุ</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $flock->note ?: '-' }}</p>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-500">จำนวนไก่เข้า</p>
                            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($displayInitialBirds) }}</p>
                            <p class="mt-1 text-xs text-slate-500">ตัว จากรายละเอียดลงไก่</p>
                        </div>
                        <span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20H7m10 0v-2a5 5 0 00-10 0v2m10 0h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">จำนวนเล้า</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($houseCount) }}</p>
                    <p class="mt-1 text-xs text-slate-500">เล้าที่อยู่ในรุ่นนี้</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">เพศผู้ / เพศเมีย</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($maleCount) }} / {{ number_format($femaleCount) }}</p>
                    <p class="mt-1 text-xs text-slate-500">ตัว จากรายละเอียดลูกไก่</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">มูลค่าลูกไก่</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-700">{{ number_format($totalAmount, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">บาท</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-white px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">รายละเอียดการลงไก่รายเล้า</h2>
                        <p class="mt-0.5 text-sm text-slate-500">รวมยอดจากรายการลงไก่จริง แยกตามเล้า และเรียงตามเลขเล้า</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 ring-1 ring-emerald-100">รวม {{ number_format($displayInitialBirds) }} ตัว</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700 ring-1 ring-slate-200">{{ number_format($houseCount) }} เล้า</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1500px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="sticky left-0 z-10 border-b border-r border-slate-200 bg-slate-50 px-3 py-3 text-center">เล้า</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-center">วันที่ลงไก่</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-center">วันที่จับไก่</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">อายุจับ</th>
                                <th class="border-b border-slate-200 bg-emerald-50 px-3 py-3 text-right text-emerald-900">จำนวนไก่เข้า</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">เพศผู้</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">เพศเมีย</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">ผู้ A</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">ผู้ B</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">เมีย A</th>
                                <th class="border-b border-slate-200 px-3 py-3 text-right">เมีย B</th>
                                <th class="border-b border-slate-200 bg-indigo-50 px-3 py-3 text-right text-indigo-900">จำนวนเงิน</th>
                                <th class="border-b border-slate-200 px-3 py-3">แหล่งลูกไก่</th>
                                <th class="border-b border-slate-200 px-3 py-3">เกรด</th>
                                <th class="border-b border-slate-200 px-3 py-3">รหัสสายพันธุ์</th>
                                <th class="border-b border-slate-200 px-3 py-3">Batch</th>
                                <th class="border-b border-slate-200 px-3 py-3">เพศ</th>
                                <th class="border-b border-slate-200 px-3 py-3">พันธุ์</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                            @php $placementsByHouse = $placements->groupBy('house_id'); @endphp
                            @foreach ($flock->flockHouseStarts->sortBy('house.house_no') as $start)
                                @php
                                    $housePlacements = $placementsByHouse->get($start->house_id) ?: collect();
                                    $firstPlacement = $housePlacements->sortBy('placement_date')->first();
                                    $lastPlacement = $housePlacements->whereNotNull('catch_date')->sortByDesc('catch_date')->first();

                                    $placementDate = $firstPlacement?->placement_date;
                                    $catchDate = $lastPlacement?->catch_date;
                                    $catchAge = $housePlacements->whereNotNull('catch_age')->max('catch_age');
                                    $initialBirds = (int) $housePlacements->sum('chicks_in');
                                    $maleTotal = (int) $housePlacements->sum('male_count');
                                    $femaleTotal = (int) $housePlacements->sum('female_count');
                                    $maleGradeA = (int) $housePlacements->sum('male_grade_a_count');
                                    $maleGradeB = (int) $housePlacements->sum('male_grade_b_count');
                                    $femaleGradeA = (int) $housePlacements->sum('female_grade_a_count');
                                    $femaleGradeB = (int) $housePlacements->sum('female_grade_b_count');
                                    $amount = (float) $housePlacements->sum('amount');

                                    $chickSource = $housePlacements->pluck('chick_source')->filter()->unique()->implode(', ');
                                    $chickGrade = $housePlacements->pluck('chick_grade')->filter()->unique()->implode(', ');
                                    $chickCode = $housePlacements->pluck('chick_code')->filter()->unique()->implode(', ');
                                    $batchNo = $housePlacements->pluck('batch_no')->filter()->unique()->implode(', ');

                                    if ($maleTotal > 0 && $femaleTotal > 0) {
                                        $sex = 'คละ';
                                    } elseif ($maleTotal > 0) {
                                        $sex = 'ผู้';
                                    } elseif ($femaleTotal > 0) {
                                        $sex = 'เมีย';
                                    } else {
                                        $sex = $housePlacements->pluck('sex')->filter()->unique()->implode(', ');
                                    }

                                    $breed = $housePlacements->pluck('breed')->filter()->unique()->implode(', ');
                                @endphp
                                <tr class="transition hover:bg-slate-50">
                                    <td class="sticky left-0 z-10 whitespace-nowrap border-r border-slate-100 bg-white px-3 py-3 text-center font-bold text-slate-900">เล้า {{ $start->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center font-medium">{{ $placementDate ? thai_date($placementDate) : thai_date($start->start_date ?: $flock->start_date) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center text-slate-500">{{ $catchDate ? thai_date($catchDate) : '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right font-medium">{{ $catchAge === null ? '-' : number_format($catchAge) }}</td>
                                    <td class="whitespace-nowrap bg-emerald-50/50 px-3 py-3 text-right font-bold text-emerald-700">{{ number_format($initialBirds > 0 ? $initialBirds : $start->initial_birds) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right">{{ number_format($maleTotal) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right">{{ number_format($femaleTotal) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-slate-500">{{ number_format($maleGradeA) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-slate-500">{{ number_format($maleGradeB) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-slate-500">{{ number_format($femaleGradeA) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-slate-500">{{ number_format($femaleGradeB) }}</td>
                                    <td class="whitespace-nowrap bg-indigo-50/50 px-3 py-3 text-right font-bold text-indigo-700">{{ number_format($amount, 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 font-medium">{{ $chickSource ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3">{{ $chickGrade ?: '-' }}</td>
                                    <td class="max-w-[280px] px-3 py-3 font-mono text-xs text-slate-500">{{ $chickCode ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">{{ $batchNo ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">{{ $sex ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-500">{{ $breed ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <x-modal name="confirm-flock-deletion" :show="$errors->{'flockDeletion_' . $flock->id}->isNotEmpty()" focusable>
        <form method="post" action="{{ route('flocks.destroy', $flock) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900">คุณแน่ใจหรือไม่ว่าต้องการลบรุ่นการเลี้ยงนี้?</h2>
            <p class="mt-2 text-sm text-slate-500">
                เมื่อลบรุ่นการเลี้ยงนี้แล้ว ข้อมูลทั้งหมดที่เกี่ยวข้องจะถูกลบอย่างถาวร โปรดยืนยันรหัสผ่านก่อนดำเนินการต่อ
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="รหัสผ่าน" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4 text-sm" placeholder="รหัสผ่านผู้ใช้" />
                <x-input-error :messages="$errors->{'flockDeletion_' . $flock->id}->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')" class="text-sm">ยกเลิก</x-secondary-button>
                <x-danger-button class="text-sm">ยืนยันการลบ</x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
