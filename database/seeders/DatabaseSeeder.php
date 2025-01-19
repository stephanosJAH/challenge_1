<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Booking;

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
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Tour::factory(10)->create();
        Hotel::factory(10)->create();
        Booking::factory(10)->create();
    }
}
