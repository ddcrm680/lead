<div class="offcanvas offcanvas-end lead-modal-bs" tabindex="-1" id="userForm" aria-labelledby="userFormLabel">
    <div class="offcanvas-header modal-head">
        <div>
            <span class="eyebrow" id="userFormEyebrow">NEW USER</span>
            <h2 id="userFormLabel">Add a new user</h2>
            <p id="userFormSubtitle">Create a user account and assign their workspace role.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body modal-body">
        <form id="userFormElement" novalidate>
            <div class="form-section">
                <h3>
                    <span>1</span> Account information
                </h3>

                <div class="form-row">
                    <label>
                        Full name *
                        <input type="text" class="form-control" id="userName" name="name" required autocomplete="name" placeholder="e.g. Rahul Mehra">
                        <span class="invalid-feedback" data-error-for="name"></span>
                    </label>

                    <label>
                        Email address *
                        <input type="email" class="form-control" id="userEmail" name="email" required autocomplete="email" placeholder="rahul@example.com">
                        <span class="invalid-feedback" data-error-for="email"></span>
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        Country code
                        <input type="text" class="form-control" id="userPhoneCountryCode" name="phone_country_code" maxlength="5" inputmode="tel" placeholder="+91">
                        <span class="invalid-feedback" data-error-for="phone_country_code"></span>
                    </label>

                    <label>
                        Mobile number
                        <input type="tel" class="form-control" id="userPhone" name="phone" maxlength="30" inputmode="numeric" autocomplete="tel" placeholder="98765 43210">
                        <span class="invalid-feedback" data-error-for="phone"></span>
                    </label>
                </div>

                <label>
                    Address
                    <textarea class="form-control" id="userAddress" name="address" rows="3" maxlength="500" autocomplete="street-address" placeholder="Enter address"></textarea>
                    <span class="invalid-feedback" data-error-for="address"></span>
                </label>
            </div>

            <div class="form-section">
                <h3>
                    <span>2</span> Access & security
                </h3>

                <div class="form-row">
                    <label>
                        Role *
                        <select class="form-select" id="userRole" name="role_id" required>
                            <option value="">Select role</option>

                            @foreach ($roles as $role)
                                @if ($role->slug !== 'super-admin')
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="role_id"></span>
                    </label>

                    <label>
                        Account status
                        <select class="form-select" id="userStatus" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="invalid-feedback" data-error-for="is_active"></span>
                    </label>
                </div>

                <div class="form-row">
                    <label>
                        Password
                        <input type="password" class="form-control" id="userPassword" name="password" autocomplete="new-password" placeholder="Enter password">
                        <span class="invalid-feedback" data-error-for="password"></span>
                    </label>

                    <label>
                        Confirm password
                        <input type="password" class="form-control" id="userPasswordConfirmation" name="password_confirmation" autocomplete="new-password" placeholder="Confirm password">
                        <span class="invalid-feedback" data-error-for="password_confirmation"></span>
                    </label>
                </div>
            </div>

            <div class="form-section">
                <h3>
                    <span>3</span> Profile
                </h3>

                <label>
                    Profile photo
                    <input type="file" class="form-control" id="userAvatar" name="avatar" accept="image/*">
                    <span class="invalid-feedback" data-error-for="avatar"></span>
                </label>
            </div>
        </form>
    </div>

    <div class="modal-foot">
        <button class="btn btn-light" type="button" data-bs-dismiss="offcanvas">
            Cancel
        </button>

        <button class="btn btn-danger" id="userSubmitBtn" type="submit" form="userFormElement">
            <span id="userSubmitText">Create user</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>