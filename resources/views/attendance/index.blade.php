<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Attendance</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time In (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoIn" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="canvasIn" class="hidden"></canvas>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-3">
                            <button id="openCamIn" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg">Open Camera</button>
                            <button id="captureIn" class="bg-ojt-primary text-white px-4 py-2 rounded-lg">Capture & Time In</button>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time Out (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoOut" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="canvasOut" class="hidden"></canvas>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-3">
                            <button id="openCamOut" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg">Open Camera</button>
                            <button id="captureOut" class="bg-ojt-dark text-white px-4 py-2 rounded-lg">Capture & Time Out</button>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-4">Tip: If camera does not open, check browser permissions and try switching to your device browser (Safari/Chrome).</p>
            </div>

            <script>
                (function() {
                    const routes = {
                        in: "{{ route('attendance.timeIn') }}",
                        out: "{{ route('attendance.timeOut') }}",
                    };

                    async function startCamera(videoEl) {
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                            videoEl.srcObject = stream;
                            if (!videoEl.readyState || videoEl.readyState < 2) {
                                await new Promise(res => videoEl.onloadedmetadata = res);
                            }
                            return stream;
                        } catch (e) {
                            console.warn('Camera error', e);
                            alert('Unable to access camera. You can use photo upload instead.');
                            throw e;
                        }
                    }

                    function captureFrame(videoEl, canvasEl) {
                        // Normalize to max dimension 1280px, preserve aspect ratio
                        const srcW = videoEl.videoWidth || 1280;
                        const srcH = videoEl.videoHeight || 720;
                        const maxDim = 1280;
                        const scale = Math.min(1, maxDim / Math.max(srcW, srcH));
                        const w = Math.round(srcW * scale);
                        const h = Math.round(srcH * scale);
                        canvasEl.width = w;
                        canvasEl.height = h;
                        const ctx = canvasEl.getContext('2d');
                        ctx.drawImage(videoEl, 0, 0, w, h);
                        // Compress to ~70% quality to reduce file size for mobile upload
                        return new Promise(resolve => canvasEl.toBlob(resolve, 'image/jpeg', 0.7));
                    }

                    async function getLocationOrNull() {
                        try {
                            return await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition((pos) => resolve(pos.coords), () => resolve(null), { enableHighAccuracy: true, timeout: 5000 });
                            });
                        } catch { return null; }
                    }

                    async function submitWithPhoto(url, fieldName, blob) {
                        const form = new FormData();
                        const file = new File([blob], `${fieldName}-${Date.now()}.jpg`, { type: 'image/jpeg' });
                        form.append(fieldName, file);
                        form.append('_token', '{{ csrf_token() }}');
                        const coords = await getLocationOrNull();
                        if (coords) {
                            if (fieldName === 'photo_in') {
                                form.append('lat_in', coords.latitude);
                                form.append('lng_in', coords.longitude);
                            } else {
                                form.append('lat_out', coords.latitude);
                                form.append('lng_out', coords.longitude);
                            }
                        }
                        const res = await fetch(url, { method: 'POST', body: form });
                        if (!res.ok) {
                            const text = await res.text();
                            throw new Error(text || 'Request failed');
                        }
                        location.reload();
                    }

                    // Time In handlers
                    let streamIn = null;
                    const videoIn = document.getElementById('videoIn');
                    const canvasIn = document.getElementById('canvasIn');
                    document.getElementById('openCamIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        if (!streamIn) streamIn = await startCamera(videoIn);
                    });
                    document.getElementById('captureIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        try {
                            if (!videoIn.srcObject) streamIn = await startCamera(videoIn);
                            const blob = await captureFrame(videoIn, canvasIn);
                            await submitWithPhoto(routes.in, 'photo_in', blob);
                        } catch (err) {
                            alert('Failed to time in via camera. Please allow camera permission and try again.');
                        }
                    });

                    // Time Out handlers
                    let streamOut = null;
                    const videoOut = document.getElementById('videoOut');
                    const canvasOut = document.getElementById('canvasOut');
                    document.getElementById('openCamOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        if (!streamOut) streamOut = await startCamera(videoOut);
                    });
                    document.getElementById('captureOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        try {
                            if (!videoOut.srcObject) streamOut = await startCamera(videoOut);
                            const blob = await captureFrame(videoOut, canvasOut);
                            await submitWithPhoto(routes.out, 'photo_out', blob);
                        } catch (err) {
                            alert('Failed to time out via camera. Please allow camera permission and try again.');
                        }
                    });
                })();
            </script>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y">
                    @forelse($logs as $log)
                        <div class="p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <p class="text-ojt-dark font-medium">{{ $log->work_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">In: {{ $log->time_in ?? '—' }} • Out: {{ $log->time_out ?? '—' }} • {{ round($log->minutes_worked/60, 2) }} hrs</p>
                            </div>
                            <div class="text-xs">
                                <span class="px-2 py-1 rounded-full 
                                    {{ $log->status === 'approved' ? 'bg-green-100 text-green-800' : ($log->status === 'flagged' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}
                                ">{{ ucfirst($log->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">No attendance logs yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-6">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>


