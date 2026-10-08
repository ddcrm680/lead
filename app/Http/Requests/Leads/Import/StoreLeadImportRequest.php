<?php

namespace App\Http\Requests\Leads\Import;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
