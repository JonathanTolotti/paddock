<?php

namespace App\Http\Requests\Clients;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $clientId = $this->route('client')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:CPF,CNPJ'],
            'document_number' => ['required', 'string', 'max:20', Rule::unique('clients')->ignore($clientId)],

            'email' => ['nullable', 'email', 'max:255', Rule::unique('clients')->ignore($clientId)],

            'phone_number' => ['nullable', 'string', 'max:20'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_number' => ['nullable', 'string', 'max:20'],
            'address_complement' => ['nullable', 'string', 'max:255'],
            'address_neighborhood' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'size:2'],
            'address_zip_code' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
