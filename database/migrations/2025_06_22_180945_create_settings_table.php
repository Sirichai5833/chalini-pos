<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // เพิ่มคอลัมน์ key แบบ unique
            $table->text('value')->nullable(); // ค่าที่จะแสดง (ข้อความแจ้งเตือน)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings'); // แค่ลบตารางทิ้ง ไม่ต้องใส่ schema ใหม่ในนี้
    }
};
