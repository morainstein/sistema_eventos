<x-mail::message>
# Evento '{{ $event->title }}' criado por {{ $event->promoter->name }}

**Titulo:** {{ $event->title }}  
**Descrição:** {{ $event->description }}  
**Início do evento:** {{ $event->start_dateTime }}  
**Fim do evento:** {{ $event->end_dateTime }}
@foreach ($event->batches as $batch)
-  **Lote {{ $batch->batch }}**
    -  **Preço:** R$ {{ ($batch->price /100) }}  
    -  **Total de ingressos:** {{ ($batch->tickets_qty) }}  
    -  **Fechamento do lote:** {{ ($batch->end_dateTime) }}  
<br>
@endforeach

## {{ config('app.name') }}
</x-mail::message>
