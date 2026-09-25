<div
    class="offcanvas offcanvas-end lead-modal-bs"
    tabindex="-1"
    id="addLeadFollowUp"
    aria-labelledby="addLeadFollowUpTitle"
>
    <div class="offcanvas-header modal-head">
        <div>
            <span class="eyebrow">FOLLOW-UP</span>

            <h2 id="addLeadFollowUpTitle">
                Schedule follow-up
            </h2>

            <p>
                Schedule the next action and reminder for {{ $lead->display_name }}.
            </p>
        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
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

        <form
            id="addLeadFollowUpForm"
            action="{{ route('storeLeadFollowUp', ['lead' => $lead->public_id]) }}"
            method="POST"
            data-lead-id="{{ $lead->public_id }}"
            novalidate
        >
            {{-- 1. Follow-up Schedule --}}
            <div class="form-section">
                <h3><span>1</span> Follow-up schedule</h3>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="followUpType">
                            Follow-up type *
                        </label>

                        <select
                            class="form-select"
                            id="followUpType"
                            name="type_id"
                            required
                        >
                            <option value="">Select type</option>
                            @foreach ($followUpTypes as $type)
                                <option value="{{ $type->id }}" @selected($loop->first)>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>

                        <span
                            class="invalid-feedback"
                            data-error-for="type_id"
                        ></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="followUpStatus">
                            Status *
                        </label>

                        <select
                            class="form-select"
                            id="followUpStatus"
                            name="status_id"
                            required
                        >
                            <option value="">Select status</option>
                            @foreach ($followUpStatuses as $status)
                                <option
                                    value="{{ $status->id }}"
                                    @selected($status->is_open && ($loop->first || $status->name === 'Pending'))
                                >
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>

                        <span
                            class="invalid-feedback"
                            data-error-for="status_id"
                        ></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="followUpDueAt">
                            Due date & time *
                        </label>

                        <input
                            type="datetime-local"
                            class="form-control"
                            id="followUpDueAt"
                            name="due_at"
                            value="{{ now()->addHour()->setMinute(0)->format('Y-m-d\TH:i') }}"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            required
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="due_at"
                        ></span>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="followUpAssignedUser">
                            Assigned to
                        </label>

                        <select
                            class="form-select"
                            id="followUpAssignedUser"
                            name="assigned_user_id"
                        >
                            <option value="">Unassigned</option>
                            @foreach ($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    @selected($lead->assigned_user_id === $user->id)
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        <span
                            class="invalid-feedback"
                            data-error-for="assigned_user_id"
                        ></span>
                    </div>
                </div>
            </div>

            {{-- 2. Objective & Notes --}}
            <div class="form-section">
                <h3><span>2</span> Objective & notes</h3>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="followUpTitle">
                            Subject / Title *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="followUpTitle"
                            name="title"
                            maxlength="255"
                            placeholder="e.g. Call back regarding pricing proposal & agreement"
                            required
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="title"
                        ></span>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="followUpNotes">
                            Notes / Agenda
                        </label>

                        <textarea
                            class="form-control"
                            id="followUpNotes"
                            name="notes"
                            rows="4"
                            maxlength="2000"
                            placeholder="Add meeting agenda, discussion points, or special instructions..."
                        ></textarea>

                        <span
                            class="invalid-feedback"
                            data-error-for="notes"
                        ></span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-foot">
        <button
            type="button"
            class="btn btn-light"
            data-bs-dismiss="offcanvas"
        >
            Cancel
        </button>

        <button
            type="submit"
            class="btn btn-danger"
            id="leadFollowUpSubmitBtn"
            form="addLeadFollowUpForm"
        >
            <span id="leadFollowUpSubmitText">Schedule follow-up</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>
