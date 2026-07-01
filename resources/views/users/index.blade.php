<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-900">ผู้ใช้</h1>
                <p class="mt-1 text-sm text-slate-600">จัดการบัญชี Super Admin และ Farm Manager</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                เพิ่มผู้ใช้
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ชื่อ</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">อีเมล</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">สิทธิ์</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-600">ฟาร์ม</th>
                                <th class="px-4 py-3 text-right font-medium text-slate-600">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($users as $row)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $row->name }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $row->email }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $row->isSuperAdmin() ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $row->isSuperAdmin() ? 'Super Admin' : 'Farm Manager' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $row->farm?->farm_name ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('users.edit', $row) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">แก้ไข</a>
                                            @if ((int) auth()->id() !== (int) $row->id)
                                                <form method="POST" action="{{ route('users.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบผู้ใช้นี้หรือไม่?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-800">ลบ</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">ยังไม่มีผู้ใช้</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
