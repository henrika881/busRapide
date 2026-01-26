<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new \App\Mail\TicketConfirmation($this->ticket))
                    ->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'titre' => 'Réservation Confirmée',
            'contenu' => "Votre billet pour {$this->ticket->voyage->trajet->villeDepart} - {$this->ticket->voyage->trajet->villeArrivee} a été confirmé.",
            'idTicket' => $this->ticket->idTicket,
            'idVoyage' => $this->ticket->idVoyage,
            'typeNotification' => 'reservation',
            'dateEnvoi' => now()
        ];
    }
}
