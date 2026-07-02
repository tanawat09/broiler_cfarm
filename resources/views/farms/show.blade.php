<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ $farm->farm_name }}</h1>
                <p class="mt-1 text-sm text-gray-600">รายละเอียดฟาร์ม</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('farms.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    กลับรายการ
                </a>
                <a href="{{ route('farms.houses.index', $farm) }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    จัดการเล้า
                </a>
                <a href="{{ route('farms.edit', $farm) }}" class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 hover:from-emerald-600 hover:via-emerald-700 hover:to-teal-700 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="h-4 w-4 text-white/80 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    แก้ไข
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-md border border-gray-200 bg-white p-6 shadow-sm">
                <dl class="grid gap-5 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ชื่อฟาร์ม</dt>
                        <dd class="mt-1 text-gray-900">{{ $farm->farm_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">รหัสฟาร์ม</dt>
                        <dd class="mt-1 text-gray-900">{{ $farm->farm_code ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ชื่อบริษัท</dt>
                        <dd class="mt-1 text-gray-900">{{ $farm->company_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ชื่อเจ้าของ</dt>
                        <dd class="mt-1 text-gray-900">{{ $farm->owner_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">จำนวนเล้า</dt>
                        <dd class="mt-1 text-gray-900">{{ number_format($farm->houses_count) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">พื้นที่การเลี้ยงต่อเล้า</dt>
                        <dd class="mt-1 text-gray-900">{{ $farm->rearing_area !== null ? number_format($farm->rearing_area) . ' ตร.ม./เล้า' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">จำนวนรุ่นการเลี้ยง</dt>
                        <dd class="mt-1 text-gray-900">{{ number_format($farm->flocks_count) }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">ที่อยู่</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $farm->address ?: '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">หมายเหตุ</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $farm->note ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
