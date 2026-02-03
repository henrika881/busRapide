<script>
    // Gestion du token et état utilisateur
    let currentUser = null;
    let userToken = localStorage.getItem('auth_token');

    // ==================== FONCTIONS MODALES ====================

    // Modal functions
    function openLoginModal() {
        const modal = document.getElementById('login-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.getElementById('mobile-menu')?.classList.remove('open');
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById('login-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            // Reset animation class if needed for next open
        }
    }

    function openRegisterModal() {
        const modal = document.getElementById('register-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.getElementById('mobile-menu')?.classList.remove('open');
        }
    }

    function closeRegisterModal() {
        const modal = document.getElementById('register-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function switchToRegister() {
        closeLoginModal();
        setTimeout(() => {
            openRegisterModal();
        }, 300);
    }

    function switchToLogin() {
        closeRegisterModal();
        setTimeout(() => {
            openLoginModal();
        }, 300);
    }

    function showForgotPassword() {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            closeLoginModal();
        }
    }

    function closeForgotPassword() {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function closeVideoModal() {
        const modal = document.getElementById('video-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function openVideoModal() {
        const modal = document.getElementById('video-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }

    // ==================== GESTION DES CLICS SUR LES MODALES ====================

    document.addEventListener('DOMContentLoaded', () => {
        // Fermer les modales en cliquant sur le fond
        const modals = ['login-modal', 'register-modal', 'forgot-password-modal', 'video-modal'];

        modals.forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        if (id === 'login-modal') closeLoginModal();
                        else if (id === 'register-modal') closeRegisterModal();
                        else if (id === 'forgot-password-modal') closeForgotPassword();
                        else if (id === 'video-modal') closeVideoModal();
                    }
                });
            }
        });

        // Check auth on load
        checkAuthStatus();
    });

    // ==================== GESTION DE L'AUTHENTIFICATION ====================

    // Connexion
    async function handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        if (!email || !password) {
            showToast('Veuillez remplir tous les champs', 'error');
            return;
        }

        showToast('Connexion en cours...');

        try {
            const response = await fetch(`${API_BASE_URL}/clients/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    motDePasse: password
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Sauvegarder le token
                userToken = data.data.token;
                localStorage.setItem('auth_token', userToken);
                localStorage.setItem('user_role', 'client');

                showToast('Connexion réussie !', 'success');
                closeLoginModal();

                // Mettre à jour l'UI
                currentUser = data.data.client;
                updateUIForLoggedInUser(currentUser);

                // Rediriger vers le profil
                setTimeout(() => {
                    window.location.href = '/profil';
                }, 1000);
            } else {
                showToast(data.message || 'Email ou mot de passe incorrect', 'error');
            }
        } catch (error) {
            console.error('Erreur connexion:', error);
            showToast('Erreur de connexion au serveur', 'error');
        }
    }

    // Inscription
    async function handleRegister(e) {
        e.preventDefault();

        const firstname = document.getElementById('register-firstname').value;
        const lastname = document.getElementById('register-lastname').value;
        const email = document.getElementById('register-email').value;
        const phone = document.getElementById('register-phone').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;
        const cni = document.getElementById('register-cni').value;
        const terms = document.getElementById('register-terms').checked;

        // Validation basique
        if (!firstname || !lastname || !email || !phone || !password || !confirmPassword || !cni) {
            showToast('Veuillez remplir tous les champs', 'error');
            return;
        }

        if (password !== confirmPassword) {
            showToast('Les mots de passe ne correspondent pas', 'error');
            return;
        }

        if (!terms) {
            showToast('Veuillez accepter les conditions générales', 'error');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Format d\'email invalide', 'error');
            return;
        }

        if (password.length < 8) {
            showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
            return;
        }

        showToast('Inscription en cours...');

        try {
            const response = await fetch(`${API_BASE_URL}/clients/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nom: lastname,
                    prenom: firstname,
                    email: email,
                    telephone: phone,
                    numeroCNI: cni,
                    motDePasse: password,
                    motDePasse_confirmation: confirmPassword,
                    accept_terms: terms ? 1 : 0
                })
            });

            const result = await response.json();

            if (response.status === 201 && result.success) {
                // Succès
                showToast('Compte créé avec succès !', 'success');

                // Stocker le token
                if (result.data && result.data.token) {
                    userToken = result.data.token;
                    localStorage.setItem('auth_token', result.data.token);
                }

                // Mettre à jour l'UI immédiatement avec les données disponibles
                if (result.data && result.data.client) {
                    currentUser = result.data.client;
                    updateUIForLoggedInUser(currentUser);
                }

                // Fermer le modal
                closeRegisterModal();

                // Rediriger vers le profil
                showToast('Redirection vers votre profil...', 'info');
                setTimeout(() => {
                    window.location.href = '/profil';
                }, 1000);

            } else if (response.status === 422) {
                // Erreurs de validation
                let errorMessages = [];
                if (result.errors) {
                    for (const field in result.errors) {
                        errorMessages = errorMessages.concat(result.errors[field]);
                    }
                    showToast(errorMessages.join(', '), 'error');
                } else {
                    showToast(result.message || 'Erreur de validation', 'error');
                }
            } else {
                // Autres erreurs
                showToast(result.message || 'Erreur lors de l\'inscription', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur de connexion au serveur. Vérifiez votre connexion.', 'error');
        }
    }

    // Déconnexion
    async function logoutUser() {
        if (userToken) {
            try {
                await fetch(`${API_BASE_URL}/clients/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${userToken}`,
                        'Accept': 'application/json'
                    }
                });
            } catch (error) {
                console.error('Erreur déconnexion:', error);
            }
        }

        // Nettoyer le localStorage
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_role');
        localStorage.removeItem('user_data');

        // Réinitialiser les variables
        userToken = null;
        currentUser = null;

        // Mettre à jour l'UI
        updateUIForGuest();
        showToast('Déconnexion réussie');

        // Recharger la page pour mettre à jour le menu mobile
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // ==================== GESTION DE L'UI ====================

    // Vérifier l'état d'authentification
    async function checkAuthStatus() {
        if (!userToken) {
            updateUIForGuest();
            return;
        }

        // Sync UI immediately with what we have in localStorage
        const storedUser = localStorage.getItem('user_data');
        if (storedUser) {
            try {
                currentUser = JSON.parse(storedUser);
                updateUIForLoggedInUser(currentUser);
            } catch (e) {
                console.error("Error parsing stored user", e);
            }
        } else {
            // Even if we don't have user data, if we have a token, we should show logged in UI
            updateUIForLoggedInUser(null);
        }

        try {
            const response = await fetch(`${API_BASE_URL}/clients/profile`, {
                headers: {
                    'Authorization': `Bearer ${userToken}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    currentUser = data.data.client;
                    localStorage.setItem('user_data', JSON.stringify(currentUser));
                    updateUIForLoggedInUser(currentUser);
                } else {
                    // Token invalide
                    logoutUser();
                }
            } else {
                // Token expiré ou invalide
                if (response.status === 401) {
                    logoutUser();
                }
            }
        } catch (error) {
            console.error('Erreur vérification auth:', error);
            // On suppose qu'on est connecté si on a un token et que c'est une erreur réseau
        }
    }

    function updateUIForLoggedInUser(user) {
        // Find elements
        const guestButtons = document.getElementById('guest-buttons');
        const authUserDisplay = document.getElementById('auth-user-display');
        const userNameElement = document.querySelector('.user-name');
        const userInitialElement = document.getElementById('user-initial');

        // Desktop header
        if (guestButtons) {
            guestButtons.classList.add('hidden');
            guestButtons.style.setProperty('display', 'none', 'important');
        }

        if (authUserDisplay) {
            authUserDisplay.classList.remove('hidden');
            authUserDisplay.style.setProperty('display', 'flex', 'important');
        }

        if (user) {
            if (userNameElement) userNameElement.textContent = user.prenom;
            if (userInitialElement && user.prenom) {
                userInitialElement.textContent = user.prenom.charAt(0).toUpperCase();
            }
        }

        // Mobile menu
        const mobileGuestButtons = document.getElementById('mobile-guest-buttons');
        const mobileAuthButtons = document.getElementById('mobile-auth-buttons');
        const mobileDashboardLink = document.getElementById('mobile-dashboard-link');
        const mobileTicketsLink = document.getElementById('mobile-tickets-link');

        if (mobileGuestButtons) {
            mobileGuestButtons.classList.add('hidden');
            mobileGuestButtons.style.setProperty('display', 'none', 'important');
        }
        if (mobileAuthButtons) {
            mobileAuthButtons.classList.remove('hidden');
            mobileAuthButtons.style.setProperty('display', 'block', 'important');
        }
        if (mobileDashboardLink) {
            mobileDashboardLink.classList.remove('hidden');
            mobileDashboardLink.style.setProperty('display', 'flex', 'important');
        }
        if (mobileTicketsLink) {
            mobileTicketsLink.classList.remove('hidden');
            mobileTicketsLink.style.setProperty('display', 'flex', 'important');
        }
    }

    function updateUIForGuest() {
        // Find elements
        const guestButtons = document.getElementById('guest-buttons');
        const authUserDisplay = document.getElementById('auth-user-display');

        // Desktop header
        if (guestButtons) {
            guestButtons.classList.remove('hidden');
            guestButtons.style.setProperty('display', 'flex', 'important');
        }

        if (authUserDisplay) {
            authUserDisplay.classList.add('hidden');
            authUserDisplay.style.setProperty('display', 'none', 'important');
        }

        // Mobile menu
        const mobileGuestButtons = document.getElementById('mobile-guest-buttons');
        const mobileAuthButtons = document.getElementById('mobile-auth-buttons');
        const mobileDashboardLink = document.getElementById('mobile-dashboard-link');
        const mobileTicketsLink = document.getElementById('mobile-tickets-link');

        if (mobileGuestButtons) {
            mobileGuestButtons.classList.remove('hidden');
            mobileGuestButtons.style.setProperty('display', 'block', 'important');
        }
        if (mobileAuthButtons) {
            mobileAuthButtons.classList.add('hidden');
            mobileAuthButtons.style.setProperty('display', 'none', 'important');
        }
        if (mobileDashboardLink) {
            mobileDashboardLink.classList.add('hidden');
            mobileDashboardLink.style.setProperty('display', 'none', 'important');
        }
        if (mobileTicketsLink) {
            mobileTicketsLink.classList.add('hidden');
            mobileTicketsLink.style.setProperty('display', 'none', 'important');
        }
    }

    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
</script>