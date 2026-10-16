<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pronostico extends Model
{
    protected $table = 'pronosticos';

    protected $fillable = [
        'serie',
        'anio',
        'mes',
        'valor',
        'limite_inferior',
        'limite_superior',
    ];

    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'valor' => 'integer',
        'limite_inferior' => 'integer',
        'limite_superior' => 'integer',
    ];
}
