<?php

namespace App\Http\Requests\Vehicles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
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
        $vehicleId = $this->route('vehicle')->id;

        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'plate' => ['required', 'string', 'max:10', Rule::unique('vehicles')->ignore($vehicleId)],
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'digits:4'],
            'color' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'size:17', Rule::unique('vehicles')->ignore($vehicleId)],
        ];
    }
}
