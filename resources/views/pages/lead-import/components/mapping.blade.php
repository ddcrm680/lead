@php
    $importMeta = $importMeta ?? [];
    $analysis = $analysis ?? [];
    $options = $options ?? [];

    $recognizedColumns = $analysis['recognized_columns'] ?? [];
    $additionalColumns = $analysis['additional_columns'] ?? [];
    $ignoredColumns = $analysis['ignored_columns'] ?? [];
    $hasSourceColumn = (bool) ($analysis['has_source_column'] ?? false);
@endphp

<section
    class="lead-import-prepare"
    aria-labelledby="leadImportPrepareHeading"
>
    <article class="panel lead-import-card">

        <header class="lead-import-section-head lead-import-section-head--with-file">
            <div>
                <h3
                    class="lead-import-title"
                    id="leadImportPrepareHeading"
                >
                    File analysis
                </h3>

                <p class="lead-import-description">
                    We’ve analyzed your file and automatically understood the columns.
                </p>
            </div>

            <div class="lead-import-file-summary">
                <span
                    class="lead-import-file-summary-icon"
                    aria-hidden="true"
                >
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                </span>

                <div class="min-w-0 flex-grow-1">
                    <strong id="leadImportPrepareFileName">
                        {{ $importMeta['original_name'] ?? 'Selected file' }}
                    </strong>

                    <small id="leadImportPrepareFileMeta">
                        @if (!empty($importMeta['row_count']) || !empty($importMeta['column_count']))
                            {{ number_format((int) ($importMeta['row_count'] ?? 0)) }} rows ·
                            {{ number_format((int) ($importMeta['column_count'] ?? 0)) }} columns
                        @else
                            Waiting for file analysis
                        @endif
                    </small>
                </div>

                <button
                    class="btn btn-sm btn-outline-dark"
                    id="leadImportChangeFileBtn"
                    type="button"
                >
                    Change
                </button>
            </div>
        </header>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--success h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-check2-circle"></i>
                    </span>

                    <div>
                        <strong id="leadImportRecognizedCount">
                            {{ count($recognizedColumns) }}
                        </strong>
                        <span>Recognized columns</span>
                        <small>Will import to Lead fields</small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat lead-import-stat--info h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-database-add"></i>
                    </span>

                    <div>
                        <strong id="leadImportAdditionalCount">
                            {{ count($additionalColumns) }}
                        </strong>
                        <span>Additional columns</span>
                        <small>Will be kept with your Leads</small>
                    </div>
                </article>
            </div>

            <div class="col-12 col-md-4">
                <article class="lead-import-stat h-100">
                    <span class="lead-import-stat-icon">
                        <i class="bi bi-eye-slash"></i>
                    </span>

                    <div>
                        <strong id="leadImportIgnoredCount">
                            {{ count($ignoredColumns) }}
                        </strong>
                        <span>Ignored columns</span>
                        <small>Export/system metadata</small>
                    </div>
                </article>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <section class="lead-import-subcard h-100">
                    <header class="mb-3">
                        <h4>
                            Recognized columns
                            <span class="text-muted" id="leadImportRecognizedLabelCount">
                                ({{ count($recognizedColumns) }})
                            </span>
                        </h4>

                        <p>
                            These columns will automatically import to the appropriate Lead fields.
                        </p>
                    </header>

                    <div
                        class="lead-import-recognized-grid"
                        id="leadImportRecognizedColumns"
                    >
                        @forelse ($recognizedColumns as $column)
                            <div class="lead-import-recognized-row">
                                <span class="lead-import-check">
                                    <i class="bi bi-check"></i>
                                </span>

                                <strong>
                                    {{ $column['header'] ?? $column['name'] ?? 'Column' }}
                                </strong>

                                <span>
                                    {{ $column['label'] ?? $column['target_label'] ?? 'Lead field' }}
                                </span>
                            </div>
                        @empty
                            <div class="lead-import-empty-inline">
                                Recognized columns will appear here after the file is analyzed.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-4">
                <div class="d-grid gap-3 h-100">
                    <section class="lead-import-subcard">
                        <header class="mb-3">
                            <h4>
                                Additional columns
                                <span class="text-muted" id="leadImportAdditionalLabelCount">
                                    ({{ count($additionalColumns) }})
                                </span>
                            </h4>

                            <p>
                                These values will be kept as additional Lead data.
                            </p>
                        </header>

                        <div
                            class="lead-import-additional-list"
                            id="leadImportAdditionalColumns"
                        >
                            @forelse ($additionalColumns as $column)
                                <div class="lead-import-additional-row">
                                    <span>
                                        <i class="bi bi-database"></i>
                                    </span>

                                    <strong>
                                        {{ $column['header'] ?? $column['name'] ?? $column }}
                                    </strong>
                                </div>
                            @empty
                                <div class="lead-import-empty-inline">
                                    No additional columns detected.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="lead-import-subcard">
                        <header class="mb-3">
                            <h4>
                                Ignored columns
                                <span class="text-muted" id="leadImportIgnoredLabelCount">
                                    ({{ count($ignoredColumns) }})
                                </span>
                            </h4>

                            <p>
                                System or export metadata that won’t be imported.
                            </p>
                        </header>

                        <div
                            class="lead-import-chip-list"
                            id="leadImportIgnoredColumns"
                        >
                            @forelse ($ignoredColumns as $column)
                                <span class="lead-import-chip">
                                    {{ $column['header'] ?? $column['name'] ?? $column }}
                                </span>
                            @empty
                                <span class="lead-import-empty-inline">
                                    No ignored columns detected.
                                </span>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <section class="lead-import-defaults mt-3">
            <header class="mb-3">
                <h4>Default values</h4>

                <p>
                    Used only when a recognized spreadsheet value is empty or missing.
                </p>
            </header>

            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-3">
                    <label
                        class="form-label"
                        for="leadImportDefaultStatus"
                    >
                        Status
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        class="form-select"
                        id="leadImportDefaultStatus"
                    >
                        <option value="">
                            Select status
                        </option>

                        @foreach (($options['statuses'] ?? []) as $status)
                            <option value="{{ $status['value'] }}">
                                {{ $status['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <label
                        class="form-label"
                        for="leadImportDefaultSource"
                    >
                        Source
                        @if (!$hasSourceColumn)
                            <span class="text-danger">*</span>
                        @endif
                    </label>

                    <select
                        class="form-select"
                        id="leadImportDefaultSource"
                        data-source-mapped="{{ $hasSourceColumn ? 'true' : 'false' }}"
                    >
                       <option value="">
                            {{ $hasSourceColumn
                                ? 'No default source'
                                : 'Select source'
                            }}
                        </option>

                        @foreach (($options['sources'] ?? []) as $source)
                            <option value="{{ $source['value'] }}">
                                {{ $source['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <label
                        class="form-label"
                        for="leadImportDefaultAssignedUser"
                    >
                        Assigned to
                    </label>

                    <select
                        class="form-select"
                        id="leadImportDefaultAssignedUser"
                    >
                        <option value="">
                            Unassigned
                        </option>

                        @foreach (($options['users'] ?? []) as $user)
                            <option value="{{ $user['value'] }}">
                                {{ $user['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <label
                        class="form-label"
                        for="leadImportDefaultPriority"
                    >
                        Priority
                    </label>

                    <select
                        class="form-select"
                        id="leadImportDefaultPriority"
                    >
                        @foreach (($options['priorities'] ?? []) as $priority)
                            <option
                                value="{{ $priority['value'] }}"
                                @selected(
                                    (string) $priority['value']
                                    ===
                                    (string) ($options['default_priority'] ?? '')
                                )
                            >
                                {{ $priority['label'] }}
                            </option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div
                class="lead-import-required-defaults mt-3"
                id="leadImportRequiredDefaults"
                hidden
            >
                <div id="leadImportRequiredDefaultsFields"></div>
            </div>
        </section>

    </article>

    <footer class="lead-import-actions">
        <button
            class="btn btn-outline-dark"
            id="leadImportMappingBackBtn"
            type="button"
        >
            <i class="bi bi-arrow-left"></i>
            Back to Upload
        </button>

        <button
            class="btn btn-danger"
            id="leadImportMappingContinueBtn"
            type="button"
            disabled
        >
            Continue to Review
            <i class="bi bi-arrow-right"></i>
        </button>
    </footer>
</section>
