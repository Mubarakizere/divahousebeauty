<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'izeremoubarak05@gmail.com'],
            [
                'name' => 'Mubarak',
                'password' => Hash::make('mariam@2025'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info("Admin Account Created: {$user->email}");
    }
}
