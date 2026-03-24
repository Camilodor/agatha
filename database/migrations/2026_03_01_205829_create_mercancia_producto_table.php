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
        Schema::create('mercancia_producto', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('mercancia_id');
    $table->unsignedBigInteger('producto_id');

    $table->integer('cantidad');

    $table->timestamps();

    $table->foreign('mercancia_id')
          ->references('id')
          ->on('mercancias')
          ->onDelete('cascade');

    $table->foreign('producto_id')
          ->references('id')
          ->on('productos')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercancia_producto');
    }
};
