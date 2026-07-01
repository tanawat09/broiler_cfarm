<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">แก้ไขรุ่นการเลี้ยง</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $flock->flock_code }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full px-3 sm:px-4 lg:px-6">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                <form method="POST" action="{{ route('flocks.update', $flock) }}">
                    @method('PUT')
                    @include('flocks._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
