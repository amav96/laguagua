<?php

namespace App\Console\Commands;

use App\Config\Seguridad\ValuePermiso;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolPermiso;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerarPermisos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar-permisos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generacion de permisos para cada rol';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $permisos = ValuePermiso::rolesPermisos();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        DB::table('roles_permisos')->truncate();
        DB::table('permisos')->truncate();

        if($permisos && count($permisos) > 0){
            foreach($permisos as $item){

                $permiso = new Permiso([
                    'nombre' => $item["nombre"]
                ]);
                $permiso->timestamps = false;
                $permiso->save();
                
                if(isset($item["administrador-sistema"])){
                    RolPermiso::insert([
                        'rol_id' => Rol::ADMINISTRADOR_SISTEMA,
                        'permiso_id' => $permiso->id,
                        'created_at' => now(),
                    ]);
                }

                if(isset($item["socio-agencia"])){
                    RolPermiso::insert([
                        'rol_id' => Rol::SOCIO_AGENCIA,
                        'permiso_id' => $permiso->id,
                        'created_at' => now(),
                    ]);
                }

                if(isset($item["operador-agencia"])){
                    RolPermiso::insert([
                        'rol_id' => Rol::OPERADOR_AGENCIA,
                        'permiso_id' => $permiso->id,
                        'created_at' => now(),
                    ]);
                }

                if(isset($item["rider"])){
                    RolPermiso::insert([
                        'rol_id' => Rol::RIDER,
                        'permiso_id' => $permiso->id,
                        'created_at' => now(),
                    ]);
                }

                if(isset($item["vendedor"])){
                    RolPermiso::insert([
                        'rol_id' => Rol::VENDEDOR,
                        'permiso_id' => $permiso->id,
                        'created_at' => now(),
                    ]);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Permisos generados correctamente";
    }
}
