@extends('layouts.app')

@section('title', 'Connexion Administration - BusRapide')

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-slate-100">
        <div class="text-center">
            <div class="w-20 h-20 bg-slate-900 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg shadow-slate-900/20">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-slate-900">
                Espace Administration
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Veuillez vous authentifier pour accéder au tableau de bord.
            </p>
        </div>
        
        <form class="mt-8 space-y-6" id="admin-login-form" onsubmit="handleAdminLogin(event)">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="email" class="sr-only">Adresse Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="appearance-none rounded-xl relative block w-full pl-10 px-3 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-brand-500 focus:border-brand-500 focus:z-10 sm:text-sm shadow-sm" 
                            placeholder="Adresse Email">
                    </div>
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="appearance-none rounded-xl relative block w-full pl-10 px-3 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-brand-500 focus:border-brand-500 focus:z-10 sm:text-sm shadow-sm" 
                            placeholder="Mot de passe">
                    </div>
                </div>
            </div>

            <div id="login-error" class="text-center text-sm text-red-600 font-bold hidden bg-red-50 p-3 rounded-lg border border-red-100"></div>

            <div>
                <button type="submit" id="btn-submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all shadow-lg hover:shadow-xl active:scale-95">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-slate-300 transition"></i>
                    </span>
                    Se connecter
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
async function handleAdminLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('login-error');
    const btn = document.getElementById('btn-submit');
    
    errorDiv.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Connexion...';
    
    try {
        const response = await fetch(`${API_BASE_URL}/admin/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
            localStorage.setItem('auth_token', data.token);
            localStorage.setItem('user_data', JSON.stringify(data.user)); // Nouveau format unifié
            
            showToast('Connexion réussie ! Redirection...', 'success');
            
            setTimeout(() => {
                window.location.href = '/admin/dashboard';
            }, 1000);
        } else {
            throw new Error(data.message || 'Identifiants incorrects');
        }
    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = `
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-slate-300 transition"></i>
            </span>
            Se connecter`;
    }
}
</script>
@endpush
@endsection
