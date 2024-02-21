<?php

use App\Models\Pais;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('paises', function (Blueprint $table) {
            $table->string('time_zone');
        });

        Pais::where("nombre", "Argentina")->update([
            "time_zone" => "America/Argentina/Buenos_Aires"
        ]);

        Pais::where("nombre", "Bolivia")->update([
            "time_zone" => "America/La_Paz"
        ]);

        Pais::where("nombre", "Brasil")->update([
            "time_zone" => "America/Sao_Paulo"
        ]);

        Pais::where("nombre", "Colombia")->update([
            "time_zone" => "America/Bogota"
        ]);

        Pais::where("nombre", "Ecuador")->update([
            "time_zone" => "America/Guayaquil"
        ]);

        Pais::where("nombre", "Paraguay")->update([
            "time_zone" => "America/Asuncion"
        ]);

        Pais::where("nombre", "Peru")->update([
            "time_zone" => "America/Lima"
        ]);

        Pais::where("nombre", "Uruguay")->update([
            "time_zone" => "America/Montevideo"
        ]);

        Pais::where("nombre", "Venezuela")->update([
            "time_zone" => "America/Caracas"
        ]);

        Pais::where("nombre", "España")->update([
            "time_zone" => "Europe/Madrid"
        ]);

        Schema::table('usuarios', function (Blueprint $table) {
            $table->integer('pais_id')->default(1)->change();
        });

        User::whereNull("pais_id")->update([
            "pais_id" => 1
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paises', function (Blueprint $table) {
            $table->dropColumn('time_zone');
        });
    }
};
