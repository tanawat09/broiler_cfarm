<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">ฟาร์ม</h1>
                <p class="mt-1 text-sm text-gray-600">จัดการข้อมูลฟาร์มสำหรับระบบบันทึกการเลี้ยงไก่เนื้อ</p>
            </div>
            @if (auth()->user()->isSuperAdmin())
                <a href="{{ route('farms.create') }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    เพิ่มฟาร์ม
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">ชื่อฟาร์ม</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">บริษัท</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">เจ้าของ</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">พื้นที่ต่อเล้า</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">เล้า</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">รุ่น</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($farms as $farm)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                                        <a href="{{ route('farms.show', $farm) }}" class="hover:underline">{{ $farm->farm_name }}</a>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $farm->company_name ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-700">{{ $farm->owner_name ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">
                                        {{ $farm->rearing_area !== null ? number_format($farm->rearing_area) . ' ตร.ม./เล้า' : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">{{ number_format($farm->houses_count) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">{{ number_format($farm->flocks_count) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('farms.edit', $farm) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">แก้ไข</a>
                                            @if (auth()->user()->isSuperAdmin())
                                                <form method="POST" action="{{ route('farms.destroy', $farm) }}" onsubmit="return confirm('ยืนยันลบข้อมูลฟาร์มนี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900">ลบ</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">ยังไม่มีข้อมูลฟาร์ม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($farms->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $farms->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
