<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use App\Repositories\RoomsRepository;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $room = RoomsRepository::create($request->user());

        return redirect()->route('rooms.show', ['uuid' => $room->uuid]);
    }
}
