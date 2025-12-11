<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Attendance</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- OJT Completed Notice --}}
            @if(auth()->user()->studentProfile?->ojt_status === 'completed')
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-base font-semibold text-blue-800">OJT Completed</h3>
                            <p class="text-sm text-blue-700 mt-1">
                                Congratulations! You have completed your OJT. Attendance logging is now disabled.
                            </p>
                            <p class="text-xs text-blue-600 mt-2">
                                You can still view your past attendance records below.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 mb-6">
                <!-- Current Time Display -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-lg font-semibold text-blue-800" id="currentTime">{{ now()->format('g:i A') }}</span>
                        <span class="text-sm text-blue-600 ml-2">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>


                @if(auth()->user()->studentProfile?->ojt_status !== 'completed')
                {{-- Only show camera if NOT completed --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time In (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoIn" autoplay playsinline class="w-full h-full object-contain"></video>
                            <canvas id="canvasIn" class="hidden"></canvas>
                            <div id="capturedImageIn" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center">
                                <img id="previewIn" class="max-w-full max-h-full object-contain" />
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3">
                            <button id="openCamIn" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span id="camInText">Open Camera</span>
                            </button>
                            <button id="switchCamIn" class="hidden bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-gray-200 transition-colors" title="Switch Camera">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button id="captureIn" class="bg-ojt-primary text-white px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-maroon-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Capture & Time In
                            </button>
                        </div>
                        
                        <!-- Capture Approval Section -->
                        <div id="approvalSectionIn" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="text-center mb-3">
                                <p class="text-sm font-medium text-gray-700">Review your photo:</p>
                            </div>
                            <div class="flex justify-center space-x-3">
                                <button id="approveIn" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve & Time In
                                </button>
                                <button id="retakeIn" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Retake Photo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time Out (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoOut" autoplay playsinline class="w-full h-full object-contain"></video>
                            <canvas id="canvasOut" class="hidden"></canvas>
                            <div id="capturedImageOut" class="hidden absolute inset-0 bg-gray-900 flex items-center justify-center">
                                <img id="previewOut" class="max-w-full max-h-full object-contain" />
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-3">
                            <button id="openCamOut" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span id="camOutText">Open Camera</span>
                            </button>
                            <button id="switchCamOut" class="hidden bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-gray-200 transition-colors" title="Switch Camera">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button id="captureOut" class="bg-ojt-dark text-white px-4 py-2 rounded-lg text-sm sm:text-base hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Capture & Time Out
                            </button>
                        </div>
                        
                        <!-- Capture Approval Section -->
                        <div id="approvalSectionOut" class="hidden mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="text-center mb-3">
                                <p class="text-sm font-medium text-gray-700">Review your photo:</p>
                            </div>
                            <div class="flex justify-center space-x-3">
                                <button id="approveOut" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve & Time Out
                                </button>
                                <button id="retakeOut" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Retake Photo
                                </button>
                            </div>
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
            @endif
            {{-- End camera section for completed students --}}

            <script>
                (function() {
                    const routes = {
                        in: "{{ route('attendance.timeIn') }}",
                        out: "{{ route('attendance.timeOut') }}",
                    };

                    // Check today's attendance status
                    const todayLog = @json($todayLog);
                    const hasTimedIn = todayLog && todayLog.time_in;
                    const hasTimedOut = todayLog && todayLog.time_out;

                    // Disable buttons based on today's status
                    if (hasTimedIn) {
                        document.getElementById('captureIn').disabled = true;
                        document.getElementById('captureIn').textContent = 'Already Timed In';
                        document.getElementById('openCamIn').disabled = true;
                    }
                    
                    if (hasTimedOut) {
                        document.getElementById('captureOut').disabled = true;
                        document.getElementById('captureOut').textContent = 'Already Timed Out';
                        document.getElementById('openCamOut').disabled = true;
                    }

                    // Live time update
                    function updateTime() {
                        const now = new Date();
                        const timeString = now.toLocaleTimeString('en-US', { 
                            hour: 'numeric', 
                            minute: '2-digit',
                            hour12: true 
                        });
                        document.getElementById('currentTime').textContent = timeString;
                    }
                    
                    // Update time every second
                    setInterval(updateTime, 1000);

                    async function startCamera(videoEl, facingMode = 'user') {
                        try {
                            // Stop any existing streams first
                            if (videoEl.srcObject) {
                                videoEl.srcObject.getTracks().forEach(track => track.stop());
                            }

                            const stream = await navigator.mediaDevices.getUserMedia({ 
                                video: { facingMode: facingMode }, 
                                audio: false 
                            });
                            
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

                    function captureFrame(videoEl, canvasEl, previewEl, containerEl) {
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
                        
                        // Show preview
                        const dataURL = canvasEl.toDataURL('image/jpeg', 0.7);
                        previewEl.src = dataURL;
                        containerEl.classList.remove('hidden');
                        videoEl.style.display = 'none';
                        
                        // Compress to ~70% quality to reduce file size for mobile upload
                        return new Promise(resolve => canvasEl.toBlob(resolve, 'image/jpeg', 0.7));
                    }

                    // Global variables to store captured blobs
                    let capturedBlobIn = null;
                    let capturedBlobOut = null;

                    async function getLocationOrNull() {
                        try {
                            return await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition((pos) => resolve(pos.coords), () => resolve(null), { enableHighAccuracy: true, timeout: 5000 });
                            });
                        } catch { return null; }
                    }


                    // Time In handlers
                    let streamIn = null;
                    let isCameraInOpen = false;
                    let facingModeIn = 'user'; // Default to front camera
                    const videoIn = document.getElementById('videoIn');
                    const canvasIn = document.getElementById('canvasIn');
                    const previewIn = document.getElementById('previewIn');
                    const capturedImageIn = document.getElementById('capturedImageIn');
                    const camInText = document.getElementById('camInText');
                    const switchCamIn = document.getElementById('switchCamIn');
                    
                    document.getElementById('openCamIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        
                        if (!isCameraInOpen) {
                            // Open camera
                            try {
                                streamIn = await startCamera(videoIn, facingModeIn);
                                videoIn.style.display = 'block';
                                capturedImageIn.classList.add('hidden');
                                isCameraInOpen = true;
                                camInText.textContent = 'Close Camera';
                                document.getElementById('openCamIn').classList.remove('bg-gray-100');
                                document.getElementById('openCamIn').classList.add('bg-red-100', 'text-red-800');
                                switchCamIn.classList.remove('hidden');
                            } catch (err) {
                                console.error('Camera error:', err);
                                showError('Failed to open camera. Please try again.');
                            }
                        } else {
                            // Close camera
                            if (streamIn) {
                                streamIn.getTracks().forEach(track => track.stop());
                                streamIn = null;
                            }
                            videoIn.style.display = 'none';
                            isCameraInOpen = false;
                            camInText.textContent = 'Open Camera';
                            document.getElementById('openCamIn').classList.remove('bg-red-100', 'text-red-800');
                            document.getElementById('openCamIn').classList.add('bg-gray-100');
                            switchCamIn.classList.add('hidden');
                        }
                    });

                    switchCamIn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        if (!isCameraInOpen) return;
                        
                        // Toggle mode
                        facingModeIn = facingModeIn === 'user' ? 'environment' : 'user';
                        
                        try {
                            streamIn = await startCamera(videoIn, facingModeIn);
                        } catch (err) {
                            console.error('Camera switch error:', err);
                            showError('Failed to switch camera.');
                        }
                    });
                    
                    document.getElementById('captureIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Capturing...';
                            button.disabled = true;
                            
                            if (!isCameraInOpen || !videoIn.srcObject) {
                                showError('Please open the camera first.');
                                button.textContent = originalText;
                                button.disabled = false;
                                return;
                            }
                            
                            capturedBlobIn = await captureFrame(videoIn, canvasIn, previewIn, capturedImageIn);
                            
                            // Show approval section
                            document.getElementById('approvalSectionIn').classList.remove('hidden');
                            button.textContent = originalText;
                            button.disabled = false;
                        } catch (err) {
                            console.error('Capture error:', err);
                            showError('Failed to capture photo. Please try again.');
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    });

                    // Approve Time In
                    document.getElementById('approveIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Processing...';
                            button.disabled = true;
                            
                            // Create FormData for file upload
                            const formData = new FormData();
                            formData.append('photo_in', new File([capturedBlobIn], `photo_in-${Date.now()}.jpg`, { type: 'image/jpeg' }));
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            // Add location if available
                            const coords = await getLocationOrNull();
                            if (coords) {
                                formData.append('lat_in', coords.latitude);
                                formData.append('lng_in', coords.longitude);
                            }
                            
                            // Submit using fetch
                            // Submitting time in request...
                            const response = await fetch(routes.in, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            // Response status: response.status
                            
                            const responseData = await response.json();
                            // Response data received
                            
                            if (response.ok && responseData.success) {
                                // Time in successful, reloading page...
                                // Show success message before reload
                                showSuccess(responseData.message + ' Redirecting...');
                                // Reload page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                console.error('Server error:', responseData);
                                showError(responseData.message || 'Failed to time in');
                                button.textContent = originalText;
                                button.disabled = false;
                                return;
                            }
                            
                        } catch (err) {
                            console.error('Time in error:', err);
                            showError('Failed to time in. Please try again.');
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    });

                    // Retake Time In
                    document.getElementById('retakeIn').addEventListener('click', (e) => {
                        e.preventDefault();
                        // Hide approval section and show camera again
                        document.getElementById('approvalSectionIn').classList.add('hidden');
                        capturedImageIn.classList.add('hidden');
                        videoIn.style.display = 'block';
                        capturedBlobIn = null;
                    });

                    // Time Out handlers
                    let streamOut = null;
                    let isCameraOutOpen = false;
                    let facingModeOut = 'user'; // Default to front camera
                    const videoOut = document.getElementById('videoOut');
                    const canvasOut = document.getElementById('canvasOut');
                    const previewOut = document.getElementById('previewOut');
                    const capturedImageOut = document.getElementById('capturedImageOut');
                    const camOutText = document.getElementById('camOutText');
                    const switchCamOut = document.getElementById('switchCamOut');
                    
                    document.getElementById('openCamOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        
                        if (!isCameraOutOpen) {
                            // Open camera
                            try {
                                streamOut = await startCamera(videoOut, facingModeOut);
                                videoOut.style.display = 'block';
                                capturedImageOut.classList.add('hidden');
                                isCameraOutOpen = true;
                                camOutText.textContent = 'Close Camera';
                                document.getElementById('openCamOut').classList.remove('bg-gray-100');
                                document.getElementById('openCamOut').classList.add('bg-red-100', 'text-red-800');
                                switchCamOut.classList.remove('hidden');
                            } catch (err) {
                                console.error('Camera error:', err);
                                showError('Failed to open camera. Please try again.');
                            }
                        } else {
                            // Close camera
                            if (streamOut) {
                                streamOut.getTracks().forEach(track => track.stop());
                                streamOut = null;
                            }
                            videoOut.style.display = 'none';
                            isCameraOutOpen = false;
                            camOutText.textContent = 'Open Camera';
                            document.getElementById('openCamOut').classList.remove('bg-red-100', 'text-red-800');
                            document.getElementById('openCamOut').classList.add('bg-gray-100');
                            switchCamOut.classList.add('hidden');
                        }
                    });

                    switchCamOut.addEventListener('click', async (e) => {
                        e.preventDefault();
                        if (!isCameraOutOpen) return;
                        
                        // Toggle mode
                        facingModeOut = facingModeOut === 'user' ? 'environment' : 'user';
                        
                        try {
                            streamOut = await startCamera(videoOut, facingModeOut);
                        } catch (err) {
                            console.error('Camera switch error:', err);
                            showError('Failed to switch camera.');
                        }
                    });
                    
                    document.getElementById('captureOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Capturing...';
                            button.disabled = true;
                            
                            if (!isCameraOutOpen || !videoOut.srcObject) {
                                showError('Please open the camera first.');
                                button.textContent = originalText;
                                button.disabled = false;
                                return;
                            }
                            
                            capturedBlobOut = await captureFrame(videoOut, canvasOut, previewOut, capturedImageOut);
                            
                            // Show approval section
                            document.getElementById('approvalSectionOut').classList.remove('hidden');
                            button.textContent = originalText;
                            button.disabled = false;
                        } catch (err) {
                            console.error('Capture error:', err);
                            showError('Failed to capture photo. Please try again.');
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    });

                    // Approve Time Out
                    document.getElementById('approveOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        const button = e.target;
                        const originalText = button.textContent;
                        
                        try {
                            button.textContent = 'Processing...';
                            button.disabled = true;
                            
                            // Create FormData for file upload
                            const formData = new FormData();
                            formData.append('photo_out', new File([capturedBlobOut], `photo_out-${Date.now()}.jpg`, { type: 'image/jpeg' }));
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            // Add location if available
                            const coords = await getLocationOrNull();
                            if (coords) {
                                formData.append('lat_out', coords.latitude);
                                formData.append('lng_out', coords.longitude);
                            }
                            
                            // Submit using fetch
                            const response = await fetch(routes.out, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            const responseData = await response.json();
                            // Time out response received
                            
                            if (response.ok && responseData.success) {
                                // Show success message before reload
                                showSuccess(responseData.message + ' Redirecting...');
                                // Reload page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showError(responseData.message || 'Failed to time out');
                                button.textContent = originalText;
                                button.disabled = false;
                                return;
                            }
                            
                        } catch (err) {
                            console.error('Time out error:', err);
                            showError('Failed to time out. Please try again.');
                            button.textContent = originalText;
                            button.disabled = false;
                        }
                    });

                    // Retake Time Out
                    document.getElementById('retakeOut').addEventListener('click', (e) => {
                        e.preventDefault();
                        // Hide approval section and show camera again
                        document.getElementById('approvalSectionOut').classList.add('hidden');
                        capturedImageOut.classList.add('hidden');
                        videoOut.style.display = 'block';
                        capturedBlobOut = null;
                    });
                })();
            </script>

            <!-- Attendance Logs Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-ojt-dark">Attendance History</h3>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Time In</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Time Out</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Hours</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Photos</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-900">{{ $log->work_date?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $log->time_in_formatted ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $log->time_out_formatted ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if($log->minutes_worked)
                                            <div class="flex flex-col">
                                                <span class="font-semibold text-ojt-primary">{{ round($log->minutes_worked / 60, 1) }}h</span>
                                                @if($log->overtime_minutes > 0)
                                                    @php
                                                        $otHours = floor($log->overtime_minutes / 60);
                                                        $otMins = $log->overtime_minutes % 60;
                                                    @endphp
                                                    <span class="text-xs text-green-600 font-medium">
                                                        @if($otHours > 0 && $otMins > 0)
                                                            +{{ $otHours }}h {{ $otMins }}m OT
                                                        @elseif($otHours > 0)
                                                            +{{ $otHours }}h OT
                                                        @else
                                                            +{{ $otMins }}m OT
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($log->photo_in_path)
                                                <button onclick="showPhoto('{{ Storage::url($log->photo_in_path) }}', 'Time In - {{ $log->work_date?->format('M d, Y') }}')" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    In
                                                </button>
                                            @endif
                                            @if($log->photo_out_path)
                                                <button onclick="showPhoto('{{ Storage::url($log->photo_out_path) }}', 'Time Out - {{ $log->work_date?->format('M d, Y') }}')" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Out
                                                </button>
                                            @endif
                                            @if(!$log->photo_in_path && !$log->photo_out_path)
                                                <span class="text-xs text-gray-400">No photos</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($log->is_recovered && $log->recovery_approved === null)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                        @elseif($log->is_recovered && $log->recovery_approved === true)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Approved</span>
                                        @elseif($log->is_recovered && $log->recovery_approved === false)
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
                                                <button onclick="openRecoveryModal({{ $log->id }}, '{{ $log->work_date->format('M d, Y') }}')" class="text-xs text-ojt-primary hover:text-maroon-700 underline">Try Again</button>
                                            </div>
                                        @elseif((! $log->time_out || ! $log->minutes_worked) && $log->work_date->lt(now()->startOfDay()))
                                            <div class="flex flex-col gap-1">
                                                 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600">Incomplete</span>
                                                 <button onclick="openRecoveryModal({{ $log->id }}, '{{ $log->work_date->format('M d, Y') }}')" class="text-xs text-ojt-primary hover:text-maroon-700 underline">Recover</button>
                                            </div>
                                        @elseif(! $log->time_out || ! $log->minutes_worked)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">In Progress</span>
                                        @elseif($log->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Complete</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">{{ ucfirst($log->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="font-medium">No attendance logs yet</p>
                                        <p class="text-sm text-gray-400 mt-1">Start by timing in above</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($logs->hasPages())
                <div class="mt-6">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closePhotoModal()">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button onclick="closePhotoModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="bg-white rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 id="photoTitle" class="font-semibold text-gray-900"></h3>
                </div>
                <div class="p-4 bg-gray-50">
                    <img id="photoImage" src="" alt="Attendance Photo" class="w-full h-auto rounded">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPhoto(url, title) {
            document.getElementById('photoImage').src = url;
            document.getElementById('photoTitle').textContent = title;
            document.getElementById('photoModal').classList.remove('hidden');
        }

        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
</x-app-layout>

<!-- Recovery Request Modal -->
<div id="recoveryModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRecoveryModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="recoveryForm" onsubmit="submitRecovery(event)">
                @csrf
                <input type="hidden" id="recoveryLogId" name="log_id">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Request Attendance Recovery
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Submit a request to recover hours for <span id="recoveryDate" class="font-medium text-gray-900"></span>.
                                </p>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="time_out" class="block text-sm font-medium text-gray-700">Time Out</label>
                                        <input type="time" name="time_out" id="time_out" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-ojt-primary focus:border-ojt-primary sm:text-sm">
                                    </div>
                                    
                                    <div>
                                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Recovery</label>
                                        <textarea name="reason" id="reason" rows="3" required
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-ojt-primary focus:border-ojt-primary sm:text-sm"
                                                  placeholder="Explain why you missed logging out..."></textarea>
                                    </div>

                                    <div>
                                        <label for="photo_out" class="block text-sm font-medium text-gray-700">Proof of Attendance</label>
                                        <p class="text-xs text-gray-500 mb-2">Upload a photo, screenshot, or document proving you were present (JPG, PNG).</p>
                                        <input type="file" name="photo_out" id="photo_out" accept="image/jpeg,image/png" required
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="submitRecoveryBtn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ojt-primary text-base font-medium text-white hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ojt-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Submit Request
                    </button>
                    <button type="button" onclick="closeRecoveryModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRecoveryModal(logId, dateStr) {
        document.getElementById('recoveryLogId').value = logId;
        document.getElementById('recoveryDate').textContent = dateStr;
        document.getElementById('recoveryModal').classList.remove('hidden');
    }

    function closeRecoveryModal() {
        document.getElementById('recoveryModal').classList.add('hidden');
        document.getElementById('recoveryForm').reset();
    }

    async function submitRecovery(e) {
        e.preventDefault();
        const btn = document.getElementById('submitRecoveryBtn');
        const originalText = btn.textContent;
        
        try {
            btn.disabled = true;
            btn.textContent = 'Submitting...';
            
            const formData = new FormData(e.target);
            
            const response = await fetch("{{ route('attendance.recovery') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Recovery request submitted successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to submit recovery request.');
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }
</script>


