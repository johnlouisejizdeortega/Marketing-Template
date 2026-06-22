<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the single admin account that can reach the dashboard.
 *
 * Credentials come from the environment so production secrets never live in
 * source control. Defaults are provided for local development only — change
 * ADMIN_PASSWORD in production and after first login.
 *
 * Run on its own with:
 *   php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Site Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
        );
    }
}
