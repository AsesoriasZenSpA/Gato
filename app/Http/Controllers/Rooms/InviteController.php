<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use App\Repositories\RoomsRepository;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $uuid)
    {
        $room = Room::query()->where('uuid', $uuid)->firstOrFail();
        $guest = User::query()->where('id', $request->input('guest_id'))->firstOrFail();

        RoomsRepository::invite($guest, $room);

        return redirect()->route('rooms.show', ['uuid' => $room->uuid]);
    }
}
