<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Subscription::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Debe seleccionar un cliente.',
            'client_id.integer' => 'El cliente seleccionado no es válido.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'plan_id.required' => 'Debe seleccionar un plan.',
            'plan_id.integer' => 'El plan seleccionado no es válido.',
            'plan_id.exists' => 'El plan seleccionado no existe.',
        ];
    }
}
