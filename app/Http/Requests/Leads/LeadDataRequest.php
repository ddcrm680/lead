<?php

namespace App\Http\Requests\Leads;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LeadDataRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'in:25,50,100',
            ],

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status_id' => [
                'nullable',
                'integer',
                'exists:lead_statuses,id',
            ],

            'source_id' => [
                'nullable',
                'integer',
                'exists:lead_sources,id',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pipeline_stage_id' => [
                'nullable',
                'integer',
                'exists:pipeline_stages,id',
            ],

            'assigned_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'priority' => [
                'nullable',
                'integer',
                'in:10,20,30,40',
            ],

            'tag_ids' => [
                'nullable',
                'array',
            ],

            'tag_ids.*' => [
                'integer',
                'exists:tags,id',
            ],

            'follow_up_type_id' => [
                'nullable',
                'integer',
                'exists:lead_follow_up_types,id',
            ],

            'follow_up_status_id' => [
                'nullable',
                'integer',
                'exists:lead_follow_up_statuses,id',
            ],

            'created_after' => [
                'nullable',
                'date',
            ],

            'created_before' => [
                'nullable',
                'date',
                'after_or_equal:created_after',
            ],
        ];
    }

    /**
     * Get custom validation messages for the request.
     */
    public function messages(): array
    {
        return [
            'page.integer' =>
                'The page number must be a valid number.',

            'page.min' =>
                'The page number must be at least 1.',

            'per_page.integer' =>
                'The number of Leads per page must be valid.',

            'per_page.in' =>
                'The number of Leads per page must be 25, 50, or 100.',

            'search.string' =>
                'The search value must be valid text.',

            'search.max' =>
                'The search may not be longer than 100 characters.',

            'status_id.integer' =>
                'The selected status is invalid.',

            'status_id.exists' =>
                'The selected status does not exist.',

            'source_id.integer' =>
                'The selected source is invalid.',

            'source_id.exists' =>
                'The selected source does not exist.',

            'city.string' =>
                'The city must be valid text.',

            'city.max' =>
                'The city may not be longer than 100 characters.',

            'state.string' =>
                'The state must be valid text.',

            'state.max' =>
                'The state may not be longer than 100 characters.',

            'country.string' =>
                'The country must be valid text.',

            'country.max' =>
                'The country may not be longer than 100 characters.',

            'pipeline_stage_id.integer' =>
                'The selected stage is invalid.',

            'pipeline_stage_id.exists' =>
                'The selected stage does not exist.',

            'assigned_user_id.integer' =>
                'The selected agent is invalid.',

            'assigned_user_id.exists' =>
                'The selected agent does not exist.',

            'priority.integer' =>
                'The selected priority is invalid.',

            'priority.in' =>
                'The selected priority is invalid.',

            'tag_ids.array' =>
                'The selected tags must be valid.',

            'tag_ids.*.integer' =>
                'Each selected tag must be valid.',

            'tag_ids.*.exists' =>
                'One or more selected tags do not exist.',

            'follow_up_type_id.integer' =>
                'The selected follow-up type is invalid.',

            'follow_up_type_id.exists' =>
                'The selected follow-up type does not exist.',

            'follow_up_status_id.integer' =>
                'The selected follow-up status is invalid.',

            'follow_up_status_id.exists' =>
                'The selected follow-up status does not exist.',

            'created_after.date' =>
                'The created-after date must be valid.',

            'created_before.date' =>
                'The created-before date must be valid.',

            'created_before.after_or_equal' =>
                'The created-before date must be on or after the created-after date.',
        ];
    }

}
