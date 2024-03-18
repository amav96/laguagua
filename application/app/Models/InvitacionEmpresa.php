<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvitacionEmpresa extends Model
{
    use HasFactory, SoftDeletes;

    CONST INVITADO = 1;
    CONST RECHAZADO = 2;
    CONST ACEPTADO = 3;

     /**
     * Table name.
     * @var string.
     */
    protected $table = 'invitaciones_empresas';

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

    public function empresa() : HasOne {
        return $this->hasOne(Empresa::class, 'id', 'empresa_id');
    }

    public function rol() : HasOne {
        return $this->hasOne(Rol::class, 'id', 'rol_id');
    }

    public function invitador() : HasOne {
        return $this->hasOne(User::class, 'id', 'invitador_id');
    }

    public function estado() : HasOne {
        return $this->hasOne(InvitacionEstado::class, 'id', 'invitacion_estado_id');
    }

    public function invitado() : HasOne {
        return $this->hasOne(User::class, 'email', 'email_invitado');
    }
}
