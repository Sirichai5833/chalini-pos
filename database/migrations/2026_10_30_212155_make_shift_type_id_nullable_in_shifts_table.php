<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_type_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        // ไม่บังคับกลับเป็น NOT NULL
        // เพราะอาจมีข้อมูลกะที่ shift_type_id เป็น NULL อยู่แล้ว
    }
};