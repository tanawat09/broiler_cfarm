<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">เปิดรุ่นการเลี้ยงใหม่</h1>
            <p class="mt-1 text-sm text-gray-600">กรอกข้อมูลรุ่นและจำนวนไก่เริ่มต้นแยกตามเล้า</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full px-3 sm:px-4 lg:px-6">
            @if ($farms->isEmpty())
                <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    ยังไม่มีข้อมูลฟาร์ม กรุณาเพิ่มฟาร์มก่อนเปิดรุ่นการเลี้ยง
                </div>
            @else
                <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                    <form method="POST" action="{{ route('flocks.store') }}">
                        @include('flocks._form')
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
