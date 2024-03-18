<?php

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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
        });

        $roles = [
            ['nombre' => 'Administrador Sistema', 'codigo' => 'administrador-sistema'],
            ['nombre' => 'Socio agencia', 'codigo' => 'socio-agencia'],
            ['nombre' => 'Operador agencia', 'codigo' => 'operador-agencia'],
            ['nombre' => 'Rider', 'codigo' => 'rider'],
            ['nombre' => 'Vendedor', 'codigo' => 'vendedor'],
        ];

        Rol::insert($roles);

        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rol_id')->nullable();
            $table->foreign('rol_id')->references('id')->on('roles');
            $table->unsignedBigInteger('permiso_id')->nullable();
            $table->foreign('permiso_id')->references('id')->on('permisos');
            $table->timestamps();
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('rol_id')->after('password')->nullable();
            $table->foreign('rol_id')->references('id')->on('roles');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('roles_permisos', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropForeign(['permiso_id']);
        });

        Schema::dropIfExists('roles_permisos');

        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('rol_id');

        });



        
    }
};
