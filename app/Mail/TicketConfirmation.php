<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', ['ticket' => $this->ticket]);

        return $this->subject('Votre réservation BusRapide - Billet Confirmé')
            ->view('emails.ticket_confirmation')
            ->attachData($pdf->output(), "billet-{$this->ticket->numeroBillet}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
