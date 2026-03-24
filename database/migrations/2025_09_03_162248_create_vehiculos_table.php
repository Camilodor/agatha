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
        Schema::create('vehiculos', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('usuarios_id')->nullable();
            $table->string('numero_placas', 20);
            $table->string('nombre_marca_vehiculo', 50);
            $table->string('nombre_propietario_vehiculo', 100);
            $table->string('documento_propietario_vehiculo', 50);
            $table->string('numero_celular_propietario', 50);
            $table->string('direccion_propietario', 50);
            $table->string('ciudad_propietario', 50);
            $table->string('numero_modelo_anio', 10);
            $table->string('color_vehiculo', 30);
            $table->date('fecha_vencimiento_soat');
            $table->date('fecha_vencimiento_tecno');
            $table->string('nombre_satelital', 50);
            $table->string('usuario_satelital', 50);
            $table->string('contrasena_satelital', 50);
            $table->decimal('capacidad_carga', 8, 2);
            $table->timestamps();

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
        Schema::dropIfExists('vehiculos');
    }
};
