@extends('layouts.app')

@section('title', 'Réservation - BusRapide')

@section('content')
<style>
    .bus-container {
        perspective: 1000px;
    }
    .bus-frame {
        background: #f1f5f9;
        border: 4px solid #1e293b;
        border-radius: 50px 50px 15px 15px;
        padding: 40px 15px 25px 15px;
        position: relative;
        min-width: 280px;
        box-shadow: 
            0 10px 0 #0f172a,
            0 25px 50px -12px rgba(0,0,0,0.5);
    }
    .bus-nose {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 20px;
        background: #1e293b;
        border-radius: 30px 30px 0 0;
    }
    .bus-windshield {
        position: absolute;
        top: 15px;
        left: 20px;
        right: 20px;
        height: 12px;
        background: #64748b;
        border-radius: 10px 10px 2px 2px;
    }
    .driver-section {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 25px;
        padding-bottom: 10px;
        display: flex;
        justify-content: flex-end;
        padding-right: 15px;
    }
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(2, 40px) 30px repeat(2, 40px);
        gap: 10px 8px;
        justify-content: center;
    }
    .seat-aisle {
        grid-column: 3;
    }
    .seat-btn {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 11px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 0 rgba(0,0,0,0.1);
    }
    .seat-btn:active:not(:disabled) {
        transform: translateY(2px);
        box-shadow: 0 2px 0 rgba(0,0,0,0.1);
    }
    .seat-vip {
        border: 2px solid #a855f7;
        color: #7e22ce;
        background: white;
    }
    .seat-vip:hover:not(:disabled) {
        background: #faf5ff;
    }
    .seat-standard {
        border: 2px solid #22c55e;
        color: #15803d;
        background: white;
    }
    .seat-standard:hover:not(:disabled) {
        background: #f0fdf4;
    }
    .seat-occupied {
        background: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
        cursor: not-allowed;
        box-shadow: none !important;
    }
    .seat-selected {
        background: #2563eb !important;
        border-color: #1d4ed8 !important;
        color: white !important;
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(37, 99, 235, 0.4) !important;
    }
    .writing-vertical {
        writing-mode: vertical-lr;
        text-orientation: mixed;
    }
</style>
    <!-- Header Résumé Recherche -->
    <div class="bg-brand-700 text-white shadow-lg relative z-30">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <div class="flex items-center text-sm md:text-base opacity-90 mb-1">
                        <i class="fas fa-map-marker-alt text-yellow-400 mr-2"></i>
                        <span id="header-depart" class="font-bold">--</span>
                        <i class="fas fa-arrow-right mx-3 text-white/60"></i>
                        <span id="header-arrivee" class="font-bold">--</span>
                    </div>
                    <div class="flex items-center text-xs md:text-sm text-white/80">
                        <i class="far fa-calendar text-yellow-400 mr-2"></i>
                        <span id="header-date">--</span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-user-friends text-yellow-400 mr-2"></i>
                        <span id="header-passagers">1 passager</span>
                    </div>
                </div>
                <button onclick="openSearchModal()"
                    class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2 backdrop-blur-sm border border-white/20">
                    <i class="fas fa-search"></i>
                    Modifier
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 grid lg:grid-cols-4 gap-8">

        <!-- Sidebar Filtres -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-card p-6 lg:sticky lg:top-24">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-slate-900">Filtres</h2>
                    <button onclick="resetFilters()"
                        class="text-brand-600 text-sm font-bold hover:text-brand-700 flex items-center gap-1">
                        <i class="fas fa-redo-alt text-xs"></i> Réinitialiser
                    </button>
                </div>

                <!-- Filtre : Heure de départ -->
                <div class="filter-section border-b border-slate-100 pb-6 mb-6">
                    <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-clock text-brand-500"></i>
                        Heure de départ
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                            <input type="checkbox" name="heure" value="matin"
                                class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-3 text-slate-700 text-sm">Matin (05:00 - 11:59)</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                            <input type="checkbox" name="heure" value="apres-midi"
                                class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-3 text-slate-700 text-sm">Après-midi (12:00 - 17:59)</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                            <input type="checkbox" name="heure" value="soir"
                                class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-3 text-slate-700 text-sm">Soir (18:00 - 23:59)</span>
                        </label>
                    </div>
                </div>

                <!-- Filtre : Type de service -->
                <div class="filter-section border-b border-slate-100 pb-6 mb-6">
                    <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-star text-brand-500"></i>
                        Type de service
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                            <input type="checkbox" name="type" value="standard"
                                class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-3 text-slate-700 text-sm">Standard</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                            <input type="checkbox" name="type" value="vip"
                                class="h-4 w-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="ml-3 text-slate-700 text-sm">VIP / Premium</span>
                        </label>
                    </div>
                </div>

                <button onclick="applyFilters()"
                    class="w-full py-3 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/20">
                    Appliquer les filtres
                </button>
            </div>

            <!-- Assistance Pub -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl shadow-card p-6 mt-6">
                <h3 class="font-bold text-lg mb-2 flex items-center gap-2">
                    <i class="fas fa-headset text-brand-400"></i>
                    Besoin d'aide ?
                </h3>
                <p class="text-slate-400 text-sm mb-4">Notre équipe est disponible 24h/24 pour vous accompagner.</p>
                <a href="tel:+237600000000"
                    class="flex items-center gap-2 text-white font-bold hover:text-brand-400 transition">
                    <i class="fas fa-phone"></i>
                    +237 6 00 00 00 00
                </a>
            </div>
        </aside>

        <!-- Liste des Résultats -->
        <section class="lg:col-span-3">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Trajets disponibles</h2>
                    <p id="results-count" class="text-slate-500 text-sm mt-1">Recherche en cours...</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-slate-600">Trier par :</span>
                    <select id="sort-select" onchange="applyFilters()"
                        class="bg-white border-2 border-slate-200 rounded-lg px-4 py-2 text-sm font-bold text-slate-700 focus:outline-none focus:border-brand-500">
                        <option value="prix_asc">Prix (croissant)</option>
                        <option value="prix_desc">Prix (décroissant)</option>
                        <option value="depart_asc">Heure de départ</option>
                    </select>
                </div>
            </div>

            <!-- Container Résultats -->
            <div id="bus-results-list" class="space-y-4">
                <!-- Loading State -->
                <div class="space-y-4 animate-pulse">
                    <div class="h-48 bg-slate-200 rounded-xl"></div>
                    <div class="h-48 bg-slate-200 rounded-xl"></div>
                    <div class="h-48 bg-slate-200 rounded-xl"></div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Modification Recherche -->
    <div id="search-modal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg p-6 relative shadow-modal">
            <button onclick="closeSearchModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <h3 class="text-xl font-bold text-slate-900 mb-6">Modifier la recherche</h3>
            <form onsubmit="handleSearchUpdate(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Départ</label>
                        <select name="modal_depart"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 font-medium focus:outline-none focus:border-brand-500">
                            <option value="douala">Douala</option>
                            <option value="yaounde">Yaoundé</option>
                            <option value="bafoussam">Bafoussam</option>
                            <option value="kribi">Kribi</option>
                            <option value="buea">Buéa</option>
                            <option value="garoua">Garoua</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Arrivée</label>
                        <select name="modal_arrivee"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 font-medium focus:outline-none focus:border-brand-500">
                            <option value="douala">Douala</option>
                            <option value="yaounde">Yaoundé</option>
                            <option value="bafoussam">Bafoussam</option>
                            <option value="kribi">Kribi</option>
                            <option value="buea">Buéa</option>
                            <option value="garoua">Garoua</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Date</label>
                    <input type="date" name="modal_date"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 font-medium focus:outline-none focus:border-brand-500">
                </div>
                <button type="submit"
                    class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold hover:bg-brand-700 transition">
                    Lancer la recherche
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // État global de la page
        let currentSearchResults = [];
        let searchParams = {
            depart: '',
            arrivee: '',
            date: ''
        };

        // userToken et API_BASE_URL sont déjà définis dans app.blade.php

        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            searchParams.depart = params.get('ville_depart') || params.get('depart') || 'douala';
            searchParams.arrivee = params.get('ville_arrivee') || params.get('arrivee') || 'yaounde';
            searchParams.date = params.get('date_voyage') || params.get('date') || new Date().toISOString().split('T')[0];
            searchParams.passagers = parseInt(params.get('passagers')) || 1;

            // 2. Mettre à jour le header
            document.getElementById('header-depart').textContent = capitalize(searchParams.depart);
            document.getElementById('header-arrivee').textContent = capitalize(searchParams.arrivee);
            document.getElementById('header-passagers').textContent = `${searchParams.passagers} Passager${searchParams.passagers > 1 ? 's' : ''}`;

            // Formatage date
            const dateObj = new Date(searchParams.date);
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            document.getElementById('header-date').textContent = dateObj.toLocaleDateString('fr-FR', options);

            // 3. Pré-remplir la modal de modif
            const modalDepart = document.querySelector('select[name="modal_depart"]');
            const modalArrivee = document.querySelector('select[name="modal_arrivee"]');
            const modalDate = document.querySelector('input[name="modal_date"]');

            if (modalDepart) modalDepart.value = searchParams.depart;
            if (modalArrivee) modalArrivee.value = searchParams.arrivee;
            if (modalDate) modalDate.value = searchParams.date;

            // 4. Charger les voyages
            loadVoyages();

            // 5. Check Pending Reservation
            checkPendingReservation();
        });

        function checkPendingReservation() {
            const pending = sessionStorage.getItem('pendingReservation');
            if (pending && userToken) {
                const data = JSON.parse(pending);
                sessionStorage.removeItem('pendingReservation');
                
                showToast('Reprise de votre réservation...', 'info');
                setTimeout(() => {
                    selectVoyageForReservation(data.voyageId, data.classe, true);
                }, 1500); 
            }
        }

        function capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Fonction pour ouvrir/fermer modal recherche
        function openSearchModal() {
            const modal = document.getElementById('search-modal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
        function closeSearchModal() {
            const modal = document.getElementById('search-modal');
            modal.classList.add('hidden');
        }
        function handleSearchUpdate(e) {
            e.preventDefault();
            const depart = e.target.modal_depart.value;
            const arrivee = e.target.modal_arrivee.value;
            const date = e.target.modal_date.value;

            window.location.href = `/reservation?depart=${depart}&arrivee=${arrivee}&date=${date}`;
        }

        // Charger les voyages depuis l'API
        async function loadVoyages() {
            const list = document.getElementById('bus-results-list');

            try {
                const query = new URLSearchParams({
                    ville_depart: searchParams.depart,
                    ville_arrivee: searchParams.arrivee,
                    date_voyage: searchParams.date
                }).toString();

                const response = await fetch(`${API_BASE_URL}/voyages/search?${query}`);
                const data = await response.json();

                if (response.ok && data.success) {
                    currentSearchResults = data.data || [];

                    if (currentSearchResults.length === 0) {
                        list.innerHTML = `
                                <div class="text-center py-12 bg-white rounded-xl shadow-sm">
                                    <i class="fa-solid fa-bus-slash text-4xl text-slate-300 mb-4"></i>
                                    <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun voyage trouvé</h3>
                                    <p class="text-slate-500">Essayez de modifier vos critères de recherche pour les 14 jours avant/après.</p>
                                </div>
                            `;
                        document.getElementById('results-count').textContent = '0 trajet trouvé';
                    } else {
                        applyFilters();
                    }
                } else {
                    list.innerHTML = `<div class="p-4 bg-red-50 text-red-600 rounded-lg">Erreur: ${data.message || 'Impossible de chercher les voyages'}</div>`;
                }
            } catch (error) {
                console.error('Erreur chargement:', error);
                list.innerHTML = `<div class="p-4 bg-red-50 text-red-600 rounded-lg">Erreur de chargement des voyages.</div>`;
            }
        }

        // Appliquer filtres et tri
        function applyFilters() {
            let filtered = [...currentSearchResults];

            // 1. Filtre Heure
            const checkedHeures = Array.from(document.querySelectorAll('input[name="heure"]:checked')).map(cb => cb.value);
            if (checkedHeures.length > 0) {
                filtered = filtered.filter(v => {
                    const hour = parseInt(v.heure_depart.split(':')[0]); // Supposons format "HH:MM"
                    if (checkedHeures.includes('matin') && hour >= 5 && hour < 12) return true;
                    if (checkedHeures.includes('apres-midi') && hour >= 12 && hour < 18) return true;
                    if (checkedHeures.includes('soir') && hour >= 18) return true;
                    return false;
                });
            }

            // 2. Filtre Type (redondant si on sépare l'affichage, mais utile si on veut filtrer une vue globale)
            // On peut le garder pour filtrer à l'intérieur des catégories
            const checkedTypes = Array.from(document.querySelectorAll('input[name="type"]:checked')).map(cb => cb.value);
            if (checkedTypes.length > 0) {
                filtered = filtered.filter(v => {
                    const type = v.categorie || v.type_bus?.toLowerCase(); // 'vip' or 'standard'
                    return checkedTypes.includes(type);
                });
            }

            // 3. Tri
            filtered.sort((a, b) => {
                // Priorité 1 : Résultats exacts d'abord
                if (a.type_result === 'exact' && b.type_result === 'similar') return -1;
                if (a.type_result === 'similar' && b.type_result === 'exact') return 1;

                // Priorité 2 : Tri utilisateur
                if (sortValue === 'prix_asc') return a.prix - b.prix;
                if (sortValue === 'prix_desc') return b.prix - a.prix;
                if (sortValue === 'depart_asc') return a.heure_depart.localeCompare(b.heure_depart);
                return 0;
            });

            renderVoyages(filtered);
        }

        function resetFilters() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.getElementById('sort-select').value = 'prix_asc';
            applyFilters();
        }

        function renderVoyages(voyages) {
            const list = document.getElementById('bus-results-list');
            document.getElementById('results-count').textContent = `${voyages.length} trajet${voyages.length > 1 ? 's' : ''} trouvé${voyages.length > 1 ? 's' : ''}`;

            if (voyages.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm col-span-full">
                        <i class="fa-solid fa-filter-circle-xmark text-4xl text-slate-300 mb-4"></i>
                        <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun résultat ne correspond à vos filtres</h3>
                        <button onclick="resetFilters()" class="text-brand-600 font-bold hover:underline">Réinitialiser les filtres</button>
                    </div>
                `;
                return;
            }

            // Séparer en deux groupes : VIP et Standard
            const vipVoyages = voyages.filter(v => (v.categorie === 'vip' || v.type_bus === 'VIP'));
            const standardVoyages = voyages.filter(v => (v.categorie !== 'vip' && v.type_bus !== 'VIP'));

            let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">';

            // Colonne VIP
            if (vipVoyages.length > 0) {
                html += `
                    <div>
                        <div class="flex items-center gap-3 mb-4 sticky top-0 bg-slate-50 z-10 py-2">
                            <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                                <i class="fa-solid fa-crown text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Gamme VIP</h3>
                                <p class="text-sm text-slate-500">Confort Supérieur</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            ${renderVoyageList(vipVoyages)}
                        </div>
                    </div>
                `;
            } else {
                 html += `
                    <div>
                         <div class="text-center p-8 bg-slate-100 rounded-xl border-2 border-dashed border-slate-200">
                            <p class="text-slate-400 font-bold">Aucun trajet VIP disponible</p>
                         </div>
                    </div>
                 `;
            }

            // Colonne Standard
            if (standardVoyages.length > 0) {
                html += `
                    <div>
                        <div class="flex items-center gap-3 mb-4 sticky top-0 bg-slate-50 z-10 py-2">
                            <div class="p-2 bg-brand-100 rounded-lg text-brand-600">
                                <i class="fa-solid fa-bus text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Gamme Standard</h3>
                                <p class="text-sm text-slate-500">Économique & Rapide</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            ${renderVoyageList(standardVoyages)}
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div>
                         <div class="text-center p-8 bg-slate-100 rounded-xl border-2 border-dashed border-slate-200">
                            <p class="text-slate-400 font-bold">Aucun trajet Standard disponible</p>
                         </div>
                    </div>
                 `;
            }
            
            html += '</div>';
            list.innerHTML = html;
        }

        // Helper pour générer le HTML d'une liste de voyages
        function renderVoyageList(voyages) {
            return voyages.map(v => {
                const isVip = v.categorie === 'vip' || v.type_bus === 'VIP';
                const isSimilar = !v.is_exact;
                const dateDisplay = v.date_depart_fr || v.date_depart;

                return `
                    <div class="bus-card bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-0 border-2 ${isSimilar ? 'border-orange-200 bg-orange-50/10' : (isVip ? 'border-purple-200' : 'border-slate-100')} overflow-hidden group mb-4">
                        ${isSimilar ? `<div class="bg-orange-100 text-orange-800 text-[10px] uppercase tracking-widest font-black px-4 py-1.5 flex items-center gap-2"><i class="fa-solid fa-calendar-days"></i> Suggestion : ${dateDisplay}</div>` : ''}
                        
                        <div class="grid grid-cols-1 md:grid-cols-12">
                            <!-- Left Accent -->
                            <div class="md:col-span-1 ${isVip ? 'bg-gradient-to-b from-purple-600 to-purple-800' : 'bg-gradient-to-b from-brand-600 to-brand-800'} text-white flex flex-row md:flex-col items-center justify-center p-3 gap-3">
                                <i class="${isVip ? 'fa-solid fa-crown text-xl' : 'fa-solid fa-bus text-xl'}"></i>
                                <span class="text-[10px] font-black uppercase tracking-tighter sm:tracking-normal md:writing-vertical">${isVip ? 'VIP' : 'STD'}</span>
                            </div>

                            <!-- Main Content -->
                            <div class="md:col-span-8 p-6">
                                <div class="flex flex-wrap items-center gap-3 mb-5">
                                    <span class="px-3 py-1 bg-slate-900 text-white text-[10px] rounded-full font-black uppercase tracking-widest">
                                        ${v.bus?.marque || 'Bus'} ${v.bus?.modele || ''}
                                    </span>
                                    ${v.climatisation ? '<span class="flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100"><i class="fa-solid fa-snowflake"></i> CLIM</span>' : ''}
                                    ${v.wifi ? '<span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100"><i class="fa-solid fa-wifi"></i> WIFI</span>' : ''}
                                    ${!isSimilar ? `<span class="text-[10px] text-slate-400 font-bold ml-auto"><i class="fa-regular fa-calendar mr-1"></i>${dateDisplay}</span>` : ''}
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 flex items-center justify-between">
                                        <div class="text-center">
                                            <p class="text-2xl font-black text-slate-900 font-outfit uppercase">${v.heure_depart || '00:00'}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Départ</p>
                                        </div>

                                        <div class="flex-1 px-8 relative">
                                            <div class="h-[2px] w-full bg-slate-200 relative">
                                                <div class="absolute -top-1.5 left-0 w-3 h-3 rounded-full bg-slate-200"></div>
                                                <div class="absolute -top-1.5 right-0 w-3 h-3 rounded-full bg-slate-900"></div>
                                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-2">
                                                    <i class="fa-solid fa-bus-simple text-slate-300 text-sm"></i>
                                                </div>
                                            </div>
                                            <p class="text-center text-[10px] font-black text-slate-400 mt-3 uppercase tracking-tighter">${v.duree_estimee || 'Direct'}</p>
                                        </div>

                                        <div class="text-center">
                                            <p class="text-2xl font-black text-slate-900 font-outfit uppercase">${v.heure_arrivee || '00:00'}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Arrivée</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Action -->
                            <div class="md:col-span-3 bg-slate-50 p-6 flex flex-col justify-center items-center border-l border-slate-100">
                                <div class="mb-5 text-center">
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">À partir de</p>
                                    <p class="text-3xl font-black ${isVip ? 'text-purple-600' : 'text-brand-600'} font-outfit">${v.prix} <span class="text-sm">FCFA</span></p>
                                </div>

                                <div class="w-full space-y-3">
                                    <div class="flex flex-col gap-1 mb-2">
                                        <div class="flex justify-between items-end">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase">Disponibilité</span>
                                            <span class="text-xs font-black ${v.places_disponibles > 5 ? 'text-green-600' : 'text-red-600'}">${v.places_disponibles} places</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full ${v.places_disponibles > 5 ? 'bg-green-500' : 'bg-red-500'}" style="width: ${Math.min(100, (v.places_disponibles/40)*100)}%"></div>
                                        </div>
                                    </div>

                                    <button onclick="selectVoyageForReservation(${v.idVoyage}, '${v.categorie || (isVip ? 'vip' : 'standard')}')" 
                                        class="w-full py-3.5 ${isVip ? 'bg-purple-600 hover:bg-purple-700 shadow-purple-200' : 'bg-slate-900 hover:bg-brand-600 shadow-slate-200'} text-white font-black rounded-xl shadow-lg transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed text-xs uppercase tracking-widest"
                                        ${v.places_disponibles === 0 ? 'disabled' : ''}>
                                        ${v.places_disponibles === 0 ? 'Complet' : 'Réserver'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Fonction globale pour sélectionner (utilise la fonction de modals/scripts)
        // Mais ici on doit ouvrir la sélection de siège spécifique
        let currentVoyageClass = 'standard';
        let currentSeatPrice = 0;

        function selectVoyageForReservation(voyageId, classe, autoSkipAuth = false) {
            currentVoyageClass = classe || 'standard';
            const token = localStorage.getItem('auth_token'); 

            // LOGIQUE DE VERROUILLAGE & AUTH
            if (!token && !autoSkipAuth) {
                sessionStorage.setItem('pendingReservation', JSON.stringify({
                    voyageId: voyageId,
                    classe: classe,
                    timestamp: new Date().getTime()
                }));
                
                showToast('Connexion requise pour réserver', 'warning');
                if (typeof openLoginModal === 'function') {
                    openLoginModal();
                } else {
                    window.location.href = '/admin/login'; // Fallback
                }
                return;
            }

            // Charger sièges
            const btn = document.activeElement;
            if(btn && btn.tagName === 'BUTTON') { 
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                btn.disabled = true;
            }

            showToast('Chargement des places...');
            fetch(`${API_BASE_URL}/voyages/${voyageId}/sieges-disponibles`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentSeatPrice = currentVoyageClass === 'vip' ? data.voyage.prixVIP : data.voyage.prixStandard;
                        showSeatModal(voyageId, data.data, currentVoyageClass); 
                    } else {
                        showToast(data.message || 'Erreur chargement places', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erreur chargement places', 'error');
                })
                .finally(() => {
                    if(btn && btn.tagName === 'BUTTON') {
                        btn.innerHTML = btn.dataset.originalText || 'Réserver'; 
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        btn.disabled = false;
                    }
                });
        }

        function showSeatModal(voyageId, allSeats, voyageClasse) {
            // Remove existing modal if any
            const existing = document.getElementById('seat-modal');
            if (existing) existing.remove();

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fadeIn';
            modal.id = 'seat-modal';

            // Reset selection state
            selectedSeats = [];
            selectedSeatNums = [];

            // Construction Grille Sièges
            let seatsHtml = '';
            
            for (let i = 0; i < allSeats.length; i++) {
                const siege = allSeats[i];
                const isOccupied = siege.statut === 'occupe';
                const isVip = siege.classe === 'vip';
                // Only allow selecting seats that match the voyage class
                // Or better: allow upgrading? For now let's stick to simple logic: 
                // If user selected VIP voyage, show all but prioritize VIP? 
                // The prompt said: "make the bus seat plan interactive... click on a seat to change its color... dynamic price calculation"
                // Assuming stricter matching for now as per previous logic
                const isMatchingClass = isVip === (voyageClasse === 'vip');
                
                seatsHtml += `
                    <button onclick="toggleSeat(this, ${siege.numero}, ${siege.idSiege})" 
                        class="seat-btn ${isOccupied || !isMatchingClass ? 'seat-occupied opacity-40' : (isVip ? 'seat-vip' : 'seat-standard')} seat-available" 
                        ${isOccupied || !isMatchingClass ? 'disabled' : ''}
                        data-price="${siege.prix}"
                        title="Siège ${siege.numero} - ${siege.prix} FCFA">
                        ${siege.numero}
                        ${isVip ? '<i class="fa-solid fa-crown absolute top-0.5 right-0.5 text-[7px]"></i>' : ''}
                    </button>
                `;
                
                 if ((i + 1) % 2 === 0 && (i + 1) % 4 !== 0) {
                    seatsHtml += '<div class="seat-aisle"></div>';
                }
            }

            modal.innerHTML = `
                <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-scaleIn flex flex-col max-h-[90vh]">
                    <div class="bg-slate-900 px-6 py-5 flex justify-between items-center text-white shrink-0">
                        <div>
                            <h3 class="font-bold text-xl font-outfit">Configuration du Bus</h3>
                            <p class="text-xs text-slate-400">Voyage ${voyageClasse.toUpperCase()}</p>
                        </div>
                        <button onclick="document.getElementById('seat-modal').remove()" class="w-10 h-10 flex items-center justify-center hover:bg-white/10 rounded-full transition">
                            <i class="fa-solid fa-xmark fa-lg"></i>
                        </button>
                    </div>
                    
                    <div class="p-6 overflow-y-auto">
                        <div class="flex justify-center gap-4 mb-6 bg-slate-50 p-3 rounded-xl text-[10px] font-bold uppercase tracking-wider text-slate-500">
                             <div class="flex items-center gap-1.5"><div class="w-3 h-3 border-2 ${voyageClasse === 'vip' ? 'border-purple-500' : 'border-green-500'} rounded-sm"></div> Libre</div>
                             <div class="flex items-center gap-1.5"><div class="w-3 h-3 bg-brand-600 rounded-sm"></div> Choisi</div>
                             <div class="flex items-center gap-1.5"><div class="w-3 h-3 bg-slate-300 rounded-sm"></div> Pris</div>
                        </div>

                        <div class="bus-container flex justify-center mb-6">
                             <div class="bus-frame">
                                <div class="bus-nose"></div>
                                <div class="driver-section">
                                    <div class="w-8 h-8 rounded-full border-4 border-slate-300 flex items-center justify-center">
                                        <i class="fa-solid fa-dharmachakra text-slate-300 text-sm"></i>
                                    </div>
                                </div>
                                <div class="seat-grid">
                                    ${seatsHtml}
                                </div>
                             </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-6 border-t border-slate-100 shrink-0">
                         <div class="flex justify-between items-end mb-4">
                             <div>
                                 <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total à payer</p>
                                 <p class="text-3xl font-black text-brand-600 font-outfit" id="total-price-display">0 <span class="text-sm text-slate-500">FCFA</span></p>
                             </div>
                             <div class="text-right">
                                  <p class="text-xs font-bold text-slate-500 mb-1" id="passenger-count-display">0/${searchParams.passagers} passager(s)</p>
                             </div>
                         </div>

                        <button id="confirm-seat-btn" disabled onclick="confirmBooking(${voyageId})" 
                            class="w-full bg-slate-300 text-white px-6 py-4 rounded-xl font-bold transition shadow-lg flex justify-center items-center gap-2">
                            <span>Sélectionnez ${searchParams.passagers} place(s)</span>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        }

        let selectedSeats = []; 
        let selectedSeatNums = []; 

        window.toggleSeat = function (btn, num, idSiege) {
            const index = selectedSeats.indexOf(idSiege);
            
            if (index > -1) {
                // Desélectionner
                selectedSeats.splice(index, 1);
                selectedSeatNums.splice(index, 1);
                
                const isVip = btn.classList.contains('seat-vip');
                btn.classList.remove('seat-selected');
            } else {
                // Sélectionner
                if (selectedSeats.length >= searchParams.passagers) {
                    showToast(`Vous avez déjà sélectionné ${searchParams.passagers} place(s)`, 'info');
                    return;
                }
                
                selectedSeats.push(idSiege);
                selectedSeatNums.push(num);
                btn.classList.add('seat-selected');
            }

            // Update UI & Price
            const currentTotal = selectedSeats.length * currentSeatPrice;
            document.getElementById('total-price-display').innerHTML = `${currentTotal.toLocaleString()} <span class="text-sm text-slate-500">FCFA</span>`;
            document.getElementById('passenger-count-display').textContent = `${selectedSeats.length}/${searchParams.passagers} passager(s)`;
            
            const confirmBtn = document.getElementById('confirm-seat-btn');
            if (selectedSeats.length === searchParams.passagers) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `<span>Confirmer la réservation</span> <i class="fa-solid fa-arrow-right"></i>`;
                confirmBtn.classList.remove('bg-slate-300', 'text-slate-500');
                confirmBtn.classList.add('bg-brand-600', 'text-white', 'hover:bg-brand-700', 'shadow-brand-500/30');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `<span>Sélectionnez encore ${searchParams.passagers - selectedSeats.length} place(s)</span>`;
                confirmBtn.classList.add('bg-slate-300');
                confirmBtn.classList.remove('bg-brand-600', 'hover:bg-brand-700', 'shadow-brand-500/30');
            }
        }

        window.confirmBooking = function (voyageId) {
            if (selectedSeats.length === 0) return;
            const siegesParam = selectedSeats.join(',');
            window.location.href = `/paiement?voyage=${voyageId}&sieges=${siegesParam}&classe=${currentVoyageClass}`;
        }

    </script>
    <style>
        /* Custom Scrollbar for results if needed */
        #bus-results-list::-webkit-scrollbar {
            width: 6px;
        }

        #bus-results-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        
        .writing-vertical-lr {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
        }
    </style>
@endpush