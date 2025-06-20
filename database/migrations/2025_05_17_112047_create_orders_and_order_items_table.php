<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ตารางคำสั่งซื้อ
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // เช่น #ORD1001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_method')->nullable(); // เช่น โอน, เก็บเงินปลายทาง
            $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled
            $table->string('tracking_number')->nullable();
            $table->timestamp('order_date')->useCurrent();
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });

        // รายการสินค้าในคำสั่งซื้อ
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_unit_id')->constrained('product_units')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // ราคาต่อหน่วย
            $table->decimal('total', 10, 2); // = quantity * price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
