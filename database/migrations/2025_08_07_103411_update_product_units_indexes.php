<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropUnique('product_units_title_coding_unique');
            $table->unique('title', 'product_units_title_unique');
            $table->unique('coding', 'product_units_coding_unique');
        });
    }

    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropUnique('product_units_title_unique');
            $table->dropUnique('product_units_coding_unique');
            $table->unique(['title', 'coding'], 'product_units_title_coding_unique');
        });
    }
};
