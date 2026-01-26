<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration - BusRapide')</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="h-full overflow-hidden">
    
    @yield('content')

    <!-- Scripts - ORDRE CORRECT -->
    <!-- 1. dashboard-app.js D'ABORD (version simplifiée) -->
    <script src="/js/dashboard-app.js" defer></script>
    
    <!-- 2. Script d'initialisation Alpine SIMPLIFIÉ -->
    <script>
        // Attendre qu'Alpine soit disponible
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initialisé...');
            
            // Vérifier si dashboardApp existe DANS window
            if (typeof window.dashboardApp === 'function') {
                console.log('✅ dashboardApp trouvée dans window, enregistrement...');
                Alpine.data('dashboardApp', window.dashboardApp);
            } 
            // Fallback: vérifier si dashboardApp existe globalement
            else if (typeof dashboardApp === 'function') {
                console.log('✅ dashboardApp trouvée globalement, enregistrement...');
                Alpine.data('dashboardApp', dashboardApp);
            }
            else {
                console.error('❌ dashboardApp non trouvée');
                // Créer un composant de secours COMPLET avec TOUTES les propriétés nécessaires
                Alpine.data('dashboardApp', () => ({
                    // === ÉTAT DE BASE ===
                    mobileMenuOpen: false,
                    currentSection: 'dashboard',
                    loading: false,
                    activeVoyageTab: 'list',
                    userName: '',
                    userPrenom: '',
                    userRole: '',
                    userEmail: '',
                    userPhone: '',
                    sessionsCount: 1,
                    editingProfile: false,
                    toasts: [],
                    editingVoyage: null,
                    deleteConfirmVoyage: null,
                    
                    // === FORMULAIRES ===
                    voyageForm: { 
                        idVoyage: '', 
                        idBus: '', 
                        idTrajet: '', 
                        dateHeureDepart: '', 
                        prixStandard: 0, 
                        prixVIP: 0 
                    },
                    busForm: { 
                        immatriculation: '', 
                        marque: '', 
                        modele: '', 
                        capaciteTotale: 45, 
                        nbSiegesVIP: 5, 
                        statut: 'en_service', 
                        dateMiseEnService: new Date().toISOString().split('T')[0] 
                    },
                    trajetForm: { 
                        villeDepart: '', 
                        villeArrivee: '', 
                        duree: 180, 
                        distance: 250, 
                        prixStandard: 5000, 
                        prixVIP: 10000 
                    },
                    profileForm: { 
                        nom: '', 
                        prenom: '', 
                        email: '' 
                    },
                    
                    // === ERREURS ===
                    formErrors: { 
                        bus: null, 
                        trajet: null, 
                        voyage: null 
                    },
                    
                    // === MODALES ===
                    showModal: null,
                    showTicketModal: false,
                    
                    // === DONNÉES ===
                    buses: [],
                    trajets: [],
                    voyages: [],
                    tickets: [],
                    
                    // === TICKETS ===
                    ticketStats: { 
                        confirmes: 0, 
                        reserves: 0, 
                        annules: 0, 
                        utilises: 0,
                        chiffreAffaire: 0,
                        vip: 0,
                        standard: 0,
                        vipRevenue: 0,
                        standardRevenue: 0 
                    },
                    loadingTickets: false,
                    ticketSearchQuery: '',
                    ticketStatusFilter: 'all',
                    ticketPeriodFilter: 'all',
                    ticketClassFilter: 'all',
                    selectedTickets: [],
                    selectedTicket: null,
                    ticketsPerPage: 10,
                    currentTicketPage: 1,
                    totalTickets: 0,
                    totalTicketPages: 0,
                    ticketPages: [],
                    searchTimeout: null,
                    selectAllTickets: false,
                    
                    // === UI DATA ===
                    statsFast: [],
                    kpis: [],
                    recentVoyages: [],
                    liveActivity: [],
                    
                    // === MÉTHODES DE BASE ===
                    init() {
                        console.log('Composant secours initialisé');
                        // Tenter de récupérer les données utilisateur
                        try {
                            const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
                            this.userName = (userData.nom || 'Admin') + ' ' + (userData.prenom || '');
                            this.userRole = userData.role || '';
                            this.userEmail = userData.email || '';
                            this.userPrenom = userData.prenom || '';
                        } catch (e) {
                            console.error('Erreur initialisation:', e);
                        }
                    },
                    
                    currentSectionTitle() {
                        const titles = { 
                            dashboard: "Vue d'ensemble", 
                            voyages: 'Gestion des Voyages', 
                            tickets: 'Gestion des Billets', 
                            clients: 'Base de données clients', 
                            embarquement: "Console d'embarquement", 
                            personnel: "Gestion de l'équipe", 
                            rapports: 'Rapports d\'activité', 
                            profil: 'Mon profil utilisateur' 
                        };
                        return titles[this.currentSection] || this.currentSection;
                    },
                    
                    getModuleIcon(section) {
                        const icons = { 
                            voyages: 'fa-solid fa-route', 
                            tickets: 'fa-solid fa-ticket-simple', 
                            clients: 'fa-solid fa-users', 
                            embarquement: 'fa-solid fa-clipboard-check', 
                            personnel: 'fa-solid fa-user-shield', 
                            rapports: 'fa-solid fa-file-invoice-dollar' 
                        };
                        return icons[section] || 'fa-solid fa-cube';
                    },
                    
                    isAdmin() { 
                        return this.userRole === 'admin' || this.userRole === 'super_admin'; 
                    },
                    
                    isGestionnaire() { 
                        return this.userRole === 'gestionnaire'; 
                    },
                    
                    isControleur() { 
                        return this.userRole === 'controleur'; 
                    },
                    
                    formatCurrency(amount) {
                        if (!amount) return '0 FCFA';
                        return new Intl.NumberFormat('fr-FR', { 
                            style: 'currency', 
                            currency: 'XOF',
                            minimumFractionDigits: 0 
                        }).format(amount);
                    },
                    
                    formatDate(dateString) {
                        if (!dateString) return '';
                        return new Date(dateString).toLocaleDateString('fr-FR');
                    },
                    
                    formatDateTime(dateString) {
                        if (!dateString) return '';
                        return new Date(dateString).toLocaleString('fr-FR');
                    },
                    
                    addToast(message, type = 'success') { 
                        const id = Date.now(); 
                        this.toasts.push({ id, message, type }); 
                        setTimeout(() => this.removeToast(id), 5000); 
                    },
                    
                    removeToast(id) { 
                        this.toasts = this.toasts.filter(t => t.id !== id); 
                    },
                    
                    refreshData() {
                        console.log('Rafraîchissement des données...');
                        this.loading = true;
                        setTimeout(() => {
                            this.loading = false;
                            this.addToast('Données rafraîchies', 'success');
                        }, 1000);
                    },
                    
                    logout() { 
                        localStorage.removeItem('auth_token'); 
                        localStorage.removeItem('user_data'); 
                        sessionStorage.removeItem('auth_token');
                        window.location.href = '/admin/login'; 
                    },
                    
                    // Méthodes vides pour éviter les erreurs
                    fetchTickets() {},
                    fetchBuses() {},
                    fetchTrajets() {},
                    fetchVoyages() {},
                    saveBus() {},
                    saveTrajet() {},
                    saveVoyage() {},
                    editVoyage() {},
                    updateProfile() {},
                    filteredTickets() { return []; },
                    debouncedSearch() {},
                    prevTicketPage() {},
                    nextTicketPage() {},
                    toggleAllTickets() {},
                    viewTicketDetails() {},
                    confirmTicket() {},
                    cancelTicket() {},
                    markTicketAsUsed() {},
                    printTicket() {},
                    exportTickets() {},
                    batchConfirmTickets() {},
                    batchCancelTickets() {},
                    deleteTicket() {}
                }));
                console.log('⚠️ Composant secours créé');
            }
        });
    </script>
    
    <!-- 3. Alpine.js EN DERNIER -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Utilitaires globaux -->
    <script>
        window.showToast = function(message, type = 'success') {
            const event = new CustomEvent('toast', {
                detail: { message, type }
            });
            window.dispatchEvent(event);
        };
        
        window.API_BASE_URL = '/api';
    </script>
    
    @stack('scripts')
</body>
</html>