<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">แก้ไขผู้ใช้</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $user->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @method('PUT')
                    @include('users._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
