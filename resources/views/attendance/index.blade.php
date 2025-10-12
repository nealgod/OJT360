<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Attendance</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 mb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time In (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoIn" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="canvasIn" class="hidden"></canvas>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3">
                            <button id="openCamIn" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base">Open Camera</button>
                            <button id="captureIn" class="bg-ojt-primary text-white px-4 py-2 rounded-lg text-sm sm:text-base">Capture & Time In</button>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time Out (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoOut" autoplay playsinline class="w-full h-full object-cover"></video>
                            <canvas id="canvasOut" class="hidden"></canvas>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3">
                            <button id="openCamOut" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base">Open Camera</button>
                            <button id="captureOut" class="bg-ojt-dark text-white px-4 py-2 rounded-lg text-sm sm:text-base">Capture & Time Out</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Camera Tips:</p>
                            <ul class="text-xs text-blue-700 mt-1 space-y-1">
                                <li>• Allow camera permissions when prompted</li>
                                <li>• Use a well-lit environment for better photos</li>
                                <li>• Hold device steady when capturing</li>
                                <li>• Photos are automatically resized and compressed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    const routes = {
                        in: "{{ route('attendance.timeIn') }}",
                        out: "{{ route('attendance.timeOut') }}",
                    };

                    async function startCamera(videoEl) {
                        try {
                            // Try back camera first, then any camera
                            let stream;
                            try {
                                stream = await navigator.mediaDevices.getUserMedia({ 
                                    video: { facingMode: 'environment' }, 
                                    audio: false 
                                });
                            } catch (e) {
                                // Back camera failed, trying any camera
                                stream = await navigator.mediaDevices.getUserMedia({ 
                                    video: true, 
                                    audio: false 
                                });
                            }
                            
                            videoEl.srcObject = stream;
                            if (!videoEl.readyState || videoEl.readyState < 2) {
                                await new Promise(res => videoEl.onloadedmetadata = res);
                            }
                            return stream;
                        } catch (e) {
                            console.warn('Camera error', e);
                            showError('Camera access denied. Please allow camera permissions and refresh the page.');
                            throw e;
                        }
                    }

                    function showError(message) {
                        // Create a more user-friendly error display
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50 max-w-sm';
                        errorDiv.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">${message}</span>
                            </div>
                        `;
                        document.body.appendChild(errorDiv);
                        setTimeout(() => errorDiv.remove(), 5000);
                    }

                    function showSuccess(message) {
                        const successDiv = document.createElement('div');
                        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50 max-w-sm';
                        successDiv.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">${message}</span>
                            </div>
                        `;
                        document.body.appendChild(successDiv);
                        setTimeout(() => successDiv.remove(), 3000);
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
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Capturing...';
                            button.disabled = true;
                            
                            if (!videoIn.srcObject) streamIn = await startCamera(videoIn);
                            const blob = await captureFrame(videoIn, canvasIn);
                            await submitWithPhoto(routes.in, 'photo_in', blob);
                            showSuccess('Time in successful!');
                        } catch (err) {
                            console.error('Time in error:', err);
                            showError('Failed to time in. Please try again.');
                        } finally {
                            button.textContent = originalText;
                            button.disabled = false;
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
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Capturing...';
                            button.disabled = true;
                            
                            if (!videoOut.srcObject) streamOut = await startCamera(videoOut);
                            const blob = await captureFrame(videoOut, canvasOut);
                            await submitWithPhoto(routes.out, 'photo_out', blob);
                            showSuccess('Time out successful!');
                        } catch (err) {
                            console.error('Time out error:', err);
                            showError('Failed to time out. Please try again.');
                        } finally {
                            button.textContent = originalText;
                            button.disabled = false;
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


