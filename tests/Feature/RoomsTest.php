<?php

namespace Tests\Feature;

use App\Events\Rooms\GameFinished;
use App\Events\Rooms\GameUpdated;
use App\Events\Rooms\Join;
use App\Events\Rooms\Leave;
use App\Mail\Rooms\Invite;
use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use App\Repositories\GamesRepository;
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

        $game = GamesRepository::create($room);
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => '         ',
        ]);

        $initializer = $game->initializer_id == $host->id ? $host : $guest;
        $not_initializer = $game->initializer_id == $host->id ? $guest : $host;

        $this->assertTrue(GamesRepository::play(0, $initializer, $room, $game));
        Event::assertDispatched(GameUpdated::class, fn ($e) => $e->room == $room && $e->user === $initializer && $e->game === $game && $e->position === 0);
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'A        ',
        ]);

        $this->assertFalse(GamesRepository::play(0, $initializer, $room, $game));
        $this->assertFalse(GamesRepository::play(0, $other, $room, $game));
        Event::assertNotDispatched(GameUpdated::class, fn ($e) => $e->room == $room && $e->user === $other && $e->game === $game && $e->position === 0);
        $this->assertFalse(GamesRepository::gotWinner($game));

        $this->assertTrue(GamesRepository::play(1, $not_initializer, $room, $game));
        $this->assertFalse(GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB       ',
        ]);

        $this->assertFalse(GamesRepository::play(2, $not_initializer, $room, $game));
        $this->assertFalse(GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB       ',
        ]);

        $this->assertTrue(GamesRepository::play(4, $initializer, $room, $game));
        $this->assertFalse(GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB  A    ',
        ]);

        $this->assertTrue(GamesRepository::play(3, $not_initializer, $room, $game));
        $this->assertFalse(GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB BA    ',
        ]);

        $this->assertTrue(GamesRepository::play(8, $initializer, $room, $game));
        Event::assertDispatched(GameFinished::class, fn ($e) => $e->room == $room && $e->user === $initializer && $e->game === $game && $e->position === 8);
        Event::assertNotDispatched(GameUpdated::class, fn ($e) => $e->room == $room && $e->user === $initializer && $e->game === $game && $e->position === 8);
        $this->assertEquals('A', GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB BA   A',
        ]);

        $this->assertFalse(GamesRepository::play(5, $not_initializer, $room, $game));
        $this->assertEquals('A', GamesRepository::gotWinner($game));
        $this->assertDatabaseHas(Game::class, [
            'id' => $game->id,
            'room_id' => $room->id,
            'board' => 'AB BA   A',
        ]);
    }
}
