<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPropioLatLngToAppMiembrosLumisialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_miembros_lumisial', function (Blueprint $table) {
            $table->boolean('es_propio')->default(0)->after('status');
            $table->decimal('latitud', 10, 8)->nullable()->after('es_propio');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_miembros_lumisial', function (Blueprint $table) {
            $table->dropColumn(['es_propio', 'latitud', 'longitud']);
        });
    }
}
