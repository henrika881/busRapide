<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageExpress - Connexion & Inscription</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <!-- Container Principal -->
    <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[650px]">

        <!-- Côté Gauche : Visuel & Marketing (Masqué sur mobile) -->
        <div class="hidden md:flex md:w-1/2 bg-blue-700 relative flex-col justify-between p-12 text-white">
            <!-- Image de fond avec overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1000&auto=format&fit=crop"
                     class="w-full h-full object-cover opacity-20 mix-blend-overlay" alt="Bus Travel">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-700 to-blue-900 opacity-90"></div>
            </div>

            <!-- Contenu Visuel -->
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-bus text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-wider">VOYAGE<span class="text-blue-300">EXPRESS</span></span>
                </div>

                <h2 class="text-4xl font-bold leading-tight mb-6">Voyagez plus loin, <br>payez moins.</h2>
                <p class="text-blue-100 text-lg leading-relaxed">
                    Rejoignez la première plateforme de billetterie numérique. Évitez les files d'attente en gare et embarquez directement avec votre QR Code.
                </p>
            </div>

            <!-- Statistiques / Preuves sociales -->
            <div class="relative z-10 grid grid-cols-2 gap-6 pt-8 border-t border-blue-500/30">
                <div>
                    <div class="text-3xl font-bold">120+</div>
                    <div class="text-blue-200 text-sm">Destinations</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">24/7</div>
                    <div class="text-blue-200 text-sm">Support Client</div>
                </div>
            </div>
        </div>

        <!-- Côté Droit : Formulaires -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center relative">

            <!-- Logo Mobile uniquement -->
            <div class="md:hidden flex items-center gap-2 mb-8 text-blue-700 justify-center">
                <i class="fas fa-bus text-2xl"></i>
                <span class="font-bold text-xl">VOYAGE EXPRESS</span>
            </div>

            <!-- FORMULAIRE DE CONNEXION (ID: login-form) -->
            <div id="login-form" class="fade-in w-full max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">Bon retour !</h3>
                    <p class="text-gray-500">Connectez-vous pour gérer vos billets.</p>
                </div>

                <form class="space-y-5" onsubmit="event.preventDefault(); alert('Connexion simulée !');">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" placeholder="client@exemple.com" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Oublié ?</a>
                        </div>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password" placeholder="••••••••" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-blue-500/30 transition-transform transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Se connecter</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Pas encore de compte ?
                        <button onclick="toggleForms()" class="text-blue-700 font-bold hover:underline ml-1">Créer un compte</button>
                    </p>
                </div>
            </div>

            <!-- FORMULAIRE D'INSCRIPTION (ID: register-form, masqué par défaut) -->
            <div id="register-form" class="hidden fade-in w-full max-w-md mx-auto">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Nouveau Passager</h3>
                    <p class="text-gray-500 text-sm">Créez un compte pour acheter vos billets.</p>
                </div>

                <form class="space-y-4" onsubmit="event.preventDefault(); alert('Inscription simulée !');">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Prénom</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nom</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" placeholder="nom@email.com" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Téléphone <span class="text-gray-400 font-normal">(Pour SMS Billet)</span></label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="tel" placeholder="+237 6..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mot de passe</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password" placeholder="Min. 8 caractères" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="terms" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" required>
                        <label for="terms" class="text-xs text-gray-600">J'accepte les <a href="#" class="text-blue-600 underline">CGU</a> et la politique de voyage.</label>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-green-500/30 transition-transform transform active:scale-95 flex items-center justify-center gap-2">
                        <span>S'inscrire et Embarquer</span>
                        <i class="fas fa-check-circle"></i>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Déjà un compte ?
                        <button onclick="toggleForms()" class="text-blue-700 font-bold hover:underline ml-1">Se connecter</button>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Simple pour basculer entre les formulaires -->
    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            if (loginForm.classList.contains('hidden')) {
                // Afficher Connexion
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            } else {
                // Afficher Inscription
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
