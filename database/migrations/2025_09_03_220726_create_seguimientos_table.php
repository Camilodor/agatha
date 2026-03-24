<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mercancias_id')->nullable();

            $table->string('estado'); 
            // Ej: Ingresado a bodega, En camino, Entrega exitosa, etc

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->foreign('mercancias_id')
                  ->references('id')
                  ->on('mercancias')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};