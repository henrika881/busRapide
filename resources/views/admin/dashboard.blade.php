@extends('layouts.admin')

@section('title', 'Console d\'Administration - BusRapide')

@section('content')
   <div class="flex h-screen overflow-hidden bg-gray-50" x-data="dashboardApp()" x-init="init()" x-cloak
      @toast.window="addToast($event.detail)">

      <!-- Mobile Sidebar Overlay -->
      <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 lg:hidden"></div>

      <!-- Toast Notifications -->
      <div class="fixed top-6 right-6 z-50 space-y-3 w-80">
         <template x-for="toast in toasts" :key="toast.id">
            <div :class="{
                                               'bg-emerald-500': toast.type === 'success',
                                               'bg-red-500': toast.type === 'error',
                                               'bg-blue-500': toast.type === 'info',
                                               'bg-amber-500': toast.type === 'warning'
                                           }"
               class="animate-fade-in flex items-center p-4 rounded-xl shadow-lg text-white">
               <div class="mr-3">
                  <i x-show="toast.type === 'success'" class="fa-solid fa-circle-check"></i>
                  <i x-show="toast.type === 'error'" class="fa-solid fa-circle-xmark"></i>
                  <i x-show="toast.type === 'info'" class="fa-solid fa-circle-info"></i>
               </div>
               <div x-text="toast.message" class="text-sm font-medium"></div>
               <button @click="removeToast(toast.id)" class="ml-auto opacity-70 hover:opacity-100 transition-opacity">
                  <i class="fa-solid fa-xmark"></i>
               </button>
            </div>
         </template>
      </div>

      <!-- Sidebar -->
      <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
         class="fixed inset-y-0 left-0 w-72 bg-slate-900 flex-shrink-0 flex flex-col transition-all duration-300 z-40 lg:relative shadow-2xl">
         <!-- Logo Section -->
         <div class="h-20 flex items-center px-8 border-b border-slate-800/50">
            <div class="flex items-center gap-3">
               <div
                  class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center shadow-lg shadow-brand-600/20">
                  <i class="fa-solid fa-bus text-white text-xl"></i>
               </div>
               <span class="text-xl font-bold text-white tracking-tight font-outfit uppercase">Bus<span
                     class="text-brand-400">Rapide</span></span>
            </div>
         </div>

         <!-- Navigation Menu -->
         <div class="flex-1 overflow-y-auto custom-scrollbar pt-6 pb-4">
            <div class="px-4 mb-4">
               <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] px-4 mb-3">Menu Principal</p>
               <nav class="space-y-1">
                  <!-- Dashboard -->
                  <button @click="currentSection = 'dashboard'"
                     :class="currentSection === 'dashboard' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                     class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                     <i class="fa-solid fa-chart-column w-5 text-center group-hover:scale-110 transition-transform"></i>
                     <span>Vue d'ensemble</span>
                  </button>

                  <!-- Modules for Gestionnaire & Super Admin -->
                  <template x-if="isAdmin() || isGestionnaire()">
                     <div class="space-y-1">
                        <button @click="currentSection = 'voyages'"
                           :class="currentSection === 'voyages' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                           class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                           <i class="fa-solid fa-route w-5 text-center group-hover:scale-110 transition-transform"></i>
                           <span>Voyages & Trajets</span>
                        </button>
                        <button @click="currentSection = 'tickets'"
                           :class="currentSection === 'tickets' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                           class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                           <i
                              class="fa-solid fa-ticket-simple w-5 text-center group-hover:scale-110 transition-transform"></i>
                           <span>Billetterie</span>
                        </button>
                        <button @click="currentSection = 'clients'"
                           :class="currentSection === 'clients' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                           class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                           <i class="fa-solid fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                           <span>Gestion Clients</span>
                        </button>
                     </div>
                  </template>

                  <!-- Module for Controleur & Super Admin -->
                  <template x-if="isAdmin() || isControleur()">
                     <button @click="currentSection = 'embarquement'"
                        :class="currentSection === 'embarquement' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                        class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                        <i
                           class="fa-solid fa-clipboard-check w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span>Embarquement</span>
                     </button>
                     <button @click="debugDashboard()"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-bold">
                        DEBUG
                     </button>
                  </template>
               </nav>
            </div>

            <!-- Management for Super Admin -->
            <template x-if="isAdmin()">
               <div class="px-4 mt-8">
                  <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] px-4 mb-3">Administration
                  </p>
                  <nav class="space-y-1">
                     <button @click="currentSection = 'personnel'"
                        :class="currentSection === 'personnel' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                        class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                        <i class="fa-solid fa-user-shield w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span>Gestion Personnel</span>
                     </button>
                     <button @click="currentSection = 'rapports'"
                        :class="currentSection === 'rapports' ? 'bg-brand-600/10 text-brand-400 border-l-4 border-brand-500' : 'text-slate-400 hover:text-white hover:bg-slate-800/50 border-l-4 border-transparent'"
                        class="w-full flex items-center gap-4 px-4 py-3 text-sm font-medium transition-all group">
                        <i
                           class="fa-solid fa-file-invoice-dollar w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span>Rapports & Stats</span>
                     </button>
                  </nav>
               </div>
            </template>
         </div>

         <!-- Footer Profile -->
         <div class="border-t border-slate-800 p-6">
            <div class="flex items-center gap-4">
               <div
                  class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700 text-brand-400 font-bold overflow-hidden">
                  <span x-text="userName.charAt(0)"></span>
               </div>
               <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-white truncate" x-text="userName"></p>
                  <p class="text-xs text-slate-500 truncate capitalize" x-text="userRole"></p>
               </div>
               <button @click="currentSection = 'profil'" class="text-slate-500 hover:text-white transition-colors">
                  <i class="fa-solid fa-gear"></i>
               </button>
            </div>
         </div>
      </aside>

      <!-- Main Content -->
      <div class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden relative">

         <!-- Header / Topbar -->
         <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 relative z-10">
            <div class="flex items-center gap-4">
               <!-- Mobile Menu Button -->
               <button @click="mobileMenuOpen = true"
                  class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-gray-100 rounded-xl transition-all">
                  <i class="fa-solid fa-bars-staggered text-xl"></i>
               </button>
               <h2 class="text-lg lg:text-2xl font-bold text-slate-900 font-outfit capitalize truncate max-w-[150px] lg:max-w-none"
                  x-text="currentSectionTitle()"></h2>
               <div
                  class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-bold uppercase tracking-wider border border-brand-100"
                  x-text="'Session Active'"></div>
            </div>

            <div class="flex items-center gap-6">
               <!-- Session Monitoring -->
               <div class="hidden lg:flex items-center gap-3 px-4 py-2 bg-gray-100 rounded-xl border border-gray-200">
                  <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                  <span class="text-xs font-semibold text-slate-600"
                     x-text="sessionsCount + ' appareils connectés'"></span>
               </div>

               <div class="flex items-center gap-2">
                  <button @click="refreshData()"
                     class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition-all"
                     title="Actualiser">
                     <i class="fa-solid fa-rotate" :class="loading ? 'animate-spin' : ''"></i>
                  </button>
                  <div class="w-px h-6 bg-gray-200 mx-2"></div>
                  <button @click="logout()"
                     class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-all text-sm font-semibold border border-transparent hover:border-red-100">
                     <i class="fa-solid fa-power-off"></i>
                     <span>Déconnexion</span>
                  </button>
               </div>
            </div>
         </header>

         <!-- Dynamic Content Body -->
         <main class="flex-1 overflow-y-auto custom-scrollbar p-8">

            <!-- SECTION VISUALIZER -->

            <!-- Dashboard Home -->
            <div x-show="currentSection === 'dashboard'" class="space-y-8 animate-fade-in">
               <!-- Welcome Banner -->
               <div
                  class="bg-gradient-to-r from-slate-900 via-brand-900 to-slate-900 rounded-[2rem] p-10 text-white relative overflow-hidden shadow-2xl">
                  <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                     <div class="max-w-xl">
                        <h3 class="text-4xl font-bold font-outfit mb-4">Heureux de vous revoir, <span x-text="userPrenom"
                              class="text-brand-400"></span> !</h3>
                        <p class="text-brand-100/70 text-lg leading-relaxed mb-6">Plateforme de gestion BusRapide
                           v2.4. Toutes vos données sont synchronisées en temps réel.</p>
                        <div class="flex flex-wrap gap-4">
                           <button @click="currentSection = 'voyages'"
                              class="px-6 py-3 bg-white text-slate-900 rounded-2xl font-bold shadow-xl hover:scale-105 transition-transform">Explorer
                              les voyages</button>
                           <button @click="currentSection = 'profil'"
                              class="px-6 py-3 bg-brand-500/20 backdrop-blur-md border border-brand-400/30 rounded-2xl font-bold hover:bg-brand-500/30 transition-all">Gérer
                              mon profil</button>
                        </div>
                     </div>
                     <div class="w-48 h-48 bg-brand-400/20 rounded-full blur-3xl absolute -right-10 -top-10"></div>
                     <div class="hidden lg:block">
                        <div class="grid grid-cols-2 gap-4">
                           <template x-for="item in statsFast" :key="item.label">
                              <div class="bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/10 w-40">
                                 <p class="text-[10px] font-bold text-brand-300 uppercase tracking-widest mb-1"
                                    x-text="item.label"></p>
                                 <p class="text-2xl font-bold font-outfit" x-text="item.value"></p>
                              </div>
                           </template>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- KPI Grid -->
               <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                  <template x-for="kpi in kpis" :key="kpi.label">
                     <div
                        class="bg-white p-6 rounded-3xl border border-gray-100 shadow-card hover:shadow-lg transition-all group relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                           <div :class="kpi.color"
                              class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shadow-inner bg-opacity-10">
                              <i :class="kpi.icon"></i>
                           </div>
                           <div class="text-slate-400 text-[10px] font-bold uppercase tracking-widest"
                              x-text="'Temps réel'"></div>
                        </div>
                        <h4 class="text-slate-500 font-medium text-sm mb-1" x-text="kpi.label"></h4>
                        <div class="flex items-end gap-2">
                           <span class="text-3xl font-bold text-slate-900 font-outfit" x-text="kpi.value"></span>
                           <span :class="kpi.trendUp ? 'text-emerald-500' : 'text-rose-500'"
                              class="text-xs font-bold mb-1.5">
                              <i :class="kpi.trendUp ? 'fa-solid fa-arrow-up' : 'fa-solid fa-arrow-down'" class="mr-1"></i>
                              <span x-text="kpi.trendValue + '%'"></span>
                           </span>
                        </div>
                        <div
                           class="absolute -right-2 -bottom-2 opacity-[0.03] text-7xl transition-transform group-hover:scale-110 group-hover:-rotate-12">
                           <i :class="kpi.icon"></i>
                        </div>
                     </div>
                  </template>
               </div>

               <!-- Charts Placeholders or Tables -->
               <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Recent Operations -->
                  <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 shadow-card p-8">
                     <div class="flex items-center justify-between mb-8">
                        <div>
                           <h3 class="text-xl font-bold text-slate-900 font-outfit">Départs aujourd'hui</h3>
                           <p class="text-sm text-slate-500">Liste des voyages programmés pour les prochaines 24h.
                           </p>
                        </div>
                        <button class="text-brand-600 font-bold text-xs hover:underline uppercase tracking-widest">Voir
                           tout le planning</button>
                     </div>
                     <div class="overflow-hidden">
                        <table class="w-full">
                           <thead>
                              <tr
                                 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                 <th class="text-left pb-4">Voyage</th>
                                 <th class="text-left pb-4">Horaire</th>
                                 <th class="text-left pb-4">Occupation</th>
                                 <th class="text-right pb-4">Actions</th>
                              </tr>
                           </thead>
                           <tbody class="divide-y divide-gray-50">
                              <template x-for="v in recentVoyages" :key="v.idVoyage">
                                 <tr class="group">
                                    <td class="py-4">
                                       <div class="flex items-center gap-3">
                                          <div
                                             class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-slate-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                                             <i class="fa-solid fa-shuttle-van"></i>
                                          </div>
                                          <div>
                                             <p class="font-bold text-slate-900 text-sm" x-text="v.trajet">
                                             </p>
                                             <p class="text-xs text-slate-400" x-text="v.bus"></p>
                                          </div>
                                       </div>
                                    </td>
                                    <td class="py-4 font-medium text-slate-600 text-sm" x-text="v.horaire"></td>
                                    <td class="py-4">
                                       <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                          <div class="h-full bg-brand-500 transition-all duration-1000"
                                             :style="'width: ' + v.taux + '%'"></div>
                                       </div>
                                       <p class="text-[10px] font-bold text-slate-400 mt-1" x-text="v.taux + '% rempli'">
                                       </p>
                                    </td>
                                    <td class="py-4 text-right">
                                       <button
                                          class="w-8 h-8 rounded-lg text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition-all">
                                          <i class="fa-solid fa-ellipsis-v"></i>
                                       </button>
                                    </td>
                                 </tr>
                              </template>
                           </tbody>
                        </table>
                     </div>
                  </div>

                  <!-- Right Sidebar Widgets -->
                  <div class="space-y-8">
                     <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-card">
                        <h4 class="text-xl font-bold font-outfit text-slate-900 mb-6 flex items-center gap-3">
                           <span class="w-1.5 h-6 bg-brand-500 rounded-full"></span>
                           Activité en Direct
                        </h4>
                        <div class="space-y-6">
                           <template x-for="act in liveActivity" :key="act.id">
                              <div class="flex gap-4 group cursor-pointer hover:translate-x-1 transition-transform">
                                 <div
                                    class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-brand-50 transition-colors">
                                    <i :class="[act.icon, act.color]" class="text-xl"></i>
                                 </div>
                                 <div class="space-y-1 py-1">
                                    <p class="text-xs font-bold text-slate-800 leading-tight" x-text="act.message"></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                       x-text="act.time"></p>
                                 </div>
                              </div>
                           </template>
                        </div>
                        <button @click="addToast('Historique complet bientôt disponible', 'info')"
                           class="w-full mt-8 py-4 bg-gray-50 text-slate-600 rounded-2xl text-xs font-bold hover:bg-gray-100 transition-colors">
                           Voir tout l'historique
                        </button>
                     </div>

                     <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                        <div class="relative z-10">
                           <h4 class="text-lg font-bold font-outfit mb-2">Centre de Support</h4>
                           <p class="text-sm text-slate-400 mb-6 font-medium leading-relaxed">Une question ? Notre
                              équipe est disponible 24/7 pour vous assister.</p>
                           <button
                              class="w-full py-4 bg-brand-600 rounded-2xl font-bold hover:bg-brand-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-brand-600/20">
                              <i class="fa-solid fa-headset"></i>
                              Assistance 24/7
                           </button>
                        </div>
                        <i
                           class="fa-solid fa-bolt text-9xl absolute -bottom-8 -right-8 text-white/5 group-hover:scale-110 transition-transform duration-700"></i>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Profile Section -->
            <div x-show="currentSection === 'profil'" class="animate-fade-in max-w-4xl mx-auto space-y-8">
               <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-card overflow-hidden">
                  <div class="h-40 bg-gradient-to-r from-brand-600 to-indigo-600 relative">
                     <div class="absolute -bottom-16 left-12">
                        <div class="w-32 h-32 rounded-[2rem] bg-white p-2 shadow-2xl">
                           <div
                              class="w-full h-full rounded-[1.8rem] bg-slate-800 flex items-center justify-center text-white text-4xl font-bold font-outfit overflow-hidden border border-gray-100">
                              <span x-text="userName.charAt(0)"></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="pt-20 px-12 pb-12">
                     <div class="flex justify-between items-start mb-10">
                        <div>
                           <h1 class="text-3xl font-bold text-slate-900 font-outfit" x-text="userName"></h1>
                           <p
                              class="text-slate-500 flex items-center gap-2 mt-1 uppercase text-xs font-bold tracking-widest leading-none">
                              <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                              Membre du personnel : <span x-text="userRole" class="text-brand-600"></span>
                           </p>
                        </div>
                        <button @click="editingProfile = !editingProfile"
                           class="px-6 py-3 rounded-2xl font-bold transition-all"
                           :class="editingProfile ? 'bg-slate-100 text-slate-600' : 'bg-brand-600 text-white shadow-xl shadow-brand-600/20'">
                           <i :class="editingProfile ? 'fa-solid fa-times mr-2' : 'fa-solid fa-pen mr-2'"></i>
                           <span x-text="editingProfile ? 'Annuler' : 'Modifier le profil'"></span>
                        </button>
                     </div>

                     <!-- Profile Info Display / Edit Form -->
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-8">
                           <div class="group">
                              <label
                                 class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Coordonnées
                                 personnelles</label>
                              <div class="space-y-4">
                                 <div
                                    class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-transparent transition-all group-hover:border-gray-200">
                                    <div
                                       class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                       <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div>
                                       <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                          Adresse Email</p>
                                       <p class="font-semibold text-slate-900" x-text="userEmail"></p>
                                    </div>
                                 </div>
                                 <div
                                    class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-transparent transition-all group-hover:border-gray-200">
                                    <div
                                       class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                       <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div>
                                       <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                          Téléphone</p>
                                       <p class="font-semibold text-slate-900" x-text="userPhone || 'Non renseigné'"></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div x-show="editingProfile" class="animate-fade-in">
                           <form @submit.prevent="updateProfile()" class="space-y-6">
                              <div class="grid grid-cols-2 gap-4">
                                 <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-700 ml-1">Prénom</label>
                                    <input type="text" x-model="profileForm.prenom"
                                       class="w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-brand-500 font-medium transition-all">
                                 </div>
                                 <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-700 ml-1">Nom</label>
                                    <input type="text" x-model="profileForm.nom"
                                       class="w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-brand-500 font-medium transition-all">
                                 </div>
                              </div>
                              <div class="space-y-2">
                                 <label class="text-xs font-bold text-slate-700 ml-1">Email professionnel</label>
                                 <input type="email" x-model="profileForm.email"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 focus:ring-2 focus:ring-brand-500 font-medium transition-all">
                              </div>
                              <button type="submit"
                                 class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold shadow-2xl hover:bg-slate-800 transition-all flex items-center justify-center gap-3">
                                 <i x-show="loading" class="fa-solid fa-spinner animate-spin"></i>
                                 Enregistrer les modifications
                              </button>
                           </form>
                        </div>

                        <div x-show="!editingProfile"
                           class="bg-gray-50 rounded-[2rem] p-8 border border-dashed border-gray-200 flex flex-col items-center justify-center text-center">
                           <div
                              class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl mb-4">
                              <i class="fa-solid fa-lock"></i>
                           </div>
                           <h4 class="font-bold text-slate-900 mb-2">Sécurité du compte</h4>
                           <p class="text-xs text-slate-500 mb-6 leading-relaxed">Pensez à changer votre mot de
                              passe régulièrement pour garantir la sécurité de la console.</p>
                           <button @click="currentSection = 'securite'"
                              class="text-brand-600 font-bold text-sm bg-white px-6 py-2 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all">Changer
                              mon mot de passe</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Voyages Management Section -->
            <div x-show="currentSection === 'voyages'" class="animate-fade-in space-y-8">
               <!-- Tabs for Voyages/Buses/Trajets -->
               <div class="flex flex-wrap gap-4 border-b border-gray-200">
                  <button @click="activeVoyageTab = 'list'"
                     :class="activeVoyageTab === 'list' ? 'border-brand-500 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                     class="px-4 py-2 border-b-2 font-bold text-sm transition-all whitespace-nowrap">Liste
                     Voyages</button>
                  <button @click="activeVoyageTab = 'buses'"
                     :class="activeVoyageTab === 'buses' ? 'border-brand-500 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                     class="px-4 py-2 border-b-2 font-bold text-sm transition-all whitespace-nowrap">Parc
                     Bus</button>
                  <button @click="activeVoyageTab = 'trajets'"
                     :class="activeVoyageTab === 'trajets' ? 'border-brand-500 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                     class="px-4 py-2 border-b-2 font-bold text-sm transition-all whitespace-nowrap">Itinéraires</button>
               </div>

               <!-- Voyages List -->
               <div x-show="activeVoyageTab === 'list'" class="space-y-6">
                  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                     <h3 class="text-xl font-bold text-slate-900 font-outfit">Affectations</h3>
                     <button @click="showModal = 'voyage'"
                        class="w-full sm:w-auto px-4 py-2 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-600/20 hover:scale-105 transition-transform">
                        <i class="fa-solid fa-plus mr-2"></i> Nouveau Voyage
                     </button>
                  </div>

                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden overflow-x-auto">
                     <table class="w-full text-left min-w-[700px]">
                        <thead class="bg-gray-50 border-b border-gray-100">
                           <tr>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Trajet</th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Date & Heure</th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Bus</th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Statut</th>
                              <th
                                 class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                 Action</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                           <template x-for="v in voyages" :key="v.idVoyage">
                              <tr class="hover:bg-gray-50/50 transition-colors">
                                 <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900"
                                       x-text="v.trajet.villeDepart + ' → ' + v.trajet.villeArrivee"></p>
                                 </td>
                                 <td class="px-6 py-4 text-sm text-slate-600" x-text="v.dateHeureDepart"></td>
                                 <td class="px-6 py-4">
                                    <span
                                       class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold uppercase"
                                       x-text="v.bus.immatriculation"></span>
                                 </td>
                                 <td class="px-6 py-4">
                                    <span :class="{
                                                                               'bg-emerald-100 text-emerald-700': v.statut === 'planifie',
                                                                               'bg-blue-100 text-blue-700': v.statut === 'en_cours',
                                                                               'bg-slate-100 text-slate-700': v.statut === 'termine'
                                                                           }"
                                       class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                       x-text="v.statut"></span>
                                 </td>
                                 {{-- <td class="px-6 py-4 text-right">
                                    <button class="text-slate-400 hover:text-brand-600 transition-colors">
                                       <i class="fa-solid fa-cog"></i>
                                    </button>
                                 </td> --}}
                                 <td class="px-6 py-4 text-right">
                                    <!-- Menu déroulant -->
                                    <div class="relative inline-block text-left" x-data="{ open: false }"
                                       @click.away="open = false">
                                       <!-- Bouton avec trois points -->
                                       <button @click="open = !open"
                                          class="text-slate-400 hover:text-brand-600 hover:bg-gray-100 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                          <i class="fa-solid fa-ellipsis-v"></i>
                                       </button>

                                       <!-- Menu déroulant -->
                                       <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                          x-transition:enter-start="transform opacity-0 scale-95"
                                          x-transition:enter-end="transform opacity-100 scale-100"
                                          x-transition:leave="transition ease-in duration-75"
                                          x-transition:leave-start="transform opacity-100 scale-100"
                                          x-transition:leave-end="transform opacity-0 scale-95"
                                          class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 py-1">

                                          <!-- Option Modifier -->
                                          <button @click="open = false; editVoyage(v)"
                                             class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-3 transition-colors">
                                             <i class="fa-solid fa-pen-to-square text-sm"></i>
                                             <span>Modifier le voyage</span>
                                          </button>

                                          <!-- Option Annuler -->
                                          <button @click="open = false; annulerVoyage(v)"
                                             class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600 flex items-center gap-3 transition-colors">
                                             <i class="fa-solid fa-ban text-sm"></i>
                                             <span>Annuler le voyage</span>
                                          </button>

                                          <!-- Séparateur -->
                                          <div class="border-t border-gray-100 my-1"></div>

                                          <!-- Option Supprimer -->
                                          <button @click="open = false; confirmDeleteVoyage(v)"
                                             class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors">
                                             <i class="fa-solid fa-trash text-sm"></i>
                                             <span>Supprimer définitivement</span>
                                          </button>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>
               </div>

               <!-- Buses List -->
               <div x-show="activeVoyageTab === 'buses'" class="space-y-6">
                  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                     <h3 class="text-xl font-bold text-slate-900 font-outfit">Parc Mobile</h3>
                     <button @click="showModal = 'bus'"
                        class="w-full sm:w-auto px-4 py-2 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-600/20 hover:scale-105 transition-transform">
                        <i class="fa-solid fa-plus mr-2"></i> Ajouter Bus
                     </button>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                     <template x-for="bus in buses" :key="bus.idBus">
                        <div
                           class="bg-white p-6 rounded-3xl border border-gray-100 shadow-card hover:shadow-lg transition-all">
                           <div class="flex justify-between items-start mb-4">
                              <div
                                 class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500">
                                 <i class="fa-solid fa-bus text-2xl"></i>
                              </div>
                              <span
                                 :class="bus.statut === 'en_service' ? 'text-emerald-500 bg-emerald-50' : 'text-amber-500 bg-amber-50'"
                                 class="px-3 py-1 rounded-full text-[10px] font-bold uppercase"
                                 x-text="bus.statut.replace('_', ' ')"></span>
                           </div>
                           <h4 class="text-lg font-bold text-slate-900" x-text="bus.marque + ' ' + bus.modele">
                           </h4>
                           <p class="text-sm font-medium text-slate-400 mb-4" x-text="bus.immatriculation"></p>
                           <div class="flex justify-between text-xs font-bold border-t border-gray-50 pt-4">
                              <span class="text-slate-400" x-text="bus.capaciteTotale + ' Places'"></span>
                              <span class="text-brand-600" x-text="bus.sieges_v_i_p_count + ' VIP'"></span>
                           </div>
                        </div>
                     </template>
                  </div>
               </div>

               <!-- Trajets List -->
               <div x-show="activeVoyageTab === 'trajets'" class="space-y-6">
                  <div class="flex justify-between items-center">
                     <h3 class="text-xl font-bold text-slate-900 font-outfit">Itinéraires</h3>
                     <button
                        @click="editingTrajet = null; trajetForm = { villeDepart: '', villeArrivee: '', prixStandard: 0, prixVIP: 0, distance: 0, duree: 0 }; showModal = 'trajet'"
                        class="px-4 py-2 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-600/20 hover:scale-105 transition-transform">
                        <i class="fa-solid fa-plus mr-2"></i> Nouveau
                     </button>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <template x-for="t in trajets" :key="t.idTrajet">
                        <div
                           class="bg-white p-6 rounded-3xl border border-gray-100 shadow-card flex flex-col sm:flex-row items-center gap-6">
                           <div class="flex flex-row sm:flex-col gap-2 shrink-0">
                                        <div
                                            class="w-20 h-16 bg-brand-50 rounded-2xl flex flex-col items-center justify-center text-brand-600 font-bold gap-1">
                                            <span class="text-[10px] opacity-60 uppercase">Dist.</span>
                                            <span x-text="t.distance + 'km'"></span>
                                        </div>
                                        <div
                                            class="w-20 h-16 bg-slate-50 rounded-2xl flex flex-col items-center justify-center text-slate-500 font-bold gap-1">
                                            <span class="text-[10px] opacity-60 uppercase">Durée</span>
                                            <span x-text="t.dureeEstimee ? t.dureeEstimee.substring(0, 5) : '--:--'"></span>
                                        </div>
                                    </div>
                           <div class="flex-1 text-center sm:text-left">
                              <h4 class="text-lg font-bold text-slate-900" x-text="t.villeDepart + ' → ' + t.villeArrivee">
                              </h4>
                              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 mt-2">
                                 <p class="text-xs text-brand-600 font-bold"
                                    x-text="new Intl.NumberFormat().format(t.prixStandard) + ' FCFA'"></p>
                                 <div class="flex gap-2">
                                    <button @click="editTrajet(t)"
                                       class="p-2 text-slate-400 hover:text-brand-600 transition-colors">
                                       <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button @click="deleteTrajet(t)"
                                       class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                       <i class="fa-solid fa-trash"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </template>
                  </div>
               </div>
            </div>

            <!-- Modals Integration -->
            <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
               <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = null"></div>
               <div
                  class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden animate-zoom-in max-h-[90vh] overflow-y-auto">
                  <div
                     class="p-6 lg:p-8 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                     <h3 class="text-xl font-bold text-slate-900 font-outfit"
                        x-text="showModal === 'voyage' ? (editingVoyage ? 'Modifier le voyage' : 'Nouveau voyage') : 
                                         showModal === 'trajet' ? (editingTrajet ? 'Modifier l\'itinéraire' : 'Nouvel itinéraire') :
                                         showModal === 'bus' ? 'Nouveau Bus' :
                                         showModal === 'client' ? (editingClient ? 'Modifier le client' : 'Nouveau client') :
                                         showModal === 'personnel' ? (editingPersonnel ? 'Modifier le membre' : 'Nouveau membre') : ''">
                     </h3>
                     <button @click="showModal = null"
                        class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-times"></i>
                     </button>
                  </div>
                  <div class="p-6 lg:p-8">
                     <!-- Bus Form -->
                     <form x-show="showModal === 'bus'" @submit.prevent="saveBus()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Immatriculation</label>
                              <input type="text" x-model="busForm.immatriculation" placeholder="Ex: LT-123-AA"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Statut</label>
                              <select x-model="busForm.statut"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium font-outfit">
                                 <option value="en_service">En Service</option>
                                 <option value="maintenance">Maintenance</option>
                                 <option value="hors_service">Hors Service</option>
                              </select>
                           </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Marque</label>
                              <input type="text" x-model="busForm.marque"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Modèle</label>
                              <input type="text" x-model="busForm.modele"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Capacité
                                 Totale</label>
                              <input type="number" x-model="busForm.capaciteTotale" min="1"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Sièges
                                 VIP</label>
                              <input type="number" x-model="busForm.nbSiegesVIP" min="0"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Date Mise en
                                 Service</label>
                              <input type="date" x-model="busForm.dateMiseEnService"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>
                        <!-- Error Display -->
                        <template x-if="formErrors.bus">
                           <div
                              class="p-3 bg-red-50 text-red-600 rounded-xl text-xs space-y-1 border border-red-100 animate-shake">
                              <template x-for="(err, field) in formErrors.bus" :key="field">
                                 <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="err[0]"></span>
                                 </p>
                              </template>
                           </div>
                        </template>
                        <button type="submit" :disabled="loading"
                           class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold shadow-xl hover:bg-slate-800 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                           <i x-show="loading" class="fa-solid fa-spinner fa-spin"></i>
                           <span x-text="loading ? 'Enregistrement...' : 'Enregistrer le Bus'"></span>
                        </button>
                     </form>

                     <!-- Trajet Form -->
                     <form x-show="showModal === 'trajet'" @submit.prevent="saveTrajet()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Ville
                                 Départ</label>
                              <input type="text" x-model="trajetForm.villeDepart"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Ville
                                 Arrivée</label>
                              <input type="text" x-model="trajetForm.villeArrivee"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prix
                                 Standard</label>
                              <input type="number" x-model="trajetForm.prixStandard"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prix VIP</label>
                              <input type="number" x-model="trajetForm.prixVIP"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Distance
                                 (km)</label>
                              <input type="number" x-model="trajetForm.distance"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Durée
                                 (min)</label>
                              <input type="number" x-model="trajetForm.duree"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3">
                           </div>
                        </div>
                        <!-- Error Display -->
                        <template x-if="formErrors.trajet">
                           <div
                              class="p-3 bg-red-50 text-red-600 rounded-xl text-xs space-y-1 border border-red-100 animate-shake">
                              <template x-for="(err, field) in formErrors.trajet" :key="field">
                                 <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="err[0]"></span>
                                 </p>
                              </template>
                           </div>
                        </template>
                        <button type="submit" :disabled="loading"
                           class="w-full py-4 bg-brand-600 text-white rounded-2xl font-bold hover:bg-brand-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                           <i x-show="loading" class="fa-solid fa-spinner fa-spin"></i>
                           <span
                              x-text="loading ? 'Enregistrement...' : (editingTrajet ? 'Mettre à jour' : 'Ajouter l\'Itinéraire')"></span>
                        </button>
                     </form>

                     <!-- Voyage Form -->
                     <form x-show="showModal === 'voyage'" @submit.prevent="saveVoyage()" class="space-y-4">
                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Itinéraire</label>
                           <select x-model="voyageForm.idTrajet"
                              @change="const t = trajets.find(x => x.idTrajet == $event.target.value); if(t) { voyageForm.prixStandard = t.prixStandard; voyageForm.prixVIP = t.prixVIP; }"
                              class="w-full bg-gray-50 border-none rounded-xl p-3">
                              <option value="">Sélectionner un trajet</option>
                              <template x-for="t in trajets" :key="t.idTrajet">
                                 <option :value="t.idTrajet" x-text="t.villeDepart + ' → ' + t.villeArrivee">
                                 </option>
                              </template>
                           </select>
                        </div>
                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Bus Affecté</label>
                           <select x-model="voyageForm.idBus" class="w-full bg-gray-50 border-none rounded-xl p-3">
                              <template x-for="b in buses.filter(x => x.statut === 'en_service')" :key="b.idBus">
                                 <option :value="b.idBus" x-text="b.immatriculation"></option>
                              </template>
                           </select>
                        </div>
                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Date & Heure</label>
                           <input type="datetime-local" x-model="voyageForm.dateHeureDepart"
                              class="w-full bg-gray-50 border-none rounded-xl p-3">
                        </div>
                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prix Standard
                              (FCFA)</label>
                           <input type="number" x-model.number="voyageForm.prixStandard" min="0"
                              class="w-full bg-gray-50 border-none rounded-xl p-3" placeholder="0">
                        </div>
                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prix VIP
                              (FCFA)</label>
                           <input type="number" x-model.number="voyageForm.prixVIP" min="0"
                              class="w-full bg-gray-50 border-none rounded-xl p-3" placeholder="0">
                        </div>
                        <!-- Error Display -->
                        <template x-if="formErrors.voyage">
                           <div
                              class="p-3 bg-red-50 text-red-600 rounded-xl text-xs space-y-1 border border-red-100 animate-shake">
                              <template x-for="(err, field) in formErrors.voyage" :key="field">
                                 <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="err[0]"></span>
                                 </p>
                              </template>
                           </div>
                        </template>
                        <button type="submit" :disabled="loading"
                           class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                           <i x-show="loading" class="fa-solid fa-spinner fa-spin"></i>
                           <span x-text="loading ? 'Planification...' : 'Planifier le voyage'"></span>
                        </button>
                     </form>

                     <!-- Client Form -->
                     <form x-show="showModal === 'client'" @submit.prevent="saveClient()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prénom</label>
                              <input type="text" x-model="clientForm.prenom" placeholder="Prénom"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Nom</label>
                              <input type="text" x-model="clientForm.nom" placeholder="Nom"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Email</label>
                              <input type="email" x-model="clientForm.email" placeholder="email@exemple.com"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Téléphone</label>
                              <input type="tel" x-model="clientForm.telephone" placeholder="+221 XX XXX XX XX"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Mot de passe</label>
                           <input type="password" x-model="clientForm.motDePasse"
                              :placeholder="editingClient ? 'Laisser vide pour ne pas changer' : 'Mot de passe'"
                              class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Ville</label>
                              <input type="text" x-model="clientForm.ville" placeholder="Ville de résidence"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Statut</label>
                              <select x-model="clientForm.statut"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                                 <option value="actif">Actif</option>
                                 <option value="inactif">Inactif</option>
                              </select>
                           </div>
                        </div>

                        <div class="flex items-center gap-2">
                           <input type="checkbox" x-model="clientForm.estVerifie" id="estVerifie"
                              class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                           <label for="estVerifie" class="text-sm text-slate-700">Compte vérifié</label>
                        </div>

                        <!-- Error Display -->
                        <template x-if="formErrors.client">
                           <div
                              class="p-3 bg-red-50 text-red-600 rounded-xl text-xs space-y-1 border border-red-100 animate-shake">
                              <template x-for="(err, field) in formErrors.client" :key="field">
                                 <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="err[0]"></span>
                                 </p>
                              </template>
                           </div>
                        </template>

                        <button type="submit" :disabled="loading"
                           class="w-full py-4 bg-brand-600 text-white rounded-2xl font-bold shadow-xl shadow-brand-600/20 hover:bg-brand-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                           <i x-show="loading" class="fa-solid fa-spinner fa-spin"></i>
                           <span
                              x-text="loading ? 'Enregistrement...' : (editingClient ? 'Mettre à jour' : 'Créer le client')"></span>
                        </button>
                     </form>

                     <form x-show="showModal === 'personnel'" @submit.prevent="savePersonnel()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Prénom</label>
                              <input type="text" x-model="personnelForm.prenom" placeholder="Prénom"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Nom</label>
                              <input type="text" x-model="personnelForm.nom" placeholder="Nom"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Email</label>
                              <input type="email" x-model="personnelForm.email" placeholder="email@entreprise.com"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Téléphone</label>
                              <input type="tel" x-model="personnelForm.telephone" placeholder="+221 XX XXX XX XX"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Rôle</label>
                              <select x-model="personnelForm.role"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                                 <option value="admin">Administrateur</option>
                                 <option value="super_admin">Super Administrateur</option>
                                 <option value="gestionnaire">Gestionnaire</option>
                                 <option value="controleur">Contrôleur</option>
                                 <option value="conducteur">Conducteur</option>
                              </select>
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Poste</label>
                              <input type="text" x-model="personnelForm.poste" placeholder="Responsable de..."
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Salaire (FCFA)</label>
                              <input type="number" x-model="personnelForm.salaire" placeholder="500000"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Date d'embauche</label>
                              <input type="date" x-model="personnelForm.dateEmbauche"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <div class="space-y-1">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Mot de passe</label>
                           <input type="password" x-model="personnelForm.motDePasse"
                              :placeholder="editingPersonnel ? 'Laisser vide pour ne pas changer' : 'Mot de passe'"
                              class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Statut</label>
                              <select x-model="personnelForm.statut"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                                 <option value="actif">Actif</option>
                                 <option value="inactif">Inactif</option>
                                 <option value="vacances">En vacances</option>
                                 <option value="maladie">Arrêt maladie</option>
                              </select>
                           </div>
                           <div class="space-y-1">
                              <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Matricule</label>
                              <input type="text" x-model="personnelForm.matricule" placeholder="EMP-001"
                                 class="w-full bg-gray-50 border-none rounded-xl p-3 focus:ring-2 focus:ring-brand-500 font-medium">
                           </div>
                        </div>

                        <!-- Permissions -->
                        <div class="space-y-2">
                           <label class="text-[10px] font-bold text-slate-500 uppercase ml-1">Permissions</label>
                           <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                              <template x-for="permission in availablePermissions" :key="permission.value">
                                 <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="personnelForm.permissions" :value="permission.value"
                                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                    <span class="text-sm text-slate-700" x-text="permission.label"></span>
                                 </label>
                              </template>
                           </div>
                        </div>

                        <!-- Error Display -->
                        <template x-if="formErrors.personnel">
                           <div
                              class="p-3 bg-red-50 text-red-600 rounded-xl text-xs space-y-1 border border-red-100 animate-shake">
                              <template x-for="(err, field) in formErrors.personnel" :key="field">
                                 <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span x-text="err[0]"></span>
                                 </p>
                              </template>
                           </div>
                        </template>

                        <button type="submit" :disabled="loading"
                           class="w-full py-4 bg-brand-600 text-white rounded-2xl font-bold shadow-xl shadow-brand-600/20 hover:bg-brand-700 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                           <i x-show="loading" class="fa-solid fa-spinner fa-spin"></i>
                           <span
                              x-text="loading ? 'Enregistrement...' : (editingPersonnel ? 'Mettre à jour' : 'Créer le membre')"></span>
                        </button>
                     </form>
                  </div>
               </div>
            </div>

            <!-- SECTION GESTION DES BILLETS AVEC FILTRES VIP/STANDARD -->
            <div x-show="currentSection === 'tickets'" class="animate-fade-in space-y-8">
               <!-- Statistiques avec VIP et Standard -->
               <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                  <!-- Billets confirmés -->
                  <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Confirmés</p>
                           <p class="text-3xl font-bold font-outfit" x-text="ticketStats.confirmes"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-check text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Billets réservés -->
                  <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Réservés</p>
                           <p class="text-3xl font-bold font-outfit" x-text="ticketStats.reserves"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-clock text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Billets VIP -->
                  <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">VIP</p>
                           <p class="text-3xl font-bold font-outfit" x-text="ticketStats.vip"></p>
                           <p class="text-xs font-bold opacity-90 mt-1" x-text="formatCurrency(ticketStats.vipRevenue)">
                           </p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-crown text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Billets Standard -->
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Standard</p>
                           <p class="text-3xl font-bold font-outfit" x-text="ticketStats.standard"></p>
                           <p class="text-xs font-bold opacity-90 mt-1"
                              x-text="formatCurrency(ticketStats.standardRevenue)"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-chair text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Chiffre d'affaires total -->
                  <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Total CA</p>
                           <p class="text-3xl font-bold font-outfit" x-text="formatCurrency(ticketStats.chiffreAffaire)">
                           </p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-money-bill-wave text-xl"></i>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Barre de contrôle avec filtre classe -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                     <div>
                        <h3 class="text-xl font-bold text-slate-900 font-outfit">Gestion des Billets</h3>
                        <p class="text-sm text-slate-500">Consultez et gérez tous les billets (VIP & Standard)</p>
                     </div>

                     <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Recherche -->
                        <div class="relative flex-1 sm:w-64">
                           <i
                              class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                           <input type="text" x-model="ticketSearchQuery" @input="debouncedSearch()"
                              placeholder="Rechercher par code, client, voyage..."
                              class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>

                        <!-- Filtre statut -->
                        <select x-model="ticketStatusFilter" @change="fetchTickets()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Tous les statuts</option>
                           <option value="en_attente">En attente</option>
                           <option value="reserve">Réservé</option>
                           <option value="confirme">Confirmé</option>
                           <option value="annule">Annulé</option>
                           <option value="utilise">Utilisé</option>
                        </select>

                        <!-- Filtre classe (VIP/Standard) -->
                        <select x-model="ticketClassFilter" @change="fetchTickets()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Toutes les classes</option>
                           <option value="vip">VIP seulement</option>
                           <option value="standard">Standard seulement</option>
                        </select>

                        <!-- Période -->
                        <select x-model="ticketPeriodFilter" @change="fetchTickets()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Toute période</option>
                           <option value="today">Aujourd'hui</option>
                           <option value="week">Cette semaine</option>
                           <option value="month">Ce mois</option>
                        </select>
                     </div>
                  </div>
               </div>

               <!-- Tableau des billets -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
                  <div class="overflow-x-auto">
                     <table class="w-full text-left min-w-[1000px]">
                        <thead class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100">
                           <tr>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 <div class="flex items-center gap-2">
                                    <input type="checkbox" x-model="selectAllTickets" @change="toggleAllTickets()"
                                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                    Code
                                 </div>
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Client
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Voyage
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Détails
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Montant
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Statut
                              </th>
                              <th
                                 class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                 Actions
                              </th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                           <!-- État de chargement -->
                           <template x-if="loadingTickets">
                              <tr>
                                 <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-spinner fa-spin text-3xl text-brand-600"></i>
                                       <p class="text-slate-500">Chargement des billets...</p>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Aucun résultat -->
                           <template x-if="!loadingTickets && filteredTickets().length === 0">
                              <tr>
                                 <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-ticket text-3xl text-slate-300"></i>
                                       <p class="text-slate-500">Aucun billet trouvé</p>
                                       <button
                                          @click="ticketSearchQuery = ''; ticketStatusFilter = 'all'; ticketClassFilter = 'all'; fetchTickets()"
                                          class="text-brand-600 font-bold hover:underline text-sm">
                                          Réinitialiser les filtres
                                       </button>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Liste des billets -->
                           <template x-for="ticket in filteredTickets()" :key="ticket.idTicket">
                              <tr class="hover:bg-gray-50/50 transition-colors group"
                                 :class="{ 'bg-brand-50': selectedTickets.includes(ticket.idTicket) }">
                                 <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                       <input type="checkbox" :value="ticket.idTicket" x-model="selectedTickets"
                                          class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                       <div>
                                          <span class="font-mono text-sm font-bold text-slate-900 block"
                                             x-text="ticket.codeBillet || ticket.numeroBillet"></span>
                                          <span class="text-xs text-slate-400"
                                             x-text="formatDate(ticket.dateAchat || ticket.created_at)"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div>
                                       <p class="font-bold text-slate-900 text-sm"
                                          x-text="ticket.client?.nom + ' ' + ticket.client?.prenom"></p>
                                       <p class="text-xs text-slate-400" x-text="ticket.client?.email"></p>
                                       <p class="text-xs text-slate-400" x-text="ticket.client?.telephone"></p>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div>
                                       <p class="font-bold text-slate-700 text-sm"
                                          x-text="ticket.voyage?.trajet?.villeDepart + ' → ' + ticket.voyage?.trajet?.villeArrivee">
                                       </p>
                                       <div class="flex items-center gap-2 mt-1">
                                          <span class="text-xs text-slate-400"
                                             x-text="formatDateTime(ticket.voyage?.dateHeureDepart)"></span>
                                          <span :class="getTicketClassColor(ticket.classeBillet || ticket.typeSiege)"
                                             class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                             x-text="getTicketClassText(ticket.classeBillet || ticket.typeSiege)"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Bus:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="ticket.voyage?.bus?.immatriculation"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Siège:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="ticket.siege?.numeroSiege || 'N/A'"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Agent:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="ticket.agent?.nom || 'Système'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div>
                                       <span class="font-bold text-slate-900 block"
                                          x-text="formatCurrency(ticket.prixPaye || ticket.prixTotal)"></span>
                                       <template x-if="ticket.modePaiement">
                                          <span class="text-xs text-slate-400 capitalize"
                                             x-text="ticket.modePaiement"></span>
                                       </template>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                       <span :class="getTicketStatusClass(ticket.statut)"
                                          class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-1 justify-center"
                                          x-text="getTicketStatusText(ticket.statut)">
                                       </span>
                                       <template x-if="ticket.statut === 'reserve'">
                                          <span class="text-xs text-amber-600 font-medium"
                                             x-text="'Expire: ' + formatTimeRemaining(ticket.dateExpiration)">
                                          </span>
                                       </template>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                       <!-- Menu déroulant -->
                                       <div class="relative inline-block text-left" x-data="{ open: false }"
                                          @click.away="open = false">
                                          <button @click="open = !open"
                                             class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-gray-100 rounded-lg transition-colors">
                                             <i class="fa-solid fa-ellipsis-v"></i>
                                          </button>

                                          <div x-show="open" x-transition
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 py-1">

                                             <!-- Voir les détails -->
                                             <button @click="open = false; viewTicketDetails(ticket)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Voir détails</span>
                                             </button>

                                             <!-- Confirmer (si réservé/en attente) -->
                                             <template
                                                x-if="ticket.statut === 'reserve' || ticket.statut === 'en_attente'">
                                                <button @click="open = false; confirmTicket(ticket)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-check-circle"></i>
                                                   <span>Confirmer</span>
                                                </button>
                                             </template>

                                             <!-- Marquer comme utilisé (si confirmé) -->
                                             <template x-if="ticket.statut === 'confirme'">
                                                <button @click="open = false; markTicketAsUsed(ticket)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-clipboard-check"></i>
                                                   <span>Marquer utilisé</span>
                                                </button>
                                             </template>

                                             <!-- Annuler -->
                                             <template
                                                x-if="['reserve', 'en_attente', 'confirme'].includes(ticket.statut)">
                                                <button @click="open = false; cancelTicket(ticket)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-ban"></i>
                                                   <span>Annuler</span>
                                                </button>
                                             </template>

                                             <!-- Imprimer -->
                                             <button @click="open = false; printTicket(ticket)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-gray-50 hover:text-slate-900 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-print"></i>
                                                <span>Imprimer</span>
                                             </button>

                                             <!-- Séparateur -->
                                             <div class="border-t border-gray-100 my-1"></div>

                                             <!-- Supprimer -->
                                             <button @click="open = false; deleteTicket(ticket)"
                                                class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                                <span>Supprimer</span>
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>

                  <!-- Pagination -->
                  <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-6 border-t border-gray-100">
                     <div class="text-sm text-slate-500">
                        Affichage de <span
                           x-text="Math.min((currentTicketPage - 1) * ticketsPerPage + 1, totalTickets)"></span>
                        à <span x-text="Math.min(currentTicketPage * ticketsPerPage, totalTickets)"></span>
                        sur <span x-text="totalTickets"></span> billets
                     </div>

                     <div class="flex items-center gap-2">
                        <button @click="prevTicketPage()" :disabled="currentTicketPage === 1"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <template x-for="page in ticketPages" :key="page">
                           <button @click="currentTicketPage = page"
                              :class="currentTicketPage === page ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-gray-50'"
                              class="w-10 h-10 flex items-center justify-center rounded-lg font-medium" x-text="page">
                           </button>
                        </template>

                        <button @click="nextTicketPage()" :disabled="currentTicketPage === totalTicketPages"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-right"></i>
                        </button>
                     </div>

                     <select x-model="ticketsPerPage" @change="fetchTickets()"
                        class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                     </select>
                  </div>
               </div>

               <!-- Actions groupées -->
               <div x-show="selectedTickets.length > 0"
                  class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                     <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center text-brand-600">
                           <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                           <p class="font-bold text-slate-900" x-text="selectedTickets.length + ' billets sélectionnés'">
                           </p>
                           <p class="text-sm text-slate-500">Actions groupées disponibles</p>
                        </div>
                     </div>

                     <div class="flex flex-wrap gap-2">
                        <button @click="batchConfirmTickets()"
                           class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-check"></i>
                           Confirmer
                        </button>

                        <button @click="batchCancelTickets()"
                           class="px-4 py-2 bg-amber-600 text-white rounded-xl font-bold text-sm hover:bg-amber-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-ban"></i>
                           Annuler
                        </button>

                        <button @click="exportTickets()"
                           class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-file-export"></i>
                           Exporter
                        </button>

                        <button @click="selectedTickets = []"
                           class="px-4 py-2 bg-gray-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                           Annuler sélection
                        </button>
                     </div>
                  </div>
               </div>
            </div>

            <!-- SECTION GESTION DES CLIENTS -->
            <div x-show="currentSection === 'clients'" class="animate-fade-in space-y-8">
               <!-- Statistiques clients -->
               <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <!-- Total clients -->
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Total Clients</p>
                           <p class="text-3xl font-bold font-outfit" x-text="clientStats.total"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-users text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Clients actifs -->
                  <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Actifs</p>
                           <p class="text-3xl font-bold font-outfit" x-text="clientStats.actifs"></p>
                           <p class="text-xs font-bold opacity-90 mt-1" x-text="clientStats.actifsPourcentage + '%'"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Nouveaux clients ce mois -->
                  <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Ce mois</p>
                           <p class="text-3xl font-bold font-outfit" x-text="clientStats.nouveauxMois"></p>
                           <p class="text-xs font-bold opacity-90 mt-1">+ <span
                                 x-text="clientStats.variationMois + '%'"></span></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-user-plus text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Chiffre d'affaires clients -->
                  <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">CA Total</p>
                           <p class="text-3xl font-bold font-outfit" x-text="formatCurrency(clientStats.chiffreAffaire)">
                           </p>
                           <p class="text-xs font-bold opacity-90 mt-1">Moyenne: <span
                                 x-text="formatCurrency(clientStats.caMoyen)"></span></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-money-bill-wave text-xl"></i>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Barre de contrôle -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                     <div>
                        <h3 class="text-xl font-bold text-slate-900 font-outfit">Gestion des Clients</h3>
                        <p class="text-sm text-slate-500">Consultez et gérez tous les clients de la plateforme</p>
                     </div>

                     <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Recherche -->
                        <div class="relative flex-1 sm:w-64">
                           <i
                              class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                           <input type="text" x-model="clientSearchQuery" @input="debouncedClientSearch()"
                              placeholder="Rechercher par nom, email, téléphone..."
                              class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>

                        <!-- Filtre statut -->
                        <select x-model="clientStatusFilter" @change="fetchClients()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Tous les statuts</option>
                           <option value="actif">Actifs</option>
                           <option value="inactif">Inactifs</option>
                           <option value="verifie">Vérifiés</option>
                           <option value="non_verifie">Non vérifiés</option>
                        </select>

                        <!-- Nouveau client -->
                        <button @click="showModal = 'client'"
                           class="px-6 py-3 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-600/20 hover:scale-105 transition-transform flex items-center gap-2">
                           <i class="fa-solid fa-plus"></i>
                           Nouveau Client
                        </button>
                     </div>
                  </div>
               </div>

               <!-- Tableau des clients -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
                  <div class="overflow-x-auto">
                     <table class="w-full text-left min-w-[1000px]">
                        <thead class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100">
                           <tr>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 <div class="flex items-center gap-2">
                                    <input type="checkbox" x-model="selectAllClients" @change="toggleAllClients()"
                                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                    Client
                                 </div>
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Contact
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Informations
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Statistiques
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Statut
                              </th>
                              <th
                                 class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                 Actions
                              </th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                           <!-- État de chargement -->
                           <template x-if="loadingClients">
                              <tr>
                                 <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-spinner fa-spin text-3xl text-brand-600"></i>
                                       <p class="text-slate-500">Chargement des clients...</p>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Aucun résultat -->
                           <template x-if="!loadingClients && filteredClients().length === 0">
                              <tr>
                                 <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-users text-3xl text-slate-300"></i>
                                       <p class="text-slate-500">Aucun client trouvé</p>
                                       <button @click="clientSearchQuery = ''; clientStatusFilter = 'all'; fetchClients()"
                                          class="text-brand-600 font-bold hover:underline text-sm">
                                          Réinitialiser les filtres
                                       </button>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Liste des clients -->
                           <template x-for="client in filteredClients()" :key="client.id_client">
                              <tr class="hover:bg-gray-50/50 transition-colors group"
                                 :class="{ 'bg-brand-50': selectedClients.includes(client.id_client) }">
                                 <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                       <input type="checkbox" :value="client.id_client" x-model="selectedClients"
                                          class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                       <div
                                          class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                                          <span class="font-bold text-slate-700"
                                             x-text="(client.prenom?.charAt(0) || '') + (client.nom?.charAt(0) || '')"></span>
                                       </div>
                                       <div>
                                          <p class="font-bold text-slate-900 text-sm"
                                             x-text="client.prenom + ' ' + client.nom"></p>
                                          <p class="text-xs text-slate-400" x-text="'ID: ' + client.codeClient"></p>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
                                          <span class="text-sm text-slate-700" x-text="client.email"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                                          <span class="text-sm text-slate-700"
                                             x-text="client.telephone || 'Non renseigné'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Inscription:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="formatDate(client.dateInscription)"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Dernière connexion:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="client.dateDerniereConnexion ? formatDate(client.dateDerniereConnexion) : 'Jamais'"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Ville:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="client.ville || 'Non spécifiée'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Voyages:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="client.nbVoyages || 0"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Total dépensé:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="formatCurrency(client.totalDepense || 0)"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-2">
                                       <span :class="getClientStatusClass(client.statut)"
                                          class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block text-center min-w-[80px]"
                                          x-text="getClientStatusText(client.statut)">
                                       </span>
                                       <div x-show="client.estVerifie === 1" class="flex items-center gap-1">
                                          <i class="fa-solid fa-badge-check text-emerald-500 text-xs"></i>
                                          <span class="text-xs text-emerald-600 font-medium">Vérifié</span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                       <!-- Menu déroulant -->
                                       <div class="relative inline-block text-left" x-data="{ open: false }"
                                          @click.away="open = false">
                                          <button @click="open = !open"
                                             class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-gray-100 rounded-lg transition-colors">
                                             <i class="fa-solid fa-ellipsis-v"></i>
                                          </button>

                                          <div x-show="open" x-transition
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 py-1">

                                             <!-- Voir profil -->
                                             <button @click="open = false; viewClientDetails(client)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Voir profil</span>
                                             </button>

                                             <!-- Modifier -->
                                             <button @click="open = false; editClient(client)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Modifier</span>
                                             </button>

                                             <!-- Vérifier/Désactiver -->
                                             <template x-if="client.estVerifie === 0">
                                                <button @click="open = false; verifyClient(client)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-check-circle"></i>
                                                   <span>Vérifier</span>
                                                </button>
                                             </template>
                                             <template x-if="client.statut === 'actif'">
                                                <button @click="open = false; toggleClientStatus(client)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-user-slash"></i>
                                                   <span>Désactiver</span>
                                                </button>
                                             </template>
                                             <template x-if="client.statut === 'inactif'">
                                                <button @click="open = false; toggleClientStatus(client)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-user-check"></i>
                                                   <span>Activer</span>
                                                </button>
                                             </template>

                                             <!-- Voir historique -->
                                             <button @click="open = false; viewClientHistory(client)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-purple-50 hover:text-purple-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-history"></i>
                                                <span>Historique</span>
                                             </button>

                                             <!-- Séparateur -->
                                             <div class="border-t border-gray-100 my-1"></div>

                                             <!-- Supprimer -->
                                             <button @click="open = false; deleteClient(client)"
                                                class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                                <span>Supprimer</span>
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>

                  <!-- Pagination -->
                  <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-6 border-t border-gray-100">
                     <div class="text-sm text-slate-500">
                        Affichage de <span
                           x-text="Math.min((currentClientPage - 1) * clientsPerPage + 1, totalClients)"></span>
                        à <span x-text="Math.min(currentClientPage * clientsPerPage, totalClients)"></span>
                        sur <span x-text="totalClients"></span> clients
                     </div>

                     <div class="flex items-center gap-2">
                        <button @click="prevClientPage()" :disabled="currentClientPage === 1"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <template x-for="page in clientPages" :key="page">
                           <button @click="currentClientPage = page"
                              :class="currentClientPage === page ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-gray-50'"
                              class="w-10 h-10 flex items-center justify-center rounded-lg font-medium" x-text="page">
                           </button>
                        </template>

                        <button @click="nextClientPage()" :disabled="currentClientPage === totalClientPages"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-right"></i>
                        </button>
                     </div>

                     <select x-model="clientsPerPage" @change="fetchClients()"
                        class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                     </select>

                     <!-- Ajoutez ceci temporairement dans la section clients -->
                     <button @click="debugClients()" class="px-4 py-2 bg-red-500 text-white rounded-lg">
                        Debug Pagination
                     </button>
                  </div>
               </div>

               <!-- Actions groupées -->
               <div x-show="selectedClients.length > 0"
                  class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                     <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center text-brand-600">
                           <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                           <p class="font-bold text-slate-900" x-text="selectedClients.length + ' clients sélectionnés'">
                           </p>
                           <p class="text-sm text-slate-500">Actions groupées disponibles</p>
                        </div>
                     </div>

                     <div class="flex flex-wrap gap-2">
                        <button @click="batchVerifyClients()"
                           class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-check"></i>
                           Vérifier
                        </button>

                        <button @click="batchActivateClients()"
                           class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-user-check"></i>
                           Activer
                        </button>

                        <button @click="exportClients()"
                           class="px-4 py-2 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-file-export"></i>
                           Exporter
                        </button>

                        <button @click="selectedClients = []"
                           class="px-4 py-2 bg-gray-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                           Annuler sélection
                        </button>
                     </div>
                  </div>
               </div>
            </div>

            <!-- SECTION CONSOLE D'EMBARQUEMENT -->
            <div x-show="currentSection === 'embarquement'" class="animate-fade-in space-y-8">
               <!-- En-tête avec statistiques en direct -->
               <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                  <div>
                     <h3 class="text-2xl font-bold text-slate-900 font-outfit">Console d'Embarquement</h3>
                     <p class="text-sm text-slate-500">Validez les billets, gérez les présences et surveillez les départs
                        en direct</p>
                  </div>

                  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                     <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">À embarquer</p>
                        <p class="text-2xl font-bold text-slate-900" x-text="embarquementStats.aEmbarquer"></p>
                     </div>
                     <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Embarkés</p>
                        <p class="text-2xl font-bold text-emerald-600" x-text="embarquementStats.embarques"></p>
                     </div>
                     <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Retards</p>
                        <p class="text-2xl font-bold text-amber-600" x-text="embarquementStats.retards"></p>
                     </div>
                     <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Absents</p>
                        <p class="text-2xl font-bold text-red-600" x-text="embarquementStats.absents"></p>
                     </div>
                  </div>
               </div>

               <!-- Barre de contrôle principale -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col lg:flex-row gap-6">
                     <!-- Recherche rapide par QR/Code -->
                     <div class="flex-1">
                        <div class="relative">
                           <i
                              class="fa-solid fa-qrcode absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                           <input type="text" x-model="scanInput" @keyup.enter="scanBillet()" @input="clearScanFeedback()"
                              placeholder="Scanner un QR code ou saisir le numéro de billet..."
                              class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-base focus:ring-4 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                              :class="scanFeedback.type ? (scanFeedback.type === 'success' ? 'border-emerald-500' : 'border-red-500') : ''">
                           <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                              <span class="text-xs font-bold text-slate-400">ENTER pour valider</span>
                           </div>
                        </div>

                        <!-- Feedback scan -->
                        <div x-show="scanFeedback.message" class="mt-3 animate-fade-in">
                           <div
                              :class="scanFeedback.type === 'success' ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'"
                              class="p-3 rounded-xl border">
                              <div class="flex items-center gap-3">
                                 <i :class="scanFeedback.type === 'success' ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-circle-xmark text-red-500'"
                                    class="text-lg"></i>
                                 <div>
                                    <p class="font-bold text-slate-900" x-text="scanFeedback.message"></p>
                                    <template x-if="scanFeedback.details">
                                       <p class="text-sm text-slate-600 mt-1" x-text="scanFeedback.details"></p>
                                    </template>
                                 </div>
                                 <button @click="clearScanFeedback()" class="ml-auto text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-times"></i>
                                 </button>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Boutons d'action -->
                     <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="openScannerModal()"
                           class="px-6 py-4 bg-brand-600 text-white rounded-2xl font-bold hover:bg-brand-700 transition-all flex items-center justify-center gap-3 shadow-lg shadow-brand-600/20">
                           <i class="fa-solid fa-camera"></i>
                           Scanner QR
                        </button>
                        <button @click="showVoyagesProches = !showVoyagesProches"
                           :class="showVoyagesProches ? 'bg-slate-900 text-white' : 'bg-gray-100 text-slate-700'"
                           class="px-6 py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-3">
                           <i class="fa-solid fa-clock"></i>
                           <span x-text="showVoyagesProches ? 'Masquer' : 'Afficher'"></span>
                        </button>
                     </div>
                  </div>
               </div>

               <!-- Voyages à proximité -->
               <div x-show="showVoyagesProches" class="animate-fade-in">
                  <div class="bg-gradient-to-r from-slate-900 to-brand-900 rounded-3xl p-6 text-white">
                     <div class="flex justify-between items-center mb-6">
                        <div>
                           <h4 class="text-xl font-bold font-outfit mb-1">Départs imminents</h4>
                           <p class="text-brand-200/70">Voyages programmés dans les 2 prochaines heures</p>
                        </div>
                        <span class="px-4 py-2 bg-white/10 rounded-full text-sm font-bold">
                           <i class="fa-solid fa-clock mr-2"></i>
                           <span
                              x-text="new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})"></span>
                        </span>
                     </div>

                     <div class="overflow-x-auto">
                        <div class="flex gap-4 pb-4 min-w-max">
                           <template x-for="voyage in voyagesProches" :key="voyage.idVoyage">
                              <div
                                 class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 min-w-[300px]">
                                 <div class="flex justify-between items-start mb-4">
                                    <div>
                                       <h5 class="font-bold text-lg mb-1"
                                          x-text="voyage.trajet.villeDepart + ' → ' + voyage.trajet.villeArrivee"></h5>
                                       <p class="text-brand-200/70 text-sm" x-text="'Bus: ' + voyage.bus.immatriculation">
                                       </p>
                                    </div>
                                    <span :class="getDepartureStatusClass(voyage)"
                                       class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                       <span x-text="getDepartureStatusText(voyage)"></span>
                                    </span>
                                 </div>

                                 <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                       <span class="text-sm text-brand-200">Départ:</span>
                                       <span class="font-bold text-lg" x-text="formatTime(voyage.dateHeureDepart)"></span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                       <span class="text-sm text-brand-200">Passagers:</span>
                                       <div class="flex items-center gap-4">
                                          <span class="font-bold" x-text="voyage.embarques"></span>
                                          <span class="text-brand-200">/</span>
                                          <span x-text="voyage.capacite"></span>
                                       </div>
                                    </div>

                                    <div class="w-full bg-white/20 rounded-full h-2">
                                       <div class="bg-emerald-400 h-2 rounded-full transition-all duration-500"
                                          :style="'width: ' + (voyage.embarques / voyage.capacite * 100) + '%'"></div>
                                    </div>

                                    <button @click="selectVoyageEmbarquement(voyage)"
                                       class="w-full mt-4 py-3 bg-white text-slate-900 rounded-xl font-bold hover:bg-gray-100 transition-all flex items-center justify-center gap-2">
                                       <i class="fa-solid fa-clipboard-check"></i>
                                       Gérer l'embarquement
                                    </button>
                                 </div>
                              </div>
                           </template>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Interface principale : Liste des passagers -->
               <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Liste des passagers -->
                  <div class="lg:col-span-2">
                     <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
                        <!-- En-tête avec filtres -->
                        <div class="p-6 border-b border-gray-100">
                           <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                              <div>
                                 <h4 class="text-lg font-bold text-slate-900 font-outfit"
                                    x-text="selectedVoyage ? 'Passagers - ' + selectedVoyage.trajet.villeDepart + ' → ' + selectedVoyage.trajet.villeArrivee : 'Sélectionnez un voyage'">
                                 </h4>
                                 <p class="text-sm text-slate-500"
                                    x-text="selectedVoyage ? 'Départ: ' + formatDateTime(selectedVoyage.dateHeureDepart) : ''">
                                 </p>
                              </div>

                              <div class="flex flex-wrap gap-3">
                                 <select x-model="passagerFilter" @change="filterPassagers()"
                                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                    <option value="all">Tous les passagers</option>
                                    <option value="a_embarquer">À embarquer</option>
                                    <option value="embarque">Embarkés</option>
                                    <option value="absent">Absents</option>
                                    <option value="retard">En retard</option>
                                 </select>

                                 <select x-model="passagerClassFilter" @change="filterPassagers()"
                                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                    <option value="all">Toutes classes</option>
                                    <option value="vip">VIP</option>
                                    <option value="standard">Standard</option>
                                 </select>

                                 <button @click="exportListeEmbarquement()" :disabled="!selectedVoyage"
                                    class="px-4 py-2 bg-gray-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-gray-200 disabled:opacity-50 transition-all flex items-center gap-2">
                                    <i class="fa-solid fa-file-export"></i>
                                    Exporter
                                 </button>
                              </div>
                           </div>
                        </div>

                        <!-- Liste des passagers -->
                        <div class="overflow-y-auto max-h-[600px]">
                           <template x-if="!selectedVoyage">
                              <div class="p-12 text-center">
                                 <div
                                    class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-bus text-3xl text-slate-400"></i>
                                 </div>
                                 <h5 class="text-lg font-bold text-slate-700 mb-2">Aucun voyage sélectionné</h5>
                                 <p class="text-slate-500 mb-6">Sélectionnez un voyage dans la liste de droite pour gérer
                                    l'embarquement</p>
                                 <button @click="showVoyagesProches = true"
                                    class="px-6 py-3 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition-all">
                                    Voir les départs imminents
                                 </button>
                              </div>
                           </template>

                           <template x-if="selectedVoyage && filteredPassagers.length === 0">
                              <div class="p-12 text-center">
                                 <i class="fa-solid fa-users text-4xl text-slate-300 mb-4"></i>
                                 <h5 class="text-lg font-bold text-slate-700 mb-2">Aucun passager trouvé</h5>
                                 <p class="text-slate-500">Aucun billet n'a été émis pour ce voyage ou les filtres ne
                                    correspondent à aucun résultat</p>
                              </div>
                           </template>

                           <template x-if="selectedVoyage && filteredPassagers.length > 0">
                              <div class="divide-y divide-gray-100">
                                 <template x-for="passager in filteredPassagers" :key="passager.idTicket">
                                    <div class="p-6 hover:bg-gray-50/50 transition-colors group" :class="{
                                                         'bg-emerald-50/30': passager.statutEmbarquement === 'embarque',
                                                         'bg-amber-50/30': passager.statutEmbarquement === 'retard',
                                                         'bg-red-50/30': passager.statutEmbarquement === 'absent'
                                                      }">
                                       <div class="flex items-center gap-4">
                                          <!-- Statut -->
                                          <div class="relative">
                                             <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                                :class="getPassagerStatusClass(passager)">
                                                <i :class="getPassagerStatusIcon(passager)" class="text-lg"></i>
                                             </div>
                                             <template x-if="passager.typeSiege === 'vip'">
                                                <div
                                                   class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                                                   <i class="fa-solid fa-crown text-[8px] text-white"></i>
                                                </div>
                                             </template>
                                          </div>

                                          <!-- Infos passager -->
                                          <div class="flex-1 min-w-0">
                                             <div class="flex justify-between items-start">
                                                <div>
                                                   <h6 class="font-bold text-slate-900 text-sm mb-1"
                                                      x-text="passager.client.prenom + ' ' + passager.client.nom"></h6>
                                                   <div class="flex items-center gap-3 text-xs text-slate-500">
                                                      <span x-text="passager.codeBillet"></span>
                                                      <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                      <span x-text="'Siège ' + passager.siege.numeroSiege"></span>
                                                      <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                      <span
                                                         :class="passager.typeSiege === 'vip' ? 'text-amber-600 font-bold' : 'text-slate-500'"
                                                         x-text="passager.typeSiege.toUpperCase()"></span>
                                                   </div>
                                                </div>
                                                <div class="text-right">
                                                   <p class="text-sm font-bold text-slate-900"
                                                      x-text="formatCurrency(passager.prixPaye)"></p>
                                                   <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider"
                                                      x-text="passager.modePaiement"></p>
                                                </div>
                                             </div>

                                             <!-- Informations supplémentaires -->
                                             <div class="mt-3 flex flex-wrap gap-4">
                                                <div class="flex items-center gap-2">
                                                   <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
                                                   <span class="text-xs text-slate-600"
                                                      x-text="passager.client.email"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                   <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                                                   <span class="text-xs text-slate-600"
                                                      x-text="passager.client.telephone || 'Non renseigné'"></span>
                                                </div>
                                                <template x-if="passager.dateEmbarquement">
                                                   <div class="flex items-center gap-2">
                                                      <i class="fa-solid fa-clock text-slate-400 text-xs"></i>
                                                      <span class="text-xs text-slate-600"
                                                         x-text="'Embarqué à ' + formatTime(passager.dateEmbarquement)"></span>
                                                   </div>
                                                </template>
                                             </div>
                                          </div>

                                          <!-- Actions -->
                                          <div class="flex items-center gap-2">
                                             <template x-if="passager.statutEmbarquement === 'a_embarquer'">
                                                <button @click="validerEmbarquement(passager)"
                                                   class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                                                   <i class="fa-solid fa-check"></i>
                                                   Valider
                                                </button>
                                             </template>

                                             <template x-if="passager.statutEmbarquement === 'embarque'">
                                                <button @click="annulerEmbarquement(passager)"
                                                   class="px-4 py-2 bg-amber-600 text-white rounded-xl font-bold text-sm hover:bg-amber-700 transition-all flex items-center gap-2">
                                                   <i class="fa-solid fa-rotate-left"></i>
                                                   Annuler
                                                </button>
                                             </template>

                                             <!-- Menu déroulant -->
                                             <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                                <button @click="open = !open"
                                                   class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-gray-100 rounded-xl transition-colors">
                                                   <i class="fa-solid fa-ellipsis-v"></i>
                                                </button>

                                                <div x-show="open" x-transition
                                                   class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 py-1">

                                                   <!-- Marquer retard -->
                                                   <template x-if="passager.statutEmbarquement !== 'retard'">
                                                      <button @click="open = false; marquerRetard(passager)"
                                                         class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600 flex items-center gap-3 transition-colors">
                                                         <i class="fa-solid fa-clock"></i>
                                                         <span>Marquer retard</span>
                                                      </button>
                                                   </template>

                                                   <!-- Marquer absent -->
                                                   <template x-if="passager.statutEmbarquement !== 'absent'">
                                                      <button @click="open = false; marquerAbsent(passager)"
                                                         class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-red-50 hover:text-red-600 flex items-center gap-3 transition-colors">
                                                         <i class="fa-solid fa-user-slash"></i>
                                                         <span>Marquer absent</span>
                                                      </button>
                                                   </template>

                                                   <!-- Voir billet -->
                                                   <button @click="open = false; voirBillet(passager)"
                                                      class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-3 transition-colors">
                                                      <i class="fa-solid fa-ticket"></i>
                                                      <span>Voir billet</span>
                                                   </button>

                                                   <!-- Imprimer -->
                                                   <button @click="open = false; imprimerBillet(passager)"
                                                      class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-gray-50 hover:text-slate-900 flex items-center gap-3 transition-colors">
                                                      <i class="fa-solid fa-print"></i>
                                                      <span>Imprimer</span>
                                                   </button>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </template>
                              </div>
                           </template>
                        </div>
                     </div>
                  </div>

                  <!-- Panneau latéral : Sélection voyage & statistiques -->
                  <div class="space-y-8">
                     <!-- Sélection du voyage -->
                     <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                        <h4 class="text-lg font-bold text-slate-900 font-outfit mb-6 flex items-center gap-3">
                           <i class="fa-solid fa-bus text-brand-600"></i>
                           Sélection du voyage
                        </h4>

                        <div class="space-y-4">
                           <div class="relative">
                              <i
                                 class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                              <input type="text" x-model="voyageSearch" @input="filterVoyages()"
                                 placeholder="Rechercher un voyage..."
                                 class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                           </div>

                           <div class="space-y-2 max-h-[400px] overflow-y-auto">
                              <template x-for="voyage in filteredVoyagesList" :key="voyage.idVoyage">
                                 <button @click="selectVoyageEmbarquement(voyage)"
                                    :class="selectedVoyage?.idVoyage === voyage.idVoyage ? 'bg-brand-50 border-brand-500 text-brand-700' : 'hover:bg-gray-50 border-transparent text-slate-700'"
                                    class="w-full text-left p-4 rounded-xl border transition-all group">
                                    <div class="flex justify-between items-start mb-2">
                                       <div>
                                          <p class="font-bold"
                                             x-text="voyage.trajet.villeDepart + ' → ' + voyage.trajet.villeArrivee"></p>
                                          <p class="text-sm text-slate-500"
                                             x-text="formatDateTime(voyage.dateHeureDepart)"></p>
                                       </div>
                                       <span :class="getVoyageStatusClass(voyage)"
                                          class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider"
                                          x-text="getVoyageStatusText(voyage)"></span>
                                    </div>

                                    <div class="flex justify-between items-center text-xs">
                                       <div class="flex items-center gap-4">
                                          <span class="text-slate-500">
                                             <i class="fa-solid fa-bus mr-1"></i>
                                             <span x-text="voyage.bus.immatriculation"></span>
                                          </span>
                                          <span class="text-slate-500">
                                             <i class="fa-solid fa-users mr-1"></i>
                                             <span x-text="voyage.embarques + '/' + voyage.capacite"></span>
                                          </span>
                                       </div>
                                       <i
                                          class="fa-solid fa-chevron-right text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                 </button>
                              </template>
                           </div>
                        </div>
                     </div>

                     <!-- Statistiques du voyage sélectionné -->
                     <template x-if="selectedVoyage">
                        <div class="bg-gradient-to-br from-slate-900 to-brand-900 rounded-3xl p-6 text-white">
                           <h4 class="text-lg font-bold font-outfit mb-6">Statistiques du voyage</h4>

                           <div class="space-y-6">
                              <!-- Progression -->
                              <div>
                                 <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-brand-200">Taux d'embarquement</span>
                                    <span class="font-bold"
                                       x-text="Math.round((selectedVoyage.embarques / selectedVoyage.capacite) * 100) + '%'"></span>
                                 </div>
                                 <div class="w-full bg-white/20 rounded-full h-3">
                                    <div class="bg-emerald-400 h-3 rounded-full transition-all duration-700"
                                       :style="'width: ' + (selectedVoyage.embarques / selectedVoyage.capacite * 100) + '%'">
                                    </div>
                                 </div>
                              </div>

                              <!-- Répartition -->
                              <div class="grid grid-cols-2 gap-4">
                                 <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <p class="text-[10px] font-bold text-brand-300 uppercase tracking-widest mb-1">VIP</p>
                                    <p class="text-2xl font-bold" x-text="selectedVoyage.stats?.vip || 0"></p>
                                 </div>
                                 <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <p class="text-[10px] font-bold text-brand-300 uppercase tracking-widest mb-1">Standard
                                    </p>
                                    <p class="text-2xl font-bold" x-text="selectedVoyage.stats?.standard || 0"></p>
                                 </div>
                              </div>

                              <!-- Détails temporels -->
                              <div class="space-y-3">
                                 <div class="flex justify-between items-center">
                                    <span class="text-sm text-brand-200">Départ dans:</span>
                                    <span class="font-bold" x-text="getTimeUntilDeparture(selectedVoyage)"></span>
                                 </div>
                                 <div class="flex justify-between items-center">
                                    <span class="text-sm text-brand-200">Porte d'embarquement:</span>
                                    <span class="font-bold" x-text="selectedVoyage.porte || 'A1'"></span>
                                 </div>
                              </div>

                              <!-- Actions rapides -->
                              <div class="pt-4 border-t border-white/10">
                                 <button @click="validerTousEmbarquements()"
                                    class="w-full mb-3 py-3 bg-emerald-500 text-white rounded-xl font-bold hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check-double"></i>
                                    Valider tous
                                 </button>
                                 <button @click="annulerTousEmbarquements()"
                                    class="w-full py-3 bg-red-500/20 text-red-300 border border-red-500/30 rounded-xl font-bold hover:bg-red-500/30 transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-ban"></i>
                                    Annuler tous
                                 </button>
                              </div>
                           </div>
                        </div>
                     </template>

                     <!-- Guide rapide -->
                     <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6">
                        <h4 class="text-lg font-bold text-slate-900 font-outfit mb-4 flex items-center gap-3">
                           <i class="fa-solid fa-circle-info text-amber-600"></i>
                           Guide d'embarquement
                        </h4>

                        <div class="space-y-3">
                           <div class="flex items-start gap-3">
                              <i class="fa-solid fa-qrcode text-amber-600 mt-1"></i>
                              <div>
                                 <p class="text-sm font-bold text-slate-900">Scanner un QR code</p>
                                 <p class="text-xs text-slate-600">Utilisez le scanner ou saisissez manuellement le code du
                                    billet</p>
                              </div>
                           </div>
                           <div class="flex items-start gap-3">
                              <i class="fa-solid fa-users text-amber-600 mt-1"></i>
                              <div>
                                 <p class="text-sm font-bold text-slate-900">Gestion des statuts</p>
                                 <p class="text-xs text-slate-600">Marquez les retards et absents pour une meilleure
                                    gestion</p>
                              </div>
                           </div>
                           <div class="flex items-start gap-3">
                              <i class="fa-solid fa-file-export text-amber-600 mt-1"></i>
                              <div>
                                 <p class="text-sm font-bold text-slate-900">Export des données</p>
                                 <p class="text-xs text-slate-600">Exportez la liste des passagers pour archivage</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Modal Scanner QR Code -->
            <div x-show="showScannerModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
               <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showScannerModal = false"></div>
               <div
                  class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden animate-zoom-in">
                  <div class="p-6 lg:p-8 border-b border-gray-100 flex justify-between items-center">
                     <h3 class="text-xl font-bold text-slate-900 font-outfit">Scanner QR Code</h3>
                     <button @click="showScannerModal = false"
                        class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-times"></i>
                     </button>
                  </div>

                  <div class="p-8 text-center">
                     <!-- Zone de scanner (simulée) -->
                     <div class="w-full max-w-md mx-auto bg-gray-900 rounded-2xl overflow-hidden relative">
                        <div class="aspect-square relative">
                           <!-- Simulation du scanner -->
                           <div class="absolute inset-0 flex items-center justify-center">
                              <div class="w-64 h-64 border-4 border-brand-500 rounded-lg relative overflow-hidden">
                                 <!-- Animation de scan -->
                                 <div class="absolute inset-x-0 top-0 h-1 bg-brand-500 animate-ping"></div>
                                 <!-- QR placeholder -->
                                 <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="fa-solid fa-qrcode text-4xl text-white/20"></i>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="p-6 bg-gray-800">
                           <p class="text-white font-bold mb-2">Positionnez le QR code dans le cadre</p>
                           <p class="text-gray-400 text-sm">Le scan s'effectue automatiquement</p>
                        </div>
                     </div>

                     <div class="mt-8">
                        <button @click="simulateQRScan()"
                           class="px-8 py-4 bg-brand-600 text-white rounded-2xl font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-600/20 flex items-center gap-3 mx-auto">
                           <i class="fa-solid fa-camera"></i>
                           Simuler un scan (démo)
                        </button>

                        <p class="text-sm text-slate-500 mt-4">
                           <i class="fa-solid fa-lightbulb mr-2 text-amber-500"></i>
                           En environnement réel, cette fonctionnalité utiliserait l'API de votre appareil
                        </p>
                     </div>
                  </div>
               </div>
            </div>


            <div x-show="currentSection === 'personnel'" class="animate-fade-in space-y-8">
               <!-- Statistiques personnel -->
               <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <!-- Total employés -->
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Total Employés</p>
                           <p class="text-3xl font-bold font-outfit" x-text="personnelStats.total"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-users text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Actifs -->
                  <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Actifs</p>
                           <p class="text-3xl font-bold font-outfit" x-text="personnelStats.actifs"></p>
                           <p class="text-xs font-bold opacity-90 mt-1" x-text="personnelStats.actifsPourcentage + '%'">
                           </p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Administrateurs -->
                  <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Administrateurs</p>
                           <p class="text-3xl font-bold font-outfit" x-text="personnelStats.admins"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-user-shield text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Contrôleurs -->
                  <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Contrôleurs</p>
                           <p class="text-3xl font-bold font-outfit" x-text="personnelStats.controleurs"></p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-clipboard-check text-xl"></i>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Barre de contrôle -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                     <div>
                        <h3 class="text-xl font-bold text-slate-900 font-outfit">Gestion du Personnel</h3>
                        <p class="text-sm text-slate-500">Consultez et gérez tous les membres de l'équipe</p>
                     </div>

                     <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Recherche -->
                        <div class="relative flex-1 sm:w-64">
                           <i
                              class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                           <input type="text" x-model="personnelSearchQuery" @input="debouncedPersonnelSearch()"
                              placeholder="Rechercher par nom, email, rôle..."
                              class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>

                        <!-- Filtre rôle -->
                        <select x-model="personnelRoleFilter" @change="fetchPersonnel()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Tous les rôles</option>
                           <option value="admin">Administrateurs</option>
                           <option value="super_admin">Super Admin</option>
                           <option value="gestionnaire">Gestionnaires</option>
                           <option value="controleur">Contrôleurs</option>
                           <option value="conducteur">Conducteurs</option>
                        </select>

                        <!-- Filtre statut -->
                        <select x-model="personnelStatusFilter" @change="fetchPersonnel()"
                           class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-brand-500">
                           <option value="all">Tous les statuts</option>
                           <option value="actif">Actifs</option>
                           <option value="inactif">Inactifs</option>
                           <option value="vacances">En vacances</option>
                           <option value="maladie">En arrêt maladie</option>
                        </select>

                        <!-- Nouveau membre -->
                        <button @click="showModal = 'personnel'"
                           class="px-6 py-3 bg-brand-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-brand-600/20 hover:scale-105 transition-transform flex items-center gap-2">
                           <i class="fa-solid fa-plus"></i>
                           Nouveau Membre
                        </button>
                     </div>
                  </div>
               </div>

               <!-- Tableau du personnel -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
                  <div class="overflow-x-auto">
                     <table class="w-full text-left min-w-[1000px]">
                        <thead class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100">
                           <tr>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 <div class="flex items-center gap-2">
                                    <input type="checkbox" x-model="selectAllPersonnel" @change="toggleAllPersonnel()"
                                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                    Employé
                                 </div>
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Contact
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Informations
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Rôle & Permissions
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Statut
                              </th>
                              <th
                                 class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                                 Actions
                              </th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                           <!-- État de chargement -->
                           <template x-if="loadingPersonnel">
                              <tr>
                                 <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-spinner fa-spin text-3xl text-brand-600"></i>
                                       <p class="text-slate-500">Chargement du personnel...</p>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Aucun résultat -->
                           <template x-if="!loadingPersonnel && filteredPersonnel().length === 0">
                              <tr>
                                 <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                       <i class="fa-solid fa-users text-3xl text-slate-300"></i>
                                       <p class="text-slate-500">Aucun membre du personnel trouvé</p>
                                       <button
                                          @click="personnelSearchQuery = ''; personnelRoleFilter = 'all'; personnelStatusFilter = 'all'; fetchPersonnel()"
                                          class="text-brand-600 font-bold hover:underline text-sm">
                                          Réinitialiser les filtres
                                       </button>
                                    </div>
                                 </td>
                              </tr>
                           </template>

                           <!-- Liste du personnel -->
                           <template x-for="employe in filteredPersonnel()" :key="employe.id">
                              <tr class="hover:bg-gray-50/50 transition-colors group"
                                 :class="{ 'bg-brand-50': selectedPersonnel.includes(employe.id) }">
                                 <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                       <input type="checkbox" :value="employe.id" x-model="selectedPersonnel"
                                          class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                       <div
                                          class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                                          <span class="font-bold text-slate-700"
                                             x-text="(employe.prenom?.charAt(0) || '') + (employe.nom?.charAt(0) || '')"></span>
                                       </div>
                                       <div>
                                          <p class="font-bold text-slate-900 text-sm"
                                             x-text="employe.prenom + ' ' + employe.nom"></p>
                                          <p class="text-xs text-slate-400" x-text="'ID: ' + employe.matricule"></p>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
                                          <span class="text-sm text-slate-700" x-text="employe.email"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                                          <span class="text-sm text-slate-700"
                                             x-text="employe.telephone || 'Non renseigné'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-1">
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Date embauche:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="formatDate(employe.dateEmbauche)"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Poste:</span>
                                          <span class="text-xs font-medium text-slate-700" x-text="employe.poste"></span>
                                       </div>
                                       <div class="flex items-center gap-2">
                                          <span class="text-xs text-slate-500">Salaire:</span>
                                          <span class="text-xs font-medium text-slate-700"
                                             x-text="formatCurrency(employe.salaire)"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-2">
                                       <span :class="getRoleClass(employe.role)"
                                          class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block text-center min-w-[100px]"
                                          x-text="getRoleText(employe.role)">
                                       </span>
                                       <div class="flex flex-wrap gap-1">
                                          <template x-for="permission in employe.permissions" :key="permission">
                                             <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px]"
                                                x-text="permission"></span>
                                          </template>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="space-y-2">
                                       <span :class="getPersonnelStatusClass(employe.statut)"
                                          class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block text-center min-w-[80px]"
                                          x-text="getPersonnelStatusText(employe.statut)">
                                       </span>
                                       <div class="text-xs text-slate-500">
                                          Dernière activité: <span
                                             x-text="employe.derniereActivite ? formatDate(employe.derniereActivite) : 'N/A'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                       <!-- Menu déroulant -->
                                       <div class="relative inline-block text-left" x-data="{ open: false }"
                                          @click.away="open = false">
                                          <button @click="open = !open"
                                             class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-gray-100 rounded-lg transition-colors">
                                             <i class="fa-solid fa-ellipsis-v"></i>
                                          </button>

                                          <div x-show="open" x-transition
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 py-1">

                                             <!-- Voir profil -->
                                             <button @click="open = false; viewPersonnelDetails(employe)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Voir profil</span>
                                             </button>

                                             <!-- Modifier -->
                                             <button @click="open = false; editPersonnel(employe)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Modifier</span>
                                             </button>

                                             <!-- Activer/Désactiver -->
                                             <template x-if="employe.statut === 'actif'">
                                                <button @click="open = false; togglePersonnelStatus(employe)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-user-slash"></i>
                                                   <span>Désactiver</span>
                                                </button>
                                             </template>
                                             <template x-if="employe.statut === 'inactif'">
                                                <button @click="open = false; togglePersonnelStatus(employe)"
                                                   class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 flex items-center gap-3 transition-colors">
                                                   <i class="fa-solid fa-user-check"></i>
                                                   <span>Activer</span>
                                                </button>
                                             </template>

                                             <!-- Réinitialiser mot de passe -->
                                             <button @click="open = false; resetPassword(employe)"
                                                class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-purple-50 hover:text-purple-600 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-key"></i>
                                                <span>Réinitialiser MDP</span>
                                             </button>

                                             <!-- Séparateur -->
                                             <div class="border-t border-gray-100 my-1"></div>

                                             <!-- Supprimer -->
                                             <button @click="open = false; deletePersonnel(employe)"
                                                class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                                <span>Supprimer</span>
                                             </button>
                                          </div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>

                  <!-- Pagination -->
                  <div class="flex flex-col sm:flex-row justify-between items-center gap-4 p-6 border-t border-gray-100">
                     <div class="text-sm text-slate-500">
                        Affichage de <span
                           x-text="Math.min((currentPersonnelPage - 1) * personnelPerPage + 1, totalPersonnel)"></span>
                        à <span x-text="Math.min(currentPersonnelPage * personnelPerPage, totalPersonnel)"></span>
                        sur <span x-text="totalPersonnel"></span> membres
                     </div>

                     <div class="flex items-center gap-2">
                        <button @click="prevPersonnelPage()" :disabled="currentPersonnelPage === 1"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-left"></i>
                        </button>

                        <!-- Ajoutez une vérification pour personnelPages -->
                        <template x-if="personnelPages && personnelPages.length > 0">
                           <template x-for="page in personnelPages" :key="page">
                              <button @click="currentPersonnelPage = page"
                                 :class="currentPersonnelPage === page ? 'bg-brand-600 text-white' : 'text-slate-600 hover:bg-gray-50'"
                                 class="w-10 h-10 flex items-center justify-center rounded-lg font-medium" x-text="page">
                              </button>
                           </template>
                        </template>

                        <!-- Ajoutez une vérification pour totalPersonnelPages -->
                        <button @click="nextPersonnelPage()"
                           :disabled="!totalPersonnelPages || currentPersonnelPage === totalPersonnelPages"
                           class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50">
                           <i class="fa-solid fa-chevron-right"></i>
                        </button>
                     </div>

                     <select x-model="personnelPerPage" @change="fetchPersonnel()"
                        class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                     </select>
                  </div>
               </div>

               <!-- Actions groupées -->
               <div x-show="selectedPersonnel.length > 0"
                  class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                     <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center text-brand-600">
                           <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                           <p class="font-bold text-slate-900" x-text="selectedPersonnel.length + ' membres sélectionnés'">
                           </p>
                           <p class="text-sm text-slate-500">Actions groupées disponibles</p>
                        </div>
                     </div>

                     <div class="flex flex-wrap gap-2">
                        <button @click="batchActivatePersonnel()"
                           class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-user-check"></i>
                           Activer
                        </button>

                        <button @click="batchDeactivatePersonnel()"
                           class="px-4 py-2 bg-amber-600 text-white rounded-xl font-bold text-sm hover:bg-amber-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-user-slash"></i>
                           Désactiver
                        </button>

                        <button @click="exportPersonnel()"
                           class="px-4 py-2 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-file-export"></i>
                           Exporter
                        </button>

                        <button @click="selectedPersonnel = []"
                           class="px-4 py-2 bg-gray-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                           Annuler sélection
                        </button>
                     </div>
                  </div>
               </div>
            </div>

            <!-- SECTION RAPPORTS & STATISTIQUES -->
            <div x-show="currentSection === 'rapports'" class="animate-fade-in space-y-8">
               <!-- En-tête avec options de période -->
               <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                  <div>
                     <h3 class="text-2xl font-bold text-slate-900 font-outfit">Rapports & Statistiques</h3>
                     <p class="text-sm text-slate-500">Analysez les performances et générez des rapports détaillés</p>
                  </div>

                  <div class="flex flex-wrap gap-4">
                     <!-- Sélecteur de période -->
                     <div class="relative">
                        <i
                           class="fa-solid fa-calendar absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                        <select x-model="reportPeriod" @change="loadReportData()"
                           class="pl-12 pr-8 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 min-w-[180px]">
                           <option value="today">Aujourd'hui</option>
                           <option value="yesterday">Hier</option>
                           <option value="week">Cette semaine</option>
                           <option value="last_week">Semaine dernière</option>
                           <option value="month">Ce mois</option>
                           <option value="last_month">Mois dernier</option>
                           <option value="quarter">Ce trimestre</option>
                           <option value="year">Cette année</option>
                           <option value="custom">Période personnalisée</option>
                        </select>
                     </div>

                     <!-- Période personnalisée (affichée seulement si custom) -->
                     <div x-show="reportPeriod === 'custom'" class="flex gap-3 items-center animate-fade-in">
                        <div class="relative">
                           <i
                              class="fa-solid fa-calendar-day absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                           <input type="date" x-model="customDateStart" @change="loadReportData()"
                              class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm">
                        </div>
                        <span class="text-slate-400">à</span>
                        <div class="relative">
                           <i
                              class="fa-solid fa-calendar-day absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                           <input type="date" x-model="customDateEnd" @change="loadReportData()"
                              class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm">
                        </div>
                     </div>

                     <!-- Boutons d'export -->
                     <div class="flex gap-3">
                        <button @click="exportReport('pdf')"
                           class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-file-pdf"></i>
                           PDF
                        </button>
                        <button @click="exportReport('excel')"
                           class="px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-file-excel"></i>
                           Excel
                        </button>
                        <button @click="printReport()"
                           class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-print"></i>
                           Imprimer
                        </button>
                     </div>
                  </div>
               </div>

               <!-- Cartes statistiques principales -->
               <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <!-- Chiffre d'affaires -->
                  <div class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Chiffre d'Affaires</p>
                           <p class="text-3xl font-bold font-outfit" x-text="formatCurrency(reportStats.totalRevenue)"></p>
                           <div class="flex items-center gap-2 mt-2">
                              <i
                                 :class="reportStats.revenueTrend > 0 ? 'fa-solid fa-arrow-up text-emerald-300' : 'fa-solid fa-arrow-down text-red-300'"></i>
                              <span class="text-sm" x-text="Math.abs(reportStats.revenueTrend) + '%'"></span>
                              <span class="text-xs opacity-80">vs période précédente</span>
                           </div>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-money-bill-wave text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Billets vendus -->
                  <div class="bg-gradient-to-r from-emerald-600 to-green-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Billets Vendus</p>
                           <p class="text-3xl font-bold font-outfit" x-text="reportStats.totalTickets"></p>
                           <div class="flex items-center gap-2 mt-2">
                              <i
                                 :class="reportStats.ticketsTrend > 0 ? 'fa-solid fa-arrow-up text-emerald-300' : 'fa-solid fa-arrow-down text-red-300'"></i>
                              <span class="text-sm" x-text="Math.abs(reportStats.ticketsTrend) + '%'"></span>
                           </div>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-ticket text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Voyages effectués -->
                  <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Voyages</p>
                           <p class="text-3xl font-bold font-outfit" x-text="reportStats.totalVoyages"></p>
                           <p class="text-sm opacity-90 mt-2">
                              <span x-text="reportStats.completedVoyages"></span> complétés
                           </p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-route text-xl"></i>
                        </div>
                     </div>
                  </div>

                  <!-- Taux d'occupation -->
                  <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl p-6 text-white shadow-xl">
                     <div class="flex items-center justify-between">
                        <div>
                           <p class="text-xs font-bold uppercase tracking-widest opacity-90 mb-1">Occupation</p>
                           <p class="text-3xl font-bold font-outfit" x-text="reportStats.occupancyRate + '%'"></p>
                           <div class="w-32 h-2 bg-white/30 rounded-full mt-3 overflow-hidden">
                              <div class="h-full bg-white rounded-full transition-all duration-1000"
                                 :style="'width: ' + reportStats.occupancyRate + '%'"></div>
                           </div>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                           <i class="fa-solid fa-chart-line text-xl"></i>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Graphiques et visualisations -->
               <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <!-- Graphique des revenus -->
                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                     <div class="flex justify-between items-center mb-6">
                        <div>
                           <h4 class="text-lg font-bold text-slate-900 font-outfit">Évolution des revenus</h4>
                           <p class="text-sm text-slate-500">Chiffre d'affaires quotidien</p>
                        </div>
                        <div class="flex gap-2">
                           <button @click="setChartType('revenue', 'daily')"
                              :class="chartConfig.revenue.type === 'daily' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-slate-700'"
                              class="px-3 py-1 rounded-lg text-xs font-bold transition-all">
                              Journalier
                           </button>
                           <button @click="setChartType('revenue', 'weekly')"
                              :class="chartConfig.revenue.type === 'weekly' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-slate-700'"
                              class="px-3 py-1 rounded-lg text-xs font-bold transition-all">
                              Hebdomadaire
                           </button>
                           <button @click="setChartType('revenue', 'monthly')"
                              :class="chartConfig.revenue.type === 'monthly' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-slate-700'"
                              class="px-3 py-1 rounded-lg text-xs font-bold transition-all">
                              Mensuel
                           </button>
                        </div>
                     </div>

                     <!-- Conteneur du graphique -->
                     <div class="h-64 flex items-center justify-center">
                        <div class="text-center">
                           <canvas id="revenueChart" class="w-full h-full"></canvas>
                           <div x-show="!revenueChartLoaded" class="py-8">
                              <i class="fa-solid fa-chart-bar text-4xl text-slate-300 mb-3"></i>
                              <p class="text-slate-500">Chargement du graphique...</p>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Répartition par type de siège -->
                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                     <div class="flex justify-between items-center mb-6">
                        <div>
                           <h4 class="text-lg font-bold text-slate-900 font-outfit">Répartition des sièges</h4>
                           <p class="text-sm text-slate-500">VIP vs Standard</p>
                        </div>
                     </div>

                     <!-- Graphique circulaire -->
                     <div class="flex flex-col lg:flex-row items-center gap-8">
                        <div class="relative w-48 h-48">
                           <canvas id="seatDistributionChart"></canvas>
                           <div class="absolute inset-0 flex items-center justify-center">
                              <div class="text-center">
                                 <p class="text-2xl font-bold text-slate-900" x-text="reportStats.totalTickets"></p>
                                 <p class="text-xs text-slate-500">Total billets</p>
                              </div>
                           </div>
                        </div>

                        <div class="flex-1 space-y-4">
                           <div class="flex justify-between items-center">
                              <div class="flex items-center gap-3">
                                 <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                 <span class="font-medium text-slate-700">VIP</span>
                              </div>
                              <div class="text-right">
                                 <p class="font-bold text-slate-900" x-text="reportStats.vipTickets"></p>
                                 <p class="text-sm text-slate-500" x-text="'(' + (reportStats.vipPercentage || 0) + '%)'">
                                 </p>
                              </div>
                           </div>

                           <div class="flex justify-between items-center">
                              <div class="flex items-center gap-3">
                                 <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                 <span class="font-medium text-slate-700">Standard</span>
                              </div>
                              <div class="text-right">
                                 <p class="font-bold text-slate-900" x-text="reportStats.standardTickets"></p>
                                 <p class="text-sm text-slate-500"
                                    x-text="'(' + (reportStats.standardPercentage || 0) + '%)'"></p>
                              </div>
                           </div>

                           <div class="pt-4 border-t border-gray-100">
                              <div class="flex justify-between items-center">
                                 <span class="text-sm text-slate-600">Revenu VIP:</span>
                                 <span class="font-bold text-slate-900"
                                    x-text="formatCurrency(reportStats.vipRevenue)"></span>
                              </div>
                              <div class="flex justify-between items-center">
                                 <span class="text-sm text-slate-600">Revenu Standard:</span>
                                 <span class="font-bold text-slate-900"
                                    x-text="formatCurrency(reportStats.standardRevenue)"></span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Tableau des meilleurs trajets -->
               <div class="bg-white rounded-3xl border border-gray-100 shadow-card overflow-hidden">
                  <div class="p-6 border-b border-gray-100">
                     <div class="flex justify-between items-center">
                        <div>
                           <h4 class="text-lg font-bold text-slate-900 font-outfit">Top 5 des trajets</h4>
                           <p class="text-sm text-slate-500">Les itinéraires les plus rentables</p>
                        </div>
                        <button @click="exportTopTrajets()"
                           class="px-4 py-2 bg-gray-100 text-slate-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all flex items-center gap-2">
                           <i class="fa-solid fa-download"></i>
                           Exporter
                        </button>
                     </div>
                  </div>

                  <div class="overflow-x-auto">
                     <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                           <tr>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Trajet
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Voyages
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Billets
                                 vendus</th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Revenus
                              </th>
                              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Taux
                                 occupation</th>
                              <th class="px6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                 Performance</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                           <template x-for="trajet in topTrajets" :key="trajet.id">
                              <tr class="hover:bg-gray-50/50 transition-colors">
                                 <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                       <div
                                          class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                                          <i class="fa-solid fa-route"></i>
                                       </div>
                                       <div>
                                          <p class="font-bold text-slate-900" x-text="trajet.route"></p>
                                          <p class="text-xs text-slate-500" x-text="trajet.distance + ' km'"></p>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <span class="font-medium text-slate-900" x-text="trajet.voyages"></span>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div>
                                       <span class="font-medium text-slate-900 block" x-text="trajet.tickets"></span>
                                       <div class="flex gap-2 mt-1">
                                          <span class="text-xs text-purple-600 font-bold"
                                             x-text="trajet.vip + ' VIP'"></span>
                                          <span class="text-xs text-blue-600 font-bold"
                                             x-text="trajet.standard + ' Std'"></span>
                                       </div>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900 block"
                                       x-text="formatCurrency(trajet.revenue)"></span>
                                    <span class="text-xs text-slate-500"
                                       x-text="'Moyenne: ' + formatCurrency(trajet.averageRevenue)"></span>
                                 </td>
                                 <td class="px-6 py-4">
                                    <div class="w-32">
                                       <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                          <div class="h-full bg-brand-500 transition-all duration-1000"
                                             :style="'width: ' + trajet.occupancy + '%'"></div>
                                       </div>
                                       <p class="text-xs font-bold text-slate-400 mt-1" x-text="trajet.occupancy + '%'">
                                       </p>
                                    </div>
                                 </td>
                                 <td class="px-6 py-4">
                                    <span :class="getPerformanceClass(trajet.performance)"
                                       class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                       x-text="getPerformanceText(trajet.performance)">
                                    </span>
                                 </td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>
               </div>

               <!-- Statistiques détaillées -->
               <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Statistiques clients -->
                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                     <h4 class="text-lg font-bold text-slate-900 font-outfit mb-6">Statistiques clients</h4>

                     <div class="space-y-6">
                        <div>
                           <div class="flex justify-between items-center mb-2">
                              <span class="text-sm text-slate-600">Nouveaux clients</span>
                              <span class="font-bold text-slate-900" x-text="reportStats.newClients"></span>
                           </div>
                           <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                              <div class="h-full bg-emerald-500 transition-all duration-1000"
                                 :style="'width: ' + Math.min(reportStats.newClientsGrowth, 100) + '%'"></div>
                           </div>
                        </div>

                        <div>
                           <div class="flex justify-between items-center mb-2">
                              <span class="text-sm text-slate-600">Taux de fidélisation</span>
                              <span class="font-bold text-slate-900" x-text="reportStats.retentionRate + '%'"></span>
                           </div>
                           <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                              <div class="h-full bg-blue-500 transition-all duration-1000"
                                 :style="'width: ' + reportStats.retentionRate + '%'"></div>
                           </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                           <div class="flex justify-between items-center py-2">
                              <span class="text-sm text-slate-600">Clients actifs</span>
                              <span class="font-bold text-slate-900" x-text="reportStats.activeClients"></span>
                           </div>
                           <div class="flex justify-between items-center py-2">
                              <span class="text-sm text-slate-600">CA moyen par client</span>
                              <span class="font-bold text-slate-900"
                                 x-text="formatCurrency(reportStats.avgRevenuePerClient)"></span>
                           </div>
                           <div class="flex justify-between items-center py-2">
                              <span class="text-sm text-slate-600">Fréquence d'achat</span>
                              <span class="font-bold text-slate-900"
                                 x-text="reportStats.purchaseFrequency + ' / mois'"></span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Performance des bus -->
                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                     <h4 class="text-lg font-bold text-slate-900 font-outfit mb-6">Performance du parc</h4>

                     <div class="space-y-6">
                        <div>
                           <div class="flex justify-between items-center mb-2">
                              <span class="text-sm text-slate-600">Taux d'utilisation</span>
                              <span class="font-bold text-slate-900" x-text="reportStats.busUtilization + '%'"></span>
                           </div>
                           <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                              <div class="h-full bg-amber-500 transition-all duration-1000"
                                 :style="'width: ' + reportStats.busUtilization + '%'"></div>
                           </div>
                        </div>

                        <div>
                           <div class="flex justify-between items-center mb-2">
                              <span class="text-sm text-slate-600">Kilométrage moyen</span>
                              <span class="font-bold text-slate-900"
                                 x-text="reportStats.avgKilometers + ' km/jour'"></span>
                           </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                           <h5 class="text-sm font-bold text-slate-700 mb-3">Bus les plus performants</h5>
                           <div class="space-y-3">
                              <template x-for="bus in topBuses" :key="bus.id">
                                 <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <div>
                                       <p class="font-medium text-slate-900" x-text="bus.immatriculation"></p>
                                       <p class="text-xs text-slate-500" x-text="bus.model"></p>
                                    </div>
                                    <div class="text-right">
                                       <p class="font-bold text-slate-900" x-text="bus.occupancy + '%'"></p>
                                       <p class="text-xs text-slate-500" x-text="bus.revenue + 'k'"></p>
                                    </div>
                                 </div>
                              </template>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- Synthèse financière -->
                  <div class="bg-white rounded-3xl border border-gray-100 shadow-card p-6">
                     <h4 class="text-lg font-bold text-slate-900 font-outfit mb-6">Synthèse financière</h4>

                     <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                           <span class="text-slate-600">Revenus totaux</span>
                           <span class="font-bold text-slate-900" x-text="formatCurrency(reportStats.totalRevenue)"></span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                           <span class="text-slate-600">Coûts opérationnels</span>
                           <span class="font-bold text-red-600"
                              x-text="formatCurrency(reportStats.operationalCosts)"></span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                           <span class="text-slate-600">Marge brute</span>
                           <span class="font-bold text-emerald-600"
                              x-text="formatCurrency(reportStats.grossMargin)"></span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                           <span class="text-slate-600">Taux de marge</span>
                           <span class="font-bold text-emerald-600" x-text="reportStats.marginRate + '%'"></span>
                        </div>

                        <div class="pt-4">
                           <div class="p-4 bg-brand-50 rounded-xl">
                              <div class="flex justify-between items-center">
                                 <span class="font-bold text-slate-900">Bénéfice net estimé</span>
                                 <span class="text-2xl font-bold text-brand-600"
                                    x-text="formatCurrency(reportStats.netProfit)"></span>
                              </div>
                              <p class="text-xs text-slate-500 mt-2">
                                 Estimation basée sur les données de la période sélectionnée
                              </p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Bouton génération rapport complet -->
               <div class="text-center pt-4">
                  <button @click="generateFullReport()"
                     class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg flex items-center gap-3 mx-auto">
                     <i class="fa-solid fa-file-chart-column"></i>
                     Générer un rapport complet détaillé
                  </button>
                  <p class="text-sm text-slate-500 mt-3">
                     Le rapport inclura toutes les statistiques, graphiques et recommandations d'optimisation
                  </p>
               </div>
            </div>
         </main>
      </div>
   </div>

   @push('styles')
      <style>
         /* Styles pour le menu déroulant */
         .dropdown-enter-active {
            transition: all 0.2s ease-out;
         }

         .dropdown-leave-active {
            transition: all 0.15s ease-in;
         }

         .dropdown-enter-from,
         .dropdown-leave-to {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
         }

         /* Animation shake pour les erreurs */
         @keyframes shake {

            0%,
            100% {
               transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
               transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
               transform: translateX(5px);
            }
         }

         .animate-shake {
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
         }

         /* Z-index pour les menus */
         .z-50 {
            z-index: 50;
         }

         .z-\[100\] {
            z-index: 100;
         }

         .z-\[200\] {
            z-index: 200;
         }

         /* Amélioration du menu déroulant */
         [x-data] .dropdown-menu {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
         }

         /* Style spécifique pour les boutons du menu */
         [x-data] .dropdown-item {
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
         }

         [x-data] .dropdown-item:hover {
            transform: translateX(3px);
         }

         [x-data] .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: transparent;
            transition: background 0.2s;
         }

         [x-data] .dropdown-item:hover::before {
            background: currentColor;
         }

         /* Animation pour le feedback de scan */
         @keyframes pulse {

            0%,
            100% {
               opacity: 1;
            }

            50% {
               opacity: 0.5;
            }
         }

         .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
         }

         /* Animation pour les nouveaux passagers */
         @keyframes highlight {
            0% {
               background-color: rgba(34, 197, 94, 0.1);
            }

            100% {
               background-color: transparent;
            }
         }

         .highlight-new {
            animation: highlight 2s ease-out;
         }

         /* Style pour le scanner */
         .scanner-frame {
            position: relative;
            overflow: hidden;
         }

         .scanner-frame::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            animation: scan 2s linear infinite;
         }

         @keyframes scan {
            0% {
               transform: translateX(-100%);
            }

            100% {
               transform: translateX(100%);
            }
         }

         /* Styles pour l'embarquement */
         .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
         }

         @keyframes pulse {

            0%,
            100% {
               opacity: 1;
            }

            50% {
               opacity: 0.5;
            }
         }

         /* Animation pour les nouveaux passagers */
         @keyframes highlight {
            0% {
               background-color: rgba(34, 197, 94, 0.1);
            }

            100% {
               background-color: transparent;
            }
         }

         .highlight-new {
            animation: highlight 2s ease-out;
         }

         /* Style pour le scanner */
         .scanner-frame {
            position: relative;
            overflow: hidden;
         }

         .scanner-frame::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            animation: scan 2s linear infinite;
         }

         @keyframes scan {
            0% {
               transform: translateX(-100%);
            }

            100% {
               transform: translateX(100%);
            }
         }

         /* Styles pour les statuts d'embarquement */
         .status-a_embarquer {
            background-color: #f3f4f6;
            color: #374151;
         }

         .status-embarque {
            background-color: #d1fae5;
            color: #047857;
         }

         .status-retard {
            background-color: #fef3c7;
            color: #d97706;
         }

         .status-absent {
            background-color: #fee2e2;
            color: #dc2626;
         }

         /* Amélioration de l'animation fade-in */
         @keyframes fadeIn {
            from {
               opacity: 0;
               transform: translateY(10px);
            }

            to {
               opacity: 1;
               transform: translateY(0);
            }
         }

         .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
         }

         /* Responsive pour l'embarquement */
         @media (max-width: 1024px) {
            .lg\:col-span-2 {
               grid-column: span 1;
            }

            .grid-cols-1 {
               grid-template-columns: 1fr;
            }
         }
      </style>
   @endpush
@endsection

@push('scripts')
   <script src="/js/dashboard-app.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush