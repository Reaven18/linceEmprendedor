<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('metodo_pago', function (Blueprint $table) {
            $table->id();
            $table->string('metodo', 50);
        });
    }
    public function down()
    {
        Schema::dropIfExists('metodo_pago');
    }
};
