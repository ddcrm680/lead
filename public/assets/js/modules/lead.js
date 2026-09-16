/**
 * --------------------------------------------------------------------------
 * Leads Module (Dynamic Injection & Global Delegation)
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

            // Initialize Tom Select on newly injected markup
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

            // Show drawer
            bootstrap.Offcanvas
                .getOrCreateInstance(addLeadDrawer)
                .show();

            // Self-cleaning on close: purge plugins and remove element from DOM
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
     * Global Click Delegation: Open Drawer & Contact Repeater Actions
     */
    document.addEventListener('click', function (event) {
        // 1. Open Create Lead Drawer
        const openBtn = event.target.closest('[data-bs-target="#addLead"], [data-action="create-lead"]');
        if (openBtn) {
            event.preventDefault();
            const createUrl = openBtn.getAttribute('data-create-url') || openBtn.getAttribute('data-url');
            openCreateLeadDrawer(createUrl);
            return;
        }

        // 2. Add Dynamic Contact Row
        const addContactBtn = event.target.closest('#addLeadContactBtn');
        if (addContactBtn) {
            const contactsContainer = $('#leadContacts');
            if (!contactsContainer) return;

            const row = document.createElement('div');
            row.className = 'row g-2 align-items-end lead-contact-row mt-1';
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
                        <label class="form-check-label small" for="primaryContact${contactIndex}">Primary</label>
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

        // 3. Remove Dynamic Contact Row
        const removeContactBtn = event.target.closest('.remove-contact-btn');
        if (removeContactBtn) {
            const row = removeContactBtn.closest('[data-contact-row]');
            row?.remove();
        }
    });

    /**
     * Global Change Delegation: Single Primary Contact Radio Behavior
     */
    document.addEventListener('change', function (event) {
        if (event.target.matches('[data-contact-primary]') && event.target.checked) {
            const contactsContainer = $('#leadContacts');
            if (!contactsContainer) return;

            $$('[data-contact-primary]', contactsContainer).forEach((checkbox) => {
                if (checkbox !== event.target) {
                    checkbox.checked = false;
                }
            });
        }
    });

    /**
     * Global Submit Delegation: Save Lead (Matches userEditForm Submit)
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

    window.openCreateLeadDrawer = openCreateLeadDrawer;
})();