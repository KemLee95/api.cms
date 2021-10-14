<?php

namespace Database\Seeders;

use App\Models\OutgoingEmailTracking;
use Database\Seeders\EventSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PostSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call([
            // UserSeeder::class,
            // PostSeeder::class,
            // EventSeeder::class
        ]);
        // OutgoingEmailTracking::factory()->count(50)->create();
    }
}
