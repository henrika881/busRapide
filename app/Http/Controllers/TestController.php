<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NelsiusPayService;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketConfirmation;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    protected $nelsiusPay;

    public function __construct(NelsiusPayService $nelsiusPay)
    {
        $this->nelsiusPay = $nelsiusPay;
    }

    /**
     * Test Nelsius Pay Initiation
     */
    public function testPayment(Request $request)
    {
        $amount = $request->amount ?? 100;
        $phone = $request->phone ?? '699000000';
        $operator = $request->operator ?? 'orange_money';

        try {
            $response = $this->nelsiusPay->initiatePayment($amount, $phone, $operator, "Test Paiement");
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Test Email Sending
     */
    public function testEmail(Request $request)
    {
        $email = $request->email;
        if (!$email) {
            return response()->json(['error' => 'Email required'], 400);
        }

        // Get the latest ticket or create a dummy one if none exists
        $ticket = Ticket::latest()->first();

        if (!$ticket) {
            return response()->json(['error' => 'No ticket found to simulate email'], 404);
        }

        // Force regeneration of QR code to ensure it's in the new format (simple string)
        $ticket->genererQR();

        try {
            Mail::to($email)->send(new TicketConfirmation($ticket));
            return response()->json(['success' => true, 'message' => "Email sent to $email"]);
        } catch (\Exception $e) {
            Log::error("Mail Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
