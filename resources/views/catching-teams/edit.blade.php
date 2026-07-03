<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between py-2">
            <h1 class="text-xl font-semibold text-gray-900">แก้ไขทีมจับไก่</h1>
            <a href="{{ route('catching-teams.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                กลับหน้ารายการ
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('catching-teams.update', $catchingTeam) }}">
                    @method('PUT')
                    @include('catching-teams._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
