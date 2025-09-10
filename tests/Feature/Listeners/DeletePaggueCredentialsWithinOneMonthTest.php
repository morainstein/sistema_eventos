<?php

namespace Tests\Feature;

use App\Events\PaggueCredentialsCreatedEvent;
use App\Http\Middleware\AuthPromoter;
use App\Listeners\DeletePaggueCredentialsWithinOneMonth;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeletePaggueCredentialsWithinOneMonthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_listener_is_being_pushed_to_queue(): void
    {
        # ARRANGE
        Queue::fake();
        $credentials = PaggueCredentials::factory()->for(Promoter::factory())->create();
        
        # ACT
        PaggueCredentialsCreatedEvent::dispatch($credentials);
        
        # ASSERT
        Queue::assertPushed(CallQueuedListener::class, function ($job){
            return $job->class === DeletePaggueCredentialsWithinOneMonth::class;
        });
    }
}
