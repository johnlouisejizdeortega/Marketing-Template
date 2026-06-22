<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // No database is used. The dashboard authenticates with a single
        // shared password from env DASHBOARD_PASSWORD (see config/dashboard.php).
    }
}
