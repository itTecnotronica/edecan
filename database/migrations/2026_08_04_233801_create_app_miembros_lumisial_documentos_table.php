<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppMiembrosLumisialDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_miembros_lumisial_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lumisial_uuid');
            $table->binary('file_data')->nullable(); // Para soportar BLOB en Laravel (a veces requiere manual ALTER para LONGBLOB)
            $table->string('mime_type');
            $table->string('original_name');
            $table->timestamps();
            
            // Si quieres que dependa de 'uuid' en la tabla 'app_miembros_lumisial'
            // $table->foreign('lumisial_uuid')->references('uuid')->on('app_miembros_lumisial')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_miembros_lumisial_documentos');
    }
}
