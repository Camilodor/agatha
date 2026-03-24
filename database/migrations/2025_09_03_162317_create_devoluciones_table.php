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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mercancias_id')->nullable();
            $table->unsignedBigInteger('despachos_id')->nullable();
            $table->unsignedBigInteger('usuarios_id')->nullable();

            $table->date('fecha_devolucion');
            $table->text('motivo_devolucion');

            $table->string('estado_devolucion')->default('Pendiente');
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->foreign('mercancias_id')
                  ->references('id')
                  ->on('mercancias')
                  ->onDelete('set null');

            $table->foreign('despachos_id')
                  ->references('id')
                  ->on('despachos')
                  ->onDelete('set null');

            $table->foreign('usuarios_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};