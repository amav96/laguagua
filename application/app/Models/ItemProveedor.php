<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProveedor extends Model
{
    use HasFactory;

    const MERCADO_LIBRE = 1;
    const INDEPENDIENTE = 2;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'items_proveedores';

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
