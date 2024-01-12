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

    const SUCURSAL_NOT_BELONG_CODE = "SUCURSAL_NOT_BELONG";
    const SUCURSAL_NOT_BELONG_MESSAGE = "Sucursal does not belong to user";

    // contactos

    const CONTACTO_CREATE_CODE = "CONTACTO_CREATE";
    const CONTACTO_CREATE_MESSAGE = "Wrong data to create CONTACTO";

    const CONTACTO_UPDATE_CODE = "CONTACTO_UPDATE";
    const CONTACTO_UPDATE_MESSAGE = "Wrong data to update CONTACTO";

    const CONTACTO_DELETE_CODE = "CONTACTO_DELETE";
    const CONTACTO_DELETE_MESSAGE = "Wrong data to delete CONTACTO";

    // comprobantes

    const COMPROBANTE_CREATE_CODE = "COMPROBANTE_CREATE";
    const COMPROBANTE_CREATE_MESSAGE = "Can't create document";

    // categorias

    const CATEGORIA_USER_NOT_EXISTS_CODE = "CATEGORIA_USER_NOT_EXISTS";
    const CATEGORIA_USER_NOT_EXISTS_MESSAGE = "User does not belong to CATEGORIA";

    const CATEGORIA_NOT_REMOVABLE_CODE = "CATEGORIA_NOT_REMOVABLE";
    const CATEGORIA_NOT_REMOVABLE_MESSAGE = "CATEGORIA is not removable";

    const CATEGORIA_CREATE_CODE = "CATEGORIA_CREATE";
    const CATEGORIA_CREATE_MESSAGE = "Wrong data to create CATEGORIA";

    const CATEGORIA_UPDATE_CODE = "CATEGORIA_UPDATE";
    const CATEGORIA_UPDATE_MESSAGE = "Wrong data to UPDATE CATEGORIA";

    const CATEGORIA_DELETE_CODE = "CATEGORIA_DELETE";
    const CATEGORIA_DELETE_MESSAGE = "Wrong data to delete CATEGORIA";
    

    // Logout
    const LOUGOUT_ERROR_CODE = "LOUGOUT_ERROR";
    const LOUGOUT_ERROR_MESSAGE = "Something went wrong during logout";
}
