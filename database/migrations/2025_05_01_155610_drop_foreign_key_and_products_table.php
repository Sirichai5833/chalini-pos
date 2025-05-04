<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyAndProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ลบ foreign key constraint ก่อน
        Schema::table('products', function (Blueprint $table) {
            // ตรวจสอบว่า constraint มีหรือไม่ก่อนที่จะลบ
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeign(['category_id']);
            }
        });

        // ลบตาราง products
        Schema::dropIfExists('products');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ทำการสร้างตาราง products ใหม่ (ตามต้องการ)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // คอลัมน์ที่เหลือ เช่น barcode, sku, unit, ฯลฯ
            $table->foreignId('category_id')->constrained(); // เพิ่ม constraint กลับเข้าไป
            $table->timestamps();
        });
    }
}
