<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DenunciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'anio' => $this->anio,
            'mes' => $this->mes,
            'departamento' => $this->distrito?->provincia?->departamento?->nombre,
            'provincia' => $this->distrito?->provincia?->nombre,
            'distrito' => $this->distrito?->nombre,
            'modalidad' => $this->modalidad?->nombre,
            'cantidad' => $this->cantidad,
        ];
    }
}
