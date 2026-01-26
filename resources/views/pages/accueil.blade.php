@extends('layouts.app')

@section('title', 'BusRapide - Accueil')

@section('content')
    <!-- Bus Scroll Animation -->
    <div class="bus-scroll-container hidden md:block overflow-hidden h-10 bg-gradient-to-r from-brand-900 to-brand-600 relative">
        <div class="bus-scroll-track flex absolute whitespace-nowrap animate-[busScroll_60s_linear_infinite]">
            <div class="flex items-center px-4 h-full">
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">🚌 Prochain départ: Douala → Yaoundé à 07:30</span>
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">🎫 Réservez en ligne et économisez 10%</span>
                <i class="fa-solid fa-bus text-white text-lg mx-4"></i>
                <span class="text-white text-sm font-medium mx-4">⭐ Voyagez confortablement avec nos bus climatisés</span>
            </div>
        </div>
    </div>

    <!-- HERO SECTION avec slider d'images -->
    <section class="hero-pattern pt-8 pb-12 md:pt-12 md:pb-24 px-4 relative">
        <!-- Slider d'images -->
        <div class="hero-slider absolute inset-0 overflow-hidden z-0">
            <div class="hero-slide active absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-100" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=1600')"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0" style="background-image: url('https://images.unsplash.com/photo-1598983062497-5d191c7c8b72?auto=format&fit=crop&q=80&w=1600')"></div>
            <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=1600')"></div>
        </div>
        
        <!-- Overlay pour meilleur contraste -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/30 z-0"></div>
        
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="text-white animate-slide-up">
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                        <i class="fa-solid fa-bolt text-yellow-300"></i>
                        <span class="text-sm font-bold">Réservez en ligne, voyagez sereinement</span>
                    </div>
                    <h1 class="hero-title text-3xl md:text-5xl font-extrabold mb-6 leading-tight hero-text drop-shadow-md">
                        Vos trajets en bus,<br>
                        <span class="text-yellow-300">simples et rapides</span>
                    </h1>
                    <p class="hero-subtext text-brand-100 text-base md:text-lg mb-8 drop-shadow-sm">
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
                    <form class="space-y-4" id="voyage-search-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                <label class="block text-xs font-bold text-slate-500 mb-1">Départ</label>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-brand-500"></i>
                                    <select name="ville_depart" class="bg-transparent w-full font-medium text-slate-900 focus:outline-none" required>
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
                                    <select name="ville_arrivee" class="bg-transparent w-full font-medium text-slate-900 focus:outline-none" required>
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
                                    <input type="date" name="date_voyage" class="bg-transparent w-full font-medium text-slate-900 focus:outline-none" required>
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition">
                                <label class="block text-xs font-bold text-slate-500 mb-1">Passagers</label>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-users text-slate-500"></i>
                                    <select name="passagers" class="bg-transparent w-full font-medium text-slate-900 focus:outline-none">
                                        <option value="1">1 passager</option>
                                        <option value="2">2 passagers</option>
                                        <option value="3">3 passagers</option>
                                        <option value="4">4 passagers</option>
                                        <option value="5">5+ passagers</option>
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
                        <a href="/reservation?depart=douala&arrivee=yaounde" class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                            <i class="fa-solid fa-ticket"></i>
                            Réserver maintenant
                        </a>
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
                        <a href="/reservation?depart=bafoussam&arrivee=douala" class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                            <i class="fa-solid fa-ticket"></i>
                            Réserver maintenant
                        </a>
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
                        <a href="/reservation?depart=yaounde&arrivee=kribi" class="w-full bg-slate-100 hover:bg-brand-600 hover:text-white text-slate-700 font-bold py-3 rounded-lg transition-all flex items-center justify-center gap-2 group-hover:bg-brand-600 group-hover:text-white">
                            <i class="fa-solid fa-ticket"></i>
                            Réserver maintenant
                        </a>
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
@endsection

@push('styles')
<style>
/* Hero text contrast */
.hero-text {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtext {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
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

@media (max-width: 768px) {
    .how-to-step::before {
        left: 20px;
    }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero image slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const totalSlides = slides.length;
        
        function startHeroSlider() {
            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                slides[currentSlide].classList.remove('opacity-100');
                slides[currentSlide].classList.add('opacity-0');
                
                currentSlide = (currentSlide + 1) % totalSlides;
                
                slides[currentSlide].classList.add('active');
                slides[currentSlide].classList.remove('opacity-0');
                slides[currentSlide].classList.add('opacity-100');
            }, 5000);
        }
        
        if (slides.length > 0) {
            startHeroSlider();
        }
        
        // Remplir la date d'aujourd'hui dans le formulaire de recherche
        const dateInput = document.querySelector('input[name="date_voyage"]');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
            dateInput.min = today;
        }

        // Gérer la soumission du formulaire de recherche
        const searchForm = document.getElementById('voyage-search-form');
        if(searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                
                const depart = formData.get('ville_depart');
                const arrivee = formData.get('ville_arrivee');
                
                if (depart === arrivee) {
                    showToast('La ville de départ et d\'arrivée doivent être différentes', 'error');
                    return;
                }
                
                // Redirection vers la page de réservation
                window.location.href = '/reservation?' + params.toString();
            });
        }
    });
</script>
@endpush
