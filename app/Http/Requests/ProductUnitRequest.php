<?php

namespace App\Http\Requests;

use App\Traits\handleResponse;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductUnitRequest extends FormRequest
{
    use handleResponse;
    public function authorize(): bool
    {
        return true; // Handle authorization in the controller
    }

    public function rules(): array
    {
        $productUnitId = $this->route('productUnit')?->id ?? null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_units', 'title')->ignore($productUnitId, 'id'),
            ],
            'unit_type' => [
                'required',
                Rule::in(['barcode', 'not_barcode']),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'can_have_float_value' => [
                'required',
                'boolean',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'unit_title_required',
            'title.string' => 'repeated_unit_must_be_string',
            'title.max' => 'repeated_unit_max_char',
            'title.unique' => 'repeated_unit_title',
            'unit_type.required' => 'repeated_type_required',
            'unit_type.in' => 'repeated_type_not_in_range',
            'description.string' => 'description_must_be_string',
            'can_have_float_value.required' => 'can_have_float_value_required',
            'can_have_float_value.boolean' => 'can_have_float_value_boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }

        $response = $this->generateResponse(
            [
                'message' => 'user_field_error' ,
                'errors' => $errors ,
                'statusCode' => 422
            ]
        );
        throw new ValidationException($validator, $response);
    }
}
