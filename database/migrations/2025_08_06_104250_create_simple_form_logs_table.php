<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('simple_form_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('loggable'); // loggable_id and loggable_type
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // e.g., 'created', 'updated'
            $table->json('old_data')->nullable(); // Old values for updates
            $table->json('new_data'); // New values
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['loggable_type', 'loggable_id']); // Index for polymorphic queries
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('simple_form_logs');
    }

};
