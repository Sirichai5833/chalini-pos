<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('proof_image')->nullable()->after('status'); // รูปหลักฐาน
        $table->text('cancel_reason')->nullable()->after('proof_image'); // หมายเหตุยกเลิก
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['proof_image', 'cancel_reason']);
    });
}

};
