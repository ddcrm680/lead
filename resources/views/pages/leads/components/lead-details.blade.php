@php
    $contacts = $lead->contacts
        ->sortByDesc('is_primary')
        ->values();

    $phone = $contacts->firstWhere('type', 'phone');
    $whatsapp = $contacts->firstWhere('type', 'whatsapp');
    $email = $contacts->firstWhere('type', 'email');

    $initials = collect(explode(' ', trim($lead->display_name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');

    $pendingFollowUps = $lead->followUps
        ->whereNull('completed_at')
        ->sortBy('due_at')
        ->values();

    $nextFollowUp = $pendingFollowUps->first();

    $followUps = $lead->followUps
        ->sortByDesc('due_at')
        ->values();

    $assignments = $lead->assignments
        ->sortByDesc('assigned_at')
        ->values();

    $events = $lead->events
        ->sortByDesc('occurred_at')
        ->values();

    $attributes = collect($lead->attributes ?? [])
        ->filter(fn ($value) => $value !== null && $value !== '');

    $contactIcons = [
        'phone' => 'telephone',
        'whatsapp' => 'whatsapp',
        'email' => 'envelope',
    ];
@endphp

<div
    class="offcanvas offcanvas-end detail-drawer lead-profile-drawer"
    tabindex="-1"
    id="leadDetail"
    aria-labelledby="leadDetailTitle"
>

    {{-- Header --}}
    <header class="offcanvas-header lead-profile-header">

        <div class="lead-profile-header-main">

            <span class="lead-profile-avatar">
                {{ $initials }}
            </span>

            <div class="lead-profile-header-copy">

                <div class="lead-profile-heading-row">
                    <span class="eyebrow">
                        LEAD PROFILE
                    </span>

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
                </div>

                <h2 id="leadDetailTitle">
                    {{ $lead->display_name }}
                </h2>

                <div class="lead-profile-meta">

                    <span class="lead-profile-id">
                        {{ $lead->public_id }}
                    </span>

                    @if ($lead->city || $lead->state)
                        <span>
                            <i class="bi bi-geo-alt"></i>

                            {{ collect([
                                $lead->city,
                                $lead->state,
                            ])->filter()->implode(', ') }}
                        </span>
                    @endif

                    <span>
                        <i class="bi bi-person"></i>
                        {{ $lead->assignedUser?->name ?? 'Unassigned' }}
                    </span>

                    <span>
                        <i class="bi bi-clock"></i>
                        {{ $lead->created_at?->format('d M Y') ?? '—' }}
                    </span>

                </div>

            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>

    </header>

    <div class="offcanvas-body lead-profile-body">

        {{-- Quick actions --}}
        <div class="lead-profile-actions">

            @if ($phone?->value)
                <a
                    class="btn btn-dark lead-profile-action"
                    href="tel:{{ $phone->value }}"
                >
                    <i class="bi bi-telephone"></i>
                    <span>Call</span>
                </a>
            @endif

            @if ($whatsapp?->value)
                <a
                    class="btn btn-outline-dark lead-profile-action"
                    href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp->value) }}"
                    target="_blank"
                    rel="noopener"
                >
                    <i class="bi bi-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            @endif

            @if ($email?->value)
                <a
                    class="btn btn-outline-dark lead-profile-action lead-profile-action--secondary"
                    href="mailto:{{ $email->value }}"
                >
                    <i class="bi bi-envelope"></i>
                    <span>Email</span>
                </a>
            @endif

            @if (auth()->user()?->hasPermission('leads.update'))
                <button
                    class="btn btn-outline-dark lead-profile-action lead-profile-action--secondary"
                    type="button"
                    data-lead-id="{{ $lead->public_id }}"
                    data-lead-action="edit"
                >
                    <i class="bi bi-pencil"></i>
                    <span>Edit</span>
                </button>
            @endif

            <x-action-menu
                :id="$lead->public_id"
                attribute="lead"
                className="btn btn-outline-dark lead-profile-more"
                :actions="[
                    [
                        'label' => 'Add follow-up',
                        'action' => 'follow-up',
                        'icon' => 'calendar-plus',
                    ],
                    [
                        'label' => 'Add tag',
                        'action' => 'tag',
                        'icon' => 'tag',
                    ],
                    ...($email?->value ? [[
                        'label' => 'Email',
                        'href' => 'mailto:' . $email->value,
                        'icon' => 'envelope',
                    ]] : []),
                    ...(auth()->user()?->hasPermission('leads.update') ? [[
                        'label' => 'Edit lead',
                        'action' => 'edit',
                        'icon' => 'pencil',
                    ]] : []),
                    [
                        'label' => 'Delete lead',
                        'action' => 'delete',
                        'icon' => 'trash',
                        'danger' => true,
                    ],
                ]"
            />

        </div>

        {{-- Summary --}}
        <section class="lead-profile-summary">

            <article class="lead-profile-stat">

                <span class="lead-profile-stat-icon">
                    <i class="bi bi-bookmark"></i>
                </span>

                <div>
                    <small>Status</small>

                    @if ($lead->status)
                        <span
                            class="status-badge"
                            @if ($lead->status->color_code)
                                style="background-color: {{ $lead->status->color_code }};"
                            @endif
                        >
                            {{ $lead->status->name }}
                        </span>
                    @else
                        <strong>—</strong>
                    @endif
                </div>

            </article>

            <article class="lead-profile-stat">

                <span class="lead-profile-stat-icon">
                    <i class="bi bi-bar-chart"></i>
                </span>

                <div>
                    <small>Stage</small>

                    @if ($lead->pipelineStage)
                        <span
                            class="status-badge"
                            @if ($lead->pipelineStage->color_code)
                                style="background-color: {{ $lead->pipelineStage->color_code }};"
                            @endif
                        >
                            {{ $lead->pipelineStage->name }}
                        </span>
                    @else
                        <strong>—</strong>
                    @endif
                </div>

            </article>

            <article class="lead-profile-stat">

                <span class="lead-profile-stat-icon lead-profile-stat-icon--warm">
                    <i class="bi bi-flag"></i>
                </span>

                <div>
                    <small>Priority</small>

                    <strong>
                        {{ $lead->priority?->name ?? '—' }}
                    </strong>
                </div>

            </article>

            <article class="lead-profile-stat">

                <span class="lead-profile-stat-icon lead-profile-stat-icon--violet">
                    <i class="bi bi-link-45deg"></i>
                </span>

                <div>
                    <small>Source</small>

                    <strong>
                        {{ $lead->source?->name ?? '—' }}
                    </strong>
                </div>

            </article>

        </section>

        {{-- Main layout --}}
        <div class="lead-profile-grid">

            {{-- Left column --}}
            <div class="lead-profile-column">

                {{-- Overview --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-person-vcard"></i>
                        </span>

                        <div>
                            <h3>Overview</h3>
                            <p>Key information about this lead.</p>
                        </div>

                    </header>

                    <dl class="lead-profile-info">

                        <div>
                            <dt>
                                <i class="bi bi-hash"></i>
                                Lead ID
                            </dt>

                            <dd class="lead-profile-break">
                                {{ $lead->public_id }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-bookmark"></i>
                                Status
                            </dt>

                            <dd>
                                {{ $lead->status?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-bar-chart"></i>
                                Stage
                            </dt>

                            <dd>
                                {{ $lead->pipelineStage?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-flag"></i>
                                Priority
                            </dt>

                            <dd>
                                {{ $lead->priority?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-link-45deg"></i>
                                Source
                            </dt>

                            <dd>
                                {{ $lead->source?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-person"></i>
                                Assigned to
                            </dt>

                            <dd>
                                {{ $lead->assignedUser?->name ?? 'Unassigned' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-person-check"></i>
                                Created by
                            </dt>

                            <dd>
                                {{ $lead->creator?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-calendar3"></i>
                                Created
                            </dt>

                            <dd>
                                {{ $lead->created_at?->format('d M Y, h:i a') ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt>
                                <i class="bi bi-arrow-repeat"></i>
                                Updated
                            </dt>

                            <dd>
                                {{ $lead->updated_at?->format('d M Y, h:i a') ?? '—' }}
                            </dd>
                        </div>

                    </dl>

                </section>

                {{-- Contacts --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-telephone"></i>
                        </span>

                        <div>
                            <h3>Contacts</h3>
                            <p>Phone, email and messaging details.</p>
                        </div>

                    </header>

                    <div class="lead-profile-contact-list">

                        @forelse ($contacts as $contact)

                            <article class="lead-profile-contact">

                                <span
                                    class="lead-profile-contact-icon lead-profile-contact-icon--{{ $contact->type }}"
                                >
                                    <i class="bi bi-{{ $contactIcons[$contact->type] ?? 'person' }}"></i>
                                </span>

                                <div class="lead-profile-contact-copy">

                                    <div class="lead-profile-contact-label">

                                        <span class="text-capitalize">
                                            {{ $contact->type }}
                                        </span>

                                        @if ($contact->is_primary)
                                            <span class="lead-profile-primary">
                                                <i class="bi bi-star-fill"></i>
                                                Primary
                                            </span>
                                        @endif

                                    </div>

                                    <strong>
                                        {{ $contact->value }}
                                    </strong>

                                </div>

                                @if ($contact->verified_at)
                                    <span class="lead-profile-verified">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Verified
                                    </span>
                                @endif

                            </article>

                        @empty

                            <div class="lead-profile-empty">

                                <i class="bi bi-person-lines-fill"></i>

                                <div>
                                    <strong>No contacts available</strong>
                                    <span>No contact details have been added.</span>
                                </div>

                            </div>

                        @endforelse

                    </div>

                </section>

                {{-- Assignment history --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-people"></i>
                        </span>

                        <div>
                            <h3>Assignment history</h3>
                            <p>Previous assignment changes for this lead.</p>
                        </div>

                    </header>

                    <div class="lead-profile-assignment-list">

                        @forelse ($assignments as $assignment)

                            <article class="lead-profile-assignment">

                                <span class="lead-profile-assignment-icon">
                                    <i class="bi bi-person"></i>
                                </span>

                                <div class="lead-profile-assignment-copy">

                                    <div class="lead-profile-assignment-head">

                                        <strong>
                                            {{ $assignment->user?->name ?? 'Unknown user' }}
                                        </strong>

                                        <time>
                                            {{ $assignment->assigned_at?->format('d M Y, h:i a') ?? '—' }}
                                        </time>

                                    </div>

                                    <div class="lead-profile-assignment-meta">

                                        @if ($assignment->assignedBy)
                                            <span>
                                                Assigned by {{ $assignment->assignedBy->name }}
                                            </span>
                                        @endif

                                        @if ($assignment->type)
                                            <span>
                                                {{ ucfirst(str_replace('_', ' ', $assignment->type)) }}
                                            </span>
                                        @endif

                                        @if ($assignment->ended_at)
                                            <span>
                                                Ended {{ $assignment->ended_at->format('d M Y, h:i a') }}
                                            </span>
                                        @endif

                                    </div>

                                    @if ($assignment->reason)
                                        <p>
                                            {{ $assignment->reason }}
                                        </p>
                                    @endif

                                </div>

                            </article>

                        @empty

                            <div class="lead-profile-empty">

                                <i class="bi bi-people"></i>

                                <div>
                                    <strong>No assignment history</strong>
                                    <span>No previous assignment changes.</span>
                                </div>

                            </div>

                        @endforelse

                    </div>

                </section>

                {{-- Additional details --}}
                @if ($attributes->isNotEmpty())

                    <section class="lead-profile-card">

                        <header class="lead-profile-card-header">

                            <span class="lead-profile-card-icon">
                                <i class="bi bi-ui-checks-grid"></i>
                            </span>

                            <div>
                                <h3>Additional details</h3>
                                <p>Custom information stored for this lead.</p>
                            </div>

                        </header>

                        <dl class="lead-profile-info">

                            @foreach ($attributes as $key => $value)

                                @php
                                    if (is_bool($value)) {
                                        $displayValue = $value ? 'Yes' : 'No';
                                    } elseif (is_array($value)) {
                                        $displayValue = collect($value)
                                            ->flatten()
                                            ->filter(
                                                fn ($item) => is_scalar($item)
                                            )
                                            ->implode(', ');
                                    } else {
                                        $displayValue = $value;
                                    }
                                @endphp

                                <div>

                                    <dt>
                                        <i class="bi bi-dot"></i>
                                        {{ \Illuminate\Support\Str::headline($key) }}
                                    </dt>

                                    <dd class="lead-profile-break">
                                        {{ $displayValue !== '' ? $displayValue : '—' }}
                                    </dd>

                                </div>

                            @endforeach

                        </dl>

                    </section>

                @endif

            </div>

            {{-- Right column --}}
            <div class="lead-profile-column">

                {{-- Location --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-geo-alt"></i>
                        </span>

                        <div>
                            <h3>Location</h3>
                            <p>Geographic information.</p>
                        </div>

                    </header>

                    <dl class="lead-profile-location">

                        <div>
                            <dt>City</dt>
                            <dd>{{ $lead->city ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt>State</dt>
                            <dd>{{ $lead->state ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt>Country</dt>
                            <dd>{{ $lead->country ?? '—' }}</dd>
                        </div>

                    </dl>

                </section>

                {{-- Tags --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-tags"></i>
                        </span>

                        <div>
                            <h3>Tags</h3>
                            <p>Labels attached to this lead.</p>
                        </div>

                        <button
                            class="btn btn-sm btn-outline-dark lead-profile-card-action"
                            type="button"
                            data-lead-id="{{ $lead->public_id }}"
                            data-lead-action="tag"
                        >
                            <i class="bi bi-plus"></i>
                            Add
                        </button>

                    </header>

                    @if ($lead->tags->isNotEmpty())

                        <div class="lead-profile-tags">

                            @foreach ($lead->tags as $tag)

                                <span
                                    class="badge rounded-pill {{ $tag->color_code ? '' : 'text-bg-light' }}"
                                    @if ($tag->color_code)
                                        style="background-color: {{ $tag->color_code }};"
                                    @endif
                                >
                                    {{ $tag->name }}
                                </span>

                            @endforeach

                        </div>

                    @else

                        <div class="lead-profile-empty lead-profile-empty--compact">

                            <i class="bi bi-tags"></i>

                            <div>
                                <strong>No tags</strong>
                                <span>This lead has no tags.</span>
                            </div>

                        </div>

                    @endif

                </section>

                {{-- Follow-up --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-calendar-check"></i>
                        </span>

                        <div>
                            <h3>Next follow-up</h3>
                            <p>Upcoming follow-up and reminders.</p>
                        </div>

                        <button
                            class="btn btn-sm btn-outline-dark lead-profile-card-action"
                            type="button"
                            data-lead-id="{{ $lead->public_id }}"
                            data-lead-action="follow-up"
                        >
                            <i class="bi bi-plus"></i>
                            Add
                        </button>

                    </header>

                    @if ($nextFollowUp)

                        <article class="lead-profile-next-followup">

                            <div class="lead-profile-next-followup-head">

                                <div>

                                    <strong>
                                        {{ $nextFollowUp->title }}
                                    </strong>

                                    <div class="lead-profile-inline-meta">

                                        @if ($nextFollowUp->type?->name)
                                            <span>
                                                {{ $nextFollowUp->type->name }}
                                            </span>
                                        @endif

                                        @if ($nextFollowUp->status?->name)
                                            <span>
                                                {{ $nextFollowUp->status->name }}
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <time>
                                    {{ $nextFollowUp->due_at?->format('d M Y, h:i a') ?? '—' }}
                                </time>

                            </div>

                            @if ($nextFollowUp->assignedUser)
                                <small>
                                    <i class="bi bi-person"></i>
                                    Assigned to {{ $nextFollowUp->assignedUser->name }}
                                </small>
                            @endif

                            @if ($nextFollowUp->notes)
                                <p>
                                    {{ $nextFollowUp->notes }}
                                </p>
                            @endif

                        </article>

                    @else

                        <div class="lead-profile-followup-empty">

                            <span>
                                <i class="bi bi-calendar2-check"></i>
                            </span>

                            <strong>
                                No pending follow-up.
                            </strong>

                            <p>
                                There is currently no upcoming follow-up for this lead.
                            </p>

                            <button
                                class="btn btn-sm btn-outline-dark lead-profile-empty-action"
                                type="button"
                                data-lead-id="{{ $lead->public_id }}"
                                data-lead-action="follow-up"
                            >
                                <i class="bi bi-plus"></i>
                                Add Follow-up
                            </button>

                        </div>

                    @endif

                    @if ($followUps->isNotEmpty())

                        <details class="lead-profile-followup-history">

                            <summary>
                                Follow-up history

                                <span>
                                    {{ $followUps->count() }}
                                </span>
                            </summary>

                            <div class="lead-profile-followup-list">

                                @foreach ($followUps as $followUp)

                                    <article>

                                        <div class="lead-profile-followup-head">

                                            <strong>
                                                {{ $followUp->title }}
                                            </strong>

                                            <time>
                                                {{ $followUp->due_at?->format('d M Y, h:i a') ?? '—' }}
                                            </time>

                                        </div>

                                        <div class="lead-profile-inline-meta">

                                            @if ($followUp->type?->name)
                                                <span>
                                                    {{ $followUp->type->name }}
                                                </span>
                                            @endif

                                            @if ($followUp->status?->name)
                                                <span>
                                                    {{ $followUp->status->name }}
                                                </span>
                                            @endif

                                            @if ($followUp->assignedUser)
                                                <span>
                                                    {{ $followUp->assignedUser->name }}
                                                </span>
                                            @endif

                                        </div>

                                        @if ($followUp->notes)
                                            <p>
                                                {{ $followUp->notes }}
                                            </p>
                                        @endif

                                        @if ($followUp->completed_at)

                                            <small class="lead-profile-completed">

                                                <i class="bi bi-check-circle-fill"></i>

                                                Completed
                                                {{ $followUp->completed_at->format('d M Y, h:i a') }}

                                                @if ($followUp->completedBy)
                                                    by {{ $followUp->completedBy->name }}
                                                @endif

                                            </small>

                                        @endif

                                    </article>

                                @endforeach

                            </div>

                        </details>

                    @endif

                </section>

                {{-- Activity --}}
                <section class="lead-profile-card">

                    <header class="lead-profile-card-header">

                        <span class="lead-profile-card-icon">
                            <i class="bi bi-clock-history"></i>
                        </span>

                        <div>
                            <h3>Activity</h3>
                            <p>Recent activity on this lead.</p>
                        </div>

                    </header>

                    <div class="lead-profile-timeline">

                        @forelse ($events as $event)

                            <article class="lead-profile-timeline-item">

                                <span class="lead-profile-timeline-dot"></span>

                                <div>

                                    <div class="lead-profile-timeline-head">

                                        <div>

                                            <strong>
                                                {{ $event->title }}
                                            </strong>

                                            @if ($event->type)
                                                <small>
                                                    {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                                                </small>
                                            @endif

                                        </div>

                                        <time>
                                            {{ $event->occurred_at?->format('d M Y, h:i a') ?? '—' }}
                                        </time>

                                    </div>

                                    @if ($event->description)
                                        <p>
                                            {{ $event->description }}
                                        </p>
                                    @endif

                                    @if ($event->user)
                                        <span class="lead-profile-event-user">
                                            <i class="bi bi-person"></i>
                                            {{ $event->user->name }}
                                        </span>
                                    @endif

                                </div>

                            </article>

                        @empty

                            <div class="lead-profile-empty">

                                <i class="bi bi-clock-history"></i>

                                <div>
                                    <strong>No activity recorded</strong>
                                    <span>Activity will appear here as the lead progresses.</span>
                                </div>

                            </div>

                        @endforelse

                    </div>

                </section>

            </div>

        </div>

    </div>

</div>
