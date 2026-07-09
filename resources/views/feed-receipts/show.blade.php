<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">รายละเอียดรับอาหาร</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ thai_date($feedReceipt->receipt_date) }} | อาหาร {{ $feedReceipt->feed_code }} | เลขที่ผลิต {{ $feedReceipt->production_lot }}
                </p>
            </div>
            <a href="{{ route('feed-receipts.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-5">
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                    <div class="text-sm text-slate-500">ฟาร์ม</div>
                    <div class="mt-1 font-bold text-slate-900">{{ $feedReceipt->farm->farm_name }}</div>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                    <div class="text-sm text-slate-500">รุ่นการเลี้ยง</div>
                    <div class="mt-1 font-bold text-slate-900">{{ $feedReceipt->flock?->flock_code ?? '-' }}</div>
                </div>
                <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">
                    <div class="text-sm text-slate-500">วันที่รับ</div>
                    <div class="mt-1 font-bold text-slate-900">{{ thai_date($feedReceipt->receipt_date) }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-slate-500">เบอร์อาหาร</div>
                    <div class="mt-1 font-bold text-slate-900">{{ $feedReceipt->feed_code }}</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-slate-500">รวมทั้งหมด</div>
                    <div class="mt-1 font-bold text-slate-900">{{ number_format((float) $feedReceipt->items->sum('quantity_kg'), 2) }} กก.</div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                    <h2 class="text-base font-bold text-slate-900">รายการลงเล้า</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">เล้า</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ชื่อเล้า</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">ปริมาณ (กก.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($feedReceipt->items->sortBy('house.house_no') as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-900">เล้า {{ $item->house->house_no }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $item->house->house_name ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format((float) $item->quantity_kg, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-slate-500">หมายเหตุ</div>
                <div class="mt-1 text-slate-800">{{ $feedReceipt->remark ?: '-' }}</div>
            </div>

            <div class="mt-6 flex justify-end">
                <form method="POST" action="{{ route('feed-receipts.destroy', $feedReceipt) }}" onsubmit="return confirm('ยืนยันลบบันทึกรับอาหารนี้?')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>ลบบันทึกนี้</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
