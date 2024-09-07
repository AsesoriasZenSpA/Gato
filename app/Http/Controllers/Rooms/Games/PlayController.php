<?php

namespace App\Http\Controllers\Rooms\Games;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Room;
use App\Repositories\GamesRepository;
use Illuminate\Http\Request;

class PlayController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $uuid, Game $game)
    {
        $room = Room::query()->where('uuid', $uuid)->firstOrFail();

        $position = (int) $request->input('position');

        GamesRepository::play($position, $request->user(), $room, $game);

        return redirect()->route('rooms.show', ['uuid' => $room->uuid]);
    }
}
