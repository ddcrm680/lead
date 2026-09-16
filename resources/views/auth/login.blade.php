@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<main class="login-shell">

    <section class="login-brand">
        <div class="brand">
            <div class="devil-mark">
                <i class="bi bi-fire"></i>
            </div>

            <div>
                <strong>LEAD <span>CRM</span></strong>
                <small>Business CRM</small>
            </div>
        </div>

        <div>
            <span class="eyebrow">ENTERPRISE SALES CRM</span>

            <h1>Turn every enquiry into momentum.</h1>

            <p>
                A focused workspace for franchise leads, follow-ups,
                assignments, studios and sales performance.
            </p>
        </div>

        <div class="login-points">
            <span><i class="bi bi-check-circle-fill"></i> Smart lead assignment</span>
            <span><i class="bi bi-check-circle-fill"></i> Priority follow-up queue</span>
            <span><i class="bi bi-check-circle-fill"></i> Complete sales visibility</span>
        </div>
    </section>

    <section class="login-form">

        <span class="eyebrow">WELCOME BACK</span>

        <h2>Sign in to your workspace</h2>

        <p>Use your workspace credentials to continue.</p>

        <form
            id="loginForm"
            method="POST"
            action="{{ route('authenticate') }}"
        >

            <label>
                Email address

                <input
                    class="form-control"
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Enter your email"

                >
                <span class="invalid-feedback" data-error-for="email"></span>

            </label>

            <label>
                Password

                <input
                    class="form-control"
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Enter  your password"
                >
                <span class="invalid-feedback" data-error-for="password"></span>

            </label>

            <div class="d-flex justify-content-between align-items-center mb-4">

                <label class="d-flex align-items-center gap-2 mb-0">

                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                    >

                    Remember me

                </label>

                <a href="#" class="text-danger text-decoration-none fw-bold d-none">
                    Forgot password?
                </a>

            </div>

            <button
                class="btn btn-danger"
                id="submitLogin"
                type="submit"
            >
                Sign in
                <i class="bi bi-arrow-right"></i>
            </button>

        </form>

    </section>

</main>

@endsection

@push('js')

<script>
  /**
   * ----------------------------------------
   * Login Form
   * ----------------------------------------
   */

  document.addEventListener('DOMContentLoaded', function () {
    const form = $('#loginForm');
    const button = $('#submitLogin');

    if (!form) return;

    form.addEventListener('submit', async function (event) {
      event.preventDefault();

      resetValidationErrors(form);
      showLoader(button, 'Signing in...');

      const formData = new FormData(form);

      try {
        const response = await axios.post(
          form.action,
          formData
        );

        handleResponseSuccess(response.data);

      } catch (error) {
        handleResponseError(error, form);

      } finally {
        hideLoader(button);
      }
    });
  });
</script>

@endpush

