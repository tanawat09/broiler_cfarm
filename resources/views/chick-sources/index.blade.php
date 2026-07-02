<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">แหล่งลูกไก่</h1>
                <p class="mt-1 text-sm text-gray-600">จัดการรายการแหล่งลูกไก่สำหรับใช้ในหน้าเปิดรุ่น</p>
            </div>
            <a href="{{ route('chick-sources.create') }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มแหล่งลูกไก่
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
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
                                <th class="px-4 py-3 text-left font-medium text-gray-600">ชื่อแหล่งลูกไก่</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600">สถานะ</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($chickSources as $source)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $source->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-center">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $source->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $source->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('chick-sources.edit', $source) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">แก้ไข</a>
                                            <form method="POST" action="{{ route('chick-sources.destroy', $source) }}" onsubmit="return confirm('ยืนยันลบแหล่งลูกไก่นี้หรือไม่?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900">ลบ</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">ยังไม่มีข้อมูลแหล่งลูกไก่</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($chickSources->hasPages())
                    <div class="border-t border-gray-200 px-4 py-3">
                        {{ $chickSources->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
