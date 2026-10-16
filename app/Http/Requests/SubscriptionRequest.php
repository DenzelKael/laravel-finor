<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('activo', true)],
            'start_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Configurar validaciones adicionales posteriores (no duplicar suscripción activa).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $clientId = $this->input('client_id');
            if ($clientId) {
                $hasActive = \App\Models\Subscription::where('client_id', $clientId)
                    ->active()
                    ->exists();

                if ($hasActive) {
                    $validator->errors()->add('client_id', 'El cliente ya cuenta con una suscripción activa.');
                }
            }
        });
    }
}
