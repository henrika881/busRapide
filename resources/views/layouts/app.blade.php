<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'BusRapide - Réservez vos billets de bus en ligne')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        const API_BASE_URL = 'http://127.0.0.1:8000/api';
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        accent: {
                            500: '#f59e0b',
                        },
                        success: {
                            500: '#10b981',
                            600: '#059669',
                        },
                        secondary: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                        }
                    },
                    boxShadow: {
                        'search': '0 10px 40px -10px rgba(0, 0, 0, 0.1)',
                        'card': '0 8px 25px -5px rgba(0, 0, 0, 0.08)',
                        'modal': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                        'glow': '0 0 30px rgba(59, 130, 246, 0.2)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: 0 },
                            '100%': { opacity: 1 },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: 0 },
                            '100%': { transform: 'translateY(0)', opacity: 1 },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-toast {
            transform: translateX(100%);
            animation: slideInRight 0.3s forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .hero-pattern {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
            position: relative;
            overflow: hidden;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 font-sans antialiased overflow-x-hidden flex flex-col min-h-screen">

    @include('partials.header')

    <main class="flex-grow">

        <!-- Persistent Alerts Section -->
        <div class="max-w-7xl mx-auto px-4 mt-6">
            @if(session('error') || $errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-6 animate-fade-in">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800">Attention</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ session('error') }}</p>
                                @if($errors->any())
                                    <ul class="list-disc list-inside mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm mb-6 animate-fade-in">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-green-800">Succès</h3>
                            <div class="mt-2 text-sm text-green-700">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

    <!-- Modals -->
    @include('partials.modals')

    @include('partials.scripts')
    @stack('scripts')

    <script>
        // Global utilities

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 flex items-center gap-3 ${type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'}`;
            toast.innerHTML = `
                <i class="fa-solid ${type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check'}"></i>
                <span class="font-bold">${message}</span>
            `;
            const container = document.getElementById('toast-container');
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Mobile Menu Logic
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            const close = document.getElementById('close-mobile-menu');

            if (btn && menu) {
                btn.addEventListener('click', () => menu.classList.add('open'));
            }
            if (close && menu) {
                close.addEventListener('click', () => menu.classList.remove('open'));
            }
        });
    </script>
</body>

</html>