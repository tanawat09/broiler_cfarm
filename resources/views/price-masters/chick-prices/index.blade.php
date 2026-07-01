<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">ราคาลูกไก่</h1>
            <p class="mt-1 text-sm text-slate-600">กำหนดราคาลูกไก่ต่อตัว แยกเพศ แยกเกรด และวันที่เริ่มใช้ราคา</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">กรุณาตรวจสอบข้อมูลราคาอีกครั้ง</div>
            @endif

            <div class="mb-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST" action="{{ route('chick-price-masters.store') }}">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-5 md:items-end">
                        <div>
                            <x-input-label for="sex" value="เพศ" />
                            <select id="sex" name="sex" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($sexes as $sex)
                                    <option value="{{ $sex }}" @selected(old('sex') === $sex)>{{ $sex }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="grade" value="เกรด" />
                            <select id="grade" name="grade" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade }}" @selected(old('grade') === $grade)>{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="price_per_bird" value="ราคา/ตัว" />
                            <x-text-input id="price_per_bird" name="price_per_bird" type="number" min="0" step="0.01" class="mt-1 block w-full text-right" :value="old('price_per_bird')" required />
                        </div>
                        <div>
                            <x-input-label for="effective_date" value="วันที่เริ่มใช้" />
                            <x-text-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" :value="old('effective_date', now()->toDateString())" required />
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" checked>
                                ใช้งาน
                            </label>
                            <x-primary-button>บันทึก</x-primary-button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-input-label for="note" value="หมายเหตุ" />
                        <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('note') }}</textarea>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่เริ่มใช้</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เพศ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เกรด</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ราคา/ตัว</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">สถานะ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หมายเหตุ</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($prices as $row)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700">{{ thai_date($row->effective_date) }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $row->sex }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $row->grade }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $row->price_per_bird, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $row->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $row->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="min-w-48 px-4 py-3 text-slate-700">{{ $row->note ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('chick-price-masters.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบราคานี้หรือไม่?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลราคาลูกไก่</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">{{ $prices->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
