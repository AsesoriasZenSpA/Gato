<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'board',
        'initializer_id',
    ];

    public function getStructureAttribute()
    {
        $rows = str_split($this->board);

        return [
            array_slice($rows, 0, 3),
            array_slice($rows, 3, 3),
            array_slice($rows, 6, 3),
        ];

    }
}
