<?php 

namespace App\Config\Seguridad;

class ValuePermiso {

    CONST ADMINISTRACION_USUARIOS_LISTADO = 'administracion_usuarios_listado';
    CONST ADMINISTRACION_USUARIOS_ACTUALIZAR_DIFERENTE = 'administracion_usuarios_actualizar_diferente';
    CONST ADMINISTRACION_ITEMS_LISTADO = 'administracion_items_listado';
    CONST ADMINISTRACION_INFORMES_USUARIOS = 'administracion_informes_usuarios';

    public static function rolesPermisos(){
        return [
            [
                'nombre' => self::ADMINISTRACION_USUARIOS_LISTADO,
                'rider' => false,
                'administrador' => true,
            ],
            [
                'nombre' => self::ADMINISTRACION_USUARIOS_ACTUALIZAR_DIFERENTE,
                'rider' => false,
                'administrador' => true,
            ],
            [
                'nombre' => self::ADMINISTRACION_ITEMS_LISTADO,
                'rider' => false,
                'administrador' => true,
            ],
            [
                'nombre' => self::ADMINISTRACION_INFORMES_USUARIOS,
                'rider' => false,
                'administrador' => true,
            ],
        ];
    }

}