@extends('layouts.app')

@section('title', 'Mon Profil - BusRapide')

@section('content')
<div class="min-h-screen bg-slate-50 pb-12">
    <!-- Header Profil -->
    <div class="bg-slate-900 text-white shadow-lg overflow-hidden relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"%23ffffff\" fill-opacity=\"0.1\" fill-rule=\"evenodd\"><circle cx=\"3\" cy=\"3\" r=\"3\"/><circle cx=\"13\" cy=\"13\" r=\"3\"/></g></svg>')"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative">
                    <div class="w-32 h-32 bg-brand-600 rounded-full flex items-center justify-center text-4xl font-black border-4 border-white/20 shadow-2xl overflow-hidden" id="profile-avatar">
                        --
                    </div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-4xl font-black tracking-tight" id="user-full-name">Chargement...</h1>
                    <p class="text-slate-400 mt-2 flex items-center justify-center md:justify-start gap-2">
                        <i class="fa-solid fa-envelope text-brand-500"></i>
                        <span id="user-email">--</span>
                        <span class="mx-2 text-slate-700">|</span>
                        <i class="fa-solid fa-phone text-brand-500"></i>
                        <span id="user-phone">--</span>
                    </p>
                    <div class="mt-6 flex flex-wrap justify-center md:justify-start gap-3">
                        <span class="px-4 py-1.5 bg-white/10 rounded-full text-xs font-bold uppercase tracking-widest border border-white/10">Client Actif</span>
                        <span id="vip-badge" class="hidden px-4 py-1.5 bg-amber-500 text-slate-900 rounded-full text-xs font-bold uppercase tracking-widest">Membre VIP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white border-b border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex gap-8 overflow-x-auto no-scrollbar">
                <button onclick="switchTab('infos')" id="tab-infos" class="profile-tab py-4 border-b-2 border-brand-600 text-brand-600 font-bold text-sm whitespace-nowrap transition-all">
                    <i class="fa-solid fa-user-gear mr-2"></i>Mes Informations
                </button>
                <button onclick="switchTab('billets')" id="tab-billets" class="profile-tab py-4 border-b-2 border-transparent text-slate-500 font-bold text-sm whitespace-nowrap transition-all hover:text-brand-600">
                    <i class="fa-solid fa-ticket mr-2"></i>Mes Billets
                </button>
                <button onclick="switchTab('notifications')" id="tab-notifications" class="profile-tab py-4 border-b-2 border-transparent text-slate-500 font-bold text-sm whitespace-nowrap transition-all hover:text-brand-600 relative">
                    <i class="fa-solid fa-bell mr-2"></i>Notifications
                    <span id="notif-count" class="hidden absolute top-3 -right-3 w-5 h-5 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center border-2 border-white">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- SECTION INFOS PERSO -->
        <div id="section-infos" class="profile-section space-y-8 animate-fade-in">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
                        <h3 class="text-xl font-bold mb-6 flex items-center gap-3 text-slate-900">
                            <i class="fa-solid fa-id-card text-brand-600 font-normal"></i>
                            Modifier Profil
                        </h3>
                        <form id="profile-form" class="space-y-6" onsubmit="updateProfile(event)">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase text-slate-500 tracking-widest pl-1">Prénom</label>
                                    <input type="text" id="input-prenom" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-bold">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase text-slate-500 tracking-widest pl-1">Nom</label>
                                    <input type="text" id="input-nom" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-bold">
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase text-slate-500 tracking-widest pl-1">Email</label>
                                    <input type="email" id="input-email" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-bold" disabled>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase text-slate-500 tracking-widest pl-1">Téléphone</label>
                                    <input type="tel" id="input-phone" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all font-bold">
                                </div>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-8 py-4 bg-brand-600 text-white rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-brand-700 transition shadow-xl shadow-brand-500/20 active:scale-[0.98]">
                                Enregistrer les modifications
                            </button>
                        </form>
                    </div>

                    <div class="bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-64 h-64 bg-brand-600/20 rounded-full blur-3xl"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                            <i class="fa-solid fa-shield-halved text-6xl text-brand-500"></i>
                            <div class="text-center md:text-left flex-1">
                                <h3 class="text-xl font-black uppercase tracking-widest mb-2 font-black">Sécurité</h3>
                                <p class="text-slate-400 text-sm">Maintenez votre compte en sécurité en changeant régulièrement votre mot de passe.</p>
                            </div>
                            <button class="px-6 py-3 bg-white text-slate-900 rounded-xl font-bold text-sm">Changer</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm text-center">
                        <div class="w-16 h-16 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase text-slate-500 tracking-widest mb-1">Dépensé Total</h4>
                        <p class="text-3xl font-black text-slate-900" id="stat-total-spent">0 FCFA</p>
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">Basé sur vos achats confirmés</p>
                    </div>

                    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm text-center">
                        <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase text-slate-500 tracking-widest mb-1">Nombre de Voyages</h4>
                        <p class="text-3xl font-black text-slate-900" id="stat-ticket-count">0</p>
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">Trajets effectués avec nous</p>
                    </div>

                    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 text-white text-center shadow-xl shadow-brand-500/20 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                        <i class="fa-solid fa-crown text-4xl text-amber-500 mb-4 group-hover:scale-110 transition-transform duration-500"></i>
                        <h4 class="text-xl font-black font-black uppercase tracking-widest mb-2 font-black">Devenir VIP</h4>
                        <p class="text-xs text-white/70 mb-6 leading-relaxed">Profitez de réductions exclusives et d'un embarquement prioritaire.</p>
                        <button class="w-full py-3 bg-white text-brand-700 rounded-xl font-black text-xs uppercase tracking-widest">En savoir plus</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION BILLETS -->
        <div id="section-billets" class="profile-section hidden space-y-6 animate-fade-in">
            <div id="tickets-list" class="grid grid-cols-1 gap-6">
                <!-- Rempli par AJAX -->
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mb-6">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <p class="text-slate-500 font-bold">Récupération de vos billets...</p>
                </div>
            </div>
        </div>

        <!-- SECTION NOTIFICATIONS -->
        <div id="section-notifications" class="profile-section hidden space-y-4 animate-fade-in">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-black text-slate-900">Centre de Notifications</h3>
                <button onclick="markAllNotifsRead()" class="text-xs font-bold text-brand-600 bg-brand-50 px-4 py-2 rounded-full hover:bg-brand-100 transition">Tout marquer comme lu</button>
            </div>
            <div id="notifications-list" class="space-y-4">
                <!-- Rempli par AJAX -->
                <div class="bg-white p-12 rounded-3xl text-center border border-slate-100">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-6">
                        <i class="fa-solid fa-bell-slash"></i>
                    </div>
                    <p class="text-slate-500 font-bold italic">Aucune notification pour le moment.</p>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '/';
            return;
        }
        
        loadClientProfile();
        loadTickets();
        loadNotifications();
    });

    async function loadClientProfile() {
        try {
            const response = await fetch(`${API_BASE_URL}/clients/profile`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            
            if (result.success && result.data) {
                const user = result.data.client;
                const stats = result.data.statistiques;
                
                // Header infos
                document.getElementById('user-full-name').textContent = `Bienvenue ${user.prenom}`;
                document.getElementById('user-email').textContent = user.email;
                document.getElementById('user-phone').textContent = user.telephone || 'Non renseigné';
                document.getElementById('profile-avatar').textContent = user.prenom.charAt(0);
                
                // Stats
                document.getElementById('stat-ticket-count').textContent = stats.total_tickets || 0;
                document.getElementById('stat-total-spent').textContent = `${(stats.montant_total || 0).toLocaleString()} FCFA`;
                
                // Form filler
                document.getElementById('input-prenom').value = user.prenom;
                document.getElementById('input-nom').value = user.nom;
                document.getElementById('input-email').value = user.email;
                document.getElementById('input-phone').value = user.telephone;
                
                if (user.vip) {
                    document.getElementById('vip-badge').classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error loading profile:', error);
            showToast('Erreur lors du chargement de votre profil', 'error');
        }
    }

    async function updateProfile(e) {
        e.preventDefault();
        const prenom = document.getElementById('input-prenom').value;
        const nom = document.getElementById('input-nom').value;
        const phone = document.getElementById('input-phone').value;
        
        try {
            const response = await fetch(`${API_BASE_URL}/clients/profile`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ prenom, nom, telephone: phone })
            });
            
            if (response.ok) {
                showToast('Profil mis à jour avec succès', 'success');
                loadClientProfile();
            } else {
                const data = await response.json();
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            showToast('Erreur technique', 'error');
        }
    }

    async function loadTickets() {
        try {
            const response = await fetch(`${API_BASE_URL}/clients/historique`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            
            const container = document.getElementById('tickets-list');
            if (result.success && result.data && result.data.data.length > 0) {
                container.innerHTML = result.data.data.map(ticket => `
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <div class="flex flex-col md:flex-row">
                            <div class="p-8 flex-1">
                                <div class="flex justify-between items-center mb-6">
                                    <span class="px-4 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-black uppercase tracking-widest">${ticket.numeroBillet}</span>
                                    <span class="px-4 py-1 ${ticket.statut === 'confirme' ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600'} rounded-full text-[10px] font-black uppercase tracking-widest">${ticket.statut}</span>
                                </div>
                                <div class="flex items-center gap-6 mb-8">
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-slate-900">${ticket.voyage.trajet.villeDepart}</p>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Départ</p>
                                    </div>
                                    <div class="flex-1 h-[2px] bg-slate-100 relative">
                                        <i class="fa-solid fa-bus text-brand-600 absolute left-1/2 -top-3 -translate-x-1/2 bg-white px-2"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-slate-900">${ticket.voyage.trajet.villeArrivee}</p>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Arrivée</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-slate-50">
                                    <div><p class="text-[10px] font-black uppercase text-slate-400 mb-1">Date</p><p class="font-bold text-sm">${new Date(ticket.voyage.dateHeureDepart).toLocaleDateString('fr-FR')}</p></div>
                                    <div><p class="text-[10px] font-black uppercase text-slate-400 mb-1">Heure</p><p class="font-bold text-sm">${ticket.voyage.heure_depart || '00:00'}</p></div>
                                    <div><p class="text-[10px] font-black uppercase text-slate-400 mb-1">Siège</p><p class="font-bold text-sm">${ticket.siege ? ticket.siege.numeroSiege : 'N/A'}</p></div>
                                    <div><p class="text-[10px] font-black uppercase text-slate-400 mb-1">Classe</p><p class="font-bold text-sm uppercase">${ticket.classeBillet}</p></div>
                                </div>
                            </div>
                            <div class="w-full md:w-64 bg-slate-50/50 p-8 flex flex-col items-center justify-center border-l border-slate-100">
                                <div class="w-32 h-32 bg-white p-2 rounded-2xl shadow-sm mb-6 flex items-center justify-center overflow-hidden transition-transform group-hover:scale-105">
                                    <img src="${ticket.codeQR ? 'data:image/png;base64,'+ticket.codeQR : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='+ticket.numeroBillet}" class="w-full h-full object-contain">
                                </div>
                                <div class="w-full flex gap-2">
                                    <button onclick="window.location.href='/api/tickets/${ticket.idTicket}/download?token=${localStorage.getItem('auth_token')}'" class="flex-1 py-3 bg-brand-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-brand-700 transition shadow-lg shadow-brand-500/20"><i class="fa-solid fa-download mr-1"></i> PDF</button>
                                    <button class="flex items-center justify-center w-12 h-12 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-brand-600 transition"><i class="fa-solid fa-share-nodes"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="bg-white p-12 rounded-3xl text-center border border-slate-100 shadow-sm">
                        <p class="text-slate-500 font-bold">Vous n'avez pas encore de billets.</p>
                        <a href="/reservation" class="mt-6 inline-block px-8 py-3 bg-brand-600 text-white rounded-xl font-black text-xs uppercase tracking-widest">Réserver mon premier voyage</a>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading tickets:', error);
        }
    }

    async function loadNotifications() {
        try {
            const response = await fetch(`${API_BASE_URL}/clients/notifications`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            
            const container = document.getElementById('notifications-list');
            const countBadge = document.getElementById('notif-count');
            
            if (result.success && result.data && result.data.length > 0) {
                const unread = result.data.filter(n => !n.dateLecture).length;
                if (unread > 0) {
                    countBadge.textContent = unread;
                    countBadge.classList.remove('hidden');
                }
                
                container.innerHTML = result.data.map(notif => `
                    <div class="bg-white p-6 rounded-3xl border ${notif.dateLecture ? 'border-slate-100 opacity-70' : 'border-brand-200 bg-brand-50/10'} shadow-sm flex gap-6 items-start transition-all hover:shadow-md">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center ${notif.dateLecture ? 'bg-slate-100 text-slate-400' : 'bg-brand-600 text-white'}">
                            <i class="fa-solid ${notif.typeNotification === 'voyage_changé' ? 'fa-triangle-exclamation' : 'fa-bell'}"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-black text-slate-900 leading-tight uppercase text-xs tracking-wider">${notif.titre}</h4>
                                <span class="text-[10px] font-bold text-slate-400">${new Date(notif.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">${notif.contenu}</p>
                            ${!notif.dateLecture ? `<button onclick="markAsRead(${notif.idNotification})" class="mt-4 text-[10px] font-black uppercase text-brand-600 tracking-widest hover:underline">Marquer comme lu</button>` : ''}
                        </div>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    function switchTab(tabId) {
        document.querySelectorAll('.profile-section').forEach(s => s.classList.add('hidden'));
        document.getElementById(`section-${tabId}`).classList.remove('hidden');
        
        document.querySelectorAll('.profile-tab').forEach(t => {
            t.classList.remove('border-brand-600', 'text-brand-600');
            t.classList.add('border-transparent', 'text-slate-500');
        });
        document.getElementById(`tab-${tabId}`).classList.remove('border-transparent', 'text-slate-500');
        document.getElementById(`tab-${tabId}`).classList.add('border-brand-600', 'text-brand-600');
    }
</script>
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection
