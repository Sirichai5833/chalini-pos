<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
    'key' => 'system_alert',
    'value' => '🚨 ข้อความแจ้งเตือนระบบจะแสดงตรงนี้จ้า',
]);

    }
}
