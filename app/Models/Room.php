<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'guest_id',
        'token',
        'uuid',
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
