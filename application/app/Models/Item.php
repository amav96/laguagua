<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'items';

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

    public function cliente() : HasOne {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function tipoItem() : HasOne {
        return $this->hasOne(TipoItem::class, 'id', 'tipo_item_id');
    }

    public function proveedorItem() : HasOne {
        return $this->hasOne(ProveedorItem::class, 'id', 'proveedor_item_id');
    }

    public function estadoItem() : HasOne {
        return $this->hasOne(EstadoItem::class, 'id', 'estado_item_id');
    }
}
