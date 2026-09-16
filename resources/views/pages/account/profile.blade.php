@extends('layouts.app')

@section('title', 'Profile')

@section('page-eyebrow', 'ACCOUNT')

@section('page-title', 'Profile')

@push('css')

<style>
/* ==================================================
   PROFILE AVATAR
================================================== */

.profile-avatar-wrap {
  position: relative;
  width: 110px;
  height: 110px;
  margin: 0 auto 18px;
}

.profile-avatar {
  width: 110px;
  height: 110px;
  border-radius: 28px;
  background: #fff0f0;
  color: var(--red);
  display: grid;
  place-items: center;
  font-size: 32px;
  font-weight: 800;
  overflow: hidden;
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Edit avatar button */

.profile-avatar-edit {
  position: absolute;
  right: -4px;
  bottom: -4px;

  width: 34px;
  height: 34px;

  display: grid;
  place-items: center;

  padding: 0;
  border: 3px solid #fff;
  border-radius: 50%;

  background: var(--red);
  color: #fff;

  font-size: 13px;
  cursor: pointer;

  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);

  transition:
    transform 0.18s ease,
    background-color 0.18s ease;
}

.profile-avatar-edit:hover {
  background: #d9161d;
  transform: scale(1.06);
}

.profile-avatar-edit:focus-visible {
  outline: 2px solid var(--red);
  outline-offset: 2px;
}

.profile-avatar-wrap .invalid-feedback {
  position: absolute;
  top: calc(100% + 8px);
  left: 50%;

  width: 260px;
  margin: 0;

  transform: translateX(-50%);

  font-size: 12px;
  line-height: 1.4;
  text-align: center;
}

/* Reserve space only when avatar validation is visible */
.profile-avatar-wrap:has(.invalid-feedback:not(:empty)) {
  margin-bottom: 48px;
}
</style>

@endpush

@section('content')

      @php
          $user = auth()->user();
          $name = trim($user->name ?? '') ?: 'User';
          $initials = collect(explode(' ', $name))
              ->filter()
              ->take(2)
              ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
              ->join('');
          $status = $user->is_active ? 'Active' : 'Inactive';
      @endphp

      <form
        id="profileForm"
        action="{{ route('updateProfile') }}"
        enctype="multipart/form-data"
      >

        <section id="profile" aria-labelledby="profileHeading">

          <div class="page-stack">

            <section class="page-intro">
              <div>
                <span class="eyebrow">ACCOUNT</span>
                <h2 id="profileHeading">My Profile</h2>
                <p>Manage your personal details, role information and notification preferences.</p>
              </div>
              <button class="btn btn-danger" id="saveProfileBtn" type="submit">Save profile</button>
            </section>

            <section class="profile-grid">

              <article class="panel profile-card">

                <div class="profile-avatar-wrap">
                  <div class="profile-avatar" id="profileAvatar">
                    
                    @if ($user->avatar && file_exists(public_path($user->avatar)))

                      <img
                        src="{{ asset($user->avatar) }}"
                        alt="{{ $name }}"
                      >
                    @else
                      {{ $initials ?: 'U' }}
                    @endif

                  </div>

                  <button
                    type="button"
                    class="profile-avatar-edit"
                    id="profileAvatarEdit"
                    aria-label="Change profile photo"
                  >
                    <i class="bi bi-camera-fill"></i>
                  </button>

                  <input
                    type="file"
                    id="profileAvatarInput"
                    name="avatar"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                  >

                  <span
                    class="invalid-feedback"
                    data-error-for="avatar"
                  ></span>
                </div>

                <h3>{{ $name }}</h3>
                <p>{{ $user->role->name ?? 'User' }}</p>
                <span class="status-badge hot">{{ $status }}</span>
                <dl>
                  <div>
                    <dt>Workspace role</dt>
                    <dd>{{ $user->role->name ?? '—' }}</dd>
                  </div>
                
                </dl>
              </article>

              <article class="panel settings-form">
                <h3>Personal information</h3>
                <p>Used for assignments, notifications and internal communication.</p>
                <hr>
                <div class="form-row">

                  <label>Full name <input class="form-control" name="name" value="{{ $user->name }}">
                    <span class="invalid-feedback" data-error-for="name"></span>
                  </label>

                  <label>Mobile number <input class="form-control" name="phone" value="{{ $user->phone ?? '' }}">
                    <span class="invalid-feedback" data-error-for="phone"></span>
                  </label>
                </div>

                <label>Email address <input class="form-control" name="email" type="email" value="{{ $user->email }}">
                  <span class="invalid-feedback" data-error-for="email"></span>
                </label>

                <label>Address <textarea class="form-control" name="address" rows="3">{{ $user->address ?? '' }}</textarea>
                  <span class="invalid-feedback" data-error-for="address"></span>
                </label>

              </article>

            </section>

          </div>
        </section>

      </form>
@endsection

@push('js')


@endpush