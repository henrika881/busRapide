<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BusRapide - Réservez vos billets de bus en ligne</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
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
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        accent: {
                            500: '#f59e0b',
                        },
                        success: {
                            500: '#10b981',
                            600: '#059669',
                        }
                    },
                    boxShadow: {
                        'search': '0 10px 40px -10px rgba(0, 0, 0, 0.1)',
                        'card': '0 8px 25px -5px rgba(0, 0, 0, 0.08)',
                        'modal': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                    },
                    animation: {
                        'bounce-slow': 'bounce 2s infinite',
                        'bus-scroll': 'busScroll 60s linear infinite',
                        'fade-in': 'fadeIn 0.5s ease-in',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bus-move': 'busMove 20s linear infinite',
                        'bus-move-reverse': 'busMoveReverse 25s linear infinite',
                        'bus-move-slow': 'busMoveSlow 30s linear infinite',
                    },
                    keyframes: {
                        busScroll: {
                            '0%': { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(-100%)' },
                        },
                        fadeIn: {
                            '0%': { opacity: 0 },
                            '100%': { opacity: 1 },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: 0 },
                            '100%': { transform: 'translateY(0)', opacity: 1 },
                        },
                        busMove: {
                            '0%': { transform: 'translateX(-100px)', opacity: 0 },
                            '10%': { opacity: 1 },
                            '90%': { opacity: 1 },
                            '100%': { transform: 'translateX(calc(100vw + 100px))', opacity: 0 },
                        },
                        busMoveReverse: {
                            '0%': { transform: 'translateX(calc(100vw + 100px))', opacity: 0 },
                            '10%': { opacity: 1 },
                            '90%': { opacity: 1 },
                            '100%': { transform: 'translateX(-100px)', opacity: 0 },
                        },
                        busMoveSlow: {
                            '0%': { transform: 'translateX(-50px)', opacity: 0 },
                            '10%': { opacity: 1 },
                            '90%': { opacity: 1 },
                            '100%': { transform: 'translateX(calc(100vw + 50px))', opacity: 0 },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom styles */
        .custom-toast {
            transform: translateX(100%);
            animation: slideInRight 0.3s forwards;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }
        
        .hero-pattern {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .hero-slide.active {
            opacity: 0.4;
        }

        .bus-card {
            transition: all 0.3s ease;
        }

        .bus-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.15);
        }

        .bus-scroll-container {
            position: relative;
            overflow: hidden;
            height: 40px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 100%);
        }

        .bus-scroll-track {
            display: flex;
            position: absolute;
            white-space: nowrap;
            animation: busScroll 60s linear infinite;
        }

        .loading-bar {
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6 0%, #10b981 50%, #3b82f6 100%);
            background-size: 200% 100%;
            animation: loading 2s infinite linear;
            border-radius: 2px;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }

        /* Custom scrollbar */
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

        /* Mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* How-to steps */
        .how-to-step {
            position: relative;
        }

        .how-to-step::before {
            content: '';
            position: absolute;
            width: 2px;
            height: 100%;
            background: #e2e8f0;
            left: 24px;
            top: 40px;
            z-index: 0;
        }

        .how-to-step:last-child::before {
            display: none;
        }

        /* Hero text contrast */
        .hero-text {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtext {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Bus animation tracks */
        .bus-animation-track {
            height: 60px;
        }

        @media (max-width: 768px) {
            .bus-animation-track {
                height: 40px;
            }
            
            .animate-bus-move,
            .animate-bus-move-reverse,
            .animate-bus-move-slow {
                animation-duration: 15s;
            }
            
            .hero-title {
                font-size: 2rem;
                line-height: 2.25rem;
            }
            
            .search-container {
                margin-top: 1rem;
                padding: 1rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .how-to-step::before {
                left: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.75rem;
                line-height: 2rem;
            }
            
            .hero-subtext {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 pb-24 md:pb-0 font-sans antialiased overflow-x-hidden">

    <!-- Bus Scroll Animation -->
    <div class="bus-scroll-container hidden md:block">
        <div class="bus-scroll-track">
            <div class="flex items-center px-4">
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">🚌 Prochain départ: Douala → Yaoundé à 07:30</span>
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">🎫 Réservez en ligne et économisez 10%</span>
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">⭐ Voyagez confortablement avec nos bus climatisés</span>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <div
                    class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 floating">
                    <i class="fa-solid fa-bus text-lg"></i>
                </div>
                <div>
                    <span class="font-bold text-xl text-slate-900 tracking-tight block">BusRapide</span>
                    <span class="text-xs text-slate-500 hidden md:block">Voyagez en toute confiance</span>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="#" class="text-sm font-bold text-brand-600 border-b-2 border-brand-600 pb-1">Accueil</a>
                <a href="#search" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Rechercher</a>
                <a href="#destinations" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Destinations</a>
                <a href="#howto" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Comment ça marche</a>
                <a href="#assistance" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Assistance</a>
            </nav>

            <!-- User Actions -->
            <div class="flex items-center gap-3">
                <!-- User Account - Desktop -->
                <div class="hidden md:flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Bienvenue</p>
                        <p class="text-sm font-bold text-slate-900">Jean Dupont</p>
                    </div>
                    <div class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-brand-600 font-bold">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
                
                <!-- Two buttons for login and register -->
                <div class="flex items-center gap-2">
                    <button onclick="openRegisterModal()"
                        class="bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span class="hidden md:inline">Inscription</span>
                    </button>
                    
                    <button onclick="openLoginModal()"
                        class="bg-brand-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span class="hidden md:inline">Connexion</span>
                    </button>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden w-10 h-10 flex items-center justify-center text-slate-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 z-50 bg-black/50 md:hidden">
        <div class="absolute right-0 top-0 h-full w-64 bg-white shadow-xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fa-solid fa-bus"></i>
                        </div>
                        <span class="font-bold text-xl">BusRapide</span>
                    </div>
                    <button id="close-mobile-menu" class="w-8 h-8 flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <a href="#" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                        <i class="fa-solid fa-house w-6"></i>
                        Accueil
                    </a>
                    <a href="#search" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                        <i class="fa-solid fa-magnifying-glass w-6"></i>
                        Rechercher
                    </a>
                    <a href="#destinations" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                        <i class="fa-solid fa-map-location-dot w-6"></i>
                        Destinations
                    </a>
                    <a href="#howto" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                        <i class="fa-solid fa-question-circle w-6"></i>
                        Comment ça marche
                    </a>
                    <a href="#assistance" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                        <i class="fa-solid fa-headset w-6"></i>
                        Assistance
                    </a>
                </div>
                
                <div class="absolute bottom-6 left-6 right-6 space-y-3">
                    <button onclick="openRegisterModal()" class="w-full bg-white border border-brand-600 text-brand-600 py-3 rounded-xl font-bold">
                        S'inscrire
                    </button>
                    <button onclick="openLoginModal()" class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold">
                        Se connecter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <main>
        <!-- HERO SECTION avec slider d'images -->
        <section class="hero-pattern pt-8 pb-12 md:pt-12 md:pb-24 px-4 relative">
            <!-- Slider d'images -->
            <div class="hero-slider">
                <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=1600')"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1598983062497-5d191c7c8b72?auto=format&fit=crop&q=80&w=1600')"></div>
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=1600')"></div>
            </div>
            
            <!-- Overlay pour meilleur contraste -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-black/20 z-0"></div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="text-white animate-slide-up">
                        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                            <i class="fa-solid fa-bolt text-yellow-300"></i>
                            <span class="text-sm font-bold">Réservez en ligne, voyagez sereinement</span>
                        </div>
                        <h1 class="hero-title text-3xl md:text-5xl font-extrabold mb-6 leading-tight hero-text">
                            Vos trajets en bus,<br>
                            <span class="text-yellow-300">simples et rapides</span>
                        </h1>
                        <p class="hero-subtext text-brand-100 text-base md:text-lg mb-8">
                            Réservez vos billets de bus en ligne en quelques clics. 
                            Plus de 100 destinations à travers le pays.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="#search"
                                class="bg-white text-brand-600 px-6 py-3 rounded-xl font-bold hover:bg-brand-50 transition-all shadow-xl flex items-center gap-2 group animate-fade-in">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                Rechercher un trajet
                            </a>
                            <a href="#howto"
                                class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-xl font-bold hover:bg-white/10 transition-all flex items-center gap-2 animate-fade-in">
                                <i class="fa-solid fa-play-circle"></i>
                                Comment ça marche
                            </a>
                        </div>
                    </div>
                    
                    <!-- Search Engine -->
                    <div id="search" class="search-container bg-white/95 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 shadow-xl animate-slide-up">
                        <h3 class="text-lg md:text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass text-brand-500"></i>
                            Rechercher un voyage
                        </h3>
                        <form class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Départ</label>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-brand-500"></i>
                                        <select class="bg-transparent w-full font-medium text-slate-900 focus:outline-none">
                                            <option value="">Choisir une ville</option>
                                            <option value="douala" selected>Douala</option>
                                            <option value="yaounde">Yaoundé</option>
                                            <option value="bafoussam">Bafoussam</option>
                                            <option value="kribi">Kribi</option>
                                            <option value="buea">Buéa</option>
                                            <option value="garoua">Garoua</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Arrivée</label>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-crosshairs text-slate-500"></i>
                                        <select class="bg-transparent w-full font-medium text-slate-900 focus:outline-none">
                                            <option value="">Choisir une ville</option>
                                            <option value="douala">Douala</option>
                                            <option value="yaounde" selected>Yaoundé</option>
                                            <option value="bafoussam">Bafoussam</option>
                                            <option value="kribi">Kribi</option>
                                            <option value="buea">Buéa</option>
                                            <option value="garoua">Garoua</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Date de voyage</label>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-days text-slate-500"></i>
                                        <input type="date" class="bg-transparent w-full font-medium text-slate-900 focus:outline-none" value="2023-12-25">
                                    </div>
                                </div>

                                <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Passagers</label>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-users text-slate-500"></i>
                                        <select class="bg-transparent w-full font-medium text-slate-900 focus:outline-none">
                                            <option>1 passager</option>
                                            <option>2 passagers</option>
                                            <option>3 passagers</option>
                                            <option>4 passagers</option>
                                            <option>5+ passagers</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-xl text-lg flex items-center justify-center gap-3 shadow-lg shadow-brand-500/30 transition-transform active:scale-[0.98]">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Rechercher les trajets disponibles</span>
                            </button>
                        </form>
                        
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-check text-green-500"></i>
                                Meilleur prix garanti
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-check text-green-500"></i>
                                Paiement sécurisé
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATS -->
        <div class="max-w-7xl mx-auto px-4 -mt-6 md:-mt-12 relative z-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="stat-card bg-white rounded-xl md:rounded-2xl p-3 md:p-4 shadow-card text-center border border-slate-100 animate-fade-in">
                    <p class="text-xl md:text-2xl font-bold text-brand-600">500+</p>
                    <p class="text-xs md:text-sm text-slate-500">Trajets par jour</p>
                </div>
                <div class="stat-card bg-white rounded-xl md:rounded-2xl p-3 md:p-4 shadow-card text-center border border-slate-100 animate-fade-in">
                    <p class="text-xl md:text-2xl font-bold text-green-600">50+</p>
                    <p class="text-xs md:text-sm text-slate-500">Destinations</p>
                </div>
                <div class="stat-card bg-white rounded-xl md:rounded-2xl p-3 md:p-4 shadow-card text-center border border-slate-100 animate-fade-in">
                    <p class="text-xl md:text-2xl font-bold text-purple-600">98%</p>
                    <p class="text-xs md:text-sm text-slate-500">Clients satisfaits</p>
                </div>
                <div class="stat-card bg-white rounded-xl md:rounded-2xl p-3 md:p-4 shadow-card text-center border border-slate-100 animate-fade-in">
                    <p class="text-xl md:text-2xl font-bold text-orange-600">24/7</p>
                    <p class="text-xs md:text-sm text-slate-500">Support client</p>
                </div>
            </div>
        </div>

        <!-- COMMENT ÇA MARCHE -->
        <section id="howto" class="max-w-7xl mx-auto px-4 py-12 md:py-16">
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Comment ça marche ?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Réservez votre billet de bus en 3 étapes simples</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Étape 1 -->
                <div class="how-to-step relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center text-brand-600 text-2xl font-bold mb-4 relative z-10">
                            1
                        </div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg text-slate-900 mb-3">Recherchez votre trajet</h3>
                            <p class="text-slate-600 mb-4">
                                Indiquez votre ville de départ, d'arrivée et la date de voyage. 
                                Notre moteur de recherche vous montre tous les trajets disponibles.
                            </p>
                            <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-500">
                                <i class="fa-solid fa-lightbulb text-brand-500 mr-2"></i>
                                Conseil : Réservez à l'avance pour plus de choix
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Étape 2 -->
                <div class="how-to-step relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl font-bold mb-4 relative z-10">
                            2
                        </div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg text-slate-900 mb-3">Sélectionnez et payez</h3>
                            <p class="text-slate-600 mb-4">
                                Choisissez le trajet qui vous convient, sélectionnez vos sièges 
                                et payez en ligne de manière sécurisée.
                            </p>
                            <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-500">
                                <i class="fa-solid fa-lightbulb text-green-500 mr-2"></i>
                                Paiement sécurisé par carte, mobile money ou virement
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Étape 3 -->
                <div class="how-to-step relative">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-2xl font-bold mb-4 relative z-10">
                            3
                        </div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg text-slate-900 mb-3">Recevez et embarquez</h3>
                            <p class="text-slate-600 mb-4">
                                Recevez votre billet électronique par email ou SMS. 
                                Présentez-le au chauffeur pour embarquer.
                            </p>
                            <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-500">
                                <i class="fa-solid fa-lightbulb text-purple-500 mr-2"></i>
                                Votre billet est également disponible dans votre espace client
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Vidéo démo -->
            <div class="mt-16 bg-slate-50 rounded-2xl p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Voir la démonstration</h3>
                        <p class="text-slate-600 mb-6">
                            Découvrez en 2 minutes comment réserver votre billet en ligne 
                            avec notre plateforme intuitive et sécurisée.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span class="text-slate-700">Interface simple et intuitive</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span class="text-slate-700">Paiement sécurisé en 2 minutes</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span class="text-slate-700">Confirmation immédiate</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-slate-900 rounded-xl p-6 text-center text-white relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-brand-500/10 rounded-full -translate-x-16 -translate-y-16"></div>
                        <div class="absolute bottom-0 right-0 w-32 h-32 bg-green-500/10 rounded-full translate-x-16 translate-y-16"></div>
                        
                        <div class="relative z-10">
                            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-play text-3xl"></i>
                            </div>
                            <h4 class="font-bold text-xl mb-2">Démonstration vidéo</h4>
                            <p class="text-slate-300 mb-6">Regardez comment réserver en moins de 2 minutes</p>
                            <button onclick="playDemoVideo()" class="bg-white text-slate-900 font-bold py-3 px-8 rounded-xl hover:bg-slate-100 transition">
                                <i class="fa-solid fa-play mr-2"></i>
                                Lancer la démo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- POPULAR DESTINATIONS -->
        <section id="destinations" class="bg-white py-12 md:py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-map-marker-alt text-brand-500"></i> Destinations populaires
                        </h2>
                        <p class="text-slate-500">Les trajets les plus recherchés par nos voyageurs</p>
                    </div>
                    <a href="#" class="text-sm font-bold text-brand-600 hover:text-brand-700 hidden md:flex items-center gap-1">
                        Voir toutes les destinations <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Destination 1 -->
                    <div class="bus-card bg-white rounded-xl md:rounded-2xl shadow-card border border-slate-100 overflow-hidden group">
                        <div class="h-40 bg-slate-900 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1598983062497-5d191c7c8b72?auto=format&fit=crop&q=80&w=800"
                                class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-lg">Douala</span>
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                    <span class="font-bold text-lg">Yaoundé</span>
                                </div>
                                <p class="text-sm text-slate-200">À partir de 4 500 FCFA</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2 text-slate-500 text-sm">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-clock"></i> 5h15
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-bus"></i> 12 départs/jour
                                    </span>
                                </div>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">Populaire</span>
                            </div>
                            <button class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                                <i class="fa-solid fa-ticket"></i>
                                Réserver maintenant
                            </button>
                        </div>
                    </div>

                    <!-- Destination 2 -->
                    <div class="bus-card bg-white rounded-xl md:rounded-2xl shadow-card border border-slate-100 overflow-hidden group">
                        <div class="h-40 bg-slate-900 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=800"
                                class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-lg">Bafoussam</span>
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                    <span class="font-bold text-lg">Douala</span>
                                </div>
                                <p class="text-sm text-slate-200">À partir de 3 500 FCFA</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2 text-slate-500 text-sm">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-clock"></i> 4h30
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-bus"></i> 8 départs/jour
                                    </span>
                                </div>
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">Direct</span>
                            </div>
                            <button class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                                <i class="fa-solid fa-ticket"></i>
                                Réserver maintenant
                            </button>
                        </div>
                    </div>

                    <!-- Destination 3 -->
                    <div class="bus-card bg-white rounded-xl md:rounded-2xl shadow-card border border-slate-100 overflow-hidden group">
                        <div class="h-40 bg-slate-900 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=800"
                                class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-lg">Yaoundé</span>
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                    <span class="font-bold text-lg">Kribi</span>
                                </div>
                                <p class="text-sm text-slate-200">À partir de 6 000 FCFA</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2 text-slate-500 text-sm">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-clock"></i> 3h45
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-bus"></i> 6 départs/jour
                                    </span>
                                </div>
                                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded">Plage</span>
                            </div>
                            <button onclick="selectVoyage(1)" class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                                <i class="fa-solid fa-ticket"></i>
                                Réserver maintenant
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-8 md:hidden">
                    <a href="#" class="text-sm font-bold text-brand-600 hover:text-brand-700 flex items-center justify-center gap-1">
                        Voir toutes les destinations <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- WHY CHOOSE US -->
        <section class="bg-slate-50 py-12 md:py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Pourquoi choisir BusRapide ?</h2>
                    <p class="text-slate-500">Nous mettons tout en œuvre pour votre confort et sécurité</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-2">Voyagez en sécurité</h3>
                        <p class="text-slate-500 text-sm">Bus entretenus et chauffeurs expérimentés</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-2">Réservation rapide</h3>
                        <p class="text-slate-500 text-sm">Réservez en 2 minutes depuis votre mobile</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-2">Support 24h/24</h3>
                        <p class="text-slate-500 text-sm">Assistance disponible à tout moment</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-2">Confort optimal</h3>
                        <p class="text-slate-500 text-sm">Wifi, climatisation et sièges confortables</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ASSISTANCE -->
        <section id="assistance" class="bg-white py-12 md:py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Besoin d'assistance ?</h2>
                        <p class="text-slate-600 mb-6">Notre équipe est disponible pour vous aider à tout moment.</p>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center text-brand-600 flex-shrink-0">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Appelez-nous</h4>
                                    <p class="text-slate-500 text-sm mb-1">Service client disponible 24h/24</p>
                                    <a href="tel:+237600000000" class="text-brand-600 font-bold">+237 6 00 00 00 00</a>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 flex-shrink-0">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Écrivez-nous</h4>
                                    <p class="text-slate-500 text-sm mb-1">Réponse sous 2 heures</p>
                                    <a href="mailto:contact@busrapide.cm" class="text-brand-600 font-bold">contact@busrapide.cm</a>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 flex-shrink-0">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">Chat en direct</h4>
                                    <p class="text-slate-500 text-sm mb-1">Discutez avec un agent en direct</p>
                                    <button class="text-brand-600 font-bold">Ouvrir le chat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 rounded-2xl p-6 shadow-card">
                        <h3 class="font-bold text-lg text-slate-900 mb-4">Questions fréquentes</h3>
                        <div class="space-y-4">
                            <div class="border-b border-slate-200 pb-4">
                                <button class="flex justify-between items-center w-full text-left" onclick="toggleFAQ(1)">
                                    <span class="font-medium text-slate-900">Comment annuler mon billet ?</span>
                                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                                </button>
                                <div id="faq-1" class="mt-2 text-slate-600 text-sm hidden">
                                    Vous pouvez annuler votre billet depuis votre compte ou en contactant notre service client. Les frais d'annulation dépendent du délai avant le départ.
                                </div>
                            </div>
                            
                            <div class="border-b border-slate-200 pb-4">
                                <button class="flex justify-between items-center w-full text-left" onclick="toggleFAQ(2)">
                                    <span class="font-medium text-slate-900">Puis-je modifier ma date de voyage ?</span>
                                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                                </button>
                                <div id="faq-2" class="mt-2 text-slate-600 text-sm hidden">
                                    Oui, vous pouvez modifier votre date de voyage jusqu'à 24h avant le départ, sous réserve de disponibilité.
                                </div>
                            </div>
                            
                            <div class="border-b border-slate-200 pb-4">
                                <button class="flex justify-between items-center w-full text-left" onclick="toggleFAQ(3)">
                                    <span class="font-medium text-slate-900">Quels sont les bagages autorisés ?</span>
                                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                                </button>
                                <div id="faq-3" class="mt-2 text-slate-600 text-sm hidden">
                                    Chaque passager a droit à 1 bagage en soute (20kg) et 1 bagage à main (5kg). Des frais supplémentaires s'appliquent pour les excédents.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fa-solid fa-bus"></i>
                        </div>
                        <span class="font-bold text-xl">BusRapide</span>
                    </div>
                    <p class="text-slate-400 text-sm mb-4">Réservez vos billets de bus en ligne simplement et rapidement. Plus de 100 destinations à travers le pays.</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Destinations</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white transition">Douala → Yaoundé</a></li>
                        <li><a href="#" class="hover:text-white transition">Bafoussam → Douala</a></li>
                        <li><a href="#" class="hover:text-white transition">Yaoundé → Kribi</a></li>
                        <li><a href="#" class="hover:text-white transition">Douala → Buea</a></li>
                        <li><a href="#" class="hover:text-white transition">Yaoundé → Garoua</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Informations</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#howto" class="hover:text-white transition">Comment ça marche</a></li>
                        <li><a href="#" class="hover:text-white transition">Conditions générales</a></li>
                        <li><a href="#" class="hover:text-white transition">Politique de confidentialité</a></li>
                        <li><a href="#assistance" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#assistance" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-4">Téléchargez l'app</h3>
                    <p class="text-slate-400 text-sm mb-4">Réservez vos billets depuis votre smartphone</p>
                    <div class="space-y-3">
                        <a href="#" class="inline-flex items-center gap-3 bg-slate-800 hover:bg-slate-700 rounded-xl px-4 py-3 transition">
                            <i class="fa-brands fa-apple text-2xl"></i>
                            <div>
                                <p class="text-xs">Disponible sur</p>
                                <p class="font-bold">App Store</p>
                            </div>
                        </a>
                        <a href="#" class="inline-flex items-center gap-3 bg-slate-800 hover:bg-slate-700 rounded-xl px-4 py-3 transition">
                            <i class="fa-brands fa-google-play text-2xl"></i>
                            <div>
                                <p class="text-xs">Disponible sur</p>
                                <p class="font-bold">Google Play</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 text-center">
                <p class="text-slate-400 text-sm">&copy; 2023 BusRapide. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- MOBILE BOTTOM NAVIGATION -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 pb-safe shadow-lg z-40 md:hidden">
        <div class="flex justify-around items-center h-16 safe-bottom">
            <a href="#" class="flex flex-col items-center justify-center w-full h-full text-brand-600">
                <i class="fa-solid fa-house text-xl mb-1"></i>
                <span class="text-[10px] font-bold">Accueil</span>
            </a>

            <a href="#search"
                class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-xl mb-1"></i>
                <span class="text-[10px] font-medium">Rechercher</span>
            </a>

            <!-- Central How-to Button -->
            <div class="relative -top-6">
                <a href="#howto"
                    class="w-14 h-14 rounded-full bg-brand-600 text-white shadow-xl shadow-brand-500/40 flex items-center justify-center text-2xl transform transition-transform active:scale-95">
                    <i class="fa-solid fa-question"></i>
                </a>
            </div>

            <a href="#destinations"
                class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
                <i class="fa-solid fa-map-marker-alt text-xl mb-1"></i>
                <span class="text-[10px] font-medium">Destinations</span>
            </a>

            <a href="#assistance"
                class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
                <i class="fa-solid fa-headset text-xl mb-1"></i>
                <span class="text-[10px] font-medium">Assistance</span>
            </a>
        </div>
    </nav>

    <!-- LOGIN MODAL -->
    <div id="login-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-4xl overflow-hidden shadow-modal animate-fadeIn flex flex-col md:flex-row">
            <!-- Left Side - Bus Animation & Welcome -->
            <div class="hidden md:flex md:w-2/5 bg-gradient-to-br from-brand-600 to-blue-700 p-8 flex-col justify-center relative overflow-hidden">
                <!-- Animated Bus Background -->
                <div class="absolute inset-0 opacity-10">
                    <div class="bus-animation-track absolute top-1/4 w-full">
                        <i class="fa-solid fa-bus text-white text-4xl absolute left-0 animate-bus-move"></i>
                    </div>
                    <div class="bus-animation-track absolute top-1/2 w-full">
                        <i class="fa-solid fa-bus text-white text-4xl absolute right-0 animate-bus-move-reverse"></i>
                    </div>
                    <div class="bus-animation-track absolute top-3/4 w-full">
                        <i class="fa-solid fa-bus text-white text-3xl absolute left-1/4 animate-bus-move-slow"></i>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10 text-white text-center">
                    <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                        <i class="fa-solid fa-ticket text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Bienvenue à bord !</h3>
                    <p class="text-blue-100 mb-6">Accédez à vos voyages, réservez en un clic et profitez de vos avantages</p>
                    
                    <div class="space-y-3 text-left">
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            <div>
                                <p class="font-bold">Historique complet</p>
                                <p class="text-sm text-blue-200">Tous vos voyages en un seul endroit</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-bolt text-xl"></i>
                            <div>
                                <p class="font-bold">Réservation rapide</p>
                                <p class="text-sm text-blue-200">Enregistrez vos infos pour gagner du temps</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-gift text-xl"></i>
                            <div>
                                <p class="font-bold">Offres exclusives</p>
                                <p class="text-sm text-blue-200">Réductions réservées aux membres</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bus Road Decoration -->
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-yellow-400"></div>
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-yellow-400/50 blur-sm"></div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="md:w-3/5 p-6 md:p-8 relative">
                <button onclick="closeLoginModal()"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200 transition z-10">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center mb-6">
                    <div class="md:hidden w-16 h-16 bg-gradient-to-br from-brand-600 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 mx-auto mb-4">
                        <i class="fa-solid fa-bus text-2xl"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Accédez à votre compte</h2>
                    <p class="text-slate-500">Continuez votre voyage avec nous</p>
                </div>

                <!-- Google Sign In -->
                <button onclick="signInWithGoogle()"
                    class="w-full py-3.5 border-2 border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-50 hover:border-brand-200 transition-all flex items-center justify-center gap-3 mb-6 group">
                    <div class="w-6 h-6 flex items-center justify-center bg-gradient-to-br from-red-500 to-red-600 rounded-full">
                        <i class="fa-brands fa-google text-white text-sm"></i>
                    </div>
                    <span class="group-hover:text-brand-600 transition-colors">Continuer avec Google</span>
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-sm text-slate-500">Ou connectez-vous avec email</span>
                    </div>
                </div>

                <!-- Login Form -->
                <form class="space-y-4">
                    <div class="relative">
                        <div class="absolute left-3 top-3 text-brand-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="login-email"
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all font-medium"
                            placeholder="email@exemple.com" required>
                    </div>

                    <div class="relative">
                        <div class="absolute left-3 top-3 text-brand-500">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="login-password"
                            class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all font-bold"
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('login-password')"
                            class="absolute right-3 top-3 text-slate-400 hover:text-brand-600 transition-colors">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                            <input type="checkbox" class="rounded text-brand-600 focus:ring-brand-500">
                            <span>Se souvenir de moi</span>
                        </label>
                        <button type="button" onclick="showForgotPassword()"
                            class="text-brand-600 font-bold hover:text-brand-700 hover:underline">
                            Mot de passe oublié ?
                        </button>
                    </div>

                    <button type="submit" onclick="handleLogin(event)"
                        class="w-full py-3.5 bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all active:scale-[0.98] mt-2 flex items-center justify-center gap-3 group">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Se connecter
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Nouveau voyageur ?
                        <button onclick="switchToRegister()" class="text-brand-600 font-bold hover:text-brand-700 hover:underline ml-1">
                            Créez votre compte
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- REGISTER MODAL -->
    <div id="register-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-4xl overflow-hidden shadow-modal animate-fadeIn flex flex-col md:flex-row">
            <!-- Left Side - Bus Animation & Welcome -->
            <div class="hidden md:flex md:w-2/5 bg-gradient-to-br from-green-600 to-emerald-700 p-8 flex-col justify-center relative overflow-hidden">
                <!-- Animated Buses Background -->
                <div class="absolute inset-0 opacity-10">
                    <div class="bus-animation-track absolute top-1/4 w-full">
                        <i class="fa-solid fa-bus text-white text-5xl absolute left-0 animate-bus-move"></i>
                        <div class="absolute left-12 top-1/2 transform -translate-y-1/2">
                            <i class="fa-solid fa-user-plus text-2xl"></i>
                        </div>
                    </div>
                    <div class="bus-animation-track absolute top-1/2 w-full">
                        <i class="fa-solid fa-bus text-white text-4xl absolute right-4 animate-bus-move-reverse"></i>
                        <div class="absolute right-16 top-1/2 transform -translate-y-1/2">
                            <i class="fa-solid fa-suitcase text-xl"></i>
                        </div>
                    </div>
                    <div class="bus-animation-track absolute top-3/4 w-full">
                        <i class="fa-solid fa-bus text-white text-3xl absolute left-1/3 animate-bus-move-slow"></i>
                        <div class="absolute left-1/2 top-1/2 transform -translate-y-1/2">
                            <i class="fa-solid fa-ticket text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="relative z-10 text-white text-center">
                    <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                        <i class="fa-solid fa-user-plus text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Commencez votre aventure !</h3>
                    <p class="text-emerald-100 mb-6">Rejoignez notre communauté de voyageurs et profitez d'avantages exclusifs</p>
                    
                    <div class="space-y-3 text-left">
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-percent text-xl"></i>
                            <div>
                                <p class="font-bold">10% de réduction</p>
                                <p class="text-sm text-emerald-200">Sur votre première réservation</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-star text-xl"></i>
                            <div>
                                <p class="font-bold">Programme fidélité</p>
                                <p class="text-sm text-emerald-200">Cumulez des points à chaque voyage</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl">
                            <i class="fa-solid fa-bolt text-xl"></i>
                            <div>
                                <p class="font-bold">Réservation éclair</p>
                                <p class="text-sm text-emerald-200">Enregistrez vos infos pour gagner du temps</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bus Road Decoration -->
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-yellow-400"></div>
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-yellow-400/50 blur-sm"></div>
                
                <!-- Floating travel icons -->
                <div class="absolute top-8 left-8 animate-bounce-slow">
                    <i class="fa-solid fa-passport text-white/30 text-2xl"></i>
                </div>
                <div class="absolute bottom-8 right-8 animate-bounce-slow" style="animation-delay: 0.5s;">
                    <i class="fa-solid fa-map text-white/30 text-2xl"></i>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="md:w-3/5 p-6 md:p-8 relative">
                <button onclick="closeRegisterModal()"
                    class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200 transition z-10">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="text-center mb-6">
                    <div class="md:hidden w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-500/30 mx-auto mb-4">
                        <i class="fa-solid fa-bus text-2xl"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Créez votre compte</h2>
                    <p class="text-slate-500">Commencez votre voyage avec nous</p>
                </div>

                <!-- Registration Form -->
                <form class="space-y-4">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-green-500">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <input type="text" id="register-firstname"
                                class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-medium"
                                placeholder="Prénom" required>
                        </div>
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-green-500">
                                <i class="fa-solid fa-user-tag"></i>
                            </div>
                            <input type="text" id="register-lastname"
                                class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-medium"
                                placeholder="Nom" required>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="relative">
                        <div class="absolute left-3 top-3 text-green-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="register-email"
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-medium"
                            placeholder="email@exemple.com" required>
                    </div>

                    <div class="relative">
                        <div class="absolute left-3 top-3 text-green-500">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" id="register-cni"
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-medium"
                            placeholder="Votre numéro de carte d'identité" required>
                    </div>

                    <!-- Ajoutez ce champ dans votre formulaire d'inscription -->


                    <!-- Phone Field -->
                    <div class="relative">
                        <div class="absolute left-3 top-3 text-green-500">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="flex">
                            <span class="absolute left-10 top-1/2 transform -translate-y-1/2 text-slate-500">+237</span>
                            <input type="tel" id="register-phone"
                                class="w-full pl-16 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-medium"
                                placeholder="6 XX XX XX XX" required>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-green-500">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input type="password" id="register-password"
                                class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-bold"
                                placeholder="Mot de passe" required>
                            <button type="button" onclick="togglePassword('register-password')"
                                class="absolute right-3 top-3 text-slate-400 hover:text-green-600 transition-colors">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="relative">
                            <div class="absolute left-3 top-3 text-green-500">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input type="password" id="register-confirm-password"
                                class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all font-bold"
                                placeholder="Confirmer" required>
                            <button type="button" onclick="togglePassword('register-confirm-password')"
                                class="absolute right-3 top-3 text-slate-400 hover:text-green-600 transition-colors">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-3 text-sm">
                        <input type="checkbox" id="register-terms" class="mt-1 rounded text-green-600 focus:ring-green-500" required>
                        <label for="register-terms" class="text-slate-600">
                            J'accepte les <a href="#" class="text-green-600 font-bold hover:underline">conditions générales</a> et la <a href="#" class="text-green-600 font-bold hover:underline">politique de confidentialité</a>
                        </label>
                    </div>

                    <button type="submit" onclick="handleRegister(event)"
                        class="w-full py-3.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 transition-all active:scale-[0.98] mt-2 flex items-center justify-center gap-3 group">
                        <i class="fa-solid fa-user-plus"></i>
                        Créer mon compte
                    </button>
                </form>

                <!-- Benefits -->
                <div class="mt-4 bg-gradient-to-r from-green-50 to-emerald-50 p-3 rounded-lg border border-green-100">
                    <p class="text-xs text-slate-700 font-bold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-gift text-green-600"></i>
                        <span>Vos avantages immédiats :</span>
                    </p>
                    <ul class="text-xs text-slate-600 space-y-1">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i>
                            <span>10% de réduction sur votre première réservation</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-green-500 text-xs"></i>
                            <span>Suivi de vos voyages en temps réel</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        Déjà un compte ?
                        <button onclick="switchToLogin()" class="text-green-600 font-bold hover:text-green-700 hover:underline ml-1">
                            Connectez-vous
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- VIDEO MODAL -->
    <div id="video-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-3xl p-6 relative shadow-modal animate-fadeIn">
            <button onclick="closeVideoModal()"
                class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:bg-slate-100 transition z-10">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            
            <div class="aspect-video bg-slate-900 rounded-xl flex items-center justify-center">
                <div class="text-center text-white">
                    <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-play text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Démonstration vidéo</h3>
                    <p class="text-slate-300">Cette fonctionnalité serait intégrée dans la version finale</p>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="font-bold text-slate-900 mb-2">Comment réserver en 3 étapes :</h4>
                <ol class="list-decimal pl-5 space-y-2 text-slate-600">
                    <li>Recherchez votre trajet avec notre formulaire intuitif</li>
                    <li>Sélectionnez votre bus et vos sièges préférés</li>
                    <li>Payez en ligne de manière sécurisée et recevez votre billet</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgot-password-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl w-full max-w-sm p-6 relative shadow-modal animate-fadeIn">
            <button onclick="closeForgotPassword()"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mx-auto mb-3">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h3 class="font-bold text-slate-900">Mot de passe oublié</h3>
                <p class="text-sm text-slate-500 mt-1">Entrez votre email pour réinitialiser</p>
            </div>
            
            <form class="space-y-3">
                <input type="email" placeholder="Votre email" 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                    Envoyer le lien
                </button>
            </form>
        </div>
    </div>

    
</body>
</html>

 
// API_BASE_URL est déjà défini dans app.blade.php


// Gestion du token et état utilisateur
let currentUser = null;
    // userToken est déjà défini dans scripts.blade.php

// ==================== FONCTIONS MODALES ====================

// Modal functions
function openLoginModal() {
    document.getElementById('login-modal').classList.remove('hidden');
    document.getElementById('login-modal').style.display = 'flex';
    document.getElementById('mobile-menu').classList.remove('open');
}

function closeLoginModal() {
    const modal = document.getElementById('login-modal');
    modal.style.animation = 'fadeOut 0.3s';
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.animation = '';
    }, 300);
}

function openRegisterModal() {
    document.getElementById('register-modal').classList.remove('hidden');
    document.getElementById('register-modal').style.display = 'flex';
    document.getElementById('mobile-menu').classList.remove('open');
}

function closeRegisterModal() {
    const modal = document.getElementById('register-modal');
    modal.style.animation = 'fadeOut 0.3s';
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.animation = '';
    }, 300);
}

function switchToRegister() {
    closeLoginModal();
    setTimeout(() => {
        openRegisterModal();
    }, 300);
}

function switchToLogin() {
    closeRegisterModal();
    setTimeout(() => {
        openLoginModal();
    }, 300);
}

// ==================== GESTION DES CLICS SUR LES MODALES ====================

// Fermer les modales en cliquant sur le fond
function setupModalCloseListeners() {
    // Modal de connexion
    const loginModal = document.getElementById('login-modal');
    if (loginModal) {
        loginModal.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                closeLoginModal();
            }
        });
    }
    
    // Modal d'inscription
    const registerModal = document.getElementById('register-modal');
    if (registerModal) {
        registerModal.addEventListener('click', function(e) {
            if (e.target === registerModal) {
                closeRegisterModal();
            }
        });
    }
    
    // Modal de mot de passe oublié
    const forgotModal = document.getElementById('forgot-password-modal');
    if (forgotModal) {
        forgotModal.addEventListener('click', function(e) {
            if (e.target === forgotModal) {
                const modal = document.getElementById('forgot-password-modal');
                modal.style.animation = 'fadeOut 0.3s';
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.style.animation = '';
                }, 300);
            }
        });
    }
    
    // Modal vidéo
    const videoModal = document.getElementById('video-modal');
    if (videoModal) {
        videoModal.addEventListener('click', function(e) {
            if (e.target === videoModal) {
                const modal = document.getElementById('video-modal');
                modal.style.animation = 'fadeOut 0.3s';
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.style.animation = '';
                }, 300);
            }
        });
    }
}

// ==================== GESTION DE L'AUTHENTIFICATION ====================

// Connexion
async function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    
    if (!email || !password) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    showToast('Connexion en cours...');
    
    try {
        const response = await fetch(`${API_BASE_URL}/clients/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                motDePasse: password
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Sauvegarder le token
            userToken = data.data.token;
            localStorage.setItem('auth_token', userToken);
            
            showToast('Connexion réussie !', 'success');
            closeLoginModal();
            
            // Mettre à jour l'UI
            currentUser = data.data.client;
            updateUIForLoggedInUser(currentUser);
            
            // Recharger la page pour mettre à jour le menu
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Email ou mot de passe incorrect', 'error');
        }
    } catch (error) {
        console.error('Erreur connexion:', error);
        showToast('Erreur de connexion au serveur', 'error');
    }
}

// Inscription
async function handleRegister(e) {
    e.preventDefault();
    
    const firstname = document.getElementById('register-firstname').value;
    const lastname = document.getElementById('register-lastname').value;
    const email = document.getElementById('register-email').value;
    const phone = document.getElementById('register-phone').value;
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm-password').value;
    const cni = document.getElementById('register-cni').value;
    const terms = document.getElementById('register-terms').checked;
    
    // Validation basique
    if (!firstname || !lastname || !email || !phone || !password || !confirmPassword || !cni) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showToast('Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (!terms) {
        showToast('Veuillez accepter les conditions générales', 'error');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showToast('Format d\'email invalide', 'error');
        return;
    }
    
    if (password.length < 8) {
        showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
        return;
    }
    
    showToast('Inscription en cours...');
    
    try {
        const response = await fetch(`${API_BASE_URL}/clients/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nom: lastname,
                prenom: firstname,
                email: email,
                telephone: phone,
                numeroCNI: cni,
                motDePasse: password,
                motDePasse_confirmation: confirmPassword,
                accept_terms: terms ? 1 : 0
            })
        });
        
        const result = await response.json();
        
        if (response.status === 201 && result.success) {
            // Succès
            showToast('Compte créé avec succès !', 'success');
            
            // Stocker le token
            if (result.data && result.data.token) {
                userToken = result.data.token;
                localStorage.setItem('auth_token', result.data.token);
            }
            
            // Fermer le modal
            closeRegisterModal();
            
            // Rediriger vers la page d'accueil
            setTimeout(() => {
                location.reload();
            }, 1500);
            
        } else if (response.status === 422) {
            // Erreurs de validation
            let errorMessages = [];
            if (result.errors) {
                for (const field in result.errors) {
                    errorMessages = errorMessages.concat(result.errors[field]);
                }
                showToast(errorMessages.join(', '), 'error');
            } else {
                showToast(result.message || 'Erreur de validation', 'error');
            }
        } else {
            // Autres erreurs
            showToast(result.message || 'Erreur lors de l\'inscription', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur de connexion au serveur. Vérifiez votre connexion.', 'error');
    }
}

// Déconnexion
async function logoutUser() {
    if (userToken) {
        try {
            await fetch(`${API_BASE_URL}/clients/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${userToken}`,
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Erreur déconnexion:', error);
        }
    }
    
    // Nettoyer le localStorage
    localStorage.removeItem('auth_token');
    
    // Réinitialiser les variables
    userToken = null;
    currentUser = null;
    
    // Mettre à jour l'UI
    updateUIForGuest();
    showToast('Déconnexion réussie');
    
    // Recharger la page pour mettre à jour le menu mobile
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// ==================== GESTION DE L'UI ====================

// Vérifier l'état d'authentification
async function checkAuthStatus() {
    if (!userToken) {
        updateUIForGuest();
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/clients/profile`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                currentUser = data.data.client;
                updateUIForLoggedInUser(currentUser);
            } else {
                logoutUser();
            }
        } else {
            // Token invalide
            logoutUser();
        }
    } catch (error) {
        console.error('Erreur vérification auth:', error);
        updateUIForGuest();
    }
}

// Mettre à jour l'UI pour utilisateur connecté
function updateUIForLoggedInUser(user) {
    // Mettre à jour le header desktop
    const userInfo = document.querySelector('.header-actions .text-right');
    if (userInfo) {
        userInfo.innerHTML = `
            <p class="text-xs text-slate-500">Bienvenue</p>
            <p class="text-sm font-bold text-slate-900">${user.prenom || ''} ${user.nom || ''}</p>
        `;
    }
    
    // Mettre à jour le bouton de connexion
    const loginButton = document.querySelector('button[onclick="openLoginModal()"]');
    if (loginButton) {
        loginButton.innerHTML = `
            <i class="fa-solid fa-user"></i>
            <span class="hidden md:inline">Mon compte</span>
        `;
        loginButton.onclick = function() {
            window.location.href = '/dashboard.html';
        };
    }
    
    // Mettre à jour le menu mobile
    updateMobileMenuForUser(user);
}

// Mettre à jour l'UI pour invité
function updateUIForGuest() {
    const loginButton = document.querySelector('button[onclick="openLoginModal()"]');
    if (loginButton) {
        loginButton.innerHTML = `
            <i class="fa-solid fa-right-to-bracket"></i>
            <span class="hidden md:inline">Connexion</span>
        `;
        loginButton.onclick = openLoginModal;
    }
}

// Mettre à jour le menu mobile pour l'utilisateur
function updateMobileMenuForUser(user) {
    const mobileMenu = document.getElementById('mobile-menu');
    if (!mobileMenu || !user) return;
    
    // Chercher l'endroit où insérer les infos utilisateur
    const menuContent = mobileMenu.querySelector('div > div');
    if (!menuContent) return;
    
    // Vérifier si la section utilisateur existe déjà
    let userSection = menuContent.querySelector('.user-section');
    if (userSection) {
        userSection.remove();
    }
    
    // Créer la section utilisateur
    userSection = document.createElement('div');
    userSection.className = 'user-section bg-slate-50 rounded-xl p-4 mb-6';
    userSection.innerHTML = `
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-brand-600 rounded-full flex items-center justify-center text-white font-bold">
                ${(user.prenom?.[0] || '') + (user.nom?.[0] || '') || 'U'}
            </div>
            <div>
                <p class="font-bold text-slate-900">${user.prenom || ''} ${user.nom || ''}</p>
                <p class="text-sm text-slate-500">${user.email || ''}</p>
            </div>
        </div>
        <div class="space-y-3">
            <a href="/dashboard.html" class="flex items-center gap-3 text-slate-700 font-medium hover:text-brand-600">
                <i class="fa-solid fa-ticket w-5"></i>
                Mes réservations
            </a>
            <button onclick="logoutUser()" class="flex items-center gap-3 text-red-600 font-medium w-full hover:text-red-700">
                <i class="fa-solid fa-right-from-bracket w-5"></i>
                Déconnexion
            </button>
        </div>
    `;
    
    // Insérer au début du menu
    menuContent.prepend(userSection);
}

// ==================== RECHERCHE DE VOYAGES ====================

// Initialiser le formulaire de recherche
function initializeSearchForm() {
    const searchForm = document.querySelector('#search form');
    if (!searchForm) return;
    
    // Définir la date d'aujourd'hui
    const dateInput = searchForm.querySelector('input[type="date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        dateInput.min = today;
    }
    
    // Gérer la soumission
    searchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const depart = this.querySelectorAll('select')[0].value;
        const arrivee = this.querySelectorAll('select')[1].value;
        const date = this.querySelector('input[type="date"]').value;
        
        if (!depart || !arrivee) {
            showToast('Veuillez sélectionner une ville de départ et d\'arrivée', 'error');
            return;
        }
        
        if (depart === arrivee) {
            showToast('La ville de départ et d\'arrivée doivent être différentes', 'error');
            return;
        }
        
        showToast(`Recherche de ${depart} à ${arrivee}...`);
        
        try {
            const response = await fetch(`${API_BASE_URL}/voyages/search?ville_depart=${depart}&date_voyage=${date}`);
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    displaySearchResults(data.data, depart, arrivee);
                } else {
                    showToast('Aucun voyage trouvé', 'warning');
                }
            } else {
                showToast('Aucun voyage trouvé', 'warning');
            }
        } catch (error) {
            console.error('Erreur recherche:', error);
            showToast('Erreur lors de la recherche', 'error');
        }
    });
}

// ==================== FONCTIONS UTILITAIRES ====================

// Toast notification
function showToast(message, type = 'success') {
    // Créer le toast
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ' + 
        (type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white');
    
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fa-solid ${type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check'}"></i>
            <div>
                <p class="font-bold">${type === 'error' ? 'Erreur' : 'Succès'}</p>
                <p class="text-sm opacity-90">${message}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Animation de sortie
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Password toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}

// FAQ Toggle
function toggleFAQ(id) {
    const faq = document.getElementById(`faq-${id}`);
    const icon = faq.previousElementSibling.querySelector('i');
    faq.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

// ==================== INITIALISATION ====================

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Hero image slider
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const totalSlides = slides.length;
    
    function startHeroSlider() {
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % totalSlides;
            slides[currentSlide].classList.add('active');
        }, 5000);
    }
    
    if (slides.length > 0) {
        startHeroSlider();
    }
    
    // Remplir la date d'aujourd'hui dans le formulaire de recherche
    const dateInput = document.querySelector('input[type="date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        dateInput.min = today;
    }
    
    // Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.add('open');
        });
    }
    
    const closeMobileMenuButton = document.getElementById('close-mobile-menu');
    if (closeMobileMenuButton) {
        closeMobileMenuButton.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.remove('open');
        });
    }
    
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenu) {
        mobileMenu.addEventListener('click', (e) => {
            if (e.target.id === 'mobile-menu') {
                document.getElementById('mobile-menu').classList.remove('open');
            }
        });
    }
    
    // Configurer les fermetures de modales
    setupModalCloseListeners();
    
    // Configurer les formulaires
    const loginForm = document.querySelector('#login-modal form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    const registerForm = document.querySelector('#register-modal form');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    
    // Vérifier l'authentification
    checkAuthStatus();
    
    // Initialiser la recherche
    initializeSearchForm();
    
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                document.getElementById('mobile-menu').classList.remove('open');
            }
        });
    });
});

// ==================== FONCTIONS GLOBALES ====================

// Exposer les fonctions globalement
window.openLoginModal = openLoginModal;
window.closeLoginModal = closeLoginModal;
window.openRegisterModal = openRegisterModal;
window.closeRegisterModal = closeRegisterModal;
window.switchToRegister = switchToRegister;
window.switchToLogin = switchToLogin;
window.togglePassword = togglePassword;
window.toggleFAQ = toggleFAQ;
window.logoutUser = logoutUser;
window.selectVoyage = selectVoyage;

// Fonctions supplémentaires (si elles existent dans votre HTML)
window.showForgotPassword = showForgotPassword;
window.closeForgotPassword = closeForgotPassword;
window.playDemoVideo = playDemoVideo;
window.closeVideoModal = closeVideoModal;
window.signInWithGoogle = signInWithGoogle;

// Définir ces fonctions si elles sont utilisées dans votre HTML
function showForgotPassword() {
    closeLoginModal();
    setTimeout(() => {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }, 300);
}

function closeForgotPassword() {
    const modal = document.getElementById('forgot-password-modal');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.animation = '';
        }, 300);
    }
}

function playDemoVideo() {
    const modal = document.getElementById('video-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeVideoModal() {
    const modal = document.getElementById('video-modal');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.animation = '';
        }, 300);
    }
}

function signInWithGoogle() {
    showToast('Connexion avec Google en cours...');
    setTimeout(() => {
        showToast('Connexion Google réussie !');
        closeLoginModal();
    }, 1500);
}

function selectVoyage(voyageId) {
    if (!userToken) {
        showToast('Veuillez vous connecter pour réserver', 'warning');
        openLoginModal();
        return;
    }
    
    // Rediriger vers la réservation artisan serv
    window.location.href = `/reservation?voyage=${voyageId}`;
}
</script>
</html>