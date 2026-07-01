<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">แก้ไขบันทึกจับไก่ขาย</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $flock->farm->farm_name }} / รุ่น {{ $flock->flock_code }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST" action="{{ route('flocks.sale-records.update', [$flock, $saleRecord]) }}">
                    @method('PUT')
                    <input type="hidden" name="redirect_to" value="{{ request()->query('redirect_to') }}">
                    @include('sale-records._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
