@php
    // Aggressive Grouping: Merge sites that have the same name OR very similar addresses
    $groupedSites = [];
    foreach ($sites as $site) {
        $name = $site['company_name'];
        $address = $site['company_address'];
        
        // Create a normalized key (Name only, to merge slight address typos)
        $key = strtolower(trim($name));
        
        if (!isset($groupedSites[$key])) {
            $groupedSites[$key] = [
                'name' => $name,
                'address' => $address, // Keep the first address found
                'students' => []
            ];
        }
        
        // Merge student lists
        foreach ($site['students'] as $student) {
            $profile = $student->studentProfile;
            $todayLog = $student->attendanceLogs->first();
            
            // Status Logic: If they have a log today and haven't timed out for the day
            $isActive = $todayLog && (($todayLog->am_in_time && !$todayLog->am_out_time) || ($todayLog->pm_in_time && !$todayLog->pm_out_time));
            
            $completed = $profile->completed_hours ?? 0;
            $required = $student->getRequiredHours();
            $progress = $required > 0 ? round(($completed / $required) * 100) : 0;

            $groupedSites[$key]['students'][] = [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $profile->phone ?? 'N/A',
                'course' => $profile->course ?? 'OJT Student',
                'image' => $profile->profile_image ? Storage::url($profile->profile_image) : null,
                'color' => $student->getAvatarColor(),
                'supervisor' => $profile->supervisor->name ?? 'None Assigned',
                'is_active' => $isActive,
                'progress' => $progress,
            ];
        }
    }

    // Dedup students inside each site (just in case)
    foreach ($groupedSites as &$gs) {
        $gs['students'] = collect($gs['students'])->unique('id')->values()->all();
    }
    unset($gs);

    $finalSites = array_values($groupedSites);
@endphp

<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    
    <style>
        #locator-map { height: 450px; width: 100%; border-radius: 0.75rem; z-index: 10; border: 1px solid #e5e7eb; background: #f3f4f6; }
        .custom-div-icon { background: none; border: none; }
        .marker-pin {
            width: 32px; height: 32px; border-radius: 50% 50% 50% 0; background: #800000;
            position: absolute; transform: rotate(-45deg); left: 50%; top: 50%; margin: -16px 0 0 -16px;
            display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .marker-text { transform: rotate(45deg); color: white; font-weight: bold; font-size: 13px; }
        .leaflet-popup-content-wrapper { border-radius: 8px; padding: 0; overflow: hidden; }
        .leaflet-popup-content { margin: 0; width: 280px !important; }
        .pop-header { background: #800000; color: white; padding: 10px 15px; font-weight: bold; font-size: 13px; }
        .pop-body { padding: 15px; }
        .student-row { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .student-row:last-child { border-bottom: none; }
        .stud-img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 1px solid #f3f4f6; }
        .stud-init { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 11px; }
        
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .pulse { animation: pulse 2s infinite ease-in-out; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Student OJT Locator</h2>
            <a href="{{ route('coord.students.index') }}" class="px-3 py-1.5 rounded-lg bg-ojt-primary text-white text-xs font-medium hover:bg-maroon-700 transition">Back to Student List</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div id="locator-map"></div>
                <div class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest flex items-center justify-between">
                    <span id="map-status">Initializing Tracker...</span>
                    <span id="map-progress">0/{{ count($finalSites) }} Pins Matched</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($finalSites as $index => $site)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between hover:border-ojt-primary transition-colors">
                        <div class="mb-4">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-ojt-primary flex items-center justify-center text-white text-lg font-bold shrink-0">
                                    {{ substr($site['name'], 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-sm font-bold text-gray-900 truncate">{{ $site['name'] }}</h4>
                                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-[8px] font-black text-gray-500 uppercase tracking-tighter">{{ count($site['students']) }} Trainees</span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 line-clamp-1 mt-0.5 italic">{{ $site['address'] }}</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Active Trainees ({{ count($site['students']) }})</p>
                                <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                                    @foreach($site['students'] as $std)
                                        <div class="flex items-center gap-3">
                                            @if($std['image'])
                                                <img src="{{ $std['image'] }}" class="w-7 h-7 rounded-full object-cover border border-gray-100">
                                            @else
                                                <div class="w-7 h-7 rounded-full {{ $std['color'] }} flex items-center justify-center text-[9px] text-white font-bold border border-gray-100">{{ substr($std['name'], 0, 1) }}</div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs text-gray-700 font-bold truncate">{{ $std['name'] }}</p>
                                                <p class="text-[8px] text-gray-400 uppercase">{{ $std['course'] }}</p>
                                            </div>
                                            <a href="{{ route('coord.students.show', $std['id']) }}" class="shrink-0 p-1.5 rounded-lg bg-gray-50 text-ojt-primary hover:bg-ojt-primary hover:text-white transition-all shadow-sm group">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="focusOnSite({{ $index }})" class="py-2 bg-gray-50 text-ojt-primary text-[10px] font-bold rounded-lg border border-gray-200 hover:bg-ojt-primary hover:text-white transition-all uppercase tracking-widest flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Locate
                            </button>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($site['name'] . ', ' . $site['address']) }}" target="_blank" class="py-2 bg-gray-50 text-blue-600 text-[10px] font-bold rounded-lg border border-gray-200 hover:bg-blue-600 hover:text-white transition-all uppercase tracking-widest flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>
                                G-Maps
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const sites = @json($finalSites);
        let map;
        let markers = [];
        let featureGroup;

        function initMap() {
            if (map) return;
            map = L.map('locator-map').setView([11.0083, 124.6111], 13);
            L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'], attribution: '&copy; Google Maps'
            }).addTo(map);
            featureGroup = L.featureGroup().addTo(map);
            loadMarkers();
        }

        async function loadMarkers() {
            featureGroup.clearLayers();
            const statusText = document.getElementById('map-status');
            const progressText = document.getElementById('map-progress');
            let pinsSet = 0;
            const markerPositions = [];

            // Strict Ormoc City bounding box to prevent "wandering" to other streets
            const ormocBounds = '124.57,10.98,124.64,11.05';

            for (let i = 0; i < sites.length; i++) {
                const site = sites[i];
                if (!site.address) continue;

                let locationData = null;
                
                const rawAddress = site.address || '';
                
                // AGGRESSIVE CLEANER: For messy longtext addresses
                // We strip "corner of", "beside", etc., and just take the first important part
                const cleanStreet = rawAddress
                    .replace(/(corner|near|beside|across|opposite|#\d+|room|floor|brgy|barangay|bgry)\s+(of|the)?/gi, '')
                    .split(',')[0]
                    .replace(/(st\.|street|rd\.|road)/gi, '')
                    .trim();
                
                const queries = [
                    site.name + ", Ormoc City",           // 1. Business + City (Most accurate)
                    cleanStreet + ", Ormoc City",         // 2. Primary Street chunk + City
                    site.name + " " + cleanStreet,        // 3. Name + Street combined
                    site.name                             // 4. Last resort: Just name
                ];

                for (let q of queries) {
                    if (!q || q.length < 3) continue;
                    try {
                        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&viewbox=${ormocBounds}&bounded=1&limit=1`;
                        const res = await fetch(url);
                        const data = await res.json();
                        if (data && data.length > 0) { 
                            locationData = data[0]; 
                            break; 
                        }
                    } catch(e) {}
                }

                let finalLat = locationData ? parseFloat(locationData.lat) : 11.0083;
                let finalLon = locationData ? parseFloat(locationData.lon) : 124.6111;
                const isApprox = !locationData;

                const posKey = `${finalLat.toFixed(5)},${finalLon.toFixed(5)}`;
                if (markerPositions.includes(posKey)) {
                    finalLat += 0.00008;
                    finalLon += 0.00008;
                }
                markerPositions.push(`${finalLat.toFixed(5)},${finalLon.toFixed(5)}`);

                let studentHtml = '';
                site.students.forEach(s => {
                    const avatar = s.image ? `<img class="stud-img" src="${s.image}">` : `<div class="stud-init ${s.color}">${s.name[0]}</div>`;
                    const statusClass = s.is_active ? 'bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.8)] pulse' : 'bg-gray-300';
                    
                    studentHtml += `
                        <div class="student-row">
                            <div class="relative">
                                ${avatar}
                                <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-white ${statusClass}"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-[11px] text-gray-900 truncate">${s.name}</div>
                                <div class="text-[9px] text-gray-500 truncate mt-0.5">${s.course}</div>
                                <div class="w-full bg-gray-100 h-1 rounded-full mt-1 overflow-hidden">
                                    <div class="bg-ojt-primary h-full transition-all" style="width: ${s.progress}%"></div>
                                </div>
                            </div>
                        </div>`;
                });

                const popupContent = `
                    <div class="pop-header">${site.name}</div>
                    <div class="pop-body">
                        <p class="text-[9px] text-gray-400 mb-2 italic border-b pb-1">${site.address}</p>
                        <p class="text-[8px] font-black text-ojt-primary uppercase tracking-widest mb-1">Deployed Trainees</p>
                        ${studentHtml}
                    </div>
                `;

                const pinIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class='marker-pin' style='${isApprox ? 'background:#ccc;' : ''}'><span class='marker-text'>${site.name.charAt(0)}</span></div>`,
                    iconSize: [32, 32], iconAnchor: [16, 32]
                });

                const marker = L.marker([finalLat, finalLon], { icon: pinIcon }).addTo(featureGroup);
                marker.bindPopup(popupContent, { offset: [0, -28] });
                markers[i] = marker;
                pinsSet++;
                progressText.textContent = `${pinsSet} / ${sites.length} Active Pins`;

                if (pinsSet === 1) map.setView([finalLat, finalLon], 16, { animate: true });
                if (pinsSet > 1) map.fitBounds(featureGroup.getBounds(), { padding: [60, 60] });

                await new Promise(r => setTimeout(r, 650)); 
            }
            statusText.textContent = "Live Tracker Ready.";
        }

        function focusOnSite(index) {
            const marker = markers[index];
            if (marker) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    map.setView(marker.getLatLng(), 19, { animate: true });
                    marker.openPopup();
                }, 400);
            }
        }

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</x-app-layout>
