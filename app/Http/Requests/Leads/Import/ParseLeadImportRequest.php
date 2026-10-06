<?php

namespace App\Http\Requests\Leads\Import;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ParseLeadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::types([
                    'xlsx',
                    'csv',
                ]),
                'extensions:xlsx,csv',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'lead import file',
        ];
    }
}
