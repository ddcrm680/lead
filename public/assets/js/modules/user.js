/**
 * ----------------------------------------
 * Users Module
 * ----------------------------------------
 *
 * Handles:
 * - User data loading
 * - Search and filters
 * - Pagination
 * - View user
 * - Edit user
 * - Create / update user
 * - User status toggle
 * - User deletion
 * - User form reset
 * - User avatar validation
 */

(() => {
    'use strict';

    /**
     * ----------------------------------------
     * Routes
     * ----------------------------------------
     */

    const routes = {
        store: '/users/store',
        view: (id) => `/users/${id}/view`,
        edit: (id) => `/users/${id}/edit`,
        update: (id) => `/users/${id}/update`,
        data: '/users/data',
        export: '/users/export',
        status: (id) => `/users/${id}/status`,
        delete: (id) => `/users/${id}/delete`,
    };

    /**
     * ----------------------------------------
     * State
     * ----------------------------------------
     */

    const state = {
        page: 1,
        perPage: 25,
    };

    let currentAbortController = null;
    let searchTimer = null;

    /**
     * ----------------------------------------
     * DOM Elements
     * ----------------------------------------
     */

    const userForm = $('#userFormElement');
    const userSubmitBtn = $('#userSubmitBtn');
    const userGrid = $('#userGrid');
    const userSearch = $('#userSearch');
    const userRoleFilter = $('#userRoleFilter');
    const userStatusFilter = $('#userStatusFilter');
    const userExportBtn = $('#exportUsersBtn');

    /**
     * ----------------------------------------
     * Load Users
     * ----------------------------------------
     */

    async function loadUsers() {
        if (!userGrid) {
            return;
        }

        if (currentAbortController) {
            currentAbortController.abort();
        }

        currentAbortController = new AbortController();

        renderSkeleton({
            element: userGrid,
            type: 'card',
            count: 3,
        });

        const params = {
            page: state.page,
            per_page: state.perPage,
        };

        if (userSearch?.value.trim()) {
            params.search = userSearch.value.trim();
        }

        if (userRoleFilter?.value) {
            params.role_id = userRoleFilter.value;
        }

        if (userStatusFilter?.value !== '') {
            params.status = userStatusFilter.value;
        }

        try {
            const response = await axios.get(
                routes.data,
                {
                    params,
                    signal: currentAbortController.signal,
                }
            );

            userGrid.innerHTML = response.data.html ?? '';

        } catch (error) {
            if (axios.isCancel(error)) {
                return;
            }

            handleResponseError(error);

            userGrid.innerHTML = `
                <div class="empty-state text-center">

                    <i class="bi bi-exclamation-circle"></i>

                    <h3>Unable to load users</h3>

                    <p>
                        Please try again.
                    </p>

                </div>
            `;
        }
    }

    /**
     * ----------------------------------------
     * Apply Filters
     * ----------------------------------------
     */

    function applyFilters() {
        state.page = 1;

        loadUsers();
    }

    /**
     * ----------------------------------------
     * Export Users
     * ----------------------------------------
     */

    async function exportUsers(format = 'csv') {
        const params = {
            format,
        };

        if (userSearch?.value.trim()) {
            params.search = userSearch.value.trim();
        }

        if (userRoleFilter?.value) {
            params.role_id = userRoleFilter.value;
        }

        if (userStatusFilter?.value !== '') {
            params.status = userStatusFilter.value;
        }

        try {
            showLoader(userExportBtn, 'Exporting...');

            const response = await axios.get(
                routes.export,
                {
                    params,
                    responseType: 'blob',
                }
            );

            const contentDisposition =
                response.headers['content-disposition'];

            let filename = `users.${format}`;

            const filenameMatch =
                contentDisposition?.match(
                    /filename="?([^"]+)"?/
                );

            if (filenameMatch?.[1]) {
                filename = filenameMatch[1];
            }

            const blob = new Blob(
                [response.data],
                {
                    type:
                        response.headers['content-type']
                        || 'application/octet-stream',
                }
            );

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = filename;

            document.body.appendChild(link);
            link.click();
            link.remove();

            window.URL.revokeObjectURL(url);

        } catch (error) {
            handleResponseError(error);
        } finally {
            hideLoader(userExportBtn);
        }
    }

    /**
     * Show export format selector.
     */
    async function showExportFormatSelector() {
        const result = await Swal.fire({
            title: 'Export Users',
            text: 'CSV, Excel, and ODS support large exports. PDF is limited to 500 records.',
            input: 'select',
            inputOptions: {
                csv: 'CSV',
                xlsx: 'Excel (.xlsx)',
                ods: 'OpenDocument (.ods)',
                pdf: 'PDF — up to 500 records',
            },
            inputValue: 'csv',
            showCancelButton: true,
            confirmButtonText: 'Export',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please select an export format.';
                }

                return undefined;
            },
        });

        if (!result.isConfirmed || !result.value) {
            return;
        }

        await exportUsers(result.value);
    }

    userExportBtn?.addEventListener(
        'click',
        showExportFormatSelector
    );

    /**
     * ----------------------------------------
     * Search
     * ----------------------------------------
     */

    userSearch?.addEventListener('input', function () {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            applyFilters();
        }, 300);
    });

    /**
     * ----------------------------------------
     * Role Filter
     * ----------------------------------------
     */

    userRoleFilter?.addEventListener(
        'change',
        applyFilters
    );

    /**
     * ----------------------------------------
     * Status Filter
     * ----------------------------------------
     */

    userStatusFilter?.addEventListener(
        'change',
        applyFilters
    );

    /**
     * ----------------------------------------
     * Per Page
     * ----------------------------------------
     */
    userGrid?.addEventListener('change', function (event) {
        if (!event.target.matches('#userPerPage')) {
            return;
        }

        state.perPage = Number(event.target.value);
        state.page = 1;

        loadUsers();
    });

    /**
     * ----------------------------------------
     * Pagination
     * ----------------------------------------
     */

    userGrid?.addEventListener('click', function (event) {
        const paginationButton = event.target.closest(
            '[data-user-page]'
        );

        if (!paginationButton || paginationButton.disabled) {
            return;
        }

        const page = Number(
            paginationButton.dataset.userPage
        );

        if (!page || page < 1) {
            return;
        }

        state.page = page;

        loadUsers();
    });

    /**
     * ----------------------------------------
     * View User
     * ----------------------------------------
     */

    async function viewUser(userId) {
        if (!userId) {
            return;
        }

        try {
            const response = await axios.get(
                routes.view(userId)
            );

            const existingDrawer = $('#userDetail');

            if (existingDrawer) {
                existingDrawer.remove();
            }

            document.body.insertAdjacentHTML(
                'beforeend',
                response.data.html ?? ''
            );

            const userDetail = $('#userDetail');

            if (!userDetail) {
                return;
            }

            bootstrap.Offcanvas
                .getOrCreateInstance(userDetail)
                .show();

            userDetail.addEventListener(
                'hidden.bs.offcanvas',
                () => {
                    userDetail.remove();
                },
                { once: true }
            );

        } catch (error) {
            handleResponseError(error);
        }
    }

    /**
     * ----------------------------------------
     * Edit User
     * ----------------------------------------
     */

    async function editUser(userId) {
        if (!userId) {
            return;
        }

        try {
            const response = await axios.get(
                routes.edit(userId)
            );

            const existingDrawer = $('#userDetail');

            if (existingDrawer) {
                const instance =
                    bootstrap.Offcanvas.getInstance(existingDrawer);

                instance?.hide();
            }

            const existingForm = $('#userEdit');

            if (existingForm) {
                const instance =
                    bootstrap.Offcanvas.getInstance(existingForm);

                instance?.dispose();

                existingForm.remove();
            }

            document.body.insertAdjacentHTML(
                'beforeend',
                response.data.html ?? ''
            );

            const userEdit = $('#userEdit');

            if (!userEdit) {
                return;
            }

            bootstrap.Offcanvas
                .getOrCreateInstance(userEdit)
                .show();

            userEdit.addEventListener(
                'hidden.bs.offcanvas',
                () => {
                    userEdit.remove();
                },
                { once: true }
            );

        } catch (error) {
            handleResponseError(error);
        }
    }
    /**
     * ----------------------------------------
     * Toggle User Status
     * ----------------------------------------
     */

    async function toggleUserStatus(userId) {
        if (!userId) {
            return;
        }

        const result = await Swal.fire({
            title: 'Change account status?',
            text: 'Are you sure you want to change this user\'s account status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it',
            cancelButtonText: 'Cancel',
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await axios.patch(
                routes.status(userId)
            );

            handleResponseSuccess(response.data);

            await loadUsers();

        } catch (error) {
            handleResponseError(error);
        }
    }

    /**
     * ----------------------------------------
     * Delete User
     * ----------------------------------------
     */

    async function deleteUser(userId) {
        if (!userId) {
            return;
        }

        const result = await Swal.fire({
            title: 'Delete user?',
            text: 'The user will be removed from the active user directory.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await axios.delete(
                routes.delete(userId)
            );

            handleResponseSuccess(response.data);

            await loadUsers();

        } catch (error) {
            handleResponseError(error);
        }
    }

    /**
     * ----------------------------------------
     * User Actions
     * ----------------------------------------
     */

    document.addEventListener('click', function (event) {
        const actionBtn = event.target.closest(
            '[data-user-action]'
        );

        if (!actionBtn) {
            return;
        }

        const userId = actionBtn.dataset.userId;
        const action = actionBtn.dataset.userAction;

        if (!userId) {
            return;
        }

        if (action === 'view') {
            viewUser(userId);
            return;
        }

        if (action === 'edit') {
            editUser(userId);
            return;
        }

        if (action === 'status') {
            toggleUserStatus(userId);
            return;
        }

        if (action === 'delete') {
            deleteUser(userId);
        }
    });
    /**
     * ----------------------------------------
     * Form Management
     * ----------------------------------------
     */

    function resetUserForm() {
        if (!userForm) {
            return;
        }

        userForm.reset();

        $('#userStatus').value = '1';

        resetValidationErrors(userForm);
    }

    document
        .querySelector('[data-bs-target="#userForm"]')
        ?.addEventListener('click', resetUserForm);

    /**
     * ----------------------------------------
     * Create / Update User
     * ----------------------------------------
     */

    userForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        resetValidationErrors(userForm);

        const file = $('#userAvatar')?.files?.[0];

        if (file) {
            const imageValidation = await validateImage(file);

            if (!imageValidation.valid) {
                showValidationErrors(userForm, {
                    avatar: [imageValidation.message],
                });

                return;
            }
        }

        showLoader(userSubmitBtn, 'Saving...');

        const formData = new FormData(userForm);

        try {
            const response = await axios.post(
                routes.store,
                formData
            );

            handleResponseSuccess(response.data);

            resetUserForm();

            bootstrap.Offcanvas
                .getOrCreateInstance($('#userForm'))
                .hide();
            state.page = 1;

            await loadUsers();

        } catch (error) {
             console.log(error);
            handleResponseError(
                error,
                userForm
            );
        } finally {
            hideLoader(userSubmitBtn);
        }
    });

    /**
     * ----------------------------------------
     * Update User
     * ----------------------------------------
     */

    document.addEventListener('submit', async function (event) {
        if (event.target.id !== 'userEditForm') {
            return;
        }

        event.preventDefault();

        const form = event.target;
        const userId = $('#userEditId')?.value;
        const submitBtn = $('#userEditSubmitBtn');

        if (!userId) {
            return;
        }

        resetValidationErrors(form);

        const file = $('#userEditAvatar')?.files?.[0];

        if (file) {
            const imageValidation = await validateImage(file);

            if (!imageValidation.valid) {
                showValidationErrors(form, {
                    avatar: [imageValidation.message],
                });

                return;
            }
        }

        showLoader(submitBtn, 'Saving...');

        const formData = new FormData(form);

        formData.append('_method', 'PUT');

        try {
            const response = await axios.post(
                routes.update(userId),
                formData
            );

            handleResponseSuccess(response.data);

            const userEdit = $('#userEdit');
            
            if (!userEdit) {
                return;
            }

            bootstrap.Offcanvas
                .getOrCreateInstance(userEdit)
                .hide();

            state.page = 1;

            await loadUsers();

        } catch (error) {
            handleResponseError(
                error,
                form
            );
        } finally {
            hideLoader(submitBtn);
        }
    });

    /**
     * ----------------------------------------
     * Initialize Module
     * ----------------------------------------
     */

    loadUsers();

})();