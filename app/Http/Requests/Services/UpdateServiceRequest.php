<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $serviceId = $this->route('service')->id;
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($serviceId)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
