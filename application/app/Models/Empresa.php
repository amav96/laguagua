<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    const INDEPENDIENTE = 1;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'empresas';

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

    public function usuariosEmpresas(): HasMany
    {
        return $this->hasMany(UsuarioEmpresa::class, "empresa_id", "id");
    }
}
