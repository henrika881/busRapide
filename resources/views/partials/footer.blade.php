<footer class="bg-slate-900 text-white py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <span class="font-bold text-xl">BusRapide</span>
                </div>
                <p class="text-slate-400 text-sm mb-4">Réservez vos billets de bus en ligne simplement et rapidement.
                    Plus de 100 destinations à travers le pays.</p>
                <div class="flex gap-3">
                    <a href="#"
                        class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#"
                        class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-slate-700 transition">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Destinations</h3>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#" class="hover:text-white transition">Douala → Yaoundé</a></li>
                    <li><a href="#" class="hover:text-white transition">Bafoussam → Douala</a></li>
                    <li><a href="#" class="hover:text-white transition">Yaoundé → Kribi</a></li>
                    <li><a href="#" class="hover:text-white transition">Douala → Buea</a></li>
                    <li><a href="#" class="hover:text-white transition">Yaoundé → Garoua</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Informations</h3>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="#howto" class="hover:text-white transition">Comment ça marche</a></li>
                    <li><a href="#" class="hover:text-white transition">Conditions générales</a></li>
                    <li><a href="#" class="hover:text-white transition">Politique de confidentialité</a></li>
                    <li><a href="#assistance" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="#assistance" class="hover:text-white transition">Contact</a></li>
                    <!-- Ajout Lien Admin -->
                    <li>
                        <a href="/admin/login"
                            class="flex items-center gap-2 text-slate-400 hover:text-brand-400 transition mt-4 pt-4 border-t border-slate-700">
                            <i class="fa-solid fa-lock text-xs"></i>
                            Espace Admin
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4">Téléchargez l'app</h3>
                <p class="text-slate-400 text-sm mb-4">Réservez vos billets depuis votre smartphone</p>
                <div class="space-y-3">
                    <a href="#"
                        class="inline-flex items-center gap-3 bg-slate-800 hover:bg-slate-700 rounded-xl px-4 py-3 transition">
                        <i class="fa-brands fa-apple text-2xl"></i>
                        <div>
                            <p class="text-xs">Disponible sur</p>
                            <p class="font-bold">App Store</p>
                        </div>
                    </a>
                    <a href="#"
                        class="inline-flex items-center gap-3 bg-slate-800 hover:bg-slate-700 rounded-xl px-4 py-3 transition">
                        <i class="fa-brands fa-google-play text-2xl"></i>
                        <div>
                            <p class="text-xs">Disponible sur</p>
                            <p class="font-bold">Google Play</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-800 text-center">
            <p class="text-slate-400 text-sm">&copy; 2023 BusRapide. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- MOBILE BOTTOM NAVIGATION -->
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 pb-safe shadow-lg z-40 md:hidden">
    <div class="flex justify-around items-center h-16 safe-bottom">
        <a href="/" class="flex flex-col items-center justify-center w-full h-full text-brand-600">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-[10px] font-bold">Accueil</span>
        </a>

        <a href="/#search"
            class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
            <i class="fa-solid fa-magnifying-glass text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Rechercher</span>
        </a>

        <!-- Central How-to Button -->
        <div class="relative -top-6">
            <a href="/#howto"
                class="w-14 h-14 rounded-full bg-brand-600 text-white shadow-xl shadow-brand-500/40 flex items-center justify-center text-2xl transform transition-transform active:scale-95">
                <i class="fa-solid fa-question"></i>
            </a>
        </div>

        <a href="/#destinations"
            class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
            <i class="fa-solid fa-map-marker-alt text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Destinations</span>
        </a>

        <a href="/dashboard"
            class="flex flex-col items-center justify-center w-full h-full text-slate-400 hover:text-brand-600 transition-colors">
            <i class="fa-solid fa-ticket text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Mes Billets</span>
        </a>
    </div>
</nav>