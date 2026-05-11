<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('reviews_productos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_cliente')->constrained('usuarios');
            $table->foreignId('id_producto')->constrained('productos');

            $table->integer('calificacion');
            $table->text('comentario')->nullable();

            $table->enum('estado', ['activa', 'oculta', 'eliminada'])->default('activa');
            $table->boolean('anonimo')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews_productos');
    }
};
