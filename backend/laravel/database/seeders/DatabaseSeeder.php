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
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    //     $this->call([
    //         ClientSeeder::class,
    //         AppointmentSeeder::class,
    //     ]);
    // }
    public function run(): void
{
    // Prüfen, ob der User bereits existiert, bevor wir ihn erstellen
    if (!User::where('email', 'test@example.com')->exists()) {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    // Dann erst Clients & Appointments seeden
    $this->call([
        ClientSeeder::class,
        AppointmentSeeder::class,
    ]);
}
}
