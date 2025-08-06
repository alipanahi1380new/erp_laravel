<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->enum('unit_type', ['barcode', 'not_barcode']);
            $table->foreignId('user_id_maker')->constrained('users')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->boolean('can_have_float_value')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};