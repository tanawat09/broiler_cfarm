<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">ราคาอาหาร</h1>
            <p class="mt-1 text-sm text-slate-600">กำหนดราคาอาหารสัตว์ตามเบอร์ (บาท/กก.) ใช้ร่วมกันทุกฟาร์มเพื่อคำนวณต้นทุน</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">กรุณาตรวจสอบข้อมูลราคาอีกครั้ง</div>
            @endif

            @if (auth()->user()->isSuperAdmin())
                <div class="mb-6 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                    <form method="POST" action="{{ route('feed-price-masters.store') }}">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-[150px_1fr_1fr_auto_auto] md:items-end">
                            <div>
                                <x-input-label for="feed_code" value="รหัส/เบอร์อาหาร" />
                                <x-text-input id="feed_code" name="feed_code" type="text" class="mt-1 block w-full" :value="old('feed_code')" placeholder="เช่น 203" required />
                                <x-input-error class="mt-2" :messages="$errors->get('feed_code')" />
                            </div>
                            <div>
                                <x-input-label for="price_per_kg" value="ราคาขาย/กก." />
                                <x-text-input id="price_per_kg" name="price_per_kg" type="number" min="0" step="0.01" class="mt-1 block w-full text-right" :value="old('price_per_kg')" placeholder="0.00" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price_per_kg')" />
                            </div>
                            <div>
                                <x-input-label for="effective_date" value="วันที่เริ่มใช้" />
                                <x-text-input id="effective_date" name="effective_date" type="date" class="mt-1 block w-full" :value="old('effective_date', now()->toDateString())" required />
                                <x-input-error class="mt-2" :messages="$errors->get('effective_date')" />
                            </div>
                            <div class="flex items-center pb-3">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" checked>
                                    ใช้งาน
                                </label>
                            </div>
                            <div class="pb-1.5">
                                <x-primary-button>บันทึก</x-primary-button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="note" value="หมายเหตุ" />
                            <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)">{{ old('note') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('note')" />
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-6 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    ราคาอาหารเป็นราคากลางของระบบ หากต้องการแก้ไขให้ติดต่อ Super Admin
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">รหัส/เบอร์อาหาร</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">วันที่เริ่มใช้</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ราคาขาย/กก.</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">สถานะ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หมายเหตุ</th>
                                @if (auth()->user()->isSuperAdmin())
                                    <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($prices as $row)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $row->feed_code }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ thai_date($row->effective_date) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format((float) $row->price_per_kg, 2) }} บาท</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $row->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $row->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="min-w-48 px-4 py-3 text-slate-700">{{ $row->note ?: '-' }}</td>
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="px-4 py-3 text-right">
                                            <form method="POST" action="{{ route('feed-price-masters.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบราคาอาหารสำหรับเบอร์ {{ $row->feed_code }} นี้หรือไม่?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">ลบ</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลราคาอาหาร</td>
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
