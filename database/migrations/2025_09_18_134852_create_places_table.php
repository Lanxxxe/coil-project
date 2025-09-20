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
        Schema::create('places', function (Blueprint $table) {
            $table->id('place_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->enum ('type', ['landmark', 'restaurant', 'heritage', 'gallery', 'museum', 'other'])->default('other');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
