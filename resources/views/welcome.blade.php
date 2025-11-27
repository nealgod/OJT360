<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>OJT360 - Internship Monitoring System</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ojt-dark antialiased bg-gradient-to-br from-ojt-light via-white to-gray-50">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-lg">OJT</span>
                        </div>
                        <span class="text-xl font-bold text-ojt-primary">OJT360</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="py-20 sm:py-32 bg-gradient-to-br from-ojt-primary/10 via-white to-maroon-700/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-block mb-6 px-4 py-2 bg-ojt-primary/20 rounded-full border border-ojt-primary/30">
                        <span class="text-ojt-primary font-semibold text-sm">🚀 Start Your OJT Today</span>
                    </div>
                    <h1 class="text-4xl sm:text-6xl font-bold text-ojt-dark mb-6 leading-tight">
                        Complete Your
                        <span class="text-ojt-primary">OJT Journey</span>
                        <br>In 6 Simple Steps
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-3xl mx-auto leading-relaxed font-medium">
                        From account activation to weekly reports. A mobile-friendly platform designed for students to manage their internship, coordinators to oversee progress, and supervisors to provide guidance.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-gradient-to-r from-ojt-primary to-maroon-700 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-maroon-700 hover:to-ojt-primary transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-gradient-to-r from-ojt-primary to-maroon-700 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-maroon-700 hover:to-ojt-primary transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Sign In
                            </a>
                            <a href="{{ route('student.verify-id') }}" class="bg-white text-ojt-primary border-2 border-ojt-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-ojt-primary/5 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                Activate Account
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Grid (no horizontal scroll) -->
        <section class="py-16 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ojt-dark mb-3">Your 6-Step Journey</h2>
                    <p class="text-gray-600 text-lg">Follow these steps to complete your OJT</p>
                </div>
                <div class="bg-gradient-to-br from-ojt-primary/5 to-maroon-700/5 border-2 border-ojt-primary/20 rounded-2xl p-8 sm:p-10 shadow-lg">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">1</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">Activate Account</span><div class="text-gray-500">Verify email</div></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">2</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">Complete Registration</span><div class="text-gray-500">Profile & course</div></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">3</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">Document Requirements</span><div class="text-gray-500">Resume & application letter</div></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">4</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">Submit Pre-Requirements</span><div class="text-gray-500">Placement docs</div></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">5</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">OJT Active</span><div class="text-gray-500">Time In/Out</div></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-ojt-primary flex items-center justify-center text-white">6</div>
                            <div class="text-sm"><span class="font-semibold text-ojt-dark">Submit Reports</span><div class="text-gray-500">Weekly logs</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ojt-dark mb-2">Key Features</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to succeed in your OJT</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-2xl border-2 border-ojt-primary/20 shadow-md hover:shadow-xl transition-all duration-300 hover:border-ojt-primary/50 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-ojt-dark mb-3">Document Submission</h3>
                        <p class="text-gray-700 leading-relaxed">Upload your resume and application letter. Submit all pre-requirements for your placement.</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border-2 border-ojt-primary/20 shadow-md hover:shadow-xl transition-all duration-300 hover:border-ojt-primary/50 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-ojt-dark mb-3">Time In/Out Tracking</h3>
                        <p class="text-gray-700 leading-relaxed">Once your OJT is active, log your daily attendance with camera verification on your mobile device.</p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border-2 border-ojt-primary/20 shadow-md hover:shadow-xl transition-all duration-300 hover:border-ojt-primary/50 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-ojt-dark mb-3">Weekly Reports</h3>
                        <p class="text-gray-700 leading-relaxed">Submit weekly reports to track your progress and hours worked during your internship.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-ojt-dark text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-ojt-primary to-maroon-700 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-bold text-lg">OJT</span>
                    </div>
                    <span class="text-xl font-bold text-white">OJT360</span>
                </div>
                <p class="text-gray-300 mb-4">
                    The comprehensive platform for managing on-the-job training programs.
                </p>
                <p class="text-gray-400">&copy; {{ date('Y') }} OJT360. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
