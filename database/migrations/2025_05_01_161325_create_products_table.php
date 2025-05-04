<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // สร้างคอลัมน์ id (primary key)
            
            // ข้อมูลสินค้า
            $table->string('name'); // ชื่อสินค้า
            $table->string('barcode')->nullable(); // บาร์โค้ด
            $table->string('sku')->nullable(); // SKU
            $table->string('unit')->default('ชิ้น'); // หน่วยนับ
            
            // ราคาและต้นทุน
            $table->decimal('cost_price', 10, 2)->nullable(); // ราคาทุน
            $table->decimal('selling_price', 10, 2); // ราคาขาย
            $table->decimal('promotion_price', 10, 2)->nullable(); // ราคาพิเศษ
            
            // ข้อมูลของแถม
            $table->boolean('has_gift')->default(false); // มีของแถมหรือไม่
            $table->string('gift_name')->nullable(); // ชื่อของแถม
            
            // คลังสินค้า
            $table->integer('stock')->default(0); // จำนวนคงเหลือ
            $table->boolean('track_stock')->default(true); // ติดตาม stock หรือไม่
            
            // สถานะสินค้า
            $table->boolean('is_online')->default(true); // ขายออนไลน์ได้หรือไม่
            $table->boolean('is_active')->default(true); // เปิดขายอยู่หรือไม่
            
            // รูปภาพและคำอธิบาย
            $table->string('image')->nullable(); // รูปภาพสินค้า
            $table->text('description')->nullable(); // คำอธิบายสินค้า
            
            // QR Code
            $table->string('qr_code')->nullable(); // QR code
            
            // Foreign Key for category
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete(); // category_id (เชื่อมโยงกับตาราง categories)
            
            $table->timestamps(); // created_at และ updated_at

            $table->integer('warehouse_stock')->default(0); // ของในคลัง
$table->integer('store_stock')->default(0); // ของที่นำมาวางขายหน้าร้าน
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
