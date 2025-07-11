<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nombmedicamento'
    ];

    public function visitas()
    {
        return $this->belongsToMany(Visita::class, 'medicamento_visita')
                    ->withPivot('indicaciones')
                    ->withTimestamps();
    }
}