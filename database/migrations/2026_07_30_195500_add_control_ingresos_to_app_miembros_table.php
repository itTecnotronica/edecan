<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddControlIngresosToAppMiembrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_miembros', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'inactivo', 'baja_definitiva'])->default('activo');
            $table->integer('cantidad_ingresos')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_miembros', function (Blueprint $table) {
            $table->dropColumn(['estado', 'cantidad_ingresos']);
        });
    }
}
