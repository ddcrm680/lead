<section data-settings-section="roles-permissions" hidden>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Roles & Permissions</h3>
            <p class="text-muted mb-0">Manage role-based security access for your sales workspace.</p>
        </div>
    </div>

    <hr class="mb-4">

    <!-- Modern Grid Layout -->
    <div class="row g-3">
        @foreach ($roles as $role)
            <div class="col-md-6 col-xl-4">
                <div class="card border shadow-sm h-100 p-3 rounded-3 position-relative">
                    <div class="card-body d-flex flex-column justify-content-between p-0">
                      <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">

                                <h5 class="fw-semibold text-dark mb-0 flex-grow-1 text-break">
                                    {{ $role->name }}
                                </h5>

                                <span class="badge bg-light text-dark border flex-shrink-0">
                                  {{ $role->permissions->count() }} access
                                </span>
                                
                            </div>

                            <p class="text-muted small mb-4">
                                Controls access levels and module permissions for members assigned to this role.
                            </p>
                        </div>

                        <div class="d-flex justify-content-end pt-3 border-top">
                            <button
                                class="btn btn-sm btn-outline-dark px-3"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#roleAccessDrawer"
                                aria-controls="roleAccessDrawer"
                                data-role-id="{{ $role->id }}"
                                data-role-name="{{ $role->name }}"
                            >
                                <i class="bi bi-shield-lock me-1"></i> Configure Access
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</section>