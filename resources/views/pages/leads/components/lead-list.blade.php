@if ($leads->count())
    {{-- Desktop / Table View --}}
    <div class="table-responsive table-desktop">
        <table class="crm-table leads-table">
            <thead>
                <tr>
                    <th style="min-width: 230px;">Lead</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Stage</th>
                    <th>Source</th>
                    <th>Assigned to</th>
                    <th>Priority</th>
                    <th style="min-width: 170px;">Next follow-up</th>
                    <th style="width: 50px; text-align: center;"></th>
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

                        $initials = collect(explode(' ', trim($lead->display_name)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($part) => mb_substr($part, 0, 1))
                            ->implode('');
                        $initials = $initials ?: 'L';
                    @endphp

                    <tr>
                        <td>
                            <button
                                class="lead-ident link-reset"
                                type="button"
                                data-lead-id="{{ $lead->public_id }}"
                                data-lead-action="view"
                                aria-label="View {{ $lead->display_name }}"
                            >
                                <span class="mini-avatar">
                                    {{ $initials }}
                                </span>

                                <span class="lead-ident-info">
                                    <strong class="lead-ident-name">{{ $lead->display_name }}</strong>

                                    <small class="lead-contact">
                                        @if ($phone?->value)
                                            <span class="contact-pill-mini">
                                                <i class="bi bi-telephone"></i>
                                                {{ $phone->value }}
                                                @if ($phone->is_primary)
                                                    <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                                @endif
                                            </span>
                                        @endif

                                        @if ($email?->value)
                                            <span class="contact-pill-mini text-truncate" style="max-width: 170px;">
                                                <i class="bi bi-envelope"></i>
                                                {{ $email->value }}
                                                @if ($email->is_primary)
                                                    <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                                @endif
                                            </span>
                                        @endif

                                        @if ($whatsapp?->value && !$phone?->value)
                                            <span class="contact-pill-mini">
                                                <i class="bi bi-whatsapp"></i>
                                                {{ $whatsapp->value }}
                                                @if ($whatsapp->is_primary)
                                                    <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                                @endif
                                            </span>
                                        @endif

                                        @if (!$phone?->value && !$email?->value && !$whatsapp?->value)
                                            <span class="text-muted">—</span>
                                        @endif
                                    </small>
                                </span>
                            </button>
                        </td>

                        <td>
                            @if ($lead->city)
                                <span class="lead-city-text">
                                    <i class="bi bi-geo-alt text-muted me-1"></i>{{ $lead->city }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
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
                            <span class="lead-source-text">
                                {{ $lead->source?->name ?? '—' }}
                            </span>
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
                                    <span>{{ $lead->assignedUser->name }}</span>
                                </span>
                            @else
                                <span class="text-muted small">Unassigned</span>
                            @endif
                        </td>

                        <td>
                            @if ($lead->priority?->name)
                                <span class="badge bg-light text-secondary border">
                                    {{ $lead->priority->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            @if ($nextFollowUp)
                                <div class="lead-table-followup">
                                    <strong class="d-block text-dark">
                                        <i class="bi bi-calendar3 text-danger me-1"></i>
                                        {{ $nextFollowUp->due_at?->format('d M, h:i a') ?? '—' }}
                                    </strong>

                                    @if ($nextFollowUp->type?->name || $nextFollowUp->status?->name)
                                        <small class="text-muted d-block">
                                            {{ $nextFollowUp->type?->name ?? '—' }}
                                            @if ($nextFollowUp->status?->name)
                                                · {{ $nextFollowUp->status->name }}
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <x-action-menu
                                :id="$lead->public_id"
                                attribute="lead"
                                className="table-action"
                                :actions="[
                                    [
                                        'label' => 'View',
                                        'action' => 'view',
                                        'icon' => 'eye',
                                    ],
                                    ...(auth()->user()?->hasPermission('leads.update') ? [
                                        [
                                            'label' => 'Edit',
                                            'action' => 'edit',
                                            'icon' => 'pencil-square',
                                        ],
                                        [
                                            'label' => 'Add follow-up',
                                            'action' => 'follow-up',
                                            'icon' => 'calendar-plus',
                                        ],
                                        [
                                            'label' => 'Manage Tags',
                                            'action' => 'tag',
                                            'icon' => 'tag',
                                        ],
                                    ] : []),
                                    ...($phone?->value ? [[
                                        'label' => 'Call',
                                        'href' => 'tel:' . $phone->value,
                                        'icon' => 'telephone',
                                    ]] : []),
                                    ...($email?->value ? [[
                                        'label' => 'Email',
                                        'href' => 'mailto:' . $email->value,
                                        'icon' => 'envelope',
                                    ]] : []),
                                    ...($whatsapp?->value ? [[
                                        'label' => 'WhatsApp',
                                        'href' => 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp->value),
                                        'icon' => 'whatsapp',
                                        'target' => '_blank',
                                        'rel' => 'noopener',
                                    ]] : []),
                                    [
                                        'label' => 'Delete',
                                        'action' => 'delete',
                                        'icon' => 'trash',
                                        'danger' => true,
                                    ],
                                ]"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile / Compact Card View --}}
    <div class="mobile-records compact-grid leads-compact-grid">
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

                $initials = collect(explode(' ', trim($lead->display_name)))
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => mb_substr($part, 0, 1))
                    ->implode('');
                $initials = $initials ?: 'L';
            @endphp

            <article class="mobile-record leads-card position-relative">

                {{-- Card Header: Avatar, Name, Primary Contact & Actions --}}
                <div class="leads-card-header d-flex align-items-start justify-content-between gap-2">
                    <button
                        class="lead-ident link-reset"
                        type="button"
                        data-lead-id="{{ $lead->public_id }}"
                        data-lead-action="view"
                        aria-label="View {{ $lead->display_name }}"
                    >
                        <span class="mini-avatar">
                            {{ $initials }}
                        </span>

                        <span class="lead-ident-info">
                            <strong>{{ $lead->display_name }}</strong>

                            <small class="lead-contact">
                                @if ($phone?->value)
                                    <span class="contact-pill-mini">
                                        <i class="bi bi-telephone"></i>
                                        {{ $phone->value }}
                                        @if ($phone->is_primary)
                                            <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                        @endif
                                    </span>
                                @elseif ($email?->value)
                                    <span class="contact-pill-mini">
                                        <i class="bi bi-envelope"></i>
                                        {{ $email->value }}
                                        @if ($email->is_primary)
                                            <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                        @endif
                                    </span>
                                @elseif ($whatsapp?->value)
                                    <span class="contact-pill-mini">
                                        <i class="bi bi-whatsapp"></i>
                                        {{ $whatsapp->value }}
                                        @if ($whatsapp->is_primary)
                                            <i class="bi bi-star-fill text-warning" title="Primary"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </small>
                        </span>
                    </button>

                    <x-action-menu
                        position="top-right"
                        :id="$lead->public_id"
                        attribute="lead"
                        :actions="[
                            [
                                'label' => 'View',
                                'action' => 'view',
                                'icon' => 'eye',
                            ],
                            ...(auth()->user()?->hasPermission('leads.update') ? [
                                [
                                    'label' => 'Edit',
                                    'action' => 'edit',
                                    'icon' => 'pencil-square',
                                ],
                                [
                                    'label' => 'Add follow-up',
                                    'action' => 'follow-up',
                                    'icon' => 'calendar-plus',
                                ],
                                [
                                    'label' => 'Manage Tags',
                                    'action' => 'tag',
                                    'icon' => 'tag',
                                ],
                            ] : []),
                            [
                                'label' => 'Delete',
                                'action' => 'delete',
                                'icon' => 'trash',
                                'danger' => true,
                            ],
                        ]"
                    />
                </div>

                {{-- Badges Row: Status, Stage, Priority --}}
                <div class="leads-card-badges d-flex align-items-center flex-wrap gap-2">
                    <span
                        class="status-badge"
                        @if ($lead->status?->color_code)
                            style="background-color: {{ $lead->status->color_code }};"
                        @endif
                    >
                        {{ $lead->status?->name ?? 'Unknown' }}
                    </span>

                    <span
                        class="status-badge"
                        @if ($lead->pipelineStage?->color_code)
                            style="background-color: {{ $lead->pipelineStage->color_code }};"
                        @endif
                    >
                        {{ $lead->pipelineStage?->name ?? 'Unknown' }}
                    </span>

                    @if ($lead->priority?->name)
                        <span class="badge bg-light text-secondary border">
                            {{ $lead->priority->name }}
                        </span>
                    @endif
                </div>

                {{-- Metadata Grid: Structured logical grouping --}}
                <div class="leads-card-meta">
                    <div class="leads-meta-item">
                        <span class="leads-meta-label">Phone</span>
                        <span class="leads-meta-val">{{ $phone?->value ?? '—' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">WhatsApp</span>
                        <span class="leads-meta-val">{{ $whatsapp?->value ?? '—' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">Email</span>
                        <span class="leads-meta-val text-truncate">{{ $email?->value ?? '—' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">City</span>
                        <span class="leads-meta-val">{{ $lead->city ?? '—' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">Source</span>
                        <span class="leads-meta-val">{{ $lead->source?->name ?? '—' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">Assigned user</span>
                        <span class="leads-meta-val">{{ $lead->assignedUser?->name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="leads-meta-item">
                        <span class="leads-meta-label">Created</span>
                        <span class="leads-meta-val">{{ $lead->created_at?->format('d M Y') ?? '—' }}</span>
                    </div>
                </div>

                {{-- Dedicated Next Follow-up Section --}}
                <div class="leads-card-followup">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-danger fs-5"></i>
                        <div>
                            <span class="leads-followup-label">NEXT FOLLOW-UP</span>
                            <div class="leads-followup-time">
                                @if ($nextFollowUp)
                                    <strong>{{ $nextFollowUp->due_at?->format('d M, h:i a') ?? '—' }}</strong>
                                    @if ($nextFollowUp->type?->name || $nextFollowUp->status?->name)
                                        <small class="text-muted d-block">
                                            {{ $nextFollowUp->type?->name ?? '' }}
                                            @if ($nextFollowUp->status?->name)
                                                · {{ $nextFollowUp->status->name }}
                                            @endif
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Communication Actions --}}
                @if ($phone?->value || $whatsapp?->value || $email?->value)
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
                    </div>
                @endif

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
