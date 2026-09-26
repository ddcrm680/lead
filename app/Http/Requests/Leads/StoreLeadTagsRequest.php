<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }
}
