<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                Student OJT Locator
            </h2>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-600">
                    Department: {{ Auth::user()->coordinatorProfile?->department }}
                </span>
                <span class="text-sm text-gray-600">
                    Program: {{ $programName }}
                </span>
                <a href="{{ route('coord.students.index') }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Students
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-blue-100 rounded-xl p-4 sm:p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19.5 8.25a7.5 7.5 0 10-15 0c0 4.142 3.5 7.5 7.5 11.25 4-3.75 7.5-7.108 7.5-11.25z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Locate your OJT students</h3>
                        <p class="mt-1 text-xs sm:text-sm text-gray-600">
                            This view groups students by their assigned company so you can quickly see
                            where they are deployed. Click <span class="font-semibold">“View on Map”</span>
                            to open the company location in Google Maps and review the list of students
                            assigned to that site.
                        </p>
                    </div>
                </div>
            </div>

            @if($sites->isEmpty())
                <div class="bg-white border border-gray-200 rounded-xl p-6 text-center text-sm text-gray-600">
                    <p class="font-medium mb-1">No students with assigned OJT companies found.</p>
                    <p class="text-xs text-gray-500">
                        Once students are assigned to companies in your department and program,
                        they will appear here.
                    </p>
                </div>
            @else
                @if(request()->get('mode') === 'map')
                    @php
                        $index = (int) request()->get('site', 0);
                        $selectedSite = $sites->values()->get($index) ?? $sites->first();
                        $initialAddress = $selectedSite['company_address'] ?? null;
                        $initialName = $selectedSite['company_name'] ?? 'Selected Site';
                    @endphp
                    <!-- MAP MODE: two-column layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">
                        <!-- Left: Sites list -->
                        <div class="lg:col-span-2 space-y-4">
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                        OJT Sites (Your Students)
                                    </p>
                                    <span class="text-[11px] text-gray-500">
                                        {{ $sites->count() }} location{{ $sites->count() === 1 ? '' : 's' }}
                                    </span>
                                </div>
                                <div class="divide-y divide-gray-100 max-h-[540px] overflow-y-auto">
                                    @foreach($sites as $i => $site)
                                        @php
                                            $studentNames = collect($site['students'])->map(function($s) {
                                                return $s->name ?? 'Unknown';
                                            })->values();
                                        @endphp
                                        <button type="button"
                                                onclick="selectLocatorSite('{{ $site['company_name'] }}', '{{ $site['company_address'] }}')"
                                                class="w-full text-left px-4 py-3 hover:bg-blue-50/60 focus:bg-blue-50/80 transition flex items-start gap-3">
                                            <div class="mt-1 flex-shrink-0">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19.5 8.25a7.5 7.5 0 10-15 0c0 4.142 3.5 7.5 7.5 11.25 4-3.75 7.5-7.108 7.5-11.25z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $site['company_name'] }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                                                 bg-blue-50 text-blue-700 border border-blue-100 flex-shrink-0">
                                                        {{ count($site['students']) }} student{{ count($site['students']) === 1 ? '' : 's' }}
                                                    </span>
                                                </div>
                                                @if($site['company_address'])
                                                    <p class="mt-0.5 text-[11px] text-gray-600 truncate">
                                                        {{ $site['company_address'] }}
                                                    </p>
                                                @endif
                                                <p class="mt-1 text-[11px] text-gray-500">
                                                    @if($studentNames->isEmpty())
                                                        No students listed.
                                                    @else
                                                        {{ $studentNames->take(2)->join(', ') }}
                                                        @if($studentNames->count() > 2)
                                                            and {{ $studentNames->count() - 2 }} more
                                                        @endif
                                                    @endif
                                                </p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right: Map -->
                        <div class="lg:col-span-3">
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm h-[480px] flex flex-col">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                            Selected Site Map
                                        </p>
                                        <p id="locatorSiteTitle" class="mt-0.5 text-sm text-gray-800 truncate">
                                            {{ $initialName ?? 'Select a site on the left' }}
                                        </p>
                                    </div>
                                    @if($initialAddress)
                                        <button type="button"
                                                id="locatorOpenInMaps"
                                                onclick="openLocatorInMaps()"
                                                class="inline-flex items-center px-2.5 py-1 rounded-md bg-ojt-primary text-white text-[11px] font-medium hover:bg-maroon-700 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M10 6h8m0 0v8m0-8L5 19" />
                                            </svg>
                                            Open in Google Maps
                                        </button>
                                    @endif
                                </div>
                                <div class="flex-1 bg-gray-100">
                                    @if($initialAddress)
                                        <iframe
                                            id="locatorMapFrame"
                                            src="https://www.google.com/maps?q={{ urlencode($initialAddress) }}&output=embed"
                                            class="w-full h-full border-0"
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
                                    @else
                                        <div id="locatorMapPlaceholder" class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                            Select a site on the left to view it on the map.
                                        </div>
                                    @endif
                                    <input type="hidden" id="locatorCurrentAddress" value="{{ $initialAddress ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- DEFAULT MODE: card grid like before -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($sites as $index => $site)
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                <div class="p-5 space-y-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900">
                                                {{ $site['company_name'] }}
                                            </h3>
                                            @if($site['company_address'])
                                                <p class="mt-1 text-xs text-gray-600">
                                                    {{ $site['company_address'] }}
                                                </p>
                                            @else
                                                <p class="mt-1 text-xs text-gray-400 italic">
                                                    No address provided
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium
                                                         bg-blue-50 text-blue-700 border border-blue-100">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ count($site['students']) }} student{{ count($site['students']) === 1 ? '' : 's' }}
                                            </span>
                                            @if($site['company_address'])
                                                @php
                                                    $studentNames = collect($site['students'])->map(function($s) {
                                                        return $s->name ?? 'Unknown';
                                                    })->values();
                                                    $previewNames = $studentNames->take(3);
                                                    $extraCount = max(0, $studentNames->count() - $previewNames->count());

                                                    $tooltipLines = [];
                                                    foreach ($previewNames as $name) {
                                                        $tooltipLines[] = '• '.$name;
                                                    }
                                                    if ($extraCount > 0) {
                                                        $tooltipLines[] = '+ '.$extraCount.' more';
                                                    }
                                                    $tooltip = 'Students at this site:&#10;'.e(implode('&#10;', $tooltipLines));
                                                @endphp
                                                <a href="{{ route('coord.students.locator', ['mode' => 'map', 'site' => $index]) }}"
                                                   title="{!! $tooltip !!}"
                                                   class="mt-2 inline-flex items-center px-2.5 py-1 rounded-md bg-ojt-primary text-white text-[11px] font-medium hover:bg-maroon-700 transition">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M10 6h8m0 0v8m0-8L5 19" />
                                                    </svg>
                                                    View Map
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-100 pt-3">
                                        <p class="text-[11px] font-semibold text-gray-500 mb-2 uppercase tracking-wide">
                                            Students in this site
                                        </p>
                                        <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                                            @foreach($site['students'] as $student)
                                                @php $studentProfile = $student->studentProfile; @endphp
                                                @if($student && $studentProfile)
                                                    <a href="{{ route('coord.students.show', $student->id) }}"
                                                       class="flex items-start justify-between text-xs text-gray-700 hover:bg-gray-50 px-2 py-1.5 rounded-md transition">
                                                        <div>
                                                            <p class="font-medium text-gray-900">
                                                                {{ $student->name }}
                                                            </p>
                                                            <p class="text-[11px] text-gray-500">
                                                                ID: {{ $studentProfile->student_id ?? 'N/A' }}
                                                                @if($studentProfile->ojt_status)
                                                                    ·
                                                                    <span class="capitalize">
                                                                        {{ $studentProfile->ojt_status }}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <svg class="w-3.5 h-3.5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if(request()->get('mode') === 'map')
        <script>
            function selectLocatorSite(name, address) {
                const titleEl = document.getElementById('locatorSiteTitle');
                const frame = document.getElementById('locatorMapFrame');
                const placeholder = document.getElementById('locatorMapPlaceholder');
                const currentAddressInput = document.getElementById('locatorCurrentAddress');

                if (titleEl) {
                    titleEl.textContent = name || 'Selected Site';
                }

                if (address && frame) {
                    const url = 'https://www.google.com/maps?q=' + encodeURIComponent(address) + '&output=embed';
                    frame.src = url;
                }

                if (placeholder && frame) {
                    placeholder.classList.add('hidden');
                    frame.classList.remove('hidden');
                }

                if (currentAddressInput) {
                    currentAddressInput.value = address || '';
                }
            }

            function openLocatorInMaps() {
                const currentAddressInput = document.getElementById('locatorCurrentAddress');
                const address = currentAddressInput ? currentAddressInput.value : '';
                if (!address) return;

                const url = 'https://www.google.com/maps?q=' + encodeURIComponent(address);
                window.open(url, '_blank');
            }
        </script>
    @endif
</x-app-layout>
