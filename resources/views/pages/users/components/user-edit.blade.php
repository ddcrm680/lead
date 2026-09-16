<div
    class="offcanvas offcanvas-end lead-modal-bs"
    tabindex="-1"
    id="userEdit"
    aria-labelledby="userEditLabel"
>
    <div class="offcanvas-header modal-head">

        <div>

            <span class="eyebrow">
                EDIT USER
            </span>

            <h2 id="userEditLabel">
                Edit user
            </h2>

            <p>
                Update the user account and workspace role.
            </p>

        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>

    </div>

    <div class="offcanvas-body modal-body">

        <form
            id="userEditForm"
            novalidate
        >
            <input
                type="hidden"
                id="userEditId"
                name="user_id"
                value="{{ $user->id }}"
            >

            <div class="form-section">

                <h3>
                    <span>1</span>
                    Account information
                </h3>

                <div class="form-row">

                    <label>
                        Full name *

                        <input
                            type="text"
                            class="form-control"
                            id="userEditName"
                            name="name"
                            required
                            autocomplete="name"
                            placeholder="e.g. Rahul Mehra"
                            value="{{ $user->name }}"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="name"
                        ></span>
                    </label>

                    <label>
                        Email address *

                        <input
                            type="email"
                            class="form-control"
                            id="userEditEmail"
                            name="email"
                            required
                            autocomplete="email"
                            placeholder="rahul@example.com"
                            value="{{ $user->email }}"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="email"
                        ></span>
                    </label>

                </div>

                <div class="form-row">

                    <label>
                        Country code

                        <input
                            type="text"
                            class="form-control"
                            id="userEditPhoneCountryCode"
                            name="phone_country_code"
                            maxlength="5"
                            inputmode="tel"
                            placeholder="+91"
                            value="{{ $user->phone_country_code ?? '' }}"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="phone_country_code"
                        ></span>
                    </label>

                    <label>
                        Mobile number

                        <input
                            type="tel"
                            class="form-control"
                            id="userEditPhone"
                            name="phone"
                            maxlength="30"
                            inputmode="numeric"
                            autocomplete="tel"
                            placeholder="98765 43210"
                            value="{{ $user->phone ?? '' }}"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="phone"
                        ></span>
                    </label>

                </div>

                <label>
                    Address

                    <textarea
                        class="form-control"
                        id="userEditAddress"
                        name="address"
                        rows="3"
                        maxlength="500"
                        autocomplete="street-address"
                        placeholder="Enter address"
                    >{{ $user->address ?? '' }}</textarea>

                    <span
                        class="invalid-feedback"
                        data-error-for="address"
                    ></span>
                </label>

            </div>

            <div class="form-section">

                <h3>
                    <span>2</span>
                    Access & security
                </h3>

                <div class="form-row">

                    <label>
                        Role *

                        <select
                            class="form-select"
                            id="userEditRole"
                            name="role_id"
                            required
                               @if ($user->role?->slug === 'super-admin')
                                    style="pointer-events: none;"
                                @endif
                        >
                            <option value="">
                                Select role
                            </option>

                            @foreach ($roles as $role)


                                    <option
                                        value="{{ $role->id }}"
                                        @selected($user->role_id == $role->id)
                                    >
                                        {{ $role->name }}
                                    </option>


                            @endforeach

                        </select>

                        <span
                            class="invalid-feedback"
                            data-error-for="role_id"
                        ></span>
                    </label>

                    <label>
                        Account status

                        <select
                            class="form-select"
                            id="userEditStatus"
                            name="is_active"
                        >
                            <option
                                value="1"
                                @selected($user->is_active)
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(!$user->is_active)
                            >
                                Inactive
                            </option>
                        </select>

                        <span
                            class="invalid-feedback"
                            data-error-for="is_active"
                        ></span>
                    </label>

                </div>

                <div class="form-row">

                    <label>
                        Password

                        <input
                            type="password"
                            class="form-control"
                            id="userEditPassword"
                            name="password"
                            autocomplete="new-password"
                            placeholder="Leave blank to keep current password"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="password"
                        ></span>
                    </label>

                    <label>
                        Confirm password

                        <input
                            type="password"
                            class="form-control"
                            id="userEditPasswordConfirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Confirm new password"
                        >

                        <span
                            class="invalid-feedback"
                            data-error-for="password_confirmation"
                        ></span>
                    </label>

                </div>

            </div>

            <div class="form-section">

                <h3>
                    <span>3</span>
                    Profile
                </h3>

                <label>
                    Profile photo

                    <input
                        type="file"
                        class="form-control"
                        id="userEditAvatar"
                        name="avatar"
                        accept="image/*"
                    >

                    <span
                        class="invalid-feedback"
                        data-error-for="avatar"
                    ></span>
                </label>

                @if ($user->avatar && file_exists(public_path($user->avatar)))

                    <div class="mt-2 d-flex align-items-center gap-2">

                        <img
                            src="{{ asset($user->avatar) }}"
                            alt="{{ $user->name }}"
                            width="48"
                            height="48"
                            class="rounded-circle object-fit-cover"
                        >

                        <small class="text-muted">
                            Current profile photo
                        </small>

                    </div>

                @endif

            </div>

        </form>

    </div>

    <div class="modal-foot">

        <button
            class="btn btn-light"
            type="button"
            data-bs-dismiss="offcanvas"
        >
            Cancel
        </button>

        <button
            class="btn btn-danger"
            id="userEditSubmitBtn"
            type="submit"
            form="userEditForm"
        >
            <span>
                Update user
            </span>

            <i class="bi bi-arrow-right"></i>
        </button>

    </div>

</div>