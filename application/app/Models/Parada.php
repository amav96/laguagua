<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parada extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'paradas';

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

    public function estadoParada(): HasOne
    {
        return $this->hasOne(EstadoParada::class, "id", "estado_parada_id");
    }
}
