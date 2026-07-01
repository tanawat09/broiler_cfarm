<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">แก้ไขแหล่งลูกไก่</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $chickSource->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('chick-sources.update', $chickSource) }}">
                    @method('PUT')
                    @include('chick-sources._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
