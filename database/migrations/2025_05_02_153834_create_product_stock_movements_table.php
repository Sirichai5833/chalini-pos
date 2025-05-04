<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // สินค้าที่เกี่ยวข้อง
            $table->enum('type', ['import', 'transfer_to_store', 'sale', 'return', 'adjust'])->comment('ประเภทการเคลื่อนไหว'); // ประเภทการเคลื่อนไหว
            $table->integer('quantity'); // จำนวนที่เปลี่ยน
            $table->string('note')->nullable(); // หมายเหตุเพิ่มเติม
            $table->string('created_by')->nullable(); // ชื่อผู้ทำรายการ (optional)
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_stock_movements');
    }
}
