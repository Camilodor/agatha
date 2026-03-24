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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mercancias_id')->nullable();
            $table->unsignedBigInteger('despachos_id')->nullable();
            $table->unsignedBigInteger('usuarios_id')->nullable();

            $table->string('nombre_recibe');
            $table->string('numero_celular_recibe');

            $table->text('observaciones')->nullable();
            $table->date('fecha_entrega');

            $table->string('estado_entrega')->default('Pendiente');

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
        Schema::dropIfExists('entregas');
    }
};