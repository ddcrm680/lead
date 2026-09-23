/**
 * --------------------------------------------------------------------------
 * Leads Module
 * --------------------------------------------------------------------------
 */
(() => {
    'use strict';

    let contactIndex = 1;
    let tagSelectInstance = null;

    /**
     * Fetch, inject, and display the Create Lead drawer.
     */
    async function openCreateLeadDrawer(createUrl) {
        if (!createUrl) {
            return;
        }

        try {
            const existingDrawer = $('#addLead');

            if (existingDrawer) {
                const instance = bootstrap.Offcanvas.getInstance(existingDrawer);
                instance?.dispose();
                existingDrawer.remove();
            }

            const response = await axios.get(createUrl);

            document.body.insertAdjacentHTML(
                'beforeend',
                response.data.html ?? ''
            );

            const addLeadDrawer = $('#addLead');

            if (!addLeadDrawer) {
                return;
            }

            // Initialize Tom Select on newly injected markup.
            const tagsSelect = $('#leadTags', addLeadDrawer);

            if (tagsSelect && typeof TomSelect !== 'undefined') {
                tagSelectInstance = new TomSelect(tagsSelect, {
                    plugins: ['remove_button'],
                    create: true,
                    persist: false,
                    createOnBlur: true,
                    placeholder: 'Select or type tags...',
                });
            }

            bootstrap.Offcanvas
                .getOrCreateInstance(addLeadDrawer)
                .show();

            // Destroy plugins and remove the injected drawer after closing.
            addLeadDrawer.addEventListener(
                'hidden.bs.offcanvas',
                () => {
                    if (tagSelectInstance) {
                        tagSelectInstance.destroy();
                        tagSelectInstance = null;
                    }

                    addLeadDrawer.remove();
                },
                { once: true }
            );
        } catch (error) {
            console.log(error);
            handleResponseError(error);
        }
    }

    /**
     * Handle Create Lead drawer and contact repeater actions.
     */
    document.addEventListener('click', function (event) {
        const openBtn = event.target.closest(
            '[data-bs-target="#addLead"], [data-action="create-lead"]'
        );

        if (openBtn) {
            event.preventDefault();

            const createUrl =
                openBtn.getAttribute('data-create-url')
                || openBtn.getAttribute('data-url');

            openCreateLeadDrawer(createUrl);
            return;
        }

        const addContactBtn = event.target.closest('#addLeadContactBtn');

        if (addContactBtn) {
            const contactsContainer = $('#leadContacts');

            if (!contactsContainer) {
                return;
            }

            const row = document.createElement('div');

            row.className =
                'row g-2 align-items-end lead-contact-row mt-1';

            row.setAttribute('data-contact-row', '');

            row.innerHTML = `
                <div class="col-4">
                    <label class="form-label small mb-1">Contact type *</label>
                    <select class="form-select" name="contacts[${contactIndex}][type]" data-contact-type required>
                        <option value="">Select type</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                    <span class="invalid-feedback" data-error-for="contacts.${contactIndex}.type"></span>
                </div>

                <div class="col-5">
                    <label class="form-label small mb-1">Contact value *</label>
                    <input
                        type="text"
                        class="form-control"
                        name="contacts[${contactIndex}][value]"
                        data-contact-value
                        required
                        maxlength="255"
                        placeholder="e.g. Enter value"
                    >
                    <span class="invalid-feedback" data-error-for="contacts.${contactIndex}.value"></span>
                </div>

                <div class="col-2 pb-2">
                    <div class="form-check m-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="contacts[${contactIndex}][is_primary]"
                            value="1"
                            id="primaryContact${contactIndex}"
                            data-contact-primary
                        >
                        <label class="form-check-label small" for="primaryContact${contactIndex}">
                            Primary
                        </label>
                    </div>
                </div>

                <div class="col-1 pb-1 text-center">
                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm remove-contact-btn d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px;"
                        title="Remove contact"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            contactsContainer.appendChild(row);
            contactIndex++;
            return;
        }

        const removeContactBtn = event.target.closest('.remove-contact-btn');

        if (removeContactBtn) {
            const row = removeContactBtn.closest('[data-contact-row]');
            row?.remove();
        }
    });

    /**
     * Ensure only one contact can be marked as primary.
     */
    document.addEventListener('change', function (event) {
        if (
            !event.target.matches('[data-contact-primary]')
            || !event.target.checked
        ) {
            return;
        }

        const contactsContainer = $('#leadContacts');

        if (!contactsContainer) {
            return;
        }

        $$('[data-contact-primary]', contactsContainer).forEach((checkbox) => {
            if (checkbox !== event.target) {
                checkbox.checked = false;
            }
        });
    });

    /**
     * Submit the Create Lead form.
     */
    document.addEventListener('submit', async function (event) {
        if (event.target.id !== 'addLeadForm') {
            return;
        }

        event.preventDefault();

        const addLeadForm = event.target;
        const leadSubmitBtn = $('#leadSubmitBtn', addLeadForm);

        resetValidationErrors(addLeadForm);
        showLoader(leadSubmitBtn, 'Saving...');

        const formData = new FormData(addLeadForm);

        try {
            const response = await axios.post(
                addLeadForm.action,
                formData
            );

            handleResponseSuccess(response.data);

            const addLeadDrawer = $('#addLead');

            if (addLeadDrawer) {
                bootstrap.Offcanvas
                    .getOrCreateInstance(addLeadDrawer)
                    .hide();
            }
        } catch (error) {
            handleResponseError(
                error,
                addLeadForm
            );
        } finally {
            hideLoader(leadSubmitBtn);
        }
    });

    /**
     * --------------------------------------------------------------------------
     * Lead List
     * --------------------------------------------------------------------------
     */

    const leadsPage = $('#leads');
    const leadList = $('#leadList');
    const leadSearch = $('#leadSearch');
    const leadStatusFilter = $('#leadStatusFilter');
    const leadSourceFilter = $('#leadSourceFilter');
    const listViewBtn = $('#listViewBtn');
    const compactViewBtn = $('#compactViewBtn');

    const advancedStatus = $('#advancedStatus');
    const advancedSource = $('#advancedSource');
    const advancedCity = $('#advancedCity');
    const advancedState = $('#advancedState');
    const advancedCountry = $('#advancedCountry');
    const advancedPipelineStage = $('#advancedPipelineStage');
    const advancedAgent = $('#advancedAgent');
    const advancedPriority = $('#advancedPriority');
    const advancedTags = $('#advancedTags');
    const advancedFollowUpType = $('#advancedFollowUpType');
    const advancedFollowUpStatus = $('#advancedFollowUpStatus');
    const advancedCreatedAfter = $('#advancedCreatedAfter');
    const advancedCreatedBefore = $('#advancedCreatedBefore');

    const applyAdvancedFiltersBtn = $('#applyAdvancedFilters');
    const resetAdvancedFiltersBtn = $('#resetAdvancedFilters');

    const leadListState = {
        page: 1,
        perPage: 25,
    };

    let leadSearchTimer = null;
    let leadListAbortController = null;

    /**
     * Switch between desktop list and compact lead views.
     */
    function setLeadView(view) {
        const isCompact = view === 'compact';
        const tableView = leadList?.querySelector('.table-desktop');
        const compactView = leadList?.querySelector('.mobile-records');

        listViewBtn?.classList.toggle('active', !isCompact);
        compactViewBtn?.classList.toggle('active', isCompact);

        if (isMobileLeadList()) {
            tableView?.classList.add('d-none');
            compactView?.classList.remove('d-none');
            return;
        }

        tableView?.classList.toggle('d-none', isCompact);
        compactView?.classList.toggle('d-none', !isCompact);
    }

    /**
     * Check whether the lead list is using the mobile layout.
     */
    function isMobileLeadList() {
        return window.matchMedia('(max-width: 767.98px)').matches;
    }

    const leadListMobileQuery = window.matchMedia(
        '(max-width: 767.98px)'
    );

    leadListMobileQuery.addEventListener('change', () => {
        setLeadView(
            compactViewBtn?.classList.contains('active')
                ? 'compact'
                : 'list'
        );
    });

    /**
     * Load the paginated lead list using the active filters.
     */
    async function loadLeads() {
        if (!leadsPage || !leadList) {
            return;
        }

        const dataUrl = leadsPage.dataset.leadsDataUrl;

        if (!dataUrl) {
            return;
        }

        if (leadListAbortController) {
            leadListAbortController.abort();
        }

        leadListAbortController = new AbortController();

        if (isMobileLeadList()) {
            renderSkeleton({
                element: leadList,
                type: 'card',
                count: 3,
            });
        } else {
            renderSkeleton({
                element: leadList,
                type: 'row',
                count: 3,
                columns: 9,
            });
        }

        const params = {
            page: leadListState.page,
            per_page: leadListState.perPage,
        };

        if (leadSearch?.value.trim()) {
            params.search = leadSearch.value.trim();
        }

        if (leadStatusFilter?.value) {
            params.status_id = leadStatusFilter.value;
        }

        if (leadSourceFilter?.value) {
            params.source_id = leadSourceFilter.value;
        }

        if (advancedCity?.value.trim()) {
            params.city = advancedCity.value.trim();
        }

        if (advancedState?.value.trim()) {
            params.state = advancedState.value.trim();
        }

        if (advancedCountry?.value.trim()) {
            params.country = advancedCountry.value.trim();
        }

        if (advancedPipelineStage?.value) {
            params.pipeline_stage_id = advancedPipelineStage.value;
        }

        if (advancedAgent?.value) {
            params.assigned_user_id = advancedAgent.value;
        }

        if (advancedPriority?.value) {
            params.priority = advancedPriority.value;
        }

        if (advancedTags) {
            const selectedTags = Array.from(advancedTags.selectedOptions)
                .map((option) => option.value)
                .filter(Boolean);

            if (selectedTags.length) {
                params.tag_ids = selectedTags;
            }
        }

        if (advancedFollowUpType?.value) {
            params.follow_up_type_id = advancedFollowUpType.value;
        }

        if (advancedFollowUpStatus?.value) {
            params.follow_up_status_id = advancedFollowUpStatus.value;
        }

        if (advancedCreatedAfter?.value) {
            params.created_after = advancedCreatedAfter.value;
        }

        if (advancedCreatedBefore?.value) {
            params.created_before = advancedCreatedBefore.value;
        }

        try {
            const response = await axios.get(
                dataUrl,
                {
                    params,
                    signal: leadListAbortController.signal,
                }
            );

            leadList.innerHTML = response.data.html ?? '';

            setLeadView(
                compactViewBtn?.classList.contains('active')
                    ? 'compact'
                    : 'list'
            );
        } catch (error) {
            if (axios.isCancel(error)) {
                return;
            }

            handleResponseError(error);

            leadList.innerHTML = `
                <div class="empty-state text-center">
                    <i class="bi bi-exclamation-circle"></i>
                    <h3>Unable to load leads</h3>
                    <p>Please try again.</p>
                </div>
            `;
        }
    }

    /**
     * Reset pagination and reload leads using the active filters.
     */
    function applyLeadFilters() {
        leadListState.page = 1;
        loadLeads();
    }

    /**
     * Clear all advanced filters and reload the lead list.
     */
    function resetAdvancedFilters() {
        if (advancedStatus) {
            advancedStatus.value = '';
        }

        if (advancedSource) {
            advancedSource.value = '';
        }

        if (advancedCity) {
            advancedCity.value = '';
        }

        if (advancedState) {
            advancedState.value = '';
        }

        if (advancedCountry) {
            advancedCountry.value = '';
        }

        if (advancedPipelineStage) {
            advancedPipelineStage.value = '';
        }

        if (advancedAgent) {
            advancedAgent.value = '';
        }

        if (advancedPriority) {
            advancedPriority.value = '';
        }

        if (advancedTags) {
            Array.from(advancedTags.options).forEach((option) => {
                option.selected = false;
            });
        }

        if (advancedFollowUpType) {
            advancedFollowUpType.value = '';
        }

        if (advancedFollowUpStatus) {
            advancedFollowUpStatus.value = '';
        }

        if (advancedCreatedAfter) {
            advancedCreatedAfter.value = '';
        }

        if (advancedCreatedBefore) {
            advancedCreatedBefore.value = '';
        }

        if (leadStatusFilter) {
            leadStatusFilter.value = '';
        }

        if (leadSourceFilter) {
            leadSourceFilter.value = '';
        }

        applyLeadFilters();
    }

    /**
     * --------------------------------------------------------------------------
     * Lead List Events
     * --------------------------------------------------------------------------
     */

    applyAdvancedFiltersBtn?.addEventListener(
        'click',
        applyLeadFilters
    );

    resetAdvancedFiltersBtn?.addEventListener(
        'click',
        resetAdvancedFilters
    );

    leadSearch?.addEventListener('input', function () {
        clearTimeout(leadSearchTimer);

        leadSearchTimer = setTimeout(() => {
            applyLeadFilters();
        }, 300);
    });

    leadStatusFilter?.addEventListener(
        'change',
        function () {
            if (advancedStatus) {
                advancedStatus.value = leadStatusFilter.value;
            }

            applyLeadFilters();
        }
    );

    leadSourceFilter?.addEventListener(
        'change',
        function () {
            if (advancedSource) {
                advancedSource.value = leadSourceFilter.value;
            }

            applyLeadFilters();
        }
    );

    advancedStatus?.addEventListener(
        'change',
        function () {
            if (leadStatusFilter) {
                leadStatusFilter.value = advancedStatus.value;
            }
        }
    );

    advancedSource?.addEventListener(
        'change',
        function () {
            if (leadSourceFilter) {
                leadSourceFilter.value = advancedSource.value;
            }
        }
    );

    listViewBtn?.addEventListener(
        'click',
        () => setLeadView('list')
    );

    compactViewBtn?.addEventListener(
        'click',
        () => setLeadView('compact')
    );

    leadList?.addEventListener('change', function (event) {
        if (!event.target.matches('#leadPerPage')) {
            return;
        }

        leadListState.perPage = Number(event.target.value);
        leadListState.page = 1;

        loadLeads();
    });

    leadList?.addEventListener('click', function (event) {
        const paginationButton = event.target.closest(
            '[data-lead-page]'
        );

        if (!paginationButton || paginationButton.disabled) {
            return;
        }

        const page = Number(paginationButton.dataset.leadPage);

        if (!page || page < 1) {
            return;
        }

        leadListState.page = page;

        loadLeads();
    });

    /**
     * Initial lead list load.
     */
    loadLeads();

    window.openCreateLeadDrawer = openCreateLeadDrawer;
})();
