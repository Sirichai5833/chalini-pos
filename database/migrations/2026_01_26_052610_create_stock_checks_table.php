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
        Schema::create('stock_checks', function (Blueprint $table) {
    $table->id();
    $table->date('check_date');
    $table->string('cycle'); // เช่น 2026-01
    $table->foreignId('checked_by')->constrained('users');
    $table->text('remark')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_checks');
    }
};
