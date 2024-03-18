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
      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table("roles")->where("codigo", "operador-agencia")->delete();

        DB::table("roles")->where("codigo", "rider")->update([
            "id" => 3,
        ]);

        DB::table("roles")->where("codigo", "vendedor")->update([
            "id" =>  4,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
