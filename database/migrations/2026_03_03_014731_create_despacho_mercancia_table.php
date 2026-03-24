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
        Schema::create('despacho_mercancia', function (Blueprint $table) {
    $table->id();
    $table->foreignId('despacho_id')->constrained()->onDelete('cascade');
    $table->foreignId('mercancia_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despacho_mercancia');
    }
};
