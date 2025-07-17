<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nombre_apellido',
        'identificacion',
        'hta',
        'dm',
        'fecha',
        'telefono',
        'zona',
        'peso',
        'talla',
        'imc',
        'perimetro_abdominal',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'tension_arterial',
        'glucometria',
        'temperatura',
        'familiar',
        'riesgo_fotografico',
        'abandono_social',
        'motivo',
        'medicamentos',
        'factores',
        'conductas',
        'novedades',
        'proximo_control',
        'firma',
        'idusuario',
        'idpaciente'
    ];

    protected $casts = [
        'fecha' => 'date',
        'proximo_control' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idusuario');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'idpaciente');
    }

    public function medicamentos()
    {
        return $this->belongsToMany(Medicamento::class, 'medicamento_visita')
                    ->withPivot('indicaciones')
                    ->withTimestamps();
    }
}