<?php

namespace App\Repositories;

use App\Events\Rooms\GameFinished;
use App\Events\Rooms\GameUpdated;
use App\Models\Game;
use App\Models\Room;
use App\Models\User;

class GamesRepository
{
    public static function create(Room $room)
    {
        $initializer_id = fake()->randomElement([
            $room->host_id,
            $room->guest_id,
        ]);

        return Game::query()->create([
            'room_id' => $room->id,
            'initializer_id' => $initializer_id,
            'board' => '         ',
        ]);
    }

    public static function play(int $position, User $user, Room $room, Game $game): bool
    {
        if (static::gotWinner($game)) {
            return false;
        }

        if (! in_array($user->id, [$room->host_id, $room->guest_id])) {
            return false;
        }

        $board = $game->board;

        $available_plays = substr_count($board, ' ', 0);
        $should_play_initializer = $available_plays % 2 == 1;

        if ($should_play_initializer && $user->id !== $game->initializer_id)
            return false;

        if(!$should_play_initializer && $user->id === $game->initializer_id)
            return false;

        if ($board[$position] !== ' ')
            return false;

        $board[$position] = $should_play_initializer ? 'A' : 'B';

        $game->update([
            'board' => $board,
        ]);

        if (static::gotWinner($game))
            broadcast(new GameFinished($room, $game, $user, $position));
        else
            broadcast(new GameUpdated($room, $game, $user, $position));


        return true;
    }

    public static function gotWinner(Game $game): string|false
    {
        $players = ['A', 'B'];
        $possibilities = [
            [0, 4, 8], [2, 4, 6],               //diagonals
            [0, 1, 2], [3, 4, 5], [6, 7, 8],    //horizontals
            [0, 3, 6], [1, 4, 7], [2, 5, 8],    //verticals
        ];
        foreach ($players as $player) {
            $chances = [];
            foreach ($possibilities as $possibility) {
                $chances[] = array_map(fn ($position) => $game->board[$position] == $player, $possibility);
            }

            foreach ($chances as $chance) {
                $truly = $chance[0] === true && $chance[1] === true && $chance[2] === true;
                if ($truly) {
                    return $player;
                }
            }
        }

        return false;
    }
}
