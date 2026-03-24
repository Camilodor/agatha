<?php 
use Illuminate\Database\Migrations\Migration; 
use Illuminate\Database\Schema\Blueprint; 
use Illuminate\Support\Facades\Schema; 
return new class extends Migration {
/**
 * Run the migrations.
 */
public function up(): void
{
    Schema::create('mercancias', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('proveedores_id')->nullable();
        $table->unsignedBigInteger('usuarios_id')->nullable();

        $table->date('fecha_ingreso');
        $table->string('numero_remesa', 50)->nullable();

        $table->string('origen_mercancia', 100);
        $table->string('destino_mercancia', 100);

        $table->string('nombre_remitente', 100);
        $table->string('documento_remitente', 50);
        $table->string('direccion_remitente', 150);
        $table->string('celular_remitente', 20);

        $table->string('nombre_destinatario', 100);
        $table->string('documento_destinatario', 50);
        $table->string('direccion_destinatario', 150);
        $table->string('celular_destinatario', 20);

        $table->decimal('valor_declarado', 10, 2);
        $table->decimal('valor_flete', 10, 2);
        $table->decimal('valor_total', 10, 2);

        $table->decimal('peso', 10, 2);
        $table->integer('unidades');

        $table->text('observaciones')->nullable();

        $table->unsignedBigInteger('tipo_pago_id')->nullable();

        $table->timestamps();

        // Relaciones
        $table->foreign('tipo_pago_id')
              ->references('id')
              ->on('tipospago')
              ->onDelete('set null');

        $table->foreign('proveedores_id')
              ->references('id')
              ->on('proveedores')
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
    Schema::dropIfExists('mercancias');
}
};