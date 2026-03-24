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
        Schema::create('referencias_laborales', function (Blueprint $table) {
              $table->id();
            $table->unsignedBigInteger('usuarios_id');
            $table->string('nombre', 45);
            $table->string('apellido', 45);
            $table->string('parentezco', 45);
            $table->string('numero_documento', 10);
            $table->unsignedBigInteger('tipo_documento_id');
            $table->string('numero_celular', 10)->unique();
            $table->string('numero_direccion', 255);
            $table->timestamps();

            $table->foreign('usuarios_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('tipo_documento_id')
                  ->references('id')
                  ->on('tiposdocumento')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referencias_laborales');
    }
};
