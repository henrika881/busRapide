function dashboardApp() {
    return {
        // ==================== DONNÉES DE BASE ====================
        currentSection: 'dashboard',
        loading: false,
        mobileMenuOpen: false,
        activeVoyageTab: 'list',

        // Helper pour les appels API
        async apiFetch(endpoint, options = {}) {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            };

            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            try {
                const response = await fetch(`/api${endpoint}`, {
                    ...options,
                    headers
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 401) {
                        this.addToast('Session expirée, veuillez vous reconnecter', 'error');
                        setTimeout(() => window.location.href = '/admin/login', 2000);
                    }
                    throw { status: response.status, data };
                }

                return data;
            } catch (error) {
                console.error(`Erreur API (${endpoint}):`, error);
                throw error;
            }
        },

        // Utilisateur
        userName: 'Admin Test',
        userPrenom: 'Admin',
        userRole: 'admin',
        userEmail: 'admin@test.com',
        userPhone: '+221 77 000 00 00',
        sessionsCount: 1,

        // UI
        editingProfile: false,
        toasts: [],
        showModal: null,

        // ==================== FORMULAIRES ====================
        profileForm: {
            nom: 'Admin',
            prenom: 'Test',
            email: 'admin@test.com'
        },
        busForm: {
            immatriculation: '',
            statut: 'en_service',
            marque: '',
            modele: '',
            capaciteTotale: 0,
            nbSiegesVIP: 0,
            dateMiseEnService: ''
        },
        trajetForm: {
            villeDepart: '',
            villeArrivee: '',
            prixStandard: 0,
            prixVIP: 0,
            distance: 0,
            duree: 0
        },
        voyageForm: {
            idTrajet: '',
            idBus: '',
            dateHeureDepart: '',
            prixStandard: 0,
            prixVIP: 0
        },
        clientForm: {
            prenom: '',
            nom: '',
            email: '',
            telephone: '',
            motDePasse: '',
            ville: '',
            statut: 'actif',
            estVerifie: false
        },
        personnelForm: {
            prenom: '',
            nom: '',
            email: '',
            telephone: '',
            role: 'gestionnaire',
            poste: '',
            salaire: 0,
            dateEmbauche: '',
            motDePasse: '',
            statut: 'actif',
            matricule: '',
            permissions: []
        },

        // ==================== ÉTATS D'ÉDITION ====================
        editingVoyage: null,
        editingTrajet: null,
        editingClient: null,
        editingPersonnel: null,

        // ==================== COLLECTIONS ====================
        voyages: [],
        buses: [],
        trajets: [],
        clients: [],
        tickets: [],

        personnel: [],

        // ==================== GESTION CLIENTS ====================
        clientSearchQuery: '',
        clientStatusFilter: 'all',
        selectedClients: [],
        selectAllClients: false,
        loadingClients: false,
        currentClientPage: 1,
        clientsPerPage: 10,
        totalClients: 0,
        clientPages: [],
        totalClientPages: 1,

        // ==================== GESTION BILLETS ====================
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
        clientStats: {
            total: 0,
            actifs: 0,
            actifsPourcentage: 0,
            nouveauxMois: 0,
            variationMois: 0,
            chiffreAffaire: 0,
            caMoyen: 0
        },
        loadingTickets: false,
        ticketSearchQuery: '',
        ticketStatusFilter: 'all',
        ticketPeriodFilter: 'all',
        ticketClassFilter: 'all',
        selectedTickets: [],
        selectAllTickets: false,
        ticketsPerPage: 10,
        currentTicketPage: 1,
        totalTickets: 2,
        ticketPages: [],
        totalTicketPages: 1,

        // ==================== CONSOLE D'EMBARQUEMENT ====================
        showScannerModal: false,
        scanInput: '',
        scanFeedback: {
            message: '',
            type: '',
            details: ''
        },
        showVoyagesProches: true,
        selectedVoyage: null,
        passagerFilter: 'all',
        passagerClassFilter: 'all',
        voyageSearch: '',
        embarquementStats: {
            aEmbarquer: 2,
            embarques: 1,
            retards: 1,
            absents: 1
        },
        voyagesProches: [],
        filteredVoyagesList: [],
        passagers: [],
        filteredPassagers: [],

        // ==================== GESTION PERSONNEL ====================
        personnelSearchQuery: '',
        personnelRoleFilter: 'all',
        personnelStatusFilter: 'all',
        selectedPersonnel: [],
        selectAllPersonnel: false,
        loadingPersonnel: false,
        currentPersonnelPage: 1,
        personnelPerPage: 10,
        totalPersonnel: 0,
        personnelPages: [],
        totalPersonnelPages: 1,
        personnelStats: {
            total: 12,
            actifs: 10,
            actifsPourcentage: 83,
            admins: 2,
            gestionnaires: 3,
            controleurs: 5,
            conducteurs: 2
        },
        availablePermissions: [
            { value: 'gestion_voyages', label: 'Gestion Voyages' },
            { value: 'gestion_billets', label: 'Gestion Billets' },
            { value: 'gestion_clients', label: 'Gestion Clients' },
            { value: 'validation_embarquement', label: 'Validation Embarquement' },
            { value: 'gestion_bus', label: 'Gestion Bus' },
            { value: 'gestion_trajets', label: 'Gestion Trajets' },
            { value: 'visualisation_stats', label: 'Visualisation Stats' },
            { value: 'administration_systeme', label: 'Administration' }
        ],

        // ==================== RAPPORTS & STATS ====================
        reportPeriod: 'month',
        customDateStart: '',
        customDateEnd: '',
        reportStats: {
            totalRevenue: 0,
            revenueTrend: 0,
            totalTickets: 0,
            ticketsTrend: 0,
            totalVoyages: 0,
            completedVoyages: 0,
            occupancyRate: 0,
            vipTickets: 0,
            standardTickets: 0,
            vipRevenue: 0,
            standardRevenue: 0,
            vipPercentage: 0,
            standardPercentage: 0,
            newClients: 0,
            newClientsGrowth: 0,
            retentionRate: 0,
            activeClients: 0,
            avgRevenuePerClient: 0,
            purchaseFrequency: 0,
            busUtilization: 0,
            avgKilometers: 0,
            operationalCosts: 0,
            grossMargin: 0,
            marginRate: 0,
            netProfit: 0
        },
        revenueChartLoaded: false,
        seatChartLoaded: false,
        chartConfig: {
            revenue: { type: 'daily' }
        },
        topTrajets: [],
        topBuses: [],

        // ==================== ERREURS DE FORMULAIRE ====================
        formErrors: {
            bus: {},
            trajet: {},
            voyage: {},
            client: {},
            personnel: {}
        },

        // ==================== DONNÉES UI ====================
        statsFast: [
            { id: 1, label: 'CA Journalier', value: '1.2M FCFA' },
            { id: 2, label: 'Voyages Actifs', value: '8' },
            { id: 3, label: 'Taux Occupation', value: '78%' },
            { id: 4, label: 'Retards', value: '2' }
        ],
        kpis: [
            {
                id: 1,
                label: 'Voyages du jour',
                value: '24',
                trendUp: true,
                trendValue: '12%',
                icon: 'fa-solid fa-route',
                color: 'text-blue-600 bg-blue-100'
            },
            {
                id: 2,
                label: 'Billets vendus',
                value: '156',
                trendUp: true,
                trendValue: '8%',
                icon: 'fa-solid fa-ticket',
                color: 'text-emerald-600 bg-emerald-100'
            },
            {
                id: 3,
                label: 'Taux occupation',
                value: '78%',
                trendUp: false,
                trendValue: '3%',
                icon: 'fa-solid fa-chart-line',
                color: 'text-amber-600 bg-amber-100'
            },
            {
                id: 4,
                label: 'Revenus du jour',
                value: '1.2M FCFA',
                trendUp: true,
                trendValue: '15%',
                icon: 'fa-solid fa-money-bill-wave',
                color: 'text-purple-600 bg-purple-100'
            }
        ],
        recentVoyages: [],
        liveActivity: [],

        // ==================== INITIALISATION ====================
        async init() {
            console.log('🚀 Dashboard initialisé');

            // Initialiser les dates pour les rapports
            const today = new Date();
            const lastWeek = new Date(today);
            lastWeek.setDate(today.getDate() - 7);

            this.customDateStart = lastWeek.toISOString().split('T')[0];
            this.customDateEnd = today.toISOString().split('T')[0];

            // Charger les données réelles
            await this.fetchMe();
            await this.fetchDashboardStats(); // Charger d'abord les KPIs
            await this.fetchBuses();
            await this.fetchTrajets();
            await this.fetchVoyages();
            await this.fetchClients();
            await this.fetchTickets();
            await this.fetchPersonnel();

            // Initialiser les données filtrées pour l'embarquement
            this.filteredVoyagesList = [...this.voyagesProches];
            this.filteredPassagers = [...this.passagers];

            await this.loadReportData();

            console.log('✅ Dashboard prêt');
        },

        async fetchDashboardStats() {
            try {
                const response = await this.apiFetch('/admin/dashboard/stats');
                if (response.success) {
                    this.kpis = response.data.kpis.map((k, index) => ({ ...k, id: index + 1 }));
                    this.statsFast = response.data.stats_fast.map((s, index) => ({ ...s, id: index + 1 }));
                    this.recentVoyages = response.data.recent_voyages;
                    this.liveActivity = response.data.live_activity;
                    if (response.data.client_stats) {
                        this.clientStats = response.data.client_stats;
                    }

                    // Optionnel: Mettre à jour d'autres compteurs globaux
                    if (response.data.counts) {
                        this.totalClients = response.data.counts.total_clients;
                    }
                }
            } catch (error) {
                console.error('Erreur fetchDashboardStats:', error);
            }
        },

        async fetchMe() {
            try {
                const response = await this.apiFetch('/admin/me');
                if (response.success) {
                    const user = response.data;
                    this.userName = `${user.prenom} ${user.nom}`;
                    this.userPrenom = user.prenom;
                    this.userEmail = user.email;
                    this.userPhone = user.telephone || '';
                    this.userRole = user.role || 'admin';
                    this.sessionsCount = user.sessions_count || 1;

                    // Pré-remplir le formulaire de profil
                    this.profileForm = {
                        nom: user.nom,
                        prenom: user.prenom,
                        email: user.email,
                        telephone: user.telephone || ''
                    };
                }
            } catch (error) {
                console.error('Erreur fetchMe:', error);
            }
        },

        async fetchBuses() {
            try {
                const response = await this.apiFetch('/admin/bus');
                if (response.success) {
                    this.buses = response.data;
                }
            } catch (error) {
                console.error('Erreur fetchBuses:', error);
            }
        },

        async fetchTrajets() {
            try {
                const response = await this.apiFetch('/admin/trajets');
                if (response.success) {
                    this.trajets = response.data;
                }
            } catch (error) {
                console.error('Erreur fetchTrajets:', error);
            }
        },

        async fetchVoyages() {
            try {
                const response = await this.apiFetch('/admin/voyages');
                if (response.success) {
                    this.voyages = response.data;
                    // Mettre à jour les voyages proches pour l'embarquement
                    const now = new Date();
                    this.voyagesProches = this.voyages.filter(v =>
                        new Date(v.dateHeureDepart) > now &&
                        (new Date(v.dateHeureDepart) - now) < (24 * 60 * 60 * 1000)
                    );
                }
            } catch (error) {
                console.error('Erreur fetchVoyages:', error);
            }
        },

        async initDemoData() {
            // Cette méthode peut rester vide ou être supprimée car on utilise fetchBuses etc.
            console.log('Utilisation des données réelles au lieu du démo');
        },

        // ==================== MÉTHODES GÉNÉRALES ====================
        addToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => this.removeToast(id), 5000);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
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

        isAdmin() {
            return this.userRole === 'admin' || this.userRole === 'super_admin';
        },

        isGestionnaire() {
            return this.userRole === 'gestionnaire';
        },

        isControleur() {
            return this.userRole === 'controleur';
        },

        // ==================== FORMATAGE ====================
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

        formatTime(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        // ==================== MÉTHODES FORMULAIRES ====================
        async saveBus() {
            console.log('Enregistrement bus:', this.busForm);
            this.loading = true;
            this.formErrors.bus = {};

            try {
                const response = await this.apiFetch('/admin/bus', {
                    method: 'POST',
                    body: JSON.stringify(this.busForm)
                });

                if (response.success) {
                    this.addToast('Bus enregistré avec succès', 'success');
                    await this.fetchBuses();
                    this.showModal = null;
                    this.busForm = {
                        immatriculation: '',
                        statut: 'en_service',
                        marque: '',
                        modele: '',
                        capaciteTotale: 45,
                        nbSiegesVIP: 5,
                        dateMiseEnService: ''
                    };
                }
            } catch (error) {
                console.error('Erreur saveBus:', error);
                if (error.status === 422) {
                    this.formErrors.bus = error.data.errors;
                    this.addToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                } else {
                    this.addToast('Une erreur est survenue lors de l\'enregistrement', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        async saveTrajet() {
            console.log('Enregistrement trajet:', this.trajetForm);
            this.loading = true;
            this.formErrors.trajet = {};

            try {
                const url = this.editingTrajet ? `/admin/trajets/${this.editingTrajet.idTrajet}` : '/admin/trajets';
                const method = this.editingTrajet ? 'PUT' : 'POST';

                const response = await this.apiFetch(url, {
                    method: method,
                    body: JSON.stringify(this.trajetForm)
                });

                if (response.success) {
                    this.addToast(this.editingTrajet ? 'Trajet mis à jour avec succès' : 'Trajet enregistré avec succès', 'success');
                    await this.fetchTrajets();
                    this.showModal = null;
                    this.editingTrajet = null;
                    this.trajetForm = {
                        villeDepart: '',
                        villeArrivee: '',
                        prixStandard: 0,
                        prixVIP: 0,
                        distance: 0,
                        duree: 0
                    };
                }
            } catch (error) {
                console.error('Erreur saveTrajet:', error);
                if (error.status === 422) {
                    this.formErrors.trajet = error.data.errors;
                    this.addToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                } else {
                    this.addToast('Une erreur est survenue lors de l\'enregistrement', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        async saveVoyage() {
            console.log('Enregistrement voyage:', this.voyageForm);
            this.loading = true;
            this.formErrors.voyage = {};

            try {
                const url = this.editingVoyage ? `/admin/voyages/${this.editingVoyage.idVoyage}` : '/admin/voyages';
                const method = this.editingVoyage ? 'PUT' : 'POST';

                const response = await this.apiFetch(url, {
                    method: method,
                    body: JSON.stringify(this.voyageForm)
                });

                if (response.success) {
                    this.addToast(this.editingVoyage ? 'Voyage mis à jour avec succès' : 'Voyage planifié avec succès', 'success');
                    await this.fetchVoyages();
                    this.showModal = null;
                    this.editingVoyage = null;
                    this.voyageForm = {
                        idTrajet: '',
                        idBus: '',
                        dateHeureDepart: '',
                        prixStandard: 0,
                        prixVIP: 0
                    };
                }
            } catch (error) {
                console.error('Erreur saveVoyage:', error);
                if (error.status === 422) {
                    this.formErrors.voyage = error.data.errors;
                    this.addToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                } else {
                    this.addToast(error.data?.message || 'Une erreur est survenue lors de l\'enregistrement', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        async saveClient() {
            console.log('Enregistrement client:', this.clientForm);
            this.loading = true;
            this.formErrors.client = {};

            try {
                const url = this.editingClient ? `/admin/clients/${this.editingClient.id_client}` : '/admin/clients';
                const method = this.editingClient ? 'PUT' : 'POST';

                const response = await this.apiFetch(url, {
                    method: method,
                    body: JSON.stringify(this.clientForm)
                });

                if (response.success) {
                    this.addToast(this.editingClient ? 'Client mis à jour avec succès' : 'Client enregistré avec succès', 'success');
                    await this.fetchClients();
                    this.showModal = null;
                    this.editingClient = null;
                    this.clientForm = {
                        prenom: '',
                        nom: '',
                        email: '',
                        telephone: '',
                        motDePasse: '',
                        ville: '',
                        statut: 'actif',
                        estVerifie: false
                    };
                }
            } catch (error) {
                console.error('Erreur saveClient:', error);
                if (error.status === 422) {
                    this.formErrors.client = error.data.errors;
                    this.addToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                } else {
                    this.addToast('Une erreur est survenue lors de l\'enregistrement', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        async savePersonnel() {
            console.log('Enregistrement personnel:', this.personnelForm);
            this.loading = true;
            this.formErrors.personnel = {};

            try {
                const url = this.editingPersonnel ? `/admin/users/${this.editingPersonnel.id}` : '/admin/users';
                const method = this.editingPersonnel ? 'PUT' : 'POST';

                const response = await this.apiFetch(url, {
                    method: method,
                    body: JSON.stringify(this.personnelForm)
                });

                if (response.success) {
                    this.addToast(this.editingPersonnel ? 'Membre mis à jour avec succès' : 'Membre créé avec succès', 'success');
                    await this.fetchPersonnel();
                    this.showModal = null;
                    this.editingPersonnel = null;
                    this.personnelForm = {
                        prenom: '',
                        nom: '',
                        email: '',
                        telephone: '',
                        password: '',
                        role: 'admin',
                        poste: '',
                        statut: 'actif'
                    };
                }
            } catch (error) {
                console.error('Erreur savePersonnel:', error);
                if (error.status === 422) {
                    this.formErrors.personnel = error.data.errors;
                    this.addToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                } else {
                    this.addToast('Une erreur est survenue lors de l\'enregistrement', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        // ==================== MÉTHODES ÉDITION ====================
        editVoyage(voyage) {
            this.editingVoyage = voyage;
            this.voyageForm = {
                idTrajet: voyage.trajet?.idTrajet || '',
                idBus: voyage.bus?.idBus || '',
                dateHeureDepart: voyage.dateHeureDepart,
                prixStandard: voyage.prixStandard || 0,
                prixVIP: voyage.prixVIP || 0
            };
            this.showModal = 'voyage';
        },

        editTrajet(trajet) {
            this.editingTrajet = trajet;

            // Convertir HH:mm:ss en minutes
            let totalMinutes = 0;
            if (trajet.dureeEstimee) {
                const parts = trajet.dureeEstimee.split(':');
                totalMinutes = parseInt(parts[0]) * 60 + parseInt(parts[1]);
            }

            this.trajetForm = {
                villeDepart: trajet.villeDepart,
                villeArrivee: trajet.villeArrivee,
                prixStandard: trajet.prixStandard || trajet.prixBase || 0,
                prixVIP: trajet.prixVIP || 0,
                distance: trajet.distance || 0,
                duree: totalMinutes
            };
            this.showModal = 'trajet';
        },

        editClient(client) {
            this.editingClient = client;
            this.clientForm = {
                prenom: client.prenom,
                nom: client.nom,
                email: client.email,
                telephone: client.telephone,
                motDePasse: '',
                ville: client.ville,
                statut: client.statut,
                estVerifie: client.estVerifie === 1
            };
            this.showModal = 'client';
        },

        editPersonnel(employe) {
            this.editingPersonnel = employe;
            this.personnelForm = {
                prenom: employe.prenom,
                nom: employe.nom,
                email: employe.email,
                telephone: employe.telephone,
                role: employe.role,
                poste: employe.poste,
                salaire: employe.salaire,
                dateEmbauche: employe.dateEmbauche,
                motDePasse: '',
                statut: employe.statut,
                matricule: employe.matricule,
                permissions: [...employe.permissions]
            };
            this.showModal = 'personnel';
        },

        // ==================== MÉTHODES SUPPRESSION ====================
        async confirmDeleteVoyage(voyage) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le voyage ${voyage.trajet?.villeDepart} → ${voyage.trajet?.villeArrivee} ?`)) {
                try {
                    const response = await this.apiFetch(`/admin/voyages/${voyage.idVoyage}`, {
                        method: 'DELETE'
                    });
                    if (response.success) {
                        this.addToast('Voyage supprimé avec succès', 'success');
                        await this.fetchVoyages();
                    }
                } catch (error) {
                    console.error('Erreur deleteVoyage:', error);
                    this.addToast('Erreur lors de la suppression', 'error');
                }
            }
        },

        async deleteTrajet(trajet) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'itinéraire ${trajet.villeDepart} → ${trajet.villeArrivee} ?`)) {
                try {
                    const response = await this.apiFetch(`/admin/trajets/${trajet.idTrajet}`, {
                        method: 'DELETE'
                    });
                    if (response.success) {
                        this.addToast('Itinéraire supprimé avec succès', 'success');
                        await this.fetchTrajets();
                    }
                } catch (error) {
                    console.error('Erreur deleteTrajet:', error);
                    this.addToast('Erreur lors de la suppression', 'error');
                }
            }
        },

        async deleteClient(client) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le client ${client.prenom} ${client.nom} ?`)) {
                try {
                    const response = await this.apiFetch(`/admin/clients/${client.id_client}`, {
                        method: 'DELETE'
                    });
                    if (response.success) {
                        this.addToast('Client supprimé avec succès', 'success');
                        await this.fetchClients();
                    }
                } catch (error) {
                    console.error('Erreur deleteClient:', error);
                    this.addToast('Erreur lors de la suppression', 'error');
                }
            }
        },

        async deletePersonnel(employe) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${employe.prenom} ${employe.nom} ?`)) {
                try {
                    const response = await this.apiFetch(`/admin/users/${employe.id}`, {
                        method: 'DELETE'
                    });
                    if (response.success) {
                        this.addToast('Membre supprimé avec succès', 'success');
                        await this.fetchPersonnel();
                    }
                } catch (error) {
                    console.error('Erreur deletePersonnel:', error);
                    this.addToast('Erreur lors de la suppression', 'error');
                }
            }
        },

        async annulerVoyage(voyage) {
            if (confirm(`Êtes-vous sûr de vouloir annuler ce voyage ?`)) {
                try {
                    const response = await this.apiFetch(`/admin/voyages/${voyage.idVoyage}/annuler`, {
                        method: 'POST'
                    });
                    if (response.success) {
                        this.addToast('Voyage annulé avec succès', 'success');
                        await this.fetchVoyages();
                    }
                } catch (error) {
                    console.error('Erreur annulerVoyage:', error);
                    this.addToast('Erreur lors de l\'annulation', 'error');
                }
            }
        },

        // ==================== MÉTHODES PAGINATION ====================
        getTicketPages() {
            if (!this.tickets || this.tickets.length === 0) {
                this.ticketPages = [1];
                this.totalTicketPages = 1;
                return;
            }
            this.totalTicketPages = Math.ceil(this.tickets.length / this.ticketsPerPage);
            this.ticketPages = Array.from({ length: this.totalTicketPages }, (_, i) => i + 1);
        },

        getClientPages() {
            if (!this.clients || this.clients.length === 0) {
                this.clientPages = [1];
                this.totalClientPages = 1;
                return;
            }
            this.totalClientPages = Math.ceil(this.clients.length / this.clientsPerPage);
            this.clientPages = Array.from({ length: this.totalClientPages }, (_, i) => i + 1);
        },

        getPersonnelPages() {
            console.log('Calcul pages personnel:', this.personnel?.length);

            if (!this.personnel || this.personnel.length === 0) {
                this.personnelPages = [1];
                this.totalPersonnelPages = 1;
                console.log('Aucun personnel - pages:', this.personnelPages);
                return;
            }

            this.totalPersonnelPages = Math.ceil(this.totalPersonnel / this.personnelPerPage);
            this.personnelPages = Array.from({ length: this.totalPersonnelPages }, (_, i) => i + 1);

            console.log('Pages calculées:', this.personnelPages, 'Total:', this.totalPersonnelPages);
        },

        prevTicketPage() {
            if (this.currentTicketPage > 1) {
                this.currentTicketPage--;
            }
        },

        nextTicketPage() {
            if (this.currentTicketPage < this.totalTicketPages) {
                this.currentTicketPage++;
            }
        },

        prevClientPage() {
            if (this.currentClientPage > 1) {
                this.currentClientPage--;
            }
        },

        nextClientPage() {
            if (this.currentClientPage < this.totalClientPages) {
                this.currentClientPage++;
            }
        },

        prevPersonnelPage() {
            if (this.currentPersonnelPage > 1) {
                this.currentPersonnelPage--;
                console.log('Page précédente:', this.currentPersonnelPage);
            }
        },

        nextPersonnelPage() {
            if (this.totalPersonnelPages && this.currentPersonnelPage < this.totalPersonnelPages) {
                this.currentPersonnelPage++;
                console.log('Page suivante:', this.currentPersonnelPage);
            }
        },

        // ==================== MÉTHODES SELECTION ====================
        toggleAllClients() {
            if (this.selectAllClients) {
                this.selectedClients = this.clients.map(c => c.id_client);
            } else {
                this.selectedClients = [];
            }
        },

        toggleAllTickets() {
            if (this.selectAllTickets) {
                this.selectedTickets = this.tickets.map(t => t.idTicket);
            } else {
                this.selectedTickets = [];
            }
        },

        toggleAllPersonnel() {
            if (this.selectAllPersonnel) {
                this.selectedPersonnel = this.personnel.map(p => p.id);
            } else {
                this.selectedPersonnel = [];
            }
        },

        // ==================== MÉTHODES CLIENTS ====================
        async fetchClients() {
            this.loadingClients = true;
            try {
                const response = await this.apiFetch('/admin/clients');
                if (response.success) {
                    this.clients = response.data;
                    this.totalClients = this.clients.length;
                    this.getClientPages();
                }
            } catch (error) {
                console.error('Erreur chargement clients:', error);
                this.addToast('Erreur lors du chargement des clients', 'error');
            } finally {
                this.loadingClients = false;
            }
        },

        filteredClients() {
            if (!this.clients) return [];

            let filtered = [...this.clients];

            // Filtre par recherche
            if (this.clientSearchQuery) {
                const query = this.clientSearchQuery.toLowerCase();
                filtered = filtered.filter(client =>
                    client.prenom.toLowerCase().includes(query) ||
                    client.nom.toLowerCase().includes(query) ||
                    client.email.toLowerCase().includes(query) ||
                    client.telephone.includes(query) ||
                    client.codeClient.toLowerCase().includes(query)
                );
            }

            // Filtre par statut
            if (this.clientStatusFilter !== 'all') {
                if (this.clientStatusFilter === 'verifie') {
                    filtered = filtered.filter(client => client.estVerifie === 1);
                } else if (this.clientStatusFilter === 'non_verifie') {
                    filtered = filtered.filter(client => client.estVerifie === 0);
                } else {
                    filtered = filtered.filter(client => client.statut === this.clientStatusFilter);
                }
            }

            // Pagination
            const start = (this.currentClientPage - 1) * this.clientsPerPage;
            const end = start + this.clientsPerPage;

            return filtered.slice(start, end);
        },

        debouncedClientSearch() {
            if (this.clientSearchTimeout) {
                clearTimeout(this.clientSearchTimeout);
            }
            this.clientSearchTimeout = setTimeout(() => {
                this.fetchClients();
            }, 300);
        },

        viewClientDetails(client) {
            console.log('Voir détails client:', client);
            this.addToast(`Affichage du profil de ${client.prenom} ${client.nom}`, 'info');
        },

        verifyClient(client) {
            client.estVerifie = 1;
            this.addToast('Client vérifié avec succès', 'success');
        },

        toggleClientStatus(client) {
            client.statut = client.statut === 'actif' ? 'inactif' : 'actif';
            const status = client.statut === 'actif' ? 'activé' : 'désactivé';
            this.addToast(`Client ${status}`, 'success');
        },

        viewClientHistory(client) {
            console.log('Voir historique client:', client);
            this.addToast(`Affichage de l'historique de ${client.prenom} ${client.nom}`, 'info');
        },

        // ==================== MÉTHODES BILLETS ====================
        async fetchTickets() {
            this.loadingTickets = true;
            try {
                const response = await this.apiFetch('/admin/tickets');
                if (response.success) {
                    // Si c'est paginé par Laravel, les données sont dans response.data.data
                    this.tickets = Array.isArray(response.data) ? response.data : (response.data.data || []);
                    this.totalTickets = response.data.total || this.tickets.length;
                    this.totalTicketPages = response.data.last_page || Math.ceil(this.totalTickets / this.ticketsPerPage);
                    this.getTicketPages();
                }
            } catch (error) {
                console.error('Erreur chargement tickets:', error);
                this.addToast('Erreur lors du chargement des billets', 'error');
            } finally {
                this.loadingTickets = false;
            }
        },

        filteredTickets() {
            if (!this.tickets) return [];

            let filtered = [...this.tickets];

            // Filtre par recherche
            if (this.ticketSearchQuery) {
                const query = this.ticketSearchQuery.toLowerCase();
                filtered = filtered.filter(ticket =>
                    ticket.codeBillet.toLowerCase().includes(query) ||
                    (ticket.client?.nom + ' ' + ticket.client?.prenom).toLowerCase().includes(query) ||
                    (ticket.voyage?.trajet?.villeDepart + ' → ' + ticket.voyage?.trajet?.villeArrivee).toLowerCase().includes(query)
                );
            }

            // Filtre par statut
            if (this.ticketStatusFilter !== 'all') {
                filtered = filtered.filter(ticket => ticket.statut === this.ticketStatusFilter);
            }

            // Filtre par classe
            if (this.ticketClassFilter !== 'all') {
                filtered = filtered.filter(ticket => ticket.classeBillet === this.ticketClassFilter);
            }

            // Pagination
            const start = (this.currentTicketPage - 1) * this.ticketsPerPage;
            const end = start + this.ticketsPerPage;

            return filtered.slice(start, end);
        },

        debouncedSearch() {
            if (this.ticketSearchTimeout) {
                clearTimeout(this.ticketSearchTimeout);
            }
            this.ticketSearchTimeout = setTimeout(() => {
                this.fetchTickets();
            }, 300);
        },

        viewTicketDetails(ticket) {
            console.log('Voir détails billet:', ticket);
            this.addToast(`Affichage des détails du billet ${ticket.codeBillet}`, 'info');
        },

        confirmTicket(ticket) {
            ticket.statut = 'confirme';
            this.addToast('Billet confirmé avec succès', 'success');
        },

        markTicketAsUsed(ticket) {
            ticket.statut = 'utilise';
            this.addToast('Billet marqué comme utilisé', 'success');
        },

        cancelTicket(ticket) {
            ticket.statut = 'annule';
            this.addToast('Billet annulé', 'warning');
        },

        printTicket(ticket) {
            console.log('Impression billet:', ticket);
            this.addToast(`Impression du billet ${ticket.codeBillet}`, 'info');
        },

        deleteTicket(ticket) {
            if (confirm(`Supprimer le billet ${ticket.codeBillet} ?`)) {
                this.tickets = this.tickets.filter(t => t.idTicket !== ticket.idTicket);
                this.addToast('Billet supprimé', 'success');
            }
        },

        formatTimeRemaining(dateString) {
            if (!dateString) return '';
            const now = new Date();
            const date = new Date(dateString);
            const diffHours = Math.floor((date - now) / (1000 * 60 * 60));
            if (diffHours < 1) return 'Moins d\'1h';
            return `${diffHours}h`;
        },

        getTicketStatusClass(status) {
            const classes = {
                'reserve': 'bg-amber-100 text-amber-700',
                'confirme': 'bg-emerald-100 text-emerald-700',
                'annule': 'bg-red-100 text-red-700',
                'utilise': 'bg-blue-100 text-blue-700',
                'en_attente': 'bg-slate-100 text-slate-700'
            };
            return classes[status] || 'bg-gray-100 text-gray-700';
        },

        getTicketStatusText(status) {
            const texts = {
                'reserve': 'Réservé',
                'confirme': 'Confirmé',
                'annule': 'Annulé',
                'utilise': 'Utilisé',
                'en_attente': 'En attente'
            };
            return texts[status] || status;
        },

        getTicketClassColor(classe) {
            const colors = {
                'vip': 'bg-purple-100 text-purple-700',
                'standard': 'bg-blue-100 text-blue-700'
            };
            return colors[classe] || 'bg-gray-100 text-gray-700';
        },

        getTicketClassText(classe) {
            return classe === 'vip' ? 'VIP' : 'Standard';
        },

        getClientStatusClass(status) {
            switch (status) {
                case 'actif': return 'bg-emerald-100 text-emerald-700';
                case 'inactif': return 'bg-slate-100 text-slate-700';
                default: return 'bg-gray-100 text-gray-700';
            }
        },

        getClientStatusText(status) {
            switch (status) {
                case 'actif': return 'Actif';
                case 'inactif': return 'Inactif';
                default: return status;
            }
        },

        // ==================== MÉTHODES PERSONNEL ====================
        async fetchPersonnel() {
            this.loadingPersonnel = true;
            try {
                const response = await this.apiFetch('/admin/users');
                if (response.success) {
                    this.personnel = response.data;
                    this.totalPersonnel = this.personnel.length;
                    this.getPersonnelPages();
                    this.updatePersonnelStats();
                }
            } catch (error) {
                console.error('Erreur chargement personnel:', error);
                this.addToast('Erreur lors du chargement du personnel', 'error');
            } finally {
                this.loadingPersonnel = false;
            }
        },

        filteredPersonnel() {
            if (!this.personnel) return [];

            let filtered = [...this.personnel];

            // Filtre par recherche
            if (this.personnelSearchQuery) {
                const query = this.personnelSearchQuery.toLowerCase();
                filtered = filtered.filter(employe =>
                    employe.prenom.toLowerCase().includes(query) ||
                    employe.nom.toLowerCase().includes(query) ||
                    employe.email.toLowerCase().includes(query) ||
                    employe.matricule.toLowerCase().includes(query) ||
                    employe.poste.toLowerCase().includes(query)
                );
            }

            // Filtre par rôle
            if (this.personnelRoleFilter !== 'all') {
                filtered = filtered.filter(employe => employe.role === this.personnelRoleFilter);
            }

            // Filtre par statut
            if (this.personnelStatusFilter !== 'all') {
                filtered = filtered.filter(employe => employe.statut === this.personnelStatusFilter);
            }

            // Pagination
            const start = (this.currentPersonnelPage - 1) * this.personnelPerPage;
            const end = start + this.personnelPerPage;

            return filtered.slice(start, end);
        },

        debouncedPersonnelSearch() {
            if (this.personnelSearchTimeout) {
                clearTimeout(this.personnelSearchTimeout);
            }
            this.personnelSearchTimeout = setTimeout(() => {
                this.fetchPersonnel();
            }, 300);
        },

        viewPersonnelDetails(employe) {
            console.log('Voir détails personnel:', employe);
            this.addToast(`Affichage du profil de ${employe.prenom} ${employe.nom}`, 'info');
        },

        togglePersonnelStatus(employe) {
            employe.statut = employe.statut === 'actif' ? 'inactif' : 'actif';
            const status = employe.statut === 'actif' ? 'activé' : 'désactivé';
            this.addToast(`Membre ${status}`, 'success');
        },

        resetPassword(employe) {
            if (confirm(`Réinitialiser le mot de passe de ${employe.prenom} ${employe.nom} ?`)) {
                // En production, vous enverriez un email de réinitialisation
                this.addToast('Email de réinitialisation envoyé', 'success');
            }
        },

        getRoleClass(role) {
            const classes = {
                'super_admin': 'bg-purple-100 text-purple-700',
                'admin': 'bg-red-100 text-red-700',
                'gestionnaire': 'bg-blue-100 text-blue-700',
                'controleur': 'bg-amber-100 text-amber-700',
                'conducteur': 'bg-green-100 text-green-700'
            };
            return classes[role] || 'bg-gray-100 text-gray-700';
        },

        getRoleText(role) {
            const texts = {
                'super_admin': 'Super Admin',
                'admin': 'Administrateur',
                'gestionnaire': 'Gestionnaire',
                'controleur': 'Contrôleur',
                'conducteur': 'Conducteur'
            };
            return texts[role] || role;
        },

        getPersonnelStatusClass(status) {
            const classes = {
                'actif': 'bg-emerald-100 text-emerald-700',
                'inactif': 'bg-slate-100 text-slate-700',
                'vacances': 'bg-blue-100 text-blue-700',
                'maladie': 'bg-orange-100 text-orange-700'
            };
            return classes[status] || 'bg-gray-100 text-gray-700';
        },

        getPersonnelStatusText(status) {
            const texts = {
                'actif': 'Actif',
                'inactif': 'Inactif',
                'vacances': 'Vacances',
                'maladie': 'Maladie'
            };
            return texts[status] || status;
        },

        updatePersonnelStats() {
            const stats = {
                total: this.personnel.length,
                actifs: this.personnel.filter(p => p.statut === 'actif').length,
                admins: this.personnel.filter(p => p.role === 'admin' || p.role === 'super_admin').length,
                gestionnaires: this.personnel.filter(p => p.role === 'gestionnaire').length,
                controleurs: this.personnel.filter(p => p.role === 'controleur').length,
                conducteurs: this.personnel.filter(p => p.role === 'conducteur').length
            };

            stats.actifsPourcentage = stats.total > 0 ? Math.round((stats.actifs / stats.total) * 100) : 0;

            this.personnelStats = stats;
        },

        // ==================== MÉTHODES BATCH ====================
        batchConfirmTickets() {
            const count = this.selectedTickets.length;
            this.tickets.forEach(ticket => {
                if (this.selectedTickets.includes(ticket.idTicket)) {
                    ticket.statut = 'confirme';
                }
            });
            this.addToast(`${count} billets confirmés`, 'success');
            this.selectedTickets = [];
        },

        batchCancelTickets() {
            const count = this.selectedTickets.length;
            this.tickets.forEach(ticket => {
                if (this.selectedTickets.includes(ticket.idTicket)) {
                    ticket.statut = 'annule';
                }
            });
            this.addToast(`${count} billets annulés`, 'warning');
            this.selectedTickets = [];
        },

        exportTickets() {
            console.log('Export tickets:', this.selectedTickets);
            this.addToast('Export des billets en cours...', 'info');
        },

        batchVerifyClients() {
            const count = this.selectedClients.length;
            this.clients.forEach(client => {
                if (this.selectedClients.includes(client.id_client)) {
                    client.estVerifie = 1;
                }
            });
            this.addToast(`${count} clients vérifiés`, 'success');
            this.selectedClients = [];
        },

        batchActivateClients() {
            const count = this.selectedClients.length;
            this.clients.forEach(client => {
                if (this.selectedClients.includes(client.id_client)) {
                    client.statut = 'actif';
                }
            });
            this.addToast(`${count} clients activés`, 'success');
            this.selectedClients = [];
        },

        exportClients() {
            console.log('Export clients:', this.selectedClients);
            this.addToast('Export des clients en cours...', 'info');
        },

        batchActivatePersonnel() {
            const count = this.selectedPersonnel.length;
            this.personnel.forEach(employe => {
                if (this.selectedPersonnel.includes(employe.id)) {
                    employe.statut = 'actif';
                }
            });
            this.addToast(`${count} membres activés`, 'success');
            this.selectedPersonnel = [];
        },

        batchDeactivatePersonnel() {
            const count = this.selectedPersonnel.length;
            this.personnel.forEach(employe => {
                if (this.selectedPersonnel.includes(employe.id)) {
                    employe.statut = 'inactif';
                }
            });
            this.addToast(`${count} membres désactivés`, 'warning');
            this.selectedPersonnel = [];
        },

        exportPersonnel() {
            console.log('Export personnel:', this.selectedPersonnel);
            this.addToast('Export du personnel en cours...', 'info');
        },

        // ==================== MÉTHODES EMBAQUEMENT ====================
        initEmbarquement() {
            console.log('🎯 Initialisation embarquement');

            // Initialiser les données filtrées
            this.filteredVoyagesList = [...this.voyagesProches];
            this.filteredPassagers = [...this.passagers];

            // Sélectionner le premier voyage par défaut
            if (this.voyagesProches.length > 0 && !this.selectedVoyage) {
                this.selectedVoyage = this.voyagesProches[0];
            }

            // Mettre à jour les stats
            this.updateEmbarquementStats();

            console.log('✅ Embarquement initialisé');
        },

        updateEmbarquementStats() {
            const stats = { aEmbarquer: 0, embarques: 0, retards: 0, absents: 0 };

            this.passagers.forEach(p => {
                switch (p.statutEmbarquement) {
                    case 'a_embarquer': stats.aEmbarquer++; break;
                    case 'embarque': stats.embarques++; break;
                    case 'retard': stats.retards++; break;
                    case 'absent': stats.absents++; break;
                }
            });

            this.embarquementStats = stats;
        },

        filterPassagers() {
            if (!this.passagers.length) {
                this.filteredPassagers = [];
                return;
            }

            let filtered = [...this.passagers];

            if (this.passagerFilter !== 'all') {
                filtered = filtered.filter(p => p.statutEmbarquement === this.passagerFilter);
            }

            if (this.passagerClassFilter !== 'all') {
                filtered = filtered.filter(p => p.typeSiege === this.passagerClassFilter);
            }

            this.filteredPassagers = filtered;
        },

        filterVoyages() {
            if (!this.voyageSearch.trim()) {
                this.filteredVoyagesList = [...this.voyagesProches];
                return;
            }

            const search = this.voyageSearch.toLowerCase();
            this.filteredVoyagesList = this.voyagesProches.filter(v =>
                v.trajet.villeDepart.toLowerCase().includes(search) ||
                v.trajet.villeArrivee.toLowerCase().includes(search) ||
                v.bus.immatriculation.toLowerCase().includes(search)
            );
        },

        selectVoyageEmbarquement(voyage) {
            this.selectedVoyage = voyage;
            this.addToast(`Voyage sélectionné: ${voyage.trajet.villeDepart} → ${voyage.trajet.villeArrivee}`, 'info');
        },

        scanBillet() {
            if (!this.scanInput.trim()) {
                this.addToast('Veuillez entrer un code billet', 'warning');
                return;
            }

            this.loading = true;

            setTimeout(() => {
                this.scanFeedback = {
                    message: 'Billet validé avec succès !',
                    type: 'success',
                    details: `Code: ${this.scanInput} - Statut: Validé`
                };

                this.addToast('Embarquement validé', 'success');

                const passager = this.passagers.find(p => p.statutEmbarquement === 'a_embarquer');
                if (passager) {
                    passager.statutEmbarquement = 'embarque';
                    passager.dateEmbarquement = new Date().toISOString();
                    this.filterPassagers();
                    this.updateEmbarquementStats();
                }

                this.scanInput = '';
                this.loading = false;
            }, 1000);
        },

        clearScanFeedback() {
            this.scanFeedback = { message: '', type: '', details: '' };
        },

        openScannerModal() {
            this.showScannerModal = true;
        },

        simulateQRScan() {
            this.showScannerModal = false;
            this.scanInput = 'BR-' + Math.floor(100000 + Math.random() * 900000);

            setTimeout(() => {
                this.scanBillet();
            }, 300);
        },

        validerEmbarquement(passager) {
            passager.statutEmbarquement = 'embarque';
            passager.dateEmbarquement = new Date().toISOString();
            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast(`${passager.client.prenom} ${passager.client.nom} embarqué`, 'success');
        },

        annulerEmbarquement(passager) {
            passager.statutEmbarquement = 'a_embarquer';
            passager.dateEmbarquement = null;
            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast('Embarquement annulé', 'warning');
        },

        marquerRetard(passager) {
            passager.statutEmbarquement = 'retard';
            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast('Passager marqué en retard', 'warning');
        },

        marquerAbsent(passager) {
            passager.statutEmbarquement = 'absent';
            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast('Passager marqué absent', 'error');
        },

        validerTousEmbarquements() {
            if (!confirm('Valider tous les passagers à embarquer ?')) return;

            let count = 0;
            this.passagers.forEach(p => {
                if (p.statutEmbarquement === 'a_embarquer') {
                    p.statutEmbarquement = 'embarque';
                    p.dateEmbarquement = new Date().toISOString();
                    count++;
                }
            });

            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast(`${count} passagers embarqués`, 'success');
        },

        annulerTousEmbarquements() {
            if (!confirm('Annuler tous les embarquements ?')) return;

            let count = 0;
            this.passagers.forEach(p => {
                if (p.statutEmbarquement === 'embarque') {
                    p.statutEmbarquement = 'a_embarquer';
                    p.dateEmbarquement = null;
                    count++;
                }
            });

            this.filterPassagers();
            this.updateEmbarquementStats();
            this.addToast(`${count} embarquements annulés`, 'warning');
        },

        getPassagerStatusClass(passager) {
            const classes = {
                'a_embarquer': 'bg-gray-100 text-gray-600',
                'embarque': 'bg-emerald-100 text-emerald-600',
                'retard': 'bg-amber-100 text-amber-600',
                'absent': 'bg-red-100 text-red-600'
            };
            return classes[passager.statutEmbarquement] || 'bg-gray-100 text-gray-600';
        },

        getPassagerStatusIcon(passager) {
            const icons = {
                'a_embarquer': 'fa-solid fa-clock',
                'embarque': 'fa-solid fa-check',
                'retard': 'fa-solid fa-exclamation',
                'absent': 'fa-solid fa-user-slash'
            };
            return icons[passager.statutEmbarquement] || 'fa-solid fa-question';
        },

        getDepartureStatusClass(voyage) {
            if (!voyage || !voyage.dateHeureDepart) return 'bg-gray-500/20 text-gray-300';

            const now = new Date();
            const departure = new Date(voyage.dateHeureDepart);
            const diffMinutes = (departure - now) / (1000 * 60);

            if (diffMinutes < 0) return 'bg-red-500/20 text-red-300';
            if (diffMinutes < 30) return 'bg-amber-500/20 text-amber-300';
            return 'bg-emerald-500/20 text-emerald-300';
        },

        getDepartureStatusText(voyage) {
            if (!voyage || !voyage.dateHeureDepart) return 'N/A';

            const now = new Date();
            const departure = new Date(voyage.dateHeureDepart);
            const diffMinutes = (departure - now) / (1000 * 60);

            if (diffMinutes < 0) return 'départé';
            if (diffMinutes < 30) return `${Math.round(diffMinutes)} min`;
            return `${Math.round(diffMinutes / 60)}h`;
        },

        getTimeUntilDeparture(voyage) {
            if (!voyage || !voyage.dateHeureDepart) return '';

            const now = new Date();
            const departure = new Date(voyage.dateHeureDepart);
            const diffMinutes = Math.floor((departure - now) / (1000 * 60));

            if (diffMinutes < 0) return 'Départé';
            if (diffMinutes < 60) return `${diffMinutes} minutes`;

            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;
            return `${hours}h${minutes > 0 ? ` ${minutes}min` : ''}`;
        },

        voirBillet(passager) {
            console.log('Voir billet:', passager);
            this.addToast(`Affichage du billet ${passager.codeBillet}`, 'info');
        },

        imprimerBillet(passager) {
            console.log('Imprimer billet:', passager);
            this.addToast(`Impression du billet ${passager.codeBillet}`, 'info');
        },

        exportListeEmbarquement() {
            console.log('Export liste embarquement');
            this.addToast('Export de la liste d\'embarquement en cours...', 'info');
        },

        getVoyageStatusClass(voyage) {
            if (!voyage || !voyage.dateHeureDepart) return 'bg-gray-100 text-gray-700';

            const now = new Date();
            const departure = new Date(voyage.dateHeureDepart);
            const diffMinutes = (departure - now) / (1000 * 60);

            if (diffMinutes < 0) return 'bg-red-100 text-red-700';
            if (diffMinutes < 30) return 'bg-amber-100 text-amber-700';
            return 'bg-emerald-100 text-emerald-700';
        },

        getVoyageStatusText(voyage) {
            if (!voyage || !voyage.dateHeureDepart) return 'N/A';

            const now = new Date();
            const departure = new Date(voyage.dateHeureDepart);
            const diffMinutes = (departure - now) / (1000 * 60);

            if (diffMinutes < 0) return 'Terminé';
            if (diffMinutes < 30) return 'Départ imminent';
            return 'À venir';
        },

        // ==================== MÉTHODES PROFIL ====================
        async updateProfile() {
            this.loading = true;
            try {
                const response = await this.apiFetch('/admin/profile/update', {
                    method: 'POST',
                    body: JSON.stringify(this.profileForm)
                });

                if (response.success) {
                    const user = response.user;
                    this.userPrenom = user.prenom;
                    this.userName = `${user.prenom} ${user.nom}`;
                    this.userEmail = user.email;
                    this.userPhone = user.telephone;
                    this.editingProfile = false;
                    this.addToast('Profil mis à jour avec succès', 'success');
                }
            } catch (error) {
                console.error('Erreur updateProfile:', error);
                this.addToast('Erreur lors de la mise à jour du profil', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ==================== MÉTHODES RAPPORTS ====================
        async loadReportData() {
            this.loading = true;

            try {
                console.log('📊 Chargement des données pour la période:', this.reportPeriod);

                // Fetch top trajets
                const trajetsRes = await this.apiFetch('/admin/reports/top-trajets');
                if (trajetsRes.success) {
                    this.topTrajets = trajetsRes.data.map(t => ({
                        id: t.idTrajet,
                        route: `${t.villeDepart} → ${t.villeArrivee}`,
                        distance: t.distance,
                        voyages: t.total_voyages,
                        tickets: t.total_tickets,
                        revenue: t.total_revenue,
                        occupancy: 0, // À calculer ?
                        performance: t.total_revenue > 1000000 ? 'excellent' : 'good'
                    }));
                }

                // Fetch top buses
                const busesRes = await this.apiFetch('/admin/reports/top-buses');
                if (busesRes.success) {
                    this.topBuses = busesRes.data.map(b => ({
                        id: b.idBus,
                        immatriculation: b.immatriculation,
                        model: `${b.marque} ${b.modele}`,
                        revenue: b.total_revenue,
                        occupancy: 0 // À calculer ?
                    }));
                }

                this.addToast('Données statistiques chargées', 'success');

            } catch (error) {
                console.error('Erreur chargement rapports:', error);
                this.addToast('Erreur lors du chargement des statistiques', 'error');
            } finally {
                this.loading = false;
            }
        },

        generateMockReportData() {
            // Données simulées pour démonstration
            const stats = {
                totalRevenue: 12500000,
                revenueTrend: 15.5,
                totalTickets: 842,
                ticketsTrend: 8.2,
                totalVoyages: 156,
                completedVoyages: 142,
                occupancyRate: 78.5,
                vipTickets: 210,
                standardTickets: 632,
                vipRevenue: 5250000,
                standardRevenue: 7250000,
                vipPercentage: 25,
                standardPercentage: 75,
                newClients: 48,
                newClientsGrowth: 32,
                retentionRate: 85.3,
                activeClients: 325,
                avgRevenuePerClient: 38462,
                purchaseFrequency: 2.4,
                busUtilization: 76.8,
                avgKilometers: 285,
                operationalCosts: 8500000,
                grossMargin: 4000000,
                marginRate: 32,
                netProfit: 3200000
            };

            const topTrajets = [
                {
                    id: 1,
                    route: 'Dakar → Thiès',
                    distance: 70,
                    voyages: 45,
                    tickets: 380,
                    vip: 95,
                    standard: 285,
                    revenue: 4750000,
                    averageRevenue: 105556,
                    occupancy: 84.5,
                    performance: 'excellent'
                },
                {
                    id: 2,
                    route: 'Dakar → Saint-Louis',
                    distance: 260,
                    voyages: 32,
                    tickets: 268,
                    vip: 67,
                    standard: 201,
                    revenue: 4020000,
                    averageRevenue: 125625,
                    occupancy: 83.8,
                    performance: 'excellent'
                },
                {
                    id: 3,
                    route: 'Dakar → Kaolack',
                    distance: 190,
                    voyages: 28,
                    tickets: 235,
                    vip: 59,
                    standard: 176,
                    revenue: 3525000,
                    averageRevenue: 125893,
                    occupancy: 83.9,
                    performance: 'excellent'
                },
                {
                    id: 4,
                    route: 'Thiès → Dakar',
                    distance: 70,
                    voyages: 40,
                    tickets: 336,
                    vip: 84,
                    standard: 252,
                    revenue: 4200000,
                    averageRevenue: 105000,
                    occupancy: 84.0,
                    performance: 'good'
                },
                {
                    id: 5,
                    route: 'Dakar → Touba',
                    distance: 195,
                    voyages: 25,
                    tickets: 210,
                    vip: 53,
                    standard: 157,
                    revenue: 3150000,
                    averageRevenue: 126000,
                    occupancy: 84.0,
                    performance: 'good'
                }
            ];

            const topBuses = [
                {
                    id: 1,
                    immatriculation: 'DK-1234-AB',
                    model: 'Mercedes Sprinter',
                    occupancy: 92.5,
                    revenue: '1.25M'
                },
                {
                    id: 2,
                    immatriculation: 'DK-5678-CD',
                    model: 'Toyota Coaster',
                    occupancy: 88.3,
                    revenue: '1.12M'
                },
                {
                    id: 3,
                    immatriculation: 'DK-9012-EF',
                    model: 'Mercedes Sprinter',
                    occupancy: 85.7,
                    revenue: '980k'
                }
            ];

            return { stats, topTrajets, topBuses };
        },

        setChartType(chart, type) {
            this.chartConfig[chart].type = type;
            this.addToast(`Affichage ${type} activé`, 'info');
        },

        getPerformanceClass(performance) {
            const classes = {
                'excellent': 'bg-emerald-100 text-emerald-700',
                'good': 'bg-blue-100 text-blue-700',
                'average': 'bg-amber-100 text-amber-700',
                'poor': 'bg-red-100 text-red-700'
            };
            return classes[performance] || 'bg-gray-100 text-gray-700';
        },

        getPerformanceText(performance) {
            const texts = {
                'excellent': 'Excellent',
                'good': 'Bon',
                'average': 'Moyen',
                'poor': 'Faible'
            };
            return texts[performance] || performance;
        },

        exportReport(format) {
            const formats = {
                'pdf': 'PDF',
                'excel': 'Excel'
            };

            this.addToast(`Génération du rapport ${formats[format]} en cours...`, 'info');

            // Simulation d'export
            setTimeout(() => {
                this.addToast(`Rapport ${formats[format]} généré avec succès`, 'success');
            }, 1500);
        },

        printReport() {
            window.print();
            this.addToast('Impression du rapport démarrée', 'info');
        },

        exportTopTrajets() {
            console.log('Export top trajets:', this.topTrajets);
            this.addToast('Export des top trajets en cours...', 'info');
        },

        generateFullReport() {
            this.loading = true;
            this.addToast('Génération du rapport complet...', 'info');

            setTimeout(() => {
                // Simuler la génération d'un rapport complet
                const reportData = {
                    date: new Date().toISOString(),
                    period: this.reportPeriod,
                    stats: this.reportStats,
                    topTrajets: this.topTrajets,
                    summary: {
                        insights: [
                            'Les trajets Dakar → Thiès et Dakar → Saint-Louis représentent 70% des revenus',
                            'Le taux d\'occupation VIP est en hausse de 15% ce mois',
                            'Recommandation: Augmenter la fréquence des voyages sur les trajets les plus rentables',
                            'Opportunité: Développer l\'offre VIP sur les trajets longue distance'
                        ],
                        recommendations: [
                            'Investir dans 2 nouveaux bus pour les trajets Dakar → Thiès',
                            'Lancer une campagne de fidélisation clients VIP',
                            'Optimiser les horaires pour augmenter le taux d\'occupation en semaine'
                        ]
                    }
                };

                console.log('📋 Rapport complet généré:', reportData);
                this.addToast('Rapport complet généré avec succès', 'success');

                this.loading = false;
            }, 2000);
        },

        // ==================== AUTRES MÉTHODES ====================
        async logout() {
            this.addToast('Déconnexion en cours...', 'info');
            try {
                await this.apiFetch('/admin/logout', { method: 'POST' });
            } catch (error) {
                console.warn('Erreur lors du logout API:', error);
            }

            localStorage.removeItem('auth_token');
            sessionStorage.removeItem('auth_token');

            setTimeout(() => {
                window.location.href = '/admin/login';
            }, 500);
        },

        refreshData() {
            this.loading = true;
            setTimeout(() => {
                this.addToast('Données rafraîchies', 'success');
                this.loading = false;
            }, 1000);
        },

        debugDashboard() {
            console.log('=== DEBUG DASHBOARD ===');
            console.log('Voyages:', this.voyages);
            console.log('Buses:', this.buses);
            console.log('Trajets:', this.trajets);
            console.log('Form Errors:', this.formErrors);
            console.log('Current Section:', this.currentSection);
            console.log('====================');
        },

        debugClients() {
            console.log('=== DEBUG CLIENTS ===');
            console.log('Clients:', this.clients);
            console.log('Client Pages:', this.clientPages);
            console.log('Total Client Pages:', this.totalClientPages);
            console.log('Current Client Page:', this.currentClientPage);
            console.log('====================');
        }
    };
}

// Export
if (typeof window !== 'undefined') {
    window.dashboardApp = dashboardApp;
    console.log('📦 dashboardApp COMPLET chargé');
}