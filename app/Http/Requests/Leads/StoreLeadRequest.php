<?php

namespace App\Http\Requests\Leads;

use App\Services\LeadPayloadValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(
        LeadPayloadValidator $leadPayloadValidator,
    ): array {
        return $leadPayloadValidator->rules();
    }

    /**
     * Attach shared Lead-domain validation.
     */
    public function withValidator(
        Validator $validator,
    ): void {
        app(LeadPayloadValidator::class)->after(
            validator: $validator,
            data: $this->all(),
        );
    }
}
