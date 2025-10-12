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
    Schema::table('product_batches', function (Blueprint $table) {
        $table->unsignedBigInteger('product_unit_id')->nullable()->after('product_id');

        // ถ้ามี foreign key
        $table->foreign('product_unit_id')->references('id')->on('product_units')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('product_batches', function (Blueprint $table) {
        $table->dropForeign(['product_unit_id']);
        $table->dropColumn('product_unit_id');
    });
}

};
