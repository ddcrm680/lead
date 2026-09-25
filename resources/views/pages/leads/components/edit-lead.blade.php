<div class="offcanvas offcanvas-end lead-modal-bs" tabindex="-1" id="editLead" aria-labelledby="editLeadLabel">
    <div class="offcanvas-header modal-head">
        <div>
            <span class="eyebrow" id="leadDrawerEyebrow">EDIT LEAD</span>
            <h2 id="editLeadLabel">Edit lead</h2>
            <p id="leadDrawerSubtitle">Update lead details and assignments.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body modal-body">
        {{-- Lead Context Summary Card --}}
        @php
            $initials = collect(explode(' ', trim($lead->display_name)))
                ->filter()
                ->map(fn($w) => mb_substr($w, 0, 1))
                ->take(2)
                ->implode('');
            $initials = $initials ?: 'L';

            $primaryPhone = $lead->contacts->where('type', 'phone')->where('is_primary', true)->first()
                ?? $lead->contacts->where('type', 'phone')->first();
            $primaryEmail = $lead->contacts->where('type', 'email')->where('is_primary', true)->first()
                ?? $lead->contacts->where('type', 'email')->first();
            $primaryWhatsapp = $lead->contacts->where('type', 'whatsapp')->where('is_primary', true)->first()
                ?? $lead->contacts->where('type', 'whatsapp')->first();
        @endphp

        <div class="lead-context-card p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between" style="background: #f8f9fa; border: 1px solid #e9ecef;">
            <div class="d-flex align-items-center gap-3">
                <span class="lead-profile-avatar" style="width: 44px; height: 44px; border-radius: 12px; font-size: 14px;">
                    {{ $initials }}
                </span>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <strong class="text-dark fs-6">{{ $lead->display_name }}</strong>

                        @if ($lead->status)
                            <span
                                class="status-badge"
                                @if ($lead->status->color_code)
                                    style="background-color: {{ $lead->status->color_code }};"
                                @endif
                            >
                                {{ $lead->status->name }}
                            </span>
                        @endif

                        @if ($lead->pipelineStage)
                            <span class="badge bg-light text-secondary border">
                                {{ $lead->pipelineStage->name }}
                            </span>
                        @endif
                    </div>

                    <div class="text-muted small d-flex align-items-center gap-3 mt-1 flex-wrap">
                        <span><i class="bi bi-hash"></i> {{ $lead->public_id }}</span>

                        <span>
                            <i class="bi bi-person"></i>
                            {{ $lead->assignedUser?->name ?? 'Unassigned' }}
                        </span>

                        @if ($primaryPhone?->value)
                            <span>
                                <i class="bi bi-telephone"></i>
                                {{ $primaryPhone->value }}
                            </span>
                        @elseif ($primaryWhatsapp?->value)
                            <span>
                                <i class="bi bi-whatsapp"></i>
                                {{ $primaryWhatsapp->value }}
                            </span>
                        @elseif ($primaryEmail?->value)
                            <span>
                                <i class="bi bi-envelope"></i>
                                {{ $primaryEmail->value }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form id="editLeadForm" action="{{ route('updateLead', ['lead' => $lead->public_id]) }}" method="POST" data-lead-id="{{ $lead->public_id }}" novalidate>
            <input type="hidden" name="_method" value="PUT">

            {{-- 1. Contact Information --}}
            <div class="form-section">
                <h3><span>1</span> Contact information</h3>

                <div class="mb-3">
                    <label class="form-label mb-1" for="editLeadDisplayName">
                        Full name *
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="editLeadDisplayName"
                        name="display_name"
                        required
                        autocomplete="name"
                        maxlength="255"
                        placeholder="e.g. Rahul Mehra"
                        value="{{ $lead->display_name }}"
                    >
                    <span class="invalid-feedback" data-error-for="display_name"></span>
                </div>

                <div id="editLeadContacts" class="d-flex flex-column gap-2">
                    @php
                        $leadContacts = $lead->contacts->sortByDesc('is_primary')->values();
                    @endphp

                    @forelse($leadContacts as $index => $contact)
                        <div class="row g-2 align-items-end lead-contact-row" data-contact-row>
                            <input type="hidden" name="contacts[{{ $index }}][id]" value="{{ $contact->id }}">

                            <div class="col-4">
                                <label class="form-label small mb-1">Contact type *</label>
                                <select class="form-select" name="contacts[{{ $index }}][type]" data-contact-type required>
                                    <option value="">Select type</option>
                                    <option value="phone" {{ $contact->type === 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="email" {{ $contact->type === 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ $contact->type === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                </select>
                                <span class="invalid-feedback" data-error-for="contacts.{{ $index }}.type"></span>
                            </div>

                            <div class="col-5">
                                <label class="form-label small mb-1">Contact value *</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="contacts[{{ $index }}][value]"
                                    data-contact-value
                                    required
                                    maxlength="255"
                                    placeholder="e.g. Enter Value"
                                    value="{{ $contact->value }}"
                                >
                                <span class="invalid-feedback" data-error-for="contacts.{{ $index }}.value"></span>
                            </div>

                            <div class="col-2 pb-2">
                                <div class="form-check m-0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="contacts[{{ $index }}][is_primary]"
                                        value="1"
                                        id="editPrimaryContact{{ $index }}"
                                        data-contact-primary
                                        {{ $contact->is_primary ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label small" for="editPrimaryContact{{ $index }}">Primary</label>
                                </div>
                            </div>

                            <div class="col-1 pb-1 text-center">
                                @if($loop->first && $loop->count === 1)
                                    <div class="p-2"></div>
                                @else
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-contact-btn d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Remove contact">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="row g-2 align-items-end lead-contact-row" data-contact-row>
                            <div class="col-4">
                                <label class="form-label small mb-1">Contact type *</label>
                                <select class="form-select" name="contacts[0][type]" data-contact-type required>
                                    <option value="">Select type</option>
                                    <option value="phone">Phone</option>
                                    <option value="email">Email</option>
                                    <option value="whatsapp">WhatsApp</option>
                                </select>
                                <span class="invalid-feedback" data-error-for="contacts.0.type"></span>
                            </div>

                            <div class="col-5">
                                <label class="form-label small mb-1">Contact value *</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="contacts[0][value]"
                                    data-contact-value
                                    required
                                    maxlength="255"
                                    placeholder="e.g. Enter Value"
                                >
                                <span class="invalid-feedback" data-error-for="contacts.0.value"></span>
                            </div>

                            <div class="col-2 pb-2">
                                <div class="form-check m-0">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="contacts[0][is_primary]"
                                        value="1"
                                        id="editPrimaryContact0"
                                        data-contact-primary
                                        checked
                                    >
                                    <label class="form-check-label small" for="editPrimaryContact0">Primary</label>
                                </div>
                            </div>

                            <div class="col-1 pb-1 text-center">
                                <div class="p-2"></div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <button type="button" class="btn btn-light mt-2" id="addEditLeadContactBtn">
                    <i class="bi bi-plus"></i> Add another contact
                </button>
                <span class="invalid-feedback" data-error-for="contacts"></span>
            </div>

            {{-- 2. Lead Information --}}
            <div class="form-section">
                <h3><span>2</span> Lead information</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" for="editLeadSource">Lead source</label>
                        <select class="form-select" id="editLeadSource" name="source_id">
                            <option value="">Select source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}" {{ $lead->source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="source_id"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1" for="editLeadStatus">Status *</label>
                        <select class="form-select" id="editLeadStatus" name="status_id" required>
                            <option value="">Select status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $lead->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="status_id"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1" for="editLeadPipelineStage">Stage</label>
                        <select class="form-select" id="editLeadPipelineStage" name="pipeline_stage_id">
                            <option value="">Select stage</option>
                            @foreach($pipelines as $pipeline)
                                @if($pipeline->stages->isNotEmpty())
                                    <optgroup label="{{ $pipeline->name }}">
                                        @foreach($pipeline->stages as $stage)
                                            <option value="{{ $stage->id }}" {{ $lead->pipeline_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="pipeline_stage_id"></span>
                    </div>

                    @php
                        $leadPriorityVal = is_object($lead->priority) ? $lead->priority->value : (int) $lead->priority;
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label mb-1" for="editLeadPriority">Priority</label>
                        <select class="form-select" id="editLeadPriority" name="priority">
                            <option value="">Default</option>
                            <option value="10" {{ $leadPriorityVal === 10 ? 'selected' : '' }}>Low</option>
                            <option value="20" {{ $leadPriorityVal === 20 ? 'selected' : '' }}>Normal</option>
                            <option value="30" {{ $leadPriorityVal === 30 ? 'selected' : '' }}>High</option>
                        </select>
                        <span class="invalid-feedback" data-error-for="priority"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="editLeadCity">City</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editLeadCity"
                            name="city"
                            maxlength="150"
                            autocomplete="address-level2"
                            placeholder="e.g. New Delhi"
                            value="{{ $lead->city }}"
                        >
                        <span class="invalid-feedback" data-error-for="city"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="editLeadState">State</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editLeadState"
                            name="state"
                            maxlength="100"
                            autocomplete="address-level1"
                            placeholder="e.g. Delhi"
                            value="{{ $lead->state }}"
                        >
                        <span class="invalid-feedback" data-error-for="state"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="editLeadCountry">Country</label>
                        <input
                            type="text"
                            class="form-control"
                            id="editLeadCountry"
                            name="country"
                            maxlength="100"
                            autocomplete="country-name"
                            placeholder="e.g. India"
                            value="{{ $lead->country }}"
                        >
                        <span class="invalid-feedback" data-error-for="country"></span>
                    </div>
                </div>
            </div>

            {{-- 3. Smart Assignment --}}
            <div class="form-section">
                <h3><span>3</span> Smart assignment</h3>
                <div class="assignment d-flex align-items-center justify-content-between p-3 rounded-3 flex-wrap gap-3" style="background-color: #fff5f5; border: 1px solid #ffe3e3;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-magic fs-4 text-danger"></i>
                        <div>
                            <strong class="d-block text-dark">Smart assignment</strong>
                            <p id="editAssignmentText" class="text-muted small mb-0">Reassign or update the assigned sales owner for this lead.</p>
                        </div>
                    </div>
                    <div style="min-width: 170px;">
                        <select class="form-select bg-white" id="editLeadAssignedUser" name="assigned_user_id">
                            <option value="">Auto assign</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $lead->assigned_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="assigned_user_id"></span>
                    </div>
                </div>
            </div>

            {{-- 4. Dynamic Lead Fields / Attributes --}}
            @if(!empty($fieldDefinitions) && count($fieldDefinitions) > 0)
                @php
                    $leadAttributes = is_array($lead->attributes) ? $lead->attributes : json_decode($lead->attributes ?? '[]', true);
                @endphp
                <div class="form-section" id="editLeadDynamicFieldsSection">
                    <h3><span>4</span> Additional information</h3>
                    <div class="row g-3">
                        @foreach($fieldDefinitions as $field)
                            @php
                                $fieldKey = $field->key ?? $field->name;
                                $fieldName = "attributes[{$fieldKey}]";
                                $fieldLabel = $field->name ?? $field->label;
                                $isRequired = !empty($field->is_required);
                                $fieldValue = $leadAttributes[$fieldKey] ?? '';
                                $options = is_array($field->options) ? $field->options : json_decode($field->options ?? '[]', true);
                            @endphp

                            <div class="col-md-6">
                                <label class="form-label mb-1">
                                    {{ $fieldLabel }}{{ $isRequired ? ' *' : '' }}
                                </label>

                                @if($field->type === 'select')
                                    <select class="form-select" name="{{ $fieldName }}" {{ $isRequired ? 'required' : '' }}>
                                        <option value="">Select {{ $fieldLabel }}</option>
                                        @foreach($options as $option)
                                            @php
                                                $val = is_array($option) ? ($option['value'] ?? '') : $option;
                                                $lbl = is_array($option) ? ($option['label'] ?? $val) : $option;
                                            @endphp
                                            <option value="{{ $val }}" {{ (string)$fieldValue === (string)$val ? 'selected' : '' }}>{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field->type === 'textarea')
                                    <textarea class="form-control" name="{{ $fieldName }}" rows="2" placeholder="Enter {{ $fieldLabel }}" {{ $isRequired ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                @else
                                    <input type="{{ in_array($field->type, ['number', 'date', 'email']) ? $field->type : 'text' }}"
                                           class="form-control"
                                           name="{{ $fieldName }}"
                                           placeholder="Enter {{ $fieldLabel }}"
                                           value="{{ $fieldValue }}"
                                           {{ $isRequired ? 'required' : '' }}>
                                @endif
                                <span class="invalid-feedback" data-error-for="attributes.{{ $fieldKey }}"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 5. Tags --}}
            <div class="form-section">
                <h3><span>5</span> Tags</h3>
                @php
                    $assignedTagNames = $lead->tags->pluck('name')->all();
                @endphp
                <div>
                    <label class="form-label mb-1" for="editLeadTags">Lead tags</label>
                    <select id="editLeadTags" name="tags[]" multiple placeholder="Select or type tags..." autocomplete="off">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}" {{ in_array($tag->name, $assignedTagNames, true) ? 'selected' : '' }}>{{ $tag->name }}</option>
                        @endforeach
                        {{-- Include any custom tags attached to this lead that might not be in the pre-queried $tags list --}}
                        @foreach($assignedTagNames as $tagName)
                            @if(!$tags->contains('name', $tagName))
                                <option value="{{ $tagName }}" selected>{{ $tagName }}</option>
                            @endif
                        @endforeach
                    </select>
                    <span class="invalid-feedback" data-error-for="tags"></span>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-foot">
        <button class="btn btn-light" type="button" data-bs-dismiss="offcanvas">Cancel</button>
        <button class="btn btn-danger" id="editLeadSubmitBtn" type="submit" form="editLeadForm">
            <span id="editLeadSubmitText">Save changes</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>
