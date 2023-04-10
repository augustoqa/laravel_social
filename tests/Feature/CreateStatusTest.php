<?php

namespace Tests\Feature;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use App\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Tests\TestCase;
use App\Events\StatusCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_users_can_not_create_statuses()
    {
        $response = $this->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        $response->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_can_create_statuses()
    {
        Event::fake([StatusCreated::class]);
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        $response->assertJson([
            'data' => ['body' => 'Mi primer status'],
        ]);

        $this->assertDatabaseHas('statuses', [
            'user_id' => $user->id,
            'body' => 'Mi primer status'
        ]);
    }

    /** @test */
    function an_event_is_fired_when_a_status_is_created()
    {
        Event::fake([StatusCreated::class]);
        \Broadcast::shouldReceive('socket')->andReturn('socket-id');

        $this->actingAs(factory(User::class)->create())
            ->postJson(route('statuses.store'), ['body' => 'Mi primer status']);

        Event::assertDispatched(StatusCreated::class, function ($statusCreatedEvent) {
            $this->assertInstanceOf(ShouldBroadcast::class, $statusCreatedEvent);
            $this->assertInstanceOf(StatusResource::class, $statusCreatedEvent->status);
            $this->assertInstanceOf(Status::class, $statusCreatedEvent->status->resource);
            $this->assertEquals(Status::first()->id, $statusCreatedEvent->status->id);
            $this->assertEquals(
                'socket-id',
                $statusCreatedEvent->socket,
                'The event ' . get_class($statusCreatedEvent) . ' must call the method "dontBroadcastToCurrentUser" in the constructor.'
            );
            return true;
        });

    }

    /** @test */
    function a_status_requires_a_body()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => '']);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message', 'errors' => ['body']
        ]);
    }

    /** @test */
    function a_status_body_requires_a_minimum_length()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->postJson(route('statuses.store'), ['body' => 'asdf']);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message', 'errors' => ['body']
        ]);
    }
}
