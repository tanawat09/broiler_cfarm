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
                <a href="{{ route('farms.edit', $farm) }}" class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
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
