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
        Schema::create('credit_payment_histories', function (Blueprint $table) {

    $table->id();

    $table->foreignId('customer_id')
        ->constrained()
        ->onDelete('cascade');

    $table->foreignId('credit_sale_id')
        ->nullable()
        ->constrained()
        ->onDelete('cascade');

    $table->decimal('amount', 10, 2);

    $table->string('note')->nullable();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payment_histories');
    }
};
