<div
    class="offcanvas offcanvas-end detail-drawer"
    tabindex="-1"
    id="userDetail"
    aria-labelledby="userDetailTitle"
>
    <div class="offcanvas-header detail-head">

        <div class="d-flex align-items-center gap-3">

            @if ($user->avatar && file_exists(public_path($user->avatar)))

                <img
                    src="{{ asset($user->avatar) }}"
                    alt="{{ $user->name }}"
                    width="52"
                    height="52"
                    class="rounded-circle object-fit-cover"
                >

            @endif

            <div>

                <span class="eyebrow">
                    USER PROFILE
                </span>

                <h2 id="userDetailTitle">
                    {{ $user->name }}
                </h2>

                <p id="userDetailSubtitle">
                    {{ $user->email }}
                    ·
                    {{ $user->role?->name ?? 'No role' }}
                </p>

            </div>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>

    </div>

    <div class="offcanvas-body">

        <div class="quick-actions">

            @if ($user->phone)

                <a
                    class="btn btn-dark"
                    href="tel:{{ trim(($user->phone_country_code ?? '') . $user->phone) }}"
                >
                    <i class="bi bi-telephone"></i>
                    Call
                </a>

            @endif

            <a
                class="btn btn-light"
                href="mailto:{{ $user->email }}"
            >
                <i class="bi bi-envelope"></i>
                Email
            </a>

            @if (auth()->user()?->hasPermission('users.update'))

                <button
                    class="btn btn-danger"
                    type="button"
                    data-user-action="edit"
                    data-user-id="{{ $user->id }}"
                >
                    <i class="bi bi-pencil"></i>
                    Edit
                </button>

            @endif

        </div>

        <section class="detail-section">

            <h3>
                Overview
            </h3>

            <dl class="detail-grid">

                <div>
                    <dt>Name</dt>
                    <dd>
                        {{ $user->name }}
                    </dd>
                </div>

                <div>
                    <dt>Email</dt>
                    <dd>
                        {{ $user->email }}
                    </dd>
                </div>

                <div>
                    <dt>Mobile</dt>
                    <dd>
                        {{ $user->phone
                            ? trim(($user->phone_country_code ?? '') . ' ' . $user->phone)
                            : 'Not provided'
                        }}
                    </dd>
                </div>

                <div>
                    <dt>Role</dt>
                    <dd>
                        {{ $user->role?->name ?? 'No role' }}
                    </dd>
                </div>

                <div>
                    <dt>Status</dt>
                    <dd>
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </dd>
                </div>

                <div>
                    <dt>Address</dt>
                    <dd>
                        {{ $user->address ?: 'Not provided' }}
                    </dd>
                </div>

                <div>
                    <dt>Created</dt>
                    <dd>
                        {{ $user->created_at?->format('d M Y, h:i A') ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt>Last updated</dt>
                    <dd>
                        {{ $user->updated_at?->format('d M Y, h:i A') ?? '—' }}
                    </dd>
                </div>

            </dl>

        </section>
        
        <section class="detail-section">

            <h3>
                Account status
            </h3>

            <p>
                This account is currently
                <strong class="{{ $user->is_active ? 'text-success' : 'text-danger' }}">
                    {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                </strong>.
            </p>

        </section>

    </div>

</div>