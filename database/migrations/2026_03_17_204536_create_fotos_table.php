<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('tipo_rol_id');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('capacidad_carga');
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('estado_entrega');
        });

        Schema::table('devoluciones', function (Blueprint $table) {
            $table->string('foto_url')->nullable()->after('estado_devolucion');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });

        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });

        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });

        Schema::table('devoluciones', function (Blueprint $table) {
            $table->dropColumn('foto_url');
        });
    }
};