<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Http\Middleware\VerifyIfPaymentCredentialsExists;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventUploadBannerRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
        $this->withoutMiddleware(VerifyIfPaymentCredentialsExists::class);
    }

    public function test_really_stores_in_s3_bucket__return_201_created_and_a_message()   
    {
        # ARRANGE
        Storage::fake();

        $event = Event::factory()
            ->has(Batch::factory())
            ->for(Promoter::factory())
            ->create();
        
        $payload = [ 
            'banner' => UploadedFile::fake()->image('banner_test.jpg',800,600)
        ];

        # ACT
        $response = $this->postJson("/api/events/{$event->id}/banner",$payload);
        
        # ASSERT
        
        $response
            ->assertExactJson(['message' => 'banner has been stored'])
            ->assertCreated();
        
        Storage::assertExists('/banners');
    }
}
