@extends('layouts.app')

@section('title', 'Paiement - BusRapide')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center text-sm text-slate-500">
            <a href="/reservation" class="hover:text-brand-600">Réservation</a>
            <i class="fa-solid fa-chevron-right mx-3 text-xs"></i>
            <span class="font-bold text-brand-600">Paiement</span>
        </div>

        <div class="grid lg:grid-cols-12 gap-8">

            <!-- Colonne Gauche : Formulaires -->
            <div class="lg:col-span-7 space-y-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Finalisez votre réservation</h1>
                    <p class="text-slate-600">Complétez les informations pour tous les passagers.</p>
                </div>

                <!-- Formulaires Passagers Dynamiques -->
                <section id="passengers-section" class="space-y-4">
                    <!-- Généré dynamiquement par JavaScript -->
                </section>

                <!-- Paiement -->
                <section class="bg-white rounded-2xl shadow-card p-6 border border-slate-100">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Méthode de Paiement</h2>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-slate-700 mb-4">Choisissez votre méthode :</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Orange Money -->
                            <button type="button" onclick="selectPaymentMethod('orange')"
                                class="payment-method-btn border-2 border-slate-200 rounded-xl p-6 transition hover:border-orange-500 hover:bg-orange-50"
                                data-method="orange">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-mobile-screen text-3xl text-orange-500"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">Orange Money</span>
                                </div>
                            </button>

                            <!-- MTN MoMo -->
                            <button type="button" onclick="selectPaymentMethod('mtn')"
                                class="payment-method-btn border-2 border-slate-200 rounded-xl p-6 transition hover:border-yellow-500 hover:bg-yellow-50"
                                data-method="mtn">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-wallet text-3xl text-yellow-600"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">MTN MoMo</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Champ téléphone (affiché conditionnellement) -->
                    <div id="phone-payment-field" class="hidden mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-phone mr-2"></i>Numéro de téléphone de paiement
                        </label>
                        <div class="flex">
                            <span
                                class="px-4 py-3 bg-slate-100 border border-r-0 border-slate-300 rounded-l-lg font-mono text-slate-700">
                                +237
                            </span>
                            <input type="tel" id="payment-phone"
                                class="flex-1 px-4 py-3 border border-slate-300 rounded-r-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                                placeholder="6 XX XX XX XX" pattern="6[0-9]{8}" maxlength="9">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Entrez le numéro qui recevra la demande de paiement
                        </p>
                    </div>

                    <!-- Bouton de paiement -->
                    <button type="button" onclick="initiatePayment()" id="pay-button"
                        class="w-full bg-brand-600 hover:bg-brand-700 text-white py-4 rounded-xl font-bold text-lg shadow-lg transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="pay-button-text">
                            <i class="fa-solid fa-lock mr-2"></i>
                            Payer <span id="amount-display">0</span> FCFA
                        </span>
                        <i id="pay-loader" class="fa-solid fa-hourglass-half fa-spin hidden"></i>
                    </button>
                </section>
            </div>

            <!-- Sidebar Récapitulatif -->
            <aside class="lg:col-span-5">
                <div class="lg:sticky lg:top-24 bg-white rounded-2xl shadow-xl p-6 border-2 border-slate-100">
                    <!-- En-tête -->
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200">
                        <i class="fa-solid fa-clipboard-list text-2xl text-brand-600"></i>
                        <h3 class="font-bold text-lg text-slate-900">Récapitulatif</h3>
                    </div>

                    <div id="trip-summary-loading" class="animate-pulse space-y-4">
                        <div class="h-16 bg-slate-100 rounded"></div>
                        <div class="h-32 bg-slate-100 rounded"></div>
                        <div class="h-20 bg-slate-100 rounded"></div>
                    </div>

                    <div id="trip-summary" class="hidden">
                        <!-- Itinéraire -->
                        <div class="mb-6 bg-gradient-to-r from-brand-50 to-blue-50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div class="text-center flex-1">
                                    <p class="text-2xl font-black text-slate-900" id="ville-depart">--</p>
                                    <p class="text-xs text-slate-500 mt-1">Départ</p>
                                </div>
                                <div class="px-4">
                                    <i class="fa-solid fa-bus text-brand-600 text-2xl animate-pulse"></i>
                                </div>
                                <div class="text-center flex-1">
                                    <p class="text-2xl font-black text-slate-900" id="ville-arrivee">--</p>
                                    <p class="text-xs text-slate-500 mt-1">Arrivée</p>
                                </div>
                            </div>
                        </div>

                        <!-- Détails -->
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-600 flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-brand-600"></i>
                                    Date
                                </span>
                                <span class="font-bold text-slate-900" id="date-voyage">--</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-600 flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-brand-600"></i>
                                    Heure
                                </span>
                                <span class="font-bold text-slate-900" id="heure-depart">--</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                <span class="text-slate-600 flex items-center gap-2">
                                    <i class="fa-solid fa-star text-brand-600"></i>
                                    Classe
                                </span>
                                <span class="font-bold px-3 py-1 rounded-full text-xs uppercase tracking-wide"
                                    id="classe-badge">--</span>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100">
                                <span class="text-slate-600 flex items-center gap-2">
                                    <i class="fa-solid fa-chair text-brand-600"></i>
                                    Sièges
                                </span>
                                <div class="flex flex-wrap gap-1 justify-end max-w-[60%]" id="sieges-list">
                                    <!-- Badges dynamiques -->
                                </div>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-slate-600 flex items-center gap-2">
                                    <i class="fa-solid fa-users text-brand-600"></i>
                                    Passagers
                                </span>
                                <span class="font-bold text-slate-900" id="passagers-count">--</span>
                            </div>
                        </div>

                        <!-- Prix Total -->
                        <div
                            class="pt-6 border-t-2 border-slate-200 bg-gradient-to-br from-brand-50 to-blue-50 -mx-6 -mb-6 px-6 pb-6 rounded-b-2xl">
                            <p class="text-sm text-slate-600 mb-2">Total à payer</p>
                            <p class="text-4xl font-black text-brand-600 flex items-baseline gap-2" id="total-price">
                                <span id="total-amount">0</span>
                                <span class="text-xl font-bold">FCFA</span>
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Modal Traitement -->
<div id="processing-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 text-center animate-fade-in-up">
        <div class="w-20 h-20 bg-blue-50 text-brand-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
             <i class="fa-solid fa-spinner fa-spin"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 mb-2">Traitement en cours...</h3>
        <p class="text-slate-600 mb-6">Veuillez patienter pendant que nous communiquons avec le service de paiement.</p>
    </div>
</div>

<!-- Modal Succès -->
    <div id="success-modal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 text-center animate-fade-in-up">
            <div
                class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Paiement Réussi !</h3>
            <p class="text-slate-600 mb-6">Votre billet a été réservé avec succès. Vous allez le recevoir par SMS et Email.
            </p>
            <button onclick="window.location.href='/tickets'"
                class="w-full py-3 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition">
                Voir mes billets
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let voyageDetails = null;
        let currentPaymentMethod = null;
        let paymentInProgress = false;
        let formModified = false;
        let passengersData = [];

        const params = new URLSearchParams(window.location.search);
        const voyageId = params.get('voyage');
        const siegeIds = params.get('sieges') ? params.get('sieges').split(',') : (params.get('siege') ? [params.get('siege')] : []);
        const classe = params.get('classe') || 'standard';
        const passagersCount = parseInt(params.get('passagers') || sessionStorage.getItem('passagers') || siegeIds.length || 1);

        document.addEventListener('DOMContentLoaded', async function () {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                showToast('Veuillez vous connecter pour continuer', 'warning');
                sessionStorage.setItem('redirectAfterLogin', window.location.href);
                setTimeout(() => window.location.href = '/', 1500);
                return;
            }

            if (!voyageId || siegeIds.length === 0) {
                showToast('Paramètres manquants', 'error');
                setTimeout(() => window.location.href = '/reservation', 1500);
                return;
            }

            // Générer les formulaires de passagers
            generatePassengerForms(passagersCount);

            // Charger infos voyage
            await loadTripDetails();

            // Détecter les modifications
            setupFormChangeDetection();
        });

        // Générer les formulaires de passagers dynamiquement
        function generatePassengerForms(count) {
            const container = document.getElementById('passengers-section');
            container.innerHTML = '';

            for (let i = 1; i <= count; i++) {
                const passengerCard = document.createElement('section');
                passengerCard.className = 'bg-white rounded-2xl shadow-card p-6 border border-slate-100';

                // Seul le passager principal (i === 1) a le champ CNI/Passeport
                if (i === 1) {
                    passengerCard.innerHTML = `
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center font-bold">
                                ${i}
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Passager Principal</h2>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-600 hover:text-brand-600 transition">
                            <input type="checkbox" id="is-main-passenger" onchange="fillMainPassengerData()" 
                                   class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="font-medium">Utiliser mes informations</span>
                        </label>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fa-solid fa-user mr-1 text-brand-600"></i>Nom complet *
                            </label>
                            <input type="text" 
                                   name="passenger[${i}][nom_complet]" 
                                   required 
                                   class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none" 
                                   placeholder="Ex: Jean Dupont">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fa-solid fa-id-card mr-1 text-brand-600"></i>CNI / Passeport / Carte Scolaire *
                            </label>
                            <input type="text" 
                                   name="passenger[${i}][cni]" 
                                   required 
                                   class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none" 
                                   placeholder="Ex: 123456789">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            <i class="fa-solid fa-venus-mars mr-1 text-brand-600"></i>Genre *
                        </label>
                        <select name="passenger[${i}][genre]" 
                                required 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none">
                            <option value="">Sélectionnez le genre</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                        </select>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-blue-800">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            <strong>Passager principal :</strong> Vous devrez présenter votre pièce d'identité lors de l'embarquement.
                        </p>
                    </div>
                `;
                } else {
                    // Les autres passagers n'ont pas besoin de CNI
                    passengerCard.innerHTML = `
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                            ${i}
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Passager ${i}</h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fa-solid fa-user mr-1 text-brand-600"></i>Nom complet *
                            </label>
                            <input type="text" 
                                   name="passenger[${i}][nom_complet]" 
                                   required 
                                   class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none" 
                                   placeholder="Ex: Marie Martin">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                <i class="fa-solid fa-venus-mars mr-1 text-brand-600"></i>Genre *
                            </label>
                            <select name="passenger[${i}][genre]" 
                                    required 
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition outline-none">
                                <option value="">Sélectionnez le genre</option>
                                <option value="homme">Homme</option>
                                <option value="femme">Femme</option>
                            </select>
                        </div>
                    </div>
                `;
                }

                container.appendChild(passengerCard);
            }
        }

        // Remplir automatiquement les données du passager principal
        async function fillMainPassengerData() {
            const checkbox = document.getElementById('is-main-passenger');
            if (!checkbox.checked) {
                // Vider les champs
                document.querySelector('[name="passenger[1][nom_complet]"]').value = '';
                document.querySelector('[name="passenger[1][cni]"]').value = '';
                document.querySelector('[name="passenger[1][genre]"]').value = '';
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/clients/profile`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const user = data.data || data;

                    // Remplir les champs
                    const nomComplet = `${user.prenom || ''} ${user.nom || ''}`.trim();
                    document.querySelector('[name="passenger[1][nom_complet]"]').value = nomComplet;

                    if (user.cni) {
                        document.querySelector('[name="passenger[1][cni]"]').value = user.cni;
                    }

                    // Essayer de déduire le genre si disponible
                    if (user.genre) {
                        document.querySelector('[name="passenger[1][genre]"]').value = user.genre.toLowerCase();
                    }

                    showToast('Informations pré-remplies avec succès', 'success');
                }
            } catch (error) {
                console.error('Erreur lors du chargement du profil:', error);
                showToast('Impossible de charger vos informations', 'error');
                checkbox.checked = false;
            }
        }

        // Charger les détails du voyage
        async function loadTripDetails() {
            try {
                // Simuler les données si l'API échoue
                let trip = null;

                try {
                    const response = await fetch(`${API_BASE_URL}/voyages/${voyageId}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (response.ok && data.success) {
                        trip = data.data;
                    }
                } catch (err) {
                    console.log('API non disponible, utilisation de données simulées');
                }

                // Si l'API échoue, utiliser des données simulées
                if (!trip) {
                    trip = {
                        idVoyage: voyageId,
                        trajet: {
                            villeDepart: 'Douala',
                            villeArrivee: 'Yaoundé'
                        },
                        dateHeureDepart: new Date().toISOString(),
                        heure_depart: '08:00',
                        prixStandard: 5000,
                        prixVIP: 8000,
                        prix: classe === 'vip' ? 8000 : 5000
                    };
                }

                voyageDetails = trip;
                renderTripSummary(trip);
            } catch (err) {
                console.error(err);
                showToast('Erreur serveur', 'error');
            }
        }

        // Afficher le récapitulatif du voyage
        function renderTripSummary(trip) {
            document.getElementById('trip-summary-loading').classList.add('hidden');
            document.getElementById('trip-summary').classList.remove('hidden');

            // Itinéraire
            document.getElementById('ville-depart').textContent = trip.trajet?.villeDepart || 'Départ';
            document.getElementById('ville-arrivee').textContent = trip.trajet?.villeArrivee || 'Arrivée';

            // Date et heure
            const dateVoyage = new Date(trip.dateHeureDepart);
            document.getElementById('date-voyage').textContent = dateVoyage.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('heure-depart').textContent = trip.heure_depart || dateVoyage.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });

            // Classe
            const classeBadge = document.getElementById('classe-badge');
            classeBadge.textContent = classe.toUpperCase();
            if (classe === 'vip') {
                classeBadge.classList.add('bg-purple-100', 'text-purple-800');
            } else {
                classeBadge.classList.add('bg-blue-100', 'text-blue-800');
            }

            // Sièges
            const siegesList = document.getElementById('sieges-list');
            siegesList.innerHTML = siegeIds.map(id => `
            <span class="px-2 py-1 bg-brand-100 text-brand-700 rounded font-bold text-xs">${id}</span>
        `).join('');

            // Nombre de passagers
            document.getElementById('passagers-count').textContent = `${passagersCount} passager${passagersCount > 1 ? 's' : ''}`;

            // Prix total
            const pricePerSeat = (classe === 'vip') ? (trip.prixVIP || trip.prix) : (trip.prixStandard || trip.prix);
            const totalAmount = pricePerSeat * siegeIds.length;

            document.getElementById('total-amount').textContent = totalAmount.toLocaleString('fr-FR');
            document.getElementById('amount-display').textContent = totalAmount.toLocaleString('fr-FR');
        }

        // Sélectionner une méthode de paiement
        function selectPaymentMethod(method) {
            currentPaymentMethod = method;

            // Mettre à jour les styles des boutons
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('border-orange-500', 'bg-orange-50', 'border-yellow-500', 'bg-yellow-50');
                btn.classList.add('border-slate-200');
            });

            const selectedBtn = document.querySelector(`[data-method="${method}"]`);
            selectedBtn.classList.remove('border-slate-200');

            if (method === 'orange') {
                selectedBtn.classList.add('border-orange-500', 'bg-orange-50');
            } else if (method === 'mtn') {
                selectedBtn.classList.add('border-yellow-500', 'bg-yellow-50');
            }

            // Afficher le champ téléphone
            document.getElementById('phone-payment-field').classList.remove('hidden');
        }

        // Valider tous les formulaires de passagers
        function validateAllPassengers() {
            let isValid = true;
            let errors = [];
            passengersData = []; // Réinitialiser pour éviter les doublons

            for (let i = 1; i <= passagersCount; i++) {
                const nomComplet = document.querySelector(`[name="passenger[${i}][nom_complet]"]`)?.value.trim();
                const genre = document.querySelector(`[name="passenger[${i}][genre]"]`)?.value;
                let cni = null;

                if (i === 1) {
                    cni = document.querySelector(`[name="passenger[${i}][cni]"]`)?.value.trim();
                    if (!nomComplet || !cni || !genre) {
                        isValid = false;
                        errors.push(`Passager Principal: Tous les champs sont requis (Nom, CNI, Genre)`);
                    }
                } else {
                    if (!nomComplet || !genre) {
                        isValid = false;
                        errors.push(`Passager ${i}: Nom et Genre sont requis`);
                    }
                }

                // Stocker les données
                const nameParts = nomComplet.split(' ');
                const nom = nameParts[0];
                const prenom = nameParts.slice(1).join(' ') || '.';

                passengersData.push({ 
                    nom: nom, 
                    prenom: prenom, 
                    cni: cni || 'N/A', 
                    genre: genre 
                });
            }

            // Valider la méthode de paiement
            if (!currentPaymentMethod) {
                isValid = false;
                errors.push('Veuillez sélectionner une méthode de paiement');
            }

            // Valider le numéro de téléphone si méthode mobile
            if (currentPaymentMethod && (currentPaymentMethod === 'orange' || currentPaymentMethod === 'mtn')) {
                const phone = document.getElementById('payment-phone')?.value.trim();
                if (!phone || !phone.match(/^6[0-9]{8}$/)) {
                    isValid = false;
                    errors.push('Numéro de téléphone invalide (format: 6XXXXXXXX)');
                }
            }

            if (!isValid) {
                showToast(errors.join('<br>'), 'error');
            }

            return isValid;
        }

        // Check payment status (Polling)
        async function checkPaymentStatus(reference) {
            const processingTitle = document.querySelector('#processing-modal h3');
            const processingDesc = document.querySelector('#processing-modal p');

            try {
                const response = await fetch(`${API_BASE_URL}/tickets/check-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reference: reference })
                });

                const data = await response.json();

                // Update UI with status message if available
                if (data.message) {
                    processingDesc.textContent = data.message;
                    // Animation effet
                    processingDesc.classList.add('animate-pulse');
                    setTimeout(() => processingDesc.classList.remove('animate-pulse'), 500);
                }

                if (data.status === 'completed' || (data.success === true && data.status !== 'pending' && data.status !== 'failed' && data.status !== 'retry')) {
                    // Succès
                    document.getElementById('processing-modal').style.display = 'none';
                    document.getElementById('success-modal').classList.remove('hidden');
                    document.getElementById('success-modal').classList.add('flex');
                    formModified = false;
                } else if (data.status === 'failed') { // Only strict 'failed' stops the process
                    // Echec
                    document.getElementById('processing-modal').style.display = 'none';
                    showToast(data.message || 'Le paiement a échoué.', 'error');
                    document.getElementById('pay-button').disabled = false;
                    document.getElementById('pay-button-text').classList.remove('hidden');
                    document.getElementById('pay-loader').classList.add('hidden');
                    paymentInProgress = false;
                } else {
                    // Toujours en attente (pending, retry, error transformé en pending, etc.)
                   // On re-vérifie dans 3 secondes
                   console.log('Polling... Status:', data.status);
                   setTimeout(() => checkPaymentStatus(reference), 3000);
                }
            } catch (error) {
                console.error("Erreur polling:", error);
                processingDesc.textContent = "Problème de connexion, nouvelle tentative...";
                // On continue d'essayer même en cas d'erreur réseau passagère
                setTimeout(() => checkPaymentStatus(reference), 3000);
            }
        }

        // Initier le paiement
        async function initiatePayment() {
            if (paymentInProgress) {
                showToast('Paiement en cours...', 'info');
                return;
            }

            if (!validateAllPassengers()) {
                return;
            }

            paymentInProgress = true;
            const button = document.getElementById('pay-button');
            button.disabled = true;
            document.getElementById('pay-button-text').classList.add('hidden');
            document.getElementById('pay-loader').classList.remove('hidden');

            try {
                // Préparer les données
                const paymentData = {
                    idVoyage: voyageId,
                    idSieges: siegeIds,
                    classe: classe,
                    modePaiement: currentPaymentMethod,
                    passagers: passengersData
                };

                if (currentPaymentMethod === 'orange' || currentPaymentMethod === 'mtn') {
                    paymentData.telephone_paiement = document.getElementById('payment-phone').value;
                    // Note: Le backend ajoute le +237 si besoin
                }

                // Show processing modal appropriately
                document.getElementById('processing-modal').style.display = 'flex';
                const processingTitle = document.querySelector('#processing-modal h3');
                const processingDesc = document.querySelector('#processing-modal p');

                if (currentPaymentMethod === 'orange' || currentPaymentMethod === 'mtn') {
                    processingTitle.textContent = 'Veuillez valider sur votre mobile';
                    processingDesc.textContent = 'Tapez votre code pour confirmer le paiement...';
                } else {
                    processingTitle.textContent = 'Traitement en cours...';
                    processingDesc.textContent = 'Veuillez patienter...';
                }

                const response = await fetch(`${API_BASE_URL}/tickets`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(paymentData)
                });

                const data = await response.json();

                if (response.status === 202 && data.reference) {
                    // Paiement initié (Pending), on lance le polling
                    checkPaymentStatus(data.reference);
                } else if (response.ok) {
                    // Succès immédiat (cas rare ou simulation)
                    document.getElementById('processing-modal').style.display = 'none';
                    document.getElementById('success-modal').classList.remove('hidden');
                    document.getElementById('success-modal').classList.add('flex');
                    formModified = false;
                } else {
                    console.warn('Erreur API Tickets:', data);
                    document.getElementById('processing-modal').style.display = 'none';
                    
                    let message = data.message || 'Erreur lors de la création du billet';
                    if (data.errors) {
                        const details = Object.values(data.errors).flat().join('\n');
                        message += '\n' + details;
                    }
                    
                    showToast(message, 'error');
                    button.disabled = false;
                    document.getElementById('pay-button-text').classList.remove('hidden');
                    document.getElementById('pay-loader').classList.add('hidden');
                    paymentInProgress = false;
                }
            } catch (err) {
                console.error('Erreur connexion tickets:', err);
                document.getElementById('processing-modal').style.display = 'none';
                showToast('Erreur de connexion au serveur', 'error');
                button.disabled = false;
                document.getElementById('pay-button-text').classList.remove('hidden');
                document.getElementById('pay-loader').classList.add('hidden');
                paymentInProgress = false;
            }
        }

        // Détecter les modifications du formulaire
        function setupFormChangeDetection() {
            document.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('change', () => {
                    formModified = true;
                });
            });
        }

        // Alerte avant de quitter la page
        window.addEventListener('beforeunload', (e) => {
            if (formModified && !paymentInProgress) {
                e.preventDefault();
                e.returnValue = 'Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter ?';
                return e.returnValue;
            }
        });
    </script>
@endpush