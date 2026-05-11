<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_cliente')
                ->constrained('usuarios');

            $table->foreignId('id_vendedor')
                ->constrained('usuarios');

            $table->string('lugar', 100)->nullable();

            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();

            $table->enum('status', [
                'pendiente',
                'confirmada',
                'cancelada',
                'completada'
            ])->nullable();

            $table->date('fecha');

            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('venta');
    }
};
