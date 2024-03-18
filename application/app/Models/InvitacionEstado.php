<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitacionEstado extends Model
{
    use HasFactory;

    CONST INVITADO = 1;
    CONST ACEPTADO = 2;
    CONST RECHAZADO = 3;
    CONST TERMINADO = 4;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'invitaciones_estados';

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
}
