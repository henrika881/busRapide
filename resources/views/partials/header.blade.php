<header class="bg-white border-b border-slate-100 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 floating">
                <i class="fa-solid fa-bus text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-xl text-slate-900 tracking-tight block">BusRapide</span>
                <span class="text-xs text-slate-500 hidden md:block">Voyagez en toute confiance</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6">
            <a href="/" class="text-sm font-bold {{ request()->is('/') ? 'text-brand-600 border-b-2 border-brand-600 pb-1' : 'text-slate-500 hover:text-brand-600' }} transition-colors">Accueil</a>
            <a href="/#search" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Rechercher</a>
            <a href="/#destinations" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Destinations</a>
            <a href="/#howto" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Comment ça marche</a>
            <a href="/#assistance" class="text-sm font-bold text-slate-500 hover:text-brand-600 transition-colors">Assistance</a>
        </nav>

        <!-- User Actions -->
        <div class="flex items-center gap-3">
            <!-- User Account - Desktop (Hidden by default, shown via JS if auth) -->
            <div id="auth-user-display" class="hidden md:flex items-center gap-3">
                
                <a href="/profil" id="user-initial-display" class="w-8 h-8 bg-brand-100 rounded-full flex items-center justify-center text-brand-600 font-bold shadow-sm hover:ring-2 hover:ring-brand-200 transition text-sm">
                    <span id="user-initial">U</span>
                </a>
                <button onclick="logoutUser()" class="ml-2 w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-100 transition-colors" title="Déconnexion">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </div>
            
            <!-- Auth Buttons (Shown by default, hidden via JS if auth) -->
            <div id="guest-buttons" class="flex items-center gap-2">
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
<div id="mobile-menu" class="mobile-menu fixed inset-0 z-50 bg-black/50 md:hidden invisible">
    <div class="absolute right-0 top-0 h-full w-64 bg-white shadow-xl transform transition-transform duration-300 translate-x-full" id="mobile-menu-content">
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
                <a href="/" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                    <i class="fa-solid fa-house w-6"></i> Accueil
                </a>
                <a href="/#search" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                    <i class="fa-solid fa-magnifying-glass w-6"></i> Rechercher
                </a>
                <a href="/#destinations" class="flex items-center gap-3 text-slate-700 font-bold text-lg">
                    <i class="fa-solid fa-map-location-dot w-6"></i> Destinations
                </a>
                
                <a href="/profil" class="flex items-center gap-3 text-slate-700 font-bold text-lg hidden" id="mobile-dashboard-link">
                    <i class="fa-solid fa-gauge w-6"></i> Mon Profil
                </a>
            </div>
            
            <div class="absolute bottom-6 left-6 right-6 space-y-3" id="mobile-guest-buttons">
                <button onclick="openRegisterModal()" class="w-full bg-white border border-brand-600 text-brand-600 py-3 rounded-xl font-bold">
                    S'inscrire
                </button>
                <button onclick="openLoginModal()" class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold">
                    Se connecter
                </button>
            </div>

            <div class="absolute bottom-6 left-6 right-6 space-y-3 hidden" id="mobile-auth-buttons">
                 <button onclick="logoutUser()" class="w-full bg-red-50 text-red-600 py-3 rounded-xl font-bold border border-red-100">
                    Déconnexion
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple logic to toggle visibility class for mobile menu
    document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const content = document.getElementById('mobile-menu-content');
        menu.classList.remove('invisible');
        setTimeout(() => content.classList.remove('translate-x-full'), 10);
    });

    document.getElementById('close-mobile-menu')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const content = document.getElementById('mobile-menu-content');
        content.classList.add('translate-x-full');
        setTimeout(() => menu.classList.add('invisible'), 300);
    });
</script>
