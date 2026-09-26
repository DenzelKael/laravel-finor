<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_method' => [
                'required',
                'in:CASH,CARD,TRANSFER,QR',
            ],

            'payment_date' => [
                'required',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Debe seleccionar una suscripción.',
            'subscription_id.integer' => 'La suscripción seleccionada no es válida.',
            'subscription_id.exists' => 'La suscripción seleccionada no existe.',

            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser un valor numérico.',
            'amount.min' => 'El monto debe ser mayor a cero.',

            'payment_method.required' => 'Debe seleccionar un método de pago.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',

            'payment_date.required' => 'La fecha de pago es obligatoria.',
            'payment_date.date' => 'La fecha de pago no es válida.',
        ];
    }
}
