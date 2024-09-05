<?php

namespace Tests\Feature;

use App\Events\Rooms\Join;
use App\Events\Rooms\Leave;
use App\Mail\Rooms\Invite;
use App\Models\Room;
use App\Models\User;
use App\Repositories\RoomsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RoomsTest extends TestCase
{
    use RefreshDatabase;

    public function test_flow(): void
    {
        Mail::fake();
        Event::fake();

        $host = User::factory()->create();
        $guest = User::factory()->create();
        $other = User::factory()->create();

        $room = RoomsRepository::create($host);
        $this->assertDatabaseHas(Room::class, [
            'id' => $room->id,
            'host_id' => $host->id,
            'token' => $room->token,
        ]);

        RoomsRepository::invite($guest, $room);
        $this->assertDatabaseHas(Room::class, [
            'id' => $room->id,
            'guest_id' => $guest->id,
        ]);
        Mail::assertSent(Invite::class);

        $this->assertTrue(RoomsRepository::verify($guest, $room->token, $room));
        $this->assertFalse(RoomsRepository::verify($other, $room->token, $room));

        RoomsRepository::joined($host, $room);
        Event::assertDispatched(Join::class, fn ($e) => $e->room == $room && $e->user === $host);

        RoomsRepository::joined($guest, $room);
        Event::assertDispatched(Join::class, fn ($e) => $e->room == $room && $e->user === $guest);

        RoomsRepository::joined($other, $room);
        Event::assertDispatched(Join::class, fn ($e) => $e->room == $room && $e->user === $other);

        RoomsRepository::leaves($host, $room);
        Event::assertDispatched(Leave::class, fn ($e) => $e->room == $room && $e->user === $host);

        RoomsRepository::leaves($guest, $room);
        Event::assertDispatched(Leave::class, fn ($e) => $e->room == $room && $e->user === $guest);

        RoomsRepository::leaves($other, $room);
        Event::assertDispatched(Leave::class, fn ($e) => $e->room == $room && $e->user === $other);
    }
}
