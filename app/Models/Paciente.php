<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'identificacion',
        'fecnacimiento',
        'nombre',
        'apellido',
        'genero',
        'longitud',
        'latitud',
        'idsede'
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'idsede');
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class, 'idpaciente');
    }
}