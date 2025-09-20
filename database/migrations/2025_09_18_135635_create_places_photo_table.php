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
        Schema::create('places_photo', function (Blueprint $table) {
        $table->id();
        $table->foreignId('place_id')->constrained('places', 'place_id')->onDelete('cascade');
        $table->string('caption')->nullable();
        $table->string('description')->nullable();
        $table->string('filename')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places_photo');
    }
};
