<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'usuario',
        'correo',
        'nombre',
        'contrasena',
        'rol',
        'estado',
        'idsede'
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'idsede');
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class, 'idusuario');
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
}