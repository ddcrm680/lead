/**
 * ----------------------------------------
 * Account Module (Native JS & Axios)
 * ----------------------------------------
 *
 * Handles:
 * - Account menu
 * - Profile update
 * - Change password
 * - Logout
 */

(() => {
    'use strict';

    /**
     * ----------------------------------------
     * Account Elements
     * ----------------------------------------
     */

    const accountMenu = $('#accountMenu');
    const accountMoreBtn = $('#accountMoreBtn');
    const changePasswordBtn = $('#changePassword');
    const logoutBtn = $('#logoutBtn');

    /**
     * ----------------------------------------
     * Close Account Menu
     * ----------------------------------------
     */

    function closeAccountMenu() {
        if (!accountMenu || !accountMoreBtn) {
            return;
        }

        accountMenu.classList.remove('show');
        accountMoreBtn.setAttribute('aria-expanded', 'false');
    }

    /**
     * ----------------------------------------
     * Toggle Account Menu
     * ----------------------------------------
     */

    accountMoreBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const isOpen = accountMenu.classList.toggle('show');
        accountMoreBtn.setAttribute('aria-expanded', String(isOpen));
    });

    /**
     * ----------------------------------------
     * Profile Avatar
     * ----------------------------------------
     */

    const profileAvatar = $('#profileAvatar');
    const profileAvatarEdit = $('#profileAvatarEdit');
    const profileAvatarInput = $('#profileAvatarInput');

    profileAvatarEdit?.addEventListener('click', () => {
        profileAvatarInput?.click();
    });

    profileAvatarInput?.addEventListener('change', function () {
        const file = this.files?.[0];

        if (!file || !profileAvatar) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            profileAvatar.innerHTML = `
                <img
                    src="${event.target.result}"
                    alt="Profile photo"
                >
            `;
        };

        reader.readAsDataURL(file);
    });

    /**
     * ----------------------------------------
     * Update Profile (Axios Version)
     * ----------------------------------------
     */

    const profileForm = $('#profileForm');
    const saveProfileBtn = $('#saveProfileBtn');

    profileForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        resetValidationErrors(profileForm);

        const file = profileAvatarInput?.files?.[0];

        const imageValidation = await validateImage(file);

        if (!imageValidation.valid) {
            showValidationErrors(profileForm, {
                avatar: [imageValidation.message],
            });

            return;
        }

        showLoader(saveProfileBtn, 'Saving...');

        const formData = new FormData(profileForm);

        try {
            const response = await axios.post(profileForm.action, formData);
            handleResponseSuccess(response.data);

        } catch (error) {
            handleResponseError(error, profileForm);

        } finally {
            hideLoader(saveProfileBtn);
        }
    });

    /**
     * ----------------------------------------
     * Change Password (Axios Version)
     * ----------------------------------------
     */

    changePasswordBtn?.addEventListener('click', async (event) => {
        event.preventDefault();
        event.stopPropagation();

        closeAccountMenu();

        await Swal.fire({
            title: 'Change Password',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-semibold">
                            Current password
                        </label>
                        <input
                            class="form-control"
                            id="currentPassword"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Enter your current password"
                        >
                        <span class="invalid-feedback" data-error-for="current_password"></span>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label fw-semibold">
                            New password
                        </label>
                        <input
                            class="form-control"
                            id="newPassword"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="Enter your new password"
                        >
                        <span class="invalid-feedback" data-error-for="password"></span>
                    </div>

                    <div class="mb-2">
                        <label for="confirmPassword" class="form-label fw-semibold">
                            Confirm new password
                        </label>
                        <input
                            class="form-control"
                            id="confirmPassword"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            placeholder="Confirm your new password"
                        >
                        <span class="invalid-feedback" data-error-for="password_confirmation"></span>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Password',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef1b23',
            focusConfirm: false,

            preConfirm: async () => {
                const popup = Swal.getPopup();
                const currentPassword = $('#currentPassword', popup).value;
                const newPassword = $('#newPassword', popup).value;
                const confirmPassword = $('#confirmPassword', popup).value;

                resetValidationErrors(popup);

                if (!currentPassword || !newPassword || !confirmPassword) {
                    Swal.showValidationMessage('Please fill in all password fields.');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    showValidationErrors(popup, {
                        password_confirmation: [
                            'The password confirmation does not match.'
                        ]
                    });
                    Swal.showValidationMessage('Please confirm your new password.');
                    return false;
                }

                Swal.showLoading();

                try {
                    const response = await axios.post('/account/change-password', {
                        current_password: currentPassword,
                        password: newPassword,
                        password_confirmation: confirmPassword
                    },
                    {
                        skipGlobalErrorHandler: true
                    });

                    return response.data;

                } catch (error) {
                    Swal.hideLoading();

                    const status = error.response ? error.response.status : 0;
                    const data = error.response ? error.response.data : {};

                    if (status === 422) {
                        showValidationErrors(popup, data.errors ?? {});
                        Swal.showValidationMessage(data.message ?? 'Please check the form and try again.');
                        return false;
                    }

                    handleResponseError(error);
                    return false;
                }
            }

        }).then((result) => {
            if (result.isConfirmed && result.value) {
                handleResponseSuccess(result.value);
            }
        });
    });

    /**
     * ----------------------------------------
     * Logout (Axios Version)
     * ----------------------------------------
     */

    logoutBtn?.addEventListener('click', async (event) => {
        event.preventDefault();
        event.stopPropagation();

        closeAccountMenu();

        const result = await Swal.fire({
            title: 'Logout from Lead CRM?',
            text: 'You will need to sign in again.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef1b23'
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await axios.post('/logout');
            handleResponseSuccess(response.data);
        } catch (error) {
            handleResponseError(error);
        }
    });

    /**
     * ----------------------------------------
     * Close Menu When Clicking Outside
     * ----------------------------------------
     */

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.account-wrap')) {
            closeAccountMenu();
        }
    });

})();