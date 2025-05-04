<?php

// database/migrations/xxxx_add_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // ตรวจสอบว่าคอลัมน์ 'role' มีอยู่ในตารางหรือไม่
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staff'); // เพิ่ม role
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // ลบคอลัมน์ 'role' หากมีอยู่
            $table->dropColumn('role');
        });
    }
}
