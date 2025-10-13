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

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h3 class="font-semibold text-ojt-dark mb-2">Time In (Camera)</h3>
                        <div class="aspect-video bg-black rounded-lg overflow-hidden relative">
                            <video id="videoIn" autoplay playsinline class="w-full h-full object-cover"></video>
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
                            <video id="videoOut" autoplay playsinline class="w-full h-full object-cover"></video>
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

            <script>
                (function() {
                    const routes = {
                        in: "{{ route('attendance.timeIn') }}",
                        out: "{{ route('attendance.timeOut') }}",
                    };

                    // Check today's attendance status
                    const todayLog = @json($logs->first());
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
                    const videoIn = document.getElementById('videoIn');
                    const canvasIn = document.getElementById('canvasIn');
                    const previewIn = document.getElementById('previewIn');
                    const capturedImageIn = document.getElementById('capturedImageIn');
                    const camInText = document.getElementById('camInText');
                    
                    document.getElementById('openCamIn').addEventListener('click', async (e) => {
                        e.preventDefault();
                        
                        if (!isCameraInOpen) {
                            // Open camera
                            try {
                                streamIn = await startCamera(videoIn);
                                videoIn.style.display = 'block';
                                capturedImageIn.classList.add('hidden');
                                isCameraInOpen = true;
                                camInText.textContent = 'Close Camera';
                                document.getElementById('openCamIn').classList.remove('bg-gray-100');
                                document.getElementById('openCamIn').classList.add('bg-red-100', 'text-red-800');
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
                            console.log('Submitting time in request...');
                            const response = await fetch(routes.in, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            console.log('Response status:', response.status);
                            console.log('Response ok:', response.ok);
                            
                            const responseData = await response.json();
                            console.log('Response data:', responseData);
                            
                            if (response.ok && responseData.success) {
                                console.log('Time in successful, reloading page...');
                                // Show success message before reload
                                showSuccess(responseData.message + ' Redirecting...');
                                // Reload page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                console.error('Server error:', responseData);
                                showError(responseData.message || 'Failed to time in');
                                throw new Error(responseData.message || 'Request failed');
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
                    const videoOut = document.getElementById('videoOut');
                    const canvasOut = document.getElementById('canvasOut');
                    const previewOut = document.getElementById('previewOut');
                    const capturedImageOut = document.getElementById('capturedImageOut');
                    const camOutText = document.getElementById('camOutText');
                    
                    document.getElementById('openCamOut').addEventListener('click', async (e) => {
                        e.preventDefault();
                        
                        if (!isCameraOutOpen) {
                            // Open camera
                            try {
                                streamOut = await startCamera(videoOut);
                                videoOut.style.display = 'block';
                                capturedImageOut.classList.add('hidden');
                                isCameraOutOpen = true;
                                camOutText.textContent = 'Close Camera';
                                document.getElementById('openCamOut').classList.remove('bg-gray-100');
                                document.getElementById('openCamOut').classList.add('bg-red-100', 'text-red-800');
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
                            console.log('Time out response:', responseData);
                            
                            if (response.ok && responseData.success) {
                                // Show success message before reload
                                showSuccess(responseData.message + ' Redirecting...');
                                // Reload page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showError(responseData.message || 'Failed to time out');
                                throw new Error(responseData.message || 'Request failed');
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

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y">
                    @forelse($logs as $log)
                        <div class="p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <p class="text-ojt-dark font-medium">{{ $log->work_date->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">
                                    In: {{ $log->time_in_formatted }} • Out: {{ $log->time_out_formatted }} • 
                                    <span class="font-medium">{{ round($log->minutes_worked/60, 2) }} hrs</span>
                                </p>
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


