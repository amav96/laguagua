<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    CONST ADMINISTRADOR_SISTEMA = 1;
    CONST ADMINISRTADOR_AGENCIA = 2;
    CONST RIDER = 3;
    CONST VENDEDOR = 4;

    /**
    * Table name.
    * @var string.
    */
   protected $table = 'roles';

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
