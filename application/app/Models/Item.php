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

    public function itemTipo() : HasOne {
        return $this->hasOne(ItemTipo::class, 'id', 'item_tipo_id');
    }

    public function itemProveedor() : HasOne {
        return $this->hasOne(ItemProveedor::class, 'id', 'item_proveedor_id');
    }

    public function itemEstado() : HasOne {
        return $this->hasOne(ItemEstado::class, 'id', 'item_estado_id');
    }

    public function empresa() : HasOne {
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }


}
