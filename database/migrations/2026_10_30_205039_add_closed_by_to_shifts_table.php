<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('shifts', 'closed_by')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->foreignId('closed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shifts', 'closed_by')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropForeign(['closed_by']);
                $table->dropColumn('closed_by');
            });
        }
    }
};
