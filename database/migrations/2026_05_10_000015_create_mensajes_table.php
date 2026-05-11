<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_emisor')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->foreignId('id_receptor')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->text('contenido');
            $table->boolean('leido')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mensajes');
    }
};
