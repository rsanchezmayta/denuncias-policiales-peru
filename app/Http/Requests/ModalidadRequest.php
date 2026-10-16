<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModalidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('modalidad')?->id;

        return [
            'nombre' => ['required', 'string', 'max:80', Rule::unique('modalidades', 'nombre')->ignore($id)],
        ];
    }
}
