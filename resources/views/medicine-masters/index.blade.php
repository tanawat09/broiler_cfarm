<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">มาสเตอร์ยา</h1>
            <p class="mt-1 text-sm text-slate-600">จัดการรายชื่อยา วัคซีน และอาหารเสริมสำหรับใช้ในหน้าบันทึกการให้ยา</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">กรุณาตรวจสอบข้อมูลยาอีกครั้ง</div>
            @endif

            @if (auth()->user()->isSuperAdmin())
                <div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <form method="POST" action="{{ route('medicine-masters.store') }}">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-[1.5fr_1fr_auto_auto] md:items-end">
                            <div>
                                <x-input-label for="name" value="ชื่อยา" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="เช่น Soludox" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label for="default_unit" value="หน่วยเริ่มต้น" />
                                <x-text-input id="default_unit" name="default_unit" type="text" class="mt-1 block w-full" :value="old('default_unit')" placeholder="กรัม, โดส, ซีซี" />
                                <x-input-error class="mt-2" :messages="$errors->get('default_unit')" />
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" checked>
                                ใช้งาน
                            </label>
                            <x-primary-button>บันทึก</x-primary-button>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="note" value="หมายเหตุ" />
                            <textarea id="note" name="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('note') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('note')" />
                        </div>
                    </form>
                </div>
            @else
                <div class="mb-6 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    มาสเตอร์ยาแก้ไขได้เฉพาะ Super Admin
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ชื่อยา</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หน่วย</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">สถานะ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">หมายเหตุ</th>
                                @if (auth()->user()->isSuperAdmin())
                                    <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($medicines as $row)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $row->default_unit ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $row->is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $row->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="min-w-48 px-4 py-3 text-slate-700">{{ $row->note ?: '-' }}</td>
                                    @if (auth()->user()->isSuperAdmin())
                                        <td class="px-4 py-3 text-right">
                                            <form method="POST" action="{{ route('medicine-masters.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบมาสเตอร์ยานี้หรือไม่?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">ลบ</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isSuperAdmin() ? 5 : 4 }}" class="px-4 py-8 text-center text-slate-500">ยังไม่มีข้อมูลยา</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">{{ $medicines->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
