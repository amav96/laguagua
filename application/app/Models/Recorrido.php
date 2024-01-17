<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Recorrido extends Model
{
    use HasFactory;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'recorridos';

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

    public function estadoRecorrido() : HasOne {
        return $this->hasOne(EstadoRecorrido::class, 'id', 'estado_recorrido_id');
    }

    public function paradas() : HasMany {
        return $this->hasMany(Parada::class, 'recorrido_id', 'id');
    }

}
