@extends('layouts.app')

@section('title', 'Settings')

@section('page-eyebrow', 'WORKSPACE CONTROL')

@section('page-title', 'Settings')

@push('css')

@endpush

@section('content')

  <section id="settings" aria-labelledby="settingsHeading">

    <div class="page-stack">

      <section class="page-intro">
        <div>
          <span class="eyebrow">WORKSPACE CONTROL</span>
          <h2 id="settingsHeading">Workspace Settings</h2>
          <p>Configure lead rules, alerts, integrations and permissions.</p>
        </div>
      </section>

      <div class="settings-grid">

          <aside class="panel settings-nav" id="settingsNav">
            <button class="active" type="button" data-setting-tab="general">
              <i class="bi bi-gear"></i> General </button>

            <button type="button" data-setting-tab="routing">
              <i class="bi bi-diagram-3"></i> Lead routing </button>

            <button type="button" data-setting-tab="notifications">
              <i class="bi bi-bell"></i> Notifications </button>

            <button type="button" data-setting-tab="integrations">
              <i class="bi bi-boxes"></i> Integrations </button>

            <button type="button" data-setting-tab="roles-permissions">
              <i class="bi bi-shield-lock"></i> Roles & Permissions </button>

          </aside>

        <section class="panel settings-form" id="settingsContent">

          @include('pages.settings.sections.general')

          @include('pages.settings.sections.lead-routing')

          @include('pages.settings.sections.notification')

          @include('pages.settings.sections.integrations')

          @include('pages.settings.sections.roles-permissions')

        </section>

      </div>

    </div>

  </section>

{{-- role access drawer canvas  --}}
  @include('pages.settings.sections.role-access')

@endsection

@push('js')

<script>
  /**
   * ----------------------------------------
   * Settings
   * ----------------------------------------
   */

  (() => {
    'use strict';

    /**
     * ----------------------------------------
     * Role Access Data
     * ----------------------------------------
     */

    const roleAccessData = @json([
      'roles' => $roles,
      'permissions' => $permissions,
    ]);

    /**
     * ----------------------------------------
     * Settings Elements
     * ----------------------------------------
     */

    const settingsNav = $('#settingsNav');
    const settingsContent = $('#settingsContent');

    /**
     * ----------------------------------------
     * Role Access Elements
     * ----------------------------------------
     */

    const roleAccessDrawer = $('#roleAccessDrawer');
    const roleAccessDrawerTitle = $('#roleAccessDrawerLabel');
    const roleAccessDrawerSubtitle = $('#roleAccessDrawerSubtitle');
    const roleAccessDrawerBody = $('#roleAccessDrawerBody');
    const saveRoleAccess = $('#saveRoleAccess');

    let activeRoleId = null;

    /**
     * ----------------------------------------
     * Escape HTML
     * ----------------------------------------
     */

    function escapeHtml(value) {
      if (value === null || value === undefined) {
        return '';
      }

      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    /**
     * ----------------------------------------
     * Permission Groups
     * ----------------------------------------
     */

    const permissionsByModule =
      roleAccessData.permissions.reduce((groups, permission) => {
        const module = permission.module || 'Other';

        if (!groups[module]) {
          groups[module] = [];
        }

        groups[module].push(permission);

        return groups;
      }, {});

    /**
     * ----------------------------------------
     * Settings Tabs
     * ----------------------------------------
     */

    settingsNav?.addEventListener('click', (event) => {
      const button =
        event.target.closest('[data-setting-tab]');

      if (!button) {
        return;
      }

      const tab = button.dataset.settingTab;

      $$('[data-setting-tab]', settingsNav).forEach((item) => {
        item.classList.toggle(
          'active',
          item === button
        );
      });

      $$('[data-settings-section]', settingsContent)
        .forEach((section) => {
          section.hidden =
            section.dataset.settingsSection !== tab;
        });
    });

    /**
     * ----------------------------------------
     * Render Role Access
     * ----------------------------------------
     */

    function renderRoleAccess(roleId) {
      const role = roleAccessData.roles.find(
        (item) => String(item.id) === String(roleId)
      );

      if (!role || !roleAccessDrawerBody) {
        return;
      }

      const assignedPermissionIds = new Set(
        role.permissions.map(
          (permission) => permission.id
        )
      );

      if (roleAccessDrawerTitle) {
        roleAccessDrawerTitle.textContent =
          `Edit ${role.name} access`;
      }

      if (roleAccessDrawerSubtitle) {
        roleAccessDrawerSubtitle.textContent =
          `Manage permissions for ${role.name}.`;
      }

      roleAccessDrawerBody.innerHTML =
              Object.entries(permissionsByModule)
                .map(([module, permissions]) => {

                  const moduleName = String(module)
                    .replace(/[-_]/g, ' ')
                    .replace(/\b\w/g, (letter) => letter.toUpperCase());

                  const permissionRows = permissions
                    .map((permission) => {
                      const checked = assignedPermissionIds.has(permission.id);

                      return `
                        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-white">
                            <div class="pe-3">
                                <span class="d-block text-dark small">${escapeHtml(permission.name)}</span>
                                ${
                                    permission.description
                                        ? `<span class="d-block text-muted">${escapeHtml(permission.description)}</span>`
                                        : ''
                                }
                            </div>
                            <div class="form-check form-switch m-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    value="${permission.id}"
                                    data-permission-id="${permission.id}"
                                    ${checked ? 'checked' : ''}
                                >
                            </div>
                        </div>
                      `;
                    })
                    .join('');

                  return `
                    <div class="card mb-3 border bg-light shadow-none">
                        <div class="card-header bg-transparent border-bottom px-3 py-2">
                            <h6 class="text-uppercase text-secondary fw-bold fs-7 mb-0 tracking-wide">
                                <i class="bi bi-folder2-open me-1"></i> ${escapeHtml(moduleName)}
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            ${permissionRows}
                        </div>
                    </div>
                  `;
                })
                .join('');
    }

    /**
     * ----------------------------------------
     * Role Access Drawer
     * ----------------------------------------
     */
    
    roleAccessDrawer?.addEventListener(
        'show.bs.offcanvas',
        (event) => {
          const button = event.relatedTarget;

          if (!button) {
            return;
          }

          activeRoleId = button.dataset.roleId;

          renderRoleAccess(activeRoleId);
        }
      );

    /**
     * ----------------------------------------
     * Role Access Drawer Cleanup
     * ----------------------------------------
     */

    roleAccessDrawer?.addEventListener(
      'hidden.bs.offcanvas',
      () => {
        activeRoleId = null;
      }
    );


    /**
     * ----------------------------------------
     * Save Role Access
     * ----------------------------------------
     */

    saveRoleAccess?.addEventListener('click', async () => {

        if (!activeRoleId) {
          return;
        }
        
        const permissionIds = Array.from(
              $$('[data-permission-id]:checked', roleAccessDrawerBody)
          ).map((input) => input.value);

        showLoader( saveRoleAccess, 'Saving...');

        try {

          const response = await axios.put(
            `/settings/roles/${activeRoleId}/permissions`,
            {
              permission_ids: permissionIds
            }
          );

          handleResponseSuccess(response.data);

          const drawer =
            bootstrap.Offcanvas.getInstance(
              roleAccessDrawer
            );

          drawer?.hide();

        } catch (error) {

          handleResponseError(error);

        } finally {

          hideLoader(saveRoleAccess);

        }
      }
    );

    /**
     * ----------------------------------------
     * Update General Settings
     * ----------------------------------------
     */

    const generalSettingsForm = $('#generalSettingsForm');
    const saveGeneralSettings = $('#saveGeneralSettings');

    generalSettingsForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        resetValidationErrors(generalSettingsForm);

        const logoFile = $('#workspaceLogo')?.files?.[0];
        const faviconFile = $('#workspaceFavicon')?.files?.[0];

        const logoValidation = await validateImage(logoFile);

        if (!logoValidation.valid) {
            showValidationErrors(generalSettingsForm, {
                workspace_logo: [logoValidation.message],
            });

            return;
        }

        const faviconValidation = await validateImage(faviconFile, {
            maxSize: 512 * 1024,
            types: [
                'image/png',
                'image/webp',
            ],
            minWidth: 16,
            minHeight: 16,
            maxWidth: 512,
            maxHeight: 512,
        });

        if (!faviconValidation.valid) {
            showValidationErrors(generalSettingsForm, {
                workspace_favicon: [faviconValidation.message],
            });

            return;
        }

        showLoader(saveGeneralSettings, 'Saving...');

        const formData = new FormData(generalSettingsForm);

        try {
            const response = await axios.post(
                generalSettingsForm.action,
                formData
            );

            handleResponseSuccess(response.data);

        } catch (error) {
            handleResponseError(
                error,
                generalSettingsForm
            );

        } finally {
            hideLoader(saveGeneralSettings);
        }
    });

  })();

</script>

@endpush