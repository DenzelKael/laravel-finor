<?php

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class VoidPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $payment = $this->route('payment');

        return $payment instanceof Payment
            && ($this->user()?->can('cancel', $payment) ?? false);
    }

    public function rules(): array
    {
        return [
            'void_reason' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'void_reason.required' =>
                'Debe indicar el motivo de la anulación.',

            'void_reason.string' =>
                'El motivo de la anulación debe ser un texto válido.',

            'void_reason.max' =>
                'El motivo de la anulación no puede superar los 255 caracteres.',
        ];
    }

    public function voidReason(): string
    {
        return trim(
            (string) $this->validated('void_reason')
        );
    }
}