<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SubscriptionPlansSeeder::class,
            AdminUserSeeder::class,
            RegionsTableSeeder::class,
            DistrictsTableSeeder::class,
            WardsTableSeeder::class,
            VillagesTableSeeder::class,
        ]);
    }
}
