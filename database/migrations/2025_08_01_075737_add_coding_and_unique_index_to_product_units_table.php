<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->string('coding')->after('title');
            $table->unique(['title', 'coding'], 'product_units_title_coding_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropUnique('product_units_title_coding_unique');
            $table->dropColumn('coding');
        });
    }
};
