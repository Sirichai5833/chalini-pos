<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->decimal('retail_price', 12, 2)
                ->default(0)
                ->after('cost_price');

            $table->decimal('wholesale_price', 12, 2)
                ->nullable()
                ->after('retail_price');

            $table->decimal('member_price', 12, 2)
                ->nullable()
                ->after('wholesale_price');

            $table->decimal('wholesale_min_quantity', 12, 2)
                ->nullable()
                ->after('member_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn([
                'retail_price',
                'wholesale_price',
                'member_price',
                'wholesale_min_quantity',
            ]);
        });
    }
};