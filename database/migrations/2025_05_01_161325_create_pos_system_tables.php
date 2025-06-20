<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. ตารางประเภทสินค้า
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. ตารางสินค้า
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(true);
            $table->timestamps();
        });

        // 3. ตารางหน่วยสินค้า
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('unit_name');
            $table->integer('unit_quantity');
            $table->decimal('wholesale', 10, 2)->nullable(); 
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->timestamps();
            $table->string('barcode')->nullable(); // <--- เพิ่มเข้าไป

        });

        // 4. ตารางคงคลัง
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('warehouse_stock')->default(0);
            $table->integer('store_stock')->default(0);
            $table->boolean('track_stock')->default(true);
            $table->timestamps();
        });

        // 5. ประวัติการเคลื่อนไหว
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->integer('unit_quantity'); // ✅ เพิ่มฟิลด์นี้
            $table->string('unit')->nullable();
            $table->string('location')->default('store'); // ✅ เพิ่ม location
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 6. ตารางล็อตสินค้า/วันหมดอายุ
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');

            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        // 7. ตารางแพ็คสินค้า
        Schema::create('product_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('child_product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });

        // 8. ตารางขาย
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->references('id')->on('users')->onDelete('cascade');  // เพิ่ม constraint foreign key
            $table->timestamp('sale_date');
            $table->string('sale_type');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        // 9. รายการขายสินค้า
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_unit_id')->constrained('product_units')->onDelete('cascade'); // ✅ เพิ่มตรงนี้
            $table->integer('quantity');
            $table->integer('unit_quantity'); // ✅ เพิ่มฟิลด์นี้
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('product_packs');
        Schema::dropIfExists('product_batches');
        Schema::dropIfExists('product_stock_movements');
        Schema::dropIfExists('product_stocks');
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
