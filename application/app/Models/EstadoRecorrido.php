<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoRecorrido extends Model
{
    use HasFactory;

    const PREPARADO = 1;
    const INICIADO = 2;
    const FINALIZADO = 3;
    const CANCELADO = 4;

    public $timestamps = false;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'estados_recorridos';

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

     // COLUMNS
    // "id" => "bigint",
    // "nombre"=> "varchar",
}
