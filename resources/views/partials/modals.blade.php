<!-- LOGIN MODAL -->
<div id="login-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl flex flex-col md:flex-row relative animate-fadeIn">
        
        <!-- Close Button (Mobile & Desktop) -->
        <button onclick="closeLoginModal()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-slate-100/80 hover:bg-slate-200 text-slate-500 hover:text-red-500 rounded-full flex items-center justify-center transition-all">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <!-- Left Side - Branding -->
        <div class="hidden md:flex md:w-5/12 bg-slate-900 p-12 flex-col justify-between text-white relative overflow-hidden">
            <!-- Abstract Shapes -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-brand-600/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 text-2xl font-bold mb-2">
                    <i class="fa-solid fa-bus-simple text-brand-500"></i>
                    <span>BusRapide</span>
                </div>
                <p class="text-slate-400 text-sm">Voyagez en toute sérénité.</p>
            </div>

            <div class="relative z-10 space-y-6">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Bon retour !</h3>
                    <p class="text-slate-400">Accédez à votre espace personnel pour gérer vos voyages et réservations.</p>
                </div>
            </div>

            <div class="relative z-10 text-xs text-slate-500">
                &copy; {{ date('Y') }} BusRapide. Tous droits réservés.
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white">
            <div class="max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Connexion</h2>
                <p class="text-slate-500 mb-8">Veuillez entrer vos identifiants pour continuer.</p>

                <!-- Google Sign In -->
                <button onclick="signInWithGoogle()"
                    class="w-full h-12 border border-slate-200 rounded-lg font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center justify-center gap-3 mb-6">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                    <span>Continuer avec Google</span>
                </button>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs font-medium text-slate-500 uppercase">Ou par email</span>
                    </div>
                </div>

                <form class="space-y-5" onsubmit="event.preventDefault();">
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-slate-700 mb-1">Adresse Email</label>
                        <input type="email" id="login-email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="nom@exemple.com" required>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="login-password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                            <button type="button" onclick="showForgotPassword()" class="text-xs font-medium text-brand-600 hover:text-brand-700">Oublié ?</button>
                        </div>
                        <div class="relative">
                            <input type="password" id="login-password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all" placeholder="••••••••" required>
                            <button type="button" onclick="togglePassword('login-password')" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                        <label for="remember-me" class="ml-2 block text-sm text-slate-600">Se souvenir de moi</label>
                    </div>

                    <button type="submit" onclick="handleLogin(event)" class="w-full h-12 bg-brand-600 text-white font-bold rounded-lg hover:bg-brand-700 active:scale-[0.98] transition-all shadow-lg shadow-brand-500/30">
                        Se connecter
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-500">
                    Pas encore de compte ?
                    <button onclick="switchToRegister()" class="font-bold text-brand-600 hover:text-brand-700">Créer un compte</button>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- REGISTER MODAL -->
<div id="register-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-5xl overflow-hidden shadow-2xl flex flex-col md:flex-row relative animate-fadeIn">
        
        <!-- Close Button -->
        <button onclick="closeRegisterModal()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-slate-100/80 hover:bg-slate-200 text-slate-500 hover:text-red-500 rounded-full flex items-center justify-center transition-all">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <!-- Left Side - Branding -->
        <div class="hidden md:flex md:w-5/12 bg-slate-900 p-12 flex-col justify-between text-white relative overflow-hidden">
             <!-- Abstract Shapes -->
             <div class="absolute top-0 right-0 w-72 h-72 bg-emerald-600/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2"></div>
             <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-600/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
             
             <div class="relative z-10">
                 <div class="flex items-center gap-3 text-2xl font-bold mb-2">
                     <i class="fa-solid fa-bus-simple text-emerald-500"></i>
                     <span>BusRapide</span>
                 </div>
                 <p class="text-slate-400 text-sm">L'aventure commence ici.</p>
             </div>
 
             <div class="relative z-10 space-y-6">
                 <div>
                     <h3 class="text-3xl font-bold mb-4">Rejoignez-nous !</h3>
                     <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-emerald-400 shrink-0">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white">Réservation simplifiée</h4>
                                <p class="text-sm text-slate-400">Réservez en quelques clics.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-emerald-400 shrink-0">
                                <i class="fa-solid fa-percent"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-white">Offres exclusives</h4>
                                <p class="text-sm text-slate-400">Profitez de réductions régulières.</p>
                            </div>
                        </li>
                     </ul>
                 </div>
             </div>
 
             <div class="relative z-10 text-xs text-slate-500">
                 &copy; {{ date('Y') }} BusRapide. Tous droits réservés.
             </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white overflow-y-auto max-h-[90vh]">
            <div class="max-w-lg mx-auto">
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Création de compte</h2>
                <p class="text-slate-500 mb-8">Remplissez le formulaire ci-dessous pour commencer.</p>

                <form class="space-y-4" onsubmit="event.preventDefault();">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Prénom</label>
                            <input type="text" id="register-firstname" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Jean">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nom</label>
                            <input type="text" id="register-lastname" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Dupont">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" id="register-email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="jean@exemple.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                            <input type="tel" id="register-phone" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="6 XX XX XX XX">
                        </div>
                    </div>

                    <div>
                         <label class="block text-sm font-medium text-slate-700 mb-1">Numéro CNI</label>
                         <input type="text" id="register-cni" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Votre numéro de CNI">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
                            <input type="password" id="register-password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="••••••••">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                            <input type="password" id="register-confirm-password" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-start pt-2">
                        <input type="checkbox" id="register-terms" class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="register-terms" class="ml-2 block text-sm text-slate-600">
                            J'accepte les <a href="#" class="text-emerald-600 font-bold hover:underline">conditions générales</a> et la <a href="#" class="text-emerald-600 font-bold hover:underline">politique de confidentialité</a>
                        </label>
                    </div>

                    <button type="submit" onclick="handleRegister(event)" class="w-full h-12 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 active:scale-[0.98] transition-all shadow-lg shadow-emerald-500/30">
                        Créer mon compte
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-500">
                    Déjà inscrit ?
                    <button onclick="switchToLogin()" class="font-bold text-emerald-600 hover:text-emerald-700">Se connecter</button>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- VIDEO MODAL -->
<div id="video-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-3xl p-6 relative shadow-modal animate-fadeIn">
        <button onclick="closeVideoModal()"
            class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:bg-slate-100 transition z-10">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        
        <div class="aspect-video bg-slate-900 rounded-xl flex items-center justify-center">
            <div class="text-center text-white">
                <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-play text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Démonstration vidéo</h3>
                <p class="text-slate-300">Cette fonctionnalité serait intégrée dans la version finale</p>
            </div>
        </div>
        
        <div class="mt-6">
            <h4 class="font-bold text-slate-900 mb-2">Comment réserver en 3 étapes :</h4>
            <ol class="list-decimal pl-5 space-y-2 text-slate-600">
                <li>Recherchez votre trajet avec notre formulaire intuitif</li>
                <li>Sélectionnez votre bus et vos sièges préférés</li>
                <li>Payez en ligne de manière sécurisée et recevez votre billet</li>
            </ol>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgot-password-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-sm p-6 relative shadow-modal animate-fadeIn">
        <button onclick="closeForgotPassword()"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-slate-200 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <div class="text-center mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mx-auto mb-3">
                <i class="fa-solid fa-key"></i>
            </div>
            <h3 class="font-bold text-slate-900">Mot de passe oublié</h3>
            <p class="text-sm text-slate-500 mt-1">Entrez votre email pour réinitialiser</p>
        </div>
        
        <form class="space-y-3">
            <input type="email" placeholder="Votre email" 
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                Envoyer le lien
            </button>
        </form>
    </div>
</div>
