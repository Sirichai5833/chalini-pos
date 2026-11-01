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
    Schema::table('shifts', function (Blueprint $table) {
        $table->decimal('expected_qr', 12, 2)->default(0);
        $table->decimal('closing_qr', 12, 2)->nullable();
        $table->decimal('qr_difference', 12, 2)->default(0);
    });
}

public function down(): void
{
    Schema::table('shifts', function (Blueprint $table) {
        $table->dropColumn([
            'expected_qr',
            'closing_qr',
            'qr_difference',
        ]);
    });
}
};
