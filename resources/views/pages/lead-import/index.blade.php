@extends('layouts.app')

@section('title', 'Import Leads')

@section('page-eyebrow', 'DATA INTAKE')

@section('page-title', 'Import Leads')

@push('css')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/modules/lead-import.css') }}"
    >
@endpush

@section('content')

    <section
        class="lead-import"
        id="leadImport"
        aria-labelledby="leadImportHeading"
        data-parse-url="{{ route('parseLeadImport') }}"
        data-prepare-url="{{ route('prepareLeadImport') }}"
        data-review-url="{{ route('reviewLeadImport') }}"
        data-store-url="{{ route('storeLeadImport') }}"


    >
        <div class="page-stack">

            <section class="page-intro">
                <div>
                    <span class="eyebrow">DATA INTAKE</span>

                    <h2 id="leadImportHeading">
                        Import Leads
                    </h2>

                    <p>
                        Upload, prepare, review and import lead data.
                    </p>
                </div>

                <div class="action-row">
                    <button
                        class="btn btn-outline-dark"
                        id="downloadLeadImportTemplateBtn"
                        type="button"
                    >
                        <i class="bi bi-file-earmark-arrow-down"></i>
                        Download template
                    </button>
                </div>
            </section>

            <nav
                class="lead-import-steps"
                aria-label="Lead import progress"
            >
                <button
                    class="lead-import-step active"
                    type="button"
                    data-import-step="1"
                    aria-current="step"
                >
                    <span class="lead-import-step-number">1</span>

                    <span class="lead-import-step-copy">
                        <strong>Upload</strong>
                        <small>Select file</small>
                    </span>
                </button>

                <button
                    class="lead-import-step"
                    type="button"
                    data-import-step="2"
                    disabled
                >
                    <span class="lead-import-step-number">2</span>

                    <span class="lead-import-step-copy">
                        <strong>Prepare</strong>
                        <small>Check data</small>
                    </span>
                </button>

                <button
                    class="lead-import-step"
                    type="button"
                    data-import-step="3"
                    disabled
                >
                    <span class="lead-import-step-number">3</span>

                    <span class="lead-import-step-copy">
                        <strong>Review</strong>
                        <small>Resolve exceptions</small>
                    </span>
                </button>

                <button
                    class="lead-import-step"
                    type="button"
                    data-import-step="4"
                    disabled
                >
                    <span class="lead-import-step-number">4</span>

                    <span class="lead-import-step-copy">
                        <strong>Import</strong>
                        <small>Confirm & results</small>
                    </span>
                </button>
            </nav>

            <div class="lead-import-content">
                <div
                    class="lead-import-panel"
                    data-import-panel="1"
                >
                    @include('pages.lead-import.components.upload')
                </div>

                <div
                    class="lead-import-panel"
                    data-import-panel="2"
                    hidden
                >
                    @include('pages.lead-import.components.mapping')
                </div>

                <div
                    class="lead-import-panel"
                    data-import-panel="3"
                    hidden
                >
                    @include('pages.lead-import.components.review')
                </div>

                <div
                    class="lead-import-panel"
                    data-import-panel="4"
                    hidden
                >
                    @include('pages.lead-import.components.import')
                </div>
            </div>

        </div>
    </section>

@endsection

@push('js')
    <script src="{{ asset('assets/js/modules/lead-import.js') }}"></script>
@endpush
