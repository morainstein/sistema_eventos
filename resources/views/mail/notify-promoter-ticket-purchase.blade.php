<x-mail::message>
# {{ $event->title }}: Compra de ingresso

**Cliente:** {{ $customer->name }}<br>
**Código do ingresso:** {{ $ticket->id }}<br>
**Valor pago:** R$ {{ ($ticket->final_price /100) }}<br>
**Status do pagamento:** Pago<br>

## {{ config('app.name') }}
</x-mail::message>
