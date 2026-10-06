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

    let matchDecisions = {};
    let reviewBaseReadyCount = 0;
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
            priority,
            continueBtn,
        } = getPrepareControls();

        if (!continueBtn) {
            return;
        }

        continueBtn.disabled =
            !status?.value
            || !priority?.value;
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
     * Reset client-side Review decisions
     * after fresh Review HTML is rendered.
     */
    function resetReviewState() {
        matchDecisions = {};

        reviewBaseReadyCount =
            readReviewCount(
                '#leadImportReviewReadyCount'
            );

        syncReviewState();
    }

    /**
     * Set one potential-match decision.
     */
    function setReviewDecision(
        rowNumber,
        decision
    ) {
        if (
            !rowNumber
            || ![
                'import',
                'skip',
            ].includes(decision)
        ) {
            return;
        }

        matchDecisions[rowNumber] =
            decision;

        syncReviewState();
    }

    /**
     * Apply one decision to all match rows.
     */
    function setAllReviewDecisions(
        decision
    ) {
        const rows =
            $$(
                '[data-review-match-row]',
                reviewPanel
            );

        rows.forEach((row) => {
            const rowNumber =
                row.dataset.reviewMatchRow;

            if (!rowNumber) {
                return;
            }

            matchDecisions[rowNumber] =
                decision;
        });

        syncReviewState();
    }

    /**
     * Synchronize Review decision buttons,
     * Ready count and Continue state.
     */
    function syncReviewState() {
        if (!reviewPanel) {
            return;
        }

        const rows =
            $$(
                '[data-review-match-row]',
                reviewPanel
            );

        const decisionButtons =
            $$(
                '[data-review-match-decision]',
                reviewPanel
            );

        const readyCount =
            $('#leadImportReviewReadyCount', reviewPanel);

        const continueBtn =
            $('#leadImportReviewContinueBtn', reviewPanel);

        const issueCount =
            readReviewCount(
                '#leadImportReviewIssueCount'
            );

        let resolvedCount = 0;
        let importCount = 0;

        rows.forEach((row) => {
            const rowNumber =
                row.dataset.reviewMatchRow;

            const decision =
                matchDecisions[rowNumber];

            if (!decision) {
                return;
            }

            resolvedCount++;

            if (decision === 'import') {
                importCount++;
            }
        });

        decisionButtons.forEach((button) => {
            const rowNumber =
                button.dataset.reviewRowNumber;

            const decision =
                button.dataset.reviewMatchDecision;

            const selected =
                matchDecisions[rowNumber]
                === decision;

            button.classList.toggle(
                'btn-dark',
                selected
            );

            button.classList.toggle(
                'btn-outline-dark',
                !selected
            );

            button.setAttribute(
                'aria-pressed',
                selected
                    ? 'true'
                    : 'false'
            );
        });

        const currentReadyCount =
            reviewBaseReadyCount
            + importCount;

        if (readyCount) {
            readyCount.textContent =
                currentReadyCount.toLocaleString();
        }

        const unresolvedCount =
            rows.length
            - resolvedCount;

        if (continueBtn) {
            continueBtn.disabled =
                issueCount > 0
                || unresolvedCount > 0
                || currentReadyCount <= 0;
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

        const bulkActions =
            $('#leadImportReviewBulkActions', reviewPanel);

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

        if (bulkActions) {
            bulkActions.hidden =
                filter !== 'matches';
        }
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

            match_decisions:
                {
                    ...matchDecisions,
                },
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
                'Import complete',

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
     * Render the completed Import result.
     */
    function renderImportResult(
        result,
        message
    ) {
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
            [
                '#leadImportResultRowsProcessed',
                result.processed_count,
            ],
            [
                '#leadImportResultMatchesReviewed',
                result.match_count,
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

        const fileName =
            $('#leadImportResultFileName', importPanel);

        if (fileName) {
            fileName.textContent =
                result.original_name
                || '—';
        }

        const resultMessage =
            $('#leadImportImportResultMessage', importPanel);

        if (resultMessage) {
            resultMessage.textContent =
                message
                || 'Your Lead import finished successfully.';
        }

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
                    {
                        match_decisions:
                            importPlan.match_decisions
                            ?? {},
                    }
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

            renderImportResult(
                result,
                response.data?.message
            );

            showImportState('results');

            showNotification({
                type: 'success',
                title: 'Import complete',
                html:
                    response.data?.message
                    ?? 'Lead import completed successfully.',
            });

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

                const reviewDecisionBtn =
                    event.target.closest(
                        '[data-review-match-decision]'
                    );

                if (reviewDecisionBtn) {
                    setReviewDecision(
                        reviewDecisionBtn.dataset
                            .reviewRowNumber,
                        reviewDecisionBtn.dataset
                            .reviewMatchDecision
                    );

                    return;
                }

                const importAllBtn =
                    event.target.closest(
                        '#leadImportMatchImportAllBtn'
                    );

                if (importAllBtn) {
                    setAllReviewDecisions(
                        'import'
                    );

                    return;
                }

                const skipAllBtn =
                    event.target.closest(
                        '#leadImportMatchSkipAllBtn'
                    );

                if (skipAllBtn) {
                    setAllReviewDecisions(
                        'skip'
                    );

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
