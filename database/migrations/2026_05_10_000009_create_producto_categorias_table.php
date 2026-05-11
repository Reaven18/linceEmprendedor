<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_categorias', function (Blueprint $table) {
            $table->foreignId('id_producto')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('id_categoria')->constrained('categorias')->cascadeOnDelete();

            $table->primary(['id_producto', 'id_categoria']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_categorias');
    }
};
