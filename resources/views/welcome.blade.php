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
                            <a href="{{ route('login') }}" class="text-ojt-primary hover:text-ojt-secondary transition-colors duration-200 font-medium">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="bg-ojt-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-maroon-700 transition-colors duration-200">
                                Get Started
                            </a>
                        @endauth
                                </div>
                            </div>
                        </div>
        </nav>

        <!-- Hero Section -->
        <section class="py-20 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-6xl font-bold text-ojt-dark mb-6">
                        Streamline Your
                        <span class="text-ojt-primary">Internship</span>
                        <br>Monitoring
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        OJT360 is the comprehensive platform for managing on-the-job training programs. 
                        Track progress, manage tasks, and ensure successful internship experiences.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-gradient-to-r from-ojt-primary to-maroon-700 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-maroon-700 hover:to-ojt-primary transition-all duration-200 transform hover:scale-105">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-ojt-primary to-maroon-700 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:from-maroon-700 hover:to-ojt-primary transition-all duration-200 transform hover:scale-105">
                                Start Free Trial
                            </a>
                            <a href="{{ route('login') }}" class="border-2 border-ojt-primary text-ojt-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-ojt-primary hover:text-white transition-all duration-200">
                                Sign In
                            </a>
                        @endauth
                                </div>
                            </div>
                        </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-ojt-dark mb-4">
                        Everything You Need for OJT Management
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Comprehensive tools designed specifically for internship monitoring and management
                    </p>
                            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-ojt-primary/5 to-maroon-700/5 p-8 rounded-2xl border border-ojt-primary/10">
                        <div class="w-12 h-12 bg-ojt-primary rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-ojt-dark mb-3">Time Tracking</h3>
                        <p class="text-gray-600">Easily log and track internship hours with our intuitive time management system.</p>
                </div>

                    <div class="bg-gradient-to-br from-ojt-primary/5 to-maroon-700/5 p-8 rounded-2xl border border-ojt-primary/10">
                        <div class="w-12 h-12 bg-ojt-primary rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-ojt-dark mb-3">Progress Monitoring</h3>
                        <p class="text-gray-600">Monitor intern progress with detailed reports and milestone tracking.</p>
                    </div>

                    <div class="bg-gradient-to-br from-ojt-primary/5 to-maroon-700/5 p-8 rounded-2xl border border-ojt-primary/10">
                        <div class="w-12 h-12 bg-ojt-primary rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-ojt-dark mb-3">Task Management</h3>
                        <p class="text-gray-600">Assign and track tasks with real-time updates and completion status.</p>
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
