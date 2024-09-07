<?php

namespace App\Http\Controllers\Rooms\Games;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Repositories\GamesRepository;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $uuid)
    {
        $room = Room::query()->where('uuid', $uuid)->firstOrFail();

        GamesRepository::create($room);

        return redirect()->route('rooms.show', ['uuid' => $room->uuid]);
    }
}
