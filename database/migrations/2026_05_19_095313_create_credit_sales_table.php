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
       Schema::create('credit_sales', function (Blueprint $table) {

    $table->id();

    $table->foreignId('customer_id')
        ->constrained()
        ->onDelete('cascade');

    $table->decimal('total_amount', 10, 2);

    $table->decimal('paid_amount', 10, 2)->default(0);

    $table->decimal('remaining_amount', 10, 2);

    $table->date('due_date')->nullable();

    $table->enum('status', [
        'unpaid',
        'partial',
        'paid'
    ])->default('unpaid');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_sales');
    }
};
