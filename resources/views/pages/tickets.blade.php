@extends('layouts.app')

@section('title', 'Mes Billets - BusRapide')

@section('content')
<div class="min-h-screen bg-slate-50 pb-12">
    <!-- Header -->
    <div class="bg-slate-900 text-white shadow-lg mb-8">
        <div class="max-w-5xl mx-auto px-4 py-8">
            <h1 class="text-3xl font-black flex items-center gap-3">
                <i class="fa-solid fa-ticket text-brand-500"></i>
                Mes Billets
            </h1>
            <p class="text-slate-400 mt-2">Retrouvez ici tous vos titres de transport réservés.</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4">
        <!-- Loading State -->
        <div id="tickets-loading" class="flex flex-col items-center justify-center py-20">
            <div class="w-12 h-12 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin mb-4"></div>
            <p class="text-slate-500 animate-pulse">Récupération de vos billets...</p>
        </div>

        <!-- Empty State -->
        <div id="tickets-empty" class="hidden text-center py-20 bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="w-20 h-20 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Aucun billet trouvé</h2>
            <p class="text-slate-500 mb-8">Vous n'avez pas encore effectué de réservation.</p>
            <a href="/reservation" class="inline-flex items-center gap-2 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-700 transition shadow-lg shadow-brand-500/20">
                <i class="fa-solid fa-search"></i>
                Rechercher un voyage
            </a>
        </div>

        <!-- Tickets List -->
        <div id="tickets-container" class="space-y-6">
            <!-- Les tickets seront injectés ici -->
        </div>
    </div>
</div>

<!-- Modal Détails Billet (pour partage/PDF) -->
<div id="ticket-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-900">Détails du Billet</h3>
            <button onclick="closeTicketModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-500 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div id="modal-content" class="p-6 overflow-y-auto max-h-[80vh]">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTickets = [];

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '/';
            return;
        }

        await loadTickets();
    });

    async function loadTickets() {
        try {
            const response = await fetch(`${API_BASE_URL}/tickets`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();
            document.getElementById('tickets-loading').classList.add('hidden');

            if (result.success && result.data && (result.data.length > 0 || (result.data.data && result.data.data.length > 0))) {
                const tickets = result.data.data || result.data;
                currentTickets = tickets;
                renderTickets(tickets);
            } else {
                document.getElementById('tickets-empty').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors du chargement des billets', 'error');
            document.getElementById('tickets-loading').classList.add('hidden');
            document.getElementById('tickets-empty').classList.remove('hidden');
        }
    }

    function renderTickets(tickets) {
        const container = document.getElementById('tickets-container');
        container.innerHTML = '';

        tickets.forEach(ticket => {
            const dateStr = ticket.voyage ? new Date(ticket.voyage.dateHeureDepart).toLocaleDateString('fr-FR', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            }) : 'Date inconnue';
            
            const voyage = ticket.voyage || {};
            const trajet = voyage.trajet || {};
            
            const card = document.createElement('div');
            card.className = "bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1";
            card.innerHTML = `
                <div class="flex flex-col md:flex-row">
                    <!-- Partie Gauche : Info Voyage -->
                    <div class="p-8 flex-1">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-xs font-black uppercase tracking-widest">
                                ${ticket.numeroBillet}
                            </span>
                            <span class="px-3 py-1 ${ticket.statut === 'confirme' ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600'} rounded-full text-xs font-bold uppercase">
                                ${ticket.statut || 'Reservé'}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-2xl font-black text-slate-900">${trajet.villeDepart || 'N/A'}</p>
                                <p class="text-xs text-slate-500 uppercase">Départ</p>
                            </div>
                            <div class="flex-1 border-t-2 border-dashed border-slate-200 relative">
                                <i class="fa-solid fa-bus text-brand-600 absolute left-1/2 -translate-x-1/2 -top-3 bg-white px-2"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-black text-slate-900">${trajet.villeArrivee || 'N/A'}</p>
                                <p class="text-xs text-slate-500 uppercase">Arrivée</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-8 pt-8 border-t border-slate-100">
                            <div>
                                <p class="text-slate-500 mb-1">Date</p>
                                <p class="font-bold text-slate-900">${dateStr}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 mb-1">Heure</p>
                                <p class="font-bold text-slate-900">${voyage.heure_depart || '00:00'}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 mb-1">Siège</p>
                                <p class="font-bold text-slate-900">${ticket.siege ? ticket.siege.numeroSiege : 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 mb-1">Classe</p>
                                <p class="font-bold text-slate-900 uppercase">${ticket.classeBillet || 'Standard'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Partie Droite : Actions & QR -->
                    <div class="bg-slate-50 border-l border-slate-100 p-8 flex flex-col items-center justify-center min-w-[240px]">
                        <div class="w-32 h-32 bg-white p-2 rounded-xl shadow-sm mb-6 flex items-center justify-center overflow-hidden">
                             <img src="${ticket.codeQR ? 'data:image/png;base64,' + ticket.codeQR : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + ticket.numeroBillet}" 
                                   alt="QR Code" class="w-full h-full object-contain">
                        </div>
                        
                        <div class="flex flex-col w-full gap-2">
                            <button onclick="downloadPDF('${ticket.idTicket}')" class="flex items-center justify-center gap-2 w-full py-2.5 bg-brand-600 text-white rounded-lg font-bold text-sm hover:bg-brand-700 transition">
                                <i class="fa-solid fa-file-pdf"></i>
                                Télécharger PDF
                            </button>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="shareWhatsApp('${ticket.idTicket}')" class="flex items-center justify-center gap-2 py-2.5 bg-green-500 text-white rounded-lg font-bold text-sm hover:bg-green-600 transition">
                                    <i class="fa-brands fa-whatsapp text-lg"></i>
                                </button>
                                <button onclick="shareEmail('${ticket.idTicket}')" class="flex items-center justify-center gap-2 py-2.5 bg-blue-500 text-white rounded-lg font-bold text-sm hover:bg-blue-600 transition">
                                    <i class="fa-solid fa-envelope"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    async function downloadPDF(ticketId) {
        showToast('Préparation du PDF...', 'info');
        window.location.href = `${API_BASE_URL}/tickets/${ticketId}/download?token=${localStorage.getItem('auth_token')}`;
    }

    function shareWhatsApp(ticketId) {
        const ticket = currentTickets.find(t => t.idTicket == ticketId);
        if (!ticket) return;

        const dateStr = new Date(ticket.voyage.dateHeureDepart).toLocaleDateString('fr-FR');
        const text = encodeURIComponent(`*BUSRAPIDE - MON BILLET*\n\n` +
            `🎫 *N° Billet:* ${ticket.numeroBillet}\n` +
            `🚌 *Trajet:* ${ticket.voyage.trajet.villeDepart} → ${ticket.voyage.trajet.villeArrivee}\n` +
            `📅 *Date:* ${dateStr}\n` +
            `⏰ *Heure:* ${ticket.voyage.heure_depart}\n` +
            `💺 *Siège:* ${ticket.siege ? ticket.siege.numeroSiege : 'N/A'} (${ticket.classeBillet})\n\n` +
            `_Présentez ce message à l'embarquement._`);
        
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function shareEmail(ticketId) {
        const ticket = currentTickets.find(t => t.idTicket == ticketId);
        if (!ticket) return;

        const subject = encodeURIComponent(`Mon Billet BusRapide - ${ticket.numeroBillet}`);
        const body = encodeURIComponent(`Voici les détails de mon billet :\n\n- Trajet: ${ticket.voyage.trajet.villeDepart} -> ${ticket.voyage.trajet.villeArrivee}\n- Date: ${new Date(ticket.voyage.dateHeureDepart).toLocaleDateString()}\n- Siège: ${ticket.siege ? ticket.siege.numeroSiege : 'N/A'}\n- Numéro: ${ticket.numeroBillet}`);
        window.location.href = `mailto:?subject=${subject}&body=${body}`;
    }

    function closeTicketModal() {
        document.getElementById('ticket-modal').classList.add('hidden');
    }
</script>
@endpush
@endsection
