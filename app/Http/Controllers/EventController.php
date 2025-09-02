<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Models\Batch;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('batches')->get();
        return response()->json($events);
    }

    public function store(CreateEventRequest $request)
    {
        DB::beginTransaction();

        $event = new Event();
        $event->promoter_id = Auth::user()->id;
        $event->fill($request->all());
        $event->save();

        if ($request->has('batches')) {
            try{
                $batchCount = Batch::count();
                foreach ($request->batches as $batchData) {
                    $batch = new Batch($batchData);
                    $batch->batch = ++$batchCount;
                    $batch->event_id = $event->id;
                    $batch->save();
                }
            }catch (\Exception $e) {
                DB::rollBack();
                $message = 'Error creating event and batches. Try again or contact support.';
                return response()->json(['message' => $message], 500);
            }
        }

        DB::commit();

        return response()->json(201);
    }

    public function show(string $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $batch = Batch::where('event_id', $event->id)
        ->orderBy('batch', 'asc')
        ->get();

        $acumulateSoldBatches = [];
        $firstAvailable = null;
        foreach ($batch as $b) {
            if($b->tickets_sold == $b->tickets_qty) {
                $acumulateSoldBatches[] = $b;
            }

            if($b->tickets_sold < $b->tickets_qty && is_null($firstAvailable)) {
                $firstAvailable = $b;
            }
        }

        $acumulateSoldBatches[] = $firstAvailable;

        $event->batches = $acumulateSoldBatches;

        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
