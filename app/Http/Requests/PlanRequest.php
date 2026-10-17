<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('plans', 'nombre')->ignore($planId),
            ],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'precio' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'duracion_dias' => ['required', 'integer', 'min:1', 'max:3650'],
            'activo' => ['required', 'boolean'],
        ];
    }
}