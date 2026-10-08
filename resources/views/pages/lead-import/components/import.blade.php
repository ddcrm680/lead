@php
    $plan = $plan ?? [];

    $totalCount = (int) ($plan['total_count'] ?? 0);
    $toImportCount = (int) ($plan['to_import_count'] ?? 0);
    $skippedCount = (int) ($plan['skipped_count'] ?? 0);
    $matchCount = (int) ($plan['match_count'] ?? 0);
@endphp

<section
    class="lead-import-import"
    aria-labelledby="leadImportImportHeading"
>
    <article class="panel lead-import-card">

        <header class="lead-import-section-head">
            <div>
                <h3
                    class="lead-import-title"
                    id="leadImportImportHeading"
                >
                    Import leads
                </h3>

                <p class="lead-import-description">
                    Confirm the reviewed rows before creating Lead records.
                </p>
            </div>

            <span class="lead-import-status-pill lead-import-status-pill--success">
                <span></span>

                <strong id="leadImportImportStateText">
                    Ready to import
                </strong>
            </span>
        </header>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--success h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </span>

                    <div>
                        <strong id="leadImportImportReadyCount">
                            {{ number_format($toImportCount) }}
                        </strong>

                        <span>
                            Leads ready to import
                        </span>

                        <small>
                            Will be created as new Leads
                        </small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--info h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-people"></i>
                    </span>

                    <div>
                        <strong id="leadImportImportMatchCount">
                            {{ number_format($matchCount) }}
                        </strong>

                        <span>
                            Duplicates
                        </span>

                        <small>
                            Will be skipped automatically
                        </small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--danger h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-skip-forward"></i>
                    </span>

                    <div>
                        <strong id="leadImportImportSkippedCount">
                            {{ number_format($skippedCount) }}
                        </strong>

                        <span>
                            Rows will be skipped
                        </span>

                        <small>
                            Duplicate rows are skipped automatically
                        </small>
                    </div>
                </article>
            </div>
        </div>

        {{-- Ready --}}
        <div
            id="leadImportImportReady"
            class="lead-import-import-state-panel"
        >
            <aside class="lead-import-tip">
                <span
                    class="lead-import-tip-icon"
                    aria-hidden="true"
                >
                    <i class="bi bi-info-circle-fill"></i>
                </span>

                <div>
                    <strong>
                        What will happen?
                    </strong>

                    <ul>
                        <li>
                            <span id="leadImportImportTotalCount">
                                {{ number_format($totalCount) }}
                            </span>
                            spreadsheet rows were reviewed.
                        </li>

                        <li>
                            Ready rows will be created using the normal Lead creation flow.
                        </li>

                        <li>
                            Duplicate rows will be skipped automatically.
                        </li>

                        <li>
                            Rows that cannot be safely imported will not be created.
                        </li>

                        <li>
                            Additional spreadsheet columns will be kept with the Lead according to the final import rules.
                        </li>
                    </ul>
                </div>
            </aside>
        </div>

        {{-- Running --}}
        <div
            id="leadImportImportRunning"
            class="lead-import-import-state-panel"
            hidden
        >
            <div class="lead-import-processing">
                <span
                    class="spinner-border"
                    aria-hidden="true"
                ></span>

                <div>
                    <strong>
                        Importing Leads…
                    </strong>

                    <p>
                        Keep this page open while the server processes the reviewed rows.
                    </p>
                </div>
            </div>
        </div>

        {{-- Partial failure results --}}
        <div
            id="leadImportImportResults"
            class="lead-import-import-state-panel"
            hidden
        >
            <div class="lead-import-result-head">
                <span>
                    <i class="bi bi-exclamation-circle"></i>
                </span>

                <div>
                    <h4>
                        Import completed with issues
                    </h4>

                    <p>
                        Some Lead rows could not be imported.
                        Review the failed rows below.
                    </p>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-6 col-xl-3">
                    <div class="lead-import-result-stat">
                        <strong id="leadImportResultProcessedCount">
                            0
                        </strong>

                        <span>
                            Processed
                        </span>
                    </div>
                </div>

                <div class="col-6 col-xl-3">
                    <div class="lead-import-result-stat">
                        <strong id="leadImportResultCreatedCount">
                            0
                        </strong>

                        <span>
                            Created
                        </span>
                    </div>
                </div>

                <div class="col-6 col-xl-3">
                    <div class="lead-import-result-stat">
                        <strong id="leadImportResultSkippedCount">
                            0
                        </strong>

                        <span>
                            Skipped
                        </span>
                    </div>
                </div>

                <div class="col-6 col-xl-3">
                    <div class="lead-import-result-stat">
                        <strong id="leadImportResultFailedCount">
                            0
                        </strong>

                        <span>
                            Failed
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="mt-3"
                id="leadImportImportFailures"
                hidden
            >
                <h4 class="lead-import-subtitle">
                    Rows that were not imported
                </h4>

                <div class="table-responsive lead-import-table-wrap">
                    <table class="table align-middle mb-0 lead-import-table">
                        <thead>
                            <tr>
                                <th>Row</th>
                                <th>Lead</th>
                                <th>Reason</th>
                            </tr>
                        </thead>

                        <tbody id="leadImportResultFailureBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Request / server failure --}}
        <div
            id="leadImportImportError"
            class="lead-import-import-state-panel"
            hidden
        >
            <div class="lead-import-error-box">
                <span>
                    <i class="bi bi-exclamation-triangle"></i>
                </span>

                <div>
                    <strong>
                        Import could not be completed
                    </strong>

                    <p id="leadImportImportErrorMessage">
                        Review the error and try again.
                    </p>
                </div>
            </div>
        </div>

    </article>

    <footer class="lead-import-actions">
        <button
            class="btn btn-outline-dark"
            id="leadImportImportBackBtn"
            type="button"
        >
            <i class="bi bi-arrow-left"></i>
            Back to Review
        </button>

        <button
            class="btn btn-danger"
            id="leadImportStartBtn"
            type="button"
            disabled
        >
            <i class="bi bi-cloud-arrow-up"></i>
            Start Import
        </button>
    </footer>
</section>
