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
        Schema::create('users', function (Blueprint $table) {
           $table->id(); 
            $table->string('nombre_usuario')->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->unsignedBigInteger('tipo_documento_id');
            $table->bigInteger('numero_documento')->unique();
            $table->bigInteger('celular')->unique();
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('email')->unique();
            $table->string('contrasena');
            $table->unsignedBigInteger('tipo_rol_id');
            $table->timestamps();
           



            $table->foreign('tipo_documento_id')->references('id')->on('tiposdocumento');
        $table->foreign('tipo_rol_id')->references('id')->on('tiposrol');
        });
        
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
        
    }
};
