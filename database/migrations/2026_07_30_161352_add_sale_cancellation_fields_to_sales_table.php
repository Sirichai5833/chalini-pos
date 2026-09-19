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
    Schema::table('sales', function (Blueprint $table) {
        $table->string('status')
            ->default('completed')
            ->index();

        $table->text('cancel_reason')
            ->nullable();

        $table->unsignedBigInteger('cancelled_by')
            ->nullable();

        $table->timestamp('cancelled_at')
            ->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropColumn([
            'status',
            'cancel_reason',
            'cancelled_by',
            'cancelled_at',
        ]);
    });
}
};
