<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_ingresos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('miembro_id'); // o integer() según el id de tu tabla app_miembros
            $table->date('fecha_ingreso');
            $table->enum('tipo_ingreso', ['consagracion', 'readmision']);
            $table->integer('numero_ingreso');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Opcional, pero recomendado si app_miembros usa id
            // $table->foreign('miembro_id')->references('id')->on('app_miembros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_ingresos');
    }
}
