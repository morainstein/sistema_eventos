<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Events\TicketPayedEvent;

use App\Models\Ticket;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function pixCashIn(Request $request)
    /**
     * - Altera status do ingresso para pago
     * - Dispara evento de ingresso comprado
     *   - Listeners: Notifica os interessados da compra (promoter e cliente)
     *   - Listeners: Incrementa a quantidade de ingressos comprados na tabela de lotes
     */
    { 
        if(!PaymentStatus::isPayed($request->status)){
            return response()->json();
        }

        $ticket = Ticket::with(['event','customer'])->find($request->external_id);
        $ticket->payment_status = PaymentStatus::PAYED->value;
        $ticket->save();

        TicketPayedEvent::dispatch($ticket);

        return response()->json();
    }
}
