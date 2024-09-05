<?php

namespace App\Repositories;

use App\Events\Rooms\Join;
use App\Events\Rooms\Leave;
use App\Mail\Rooms\Invite;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RoomsRepository
{
    public static function create(User $host): Room
    {
        return Room::query()->create([
            'host_id' => $host->id,
            'uuid' => Str::uuid(),
            'token' => Str::random(32),
        ]);
    }

    public static function invite(User $guest, Room $room): void
    {
        $room->update([
            'guest_id' => $guest->id,
        ]);

        Mail::to($guest)->send(new Invite($guest, $room));
    }

    public static function verify(User $user, string $token, Room $room): bool
    {
        return $room->token === $token
            && $room->guest_id === $user->id;
    }

    public static function joined(User $user, Room $room): void
    {
        broadcast(new Join($user, $room));
    }

    public static function leaves(User $user, Room $room): void
    {
        broadcast(new Leave($user, $room));
    }
}
