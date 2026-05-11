<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_rol');

            $table->enum('estado', ['activa', 'oculta', 'rechazada'])->default('activa');
            $table->timestamp('asignado_en')->useCurrent();

            $table->primary(['id_usuario', 'id_rol']);

            $table->foreign('id_usuario')->references('id')->on('usuarios')->cascadeOnDelete();
            $table->foreign('id_rol')->references('id')->on('roles')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('usuario_roles');
    }
};
