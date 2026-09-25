<div class="offcanvas offcanvas-end lead-modal-bs" tabindex="-1" id="addLead" aria-labelledby="addLeadLabel">
    <div class="offcanvas-header modal-head">
        <div>
            <span class="eyebrow" id="leadDrawerEyebrow">NEW LEAD</span>
            <h2 id="addLeadLabel">Add a new lead</h2>
            <p id="leadDrawerSubtitle">Create and assign a lead to the right sales owner.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body modal-body">
        <form id="addLeadForm" action="{{ route('storeLead') }}" method="POST" novalidate>
            {{-- 1. Contact Information --}}
            <div class="form-section">
                <h3><span>1</span> Contact information</h3>

                <div class="mb-3">
                    <label class="form-label mb-1" for="leadDisplayName">
                        Full name *
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="leadDisplayName"
                        name="display_name"
                        required
                        autocomplete="name"
                        maxlength="255"
                        placeholder="e.g. Rahul Mehra"
                    >
                    <span class="invalid-feedback" data-error-for="display_name"></span>
                </div>

                <div id="leadContacts" class="d-flex flex-column gap-2">
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
                                    id="primaryContact0"
                                    data-contact-primary
                                    checked
                                >
                                <label class="form-check-label small" for="primaryContact0">Primary</label>
                            </div>
                        </div>

                        <div class="col-1 pb-1 text-center">
                            {{-- Spacer matching the delete button on repeated rows --}}
                            <div class="p-2"></div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-light mt-2" id="addLeadContactBtn">
                    <i class="bi bi-plus"></i> Add another contact
                </button>
                <span class="invalid-feedback" data-error-for="contacts"></span>
            </div>

            {{-- 2. Lead Information --}}
            <div class="form-section">
                <h3><span>2</span> Lead information</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1" for="leadSource">Lead source</label>
                        <select class="form-select" id="leadSource" name="source_id">
                            <option value="">Select source</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="source_id"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1" for="leadStatus">Status *</label>
                        <select class="form-select" id="leadStatus" name="status_id" required>
                            <option value="">Select status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="status_id"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1" for="leadPipelineStage">Stage</label>
                        <select class="form-select" id="leadPipelineStage" name="pipeline_stage_id">
                            <option value="">Select stage</option>
                            @foreach($pipelines as $pipeline)
                                @if($pipeline->stages->isNotEmpty())
                                    <optgroup label="{{ $pipeline->name }}">
                                        @foreach($pipeline->stages as $stage)
                                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="pipeline_stage_id"></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label mb-1" for="leadPriority">Priority</label>
                        <select class="form-select" id="leadPriority" name="priority">
                            <option value="">Default</option>
                            <option value="10">Low</option>
                            <option value="20" selected>Normal</option>
                            <option value="30">High</option>
                        </select>
                        <span class="invalid-feedback" data-error-for="priority"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="leadCity">City</label>
                        <input
                            type="text"
                            class="form-control"
                            id="leadCity"
                            name="city"
                            maxlength="150"
                            autocomplete="address-level2"
                            placeholder="e.g. New Delhi"
                        >
                        <span class="invalid-feedback" data-error-for="city"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="leadState">State</label>
                        <input
                            type="text"
                            class="form-control"
                            id="leadState"
                            name="state"
                            maxlength="100"
                            autocomplete="address-level1"
                            placeholder="e.g. Delhi"
                        >
                        <span class="invalid-feedback" data-error-for="state"></span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label mb-1" for="leadCountry">Country</label>
                        <input
                            type="text"
                            class="form-control"
                            id="leadCountry"
                            name="country"
                            maxlength="100"
                            autocomplete="country-name"
                            placeholder="e.g. India"
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
                            <p id="assignmentText" class="text-muted small mb-0">This lead will be routed to the best available agent based on availability and workload.</p>
                        </div>
                    </div>
                    <div style="min-width: 170px;">
                        <select class="form-select bg-white" id="leadAssignedUser" name="assigned_user_id">
                            <option value="">Auto assign</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="assigned_user_id"></span>
                    </div>
                </div>
            </div>

            {{-- 4. Dynamic Lead Fields / Attributes --}}
            @if(!empty($fieldDefinitions) && count($fieldDefinitions) > 0)
                <div class="form-section" id="leadDynamicFieldsSection">
                    <h3><span>4</span> Additional information</h3>
                    <div class="row g-3">
                        @foreach($fieldDefinitions as $field)
                            @php
                                $fieldKey = $field->key ?? $field->name;
                                $fieldName = "attributes[{$fieldKey}]";
                                $fieldLabel = $field->name ?? $field->label;
                                $isRequired = !empty($field->is_required);
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
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field->type === 'textarea')
                                    <textarea class="form-control" name="{{ $fieldName }}" rows="2" placeholder="Enter {{ $fieldLabel }}" {{ $isRequired ? 'required' : '' }}></textarea>
                                @else
                                    <input type="{{ in_array($field->type, ['number', 'date', 'email']) ? $field->type : 'text' }}"
                                           class="form-control"
                                           name="{{ $fieldName }}"
                                           placeholder="Enter {{ $fieldLabel }}"
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
                <div>
                    <label class="form-label mb-1" for="leadTags">Lead tags</label>
                    <select id="leadTags" name="tags[]" multiple placeholder="Select or type tags..." autocomplete="off">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <span class="invalid-feedback" data-error-for="tags"></span>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-foot">
        <button class="btn btn-light" type="button" data-bs-dismiss="offcanvas">Cancel</button>
        <button class="btn btn-danger" id="leadSubmitBtn" type="submit" form="addLeadForm">
            <span id="leadSubmitText">Create lead</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>