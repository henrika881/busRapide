
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Paiement Sécurisé | BusRapide Express</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css pour animations fluides -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="api.js"></script>
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
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                        },
                        accent: {
                            500: '#f59e0b',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'slide-up-mobile': 'slideUpMobile 0.4s ease-out forwards',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                        'pulse-subtle': 'pulseSubtle 2s infinite',
                        'wave': 'wave 1.5s ease-in-out infinite',
                        'shake': 'shake 0.5s ease-in-out',
                        'checkmark': 'checkmark 0.5s ease-in-out forwards',
                        'progress': 'progress 3s ease-in-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideUpMobile: {
                            '0%': { transform: 'translateY(100%)' },
                            '100%': { transform: 'translateY(0)' },
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(-5px)', animationTimingFunction: 'cubic-bezier(0.8,0,1,1)' },
                            '50%': { transform: 'translateY(0)', animationTimingFunction: 'cubic-bezier(0,0,0.2,1)' },
                        },
                        pulseSubtle: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' },
                        },
                        wave: {
                            '0%': { transform: 'translateX(-100%)' },
                            '50%': { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(100%)' },
                        },
                        shake: {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-5px)' },
                            '20%, 40%, 60%, 80%': { transform: 'translateX(5px)' },
                        },
                        checkmark: {
                            '0%': { strokeDashoffset: '100', opacity: '0' },
                            '50%': { opacity: '1' },
                            '100%': { strokeDashoffset: '0', opacity: '1' },
                        },
                        progress: {
                            '0%': { width: '0%' },
                            '100%': { width: '100%' },
                        }
                    },
                    boxShadow: {
                        'mobile-bottom': '0 -4px 20px -5px rgba(0, 0, 0, 0.1)',
                        'glow': '0 0 30px rgba(59, 130, 246, 0.2)',
                        'glow-green': '0 0 30px rgba(16, 185, 129, 0.2)',
                        'inner-lg': 'inset 0 2px 10px 0 rgba(0, 0, 0, 0.06)',
                        'card': '0 10px 40px -15px rgba(0, 0, 0, 0.1)',
                        'card-hover': '0 20px 60px -20px rgba(59, 130, 246, 0.3)',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3b82f6, #10b981);
            border-radius: 4px;
        }

        /* Input Animations */
        .input-group:focus-within label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #2563eb;
        }

        .clean-input::placeholder {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .clean-input:focus::placeholder {
            opacity: 0.5;
        }

        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Card Shimmer Effect */
        .card-shimmer {
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.6) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Custom Checkbox */
        .custom-checkbox {
            position: relative;
            cursor: pointer;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .custom-checkbox input:checked ~ .checkmark {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            content: "";
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* Payment Card Styling */
        .payment-card {
            perspective: 1000px;
        }

        .payment-card-inner {
            position: relative;
            width: 100%;
            height: 200px;
            transform-style: preserve-3d;
            transition: transform 0.8s;
        }

        .payment-card:hover .payment-card-inner {
            transform: rotateY(5deg) rotateX(5deg);
        }

        .payment-card-front,
        .payment-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .payment-card-front {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .payment-card-back {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: rotateY(180deg);
        }

        /* Ripple Effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .ripple:focus:after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }

        /* Mobile Adjustments */
        @media (max-width: 640px) {
            body {
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            }

            .mobile-card {
                border-radius: 16px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
                margin: 8px;
            }

            .safe-area-bottom {
                padding-bottom: calc(env(safe-area-inset-bottom, 20px) + 10px);
            }

            /* Better tap targets */
            input,
            button,
            select {
                font-size: 16px !important;
                min-height: 44px;
            }

            .touch-target {
                min-height: 44px;
                min-width: 44px;
            }
        }

        /* Loading Bar */
        .loading-bar {
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b);
            background-size: 200% 100%;
            animation: loadingBar 2s infinite linear;
        }

        @keyframes loadingBar {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Glass Effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
</head>

<body class="gradient-bg text-slate-800 antialiased min-h-screen font-sans">

    <!-- Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-white transition-opacity duration-300">
        <div class="text-center">
            <div class="relative w-24 h-24 mx-auto mb-8">
                <div class="absolute inset-0 border-4 border-brand-100 rounded-full"></div>
                <div class="absolute inset-4 border-4 border-t-brand-600 border-r-brand-600 border-b-brand-600 border-l-transparent rounded-full animate-spin"></div>
                <div class="absolute inset-8 bg-brand-600 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-bus text-white text-2xl"></i>
                </div>
            </div>
            <p class="text-brand-600 font-bold text-lg animate-pulse">Chargement sécurisé...</p>
        </div>
    </div>

    <!-- Header App Style -->
    <header
        class="glass-effect border-b border-slate-200/50 sticky top-0 z-40 transition-all duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="#" class="touch-target w-10 h-10 flex items-center justify-center rounded-full hover:bg-slate-100 active:bg-slate-200 transition-colors md:hidden">
                    <i class="fa-solid fa-arrow-left text-slate-700"></i>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-600 to-brand-800 rounded-xl flex items-center justify-center shadow-lg shadow-brand-500/30">
                        <i class="fa-solid fa-bus text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-lg tracking-tight text-slate-900 leading-none">BusRapide <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-secondary-500">Express</span></span>
                        <span class="text-xs text-slate-500 font-medium">Paiement Sécurisé</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Progress Steps -->
                <div class="hidden md:flex items-center">
                    <div class="flex flex-col items-center mx-2">
                        <div class="w-8 h-8 rounded-full bg-secondary-500 text-white flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>
                        <span class="text-xs mt-1 text-slate-600">Trajet</span>
                    </div>
                    <div class="w-16 h-1 bg-secondary-200"></div>
                    <div class="flex flex-col items-center mx-2">
                        <div class="w-8 h-8 rounded-full bg-secondary-500 text-white flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>
                        <span class="text-xs mt-1 text-slate-600">Passagers</span>
                    </div>
                    <div class="w-16 h-1 bg-brand-200"></div>
                    <div class="flex flex-col items-center mx-2">
                        <div class="w-8 h-8 rounded-full bg-white border-2 border-brand-600 text-brand-600 flex items-center justify-center shadow-lg relative">
                            <span class="font-bold text-sm">3</span>
                            <div class="notification-badge animate-pulse-subtle"></div>
                        </div>
                        <span class="text-xs mt-1 font-bold text-slate-800">Paiement</span>
                    </div>
                </div>

                <!-- Help Button -->
                <button onclick="showHelp()" class="touch-target w-10 h-10 rounded-full hover:bg-slate-100 active:bg-slate-200 transition-colors flex items-center justify-center text-slate-600 relative">
                    <i class="fa-solid fa-circle-question"></i>
                </button>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="h-1 w-full bg-slate-100 overflow-hidden">
            <div class="loading-bar"></div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 md:py-8">
        <!-- Breadcrumb Mobile -->
        <div class="md:hidden mb-4">
            <div class="flex items-center text-xs text-slate-500">
                <a href="#" class="hover:text-brand-600">Trajet</a>
                <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                <a href="#" class="hover:text-brand-600">Passagers</a>
                <i class="fa-solid fa-chevron-right mx-2 text-xs"></i>
                <span class="font-bold text-brand-600">Paiement</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-6 lg:gap-8">

            <!-- Left Column: Content -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Main Header -->
                <div class="animate-fade-in-up">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Finalisez votre réservation</h1>
                    <p class="text-slate-600">Sécurisez vos places pour <span class="font-bold text-brand-600">Paris → Lyon</span> du <span class="font-bold">25 Décembre 2025</span></p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center">
                                <i class="fa-solid fa-users text-brand-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Passagers</p>
                                <p class="font-bold">2 Adultes</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                                <i class="fa-solid fa-seat text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Sièges</p>
                                <p class="font-bold">12A, 12B</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                                <i class="fa-solid fa-clock text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Départ</p>
                                <p class="font-bold">08:30</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                <i class="fa-solid fa-hourglass text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Offre expire</p>
                                <p class="font-bold text-accent-500">15:24</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Payment Sections -->
                <div class="space-y-6">
                    <!-- Contact Section -->
                    <section class="glass-effect rounded-2xl shadow-card overflow-hidden border border-slate-100 animate-fade-in-up"
                        style="animation-delay: 0.1s;">
                        <div class="p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-white flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg md:text-xl font-bold text-slate-800">Coordonnées</h2>
                                        <p class="text-sm text-slate-500">Où envoyer votre billet électronique ?</p>
                                    </div>
                                </div>
                                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                                    <i class="fa-solid fa-shield-check"></i>
                                    <span>Protégé</span>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="group input-group relative">
                                    <label for="contactPhoneNumber" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Numéro de téléphone</label>
                                    <div class="relative">
                                        <input type="tel" id="contactPhoneNumber"
                                            class="clean-input w-full pt-6 pb-3 px-4 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                            placeholder="+33 6 12 34 56 78"
                                            oninput="validatePhone(this)">
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="fa-solid fa-check-circle hidden text-green-500" id="phone-valid"></i>
                                            <i class="fa-solid fa-exclamation-circle hidden text-red-500" id="phone-invalid"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-2 flex items-center gap-2"><i class="fa-solid fa-lock text-slate-300"></i>Requis pour recevoir votre billet électronique et les notifications</p>
                                </div>

                                <div class="group input-group relative">
                                    <label for="contactEmail" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Adresse email (optionnel)</label>
                                    <div class="relative">
                                        <input type="email" id="contactEmail"
                                            class="clean-input w-full pt-6 pb-3 px-4 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                            placeholder="exemple@email.com"
                                            oninput="validateEmail(this)">
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400">
                                            <i class="fa-solid fa-check-circle hidden text-green-500" id="email-valid"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-2">Recevez une copie de votre billet par email</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Section -->
                    <section class="glass-effect rounded-2xl shadow-card overflow-hidden border border-slate-100 animate-fade-in-up"
                        style="animation-delay: 0.2s;">
                        <div class="p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-600 to-purple-600 text-white flex items-center justify-center shadow-lg">
                                        <i class="fa-regular fa-credit-card text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg md:text-xl font-bold text-slate-800">Paiement Sécurisé</h2>
                                        <p class="text-sm text-slate-500">Choisissez votre méthode de paiement</p>
                                    </div>
                                </div>
                                <div class="hidden md:flex items-center gap-2 text-xs">
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    </div>
                                    <span class="font-mono text-slate-600">TLS 1.3</span>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="mb-8">
                                <h3 class="text-sm font-bold text-slate-700 mb-4">Sélectionnez votre mode de paiement :</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                                    <button onclick="togglePayment('card')" id="tab-card"
                                        class="payment-tab py-4 rounded-xl border-2 border-brand-500 bg-brand-50 shadow-sm transition-all hover:shadow-md active:scale-95 flex flex-col items-center gap-2">
                                        <i class="fa-regular fa-credit-card text-2xl text-brand-600"></i>
                                        <span class="font-bold text-slate-800">Carte bancaire</span>
                                        <span class="text-xs text-slate-500">Visa, Mastercard</span>
                                    </button>
                                    <button onclick="togglePayment('om')" id="tab-om"
                                        class="payment-tab py-4 rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md active:scale-95 flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-mobile-alt text-2xl text-orange-500"></i>
                                        <span class="font-bold text-slate-800">Orange Money</span>
                                        <span class="text-xs text-slate-500">Paiement mobile</span>
                                    </button>
                                    <button onclick="togglePayment('mm')" id="tab-mm"
                                        class="payment-tab py-4 rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md active:scale-95 flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-wallet text-2xl text-yellow-500"></i>
                                        <span class="font-bold text-slate-800">Mobile Money</span>
                                        <span class="text-xs text-slate-500">Wave, Free Money</span>
                                    </button>
                                    <button onclick="togglePayment('paypal')" id="tab-paypal"
                                        class="payment-tab py-4 rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md active:scale-95 flex flex-col items-center gap-2">
                                        <i class="fa-brands fa-paypal text-2xl text-blue-500"></i>
                                        <span class="font-bold text-slate-800">PayPal</span>
                                        <span class="text-xs text-slate-500">Compte PayPal</span>
                                    </button>
                                </div>

                                <!-- Saved Cards -->
                                <div id="saved-cards" class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-bold text-slate-700">Cartes enregistrées</h4>
                                        <button onclick="showAddCard()" class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                                            <i class="fa-solid fa-plus mr-1"></i>Ajouter une carte
                                        </button>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200 hover:bg-white cursor-pointer transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-6 bg-gradient-to-r from-blue-900 to-blue-700 rounded flex items-center justify-center">
                                                    <i class="fa-brands fa-cc-visa text-white text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-slate-800">•••• 4321</p>
                                                    <p class="text-xs text-slate-500">Expire 12/25</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Par défaut</span>
                                                <i class="fa-solid fa-chevron-right text-slate-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Forms Area -->
                            <div class="relative min-h-[320px] md:min-h-[280px]">
                                <!-- Card Form -->
                                <div id="card-form" class="payment-form space-y-5 transition-all duration-500">
                                    <div class="payment-card mb-6">
                                        <div class="payment-card-inner">
                                            <div class="payment-card-front">
                                                <div class="flex justify-between items-start mb-8">
                                                    <i class="fa-brands fa-cc-visa text-3xl"></i>
                                                    <span class="text-xs">Carte bancaire</span>
                                                </div>
                                                <div class="mb-6">
                                                    <div class="text-sm text-white/80 mb-1">Numéro de carte</div>
                                                    <div class="font-mono text-xl tracking-widest" id="card-display">•••• •••• •••• ••••</div>
                                                </div>
                                                <div class="flex justify-between">
                                                    <div>
                                                        <div class="text-sm text-white/80 mb-1">Titulaire</div>
                                                        <div class="font-medium uppercase" id="name-display">NOM PRENOM</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm text-white/80 mb-1">Expire</div>
                                                        <div class="font-medium" id="expiry-display">MM/AA</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="payment-card-back">
                                                <div class="h-12 bg-black mt-8 mb-6"></div>
                                                <div class="flex justify-end">
                                                    <div class="bg-white text-black p-2 rounded text-sm font-mono" id="cvc-display">•••</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid md:grid-cols-2 gap-5">
                                        <div class="space-y-1 group input-group relative">
                                            <label for="cardName" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Titulaire de la carte</label>
                                            <input type="text" id="cardName"
                                                class="clean-input w-full pt-6 pb-3 px-4 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                                placeholder="NOM PRENOM"
                                                oninput="updateCardDisplay()">
                                        </div>

                                        <div class="space-y-1 group input-group relative">
                                            <label for="cardNumber" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Numéro de carte</label>
                                            <div class="relative">
                                                <input type="tel" inputmode="numeric" id="cardNumber"
                                                    class="clean-input w-full pt-6 pb-3 px-4 pr-12 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                                    placeholder="0000 0000 0000 0000"
                                                    maxlength="19"
                                                    oninput="formatCard(this); updateCardDisplay()">
                                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 flex gap-1">
                                                    <i class="fa-brands fa-cc-visa text-slate-400"></i>
                                                    <i class="fa-brands fa-cc-mastercard text-slate-400"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-1 group input-group relative">
                                            <label for="expiryDate" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Date d'expiration</label>
                                            <input type="tel" inputmode="numeric" id="expiryDate"
                                                class="clean-input w-full pt-6 pb-3 px-4 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                                placeholder="MM/AA"
                                                maxlength="5"
                                                oninput="formatExpiry(this); updateCardDisplay()">
                                        </div>

                                        <div class="space-y-1 group input-group relative">
                                            <label for="cvc" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Code de sécurité</label>
                                            <div class="relative">
                                                <input type="tel" inputmode="numeric" id="cvc"
                                                    class="clean-input w-full pt-6 pb-3 px-4 pr-12 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                                    placeholder="123"
                                                    maxlength="4"
                                                    oninput="updateCardDisplay()">
                                                <button type="button" onclick="toggleCvcHelp()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-brand-600">
                                                    <i class="fa-solid fa-circle-question"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Save Card Option -->
                                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                                        <label class="custom-checkbox cursor-pointer flex items-center gap-3">
                                            <input type="checkbox" id="saveCard" checked>
                                            <span class="checkmark"></span>
                                            <span class="text-sm text-slate-700">Enregistrer cette carte pour des paiements futurs</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Mobile Forms -->
                                <div id="mobile-form" class="payment-form absolute inset-0 opacity-0 translate-x-10 pointer-events-none transition-all duration-500">
                                    <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200 rounded-2xl p-6 md:p-8 text-center h-full flex flex-col justify-center items-center">
                                        <div class="w-20 h-20 rounded-2xl shadow-lg flex items-center justify-center mb-6 text-4xl transition-all duration-500"
                                            id="mobile-icon">
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-800 mb-3" id="mobile-title">Paiement Mobile</h3>
                                        <p class="text-slate-600 mb-8 max-w-md" id="mobile-text">Entrez votre numéro de téléphone pour recevoir une demande de paiement sécurisée</p>

                                        <div class="w-full max-w-xs space-y-1 group input-group relative text-left">
                                            <label for="mmPhoneNumber" class="text-xs font-bold uppercase text-slate-500 absolute top-3 left-4 transition-all duration-300 pointer-events-none z-10 bg-white px-2">Numéro de téléphone</label>
                                            <input type="tel" id="mmPhoneNumber"
                                                class="w-full pt-6 pb-3 px-4 bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-300 text-base shadow-inner-lg"
                                                placeholder="+225 XX XX XX XX">
                                        </div>
                                        
                                        <button onclick="sendMobilePayment()" class="mt-6 px-8 py-3 bg-gradient-to-r from-brand-600 to-brand-700 text-white font-bold rounded-lg hover:shadow-lg transition-all active:scale-95">
                                            Envoyer la demande de paiement
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Security Badges -->
                    <div class="flex flex-wrap justify-center gap-6 py-6">
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <i class="fa-solid fa-lock text-green-500"></i>
                            <span>Paiement 100% sécurisé</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <i class="fa-solid fa-shield-check text-blue-500"></i>
                            <span>Données cryptées</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <i class="fa-solid fa-user-shield text-purple-500"></i>
                            <span>Confidentialité garantie</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary -->
            <div class="lg:col-span-4">
                <div class="sticky top-28 space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-2xl shadow-slate-900/30 relative overflow-hidden">
                        <!-- Animated Background -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"%23ffffff\" fill-opacity=\"0.1\" fill-rule=\"evenodd\"><circle cx=\"3\" cy=\"3\" r=\"3\"/><circle cx=\"13\" cy=\"13\" r=\"3\"/></g></svg>')"></div>
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>Votre Billet</span>
                                </h3>
                                <span class="text-xs px-3 py-1 bg-white/20 rounded-full">2 passagers</span>
                            </div>

                            <!-- Journey Info -->
                            <div class="bg-white/10 rounded-xl p-4 mb-6 backdrop-blur-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="text-sm text-slate-300">De</div>
                                        <div class="font-bold text-lg">Paris</div>
                                    </div>
                                    <div class="relative px-4">
                                        <div class="w-8 h-0.5 bg-slate-400"></div>
                                        <i class="fa-solid fa-arrow-right absolute -right-1 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-300">Vers</div>
                                        <div class="font-bold text-lg">Lyon</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-slate-400">Date</div>
                                        <div class="font-medium">25 Déc. 2025</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400">Départ</div>
                                        <div class="font-medium">08:30</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-300">Billet Adulte ×2</span>
                                    <span>54.00 €</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-300">Réservation</span>
                                    <span>2.00 €</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-300">Assurance voyage</span>
                                    <span>3.99 €</span>
                                </div>
                                <div class="h-px bg-white/20 my-2"></div>
                                <div class="flex justify-between text-base font-bold">
                                    <span>Total TTC</span>
                                    <span class="text-2xl text-brand-300">59.99 €</span>
                                </div>
                            </div>

                            <!-- Promo Code -->
                            <div class="mb-6">
                                <button onclick="togglePromoCode()" class="w-full flex items-center justify-between p-3 bg-white/10 rounded-lg hover:bg-white/15 transition-colors">
                                    <span class="text-sm">Ajouter un code promo</span>
                                    <i class="fa-solid fa-tag"></i>
                                </button>
                                <div id="promo-code-input" class="hidden mt-3">
                                    <div class="flex gap-2">
                                        <input type="text" placeholder="Entrez votre code" class="flex-1 px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-brand-400">
                                        <button class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Appliquer</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="text-xs text-slate-400 space-y-2">
                                <label class="flex items-start gap-2">
                                    <input type="checkbox" class="mt-1" required>
                                    <span>J'accepte les <a href="#" class="text-brand-300 hover:underline">conditions générales</a> et la <a href="#" class="text-brand-300 hover:underline">politique de confidentialité</a></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button onclick="processPayment()" id="pay-button"
                        class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white font-bold rounded-xl shadow-xl hover:shadow-2xl hover:shadow-brand-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3 group text-base ripple">
                        <span>Payer & Recevoir mon Billet</span>
                        <i class="fa-solid fa-lock group-hover:animate-bounce"></i>
                    </button>

                    <!-- Secure Payment Info -->
                    <div class="text-center text-xs text-slate-500 space-y-2">
                        <div class="flex items-center justify-center gap-4 text-lg">
                            <i class="fa-brands fa-cc-visa text-blue-900"></i>
                            <i class="fa-brands fa-cc-mastercard text-red-600"></i>
                            <i class="fa-brands fa-cc-paypal text-blue-500"></i>
                            <i class="fa-solid fa-lock text-green-500"></i>
                        </div>
                        <p><i class="fa-solid fa-shield-check mr-1"></i>Paiement sécurisé SSL 256-bit</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MOBILE STICKY BAR -->
    <div
        class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-slate-200 p-4 safe-area-bottom shadow-2xl lg:hidden z-50 animate-slide-up-mobile">
        <div class="flex justify-between items-center mb-3">
            <div>
                <p class="text-xs text-slate-500 font-medium">Total à payer</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-900">59.99</span>
                    <span class="text-sm font-bold text-slate-500">€</span>
                </div>
            </div>
            <div>
                <button onclick="scrollToTop()"
                    class="text-xs text-brand-600 font-bold bg-brand-50 px-3 py-1.5 rounded-lg border border-brand-100 flex items-center gap-1 touch-target">
                    <i class="fa-solid fa-angle-up"></i> Haut
                </button>
            </div>
        </div>
        <button onclick="processPayment()" id="mobile-pay-button"
            class="w-full bg-gradient-to-r from-slate-900 to-slate-800 text-white font-bold py-4 rounded-xl shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-3 ripple">
            <span>Payer & Recevoir Billet</span>
            <i class="fa-solid fa-ticket"></i>
        </button>
    </div>

    <!-- Help Modal -->
    <div id="help-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="hideHelp()"></div>
        <div class="bg-white rounded-2xl w-full max-w-md p-6 relative z-10 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Aide & Assistance</h3>
                <button onclick="hideHelp()" class="touch-target w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-phone text-brand-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700">Assistance 24/7</h4>
                        <p class="text-sm text-slate-600">+33 1 23 45 67 89</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-shield-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700">Paiement Sécurisé</h4>
                        <p class="text-sm text-slate-600">Vos données sont cryptées et protégées</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-clock text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700">Réservation Garantie</h4>
                        <p class="text-sm text-slate-600">Vos places sont réservées pendant 15 minutes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processing-modal"
        class="fixed inset-0 z-[60] hidden flex flex-col items-center justify-center bg-white/95 backdrop-blur-sm px-8 text-center">
        <div class="relative mb-8">
            <div class="w-24 h-24 rounded-full border-4 border-slate-100 border-t-brand-600 border-r-brand-600 border-b-brand-600 border-l-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fa-solid fa-lock text-2xl text-brand-600"></i>
            </div>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Validation du paiement...</h3>
        <p class="text-slate-500 mb-6">Veuillez patienter pendant le traitement sécurisé</p>
        <div class="w-full max-w-xs bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-brand-600 to-brand-400 animate-progress"></div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
            onclick="window.location.reload()"></div>
        <div
            class="bg-white rounded-2xl w-full max-w-sm p-8 relative z-10 text-center shadow-2xl animate-bounce-subtle">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-500 rounded-full animate-pulse"></div>
                <div class="absolute inset-2 bg-white rounded-full flex items-center justify-center">
                    <svg class="checkmark" width="48" height="48" viewBox="0 0 48 48">
                        <circle class="checkmark-circle" cx="24" cy="24" r="22" fill="none" stroke="#10b981" stroke-width="4"/>
                        <path class="checkmark-check" fill="none" stroke="#10b981" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="100" stroke-dashoffset="100" d="M14 24 L22 32 L34 18"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Paiement Réussi !</h2>
            <p class="text-slate-500 mb-6 font-medium">Votre billet a été émis avec succès</p>

            <div class="bg-gradient-to-r from-brand-50 to-green-50 border border-brand-100 rounded-xl p-4 mb-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M0,0 L30,30 M30,0 L0,30\" stroke=\"%233b82f6\" stroke-width=\"1\"/></svg>')"></div>
                </div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-3 pb-2 border-b border-brand-100">
                        <div class="text-left">
                            <div class="text-xs text-slate-500">Référence</div>
                            <div class="font-mono font-bold">BRX-9852-2025</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500">Siège</div>
                            <div class="font-bold text-lg">12A</div>
                        </div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <div class="text-left">
                            <div class="text-slate-500">Passager</div>
                            <div class="font-bold">Jean Dupont</div>
                        </div>
                        <div class="text-right">
                            <div class="text-slate-500">Date</div>
                            <div class="font-bold">25/12/2025</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="downloadTicket()"
                    class="w-full bg-gradient-to-r from-slate-900 to-slate-800 text-white font-bold py-3.5 rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    <span>Télécharger le billet</span>
                </button>
                <button onclick="sendTicket()"
                    class="w-full bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold py-3.5 rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Envoyer par SMS</span>
                </button>
                <button onclick="window.location.reload()"
                    class="w-full text-slate-400 font-medium py-3 text-sm hover:text-slate-600">
                    Retour à l'accueil
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen
            setTimeout(() => {
                document.getElementById('loading-screen').style.opacity = '0';
                setTimeout(() => {
                    document.getElementById('loading-screen').style.display = 'none';
                }, 300);
            }, 1000);

            // Initialize payment method
            togglePayment('card');
            
            // Add ripple effect to buttons
            document.querySelectorAll('.ripple').forEach(button => {
                button.addEventListener('click', function(e) {
                    let x = e.clientX - e.target.getBoundingClientRect().left;
                    let y = e.clientY - e.target.getBoundingClientRect().top;
                    let ripple = document.createElement('span');
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 1000);
                });
            });
        });

        // Variables
        let currentMethod = 'card';
        let cardData = {
            name: '',
            number: '',
            expiry: '',
            cvc: ''
        };

        // Toggle payment method
        function togglePayment(method) {
            const tabs = ['card', 'om', 'mm', 'paypal'];
            const cardForm = document.getElementById('card-form');
            const mobileForm = document.getElementById('mobile-form');
            
            // Reset all tabs
            tabs.forEach(tab => {
                const tabEl = document.getElementById(`tab-${tab}`);
                if (tabEl) {
                    tabEl.className = tabEl.className.replace(/border-brand-500 bg-brand-50/, 'border-slate-200 bg-white');
                }
            });
            
            // Activate selected tab
            const activeTab = document.getElementById(`tab-${method}`);
            if (activeTab) {
                activeTab.className = activeTab.className.replace(/border-slate-200 bg-white/, 'border-brand-500 bg-brand-50');
            }
            
            // Update mobile form content
            if (method === 'om') {
                updateMobileForm('Orange Money', 'Paiement via Orange Money sécurisé', 'fa-solid fa-mobile-alt', 'text-orange-500');
            } else if (method === 'mm') {
                updateMobileForm('Mobile Money', 'Paiement via Mobile Money (Wave, Free)', 'fa-solid fa-wallet', 'text-yellow-500');
            } else if (method === 'paypal') {
                updateMobileForm('PayPal', 'Paiement via votre compte PayPal', 'fa-brands fa-paypal', 'text-blue-500');
            }
            
            // Show appropriate form
            if (method === 'card') {
                mobileForm.style.opacity = '0';
                mobileForm.style.transform = 'translateX(10px)';
                mobileForm.style.pointerEvents = 'none';
                
                setTimeout(() => {
                    cardForm.style.opacity = '1';
                    cardForm.style.transform = 'translateX(0)';
                    cardForm.style.pointerEvents = 'auto';
                }, 50);
            } else {
                cardForm.style.opacity = '0';
                cardForm.style.transform = 'translateX(-10px)';
                cardForm.style.pointerEvents = 'none';
                
                setTimeout(() => {
                    mobileForm.style.opacity = '1';
                    mobileForm.style.transform = 'translateX(0)';
                    mobileForm.style.pointerEvents = 'auto';
                }, 50);
            }
            
            currentMethod = method;
        }

        function updateMobileForm(title, text, icon, iconColor) {
            document.getElementById('mobile-title').textContent = title;
            document.getElementById('mobile-text').textContent = text;
            const mobileIcon = document.getElementById('mobile-icon');
            mobileIcon.innerHTML = `<i class="${icon} text-4xl ${iconColor}"></i>`;
        }

        // Format card number
        function formatCard(input) {
            let val = input.value.replace(/\D/g, '');
            val = val.substring(0, 16);
            val = val.replace(/(\d{4})/g, '$1 ').trim();
            input.value = val;
            cardData.number = val;
        }

        // Format expiry date
        function formatExpiry(input) {
            let val = input.value.replace(/\D/g, '');
            if (val.length >= 2) {
                val = val.substring(0, 2) + '/' + val.substring(2, 4);
            }
            input.value = val;
            cardData.expiry = val;
        }

        // Update card display
        function updateCardDisplay() {
            const name = document.getElementById('cardName').value || 'NOM PRENOM';
            const number = document.getElementById('cardNumber').value || '•••• •••• •••• ••••';
            const expiry = document.getElementById('expiryDate').value || 'MM/AA';
            const cvc = document.getElementById('cvc').value || '•••';
            
            document.getElementById('name-display').textContent = name.toUpperCase();
            document.getElementById('card-display').textContent = number;
            document.getElementById('expiry-display').textContent = expiry;
            document.getElementById('cvc-display').textContent = cvc.padEnd(3, '•');
        }

        // Validate phone number
        function validatePhone(input) {
            const phone = input.value.replace(/\D/g, '');
            const isValid = phone.length >= 9;
            
            document.getElementById('phone-valid').style.display = isValid ? 'block' : 'none';
            document.getElementById('phone-invalid').style.display = !isValid && phone.length > 0 ? 'block' : 'none';
            
            return isValid;
        }

        // Validate email
        function validateEmail(input) {
            const email = input.value;
            const isValid = email.includes('@') && email.includes('.');
            document.getElementById('email-valid').style.display = isValid && email.length > 0 ? 'block' : 'none';
            return isValid;
        }

        // Show/hide help modal
        function showHelp() {
            document.getElementById('help-modal').style.display = 'flex';
        }

        function hideHelp() {
            document.getElementById('help-modal').style.display = 'none';
        }

        // Toggle promo code input
        function togglePromoCode() {
            const input = document.getElementById('promo-code-input');
            input.classList.toggle('hidden');
        }

        // Scroll to top
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Toggle CVC help
        function toggleCvcHelp() {
            alert("Le code CVC est le code à 3 chiffres au dos de votre carte, à droite de la bande signature.");
        }

        // Process payment
        function processPayment() {
            // Validate form
            if (!validateForm()) {
                document.getElementById('pay-button').classList.add('animate-shake');
                document.getElementById('mobile-pay-button').classList.add('animate-shake');
                setTimeout(() => {
                    document.getElementById('pay-button').classList.remove('animate-shake');
                    document.getElementById('mobile-pay-button').classList.remove('animate-shake');
                }, 500);
                return;
            }
            
            // Show processing modal
            document.getElementById('processing-modal').style.display = 'flex';
            
            // Simulate payment processing
            setTimeout(() => {
                document.getElementById('processing-modal').style.display = 'none';
                document.getElementById('success-modal').style.display = 'flex';
                
                // Animate checkmark
                setTimeout(() => {
                    const checkmark = document.querySelector('.checkmark-check');
                    if (checkmark) {
                        checkmark.style.strokeDashoffset = '0';
                    }
                }, 300);
            }, 3000);
        }

        // Validate form
        function validateForm() {
            const phone = document.getElementById('contactPhoneNumber').value.replace(/\D/g, '');
            if (phone.length < 9) {
                alert("Veuillez entrer un numéro de téléphone valide");
                return false;
            }
            
            if (currentMethod === 'card') {
                const name = document.getElementById('cardName').value;
                const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
                const expiry = document.getElementById('expiryDate').value;
                const cvc = document.getElementById('cvc').value;
                
                if (!name || name.length < 3) {
                    alert("Veuillez entrer le nom du titulaire de la carte");
                    return false;
                }
                
                if (!number || number.length < 16) {
                    alert("Veuillez entrer un numéro de carte valide");
                    return false;
                }
                
                if (!expiry || expiry.length < 5) {
                    alert("Veuillez entrer une date d'expiration valide");
                    return false;
                }
                
                if (!cvc || cvc.length < 3) {
                    alert("Veuillez entrer le code CVC");
                    return false;
                }
            }
            
            return true;
        }

        // Download ticket
        function downloadTicket() {
            alert("Téléchargement du billet...");
        }

        // Send ticket via SMS
        function sendTicket() {
            alert("Billet envoyé par SMS !");
        }

        // Send mobile payment request
        function sendMobilePayment() {
            const phone = document.getElementById('mmPhoneNumber').value;
            if (!phone) {
                alert("Veuillez entrer votre numéro de téléphone");
                return;
            }
            alert(`Demande de paiement envoyée au ${phone}. Veuillez valider sur votre téléphone.`);
        }

        // Show add card form
        function showAddCard() {
            alert("Fonctionnalité d'ajout de carte en développement");
        }
    </script>
</body>

</html>