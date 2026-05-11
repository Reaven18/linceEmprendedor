<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_vendedor')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->unsigned();

            $table->integer('stock')->default(0);
            $table->boolean('es_perecedero')->default(false);

            $table->enum('status', ['disponible', 'agotado', 'pausado'])
                ->default('disponible');

            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
