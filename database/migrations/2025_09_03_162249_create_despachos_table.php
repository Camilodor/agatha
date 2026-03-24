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
        Schema::create('despachos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculos_id')->nullable();
            $table->unsignedBigInteger('usuarios_id')->nullable();
            $table->unsignedBigInteger('tipo_pago_id')->nullable();

            $table->date('fecha_despacho');

            $table->decimal('negociacion', 10, 2);
            $table->decimal('anticipo', 10, 2);
            $table->decimal('saldo', 10, 2);

            $table->text('observaciones_mer')->nullable();

            $table->timestamps();

            // Relaciones (Foreign Keys)
            

            $table->foreign('vehiculos_id')
                  ->references('id')
                  ->on('vehiculos')
                  ->onDelete('set null');

            $table->foreign('usuarios_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('tipo_pago_id')
                  ->references('id')
                  ->on('tipospago')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despachos');
    }
};