@if ($users->count())

<div class="priority-grid">

    @foreach ($users as $user)

        <article class="panel directory-card">


                <div class="directory-icon">
                    <i class="bi bi-person"></i>
                </div>


                 <x-action-menu
                    position="top-right"
                    :id="$user->id"
                    attribute="user"
                    post
                    :actions="[
                        [
                            'label' => 'Edit user',
                            'action' => 'edit',
                            'icon' => 'pencil',
                        ],
                        [
                            'label' => $user->is_active
                                ? 'Deactivate user'
                                : 'Activate user',
                            'action' => 'status',
                            'icon' => $user->is_active
                                ? 'person-dash'
                                : 'person-check',
                        ],
                        [
                            'label' => 'Delete user',
                            'action' => 'delete',
                            'icon' => 'trash',
                            'danger' => true,
                        ],
                    ]"
                />

               <span
                    class="availability"
                    @if (!$user->is_active)
                        style="color: #dc3545;"
                    @endif
                >
                    <i
                        @if (!$user->is_active)
                            style="background-color: #dc3545;"
                        @endif
                    ></i>

                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span> 

            <h3>
                {{ $user->name }}
            </h3>

            <p>
                {{ $user->email }}
            </p>

            <div class="directory-meta">

                <span>
                    {{ $user->role?->name ?? 'No role' }}
                </span>

                <strong>
                    {{ $user->phone
                        ? trim(($user->phone_country_code ?? '') . ' ' . $user->phone)
                        : 'No phone'
                    }}
                </strong>

            </div>

            <button
                class="btn btn-light"
                type="button"
                data-user-id="{{ $user->id }}"
                data-user-action="view"
            >
                View details
                <i class="bi bi-arrow-up-right"></i>
            </button>

        </article>

    @endforeach
</div>

@else

    <div class="empty-state text-center">

        <i class="bi bi-people"></i>

        <h3>No users found</h3>

        <p>
            There are no users matching your current filters.
        </p>

    </div>

@endif

<x-pagination
    :paginator="$users"
    prefix="user"
/>
