<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // สร้าง User 1 ตัวที่มี role เป็น 'admin'
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'manage@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
    
        // สร้าง User 1 ตัวที่มี role เป็น 'staff'
        User::factory()->create([
            'name' => 'Test Staff',
            'email' => 'member101@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'member',
        ]);

        //   User::factory()->create([
        //     'name' => 'StaffOnline',
        //     'email' => 'StaffOnline@gmail.com',
        //     'password' => Hash::make('12345678'),
        //     'role' => 'member',
        // ]);

    }
    
}
