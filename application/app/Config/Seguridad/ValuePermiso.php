<?php 

namespace App\Config\Seguridad;

class ValuePermiso {

    // administracion
    CONST ADMINISTRACION_USUARIOS_LISTADO = 'administracion_usuarios_listado';
    CONST ADMINISTRACION_USUARIOS_ACTUALIZAR_DIFERENTE = 'administracion_usuarios_actualizar_diferente';
    CONST ADMINISTRACION_ITEMS_LISTADO = 'administracion_items_listado';
    CONST ADMINISTRACION_INFORMES_USUARIOS = 'administracion_informes_usuarios';
    CONST ADMINISTRACION_USURPAR_USUARIOS = 'administracion_usurpar_usuarios';

    // operacion
    CONST OPERACION_CREAR_RECORRIDO = 'operacion_crear_recorrido';
    CONST OPERACION_LISTAR_MIS_RECORRIDOS = 'operacion_listar_mis_recorridos';
    CONST OPERACION_CREAR_PARADA = 'operacion_crear_parada';
    CONST OPERACION_LISTAR_MIS_ITEMS = 'operacion_listar_mis_items';
    
    public static function rolesPermisos(){
        return [
            [
                'nombre' => self::ADMINISTRACION_USUARIOS_LISTADO,
                'administrador-sistema' => true,
                'socio-agencia' => false,
                'operador-agencia' => false,
                'rider' => false,
                'vendedor' => false,
            ],
            [
                'nombre' => self::ADMINISTRACION_USUARIOS_ACTUALIZAR_DIFERENTE,
                'administrador-sistema' => true,
                'socio-agencia' => false,
                'operador-agencia' => false,
                'rider' => false,
                'vendedor' => false,
            ],
            [
                'nombre' => self::ADMINISTRACION_ITEMS_LISTADO,
                'administrador-sistema' => true,
                'socio-agencia' => false,
                'operador-agencia' => false,
                'rider' => false,
                'vendedor' => false,
            ],
            [
                'nombre' => self::ADMINISTRACION_INFORMES_USUARIOS,
                'administrador-sistema' => true,
                'socio-agencia' => false,
                'operador-agencia' => false,
                'rider' => false,
                'vendedor' => false,
            ],
            [
                'nombre' => self::ADMINISTRACION_USURPAR_USUARIOS,
                'administrador-sistema' => true,
                'socio-agencia' => false,
                'operador-agencia' => false,
                'rider' => false,
                'vendedor' => false,
            ],
            [
                'nombre' => self::OPERACION_CREAR_RECORRIDO,
                'administrador-sistema' => true,
                'socio-agencia' => true,
                'operador-agencia' => false,
                'rider' => true,
                'vendedor' => false,
            ],
            [
                'nombre' => self::OPERACION_CREAR_PARADA,
                'administrador-sistema' => true,
                'socio-agencia' => true,
                'operador-agencia' => false,
                'rider' => true,
                'vendedor' => false,
            ],
            [
                'nombre' => self::OPERACION_LISTAR_MIS_RECORRIDOS,
                'administrador-sistema' => true,
                'socio-agencia' => true,
                'operador-agencia' => false,
                'rider' => true,
                'vendedor' => false,
            ],
            [
                'nombre' => self::OPERACION_LISTAR_MIS_ITEMS,
                'administrador-sistema' => true,
                'socio-agencia' => true,
                'operador-agencia' => false,
                'rider' => true,
                'vendedor' => false,
            ],
        ];
    }

}