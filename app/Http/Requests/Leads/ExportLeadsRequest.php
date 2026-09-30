<?php

namespace App\Http\Requests\Leads;

class ExportLeadsRequest extends LeadDataRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = parent::rules();

        unset(
            $rules['page'],
            $rules['per_page'],
        );

        $rules['format'] = [
            'nullable',
            'string',
            'in:csv,xlsx,ods,pdf',
        ];

        return $rules;
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            ...parent::messages(),

            'format.string' =>
                'The export format must be valid.',

            'format.in' =>
                'The selected export format is not supported.',
        ];
    }
}
