@extends('layouts.app')

@section('title', 'Tableau de bord ')

@section('content')
<div class="min-h-screen bg-slate-50 pb-12">
    <!-- Header Dashboard -->
    <div class="bg-slate-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high text-brand-500"></i>
                        Tableau de Bord
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">
                        Bienvenue, <span id="user-name" class="font-bold text-white">--</span>
                        <span id="user-role-badge" class="ml-2 px-2 py-0.5 rounded text-xs font-bold uppercase bg-slate-700 text-slate-300">--</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span id="current-date" class="text-sm text-slate-400 hidden md:block">--</span>
                    <button onclick="logoutAdmin()" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500/20 px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-power-off"></i>
                        Déconnexion
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Loading State -->
        <div id="dashboard-loading" class="flex flex-col items-center justify-center py-20">
            <div class="w-12 h-12 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin mb-4"></div>
            <p class="text-slate-500 animate-pulse">Chargement de vos données...</p>
        </div>

        <!-- DASHBOARD ADMIN (Gestion Utilisateurs & Stats Globales) -->
        <div id="admin-dashboard" class="hidden dashboard-view space-y-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-50 text-blue-600 w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-900 mb-1" id="stats-users">--</h3>
                    <p class="text-sm text-slate-500">Clients Inscrits</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-brand-50 text-brand-600 w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-bus text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded-full">Actifs</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-900 mb-1" id="stats-buses">--</h3>
                    <p class="text-sm text-slate-500">Bus en Circulation</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-purple-50 text-purple-600 w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-ticket text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">+5%</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-900 mb-1" id="stats-tickets">--</h3>
                    <p class="text-sm text-slate-500">Billets Vendus (Auj.)</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-green-50 text-green-600 w-10 h-10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-1" id="stats-revenue">-- FCFA</h3>
                    <p class="text-sm text-slate-500">Revenus du Jour</p>
                </div>
            </div>

            <!-- Gestion Utilisateurs -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-900">Gestion des Employés</h3>
                    <button class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                        <i class="fa-solid fa-plus mr-2"></i> Ajouter un employé
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-4">Nom</th>
                                <th class="px-6 py-4">Rôle</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employees-table-body" class="divide-y divide-slate-100">
                            <!-- Rempli par JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DASHBOARD CONTROLEUR (Validation Billets & Embarquement) -->
        <div id="controleur-dashboard" class="hidden dashboard-view space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Scan Billet -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 text-center">
                    <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-qrcode text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Scanner un Billet</h2>
                    <p class="text-slate-500 mb-6">Utilisez la caméra ou saisissez le code manuellement</p>
                    
                    <div class="max-w-md mx-auto space-y-4">
                        <button class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-camera"></i>
                            Ouvrir le scanner
                        </button>
                        <div class="relative">
                            <input type="text" placeholder="Entrer le code billet (ex: TIC-1234)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 text-center font-mono uppercase">
                            <button class="absolute right-2 top-2 bottom-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-bold text-sm transition">
                                OK
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste Embarquement -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-lg text-slate-900 mb-1">Prochain Départ</h3>
                        <p class="text-sm text-brand-600 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-clock"></i> 14:00 • Douala -> Yaoundé
                        </p>
                    </div>
                    <div class="max-h-[400px] overflow-y-auto p-4 space-y-3">
                        <!-- Item Passager -->
                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-100 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-200 text-green-700 rounded-full flex items-center justify-center font-bold text-sm">A1</div>
                                <div>
                                    <p class="font-bold text-slate-900">Jean Dupont</p>
                                    <p class="text-xs text-green-700">Validé à 13:45</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-check-circle text-green-500 text-xl"></i>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg opacity-60">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-200 text-slate-500 rounded-full flex items-center justify-center font-bold text-sm">A2</div>
                                <div>
                                    <p class="font-bold text-slate-900">Marie Curie</p>
                                    <p class="text-xs text-slate-500">En attente</p>
                                </div>
                            </div>
                            <button class="text-xs font-bold bg-white border border-slate-200 px-3 py-1 rounded shadow-sm hover:bg-slate-50">Valider</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Mock Data pour la démo (sera remplacé par API)
    const MOCK_USER = {
        name: 'Admin User',
        role: localStorage.getItem('user_role') || 'admin' // admin, controleur
    };

    document.addEventListener('DOMContentLoaded', () => {
        initializeDashboard();
    });

    function initializeDashboard() {
        // Simuler chargement API
        setTimeout(() => {
            document.getElementById('dashboard-loading').classList.add('hidden');
            
            // Setup User Info
            document.getElementById('user-name').textContent = MOCK_USER.name;
            document.getElementById('user-role-badge').textContent = MOCK_USER.role;
            document.getElementById('current-date').textContent = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

            // Show appropriate view
            document.querySelectorAll('.dashboard-view').forEach(el => el.classList.add('hidden'));
            
            if (MOCK_USER.role === 'admin' || MOCK_USER.role === 'super_admin') {
                document.getElementById('admin-dashboard').classList.remove('hidden');
                loadAdminStats();
            } else if (MOCK_USER.role === 'controleur') {
                document.getElementById('controleur-dashboard').classList.remove('hidden');
            } else if (MOCK_USER.role === 'client') {
                window.location.href = '/tickets';
            } else {
                showToast('Rôle inconnu', 'error');
            }

        }, 800);
    }

    async function loadAdminStats() {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch('/api/admin/dashboard/stats', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if(result.success) {
                    const data = result.data;
                    document.getElementById('stats-users').textContent = data.total_clients || 0;
                    document.getElementById('stats-buses').textContent = data.active_buses || 0;
                    document.getElementById('stats-tickets').textContent = data.tickets_today || 0;
                    document.getElementById('stats-revenue').textContent = new Intl.NumberFormat('fr-FR').format(data.revenue_today || 0) + ' FCFA';
                }
            }
        } catch (error) {
            console.error('Erreur chargement stats:', error);
        }
    }

    function logoutAdmin() {
        if(confirm('Voulez-vous vraiment vous déconnecter ?')) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            window.location.href = '/';
        }
    }
</script>