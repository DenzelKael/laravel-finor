<?php

namespace App\Http\Requests;

use App\Enums\ClientStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('clients.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(ClientStatus::class)],
        ];
    }

    public function status(): ?ClientStatus
    {
        return ClientStatus::tryFrom($this->validated('status') ?? '');
    }
}
