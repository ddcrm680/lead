/**
 * --------------------------------------------------------------------------
 * Lead Import
 * --------------------------------------------------------------------------
 *
 * Step 1:
 * - Select / drop CSV or XLSX
 * - Upload and parse
 * - Render server-generated Prepare Blade
 * - Open Step 2
 */

(() => {
    'use strict';

    const leadImportPage = $('#leadImport');

    if (!leadImportPage) {
        return;
    }

    const parseUrl =
        leadImportPage.dataset.parseUrl;

    const prepareUrl =
        leadImportPage.dataset.prepareUrl;

    const reviewUrl =
        leadImportPage.dataset.reviewUrl;

    const storeUrl =
        leadImportPage.dataset.storeUrl;

    const leadsUrl =
        leadImportPage.dataset.leadsUrl;

    const fileInput =
        $('#leadImportFileInput', leadImportPage);

    const browseBtn =
        $('#leadImportBrowseBtn', leadImportPage);

    const dropzone =
        $('#leadImportDropzone', leadImportPage);

    const selectedFileBox =
        $('#leadImportSelectedFile', leadImportPage);

    const selectedFileName =
        $('#leadImportSelectedFileName', leadImportPage);

    const selectedFileMeta =
        $('#leadImportSelectedFileMeta', leadImportPage);

    const removeFileBtn =
        $('#leadImportRemoveFileBtn', leadImportPage);

    const uploadContinueBtn =
        $('#leadImportUploadContinueBtn', leadImportPage);

    const preparePanel =
        $('[data-import-panel="2"]', leadImportPage);

    const reviewPanel =
        $('[data-import-panel="3"]', leadImportPage);

    const importPanel =
        $('[data-import-panel="4"]', leadImportPage);

    const steps =
        $$('[data-import-step]', leadImportPage);

    const panels =
        $$('[data-import-panel]', leadImportPage);

    let selectedFile = null;
    let importPlan = null;

    /**
     * Display one workflow step.
     */
    function showStep(stepNumber) {
        panels.forEach((panel) => {
            panel.hidden =
                Number(panel.dataset.importPanel)
                !== stepNumber;
        });

        steps.forEach((step) => {
            const number =
                Number(step.dataset.importStep);

            step.classList.toggle(
                'active',
                number === stepNumber
            );

            step.classList.toggle(
                'completed',
                number < stepNumber
            );

            if (number === stepNumber) {
                step.setAttribute(
                    'aria-current',
                    'step'
                );
            } else {
                step.removeAttribute(
                    'aria-current'
                );
            }
        });
    }

    /**
     * Format selected file size for display.
     */
    function formatFileSize(bytes) {
        if (!Number.isFinite(bytes) || bytes <= 0) {
            return '0 KB';
        }

        if (bytes < 1024 * 1024) {
            return `${Math.ceil(bytes / 1024)} KB`;
        }

        return `${(
            bytes
            / 1024
            / 1024
        ).toFixed(1)} MB`;
    }

    /**
     * Check supported spreadsheet extension.
     */
    function isSupportedFile(file) {
        const extension =
            file?.name
                ?.split('.')
                ?.pop()
                ?.toLowerCase();

        return [
            'csv',
            'xlsx',
        ].includes(extension);
    }

    /**
     * Store and display the selected file.
     */
    function setSelectedFile(file) {
        if (!file) {
            return;
        }

        if (!isSupportedFile(file)) {
            clearSelectedFile();

            showNotification({
                type: 'error',
                title: 'Unsupported file',
                html: 'Choose a CSV or XLSX file.',
            });

            return;
        }

        selectedFile = file;

        if (selectedFileName) {
            selectedFileName.textContent =
                file.name;
        }

        if (selectedFileMeta) {
            const extension =
                file.name
                    .split('.')
                    .pop()
                    ?.toUpperCase()
                ?? '';

            selectedFileMeta.textContent =
                `${extension} · ${formatFileSize(file.size)}`;
        }

        if (selectedFileBox) {
            selectedFileBox.hidden = false;
        }

        if (uploadContinueBtn) {
            uploadContinueBtn.disabled = false;
        }
    }

    /**
     * Clear selected upload.
     */
    function clearSelectedFile() {
        selectedFile = null;

        if (fileInput) {
            fileInput.value = '';
        }

        if (selectedFileBox) {
            selectedFileBox.hidden = true;
        }

        if (selectedFileName) {
            selectedFileName.textContent = '—';
        }

        if (selectedFileMeta) {
            selectedFileMeta.textContent = '—';
        }

        if (uploadContinueBtn) {
            uploadContinueBtn.disabled = true;
        }
    }

    /**
     * Upload and prepare the file.
     */
    async function analyzeFile() {
        if (
            !selectedFile
            || !parseUrl
            || !preparePanel
            || !uploadContinueBtn
        ) {
            return;
        }

        const formData =
            new FormData();

        formData.append(
            'file',
            selectedFile
        );

        showLoader(
            uploadContinueBtn,
            'Analyzing...'
        );

        try {
            const response =
                await axios.post(
                    parseUrl,
                    formData
                );

            const html =
                response.data?.html
                ?? '';

            if (!html.trim()) {
                showNotification({
                    type: 'error',
                    title: 'Unable to prepare file',
                    html: 'The server did not return the Prepare step.',
                });

                return;
            }

            preparePanel.innerHTML =
                html;

            syncPrepareState();

            const prepareStep =
                $('[data-import-step="2"]', leadImportPage);

            if (prepareStep) {
                prepareStep.disabled = false;
            }

            showStep(2);

        } catch (error) {
            handleResponseError(error);

        } finally {
            hideLoader(
                uploadContinueBtn
            );
        }
    }


    /**
     * Get the current Prepare controls.
     */
    function getPrepareControls() {
        return {
            status:
                $('#leadImportDefaultStatus', leadImportPage),

            source:
                $('#leadImportDefaultSource', leadImportPage),

            assignedUser:
                $('#leadImportDefaultAssignedUser', leadImportPage),

            priority:
                $('#leadImportDefaultPriority', leadImportPage),

            continueBtn:
                $('#leadImportMappingContinueBtn', leadImportPage),
        };
    }

    /**
     * Enable Continue only when required defaults exist.
     */
    function syncPrepareState() {
        const {
            status,
            source,
            priority,
            continueBtn,
        } = getPrepareControls();

        if (!continueBtn) {
            return;
        }

        const sourceMapped =
            source?.dataset.sourceMapped === 'true';

        continueBtn.disabled =
            !status?.value
            || !priority?.value
            || (
                !sourceMapped
                && !source?.value
            );
    }

    /**
     * Build the small Prepare request payload.
     */
    function buildPreparePayload() {
        const {
            status,
            source,
            assignedUser,
            priority,
        } = getPrepareControls();

        return {
            status_id:
                Number(status.value),

            source_id:
                source?.value
                    ? Number(source.value)
                    : null,

            assigned_user_id:
                assignedUser?.value
                    ? Number(assignedUser.value)
                    : null,

            priority:
                Number(priority.value),
        };
    }

   /**
     * Save Prepare defaults and build the Review step.
     */
    async function savePrepareDefaults() {
        const {
            status,
            priority,
            continueBtn,
        } = getPrepareControls();

        if (
            !prepareUrl
            || !reviewUrl
            || !reviewPanel
            || !continueBtn
        ) {
            return;
        }

        if (
            !status?.value
            || !priority?.value
        ) {
            syncPrepareState();

            return;
        }

        showLoader(
            continueBtn,
            'Reviewing...'
        );

        try {
            await axios.post(
                prepareUrl,
                buildPreparePayload()
            );

            const response =
                await axios.post(
                    reviewUrl
                );

            const html =
                response.data?.html
                ?? '';

            if (!html.trim()) {
                showNotification({
                    type: 'error',
                    title: 'Unable to review import',
                    html: 'The server did not return the Review step.',
                });

                return;
            }

            reviewPanel.innerHTML =
                html;

            resetReviewState();
            const activeReviewTab = $('[data-review-filter].active',reviewPanel);

            showReviewView(activeReviewTab?.dataset.reviewFilter?? 'sample');

            const reviewStep =
                $('[data-import-step="3"]', leadImportPage);

            if (reviewStep) {
                reviewStep.disabled = false;
            }

            showStep(3);

            showNotification({
                type: 'success',
                title: 'Review ready',
                html:
                    response.data?.message
                    ?? 'Lead import reviewed successfully.',
            });

        } catch (error) {
            handleResponseError(error);

        } finally {
            hideLoader(
                continueBtn
            );

            syncPrepareState();
        }
    }

    /**
     * Read a numeric Review count.
     */
    function readReviewCount(selector) {
        const element =
            $(selector, reviewPanel);

        if (!element) {
            return 0;
        }

        return Number.parseInt(
            element.textContent
                .replace(/[^\d]/g, ''),
            10
        ) || 0;
    }

    /**
     * Synchronize Review state after fresh
     * Review HTML is rendered.
     */
    function resetReviewState() {
        syncReviewState();
    }

    /**
     * Synchronize Review counts and
     * Continue state.
     */
    function syncReviewState() {
        if (!reviewPanel) {
            return;
        }

        const continueBtn =
            $('#leadImportReviewContinueBtn', reviewPanel);

        const readyCount =
            readReviewCount(
                '#leadImportReviewReadyCount'
            );

        const issueCount =
            readReviewCount(
                '#leadImportReviewIssueCount'
            );

        if (continueBtn) {
            continueBtn.disabled =
                issueCount > 0
                || readyCount <= 0;
        }
    }

    /**
     * Display one Review result view.
     */
    function showReviewView(filter) {
        if (
            !reviewPanel
            || ![
                'matches',
                'issues',
                'sample',
            ].includes(filter)
        ) {
            return;
        }

        const tabs =
            $$(
                '[data-review-filter]',
                reviewPanel
            );

        const views =
            $$(
                '[data-review-view]',
                reviewPanel
            );

        tabs.forEach((tab) => {
            tab.classList.toggle(
                'active',
                tab.dataset.reviewFilter
                    === filter
            );
        });

        views.forEach((view) => {
            view.hidden =
                view.dataset.reviewView
                !== filter;
        });
    }

    /**
     * Build the current client-side Import plan.
     */
    function buildImportPlan() {
        const totalCount =
            readReviewCount(
                '#leadImportReviewStateText'
            );

        const toImportCount =
            readReviewCount(
                '#leadImportReviewReadyCount'
            );

        const matchCount =
            readReviewCount(
                '#leadImportReviewMatchCount'
            );

        return {
            total_count:
                totalCount,

            to_import_count:
                toImportCount,

            skipped_count:
                Math.max(
                    totalCount - toImportCount,
                    0
                ),

            match_count:
                matchCount,
        };
    }

    /**
     * Render the Import confirmation plan.
     */
    function renderImportPlan(plan) {
        if (
            !importPanel
            || !plan
        ) {
            return;
        }

        const readyCount =
            $('#leadImportImportReadyCount', importPanel);

        const matchCount =
            $('#leadImportImportMatchCount', importPanel);

        const skippedCount =
            $('#leadImportImportSkippedCount', importPanel);

        const totalCount =
            $('#leadImportImportTotalCount', importPanel);

        if (readyCount) {
            readyCount.textContent =
                plan.to_import_count
                    .toLocaleString();
        }

        if (matchCount) {
            matchCount.textContent =
                plan.match_count
                    .toLocaleString();
        }

        if (skippedCount) {
            skippedCount.textContent =
                plan.skipped_count
                    .toLocaleString();
        }

        if (totalCount) {
            totalCount.textContent =
                plan.total_count
                    .toLocaleString();
        }

        const startBtn =
            $('#leadImportStartBtn', importPanel);

        if (startBtn) {
            startBtn.disabled =
                !storeUrl
                || plan.to_import_count <= 0;
        }
    }

    /**
     * Display one Import execution state.
     */
    function showImportState(state) {
        if (!importPanel) {
            return;
        }

        const states = {
            ready:
                '#leadImportImportReady',

            running:
                '#leadImportImportRunning',

            results:
                '#leadImportImportResults',

            error:
                '#leadImportImportError',
        };

        Object.entries(states)
            .forEach(([name, selector]) => {
                const element =
                    $(selector, importPanel);

                if (element) {
                    element.hidden =
                        name !== state;
                }
            });

        const stateText =
            $('#leadImportImportStateText', importPanel);

        const labels = {
            ready:
                'Ready to import',

            running:
                'Importing Leads…',

            results:
                'Import completed with issues',

            error:
                'Import failed',
        };

        if (stateText) {
            stateText.textContent =
                labels[state]
                ?? '';
        }

        const backBtn =
            $('#leadImportImportBackBtn', importPanel);

        const startBtn =
            $('#leadImportStartBtn', importPanel);

        if (backBtn) {
            /*
             * Do not leave Step 4 while the request
             * is running or after the session has
             * been consumed successfully.
             */
            backBtn.disabled =
                state === 'running'
                || state === 'results';
        }

        if (startBtn) {
            if (
                state === 'running'
                || state === 'results'
            ) {
                startBtn.disabled = true;

                return;
            }

            startBtn.disabled =
                !storeUrl
                || !importPlan
                || importPlan.to_import_count <= 0;
        }
    }

   /**
     * Render a partial Import result when
     * one or more rows could not be imported.
     */
    function renderImportResult(result) {
        if (
            !importPanel
            || !result
        ) {
            return;
        }

        const values = [
            [
                '#leadImportResultProcessedCount',
                result.processed_count,
            ],
            [
                '#leadImportResultCreatedCount',
                result.created_count,
            ],
            [
                '#leadImportResultSkippedCount',
                result.skipped_count,
            ],
            [
                '#leadImportResultFailedCount',
                result.failed_count,
            ],
        ];

        values.forEach(
            ([selector, value]) => {
                const element =
                    $(selector, importPanel);

                if (element) {
                    element.textContent =
                        Number(value ?? 0)
                            .toLocaleString();
                }
            }
        );

        const failures =
            Array.isArray(result.failures)
                ? result.failures
                : [];

        const failuresBox =
            $('#leadImportImportFailures', importPanel);

        const failureBody =
            $('#leadImportResultFailureBody', importPanel);

        if (failureBody) {
            failureBody.replaceChildren();

            failures.forEach((failure) => {
                const row =
                    document.createElement('tr');

                const rowNumber =
                    document.createElement('td');

                const leadName =
                    document.createElement('td');

                const reason =
                    document.createElement('td');

                rowNumber.textContent =
                    failure.row_number
                    ?? '—';

                leadName.textContent =
                    failure.display_name
                    || 'Lead';

                reason.textContent =
                    failure.reason
                    || 'The Lead could not be imported.';

                row.append(
                    rowNumber,
                    leadName,
                    reason
                );

                failureBody.appendChild(row);
            });
        }

        if (failuresBox) {
            failuresBox.hidden =
                failures.length === 0;
        }
    }


    /**
     * Show the completed Import summary and
     * continue to the Leads list.
     */
    async function showImportSuccess(result) {
        const processedCount =
            Number(
                result.processed_count
                ?? 0
            ).toLocaleString();

        const createdCount =
            Number(
                result.created_count
                ?? 0
            ).toLocaleString();

        const skippedCount =
            Number(
                result.skipped_count
                ?? 0
            ).toLocaleString();

        const failedCount =
            Number(
                result.failed_count
                ?? 0
            ).toLocaleString();

        const runningState =
            $('#leadImportImportRunning', importPanel);

        if (runningState) {
            runningState.hidden = true;
        }

        const stateText =
            $('#leadImportImportStateText', importPanel);

        if (stateText) {
            stateText.textContent =
                'Import complete';
        }

        const modalResult =
        await Swal.fire({
            icon: 'success',
            title: 'Import complete',

            html: `
                <div class="text-start">
                    <p class="text-muted mb-3">
                        Your Lead import completed successfully.
                    </p>

                    <div class="d-grid gap-2">
                        <div class="d-flex justify-content-between">
                            <span>Processed</span>
                            <strong>${processedCount}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Created</span>
                            <strong>${createdCount}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Skipped</span>
                            <strong>${skippedCount}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Failed</span>
                            <strong>${failedCount}</strong>
                        </div>
                    </div>
                </div>
            `,

            confirmButtonText: 'View Leads',
            cancelButtonText: 'Import Another',

            showCancelButton: true,
            reverseButtons: true,

            confirmButtonColor: '#ef1b23',

            allowOutsideClick: false,
            allowEscapeKey: false,
        });


        if (modalResult.isConfirmed) {
            if (leadsUrl) {
                window.location.href =
                    leadsUrl;

                return;
            }

            window.location.reload();

            return;
        }

        if (
            modalResult.dismiss
            === Swal.DismissReason.cancel
        ) {
            window.location.reload();
        }
    }

    /**
     * Persist the reviewed Import plan.
     */
    async function startLeadImport() {
        if (
            !storeUrl
            || !importPanel
            || !importPlan
            || importPlan.to_import_count <= 0
        ) {
            return;
        }

        const startBtn =
            $('#leadImportStartBtn', importPanel);

        if (
            !startBtn
            || startBtn.disabled
        ) {
            return;
        }

        showImportState('running');

        try {
            const response =
                await axios.post(
                    storeUrl,
                    {}
                );

            const result =
                response.data?.result;

            if (
                !result
                || typeof result !== 'object'
            ) {
                throw new Error(
                    'The server did not return an Import result.'
                );
            }

            const failedCount =
                Number(
                    result.failed_count
                    ?? 0
                );

            if (failedCount > 0) {
                    renderImportResult(
                        result
                    );

                    showImportState(
                        'results'
                    );

                    return;
                }

            await showImportSuccess(
                result
            );

        } catch (error) {
            const errorMessage =
                $('#leadImportImportErrorMessage', importPanel);

            if (errorMessage) {
                errorMessage.textContent =
                    error.response?.data?.message
                    ?? 'The Lead import could not be completed. Review the error and try again.';
            }

            showImportState('error');

            handleResponseError(error);
        }
    }

    /**
     * Open Step 4 with the current reviewed plan.
     */
    function openImportStep() {
        if (!importPanel) {
            return;
        }

        importPlan =
            buildImportPlan();

        renderImportPlan(
            importPlan
        );

        const importStep =
            $('[data-import-step="4"]', leadImportPage);

        if (importStep) {
            importStep.disabled = false;
        }

        showImportState('ready');

        showStep(4);
    }


    /**
     * Browse for file.
     */
    browseBtn?.addEventListener(
        'click',
        () => {
            fileInput?.click();
        }
    );

    /**
     * File picker selection.
     */
    fileInput?.addEventListener(
        'change',
        () => {
            setSelectedFile(
                fileInput.files?.[0]
            );
        }
    );

    /**
     * Remove file.
     */
    removeFileBtn?.addEventListener(
        'click',
        clearSelectedFile
    );

    /**
     * Drag state.
     */
    dropzone?.addEventListener(
        'dragover',
        (event) => {
            event.preventDefault();

            dropzone.classList.add(
                'is-dragging'
            );
        }
    );

    dropzone?.addEventListener(
        'dragleave',
        () => {
            dropzone.classList.remove(
                'is-dragging'
            );
        }
    );

    /**
     * Dropped file.
     */
    dropzone?.addEventListener(
        'drop',
        (event) => {
            event.preventDefault();

            dropzone.classList.remove(
                'is-dragging'
            );

            setSelectedFile(
                event.dataTransfer
                    ?.files?.[0]
            );
        }
    );

    /**
     * Analyze upload.
     */
    uploadContinueBtn?.addEventListener(
        'click',
        analyzeFile
    );

    /**
     * Prepare markup is injected dynamically,
     * so use delegation for its Back / Change buttons.
     */
    leadImportPage.addEventListener(
        'click',
        (event) => {

                const reviewFilterBtn =
                    event.target.closest(
                        '[data-review-filter]'
                    );

                if (reviewFilterBtn) {
                    showReviewView(
                        reviewFilterBtn.dataset
                            .reviewFilter
                    );

                    return;
                }

                const reviewBackBtn =
                    event.target.closest(
                        '#leadImportReviewBackBtn'
                    );

                if (reviewBackBtn) {
                    showStep(2);

                    return;
                }

                const reviewContinueBtn =
                    event.target.closest(
                        '#leadImportReviewContinueBtn'
                    );

                if (reviewContinueBtn) {
                    if (reviewContinueBtn.disabled) {
                        return;
                    }

                    openImportStep();

                    return;
                }

                const importBackBtn =
                    event.target.closest(
                        '#leadImportImportBackBtn'
                    );

                if (importBackBtn) {
                    showStep(3);

                    return;
                }

                const startImportBtn =
                    event.target.closest(
                        '#leadImportStartBtn'
                    );

                if (startImportBtn) {
                    startLeadImport();

                    return;
                }

             const continueBtn =
                event.target.closest(
                    '#leadImportMappingContinueBtn'
                );

            if (continueBtn) {
                savePrepareDefaults();

                return;
            }

            const backBtn =
                event.target.closest(
                    '#leadImportMappingBackBtn'
                );

            if (backBtn) {
                showStep(1);

                return;
            }

            const changeFileBtn =
                event.target.closest(
                    '#leadImportChangeFileBtn'
                );

            if (changeFileBtn) {
                clearSelectedFile();
                showStep(1);
                fileInput?.click();
            }
        }
    );

    /**
     * Prepare defaults are rendered dynamically,
     * so listen for changes through the page root.
     */
    leadImportPage.addEventListener(
        'change',
        (event) => {
            if (
                event.target.matches(
                    '#leadImportDefaultStatus, '
                    + '#leadImportDefaultSource, '
                    + '#leadImportDefaultAssignedUser, '
                    + '#leadImportDefaultPriority'
                )
            ) {
                syncPrepareState();
            }
        }
    );
})();
