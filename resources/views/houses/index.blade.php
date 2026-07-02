<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">จัดการเล้า</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $farm->farm_name }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('farms.show', $farm) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    กลับรายละเอียดฟาร์ม
                </a>
                <form method="POST" action="{{ route('farms.houses.generate', $farm) }}">
                    @csrf
                    <button type="submit" class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        สร้างเล้า {{ $farm->house_count }} เล้าอัตโนมัติ
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    กรุณาตรวจสอบข้อมูลเล้าอีกครั้ง
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">เลขเล้า</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">ชื่อเล้า</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">สถานะ</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($houseNumbers as $houseNo)
                                @php
                                    $house = $housesByNo->get($houseNo);
                                    $formId = $house ? 'house-form-'.$house->id : null;
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">เล้า {{ $houseNo }}</td>
                                    <td class="min-w-64 px-4 py-3">
                                        @if ($house)
                                            <input
                                                type="text"
                                                name="house_name"
                                                form="{{ $formId }}"
                                                value="{{ old('house_name', $house->house_name) }}"
                                                class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                        @else
                                            <span class="text-gray-400">ยังไม่สร้าง</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if ($house)
                                            <input type="hidden" name="is_active" value="0" form="{{ $formId }}">
                                            <label class="inline-flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    name="is_active"
                                                    value="1"
                                                    form="{{ $formId }}"
                                                    @checked(old('is_active', $house->is_active))
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                >
                                                <span class="{{ $house->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                                    {{ $house->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                                </span>
                                            </label>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        @if ($house)
                                            <form id="{{ $formId }}" method="POST" action="{{ route('farms.houses.update', [$farm, $house]) }}">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <button type="submit" form="{{ $formId }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">
                                                บันทึก
                                            </button>
                                        @else
                                            <span class="text-gray-400">กดสร้างอัตโนมัติ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
