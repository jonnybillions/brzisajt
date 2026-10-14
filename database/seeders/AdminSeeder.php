<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Administrator iz ADMIN_EMAIL / ADMIN_PASSWORD. Javne registracije nema. */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('brzisajt.admin');

        if (! $config['email'] || ! $config['password']) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD nisu podešeni — administrator nije napravljen.');

            return;
        }

        $user = User::firstOrNew(['email' => $config['email']]);
        $user->forceFill([
            'name' => $config['name'],
            'password' => $config['password'],
            'is_admin' => true,
        ])->save();
    }
}
