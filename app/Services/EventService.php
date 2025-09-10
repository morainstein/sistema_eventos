<?php

namespace App\Services;

use App\Mail\EventCreated;
use App\Mail\EventCreatedMail;
use App\Models\Admin;
use App\Models\Batch;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDOException;
use stdClass;

class EventService
{
    /**
     * @return Array<stdClass> $acumulatedDtoEvents
     * @param Illuminate\Database\Eloquent\Collection<App\Models\Event> $events
     */
    public function returnAllEventWithFirstAvailableBatch(Collection $events)
    {
        $acumulatedDtoEvents = [];
        foreach($events as $event){
            $acumulatedDtoEvents[] = $this->returnOneEventWithFirstAvailableBatch($event);
        }

        return $acumulatedDtoEvents;
    }

    /**
     * @return stdClass $eventDto
     */
    public function returnOneEventWithFirstAvailableBatch(Event $event)
    {
        $acumulateSoldBatches = [];
        $firstAvailable = null;
        foreach ($event->batches as $b) {
            if($b->tickets_sold == $b->tickets_qty) {
                $acumulateSoldBatches[] = $b;
            }

            if($b->tickets_sold < $b->tickets_qty && is_null($firstAvailable)) {
                $firstAvailable = $b;
            }
        }
        $acumulateSoldBatches[] = $firstAvailable;

        $eventDto = new stdClass($event);
        foreach($event->getAttributes() as $key => $value){
            $eventDto->$key = $value;
        }

        $eventDto->batches = $acumulateSoldBatches;

        return $eventDto;
    }

    /**
     * @throws PDOException
     */
    public function createEventWithBatches(Request $request): Event
    {
        DB::beginTransaction();

        $event = new Event($request->all());
        $event->promoter_id = Auth::user()->id;
        $event->save();

        try{
            $batchCount = Batch::count();
            foreach ($request->batches as $batchData) {
                $batch = new Batch($batchData);
                $batch->batch = ++$batchCount;
                $batch->event_id = $event->id;
                $batch->save();
            }
        }catch (\PDOException $e) {
            DB::rollBack();

            throw new PDOException(previous: $e);
        }

        DB::commit();

        return $event;
    }
}