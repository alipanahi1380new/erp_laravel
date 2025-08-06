<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->renameColumn('store_name', 'title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->renameColumn('title', 'store_name');
        });
    }
};
