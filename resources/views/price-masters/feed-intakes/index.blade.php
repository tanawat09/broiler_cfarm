<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between py-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">เกณฑ์มาตรฐานการกินอาหารรายวัน</h1>
                </div>
                <p class="mt-1.5 text-sm text-slate-500 font-medium">
                    กำหนดและเปรียบเทียบเกณฑ์อัตราการกินอาหารรายวัน (หน่วย: <span class="text-slate-800 font-bold">กรัม/ตัว/วัน</span>) ของไก่เนื้อคละเพศ, ตัวผู้, และตัวเมีย
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                    <svg class="h-4 w-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    กลับหน้าหลัก
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto w-full max-w-[96%] px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50/50 p-4 text-sm text-red-800 shadow-sm backdrop-blur">
                    <div class="flex items-center gap-2 font-bold mb-2">
                        <svg class="h-5 w-5 text-red-650" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        พบข้อผิดพลาดในการบันทึกข้อมูล:
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-12 items-start">
                
                <!-- คอลัมน์ซ้าย: ฟอร์มจัดการเกณฑ์มาตรฐาน (Sticky) -->
                <div class="lg:col-span-4 lg:sticky lg:top-24">
                    @if (auth()->user()->isSuperAdmin())
                        <div id="feed_intake_form_panel" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300">
                            <div class="mb-5 border-b border-slate-100 pb-3">
                                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>บันทึกเกณฑ์การกินอาหารรายวัน</span>
                                </h2>
                                <p class="text-xs text-slate-500 mt-1">กรอกข้อมูลอัตรากินอาหารมาตรฐานเพื่อใช้เปรียบเทียบในระบบ</p>
                            </div>

                            <form method="POST" action="{{ route('feed-intake-masters.store') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" id="form_method" name="_method" value="POST">

                                <div>
                                    <x-input-label for="age" value="อายุสะสม (วัน)" class="font-semibold text-slate-700" />
                                    <div class="relative mt-1">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <x-text-input id="age" name="age" type="number" min="0" max="100" class="block w-full pl-9 text-right font-semibold" :value="old('age')" placeholder="เช่น 0, 1, 2" required />
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-4">
                                    <div class="text-xs font-bold text-slate-600 uppercase tracking-wider">อัตราการกินอาหาร (กรัม/ตัว/วัน)</div>
                                    
                                    <div>
                                        <x-input-label for="feed_ah" value="ฝูงคละเพศ (AH)" class="text-emerald-800 font-semibold" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">AH</span>
                                            </div>
                                            <x-text-input id="feed_ah" name="feed_ah" type="number" min="0" step="0.01" class="block w-full pl-12 text-right font-bold text-emerald-700 border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500" :value="old('feed_ah')" placeholder="0.00" required />
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="feed_male" value="ไก่ตัวผู้ (Male)" class="text-indigo-850 font-semibold" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-xs font-bold text-indigo-650 bg-indigo-50 px-1.5 py-0.5 rounded">M</span>
                                            </div>
                                            <x-text-input id="feed_male" name="feed_male" type="number" min="0" step="0.01" class="block w-full pl-12 text-right font-bold text-indigo-700 border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500" :value="old('feed_male')" placeholder="0.00" required />
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label for="feed_female" value="ไก่ตัวเมีย (Female)" class="text-rose-800 font-semibold" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-xs font-bold text-rose-650 bg-rose-50 px-1.5 py-0.5 rounded">F</span>
                                            </div>
                                            <x-text-input id="feed_female" name="feed_female" type="number" min="0" step="0.01" class="block w-full pl-12 text-right font-bold text-rose-700 border-rose-300 focus:border-rose-500 focus:ring-rose-500" :value="old('feed_female')" placeholder="0.00" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 flex flex-col gap-2">
                                    <button type="submit" id="submit_btn" class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 hover:shadow transition-all duration-150">
                                        บันทึกเกณฑ์มาตรฐาน
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-5 shadow-sm backdrop-blur">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-sky-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-bold text-sky-900">เกณฑ์การกินอาหารมาตรฐาน</h3>
                                    <p class="text-xs text-sky-700 mt-1 leading-relaxed">
                                        ข้อมูลชุดนี้เป็นเกณฑ์มาตรฐานส่วนกลางของระบบที่ใช้ในการคำนวณเปรียบเทียบในฟาร์มและเล้าต่างๆ หากพบข้อมูลไม่ถูกต้องหรือต้องการปรับแก้กรุณาติดต่อผู้ดูแลระบบ (Admin)
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- คอลัมน์ขวา: ตารางฐานข้อมูลการกินอาหาร (Unified Scrollable Table) -->
                <div class="lg:col-span-8">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        
                        <!-- เครื่องมือค้นหาและฟิลเตอร์ด่วน (Premium Filters Panel) -->
                        <div class="border-b border-slate-100 bg-slate-50/50 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">ตารางอ้างอิงอัตรากินอาหารมาตรฐาน</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">ค้นหาข้อมูลตามช่วงอายุ หรือกรองด่วนตามสัปดาห์</p>
                                </div>
                                <div class="relative w-full sm:w-48">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="doc_search" onkeyup="filterBySearch()" placeholder="ค้นหาอายุ..." class="block w-full rounded-lg border-slate-300 pl-9 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                            </div>

                            <!-- แท็บสัปดาห์ (Quick Javascript Filters) -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" onclick="filterByWeek('all', this)" class="week-filter-btn rounded-lg bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition-all">
                                    ทั้งหมด
                                </button>
                                <button type="button" onclick="filterByWeek('w1-2', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 1 - 2 (วัน 0-14)
                                </button>
                                <button type="button" onclick="filterByWeek('w3-4', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 3 - 4 (วัน 15-28)
                                </button>
                                <button type="button" onclick="filterByWeek('w5-6', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 5 - 6 (วัน 29-42)
                                </button>
                                <button type="button" onclick="filterByWeek('w7-8', this)" class="week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all">
                                    สัปดาห์ 7 - 8 (วัน 43-56)
                                </button>
                            </div>
                        </div>

                        <!-- ตารางข้อมูลหลัก (Scrollable body with Sticky Headers) -->
                        <div class="max-h-[640px] overflow-y-auto" style="scrollbar-width: thin;">
                            <table class="w-full text-left text-sm text-slate-700 border-collapse">
                                <thead class="sticky top-0 bg-slate-50 text-slate-600 shadow-sm z-10">
                                    <tr class="border-b border-slate-100">
                                        <th class="px-5 py-3.5 font-bold">อายุ</th>
                                        <th class="px-5 py-3.5 font-bold">อายุ (สัปดาห์)</th>
                                        <th class="px-5 py-3.5 text-right font-bold text-emerald-800 bg-emerald-50/40">AH (กรัม/ตัว)</th>
                                        <th class="px-5 py-3.5 text-right font-bold text-indigo-800 bg-indigo-50/40">Male (กรัม/ตัว)</th>
                                        <th class="px-5 py-3.5 text-right font-bold text-rose-800 bg-rose-50/40">Female (กรัม/ตัว)</th>
                                        @if (auth()->user()->isSuperAdmin())
                                            <th class="px-5 py-3.5 text-center font-bold">การกระทำ</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="feed_intake_tbody" class="divide-y divide-slate-100 bg-white">
                                    @forelse ($records as $row)
                                        @php
                                            $week = floor($row->age / 7) + 1;
                                            $weekGroup = $week <= 2 ? 'w1-2' : ($week <= 4 ? 'w3-4' : ($week <= 6 ? 'w5-6' : 'w7-8'));
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 transition-colors duration-150 feed-row" data-age="{{ $row->age }}" data-week-group="{{ $weekGroup }}">
                                            <td class="whitespace-nowrap px-5 py-3 font-bold text-slate-900 border-l-2 border-transparent hover:border-emerald-500 pl-4 transition-all">
                                                {{ $row->age }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-3">
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                                    {{ $week }} สัปดาห์
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-3 text-right font-bold text-emerald-700 bg-emerald-50/20">
                                                {{ number_format($row->feed_ah, 1) }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-3 text-right font-bold text-indigo-700 bg-indigo-50/20">
                                                {{ number_format($row->feed_male, 1) }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-3 text-right font-bold text-rose-700 bg-rose-50/20">
                                                {{ number_format($row->feed_female, 1) }}
                                            </td>
                                            @if (auth()->user()->isSuperAdmin())
                                                <td class="whitespace-nowrap px-5 py-3 text-center">
                                                    <div class="inline-flex items-center gap-3 justify-center">
                                                        <button type="button"
                                                                onclick="editRecord(this)"
                                                                data-id="{{ $row->id }}"
                                                                data-age="{{ $row->age }}"
                                                                data-ah="{{ (float) $row->feed_ah }}"
                                                                data-male="{{ (float) $row->feed_male }}"
                                                                data-female="{{ (float) $row->feed_female }}"
                                                                class="inline-flex items-center text-xs font-bold text-emerald-650 hover:text-emerald-950 transition-colors">
                                                            <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                            </svg>
                                                            แก้ไข
                                                        </button>
                                                        <form method="POST" action="{{ route('feed-intake-masters.destroy', $row) }}" onsubmit="return confirm('ยืนยันลบเกณฑ์มาตรฐานของช่วงอายุ {{ $row->age }} วันนี้หรือไม่?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center text-xs font-bold text-red-600 hover:text-red-950 transition-colors">
                                                                <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->isSuperAdmin() ? 6 : 5 }}" class="px-5 py-12 text-center text-slate-400">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0017.25 3H6.75A2.25 2.25 0 004.5 5.25v6a2.25 2.25 0 002.25 2.25z" />
                                                    </svg>
                                                    <span class="font-medium text-slate-500">ยังไม่มีข้อมูลเกณฑ์มาตรฐานการกินอาหาร</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <script>
            let currentFilter = 'all';

            window.editRecord = (btn) => {
                const age = btn.dataset.age;
                const ah = btn.dataset.ah;
                const male = btn.dataset.male;
                const female = btn.dataset.female;

                // 1. Change header title
                const header = document.querySelector('#feed_intake_form_panel h2');
                if (header) {
                    header.innerHTML = `
                        <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>แก้ไขเกณฑ์การกินอาหารรายวัน (อายุ ${age} วัน)</span>
                    `;
                }

                // 2. Populate inputs
                document.getElementById('age').value = age;
                document.getElementById('feed_ah').value = ah;
                document.getElementById('feed_male').value = male;
                document.getElementById('feed_female').value = female;

                // 3. Highlight form
                const formPanel = document.getElementById('feed_intake_form_panel');
                if (formPanel) {
                    formPanel.classList.add('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
                    formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                // 4. Add reset button next to save button
                let cancelBtn = document.getElementById('cancel_edit_btn');
                if (!cancelBtn) {
                    cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.id = 'cancel_edit_btn';
                    cancelBtn.className = 'w-full mt-2 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors';
                    cancelBtn.textContent = 'ยกเลิกการแก้ไข';
                    cancelBtn.onclick = resetFormToCreate;
                    
                    const form = document.querySelector('#feed_intake_form_panel form');
                    if (form) {
                        form.appendChild(cancelBtn);
                    }
                }
            };

            window.resetFormToCreate = () => {
                // 1. Reset title
                const header = document.querySelector('#feed_intake_form_panel h2');
                if (header) {
                    header.innerHTML = `
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>บันทึกเกณฑ์การกินอาหารรายวัน</span>
                    `;
                }

                // 2. Clear inputs
                document.getElementById('age').value = '';
                document.getElementById('feed_ah').value = '';
                document.getElementById('feed_male').value = '';
                document.getElementById('feed_female').value = '';

                // 3. Clear highlight
                const formPanel = document.getElementById('feed_intake_form_panel');
                if (formPanel) {
                    formPanel.classList.remove('ring-2', 'ring-amber-500', 'border-transparent', 'shadow-amber-100');
                }

                // 4. Remove cancel edit button
                const cancelBtn = document.getElementById('cancel_edit_btn');
                if (cancelBtn) {
                    cancelBtn.remove();
                }
            };

            // Instant Client-side Filters
            window.filterByWeek = (weekGroup, btn) => {
                currentFilter = weekGroup;
                
                // Style active button
                document.querySelectorAll('.week-filter-btn').forEach(b => {
                    b.className = 'week-filter-btn rounded-lg border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-all';
                });
                btn.className = 'week-filter-btn rounded-lg bg-emerald-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition-all';

                applyFilters();
            };

            window.filterBySearch = () => {
                applyFilters();
            };

            function applyFilters() {
                const searchVal = document.getElementById('doc_search').value.toLowerCase().trim();
                const rows = document.querySelectorAll('.feed-row');

                rows.forEach(row => {
                    const rowAge = row.dataset.age;
                    const rowWeekGroup = row.dataset.weekGroup;

                    const matchesWeek = (currentFilter === 'all' || rowWeekGroup === currentFilter);
                    const matchesSearch = (!searchVal || rowAge.includes(searchVal));

                    if (matchesWeek && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
