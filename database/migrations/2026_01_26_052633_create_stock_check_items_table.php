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
        Schema::create('stock_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_check_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id');
            $table->foreignId('unit_id');
            $table->integer('system_qty');
            $table->integer('real_qty');
            $table->integer('diff_qty'); // real - system
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_check_items');
    }
};
