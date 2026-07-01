<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ $flock->flock_code }}</h1>
                <p class="mt-1 text-sm text-gray-600">รายละเอียดรุ่นการเลี้ยง</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('flocks.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    กลับรายการ
                </a>
                <a href="{{ route('flocks.daily-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    อุณหภูมิ/ความชื้น/สูญเสียรายวัน
                </a>
                <a href="{{ route('flocks.summary', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    ใบหน้าเล้า
                </a>
                <a href="{{ route('flocks.sale-records.index', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    จับไก่ขาย
                </a>
                <a href="{{ route('flocks.close.show', $flock) }}" class="inline-flex items-center justify-center rounded-md border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-800 hover:bg-amber-100">
                    ปิดรุ่น
                </a>
                <a href="{{ route('flocks.edit', $flock) }}" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    แก้ไข
                </a>
                @if(auth()->user()->isSuperAdmin())
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-flock-deletion')" class="inline-flex items-center justify-center rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                        ลบ
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-md border border-gray-200 bg-white p-6 shadow-sm">
                <dl class="grid gap-5 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ฟาร์ม</dt>
                        <dd class="mt-1 text-gray-900">{{ $flock->farm->farm_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">รหัสรุ่น</dt>
                        <dd class="mt-1 text-gray-900">{{ $flock->flock_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ประเภทไก่</dt>
                        <dd class="mt-1 text-gray-900">{{ $flock->chicken_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">วันที่เริ่มเลี้ยง</dt>
                        <dd class="mt-1 text-gray-900">{{ $flock->start_date->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">จำนวนไก่เริ่มต้นรวม</dt>
                        <dd class="mt-1 text-gray-900">{{ number_format($flock->initial_birds) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">สถานะ</dt>
                        <dd class="mt-1 text-gray-900">{{ $flock->status === 'active' ? 'กำลังเลี้ยง' : $flock->status }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">หมายเหตุ</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $flock->note ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">รายละเอียดลงไก่รายเล้า</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[2200px] divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-center font-medium text-gray-600">เล้า</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-600">วันที่ลง</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-600">วันที่จับ</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">อายุจับ</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">ไก่เข้า</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">M</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">FM</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">M A</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">M B</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">FM A</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">FM B</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-600">จำนวนเงิน</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">แหล่งลูกไก่</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">เกรดลูกไก่</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">รหัส</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">รุ่น</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">เพศ</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-600">พันธุ์</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @php $placementsByHouse = $flock->flockHousePlacements->keyBy('house_id'); @endphp
                            @foreach ($flock->flockHouseStarts->sortBy('house.house_no') as $start)
                                @php $placement = $placementsByHouse->get($start->house_id); @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-3 py-3 text-center font-medium text-gray-900">{{ $start->house->house_no }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center text-gray-700">{{ $placement?->placement_date ? thai_date($placement->placement_date) : thai_date($start->start_date ?: $flock->start_date) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center text-gray-700">{{ $placement?->catch_date ? thai_date($placement->catch_date) : '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ $placement?->catch_age === null ? '-' : number_format($placement->catch_age) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->chicks_in ?? $start->initial_birds) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->male_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->female_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->male_grade_a_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->male_grade_b_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->female_grade_a_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format($placement?->female_grade_b_count ?? 0) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right text-gray-700">{{ number_format((float) ($placement?->amount ?? 0), 2) }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $placement?->chick_source ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $placement?->chick_grade ?: '-' }}</td>
                                    <td class="min-w-80 px-3 py-3 text-gray-700">{{ $placement?->chick_code ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $placement?->batch_no ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $placement?->sex ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-gray-700">{{ $placement?->breed ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-modal name="confirm-flock-deletion" :show="$errors->{'flockDeletion_' . $flock->id}->isNotEmpty()" focusable>
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
                <x-input-label for="password" value="รหัสผ่าน" class="sr-only" />

                <x-text-input
                    id="password"
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
</x-app-layout>
