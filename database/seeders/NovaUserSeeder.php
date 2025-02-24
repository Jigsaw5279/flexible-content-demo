<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class NovaUserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
        ]);
    }
}
