<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoItem extends Model
{
    use HasFactory;

    const EN_ESPERA  = 1;
    const PREPARADO  = 2;
    const ENTREGADO  = 3;
    const RETIRADO   = 4;
    const CANCELADO  = 5;

    public $timestamps = false;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'estados_items';

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
