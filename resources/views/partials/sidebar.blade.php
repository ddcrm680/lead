  <aside class="sidebar" id="sidebar" aria-label="Primary navigation">
    <button class="mobile-close" id="closeNav" type="button" aria-label="Close navigation"><i class="bi bi-x-lg"></i></button>

     @php

        $workspaceName = $generalSettings['workspace_name'] ?? 'Lead CRM';

        $workspaceParts = preg_split('/\s+/', trim($workspaceName), -1, PREG_SPLIT_NO_EMPTY);

        $workspaceBrand = strtoupper($workspaceParts[0]);

        $workspaceAccent = count($workspaceParts) > 1 ? strtoupper(implode(' ', array_slice($workspaceParts, 1))) : '';
        $workspaceLogo = $generalSettings['workspace_logo'] ?? null;

    @endphp

    <div class="brand">
        @if (!empty($workspaceLogo) && file_exists(public_path($workspaceLogo)))
            <div class="flex-shrink-0">
                <img src="{{ asset($workspaceLogo) }}" alt="{{ $workspaceName }}" class="img-fluid rounded" style="max-height: 40px; width: auto;">
            </div>
        @else
            <div class="devil-mark flex-shrink-0">
                <i class="bi bi-box-seam-fill"></i>
            </div>
        @endif

        <div>
            <strong>
              {{ $workspaceBrand }}

              @if ($workspaceAccent)

              <span>{{ $workspaceAccent }}</span>

              @endif

            </strong>

            <small>Lead Management Platform</small>
        </div>
    </div>

    <div class="sidebar-scroll">
      <div class="nav-caption">WORKSPACE</div>
      <nav class="side-nav">

        <a
            href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
        >
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <button type="button" data-page="priority"><i class="bi bi-lightning-charge-fill"></i><span>Priority Queue</span><em id="priorityCountNav">12</em></button>
        <button type="button" data-page="followups"><i class="bi bi-telephone-forward-fill"></i><span>Follow-ups</span><em id="followupCountNav">23</em></button>
        <button type="button" data-page="tasks"><i class="bi bi-check2-square"></i><span>Assigned Tasks</span></button>
      </nav>

      <div class="nav-caption">LEADS</div>
      <nav class="side-nav">

          @if (auth()->user()?->hasPermission('leads.view'))
              <a
                  href="{{ route('leads') }}"
                  class="{{ request()->routeIs('leads', 'leadsData') ? 'active' : '' }}"
              >
                  <i class="bi bi-people-fill"></i>
                  <span>All Leads</span>
              </a>
          @endif

        <button type="button" data-page="pipeline"><i class="bi bi-kanban-fill"></i><span>Pipeline</span></button>
        <button type="button" data-page="reports"><i class="bi bi-bar-chart-line-fill"></i><span>Reports</span></button>
      </nav>

      <div class="nav-caption">OPERATIONS</div>
      <nav class="side-nav">
        <button type="button" data-page="studios"><i class="bi bi-shop-window"></i><span>Studios</span></button>
        <button type="button" data-page="property"><i class="bi bi-buildings-fill"></i><span>Rent Property</span></button>
      </nav>

      <div class="nav-caption">DATA & SYSTEM</div>
      <nav class="side-nav">
        <button type="button" data-page="import"><i class="bi bi-cloud-arrow-up-fill"></i><span>Import Leads</span></button>
        <button type="button" data-page="export"><i class="bi bi-cloud-arrow-down-fill"></i><span>Export Leads</span></button>

          @if (auth()->user()?->hasPermission('users.view'))
              <a
                  href="{{ route('users') }}"
                  class="{{ request()->routeIs('users') ? 'active' : '' }}"
              >
                  <i class="bi bi-people-fill"></i>
                  <span>Users</span>
              </a>
          @endif


        <a
            href="{{ route('settings') }}"
            class="{{ request()->routeIs('settings') ? 'active' : '' }}"
        >
            <i class="bi bi-sliders2"></i>
            <span>Settings</span>
        </a>
        
      </nav>
    </div>

    <div class="sidebar-footer">
      <div class="sidebar-card"><span class="live-dot"></span><strong>CRM services online</strong><p id="lastSyncText">Last sync 34 seconds ago</p></div>

      @php
          $user = auth()->user();
          $name = trim($user->name ?? '') ?: 'User';
          $initials = collect(explode(' ', $name))
              ->filter()
              ->take(2)
              ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
              ->join('');
      @endphp

      <div class="account-wrap">
          <div class="account-menu" id="accountMenu">
              <a href="{{ route('profile') }}" id="viewProfile">
                  <i class="bi bi-person"></i>
                  View Profile
              </a>

              <button type="button" id="changePassword">
                  <i class="bi bi-key"></i>
                  Change Password
              </button>

              <hr>

              <button type="button" class="logout-action" id="logoutBtn">
                  <i class="bi bi-box-arrow-right"></i>
                  Logout
              </button>
          </div>

          <div class="user-card" id="userProfileCard" role="button" tabindex="0">
              <div class="avatar">
                  {{ $initials ?: 'U' }}
              </div>

              <div>
                  <strong>{{ $name }}</strong>
                  <small>{{ $user->role->name ?? 'User' }}</small>
              </div>

              <button
                  type="button"
                  class="account-more"
                  id="accountMoreBtn"
                  aria-label="Open account menu"
                  aria-expanded="false"
              >
                  <i class="bi bi-three-dots-vertical"></i>
              </button>
          </div>
      </div>

    </div>
  </aside>
