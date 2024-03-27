<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function paradaEstado(): HasOne
    {
        return $this->hasOne(ParadaEstado::class, "id", "parada_estado_id");
    }

    public function items(): hasMany
    {
        return $this->hasMany(Item::class, "parada_id", "id");
    }

    // public function getHoraLlegadaEstimadaAttribute($value)
    // {
    //     return Carbon::parse($value)->format('Y-m-d H:i:s');
    // }

    public function comprobantes() : HasMany {
        return $this->hasMany(ParadaComprobante::class, 'parada_id', 'id');
    }
    
}
