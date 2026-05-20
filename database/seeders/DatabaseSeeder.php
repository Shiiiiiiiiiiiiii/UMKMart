<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Admin ────────────────────────────────
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        // ── Seller ──────────────────────────────
        User::create([
            'name'     => 'Seller',
            'email'    => 'seller@gmail.com',
            'password' => 'password',
            'role'     => 'seller',
        ]);

        // ── Buyer / User ─────────────────────────
        User::create([
            'name'     => 'Buyer',
            'email'    => 'buyer@gmail.com',
            'password' => 'password',
            'role'     => 'buyer',
        ]);

        $this->command->info('✅ Database seeded successfully with clean plain users!');
        $this->command->info('');
        $this->command->info('📧 Login Credentials (password: "password" for all):');
        $this->command->info('   Admin : admin@gmail.com');
        $this->command->info('   Seller: seller@gmail.com');
        $this->command->info('   Buyer : buyer@gmail.com');
    }
}
