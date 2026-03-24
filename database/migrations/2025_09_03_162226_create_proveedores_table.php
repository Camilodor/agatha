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
        Schema::create('proveedores', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('usuarios_id'); // 🔗 Relación con usuario
    $table->string('nombre', 255);
    $table->text('descripcion')->nullable();
    $table->timestamps();

    $table->foreign('usuarios_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
