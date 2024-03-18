<?php

use App\Models\Empresa;
use App\Models\Rol;
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
        DB::table("usuarios")->where("rol_id", "!=", Rol::ADMINISTRADOR_SISTEMA)->update([
            "rol_id" => 2
        ]);

        DB::table("roles")->where("codigo", "socio-agencia")->update([
            "codigo" => "agencia-repartidor",
            "nombre" => "Agencia repartidor"
        ]);

    
        Schema::table('usuarios_empresas', function (Blueprint $table) {
            $table->unsignedBigInteger("rol_id")->after("empresa_id")->nullable();
            $table->foreign('rol_id')->references('id')->on('roles');
        });

       
        DB::table('usuarios_empresas')->where("empresa_id", Empresa::INDEPENDIENTE)->update([
            "rol_id" => 2
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios_empresas', function (Blueprint $table) {
            $table->dropForeign(["rol_id"]);
            $table->dropColumn("rol_id");
        });

        
    }
};
