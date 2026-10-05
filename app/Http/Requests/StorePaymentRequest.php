<?php

namespace App\Http\Requests;

use App\DTOs\PaymentData;
use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Rules\ActiveSubscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Payment::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
                new ActiveSubscription(),
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:99999999.99',
            ],

            'payment_method' => [
                'required',
                Rule::enum(PaymentMethod::class),
            ],

            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' =>
                'Debe seleccionar una suscripción.',

            'subscription_id.integer' =>
                'La suscripción seleccionada no es válida.',

            'subscription_id.exists' =>
                'La suscripción seleccionada no existe en el sistema.',

            'amount.required' =>
                'El monto es obligatorio.',

            'amount.numeric' =>
                'El monto debe ser un valor numérico.',

            'amount.min' =>
                'El monto debe ser mayor a cero.',

            'amount.max' =>
                'El monto excede el límite máximo permitido.',

            'payment_method.required' =>
                'Debe seleccionar un método de pago.',

            'payment_method.Illuminate\Validation\Rules\Enum' =>
                'El método de pago seleccionado no es válido.',

            'payment_date.required' =>
                'La fecha de pago es obligatoria.',

            'payment_date.date' =>
                'La fecha de pago no es una fecha válida.',

            'payment_date.before_or_equal' =>
                'La fecha de pago no puede ser una fecha futura.',
        ];
    }

    /**
     * Return validated data as a PaymentData DTO.
     */
    public function toDto(): PaymentData
    {
        return PaymentData::fromArray(
            $this->validated()
        );
    }
}