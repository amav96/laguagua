<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    use HasFactory;

    
     /**
     * Table name.
     * @var string.
     */
    protected $table = 'clientes';

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

    public function clientesNumeros()
    {
        return $this->hasMany(ClienteNumero::class);
    }

    public function tipoDocumento() : HasOne {
        return $this->hasOne(TipoDocumento::class, 'id', 'tipo_documento_id');
    }
}
