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
       Schema::create('shifts', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->foreignId('shift_type_id')->constrained();

    $table->decimal('opening_cash',10,2);

    $table->decimal('expected_cash',10,2)->nullable();

    $table->decimal('closing_cash',10,2)->nullable();

    $table->decimal('cash_difference',10,2)->nullable();

    $table->timestamp('opened_at');

    $table->timestamp('closed_at')->nullable();

    $table->enum('status',['open','closed'])->default('open');

    $table->text('note')->nullable();

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
