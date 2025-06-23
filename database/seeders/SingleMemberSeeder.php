<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SingleMemberSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'StaffOnline',
            'email' => 'StaffOnline@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'member',
            'room_number' => '601',
        ]);
    }
}
