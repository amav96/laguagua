<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParadaEstado extends Model
{
    use HasFactory;

    CONST PREPARADO             = 1;
    CONST EN_CAMINO             = 2;
    CONST VISITADO              = 3;
    CONST CANCELADO             = 4;
    const NO_RESPONDE           = 5;
    const DIRECCION_INCORRECTA  = 6;
    const FALTAN_DATOS          = 7;
    const RECHAZADO             = 8;

    public $timestamps = false;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'paradas_estados';

    /**
     * Table primary key.
     * @var string.
     */
    protected $primaryKey = 'id';

    /**
     * Table primary key autoincrementing?.
     * @var bool.
     */
    public $incrementing = true;

    /**
     * @var array.
     */
    protected $guarded = [];
}
