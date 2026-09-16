@php
// These could ideally be moved to a config file (e.g., config/settings.php)
    $timezones = [
        'Asia/Kolkata' => 'Asia/Kolkata (IST)',
        'UTC' => 'UTC',
        'America/New_York' => 'America/New_York (EST)',
        'Europe/London' => 'Europe/London (GMT)',
    ];

    $dateFormats = [
        'd M Y' => 'DD MMM YYYY',
        'd/m/Y' => 'DD/MM/YYYY',
        'm/d/Y' => 'MM/DD/YYYY',
        'Y-m-d' => 'YYYY-MM-DD',
    ];

    $timeFormats = [
        'h:i A' => '12-hour (09:30 AM)',
        'H:i' => '24-hour (09:30)',
    ];

    $currencies = [
        'INR' => 'INR — Indian Rupee',
        'USD' => 'USD — US Dollar',
        'EUR' => 'EUR — Euro',
        'GBP' => 'GBP — British Pound',
    ];

    $countries = [
        'IN' => 'India',
        'US' => 'United States',
        'GB' => 'United Kingdom',
    ];

    $languages = [
        'en' => 'English',
    ];

    $weekStarts = [
        'monday' => 'Monday',
        'sunday' => 'Sunday',
    ];
@endphp

<section data-settings-section="general" class="container-fluid py-4 px-0">

    {{-- Section Header --}}
    <div class="mb-4 pb-3 border-bottom">
        <h4 class="mb-1 fw-bold text-dark">General preferences</h4>
        <p class="text-muted mb-0">Default behavior and core configurations for your Lead Command workspace.</p>
    </div>

    <form id="generalSettingsForm" enctype="multipart/form-data" action="{{ route('updateGeneral') }}">
            {{-- Workspace Settings --}}
            <div class="card border-0 shadow-sm mb-4 rounded-3">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <h5 class="fw-semibold text-dark mb-1">Workspace</h5>
                        <p class="text-muted small mb-0">Manage your workspace identity, name, and branding logo.</p>
                    </div>

                    <div class="row g-3">
                        {{-- Workspace Name --}}
                        <div class="col-md-4">
                            <label for="workspaceName" class="form-label fw-medium text-secondary small">Name</label>
                            <input type="text" class="form-control" id="workspaceName" name="workspace_name" value="{{ $generalSettings['workspace_name'] ?? 'Lead CRM Workspace' }}">
                            <span class="invalid-feedback" data-error-for="workspace_name"></span>
                        </div>

                        {{-- Workspace Logo --}}
                        <div class="col-md-4">
                            <label for="workspaceLogo" class="form-label fw-medium text-secondary small">Logo</label>
                            <div class="d-flex align-items-start gap-3">
                                @if (!empty($generalSettings['workspace_logo']) && file_exists(public_path($generalSettings['workspace_logo'])))
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset($generalSettings['workspace_logo']) }}" alt="Workspace logo" class="rounded border bg-light object-fit-contain" style="height: 40px; width: 40px;">
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="workspaceLogo" name="workspace_logo" accept="image/jpeg,image/png,image/webp">
                                    <span class="invalid-feedback" data-error-for="workspace_logo"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Workspace Favicon --}}
                        <div class="col-md-4">
                            <label for="workspaceFavicon" class="form-label fw-medium text-secondary small">Favicon</label>
                            <div class="d-flex align-items-start gap-3">
                                @if (!empty($generalSettings['workspace_favicon']) && file_exists(public_path($generalSettings['workspace_favicon'])))
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset($generalSettings['workspace_favicon']) }}" alt="Workspace favicon" class="rounded border bg-light object-fit-contain" style="height: 40px; width: 40px;">
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="workspaceFavicon" name="workspace_favicon" accept="image/png,image/webp">
                                    <span class="invalid-feedback" data-error-for="workspace_favicon"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        {{-- Regional Preferences --}}
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body p-4">
                <div class="mb-3">
                    <h5 class="fw-semibold text-dark mb-1">Regional preferences</h5>
                    <p class="text-muted small mb-0">Configure your default timezone, date layouts, currency, and language.</p>
                </div>

                <div class="row g-3">
                    {{-- Timezone --}}
                    <div class="col-md-6">
                        <label for="timezone" class="form-label fw-medium text-secondary small">Default timezone</label>
                        <select class="form-select" id="timezone" name="timezone">
                            @foreach ($timezones as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['timezone'] ?? 'Asia/Kolkata') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="timezone"></span>
                    </div>

                    {{-- Date Format --}}
                    <div class="col-md-6">
                        <label for="dateFormat" class="form-label fw-medium text-secondary small">Date format</label>
                        <select class="form-select" id="dateFormat" name="date_format">
                            @foreach ($dateFormats as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['date_format'] ?? 'd M Y') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="date_format"></span>
                    </div>

                    {{-- Time Format --}}
                    <div class="col-md-6">
                        <label for="timeFormat" class="form-label fw-medium text-secondary small">Time format</label>
                        <select class="form-select" id="timeFormat" name="time_format">
                            @foreach ($timeFormats as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['time_format'] ?? 'h:i A') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="time_format"></span>
                    </div>

                    {{-- Currency --}}
                    <div class="col-md-6">
                        <label for="currency" class="form-label fw-medium text-secondary small">Default currency</label>
                        <select class="form-select" id="currency" name="currency">
                            @foreach ($currencies as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['currency'] ?? 'INR') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="currency"></span>
                    </div>

                    {{-- Country --}}
                    <div class="col-md-6">
                        <label for="defaultCountry" class="form-label fw-medium text-secondary small">Default country</label>
                        <select class="form-select" id="defaultCountry" name="default_country">
                            @foreach ($countries as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['default_country'] ?? 'IN') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="default_country"></span>
                    </div>

                    {{-- Language --}}
                    <div class="col-md-6">
                        <label for="language" class="form-label fw-medium text-secondary small">Default language</label>
                        <select class="form-select" id="language" name="language">
                            @foreach ($languages as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['language'] ?? 'en') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="language"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Preferences --}}
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body p-4">
                <div class="mb-3">
                    <h5 class="fw-semibold text-dark mb-1">Business preferences</h5>
                    <p class="text-muted small mb-0">Set your standard operational hours and calendar start days.</p>
                </div>

                <div class="row g-3">
                    {{-- Week Starts On --}}
                    <div class="col-md-4">
                        <label for="weekStarts" class="form-label fw-medium text-secondary small">Week starts on</label>
                        <select class="form-select" id="weekStarts" name="week_starts">
                            @foreach ($weekStarts as $value => $label)
                                <option value="{{ $value }}" @selected(($generalSettings['week_starts'] ?? 'monday') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" data-error-for="week_starts"></span>
                    </div>

                    {{-- Business Start Time --}}
                    <div class="col-md-4">
                        <label for="businessStart" class="form-label fw-medium text-secondary small">Business start time</label>
                        <input
                            type="time"
                            class="form-control"
                            id="businessStart"
                            name="business_start"
                            value="{{ $generalSettings['business_start'] ?? '09:00' }}"
                        >
                        <span class="invalid-feedback" data-error-for="business_start"></span>
                    </div>

                    {{-- Business End Time --}}
                    <div class="col-md-4">
                        <label for="businessEnd" class="form-label fw-medium text-secondary small">Business end time</label>
                        <input
                            type="time"
                            class="form-control"
                            id="businessEnd"
                            name="business_end"
                            value="{{ $generalSettings['business_end'] ?? '18:00' }}"
                        >
                        <span class="invalid-feedback" data-error-for="business_end"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
            <button type="reset" class="btn btn-danger px-4">Reset</button>
            <button type="submit" class="btn btn-dark px-4" id="saveGeneralSettings">Save changes</button>
        </div>

    </form>
</section>