@php
    $review = $review ?? [];

    $totalCount = (int) ($review['total_count'] ?? 0);
    $readyCount = (int) ($review['ready_count'] ?? 0);
    $issueCount = (int) ($review['issue_count'] ?? 0);
    $matchCount = (int) ($review['match_count'] ?? 0);
    $issues = $review['issues'] ?? [];
    $matches = $review['matches'] ?? [];
    $sample = $review['sample'] ?? [];
@endphp

<section
    class="lead-import-review"
    aria-labelledby="leadImportReviewHeading"
>
    <article class="panel lead-import-card">

        <header class="lead-import-section-head">
            <div>
                <h3
                    class="lead-import-title"
                    id="leadImportReviewHeading"
                >
                    Review and confirm
                </h3>

                <p class="lead-import-description">
                    Review duplicate rows and problems before importing. Duplicates will be skipped automatically.
                </p>
            </div>
        </header>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--success h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </span>

                    <div>
                        <strong id="leadImportReviewReadyCount">
                            {{ number_format($readyCount) }}
                        </strong>
                        <span>Ready to import</span>
                        <small>Valid rows with no action needed</small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--warning h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-intersect"></i>
                    </span>

                    <div>
                        <strong id="leadImportReviewMatchCount">
                            {{ number_format($matchCount) }}
                        </strong>
                        <span>Duplicates</span>
                        <small>Will be skipped automatically</small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--danger h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-exclamation-circle"></i>
                    </span>

                    <div>
                        <strong id="leadImportReviewIssueCount">
                            {{ number_format($issueCount) }}
                        </strong>
                        <span>Problems</span>
                        <small>Need attention before import</small>
                    </div>
                </article>
            </div>
        </div>

        <div class="lead-import-review-toolbar">
            <div
                class="lead-import-review-tabs"
                role="tablist"
                aria-label="Import review filters"
            >

                <button
                    class="lead-import-review-tab active"
                    type="button"
                    data-review-filter="sample"
                >
                    Ready To Import
                </button>

                <button
                    class="lead-import-review-tab"
                    type="button"
                    data-review-filter="issues"
                >
                    Problems
                    <span id="leadImportReviewIssueTabCount">
                        {{ number_format($issueCount) }}
                    </span>
                </button>


                <button
                    class="lead-import-review-tab"
                    type="button"
                    data-review-filter="matches"
                >
                     Duplicates
                    <span id="leadImportReviewMatchTabCount">
                        {{ number_format($matchCount) }}
                    </span>
                </button>


            </div>

        </div>

        <div
            class="lead-import-review-view"
            id="leadImportReviewMatches"
            data-review-view="matches"
        >
            <div class="table-responsive lead-import-table-wrap">
                <table class="table align-middle mb-0 lead-import-table">
                    <thead>
                        <tr>
                            <th scope="col">Row</th>
                            <th scope="col">Lead data</th>
                            <th scope="col">Duplicate of</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody id="leadImportReviewMatchList">
                        @forelse ($matches as $match)
                            @php
                                $matchItems = $match['matches'] ?? [];
                                $firstMatch = $matchItems[0] ?? [];
                                $existingLead = $firstMatch['lead'] ?? [];
                                $contacts = $match['contacts'] ?? [];
                                $firstContact = $contacts[0] ?? [];
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        {{ $match['row_number'] ?? '—' }}
                                    </strong>
                                </td>

                                <td>
                                    <strong class="d-block">
                                        {{ $match['display_name'] ?? 'Lead' }}
                                    </strong>

                                    @if (!empty($firstContact['value']))
                                        <small class="text-muted d-block mt-1">
                                            {{ $firstContact['value'] }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <strong class="d-block">
                                        {{ $existingLead['display_name'] ?? 'Existing Lead' }}
                                    </strong>

                                    @if (!empty($firstMatch['contact_value']))
                                        <small class="text-muted d-block mt-1">
                                            {{ $firstMatch['contact_value'] }}
                                        </small>
                                    @endif

                                    @if (!empty($existingLead['public_id']))
                                        <a
                                            class="lead-import-existing-link"
                                            href="{{ route('viewLead', ['lead' => $existingLead['public_id']]) }}"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            View existing Lead
                                        </a>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <span class="badge text-bg-secondary">
                                        Will be skipped
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="lead-import-empty-state">
                                        <i class="bi bi-check2-circle"></i>
                                        <strong>No duplicates found</strong>
                                        <p>No rows need to be skipped as duplicates.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="lead-import-review-view"
            id="leadImportReviewIssues"
            data-review-view="issues"
            hidden
        >
            <div class="lead-import-review-issues" id="leadImportReviewIssueList">
                @forelse ($issues as $issue)
                    <article class="lead-import-issue-row">
                        <span class="lead-import-issue-row-number">
                            Row {{ $issue['row_number'] ?? '—' }}
                        </span>

                        <div>
                            <strong>
                                {{ $issue['display_name'] ?? 'Lead row' }}
                            </strong>

                            <ul>
                                @foreach (($issue['errors'] ?? []) as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @empty
                    <div class="lead-import-empty-state">
                        <i class="bi bi-check2-circle"></i>
                        <strong>No validation problems</strong>
                        <p>All reviewed rows currently pass Lead validation.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div
            class="lead-import-review-view"
            id="leadImportReviewSample"
            data-review-view="sample"
            hidden
        >
            <div class="table-responsive lead-import-table-wrap">
                <table class="table align-middle mb-0 lead-import-table">
                    <thead>
                        <tr>
                            <th scope="col">Row</th>
                            <th scope="col">Lead</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Location</th>
                        </tr>
                    </thead>

                    <tbody id="leadImportReviewSampleBody">
                        @forelse ($sample as $row)
                            @php
                                $contacts = $row['contacts'] ?? [];
                                $firstContact = $contacts[0] ?? [];
                                $location = collect([
                                    $row['city'] ?? null,
                                    $row['state'] ?? null,
                                    $row['country'] ?? null,
                                ])->filter()->implode(', ');
                            @endphp

                            <tr>
                                <td>{{ $row['row_number'] ?? '—' }}</td>
                                <td>
                                    <strong>{{ $row['display_name'] ?? 'Lead' }}</strong>
                                </td>
                                <td>{{ $firstContact['value'] ?? '—' }}</td>
                                <td>{{ $location !== '' ? $location : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="lead-import-empty-state">
                                        <i class="bi bi-list-check"></i>
                                        <strong>No ready sample yet</strong>
                                        <p>Ready rows will appear here after Review runs.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="lead-import-review-meta mt-3"
            id="leadImportReviewStateText"
        >
            {{ number_format($totalCount) }} total rows reviewed
        </div>

    </article>

    <footer class="lead-import-actions">
        <button
            class="btn btn-outline-dark"
            id="leadImportReviewBackBtn"
            type="button"
        >
            <i class="bi bi-arrow-left"></i>
            Back to Prepare
        </button>

        <button
            class="btn btn-danger"
            id="leadImportReviewContinueBtn"
            type="button"
            disabled
        >
            Continue to Import
            <i class="bi bi-arrow-right"></i>
        </button>
    </footer>
</section>
