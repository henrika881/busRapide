
<!DOCTYPE html>
<html lang="fr">
<head>
   @php
    // Récupérer les trajets directement depuis la base de données
    use App\Models\Trajet;
    use App\Models\Voyage;
    use Carbon\Carbon;

    // 1. Récupérer tous les trajets disponibles
    $trajets = Trajet::all();

    // 2. Récupérer les paramètres de recherche (s'ils existent)
    $ville_depart = request('ville_depart', 'douala');
    $ville_arrivee = request('ville_arrivee', 'yaounde');
    $date_voyage = request('date_voyage', Carbon::today()->format('Y-m-d'));
    $passagers = request('passagers', 1);

    // 3. Récupérer les voyages selon les critères
    $voyages = Voyage::with(['trajet', 'bus'])
        ->whereHas('trajet', function($query) use ($ville_depart, $ville_arrivee) {
            $query->where('villeDepart', 'like', '%'.$ville_depart.'%')
                ->where('villeArrivee', 'like', '%'.$ville_arrivee.'%');
        })
        ->whereDate('dateHeureDepart', '>=', $date_voyage)
        ->where('statut', 'planifie')
        ->get();
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Réservez vos trajets de bus en ligne. Trouvez et comparez les meilleures options de voyage entre Paris et Lyon.">
    <title>Sélection de Trajets de Bus | Voyagez Confiant</title>
    <!-- Chargement de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="api.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1D4ED8',       // Bleu principal
                        'primary-dark': '#1E40AF',   // Bleu foncé (hover)
                        'secondary': '#F59E0B',      // Ambre pour accents
                        'accent': '#10B981',         // Vert pour confirmation
                        'neutral': '#6B7280',        // Gris neutre
                        'light': '#F8FAFC',          // Fond clair
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 4px 20px rgba(0, 0, 0, 0.08)',
                        'card-hover': '0 8px 30px rgba(0, 0, 0, 0.12)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
            background-color: rgba(29, 78, 216, 0.95);
        }
        
        .bus-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }
        
        .bus-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border-left-color: #1D4ED8;
        }
        
        .recommended-card {
            border-left-color: #F59E0B;
            background: linear-gradient(to right, rgba(245, 158, 11, 0.03), transparent);
        }
        
        .economy-card {
            border-left-color: #10B981;
        }
        
        .fastest-card {
            border-left-color: #EF4444;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .service-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            margin-right: 8px;
            background-color: rgba(29, 78, 216, 0.1);
            color: #1D4ED8;
        }
        
        .price-tag {
            position: relative;
        }
        
        .price-tag::before {
            content: "À partir de";
            display: block;
            font-size: 0.75rem;
            color: #6B7280;
            margin-bottom: 2px;
        }
        
        @media (max-width: 767px) {
            .bus-card > div {
                padding: 1rem 0;
            }
            
            .mobile-divider {
                border-top: 1px solid #E5E7EB;
                margin-top: 1rem;
                padding-top: 1rem;
            }
        }
        
        .footer-links a {
            transition: color 0.2s ease;
        }
        
        .footer-links a:hover {
            color: #F59E0B;
        }
        
        .filter-section {
            transition: all 0.3s ease;
        }
        
        .filter-section:not(:last-child) {
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans">

    <!-- En-tête -->
    <header class="sticky-header text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center">
                        <i class="fas fa-bus text-secondary text-2xl mr-3"></i>
                        <h1 class="text-2xl font-bold tracking-tight">Voyagez<span class="font-light">Confiant</span></h1>
                    </div>
                    <div class="mt-2">
                        <nav class="text-sm">
                            <a href="/" class="hover:text-secondary mr-4"><i class="fas fa-home mr-1"></i>Accueil</a>
                            <a href="/login" class="hover:text-secondary mr-4"><i class="fas fa-user mr-1"></i>Connexion</a>
                            <a href="/inscription" class="hover:text-secondary"><i class="fas fa-user-plus mr-1"></i>Inscription</a>
                        </nav>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 w-full sm:w-auto">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center">
                        <div class="mb-3 sm:mb-0 sm:mr-6">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-secondary mr-2"></i>
                                <span class="font-medium">{{ ucfirst($ville_depart) }}</span>
                                <i class="fas fa-arrow-right mx-3 text-secondary"></i>
                                <span class="font-medium">{{ ucfirst($ville_arrivee) }}</span>
                            </div>
                            <div class="flex items-center text-sm mt-1">
                                <i class="far fa-calendar text-secondary mr-2"></i>
                                <span>{{ \Carbon\Carbon::parse($date_voyage)->translatedFormat('l d F Y') }}</span>
                                <span class="mx-2">•</span>
                                <i class="fas fa-user-friends text-secondary mr-2"></i>
                                <span>{{ $passagers }} passager{{ $passagers > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <button onclick="showSearchModal()" class="bg-white text-primary px-5 py-2.5 rounded-lg font-semibold hover:bg-gray-50 transition duration-200 shadow-md flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu Principal -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid lg:grid-cols-4 gap-8">
        
        <!-- Filtres -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-card p-6 h-fit lg:sticky top-24">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Filtres</h2>
                    <button class="text-primary text-sm font-medium hover:text-primary-dark">
                        <i class="fas fa-redo-alt mr-1"></i> Réinitialiser
                    </button>
                </div>
                
                <!-- Filtre : Heure de départ -->
                <div class="filter-section">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Heure de départ
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Matin (05:00 - 11:59)</span>
                            <span class="ml-auto text-sm text-gray-500">3</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" checked class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Après-midi (12:00 - 17:59)</span>
                            <span class="ml-auto text-sm text-gray-500">4</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Soir (18:00 - 23:59)</span>
                            <span class="ml-auto text-sm text-gray-500">2</span>
                        </label>
                    </div>
                </div>
                
                <!-- Filtre : Type de service -->
                <div class="filter-section">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-star text-primary mr-2"></i>
                        Type de service
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" checked class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Standard</span>
                            <span class="ml-auto badge bg-blue-100 text-primary">5 trajets</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Premium</span>
                            <span class="ml-auto badge bg-amber-100 text-amber-800">3 trajets</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Économique</span>
                            <span class="ml-auto badge bg-green-100 text-green-800">1 trajet</span>
                        </label>
                    </div>
                </div>
                
                <!-- Filtre : Compagnies -->
                <div class="filter-section">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-building text-primary mr-2"></i>
                        Compagnies
                    </h3>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">FlixBus</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" checked class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">BlaBlaCar Bus</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                            <input type="checkbox" class="h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-3 text-gray-700">Ouibus</span>
                        </label>
                    </div>
                </div>
                
                <!-- Bouton Appliquer -->
                <button class="w-full py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition duration-200 shadow-md flex items-center justify-center mt-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Appliquer les filtres
                </button>
            </div>
            
            <!-- Assistance -->
            <div class="bg-gradient-to-r from-primary to-primary-dark text-white rounded-xl shadow-card p-6 mt-6">
                <h3 class="font-bold text-lg mb-3 flex items-center">
                    <i class="fas fa-headset mr-3 text-xl"></i>
                    Besoin d'aide ?
                </h3>
                <p class="text-white/90 mb-4">Notre équipe est disponible 7j/7 pour vous accompagner dans votre réservation.</p>
                <div class="space-y-3">
                    <a href="tel:+33123456789" class="flex items-center hover:text-secondary transition">
                        <i class="fas fa-phone-alt mr-3"></i>
                        <span>+33 1 23 45 67 89</span>
                    </a>
                    <a href="mailto:assistance@voyagezconfiant.fr" class="flex items-center hover:text-secondary transition">
                        <i class="fas fa-envelope mr-3"></i>
                        <span>assistance@voyagezconfiant.fr</span>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Résultats -->
        <section class="lg:col-span-3">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Trajets disponibles</h2>
                    <p class="text-gray-600 mt-1">
                        {{ $voyages->count() }} trajet{{ $voyages->count() > 1 ? 's' : '' }} trouvé{{ $voyages->count() > 1 ? 's' : '' }} 
                        pour <span class="font-semibold text-primary">{{ ucfirst($ville_depart) }} → {{ ucfirst($ville_arrivee) }}</span> 
                        le <span class="font-semibold">{{ \Carbon\Carbon::parse($date_voyage)->translatedFormat('d F Y') }}</span>
                    </p>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-3">Trier par :</span>
                    <select id="sortSelect" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="prix_asc">Prix (croissant)</option>
                        <option value="prix_desc">Prix (décroissant)</option>
                        <option value="depart_asc">Heure de départ</option>
                    </select>
                </div>
            </div>
            
            <!-- Liste des trajets -->
            <div class="space-y-5" id="bus-results-list">
                @if($voyages->count() > 0)
                    @foreach($voyages as $voyage)
                        <div class="bus-card bg-white rounded-xl shadow-card p-5 hover:shadow-lg transition-all">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                <!-- Compagnie et horaires -->
                                <div class="md:col-span-5">
                                    <div class="flex flex-col h-full">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-semibold text-primary">{{ $voyage->bus->marque ?? 'Bus' }} {{ $voyage->bus->modele ?? '' }}</span>
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">
                                                {{ $voyage->bus->immatriculation ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($voyage->dateHeureDepart)->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $voyage->trajet->villeDepart ?? 'Départ' }}</div>
                                            </div>
                                            <div class="flex-grow mx-4">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="h-1 bg-gray-300 rounded-full w-full"></div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-sm text-gray-700 font-medium">
                                                        {{ $voyage->trajet->duree ?? '0' }} min
                                                    </div>
                                                    <div class="text-xs text-gray-500">Direct</div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($voyage->dateHeureDepart)->addMinutes($voyage->trajet->duree ?? 0)->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $voyage->trajet->villeArrivee ?? 'Arrivée' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Services inclus -->
                                <div class="md:col-span-4 mobile-divider md:border-l md:border-gray-200 md:pl-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Informations</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="service-icon">
                                                <i class="fas fa-road text-xs"></i>
                                            </span>
                                            {{ $voyage->trajet->distance ?? '0' }} km
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="service-icon">
                                                <i class="fas fa-users text-xs"></i>
                                            </span>
                                            {{ $voyage->bus->capaciteTotale ?? '0' }} places
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="service-icon">
                                                <i class="fas fa-snowflake text-xs"></i>
                                            </span>
                                            Climatisation
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="service-icon">
                                                <i class="fas fa-wifi text-xs"></i>
                                            </span>
                                            Wi-Fi
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Prix et réservation -->
                                <div class="md:col-span-3 mobile-divider md:border-l md:border-gray-200 md:pl-6">
                                    <div class="flex flex-col h-full justify-between">
                                        <div>
                                            <div class="price-tag">
                                                <div class="text-3xl font-bold text-primary">{{ $voyage->prixStandard ?? '0' }} FCFA</div>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-4">Prix par personne</p>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="text-xs text-green-600 font-medium">
                                                <i class="fas fa-check-circle mr-1"></i> Places disponibles
                                            </div>
                                            <button onclick="selectVoyage({{ $voyage->idVoyage }})" 
                                                    class="w-full py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition duration-200 shadow-md">
                                                Sélectionner
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <i class="fa-solid fa-bus-slash text-4xl text-slate-300 mb-4"></i>
                        <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun voyage trouvé</h3>
                        <p class="text-slate-500">Modifiez vos critères de recherche</p>
                        <button onclick="showSearchModal()" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg">
                            Modifier la recherche
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Pied de page -->
    <footer class="bg-gray-900 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-bus text-secondary text-2xl mr-3"></i>
                        <h3 class="text-xl font-bold">Voyagez<span class="font-light">Confiant</span></h3>
                    </div>
                    <p class="text-gray-400 text-sm">Votre partenaire de confiance pour les voyages en bus depuis 2010.</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-4">Informations</h4>
                    <ul class="footer-links space-y-2 text-gray-400">
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>À propos de nous</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Nos engagements</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Carrières</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Presse</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-4">Assistance</h4>
                    <ul class="footer-links space-y-2 text-gray-400">
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Centre d'aide</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Conditions générales</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Politique de confidentialité</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right text-secondary mr-2"></i>Contactez-nous</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-lg mb-4">Newsletter</h4>
                    <p class="text-gray-400 text-sm mb-4">Recevez nos offres exclusives et actualités.</p>
                    <div class="flex">
                        <input type="email" placeholder="Votre email" class="flex-grow px-4 py-2 rounded-l-lg text-gray-900 focus:outline-none">
                        <button class="bg-secondary text-gray-900 px-4 py-2 rounded-r-lg font-semibold hover:bg-amber-500 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-500 text-sm">
                <p>© 2026 VoyagezConfiant. Tous droits réservés. | SIRET: 123 456 789 00010</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
   <script>
// API_BASE_URL et userToken sont déjà définis dans app.blade.php et scripts.blade.php
let currentUser = null;
let selectedSeats = [];
let currentVoyageId = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    setupEventListeners();
    loadVoyages();
    initializeFilters();
});

// Vérifier l'authentification
async function checkAuth() {
    if (!userToken) {
        window.location.href = '/acceuil.html';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/clients/profile`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Non authentifié');
        }
        
        currentUser = await response.json();
        updateUserInfo();
        loadUserTickets();
    } catch (error) {
        console.error('Erreur auth:', error);
        localStorage.removeItem('token');
        window.location.href = '/acceuil.html';
    }
}

// Mettre à jour les infos utilisateur
function updateUserInfo() {
    if (!currentUser) return;
    
    // Mettre à jour le header
    const userInfo = document.querySelector('.header-container .text-right');
    if (userInfo) {
        userInfo.innerHTML = `
            <p class="text-xs text-slate-500">Bienvenue</p>
            <p class="text-sm font-bold text-slate-900">${currentUser.prenom || ''} ${currentUser.nom || ''}</p>
        `;
    }
    
    // Afficher le badge VIP
    if (currentUser.est_vip) {
        const badge = document.createElement('span');
        badge.className = 'bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded-full ml-2 flex items-center gap-1';
        badge.innerHTML = '<i class="fas fa-crown"></i> VIP';
        document.querySelector('.logo-section').appendChild(badge);
    }
}

// Charger les voyages
async function loadVoyages(filters = {}) {
    try {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE_URL}/voyages/search?${params}`);
        
        if (response.ok) {
            const voyages = await response.json();
            displayVoyages(voyages);
        }
    } catch (error) {
        console.error('Erreur chargement voyages:', error);
        showToast('Erreur de chargement des voyages', 'error');
    }
}

// Afficher les voyages
function displayVoyages(voyages) {
    const resultsList = document.getElementById('bus-results-list');
    if (!resultsList) return;
    
    if (!voyages || voyages.length === 0) {
        resultsList.innerHTML = `
            <div class="text-center py-12">
                <i class="fa-solid fa-bus-slash text-4xl text-slate-300 mb-4"></i>
                <h3 class="text-lg font-bold text-slate-700 mb-2">Aucun voyage trouvé</h3>
                <p class="text-slate-500">Modifiez vos critères de recherche</p>
            </div>
        `;
        return;
    }
    
    resultsList.innerHTML = voyages.map((voyage, index) => {
        const isRecommended = index === 0;
        const isPremium = voyage.type_bus === 'premium';
        const isEconomy = voyage.prix && voyage.prix < 25;
        
        return `
            <div class="bus-card ${isRecommended ? 'recommended-card' : ''} ${isPremium ? 'border-l-purple-500' : ''} ${isEconomy ? 'economy-card' : ''}
                bg-white rounded-xl shadow-card p-5 hover:shadow-card-hover transition-all">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Compagnie et horaires -->
                    <div class="md:col-span-5">
                        <div class="flex flex-col h-full">
                            <div class="flex items-center justify-between mb-3">
                                ${isRecommended ? `
                                    <span class="badge bg-amber-100 text-amber-800">
                                        <i class="fas fa-crown mr-1"></i> Recommandé
                                    </span>
                                ` : isPremium ? `
                                    <span class="badge bg-purple-100 text-purple-800">
                                        <i class="fas fa-star mr-1"></i> Premium
                                    </span>
                                ` : isEconomy ? `
                                    <span class="badge bg-green-100 text-green-800">
                                        <i class="fas fa-euro-sign mr-1"></i> Meilleur prix
                                    </span>
                                ` : ''}
                                <span class="text-sm font-semibold text-primary">${voyage.compagnie || 'Compagnie'}</span>
                            </div>
                            <div class="flex items-center mb-2">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">${voyage.heure_depart || '--:--'}</div>
                                    <div class="text-xs text-gray-500 mt-1">${voyage.depart || 'Départ'}</div>
                                </div>
                                <div class="flex-grow mx-4">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="h-1 bg-gray-300 rounded-full w-full"></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-700 font-medium">${voyage.duree || '--h--'}</div>
                                        <div class="text-xs text-gray-500">${voyage.correspondances === 0 ? 'Direct' : `${voyage.correspondances} correspondance(s)`}</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">${voyage.heure_arrivee || '--:--'}</div>
                                    <div class="text-xs text-gray-500 mt-1">${voyage.arrivee || 'Arrivée'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Services inclus -->
                    <div class="md:col-span-4 mobile-divider md:border-l md:border-gray-200 md:pl-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Services inclus</h4>
                        <div class="grid grid-cols-2 gap-2">
                            ${voyage.services && voyage.services.includes('wifi') ? `
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="service-icon">
                                        <i class="fas fa-wifi text-xs"></i>
                                    </span>
                                    Wi-Fi gratuit
                                </div>
                            ` : ''}
                            ${voyage.services && voyage.services.includes('prises') ? `
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="service-icon">
                                        <i class="fas fa-plug text-xs"></i>
                                    </span>
                                    Prises USB
                                </div>
                            ` : ''}
                            ${voyage.services && voyage.services.includes('bagage') ? `
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="service-icon">
                                        <i class="fas fa-suitcase-rolling text-xs"></i>
                                    </span>
                                    Bagage inclus
                                </div>
                            ` : ''}
                            ${voyage.services && voyage.services.includes('climatisation') ? `
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="service-icon">
                                        <i class="fas fa-snowflake text-xs"></i>
                                    </span>
                                    Climatisation
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Prix et réservation -->
                    <div class="md:col-span-3 mobile-divider md:border-l md:border-gray-200 md:pl-6">
                        <div class="flex flex-col h-full justify-between">
                            <div>
                                <div class="price-tag">
                                    <div class="text-3xl font-bold text-primary">${voyage.prix ? `${voyage.prix} €` : '-- €'}</div>
                                </div>
                                <p class="text-xs text-gray-500 mb-4">Prix par personne, TVA incluse</p>
                            </div>
                            <div class="space-y-3">
                                <div class="text-xs ${voyage.places_disponibles > 5 ? 'text-green-600' : voyage.places_disponibles > 0 ? 'text-amber-600' : 'text-red-600'} font-medium">
                                    <i class="fas ${voyage.places_disponibles > 5 ? 'fa-check-circle' : voyage.places_disponibles > 0 ? 'fa-exclamation-circle' : 'fa-times-circle'} mr-1"></i>
                                    ${voyage.places_disponibles > 5 ? 'Places disponibles' : voyage.places_disponibles > 0 ? `${voyage.places_disponibles} places restantes` : 'Complet'}
                                </div>
                                <button onclick="selectVoyageForReservation(${voyage.id})" 
                                        class="w-full py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition duration-200 shadow-md"
                                        ${voyage.places_disponibles <= 0 ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''}>
                                    ${voyage.places_disponibles <= 0 ? 'Complet' : 'Sélectionner'}
                                </button>
                                <button class="w-full py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200"
                                        onclick="toggleFavorite(${voyage.id})">
                                    <i class="far fa-heart mr-2"></i>Favoris
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Initialiser les filtres
function initializeFilters() {
    // Filtre par heure
    document.querySelectorAll('.filter-section input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
    
    // Filtre par compagnie
    document.querySelectorAll('input[name="compagnie"]').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
    
    // Filtre par type de service
    document.querySelectorAll('input[name="type_service"]').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
    
    // Bouton réinitialiser
    const resetBtn = document.querySelector('button:has(.fa-redo-alt)');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }
    
    // Bouton appliquer
    const applyBtn = document.querySelector('button:has(.fa-check-circle)');
    if (applyBtn) {
        applyBtn.addEventListener('click', applyFilters);
    }
    
    // Tri
    const sortSelect = document.querySelector('select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            applyFilters();
        });
    }
}

// Appliquer les filtres
function applyFilters() {
    const filters = {};
    
    // Heure de départ
    const heures = [];
    document.querySelectorAll('input[name="heure"]:checked').forEach(cb => {
        const label = cb.nextElementSibling.textContent.trim();
        if (label.includes('Matin')) heures.push('matin');
        if (label.includes('Après-midi')) heures.push('apres-midi');
        if (label.includes('Soir')) heures.push('soir');
    });
    if (heures.length > 0) filters.heure_depart = heures;
    
    // Type de service
    const services = [];
    document.querySelectorAll('input[name="type_service"]:checked').forEach(cb => {
        const label = cb.nextElementSibling.textContent.trim();
        services.push(label.toLowerCase());
    });
    if (services.length > 0) filters.type_service = services;
    
    // Compagnies
    const compagnies = [];
    document.querySelectorAll('input[name="compagnie"]:checked').forEach(cb => {
        const label = cb.nextElementSibling.textContent.trim();
        compagnies.push(label);
    });
    if (compagnies.length > 0) filters.compagnie = compagnies;
    
    // Tri
    const sortSelect = document.querySelector('select');
    if (sortSelect && sortSelect.value) {
        switch(sortSelect.value) {
            case 'Prix (croissant)':
                filters.sort = 'prix_asc';
                break;
            case 'Prix (décroissant)':
                filters.sort = 'prix_desc';
                break;
            case 'Durée (plus rapide)':
                filters.sort = 'duree_asc';
                break;
            case 'Heure de départ':
                filters.sort = 'heure_depart_asc';
                break;
        }
    }
    
    loadVoyages(filters);
}

// Réinitialiser les filtres
function resetFilters() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    const sortSelect = document.querySelector('select');
    if (sortSelect) {
        sortSelect.selectedIndex = 0;
    }
    
    loadVoyages();
}

// Sélectionner un voyage pour réservation
function selectVoyageForReservation(voyageId) {
    if (!userToken) {
        showToast('Veuillez vous connecter pour réserver', 'warning');
        window.location.href = '/acceuil.html';
        return;
    }
    
    currentVoyageId = voyageId;
    
    // Récupérer les sièges disponibles
    fetch(`${API_BASE_URL}/voyages/${voyageId}/sieges-disponibles`)
        .then(response => response.json())
        .then(sieges => {
            showSeatSelectionModal(voyageId, sieges);
        })
        .catch(error => {
            console.error('Erreur sièges:', error);
            showToast('Erreur de chargement des sièges', 'error');
        });
}

// Afficher la modal de sélection de sièges
function showSeatSelectionModal(voyageId, availableSeats) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-900">Sélectionnez vos sièges</h3>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="mb-6">
                    <h4 class="font-bold text-slate-900 mb-2">Disposition du bus</h4>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="grid grid-cols-4 gap-2" id="seat-grid">
                            ${generateSeatGrid(availableSeats)}
                        </div>
                        <div class="flex items-center justify-center gap-6 mt-6 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-green-100 border border-green-300 rounded"></div>
                                <span>Disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-red-100 border border-red-300 rounded"></div>
                                <span>Occupé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-100 border-2 border-blue-500 rounded"></div>
                                <span>Sélectionné</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="selected-seats-display" class="mb-6 p-4 bg-blue-50 rounded-lg hidden">
                    <h4 class="font-bold text-blue-900 mb-2">Sièges sélectionnés</h4>
                    <div id="selected-seats-list" class="flex flex-wrap gap-2"></div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500" id="seat-count">0 siège sélectionné</p>
                    <p class="text-lg font-bold" id="total-price">Total: 0 €</p>
                </div>
                <button onclick="proceedToPayment(${voyageId})" 
                        id="proceed-btn"
                        class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-dark transition disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    Continuer vers le paiement
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Initialiser la sélection des sièges
    initializeSeatSelection(availableSeats);
}

// Générer la grille des sièges
function generateSeatGrid(availableSeats) {
    let html = '';
    const totalSeats = 40; // Supposons un bus de 40 places
    
    for (let i = 1; i <= totalSeats; i++) {
        const isAvailable = availableSeats.includes(i);
        const seatClass = isAvailable 
            ? 'bg-green-100 border-green-300 hover:bg-green-200 cursor-pointer' 
            : 'bg-red-100 border-red-300 cursor-not-allowed';
        
        html += `
            <button class="seat-btn w-full aspect-square rounded border flex items-center justify-center font-bold transition ${seatClass}"
                    data-seat="${i}"
                    ${!isAvailable ? 'disabled' : ''}
                    onclick="selectSeat(${i}, ${isAvailable})">
                ${i}
            </button>
        `;
        
        // Ajouter un passage à la ligne tous les 4 sièges
        if (i % 4 === 0 && i < totalSeats) {
            html += '<div class="col-span-4 h-4"></div>';
        }
    }
    
    return html;
}

// Initialiser la sélection des sièges
function initializeSeatSelection(availableSeats) {
    selectedSeats = [];
}

// Sélectionner un siège
function selectSeat(seatNumber, isAvailable) {
    if (!isAvailable) return;
    
    const index = selectedSeats.indexOf(seatNumber);
    const seatBtn = document.querySelector(`[data-seat="${seatNumber}"]`);
    
    if (index === -1) {
        // Ajouter le siège
        selectedSeats.push(seatNumber);
        seatBtn.classList.remove('bg-green-100', 'border-green-300');
        seatBtn.classList.add('bg-blue-100', 'border-2', 'border-blue-500');
    } else {
        // Retirer le siège
        selectedSeats.splice(index, 1);
        seatBtn.classList.remove('bg-blue-100', 'border-2', 'border-blue-500');
        seatBtn.classList.add('bg-green-100', 'border-green-300');
    }
    
    updateSeatSelectionDisplay();
}

// Mettre à jour l'affichage de la sélection
function updateSeatSelectionDisplay() {
    const seatCount = selectedSeats.length;
    const seatCountElement = document.getElementById('seat-count');
    const totalPriceElement = document.getElementById('total-price');
    const proceedBtn = document.getElementById('proceed-btn');
    const selectedDisplay = document.getElementById('selected-seats-display');
    const selectedList = document.getElementById('selected-seats-list');
    
    if (seatCountElement) {
        seatCountElement.textContent = `${seatCount} siège${seatCount > 1 ? 's' : ''} sélectionné${seatCount > 1 ? 's' : ''}`;
    }
    
    // Calculer le prix total (vous devrez récupérer le prix du voyage)
    const pricePerSeat = 22.99; // À remplacer par le prix réel
    const totalPrice = seatCount * pricePerSeat;
    
    if (totalPriceElement) {
        totalPriceElement.textContent = `Total: ${totalPrice.toFixed(2)} €`;
    }
    
    if (proceedBtn) {
        proceedBtn.disabled = seatCount === 0;
    }
    
    if (selectedDisplay) {
        if (seatCount > 0) {
            selectedDisplay.classList.remove('hidden');
            selectedList.innerHTML = selectedSeats.map(seat => `
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                    <i class="fas fa-chair"></i>
                    ${seat}
                </span>
            `).join('');
        } else {
            selectedDisplay.classList.add('hidden');
        }
    }
}

// Procéder au paiement
async function proceedToPayment(voyageId) {
    if (selectedSeats.length === 0) {
        showToast('Veuillez sélectionner au moins un siège', 'warning');
        return;
    }
    
    try {
        // Créer la réservation
        const response = await fetch(`${API_BASE_URL}/tickets`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                voyage_id: voyageId,
                siege_ids: selectedSeats
            })
        });
        
        if (response.ok) {
            const ticket = await response.json();
            showToast('Réservation créée avec succès !');
            
            // Fermer la modal
            document.querySelector('.fixed.inset-0').remove();
            
            // Rediriger vers le paiement
            setTimeout(() => {
                window.location.href = `/paiement.html?ticket=${ticket.id}`;
            }, 1000);
        } else {
            const error = await response.json();
            showToast(error.message || 'Erreur lors de la réservation', 'error');
        }
    } catch (error) {
        console.error('Erreur réservation:', error);
        showToast('Erreur de connexion au serveur', 'error');
    }
}

// Charger les tickets de l'utilisateur
async function loadUserTickets() {
    try {
        const response = await fetch(`${API_BASE_URL}/tickets`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const tickets = await response.json();
            updateTicketsSidebar(tickets);
        }
    } catch (error) {
        console.error('Erreur chargement tickets:', error);
    }
}

// Mettre à jour la sidebar des tickets
function updateTicketsSidebar(tickets) {
    const sidebar = document.querySelector('aside');
    if (!sidebar || !tickets || tickets.length === 0) return;
    
    // Créer une section pour les réservations récentes
    const reservationsSection = document.createElement('div');
    reservationsSection.className = 'bg-white rounded-xl shadow-card p-6 mt-6';
    reservationsSection.innerHTML = `
        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-ticket-alt text-primary mr-2"></i>
            Mes réservations récentes
        </h3>
        <div class="space-y-3">
            ${tickets.slice(0, 3).map(ticket => `
                <div class="border border-gray-200 rounded-lg p-3 hover:border-primary transition">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-sm">${ticket.reference || 'N/A'}</p>
                            <p class="text-xs text-gray-500">${ticket.voyage?.trajet?.depart || ''} → ${ticket.voyage?.trajet?.arrivee || ''}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full ${getStatusClass(ticket.statut)}">
                            ${ticket.statut || 'inconnu'}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-bold">${ticket.montant_total || 0} €</p>
                        <button onclick="viewTicket(${ticket.id})" class="text-xs text-primary hover:text-primary-dark">
                            Détails
                        </button>
                    </div>
                </div>
            `).join('')}
        </div>
        ${tickets.length > 3 ? `
            <button onclick="viewAllTickets()" class="w-full mt-4 text-center text-primary hover:text-primary-dark text-sm font-bold">
                Voir toutes mes réservations (${tickets.length})
            </button>
        ` : ''}
    `;
    
    // Ajouter après la section d'assistance
    const assistanceSection = sidebar.querySelector('.bg-gradient-to-r');
    if (assistanceSection) {
        sidebar.insertBefore(reservationsSection, assistanceSection.nextSibling);
    } else {
        sidebar.appendChild(reservationsSection);
    }
}

function getStatusClass(status) {
    switch(status?.toLowerCase()) {
        case 'confirme': return 'bg-green-100 text-green-800';
        case 'reserve': return 'bg-blue-100 text-blue-800';
        case 'annule': return 'bg-red-100 text-red-800';
        case 'embarque': return 'bg-purple-100 text-purple-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Voir les détails d'un ticket
async function viewTicket(ticketId) {
    try {
        const response = await fetch(`${API_BASE_URL}/tickets/${ticketId}`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const ticket = await response.json();
            showTicketModal(ticket);
        }
    } catch (error) {
        console.error('Erreur chargement ticket:', error);
        showToast('Erreur de chargement du ticket', 'error');
    }
}

// Afficher la modal du ticket
function showTicketModal(ticket) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-900">Détails du billet</h3>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-slate-500">Référence</p>
                            <p class="font-bold text-lg">${ticket.reference || 'N/A'}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-bold ${getStatusClass(ticket.statut)}">
                            ${ticket.statut || 'inconnu'}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-slate-500">Voyage</p>
                        <p class="font-bold">${ticket.voyage?.trajet?.depart || ''} → ${ticket.voyage?.trajet?.arrivee || ''}</p>
                        <p class="text-sm text-slate-500">${ticket.voyage?.date_depart || ''} • ${ticket.voyage?.heure_depart || ''}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-slate-500">Sièges</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            ${ticket.sieges?.map(siege => `
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                    ${siege.numero || ''}
                                </span>
                            `).join('') || 'Aucun'}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Passager</p>
                            <p class="font-bold">${ticket.client?.prenom || ''} ${ticket.client?.nom || ''}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Prix</p>
                            <p class="font-bold text-xl">${ticket.montant_total || 0} €</p>
                        </div>
                    </div>
                    
                    ${ticket.qr_code_url ? `
                        <div class="text-center pt-4 border-t border-slate-200">
                            <p class="text-sm text-slate-500 mb-2">QR Code d'embarquement</p>
                            <img src="${ticket.qr_code_url}" alt="QR Code" class="w-32 h-32 mx-auto">
                        </div>
                    ` : ''}
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 flex justify-between">
                ${ticket.statut === 'reserve' ? `
                    <button onclick="annulerTicket(${ticket.id})" 
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                        Annuler
                    </button>
                ` : ''}
                <button onclick="printTicket(${ticket.id})" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    Imprimer
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Annuler un ticket
async function annulerTicket(ticketId) {
    if (!confirm('Êtes-vous sûr de vouloir annuler ce billet ?')) return;
    
    try {
        const response = await fetch(`${API_BASE_URL}/tickets/${ticketId}/annuler`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            showToast('Billet annulé avec succès');
            document.querySelector('.fixed.inset-0').remove();
            loadUserTickets();
        } else {
            const error = await response.json();
            showToast(error.message || 'Erreur lors de l\'annulation', 'error');
        }
    } catch (error) {
        console.error('Erreur annulation:', error);
        showToast('Erreur de connexion', 'error');
    }
}

// Imprimer un ticket
async function printTicket(ticketId) {
    try {
        const response = await fetch(`${API_BASE_URL}/tickets/${ticketId}/qrcode`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'image/png'
            }
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = URL.createObjectURL(blob);
            
            // Ouvrir dans une nouvelle fenêtre pour impression
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Ticket ${ticketId}</title>
                    <style>
                        body { font-family: sans-serif; padding: 20px; }
                        .ticket { border: 2px dashed #ccc; padding: 20px; max-width: 400px; margin: auto; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .qr-code { display: block; margin: 20px auto; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <h2>BusRapide</h2>
                            <p>Billet d'embarquement</p>
                        </div>
                        <img src="${url}" class="qr-code" width="200" height="200">
                        <p>Présentez ce QR code à l'embarquement</p>
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 1000);
                        };
                    </script>
                </body>
                </html>
            `);
        }
    } catch (error) {
        console.error('Erreur impression:', error);
        showToast('Erreur lors de l\'impression', 'error');
    }
}

// Voir tous les tickets
function viewAllTickets() {
    // Rediriger vers une page dédiée ou ouvrir une modale plus grande
    alert('Cette fonctionnalité serait implémentée dans une page dédiée');
}

// Basculer favori
function toggleFavorite(voyageId) {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas', 'text-red-500');
        btn.innerHTML = '<i class="fas fa-heart mr-2 text-red-500"></i>Favori';
        showToast('Ajouté aux favoris');
    } else {
        icon.classList.remove('fas', 'text-red-500');
        icon.classList.add('far');
        btn.innerHTML = '<i class="far fa-heart mr-2"></i>Favoris';
        showToast('Retiré des favoris');
    }
} 

// Configurer les événements
function setupEventListeners() {
    // Gestion des favoris existants
    document.querySelectorAll('.bus-card button:has(.fa-heart)').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = '#EF4444';
                this.innerHTML = '<i class="fas fa-heart mr-2" style="color:#EF4444"></i>Favori';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                this.innerHTML = '<i class="far fa-heart mr-2"></i>Favoris';
            }
        });
    });
    
    // Bouton modifier dans le header
    const modifyBtn = document.querySelector('button:has(.fa-edit)');
    if (modifyBtn) {
        modifyBtn.addEventListener('click', function() {
            showSearchModificationModal();
        });
    }
}

function showSearchModificationModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl w-full max-w-md">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-xl font-bold text-slate-900">Modifier la recherche</h3>
            </div>
            <div class="p-6">
                <form onsubmit="event.preventDefault(); updateSearch();">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Trajet</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" placeholder="Départ" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                <input type="text" placeholder="Arrivée" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Passagers</label>
                            <select class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                <option>1 passager</option>
                                <option>2 passagers</option>
                                <option>3 passagers</option>
                                <option>4 passagers</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="this.closest('.fixed').remove()"
                                class="flex-1 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="flex-1 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark">
                            Appliquer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function updateSearch() {
    showToast('Recherche modifiée');
    document.querySelector('.fixed.inset-0').remove();
    // Recharger les voyages avec les nouveaux critères
    loadVoyages();
}

// Fonction toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'warning' ? 'bg-yellow-500 text-white' : 
        'bg-green-500 text-white'
    }`;
    
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fa-solid ${
                type === 'error' ? 'fa-circle-exclamation' : 
                type === 'warning' ? 'fa-triangle-exclamation' : 
                'fa-circle-check'
            } text-xl"></i>
            <div>
                <p class="font-bold">${type === 'error' ? 'Erreur' : type === 'warning' ? 'Attention' : 'Succès'}</p>
                <p class="text-sm opacity-90">${message}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Déconnexion
function logout() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        if (userToken) {
            fetch(`${API_BASE_URL}/clients/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${userToken}`,
                    'Accept': 'application/json'
                }
            }).catch(console.error);
        }
        
        localStorage.removeItem('token');
        localStorage.removeItem('user_type');
        window.location.href = '/acceuil.html';
    }
}

// Exposer les fonctions globalement
window.selectVoyageForReservation = selectVoyageForReservation;
window.selectSeat = selectSeat;
window.proceedToPayment = proceedToPayment;
window.viewTicket = viewTicket;
window.annulerTicket = annulerTicket;
window.printTicket = printTicket;
window.toggleFavorite = toggleFavorite;
window.logout = logout;
window.viewAllTickets = viewAllTickets;
</script>

<!-- Modal de modification de recherche -->
<div id="searchModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Modifier la recherche</h3>
            
            <form action="/reservation" method="GET" class="space-y-4">
                <!-- Ville de départ -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville de départ</label>
                    <select name="ville_depart" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez...</option>
                        @foreach($trajets as $trajet)
                            <option value="{{ strtolower($trajet->villeDepart) }}" {{ $ville_depart == strtolower($trajet->villeDepart) ? 'selected' : '' }}>
                                {{ $trajet->villeDepart }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Ville d'arrivée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville d'arrivée</label>
                    <select name="ville_arrivee" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Sélectionnez...</option>
                        @foreach($trajets as $trajet)
                            <option value="{{ strtolower($trajet->villeArrivee) }}" {{ $ville_arrivee == strtolower($trajet->villeArrivee) ? 'selected' : '' }}>
                                {{ $trajet->villeArrivee }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de voyage</label>
                    <input type="date" name="date_voyage" value="{{ $date_voyage }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <!-- Passagers -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de passagers</label>
                    <select name="passagers" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $passagers == $i ? 'selected' : '' }}>{{ $i }} passager{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Boutons -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeSearchModal()" 
                            class="flex-1 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentVoyageId = null;
let selectedSeats = [];

// Fonctions pour le modal
function showSearchModal() {
    document.getElementById('searchModal').classList.remove('hidden');
}

function closeSearchModal() {
    document.getElementById('searchModal').classList.add('hidden');
}

// Fermer en cliquant à l'extérieur
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('searchModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSearchModal();
            }
        });
    }
    
    // Fermer avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeSearchModal();
        }
    });
    
    // Gestion du tri
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortVoyages(this.value);
        });
    }
});

// Fonction pour trier les voyages
function sortVoyages(sortType) {
    const container = document.getElementById('bus-results-list');
    const voyages = Array.from(container.querySelectorAll('.bus-card'));
    
    voyages.sort(function(a, b) {
        const priceA = parseFloat(a.querySelector('.text-3xl').textContent.replace(' FCFA', '').replace(/[^0-9.]/g, ''));
        const priceB = parseFloat(b.querySelector('.text-3xl').textContent.replace(' FCFA', '').replace(/[^0-9.]/g, ''));
        const timeA = a.querySelector('.text-2xl:first-child').textContent;
        const timeB = b.querySelector('.text-2xl:first-child').textContent;
        
        switch(sortType) {
            case 'prix_asc':
                return priceA - priceB;
            case 'prix_desc':
                return priceB - priceA;
            case 'depart_asc':
                return timeA.localeCompare(timeB);
            default:
                return 0;
        }
    });
    
    // Réorganiser les éléments
    voyages.forEach(voyage => container.appendChild(voyage));
}

// Fonction pour sélectionner un voyage (simplifiée)
function selectVoyage(voyageId) {
    currentVoyageId = voyageId;
    
    // Afficher un message ou rediriger
    alert(`Voyage ${voyageId} sélectionné. Redirection vers la sélection des sièges...`);
    
    // Redirection vers une page de sélection de sièges
    window.location.href = `/selection-siege/${voyageId}`;
}

// Fonction toast (pour les messages)
function showToast(message, type = 'success') {
    // Créer un élément toast
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded-md shadow-lg text-white ${
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-green-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Retirer après 3 secondes
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Exposer les fonctions globalement
window.showSearchModal = showSearchModal;
window.closeSearchModal = closeSearchModal;
window.selectVoyage = selectVoyage;
</script>
</body>
</html>