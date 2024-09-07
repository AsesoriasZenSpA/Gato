<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Host',
            'email' => 'host@app.cl',
            'password' => bcrypt('secretpass'),
        ]);
        User::factory()->create([
            'name' => 'Guest',
            'email' => 'guest@app.cl',
            'password' => bcrypt('secretpass'),
        ]);
    }
}
