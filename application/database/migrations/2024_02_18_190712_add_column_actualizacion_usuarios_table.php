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
         Schema::table('usuarios', function (Blueprint $table) {
            $table->string("actualizacion")->default("")->after('password');
            $table->string("version")->default("")->after('actualizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn("actualizacion");
            $table->dropColumn("version");
        });
    }
};
