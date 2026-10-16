<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modalidad extends Model
{
    protected $table = 'modalidades';

    protected $fillable = ['nombre'];

    public function denuncias(): HasMany
    {
        return $this->hasMany(Denuncia::class);
    }
}
