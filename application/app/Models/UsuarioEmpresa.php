<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioEmpresa extends Model
{

    use HasFactory, SoftDeletes;
    
     /**
     * Table name.
     * @var string.
     */
    protected $table = 'usuarios_empresas';

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

    public function usuario() : HasOne {
        return $this->hasOne(User::class, 'id', 'usuario_id');
    }

    public function empresa() : HasOne {
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    public function rol() : HasOne {
        return $this->hasOne(Rol::class, 'id', 'rol_id');
    }

    public function invitacion() : HasOne {
        return $this->hasOne(InvitacionEmpresa::class, 'id', 'invitacion_id');
    }
    
}
