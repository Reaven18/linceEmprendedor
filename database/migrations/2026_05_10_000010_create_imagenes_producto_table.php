<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('imagenes_producto', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_producto')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->text('url_imagen');
            $table->integer('orden')->default(1);

            $table->unique(['id_producto', 'orden']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagenes_producto');
    }
};
