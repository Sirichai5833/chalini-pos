<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_sale_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('credit_sale_id');

            $table->unsignedBigInteger('product_id');

            $table->string('product_name')->nullable();

            $table->integer('qty')->default(1);

            $table->decimal('price', 10, 2)->default(0);

            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();

            // FK
            $table->foreign('credit_sale_id')
                ->references('id')
                ->on('credit_sales')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_sale_items');
    }
};