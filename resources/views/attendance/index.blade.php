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

                <!-- 4-Step Progress Indicator -->
                <div class="relative mb-8 max-w-lg mx-auto">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -z-10 transform -translate-y-1/2 rounded"></div>
                    <div class="flex justify-between w-full px-2">
                        <!-- Step 1: AM In -->
                        <div class="flex flex-col items-center bg-white px-2">
                            <div id="step1" class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-300 bg-white text-gray-500 font-bold text-xs transition-colors">
                                IN
                            </div>
                            <span class="text-xs font-medium text-gray-500 mt-1 uppercase">Morning</span>
                        </div>
                        <!-- Step 2: AM Out -->
                        <div class="flex flex-col items-center bg-white px-2">
                            <div id="step2" class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-300 bg-white text-gray-500 font-bold text-xs transition-colors">
                                OUT
                            </div>
                            <span class="text-xs font-medium text-gray-500 mt-1 uppercase">Lunch</span>
                        </div>
                        <!-- Step 3: PM In -->
                        <div class="flex flex-col items-center bg-white px-2">
                            <div id="step3" class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-300 bg-white text-gray-500 font-bold text-xs transition-colors">
                                IN
                            </div>
                            <span class="text-xs font-medium text-gray-500 mt-1 uppercase">Afternoon</span>
                        </div>
                        <!-- Step 4: PM Out -->
                        <div class="flex flex-col items-center bg-white px-2">
                            <div id="step4" class="w-8 h-8 rounded-full flex items-center justify-center border-2 border-gray-300 bg-white text-gray-500 font-bold text-xs transition-colors">
                                OUT
                            </div>
                            <span class="text-xs font-medium text-gray-500 mt-1 uppercase">Day End</span>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Status Banner -->
                <div id="statusBanner" class="hidden mb-6 p-4 rounded-lg border flex items-start">
                    <div class="flex-shrink-0">
                        <svg id="statusIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium" id="statusTitle">Attendance Status</h3>
                        <div class="mt-1 text-sm" id="statusMessage"></div>
                    </div>
                </div>


                @if(auth()->user()->studentProfile?->ojt_status !== 'completed')
                {{-- Only show camera if NOT completed --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h3 id="headerIn" class="font-semibold text-ojt-dark mb-2">Time In (Morning)</h3>
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
                        <h3 id="headerOut" class="font-semibold text-ojt-dark mb-2">Time Out (Morning)</h3>
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
                    
                    const amIn = todayLog && todayLog.am_in_time;
                    const amOut = todayLog && todayLog.am_out_time;
                    const pmIn = todayLog && todayLog.pm_in_time;
                    const pmOut = todayLog && todayLog.pm_out_time;

                    const btnIn = document.getElementById('captureIn');
                    const btnOut = document.getElementById('captureOut');
                    const camIn = document.getElementById('openCamIn');
                    const camOut = document.getElementById('openCamOut');
                    const headerIn = document.getElementById('headerIn');
                    const headerOut = document.getElementById('headerOut');
                    const containerIn = headerIn.parentElement;
                    const containerOut = headerOut.parentElement;

                    const steps = [
                        document.getElementById('step1'),
                        document.getElementById('step2'),
                        document.getElementById('step3'),
                        document.getElementById('step4')
                    ];
                    const banner = document.getElementById('statusBanner');
                    const bannerTitle = document.getElementById('statusTitle');
bannerMessage = document.getElementById('statusMessage');

                    function updateStep(index, state) {
                        const el = steps[index];
                        el.className = "w-8 h-8 rounded-full flex items-center justify-center border-2 font-bold text-xs transition-colors";
                        
                        if (state === 'done') {
                            el.classList.add('bg-green-500', 'border-green-500', 'text-white');
                            el.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                        } else if (state === 'active') {
                            el.classList.add('bg-white', 'border-blue-600', 'text-blue-600', 'shadow-md', 'scale-110');
                        } else {
                            el.classList.add('bg-white', 'border-gray-300', 'text-gray-400');
                        }
                    }

                    function highlightCard(activeSide) {
                        // Reset
                        containerIn.classList.remove('ring-4', 'ring-blue-400', 'ring-opacity-50', 'bg-blue-50');
                        containerOut.classList.remove('ring-4', 'ring-blue-400', 'ring-opacity-50', 'bg-blue-50');
                        containerIn.classList.add('opacity-50', 'grayscale');
                        containerOut.classList.add('opacity-50', 'grayscale');

                        if (activeSide === 'in') {
                            containerIn.classList.remove('opacity-50', 'grayscale');
                            containerIn.classList.add('ring-4', 'ring-blue-400', 'ring-opacity-50', 'bg-blue-50', 'rounded-xl', 'p-2', 'transition-all');
                        } else if (activeSide === 'out') {
                            containerOut.classList.remove('opacity-50', 'grayscale');
                            containerOut.classList.add('ring-4', 'ring-blue-400', 'ring-opacity-50', 'bg-blue-50', 'rounded-xl', 'p-2', 'transition-all');
                        }
                    }

                    function showBanner(type, title, msg) {
                        banner.classList.remove('hidden', 'bg-blue-50', 'border-blue-200', 'text-blue-800', 'bg-yellow-50', 'border-yellow-200', 'text-yellow-800', 'bg-green-50', 'border-green-200', 'text-green-800');
                        
                        if (type === 'info') {
                            banner.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-800');
                        } else if (type === 'warning') {
                            banner.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-800');
                        } else if (type === 'success') {
                            banner.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
                        }
                        
                        bannerTitle.textContent = title;
                        bannerMessage.textContent = msg;
                    }
                    
                    // Simple Break Calculation
                    function getBreakDuration() {
                        if (todayLog && todayLog.am_out_time && todayLog.pm_in_time) {
                             const start = new Date("2000-01-01 " + todayLog.am_out_time);
                             const end = new Date("2000-01-01 " + todayLog.pm_in_time);
                             const diffMs = end - start;
                             const diffMins = Math.round(diffMs / 60000);
                             const h = Math.floor(diffMins / 60);
                             const m = diffMins % 60;
                             return `${h}h ${m}m`;
                        }
                        return '';
                    }


                    // Reset all first
                    steps.forEach(s => updateStep(steps.indexOf(s), 'pending'));
                    btnIn.disabled = false;
                    camIn.disabled = false;
                    btnOut.disabled = true; // Default disabled until In is done
                    camOut.disabled = true;

                    // State Logic
                    const isFullyDone = pmOut; // Check explicit PM Out (Day End)

                    if (!amIn) {
                        // STATE 1: Ready for AM IN
                        updateStep(0, 'active');
                        highlightCard('in');
                        headerIn.textContent = "Time In (Morning)";
                        headerOut.textContent = "Time Out (Morning)";
                        showBanner('info', 'Good Morning!', 'Please Time In to start your morning shift.');
                    } else if (isFullyDone) {
                        // STATE 5: ALL DONE (Moved up priority to catch Recovered logs)
                        updateStep(0, 'done');
                        updateStep(1, 'done');
                        updateStep(2, 'done');
                        updateStep(3, 'done');
                        highlightCard('none');
                        headerIn.textContent = "Day Complete";
                        headerOut.textContent = "Day Complete";
                        
                        btnIn.disabled = true;
                        camIn.disabled = true;
                        btnOut.disabled = true;
                        camOut.disabled = true;
                        btnIn.textContent = "Done";
                        btnOut.textContent = "Done";
                        
                        const breakTime = getBreakDuration();
                        const breakMsg = breakTime ? ' Total Break: ' + breakTime + '.' : '';
                        showBanner('success', 'Day Complete', 'You have completed your attendance for today.' + breakMsg + ' Great job!');
                        
                    } else if (amIn && !amOut) {
                        // STATE 2: AM IN Done -> Ready for AM OUT
                        updateStep(0, 'done');
                        updateStep(1, 'active');
                        highlightCard('out');
                        headerIn.textContent = "Morning In: " + formatTime(todayLog.am_in_time);
                        headerOut.textContent = "Time Out (Morning)";
                        
                        // Disable In, Enable Out
                        btnIn.disabled = true;
                        camIn.disabled = true;
                        btnIn.textContent = "Checked In (AM)";
                        
                        btnOut.disabled = false;
                        camOut.disabled = false;
                        
                        showBanner('info', 'Morning Shift in Progress', 'Work hard! When you take your lunch break, please Time Out.');
                    } else if (amOut && !pmIn) {
                        // STATE 3: AM OUT Done -> Ready for PM IN
                        updateStep(0, 'done');
                        updateStep(1, 'done');
                        updateStep(2, 'active');
                        highlightCard('in');
                        headerIn.textContent = "Time In (Afternoon)";
                        headerOut.textContent = "Morning Out: " + formatTime(todayLog.am_out_time);
                        
                        // Enable In (for PM), Disable Out
                        btnIn.disabled = false;
                        camIn.disabled = false;
                        btnIn.textContent = "Capture & Time In (PM)"; // Update button text
                        
                        btnOut.disabled = true;
                        camOut.disabled = true;
                        btnOut.textContent = "Checked Out (AM)";
                        
                        showBanner('warning', 'On Lunch Break', 'You are currently clocked out for lunch. Don\'t forget to Time In for the Afternoon!');
                    } else if (pmIn && !isFullyDone) {
                        // STATE 4: PM IN Done -> Ready for PM OUT
                        updateStep(0, 'done');
                        updateStep(1, 'done');
                        updateStep(2, 'done');
                        updateStep(3, 'active');
                        highlightCard('out');
                        headerIn.textContent = "Afternoon In: " + formatTime(todayLog.pm_in_time);
                        headerOut.textContent = "Time Out (Afternoon)";
                        
                        // Disable In, Enable Out
                        btnIn.disabled = true;
                        camIn.disabled = true;
                        btnIn.textContent = "Checked In (PM)";
                        
                        btnOut.disabled = false;
                        camOut.disabled = false;
                        btnOut.textContent = "Capture & Time Out (PM)";
                        
                        const breakTime = getBreakDuration();
                        const breakMsg = breakTime ? ` (Break: ${breakTime})` : '';
                        showBanner('info', 'Afternoon Shift in Progress', 'You are checked in for the afternoon' + breakMsg + '. Don\'t forget to Time Out when you finish!');
                    }

                    function formatTime(timeStr) {
                        if(!timeStr) return '';
                        // Simple parser for HH:MM:SS to H:MM A
                        const [h, m] = timeStr.split(':');
                        const hour = parseInt(h);
                        const ampm = hour >= 12 ? 'PM' : 'AM';
                        const hour12 = hour % 12 || 12;
                        return `${hour12}:${m} ${ampm}`;
                    }

                    // Live time update & Break Timer
                    setInterval(() => {
                        const now = new Date();
                        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        
                        // Live Break Timer (If AM Out is done/exists but PM In is not)
                        if (todayLog && todayLog.am_out_time && !todayLog.pm_in_time) {
                            // Helper to parse "H:i:s" today
                            const [h, m, s] = todayLog.am_out_time.split(':');
                            const amOutDate = new Date();
                            amOutDate.setHours(h, m, s || 0);
                            
                            const diffMs = now - amOutDate;
                            if (diffMs > 0) {
                                const diffMins = Math.floor(diffMs / 60000);
                                const diffSecs = Math.floor((diffMs % 60000) / 1000);
                                const hours = Math.floor(diffMins / 60);
                                const mins = diffMins % 60;
                                
                                let durationStr = '';
                                if (hours > 0) durationStr += `${hours}h `;
                                durationStr += `${mins}m ${diffSecs}s`;
                                
                                const bannerMsg = document.getElementById('statusMessage');
                                if (bannerMsg) {
                                    bannerMsg.textContent = `You are currently clocked out for lunch. (Duration: ${durationStr}). Don't forget to Time In for the Afternoon!`;
                                }
                            }
                        }
                    }, 1000);

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

                    let lastKnownCoords = null;

                    async function getLocationOrNull(forceFresh = false) {
                        if (lastKnownCoords && !forceFresh) return lastKnownCoords;
                        
                        try {
                            // Try High Accuracy first with a longer timeout
                            const coords = await new Promise((resolve, reject) => {
                                navigator.geolocation.getCurrentPosition(
                                    (pos) => resolve(pos.coords), 
                                    () => reject('failed'), 
                                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
                                );
                            });
                            lastKnownCoords = coords;
                            return coords;
                        } catch {
                            // Fallback to low accuracy (Faster, works better indoors/mobile)
                            try {
                                const coords = await new Promise((resolve) => {
                                    navigator.geolocation.getCurrentPosition(
                                        (pos) => resolve(pos.coords), 
                                        () => resolve(null), 
                                        { enableHighAccuracy: false, timeout: 5000 }
                                    );
                                });
                                if (coords) lastKnownCoords = coords;
                                return coords;
                            } catch { return lastKnownCoords; }
                        }
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
                                
                                // Start pre-fetching location in background to warm up GPS
                                getLocationOrNull(true); 
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
                            
                            // REQUIRE Location (Mandatory) -> NOW RELAXED FOR DEV
                            const coords = await getLocationOrNull();
                            if (!coords) {
                                console.warn("GPS failed. Proceeding without location (Dev Mode).");
                            } else {
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

                                // Start pre-fetching location in background to warm up GPS
                                getLocationOrNull(true);
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
                            
                            // REQUIRE Location (Mandatory) -> NOW RELAXED FOR DEV
                            const coords = await getLocationOrNull();
                            if (!coords) {
                                console.warn("GPS failed. Proceeding without location (Dev Mode).");
                            } else {
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
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Date</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Schedule</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Duration</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wide text-xs">Photos</th>
                                    <th class="px-3 py-3 text-center font-medium text-gray-500 uppercase tracking-wide text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 group">
                                    <td class="px-3 py-2 text-gray-900 whitespace-nowrap text-sm font-medium">
                                        {{ $log->work_date?->format('M d') ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 text-xs">
                                        <div class="flex flex-col gap-1">
                                            <!-- AM -->
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400 font-bold w-6">AM</span>
                                                @if($log->am_in_time)
                                                    <span>{{ \Carbon\Carbon::parse($log->am_in_time)->format('g:i a') }}</span>
                                                    <span class="text-gray-300">-</span>
                                                    <span>{{ $log->am_out_time ? \Carbon\Carbon::parse($log->am_out_time)->format('g:i a') : '...' }}</span>
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </div>
                                            <!-- PM -->
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-400 font-bold w-6">PM</span>
                                                @if($log->pm_in_time)
                                                    <span>{{ \Carbon\Carbon::parse($log->pm_in_time)->format('g:i a') }}</span>
                                                    <span class="text-gray-300">-</span>
                                                    <span>{{ $log->pm_out_time ? \Carbon\Carbon::parse($log->pm_out_time)->format('g:i a') : '...' }}</span>
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-xs">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex justify-between w-24">
                                                <span class="text-gray-500">Work:</span>
                                                <span class="font-bold text-gray-900">{{ $log->minutes_worked ? round($log->minutes_worked / 60, 1).'h' : '—' }}</span>
                                            </div>
                                            <div class="flex justify-between w-24">
                                                <span class="text-gray-500">Break:</span>
                                                <span class="text-gray-700">
                                                    @if($log->am_out_time && $log->pm_in_time)
                                                        {{ \Carbon\Carbon::parse($log->am_out_time)->diff(\Carbon\Carbon::parse($log->pm_in_time))->format('%hh %im') }}
                                                    @elseif($log->break_minutes)
                                                        {{ floor($log->break_minutes / 60) }}h {{ $log->break_minutes % 60 }}m
                                                    @else
                                                        —
                                                    @endif
                                                </span>
                                            </div>
                                            @if($log->overtime_minutes > 0)
                                                <div class="flex justify-end mt-1">
                                                    <span class="bg-green-100 text-green-700 border border-green-200 px-1.5 py-0.5 rounded text-[10px] font-bold">
                                                        +{{ round($log->overtime_minutes / 60, 1) }}h OT
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-1">
                                            @foreach(['am_in', 'am_out', 'pm_in', 'pm_out'] as $type)
                                                @php
                                                    $photo = $type.'_photo';
                                                    $hasPhoto = $log->$photo;
                                                @endphp
                                                <button 
                                                    @if($hasPhoto) onclick="showPhoto('{{ Storage::url($hasPhoto) }}', '{{ strtoupper(str_replace('_', ' ', $type)) }}')" @endif
                                                    class="w-6 h-6 rounded flex items-center justify-center transition-all {{ $hasPhoto ? 'bg-blue-100 text-blue-600 hover:bg-blue-200 cursor-pointer' : 'bg-gray-100 text-gray-300 cursor-default' }}"
                                                    title="{{ strtoupper(str_replace('_', ' ', $type)) }}"
                                                    {{ !$hasPhoto ? 'disabled' : '' }}
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                        <td class="px-3 py-2 text-right">
                                            @php
                                                // Check if log is actually complete
                                                // Complete = has OUT time for the last IN punch
                                                $hasAmIn = $log->am_in_time;
                                                $hasAmOut = $log->am_out_time;
                                                $hasPmIn = $log->pm_in_time;
                                                $hasPmOut = $log->pm_out_time;
                                                
                                                // If they started PM shift, they need PM OUT to be complete
                                                // If they only did AM shift, they need AM OUT to be complete
                                                if ($hasPmIn) {
                                                    $isComplete = $hasPmOut; // PM started, need PM OUT
                                                } elseif ($hasAmIn) {
                                                    $isComplete = $hasAmOut; // Only AM, need AM OUT
                                                } else {
                                                    $isComplete = false; // No punches at all
                                                }
                                                
                                                // Check if this is a past date (missed log)
                                                $isPastDate = $log->work_date < today();
                                                $isMissed = $isPastDate && !$isComplete && $log->status !== 'pending';
                                            @endphp
                                            
                                            @if($log->status === 'approved' && $log->is_recovered)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Recovered
                                                </span>
                                            @elseif($log->status === 'approved' && $isComplete)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($log->status === 'pending' && $log->is_recovered)
                                                {{-- Only show "Pending Review" if there's an actual recovery request --}}
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending Review
                                                </span>
                                            @elseif($isMissed)
                                                {{-- Past incomplete log - needs recovery --}}
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200" title="This log is incomplete. Please submit a recovery request.">
                                                    Incomplete
                                                </span>
                                            @elseif($log->is_recovered && $log->recovery_approved === false)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200" title="Your recovery request was rejected. You may request again.">
                                                    Recovery Rejected
                                                </span>
                                            @elseif($log->status === 'rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @else
                                                {{-- Current day in progress --}}
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    In Progress
                                                </span>
                                            @endif
                                        </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
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


@push('scripts')
<script>
    // Attendance page specific scripts (Camera, logic) already defined above in main script block.
</script>
@endpush
</x-app-layout>


