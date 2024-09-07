<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $uuid)
    {
        $room = Room::query()->where('uuid', $uuid)->firstOrFail();

        return view('rooms.show', ['room' => $room]);
    }
}
