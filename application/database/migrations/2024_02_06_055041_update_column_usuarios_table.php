<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Haz que el campo 'nombre' sea nullable
            $table->string('nombre')->nullable()->change();
            
            // Haz que el campo 'password' sea nullable
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Revertir los cambios (esto es opcional dependiendo de tus necesidades)
            $table->string('nombre')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
