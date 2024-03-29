<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Table name.
     * @var string.
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'pais_id',
        'rol_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function empresas() : BelongsToMany {
        return $this->belongsToMany(Empresa::class, 'usuarios_empresas', 'usuario_id', 'empresa_id');
    }

    public function pais() : BelongsTo {
        return $this->belongsTo(Pais::class, 'pais_id', 'id');
    }

    public function usuarioConsumo() :BelongsTo {
        return $this->belongsTo(UsuarioConsumo::class, 'id', 'usuario_id');
    }

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'rider_id', 'id');
    }

    public function rol() : BelongsTo {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
}


