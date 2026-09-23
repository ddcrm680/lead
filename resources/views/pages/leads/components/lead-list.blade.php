@if ($leads->count())
    {{-- Desktop / Table View --}}
    <div class="table-responsive table-desktop">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Lead</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Source</th>
                    <th>Assigned to</th>
                    <th>Priority</th>
                    <th>Next follow-up</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($leads as $lead)
                    @php
                        $contacts = $lead->contacts
                            ->sortByDesc('is_primary')
                            ->values();

                        $phone = $contacts->firstWhere('type', 'phone');
                        $whatsapp = $contacts->firstWhere('type', 'whatsapp');
                        $email = $contacts->firstWhere('type', 'email');

                        $nextFollowUp = $lead->followUps
                            ->whereNull('completed_at')
                            ->sortBy('due_at')
                            ->first();
                    @endphp

                    <tr>
                        <td>
                            <button
                                class="lead-ident link-reset"
                                type="button"
                                data-view-lead="{{ $lead->public_id }}"
                                aria-label="View {{ $lead->display_name }}"
                            >
                                <span class="mini-avatar">
                                    {{ collect(explode(' ', trim($lead->display_name)))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn ($part) => mb_substr($part, 0, 1))
                                        ->implode('') }}
                                </span>

                                <span>
                                    <strong>{{ $lead->display_name }}</strong>

                                    <small class="lead-contact">
                                        @if ($phone?->value)
                                            <i class="bi bi-telephone"></i>
                                            {{ $phone->value }}

                                            @if ($phone->is_primary)
                                                <i class="bi bi-star-fill" title="Primary"></i>
                                            @endif
                                        @endif

                                        @if ($email?->value)
                                            @if ($phone?->value)
                                                |
                                            @endif

                                            <i class="bi bi-envelope"></i>
                                            {{ $email->value }}

                                            @if ($email->is_primary)
                                                <i class="bi bi-star-fill" title="Primary"></i>
                                            @endif
                                        @endif

                                        @if ($whatsapp?->value)
                                            <br>
                                            <i class="bi bi-whatsapp"></i>
                                            {{ $whatsapp->value }}

                                            @if ($whatsapp->is_primary)
                                                <i class="bi bi-star-fill" title="Primary"></i>
                                            @endif
                                        @endif

                                        @if (!$phone?->value && !$email?->value && !$whatsapp?->value)
                                            —
                                        @endif
                                    </small>
                                </span>
                            </button>
                        </td>

                        <td>
                            {{ $lead->city ?? '—' }}
                        </td>

                        <td>
                            <span
                                class="status-badge"
                                @if ($lead->status?->color_code)
                                    style="background-color: {{ $lead->status->color_code }};"
                                @endif
                            >
                                {{ $lead->status?->name ?? 'Unknown' }}
                            </span>
                        </td>


                        <td>
                            <span
                                class="status-badge"
                                @if ($lead->pipelineStage?->color_code)
                                    style="background-color: {{ $lead->pipelineStage->color_code }};"
                                @endif
                            >
                                {{ $lead->pipelineStage?->name ?? 'Unknown' }}
                            </span>
                        </td>

                        <td>
                            {{ $lead->source?->name ?? '—' }}
                        </td>

                        <td>
                            @if ($lead->assignedUser)
                                <span class="agent-chip">
                                    <span class="mini-avatar">
                                        {{ collect(explode(' ', trim($lead->assignedUser->name)))
                                            ->filter()
                                            ->take(2)
                                            ->map(fn ($part) => mb_substr($part, 0, 1))
                                            ->implode('') }}
                                    </span>

                                    {{ $lead->assignedUser->name }}
                                </span>
                            @else
                                Unassigned
                            @endif
                        </td>

                        <td>
                            {{ $lead->priority?->name ?? '—' }}
                        </td>

                        <td>
                            @if ($nextFollowUp)
                                <strong>
                                    {{ $nextFollowUp->due_at?->format('d M, h:i a') ?? '—' }}
                                </strong>

                                @if ($nextFollowUp->type?->name || $nextFollowUp->status?->name)
                                    <small class="d-block">
                                        {{ $nextFollowUp->type?->name ?? '—' }}

                                        @if ($nextFollowUp->status?->name)
                                            · {{ $nextFollowUp->status->name }}
                                        @endif
                                    </small>
                                @endif
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <button
                                    class="table-action"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    aria-label="Lead actions"
                                >
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button
                                            class="dropdown-item"
                                            type="button"
                                            data-view-lead="{{ $lead->public_id }}"
                                        >
                                            <i class="bi bi-eye me-2"></i>
                                            View details
                                        </button>
                                    </li>

                                    <li>
                                        <button
                                            class="dropdown-item"
                                            type="button"
                                            data-edit-lead="{{ $lead->public_id }}"
                                        >
                                            <i class="bi bi-pencil-square me-2"></i>
                                            Edit lead
                                        </button>
                                    </li>

                                    @if ($phone?->value)
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="tel:{{ $phone->value }}"
                                            >
                                                <i class="bi bi-telephone me-2"></i>
                                                Call
                                            </a>
                                        </li>
                                    @endif

                                    @if ($email?->value)
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="mailto:{{ $email->value }}"
                                            >
                                                <i class="bi bi-envelope me-2"></i>
                                                Email
                                            </a>
                                        </li>
                                    @endif

                                    @if ($whatsapp?->value)
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                target="_blank"
                                                rel="noopener"
                                                href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp->value) }}"
                                            >
                                                <i class="bi bi-whatsapp me-2"></i>
                                                WhatsApp
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <button
                                            class="dropdown-item text-danger"
                                            type="button"
                                            data-delete-lead="{{ $lead->public_id }}"
                                        >
                                            <i class="bi bi-trash me-2"></i>
                                            Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile View --}}
    <div class="mobile-records compact-grid">
        @foreach ($leads as $lead)
            @php
                $contacts = $lead->contacts
                    ->sortByDesc('is_primary')
                    ->values();

                $phone = $contacts->firstWhere('type', 'phone');
                $whatsapp = $contacts->firstWhere('type', 'whatsapp');
                $email = $contacts->firstWhere('type', 'email');

                $nextFollowUp = $lead->followUps
                    ->whereNull('completed_at')
                    ->sortBy('due_at')
                    ->first();
            @endphp

            <article class="mobile-record">
                <div class="mobile-record-head">
                    <button
                        class="lead-ident link-reset"
                        type="button"
                        data-view-lead="{{ $lead->public_id }}"
                        aria-label="View {{ $lead->display_name }}"
                    >
                        <span class="mini-avatar">
                            {{ collect(explode(' ', trim($lead->display_name)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => mb_substr($part, 0, 1))
                                ->implode('') }}
                        </span>

                        <span>
                            <strong>{{ $lead->display_name }}</strong>

                            <small class="lead-contact">
                                @if ($phone?->value)
                                    <i class="bi bi-telephone"></i>
                                    {{ $phone->value }}

                                    @if ($phone->is_primary)
                                        <i class="bi bi-star-fill" title="Primary"></i>
                                    @endif
                                @elseif ($email?->value)
                                    <i class="bi bi-envelope"></i>
                                    {{ $email->value }}

                                    @if ($email->is_primary)
                                        <i class="bi bi-star-fill" title="Primary"></i>
                                    @endif
                                @endif
                            </small>
                        </span>
                    </button>
                </div>

                <dl>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $phone?->value ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>WhatsApp</dt>
                        <dd>{{ $whatsapp?->value ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>Email</dt>
                        <dd>{{ $email?->value ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>City</dt>
                        <dd>{{ $lead->city ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>Source</dt>
                        <dd>{{ $lead->source?->name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>Status</dt>
                        <dd>
                            <span
                                class="status-badge"
                                @if ($lead->status?->color_code)
                                    style="background-color: {{ $lead->status->color_code }};"
                                @endif
                            >
                                {{ $lead->status?->name ?? 'Unknown' }}
                            </span>
                        </dd>
                    </div>

                     <div>
                        <dt>Stage</dt>
                        <dd>
                            <span
                                class="status-badge"
                                @if ($lead->pipelineStage?->color_code)
                                    style="background-color: {{ $lead->pipelineStage->color_code }};"
                                @endif
                            >
                                {{ $lead->pipelineStage?->name ?? 'Unknown' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt>Priority</dt>
                        <dd>{{ $lead->priority?->name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>Assigned user</dt>
                        <dd>{{ $lead->assignedUser?->name ?? 'Unassigned' }}</dd>
                    </div>

                    <div>
                        <dt>Created</dt>
                        <dd>{{ $lead->created_at?->format('d M Y') ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt>Next follow-up</dt>
                        <dd>
                            @if ($nextFollowUp)
                                <strong>
                                    {{ $nextFollowUp->due_at?->format('d M, h:i a') ?? '—' }}
                                </strong>

                                @if ($nextFollowUp->type?->name || $nextFollowUp->status?->name)
                                    <small class="d-block">
                                        {{ $nextFollowUp->type?->name ?? '—' }}

                                        @if ($nextFollowUp->status?->name)
                                            · {{ $nextFollowUp->status->name }}
                                        @endif
                                    </small>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mobile-actions">
                    @if ($phone?->value)
                        <a
                            class="btn btn-light"
                            href="tel:{{ $phone->value }}"
                        >
                            <i class="bi bi-telephone"></i>
                            Call
                        </a>
                    @endif

                    @if ($whatsapp?->value)
                        <a
                            class="btn btn-light"
                            target="_blank"
                            rel="noopener"
                            href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp->value) }}"
                        >
                            <i class="bi bi-whatsapp"></i>
                            WhatsApp
                        </a>
                    @endif

                    @if ($email?->value)
                        <a
                            class="btn btn-light"
                            href="mailto:{{ $email->value }}"
                        >
                            <i class="bi bi-envelope"></i>
                            Email
                        </a>
                    @endif

                    <button
                        class="btn btn-light"
                        type="button"
                        data-edit-lead="{{ $lead->public_id }}"
                    >
                        <i class="bi bi-pencil"></i>
                        Edit
                    </button>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="empty-state text-center">
        <i class="bi bi-people"></i>
        <h3>No leads found</h3>
        <p>There are no leads matching your current filters.</p>
    </div>
@endif

<x-pagination
    :paginator="$leads"
    prefix="lead"
/>
