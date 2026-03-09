<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Phase 5 – Demo user seeder
 *
 * WHY: A known demo user (email + password) lets us test login, cart, and
 * split payment without registering every time. rewards_balance is set
 * after create() because it's not in User's $fillable (security: we
 * don't allow mass assignment of balance from requests). Setting it in
 * code here is safe because the seeder runs in a controlled environment.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Set rewards outside fillable; only in seeders/code, never from request
        $user->rewards_balance = 500.00;
        $user->save();
    }
}
