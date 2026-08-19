<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEncargadoIdToAppMiembrosDiocesisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_miembros_diocesis', function (Blueprint $table) {
            $table->unsignedInteger('encargado_id')->nullable()->after('State');
            
            // Si quieres que a nivel base de datos esté restringido:
            // $table->foreign('encargado_id')->references('id')->on('app_miembros')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_miembros_diocesis', function (Blueprint $table) {
            // $table->dropForeign(['encargado_id']);
            $table->dropColumn('encargado_id');
        });
    }
}
