<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('venta_detalle', function (Blueprint $table) {
            $table->id();

            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);

            $table->foreignId('id_venta')->constrained('venta');
            $table->foreignId('id_producto')->constrained('productos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('venta_detalle');
    }
};
