<?php

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

        Schema::create('invitaciones_estados', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("codigo");
            $table->string("color");
        });

        $estados = [
            [
                "nombre" => "Invitado",
                "codigo" => "invitado",
                "color" => "#FFB133",
            ],
            [
                "nombre" => "Aceptado",
                "codigo" => "aceptado",
                "color" => "#00c853",
            ],
            [
                "nombre" => "Rechazado",
                "codigo" => "rechazado",
                "color"  => "#F31B1B"
            ],
            [
                "nombre" => "Terminado",
                "codigo" => "terminado",
                "color"  => "#FF5733"
            ],
        ];

        foreach($estados as $estado){

            DB::table("invitaciones_estados")->insert([
                "nombre" => $estado["nombre"],
                "codigo" => $estado["codigo"],
                "color" => $estado["color"],
            ]);

        }

    
        Schema::create('invitaciones_empresas', function (Blueprint $table) {
            $table->id();

            $table->string("email_invitado");

            $table->unsignedBigInteger("invitador_id");
            $table->foreign('invitador_id')->references('id')->on('usuarios');

            $table->unsignedBigInteger("empresa_id");
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger("invitacion_estado_id");
            $table->foreign('invitacion_estado_id')->references('id')->on('invitaciones_estados');

            $table->unsignedBigInteger("rol_id");
            $table->foreign('rol_id')->references('id')->on('roles');

            $table->softDeletes();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitaciones_empresas' , function (Blueprint $table) {
            $table->dropForeign(["invitador_id"]);
            $table->dropForeign(["empresa_id"]);
            $table->dropForeign(["invitacion_estado_id"]);
            $table->dropForeign(["rol_id"]);
        });

        Schema::dropIfExists('invitaciones_estados');
        Schema::dropIfExists('invitaciones_empresas');
    }
};
