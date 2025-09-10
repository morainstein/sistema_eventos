<?php

namespace App\Http\Controllers;

use App\Events\EventCreatedEvent;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UploadedBannerRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDOException;

class EventController extends Controller
{
    public function __construct(
        public readonly EventService $eventService
    ){}

    public function index()
    {
        $events = Event::with('batches')->get();

        $acumulatedEvents = $this->eventService->returnAllEventWithFirstAvailableBatch($events);
        return response()->json($acumulatedEvents);
    }

    public function store(CreateEventRequest $request)
    /**
     * - Cria eventos com seus respectivos lotes de ingressos
     * - Dispara evento de evento criado
     *   - Listener: Informa todos os admins via email 
     */
    {
        try {
            $event = $this->eventService->createEventWithBatches($request);

        } catch (\PDOException $e) {
            $message = 'Error creating event and batches. Try again or contact support.';

            return response()->json(['message' => $message], 500);
        }

        EventCreatedEvent::dispatch($event);

        return response()->json(['message' => 'Event registered successfully'],201);
    }

    public function show(string $eventId)
    /**
     * - Retorna o evento somente com o lote disponível (e todos os lotes fechados antes do disponível)
     */
    {
        $event = Event::with('batches')->find($eventId);
        if(!$event){
            return response()->json(['message' => 'Event not found'], 404);
        }

        $eventDto = $this->eventService->returnOneEventWithFirstAvailableBatch($event);

        return response()->json($eventDto, 200);
    }

    public function uploadedBanner(Event $event, UploadedBannerRequest $request)
    {        
        $bannerPath = Storage::url($request->file('banner')->store('/banners'));

        $event->banner_link = $bannerPath;
        $event->save();

        return response()->json(['message' => 'banner has been stored'],201);
    }

    public function update(Request $request, Event $event)
    {
        $message = "As this application is only for portfolio, this functionality was not implemented due to the complexity of the business rules";
        return response()->json(['message' => $message],501);
    }

    public function destroy(Event $event)
    {
        $message = "As this application is only for portfolio, this functionality was not implemented due to the complexity of the business rules";
        return response()->json(['message' => $message],501);
    }
}
