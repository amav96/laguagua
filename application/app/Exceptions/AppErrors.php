<?php

namespace App\Exceptions;

class AppErrors
{
    // Request 
    const WRONG_INPUT_DATA_CODE = "WRONG_INPUT_DATA";
    const WRONG_INPUT_DATA_MESSAGE = "";

    // User service
    const WRONG_USER_PASS_CODE = "WRONG_USER_PASSWORD";
    const WRONG_USER_PASS_MESSAGE = "Wrong user or password";

    const USER_BLOCKED_CODE = "USER_BLOQUED";
    const USER_BLOCKED_MESSAGE = "Account blocked for security purposes";

    const USER_EXIST_CODE = "USER_EXIST";
    const USER_EXIST_MESSAGE = "User with this email already exist";

    const ERROR_REGISTRO_CODE = "ERROR_REGISTER_CODE";
    const ERROR_REGISTRO_MESSAGE = "Error register";

    const USUARIO_NO_TE_PERTENECE_CODE = "USUARIO_NO_TE_PERTENECE";
    const USUARIO_NO_TE_PERTENECE_MESSAGE = "User dont belong to you";

    

    // Empresa service

    const EMPRESA_CREATE_CODE = "EMPRESA_CREATE";
    const EMPRESA_CREATE_MESSAGE = "Wrong data to create Empresa";

    const SUCURSAL_CREATE_CODE = "SUCURSAL_CREATE";
    const SUCURSAL_CREATE_MESSAGE = "Wrong data to create Sucursal";

    const SUCURSAL_UPDATE_CODE = "SUCURSAL_UPDATE";
    const SUCURSAL_UPDATE_MESSAGE = "Wrong data to update Sucursal";

    const SUCURSAL_DELETE_CODE = "SUCURSAL_DELETE";
    const SUCURSAL_DELETE_MESSAGE = "Wrong data to delete Sucursal";

    const EMPRESA_USER_NOT_EXISTS_CODE = "EMPRESA_USER_NOT_EXISTS";
    const EMPRESA_USER_NOT_EXISTS_MESSAGE = "User does not belong to company";

    // recorridos

    const RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE = "RECORRIDO_USUARIO_NO_TE_PERTENECE";
    const RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE = "User does not belong to company";

    const RECORRIDO_NO_EXISTE_CODE = "RECORRIDO_NO_EXISTE";
    const RECORRIDO_NO_EXISTE_MESSAGE = "Recorrido no existe";
    

    // parada

    CONST PARADA_NO_PERTENCE_RECORRIDO_USUARIO_CODE = "PARADA NO PERTENCE RECORRIDO USUARIO";
    CONST PARADA_NO_PERTENCE_RECORRIDO_USUARIO_MESSAGE = "Parada no pertenece a recorrido de usuario";

    CONST PARADA_CREAR_ERROR_CODE = "PARADA CREAR ERROR";
    CONST PARADA_CREAR_ERROR_MESSAGE = "No se creo la parada correctamente";

    CONST PARADA_ACTUALIZAR_ERROR_CODE = "PARADA ACTUALIZAR ERROR";
    CONST PARADA_ACTUALIZAR_ERROR_MESSAGE = "No se creo la parada correctamente";

    const PARADA_NO_PERTECE_USUARIO_CODE = "PARADA_NO_PERTECE_USUARIO";
    const PARADA_NO_PERTECE_USUARIO_MESSAGE = "La parada no pertenece al usuario";

    // items

    CONST ITEM_CREAR_ERROR_CODE = "ITEM CREAR ERROR";
    CONST ITEM_CREAR_ERROR_MESSAGE = "No se creo el ITEM correctamente";

    CONST ITEM_CREAR_DUPLICADO_ERROR_CODE = "ITEM CREAR DUPLICADO ERROR";
    CONST ITEM_CREAR_DUPLICADO_ERROR_MESSAGE = "El item esta duplicado";

    // clientes

    CONST CLIENTE_CREAR_ERROR_CODE = "CLIENTE CREAR ERROR";
    CONST CLIENTE_CREAR_ERROR_MESSAGE = "No se creo CLIENTE correctamente";

    CONST CLIENTE_ACTUALIZAR_ERROR_CODE = "CLIENTE ACTUALIZAR ERROR";
    CONST CLIENTE_ACTUALIZAR_ERROR_MESSAGE = "No se actualizo CLIENTE correctamente";

    const CLIENTE_EXISTENTE_CODE = "CLIENTE EXISTENTE";
    const CLIENTE_EXISTENTE_MESSAGE = "Un cliente ya existe con estos datos";

    // Logout
    const LOUGOUT_ERROR_CODE = "LOUGOUT_ERROR";
    const LOUGOUT_ERROR_MESSAGE = "Something went wrong during logout";
}
