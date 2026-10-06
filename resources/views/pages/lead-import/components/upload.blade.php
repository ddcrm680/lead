<section
    class="lead-import-upload"
    aria-labelledby="leadImportUploadHeading"
>
    <article class="panel lead-import-card">

        <header class="lead-import-section-head">
            <div>
                <h3
                    class="lead-import-title"
                    id="leadImportUploadHeading"
                >
                    Upload your file
                </h3>

                <p class="lead-import-description">
                    Upload a CSV or XLSX file containing your leads. We’ll automatically understand the columns and prepare your data.
                </p>
            </div>
        </header>

        <div
            class="lead-import-dropzone"
            id="leadImportDropzone"
        >
            <input
                id="leadImportFileInput"
                type="file"
                accept=".xlsx,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"
                hidden
            >

            <span
                class="lead-import-dropzone-icon"
                aria-hidden="true"
            >
                <i class="bi bi-cloud-arrow-up"></i>
            </span>

            <h4>
                Drag and drop your file here
            </h4>

            <p>
                or choose it from your device
            </p>

            <button
                class="btn btn-outline-dark"
                id="leadImportBrowseBtn"
                type="button"
            >
                <i class="bi bi-folder2-open"></i>
                Choose file
            </button>

            <small>
                Supports CSV and XLSX files.
            </small>
        </div>

        <div
            class="lead-import-file mt-3"
            id="leadImportSelectedFile"
            hidden
        >
            <span
                class="lead-import-file-icon"
                aria-hidden="true"
            >
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </span>

            <div class="flex-grow-1 min-w-0">
                <strong id="leadImportSelectedFileName">
                    —
                </strong>

                <small id="leadImportSelectedFileMeta">
                    —
                </small>
            </div>

            <button
                class="btn btn-sm btn-outline-dark flex-shrink-0"
                id="leadImportRemoveFileBtn"
                type="button"
                aria-label="Remove selected file"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <aside class="lead-import-tip mt-3">
            <span
                class="lead-import-tip-icon"
                aria-hidden="true"
            >
                <i class="bi bi-info-circle-fill"></i>
            </span>

            <div>
                <strong>
                    Tips for best results
                </strong>

                <ul>
                    <li>Use a header row with familiar column names such as Name, Email and Phone.</li>
                    <li>You can include extra columns — we’ll keep useful additional data with each Lead.</li>
                    <li>Potential duplicate Leads will be detected during Review.</li>
                </ul>
            </div>
        </aside>

    </article>

    <footer class="lead-import-actions">
        <a
            class="btn btn-outline-dark"
            href="{{ route('leads') }}"
        >
            <i class="bi bi-arrow-left"></i>
            Back to Leads
        </a>

        <button
            class="btn btn-danger"
            id="leadImportUploadContinueBtn"
            type="button"
            disabled
        >
            Analyze file
            <i class="bi bi-arrow-right"></i>
        </button>
    </footer>
</section>
