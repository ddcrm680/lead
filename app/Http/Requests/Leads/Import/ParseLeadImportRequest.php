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
        $extension = strtolower(
            (string) $this->file('file')
                ?->getClientOriginalExtension()
        );

        $typeRule = $extension === 'csv'
            ? 'mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel'
            : File::types([
                'xlsx',
            ]);

        return [
            'file' => [
                'required',
                'file',
                'extensions:xlsx,csv',
                $typeRule,
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
