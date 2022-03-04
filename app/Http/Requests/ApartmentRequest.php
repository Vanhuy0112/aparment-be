<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'apartment_id' => [
                'required', 'string',
                Rule::unique('apartments')->ignore($this->id)
            ],
            'floor' => 'required|integer|min:1',
            'status' => 'required',
            'description' => 'nullable',
            'square_meters' => 'nullable',
            'type_apartment' => 'required|integer|min:0',
            'password' => 'required',
            'building_id' => 'required',
            'user_id' => 'nullable',

        ];
    }
}
