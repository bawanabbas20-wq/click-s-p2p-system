<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Creates the initial admin account for fresh deployments.
 * Run with: php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run()
    {
        // Check if admin already exists
        if (User::where('email', 'admin@click.com')->exists()) {
            $this->command->warn('Admin user already exists. Skipping...');
            return;
        }

        // Create the main admin account
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@click.com',
            'password' => Hash::make('ChangeMe123!'),
            'role' => 'admin',
        ]);

        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║           ADMIN ACCOUNT CREATED SUCCESSFULLY             ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║  Email:    admin@click.com                               ║');
        $this->command->info('║  Password: ChangeMe123!                                  ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->warn('║  ⚠️  IMPORTANT: Change this password after first login!  ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
