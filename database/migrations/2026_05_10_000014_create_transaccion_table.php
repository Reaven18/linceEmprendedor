<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion', function (Blueprint $table) {
            $table->foreignId('id_venta')->constrained('venta');

            $table->integer('consecutivo');
            $table->decimal('total', 10, 2);
            
            $table->foreignId('id_metodo_de_pago')->constrained('metodo_pago');

            $table->primary(['id_venta', 'consecutivo']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaccion');
    }
};
